<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref } from 'vue';
import FeedbackFormBARS from './FeedbackFormBARS.vue';

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
                        skill: skill || { name: 'Unknown Skill' },
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
    <div v-if="pendingRequests.length > 0" class="mb-6">
        <v-alert
            color="indigo-lighten-5"
            border="start"
            border-color="indigo"
            elevation="1"
        >
            <template #prepend>
                <v-icon color="indigo" size="large">mdi-account-heart</v-icon>
            </template>

            <div class="d-flex align-center justify-space-between w-100">
                <div>
                    <div class="text-subtitle-1 font-weight-bold indigo--text">
                        Feedback 360 Pendiente (Metodología BARS)
                    </div>
                    <div class="text-body-2 text-secondary">
                        Tienes {{ pendingRequests.length }} solicitudes de
                        feedback pendientes por completar.
                    </div>
                </div>
                <div
                    class="d-flex pa-1 gap-2 overflow-x-auto"
                    style="max-width: 60%"
                >
                    <v-chip
                        v-for="req in pendingRequests"
                        :key="req.id"
                        closable
                        @click="openFeedbackForm(req)"
                        class="ma-1"
                        color="indigo"
                        variant="flat"
                    >
                        {{ req.subject.full_name }}
                    </v-chip>
                </div>
            </div>
        </v-alert>

        <!-- Feedback Form Dialog -->
        <v-dialog v-model="dialog" max-width="900" persistent>
            <v-card
                v-if="selectedRequest"
                class="d-flex flex-column"
                style="max-height: 90vh"
            >
                <v-card-title class="bg-indigo flex-shrink-0 py-4 text-white">
                    Feedback para {{ selectedRequest.subject.full_name }}
                    <div class="text-caption text-indigo-lighten-4">
                        Relación: {{ selectedRequest.relationship }}
                    </div>
                </v-card-title>

                <v-card-text class="pa-6 overflow-y-auto">
                    <div
                        v-for="(item, i) in feedbackItems"
                        :key="i"
                        class="mb-6"
                    >
                        <!-- Open Question -->
                        <div v-if="item.type === 'open'">
                            <div class="text-subtitle-2 indigo--text mb-2">
                                {{ item.question }}
                            </div>
                            <v-textarea
                                v-model="item.answer"
                                variant="outlined"
                                density="comfortable"
                                rows="3"
                                placeholder="Escribe tu observación detallada aquí..."
                                hide-details="auto"
                            ></v-textarea>
                        </div>

                        <!-- BARS Evaluation -->
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

                        <v-divider
                            v-if="i < feedbackItems.length - 1"
                            class="my-6"
                        ></v-divider>
                    </div>
                </v-card-text>

                <v-card-actions
                    class="pa-4 flex-shrink-0 border-t bg-white pt-0"
                >
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="dialog = false">Cerrar</v-btn>
                    <v-btn
                        color="indigo"
                        variant="flat"
                        :loading="submitting"
                        @click="submitFeedback"
                    >
                        Enviar Evaluación
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>
