<script setup lang="ts">
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

interface SurveyQuestion {
    type: 'nps' | 'rating' | 'text' | 'multiple_choice';
    question: string;
    options?: string[];
}

interface Survey {
    id: number;
    title: string;
    description: string | null;
    questions: SurveyQuestion[];
    is_mandatory: boolean;
}

interface NpsResult {
    promoters: number;
    passives: number;
    detractors: number;
    nps_score: number;
}

interface QuestionResult {
    question: string;
    type: string;
    nps?: NpsResult;
    average?: number;
    count?: number;
    responses?: string[];
    distribution?: Record<string, number>;
}

interface SurveyResults {
    total_responses: number;
    questions: QuestionResult[];
}

const props = defineProps<{ courseId?: number; surveyId?: number }>();

const survey = ref<Survey | null>(null);
const results = ref<SurveyResults | null>(null);
const loading = ref(false);
const submitting = ref(false);
const error = ref('');
const success = ref('');
const activeView = ref<'form' | 'results'>('form');
const answers = ref<Array<{ question_index: number; answer: string | number }>>(
    [],
);

const npsLabels = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];

async function fetchSurvey() {
    if (!props.courseId) return;
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/lms/courses/${props.courseId}/survey`,
        );
        survey.value = res.data.data;
        if (survey.value) {
            answers.value = survey.value.questions.map((_, idx) => ({
                question_index: idx,
                answer: '',
            }));
        }
    } catch {
        error.value = 'Failed to load survey.';
    } finally {
        loading.value = false;
    }
}

async function fetchResults() {
    const sid = props.surveyId ?? survey.value?.id;
    if (!sid) return;
    loading.value = true;
    try {
        const res = await axios.get(`/api/lms/surveys/${sid}/results`);
        results.value = res.data.data;
    } catch {
        error.value = 'Failed to load results.';
    } finally {
        loading.value = false;
    }
}

async function submitSurvey() {
    if (!survey.value) return;
    submitting.value = true;
    error.value = '';
    try {
        await axios.post(`/api/lms/surveys/${survey.value.id}/submit`, {
            answers: answers.value,
        });
        success.value = 'Thank you for your feedback!';
        activeView.value = 'results';
        await fetchResults();
    } catch (e: unknown) {
        const msg =
            (e as { response?: { data?: { message?: string } } })?.response
                ?.data?.message ?? 'Submission failed.';
        error.value = msg;
    } finally {
        submitting.value = false;
    }
}

const npsColor = computed(() => {
    const nps =
        results.value?.questions.find((q) => q.type === 'nps')?.nps
            ?.nps_score ?? 0;
    if (nps >= 50) return 'green';
    if (nps >= 0) return 'orange';
    return 'red';
});

onMounted(() => {
    fetchSurvey();
    if (props.surveyId) {
        activeView.value = 'results';
        fetchResults();
    }
});
</script>

<template>
    <v-container>
        <v-row>
            <v-col cols="12" md="8" offset-md="2">
                <h1 class="text-h4 mb-4">Course Survey</h1>

                <v-alert
                    v-if="error"
                    type="error"
                    closable
                    class="mb-4"
                    @click:close="error = ''"
                >
                    {{ error }}
                </v-alert>
                <v-alert
                    v-if="success"
                    type="success"
                    closable
                    class="mb-4"
                    @click:close="success = ''"
                >
                    {{ success }}
                </v-alert>

                <v-tabs v-model="activeView" class="mb-4">
                    <v-tab value="form">
                        <v-icon start>mdi-form-select</v-icon>
                        Survey Form
                    </v-tab>
                    <v-tab value="results">
                        <v-icon start>mdi-chart-bar</v-icon>
                        Results
                    </v-tab>
                </v-tabs>

                <v-progress-linear v-if="loading" indeterminate class="mb-4" />

                <!-- Survey Form -->
                <v-card v-if="activeView === 'form' && survey" elevation="2">
                    <v-card-title>{{ survey.title }}</v-card-title>
                    <v-card-subtitle v-if="survey.description">{{
                        survey.description
                    }}</v-card-subtitle>
                    <v-card-text>
                        <div
                            v-for="(question, idx) in survey.questions"
                            :key="idx"
                            class="mb-6"
                        >
                            <h3 class="text-subtitle-1 font-weight-bold mb-2">
                                {{ question.question }}
                            </h3>

                            <!-- NPS -->
                            <div v-if="question.type === 'nps'">
                                <v-btn-toggle
                                    v-model="answers[idx].answer"
                                    mandatory
                                    class="d-flex flex-wrap"
                                >
                                    <v-btn
                                        v-for="n in npsLabels"
                                        :key="n"
                                        :value="parseInt(n)"
                                        size="small"
                                        class="ma-1"
                                    >
                                        {{ n }}
                                    </v-btn>
                                </v-btn-toggle>
                                <div
                                    class="d-flex justify-space-between text-caption mt-1"
                                >
                                    <span>Not likely</span>
                                    <span>Very likely</span>
                                </div>
                            </div>

                            <!-- Rating -->
                            <v-rating
                                v-else-if="question.type === 'rating'"
                                v-model="answers[idx].answer"
                                length="5"
                                hover
                            />

                            <!-- Text -->
                            <v-textarea
                                v-else-if="question.type === 'text'"
                                v-model="answers[idx].answer"
                                rows="3"
                                variant="outlined"
                            />

                            <!-- Multiple Choice -->
                            <v-radio-group
                                v-else-if="question.type === 'multiple_choice'"
                                v-model="answers[idx].answer"
                            >
                                <v-radio
                                    v-for="option in question.options"
                                    :key="option"
                                    :label="option"
                                    :value="option"
                                />
                            </v-radio-group>
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn
                            color="primary"
                            :loading="submitting"
                            @click="submitSurvey"
                        >
                            Submit Survey
                        </v-btn>
                    </v-card-actions>
                </v-card>

                <v-alert
                    v-if="activeView === 'form' && !survey && !loading"
                    type="info"
                >
                    No survey available for this course.
                </v-alert>

                <!-- Results View -->
                <div v-if="activeView === 'results' && results">
                    <v-card class="mb-4" elevation="2">
                        <v-card-title>Survey Results</v-card-title>
                        <v-card-subtitle
                            >{{
                                results.total_responses
                            }}
                            response(s)</v-card-subtitle
                        >
                    </v-card>

                    <v-card
                        v-for="(q, idx) in results.questions"
                        :key="idx"
                        class="mb-4"
                        elevation="1"
                    >
                        <v-card-title class="text-subtitle-1">{{
                            q.question
                        }}</v-card-title>
                        <v-card-text>
                            <!-- NPS Gauge -->
                            <div
                                v-if="q.type === 'nps' && q.nps"
                                class="text-center"
                            >
                                <div
                                    class="text-h2 font-weight-bold"
                                    :class="`text-${npsColor}`"
                                >
                                    {{ q.nps.nps_score }}
                                </div>
                                <div class="text-caption mb-2">NPS Score</div>
                                <v-row>
                                    <v-col cols="4">
                                        <v-chip color="green" size="small"
                                            >Promoters:
                                            {{ q.nps.promoters }}</v-chip
                                        >
                                    </v-col>
                                    <v-col cols="4">
                                        <v-chip color="orange" size="small"
                                            >Passives:
                                            {{ q.nps.passives }}</v-chip
                                        >
                                    </v-col>
                                    <v-col cols="4">
                                        <v-chip color="red" size="small"
                                            >Detractors:
                                            {{ q.nps.detractors }}</v-chip
                                        >
                                    </v-col>
                                </v-row>
                            </div>

                            <!-- Rating Average -->
                            <div v-else-if="q.type === 'rating'">
                                <v-rating
                                    :model-value="q.average ?? 0"
                                    readonly
                                    half-increments
                                />
                                <span class="ml-2"
                                    >{{ (q.average ?? 0).toFixed(1) }} avg ({{
                                        q.count
                                    }}
                                    responses)</span
                                >
                            </div>

                            <!-- Text Responses -->
                            <div v-else-if="q.type === 'text'">
                                <v-list density="compact">
                                    <v-list-item
                                        v-for="(resp, rIdx) in q.responses ??
                                        []"
                                        :key="rIdx"
                                    >
                                        <v-list-item-title>{{
                                            resp
                                        }}</v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </div>

                            <!-- Multiple Choice Distribution -->
                            <div
                                v-else-if="
                                    q.type === 'multiple_choice' &&
                                    q.distribution
                                "
                            >
                                <div
                                    v-for="(count, option) in q.distribution"
                                    :key="String(option)"
                                    class="mb-2"
                                >
                                    <div class="d-flex align-center">
                                        <span
                                            class="mr-2"
                                            style="min-width: 120px"
                                            >{{ option }}</span
                                        >
                                        <v-progress-linear
                                            :model-value="
                                                (count /
                                                    results.total_responses) *
                                                100
                                            "
                                            height="20"
                                            color="primary"
                                        >
                                            <template #default>{{
                                                count
                                            }}</template>
                                        </v-progress-linear>
                                    </div>
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </div>

                <v-alert
                    v-if="activeView === 'results' && !results && !loading"
                    type="info"
                >
                    No results available yet.
                </v-alert>
            </v-col>
        </v-row>
    </v-container>
</template>
