<script setup lang="ts">
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { PhX } from '@phosphor-icons/vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import FeedbackFormBARS from './FeedbackFormBARS.vue';

const { t } = useI18n();

const pendingRequests = ref<any[]>([]);
const loading = ref(true);
const dialog = ref(false);
const selectedRequest = ref<any>(null);
const feedbackItems = ref<any[]>([]);
const submitting = ref(false);

const loadRequests = async () => {
    loading.value = true;
    try {
        const response = await axios.get(
            '/api/strategic-planning/assessments/feedback/pending',
        );
        pendingRequests.value = response.data;
    } catch (e) {
        console.error('Error loading pending requests', e);
    } finally {
        loading.value = false;
    }
};

const openFeedbackForm = async (request: any) => {
    selectedRequest.value = request;
    dialog.value = true;
    loading.value = true;

    try {
        // 1. Fetch catalogs (Skills & BARS Levels)
        const [skillsResponse, barsResponse] = await Promise.all([
            axios.get('/api/catalogs', { params: { catalogs: ['skills'] } }),
            axios.post('/api/CompetencyLevelBars/search', {
                data: { filters: {} },
            }),
        ]);

        const allSkills = skillsResponse.data.skills;
        const allBars = barsResponse.data.data;

        // 2. Check if request has pre-filled feedback items from backend
        if (request.feedback && request.feedback.length > 0) {
            feedbackItems.value = request.feedback.map((fb: any) => {
                const isBars = !!fb.skill_id;

                if (isBars) {
                    const skill = allSkills.find(
                        (s: any) => s.id === fb.skill_id,
                    );
                    const skillLevels = allBars
                        .filter((b: any) => b.skill_id === fb.skill_id)
                        .sort((a: any, b: any) => a.level - b.level);

                    return {
                        id: fb.id, // Important for updates
                        type: 'bars',
                        skill_id: fb.skill_id,
                        skill: skill || {
                            name: t('pending_feedback.unknown_skill'),
                        },
                        levels: skillLevels,
                        question:
                            fb.question || 'Evalúa el nivel de competencia:',
                        score: fb.score,
                        evidence_url: fb.evidence_url,
                        confidence_level: fb.confidence_level ?? 80,
                    };
                } else {
                    return {
                        id: fb.id,
                        type: 'open',
                        question: fb.question,
                        answer: fb.answer || '',
                    };
                }
            });
        } else {
            // 3. Fallback: Mocking (Legacy behavior)
            console.warn('No pre-filled questions found. Using legacy mock.');
            feedbackItems.value = [
                {
                    type: 'open',
                    question:
                        '¿Cuál es el mayor impacto positivo de este integrante?',
                    answer: '',
                },
                {
                    type: 'open',
                    question: '¿Qué debería empezar a hacer diferente?',
                    answer: '',
                },
            ];
        }
    } catch (e) {
        console.error('Error preparing feedback form', e);
    } finally {
        loading.value = false;
    }
};

const submitFeedback = async () => {
    submitting.value = true;
    try {
        const answersPayload = feedbackItems.value.map((item) => {
            const payload: any = {
                id: item.id, // Send ID to update existing row
                question: item.question,
            };

            if (item.type === 'open') {
                payload.answer = item.answer;
            } else {
                payload.skill_id = item.skill_id;
                payload.score = item.score;
                payload.evidence_url = item.evidence_url;
                payload.confidence_level = item.confidence_level;
            }
            return payload;
        });

        await axios.post(
            '/api/strategic-planning/assessments/feedback/submit',
            {
                request_id: selectedRequest.value.id,
                answers: answersPayload,
            },
        );
        dialog.value = false;
        await loadRequests();
    } catch (e) {
        console.error('Error submitting feedback', e);
    } finally {
        submitting.value = false;
    }
};

onMounted(loadRequests);
</script>
<template>
    <div v-if="pendingRequests.length > 0" class="mb-10">
        <StCardGlass
            class="overflow-hidden border-indigo-500/20 bg-indigo-500/5"
            :no-hover="true"
        >
            <div
                class="flex flex-wrap items-center justify-between gap-6 px-8 py-6"
            >
                <div class="flex items-center gap-6">
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-2xl border border-indigo-500/20 bg-indigo-500/10"
                    >
                        <v-icon color="indigo-400" size="32"
                            >mdi-account-heart</v-icon
                        >
                    </div>
                    <div>
                        <h2
                            class="text-h5 font-weight-black mb-1 tracking-tight text-white"
                        >
                            {{ $t('pending_feedback.title') }}
                        </h2>
                        <div
                            class="text-body-2 font-medium text-white/50"
                            v-html="
                                $t('pending_feedback.subtitle', {
                                    count: `<span class='font-bold tracking-wider text-indigo-300 uppercase'>${pendingRequests.length}</span>`,
                                })
                            "
                        ></div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button
                        v-for="req in pendingRequests"
                        :key="req.id"
                        @click="openFeedbackForm(req)"
                        class="group flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-5 py-2.5 text-sm font-bold text-white transition-all duration-300 hover:border-indigo-500 hover:bg-indigo-600"
                    >
                        <v-avatar
                            size="24"
                            class="border border-white/20 opacity-80 group-hover:opacity-100"
                        >
                            <v-icon size="14">mdi-account</v-icon>
                        </v-avatar>
                        {{ req.subject.full_name }}
                    </button>
                </div>
            </div>
        </StCardGlass>

        <!-- Feedback Form Dialog -->
        <v-dialog v-model="dialog" max-width="1000" persistent>
            <div
                v-if="selectedRequest"
                class="st-glass-container relative flex h-full flex-col overflow-hidden border-none shadow-2xl"
                style="max-height: 90vh"
            >
                <!-- Dialog Header -->
                <div
                    class="flex items-center justify-between border-b border-white/10 bg-white/5 px-8 py-6"
                >
                    <div class="flex items-center gap-5">
                        <v-avatar
                            color="indigo-700"
                            size="56"
                            class="border border-indigo-400/30"
                        >
                            <v-icon size="28" color="white"
                                >mdi-account-star</v-icon
                            >
                        </v-avatar>
                        <div>
                            <div
                                class="text-h6 font-weight-black leading-tight text-white"
                            >
                                {{
                                    $t('pending_feedback.analysis_for', {
                                        name: selectedRequest.subject.full_name,
                                    })
                                }}
                            </div>
                            <div
                                class="text-caption mt-1 font-bold tracking-widest text-indigo-400 uppercase"
                            >
                                {{
                                    $t('pending_feedback.correlation', {
                                        relationship:
                                            selectedRequest.relationship,
                                    })
                                }}
                            </div>
                        </div>
                    </div>
                    <StButtonGlass
                        variant="ghost"
                        circle
                        :icon="PhX"
                        class="!text-white/40 hover:!text-white"
                        @click="dialog = false"
                    />
                </div>

                <div
                    class="pa-10 scrollbar-hide flex-grow overflow-x-hidden overflow-y-auto"
                >
                    <div
                        v-for="(item, i) in feedbackItems"
                        :key="i"
                        class="mb-12 animate-in duration-500 fade-in slide-in-from-bottom-4"
                        :style="{ animationDelay: i * 100 + 'ms' }"
                    >
                        <!-- Open Question -->
                        <div v-if="item.type === 'open'" class="max-w-4xl">
                            <div
                                class="text-caption font-weight-black mb-4 tracking-widest text-white/40 uppercase"
                            >
                                {{
                                    $t('pending_feedback.strategic_insight', {
                                        question: item.question,
                                    })
                                }}
                            </div>
                            <v-textarea
                                v-model="item.answer"
                                variant="outlined"
                                rows="4"
                                :placeholder="
                                    $t('pending_feedback.capture_details')
                                "
                                hide-details="auto"
                                class="glass-input-premium"
                                persistent-placeholder
                            ></v-textarea>
                        </div>

                        <div v-if="item.type === 'bars'">
                            <FeedbackFormBARS
                                :skill="item.skill"
                                :levels="item.levels"
                                :question="item.question"
                                v-model="item.score"
                                v-model:evidence="item.evidence_url"
                                v-model:confidence="item.confidence_level"
                            />
                        </div>
                    </div>
                </div>

                <div
                    class="flex items-center justify-end gap-4 border-t border-white/10 bg-white/5 px-8 py-6"
                >
                    <StButtonGlass variant="ghost" @click="dialog = false">
                        {{ $t('pending_feedback.close') }}
                    </StButtonGlass>
                    <StButtonGlass
                        variant="primary"
                        class="!px-8"
                        :loading="submitting"
                        @click="submitFeedback"
                    >
                        {{ $t('pending_feedback.submit') }}
                    </StButtonGlass>
                </div>
            </div>
        </v-dialog>
    </div>
</template>
