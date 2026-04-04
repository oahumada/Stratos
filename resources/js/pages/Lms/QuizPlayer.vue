<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {
    PhArrowLeft,
    PhArrowRight,
    PhCheck,
    PhClock,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

defineOptions({ layout: AppLayout });

interface Question {
    id: number;
    question_text: string;
    question_type: string;
    options: { id: string; text: string }[] | null;
    points: number;
    order: number;
}

interface Quiz {
    id: number;
    title: string;
    description: string | null;
    passing_score: number;
    max_attempts: number;
    time_limit_minutes: number | null;
    shuffle_questions: boolean;
    questions: Question[];
}

interface AttemptResult {
    id: number;
    score: number;
    total_points: number;
    max_points: number;
    passed: boolean;
    answers: {
        question_id: number;
        answer: any;
        is_correct: boolean;
        points_earned: number;
    }[];
    completed_at: string;
}

const props = defineProps<{ quizId: number }>();

const quiz = ref<Quiz | null>(null);
const loading = ref(true);
const submitting = ref(false);
const error = ref('');
const currentQuestion = ref(0);
const answers = ref<Record<number, any>>({});
const attemptId = ref<number | null>(null);
const result = ref<AttemptResult | null>(null);
const timeRemaining = ref<number | null>(null);
let timerInterval: ReturnType<typeof setInterval> | null = null;

const totalQuestions = computed(() => quiz.value?.questions.length ?? 0);
const answeredCount = computed(() => Object.keys(answers.value).length);
const allAnswered = computed(
    () => answeredCount.value === totalQuestions.value,
);
const currentQ = computed(
    () => quiz.value?.questions[currentQuestion.value] ?? null,
);

const formatTime = (seconds: number) => {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
};

async function loadQuiz() {
    try {
        loading.value = true;
        const { data } = await axios.get(`/api/lms/quizzes/${props.quizId}`);
        quiz.value = data.data ?? data;
    } catch (e: any) {
        error.value = e.response?.data?.message ?? 'Error cargando quiz';
    } finally {
        loading.value = false;
    }
}

async function startAttempt() {
    try {
        loading.value = true;
        error.value = '';
        const { data } = await axios.post(
            `/api/lms/quizzes/${props.quizId}/start`,
        );
        attemptId.value = data.data?.id ?? data.id;
        if (quiz.value?.time_limit_minutes) {
            timeRemaining.value = quiz.value.time_limit_minutes * 60;
            timerInterval = setInterval(() => {
                if (timeRemaining.value !== null) {
                    timeRemaining.value--;
                    if (timeRemaining.value <= 0) {
                        submitQuiz();
                    }
                }
            }, 1000);
        }
    } catch (e: any) {
        error.value =
            e.response?.data?.message ?? 'No se pudo iniciar el intento';
    } finally {
        loading.value = false;
    }
}

async function submitQuiz() {
    if (timerInterval) clearInterval(timerInterval);
    try {
        submitting.value = true;
        error.value = '';
        const formattedAnswers = Object.entries(answers.value).map(
            ([qId, ans]) => ({
                question_id: parseInt(qId),
                answer: Array.isArray(ans) ? ans : [ans],
            }),
        );
        const { data } = await axios.post(
            `/api/lms/quizzes/${props.quizId}/submit`,
            {
                attempt_id: attemptId.value,
                answers: formattedAnswers,
            },
        );
        result.value = data.data ?? data;
    } catch (e: any) {
        error.value = e.response?.data?.message ?? 'Error al enviar respuestas';
    } finally {
        submitting.value = false;
    }
}

function setAnswer(questionId: number, value: any) {
    answers.value[questionId] = value;
}

onMounted(loadQuiz);
onBeforeUnmount(() => {
    if (timerInterval) clearInterval(timerInterval);
});
</script>

<template>
    <v-container class="py-6" max-width="900">
        <!-- Loading -->
        <v-skeleton-loader v-if="loading" type="article, actions" />

        <!-- Error -->
        <v-alert
            v-if="error"
            type="error"
            class="mb-4"
            closable
            @click:close="error = ''"
        >
            {{ error }}
        </v-alert>

        <!-- Results -->
        <template v-if="result">
            <v-card class="mb-6">
                <v-card-title class="text-h5">
                    <v-icon
                        :color="result.passed ? 'success' : 'error'"
                        class="mr-2"
                    >
                        {{
                            result.passed
                                ? 'mdi-check-circle'
                                : 'mdi-close-circle'
                        }}
                    </v-icon>
                    {{ result.passed ? '¡Aprobado!' : 'No aprobado' }}
                </v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols="4">
                            <div
                                class="text-h3 text-center"
                                :class="
                                    result.passed
                                        ? 'text-success'
                                        : 'text-error'
                                "
                            >
                                {{ Math.round(result.score) }}%
                            </div>
                            <div class="text-caption text-center">
                                Puntuación
                            </div>
                        </v-col>
                        <v-col cols="4">
                            <div class="text-h3 text-center">
                                {{ result.total_points }}/{{
                                    result.max_points
                                }}
                            </div>
                            <div class="text-caption text-center">Puntos</div>
                        </v-col>
                        <v-col cols="4">
                            <div class="text-h3 text-center">
                                {{ quiz?.passing_score }}%
                            </div>
                            <div class="text-caption text-center">
                                Mínimo para aprobar
                            </div>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>

            <!-- Answer review -->
            <v-card v-for="(q, i) in quiz?.questions" :key="q.id" class="mb-3">
                <v-card-text>
                    <div class="d-flex align-center mb-2">
                        <v-chip
                            :color="
                                result.answers.find(
                                    (a) => a.question_id === q.id,
                                )?.is_correct
                                    ? 'success'
                                    : 'error'
                            "
                            size="small"
                            class="mr-2"
                        >
                            {{
                                result.answers.find(
                                    (a) => a.question_id === q.id,
                                )?.is_correct
                                    ? '✓'
                                    : '✗'
                            }}
                        </v-chip>
                        <strong>Pregunta {{ i + 1 }}:</strong>&nbsp;{{
                            q.question_text
                        }}
                    </div>
                    <div class="text-caption text-grey">
                        Puntos:
                        {{
                            result.answers.find((a) => a.question_id === q.id)
                                ?.points_earned ?? 0
                        }}
                        / {{ q.points }}
                    </div>
                </v-card-text>
            </v-card>
        </template>

        <!-- Quiz in progress -->
        <template v-else-if="quiz && attemptId">
            <v-card class="mb-4">
                <v-card-title class="d-flex justify-space-between align-center">
                    <span>{{ quiz.title }}</span>
                    <v-chip
                        v-if="timeRemaining !== null"
                        :color="timeRemaining < 60 ? 'error' : 'primary'"
                        variant="elevated"
                    >
                        <PhClock :size="16" class="mr-1" />
                        {{ formatTime(timeRemaining) }}
                    </v-chip>
                </v-card-title>
                <v-card-subtitle>
                    Pregunta {{ currentQuestion + 1 }} de {{ totalQuestions }} ·
                    {{ answeredCount }} respondidas
                </v-card-subtitle>
                <v-progress-linear
                    :model-value="(answeredCount / totalQuestions) * 100"
                    color="primary"
                    height="4"
                />
            </v-card>

            <!-- Question card -->
            <v-card v-if="currentQ" class="mb-4">
                <v-card-text>
                    <div class="text-h6 mb-4">{{ currentQ.question_text }}</div>

                    <!-- Multiple choice -->
                    <template
                        v-if="
                            currentQ.question_type === 'multiple_choice' &&
                            currentQ.options
                        "
                    >
                        <v-radio-group
                            v-model="answers[currentQ.id]"
                            @update:model-value="
                                (v: any) => setAnswer(currentQ!.id, v)
                            "
                        >
                            <v-radio
                                v-for="opt in currentQ.options"
                                :key="opt.id"
                                :label="opt.text"
                                :value="opt.id"
                            />
                        </v-radio-group>
                    </template>

                    <!-- True/False -->
                    <template
                        v-else-if="currentQ.question_type === 'true_false'"
                    >
                        <v-radio-group
                            v-model="answers[currentQ.id]"
                            @update:model-value="
                                (v: any) => setAnswer(currentQ!.id, v)
                            "
                        >
                            <v-radio label="Verdadero" :value="true" />
                            <v-radio label="Falso" :value="false" />
                        </v-radio-group>
                    </template>

                    <!-- Fill blank -->
                    <template
                        v-else-if="currentQ.question_type === 'fill_blank'"
                    >
                        <v-text-field
                            v-model="answers[currentQ.id]"
                            label="Tu respuesta"
                            variant="outlined"
                            @update:model-value="
                                (v: any) => setAnswer(currentQ!.id, v)
                            "
                        />
                    </template>

                    <!-- Short answer -->
                    <template
                        v-else-if="currentQ.question_type === 'short_answer'"
                    >
                        <v-textarea
                            v-model="answers[currentQ.id]"
                            label="Tu respuesta"
                            variant="outlined"
                            rows="4"
                            @update:model-value="
                                (v: any) => setAnswer(currentQ!.id, v)
                            "
                        />
                    </template>

                    <div class="text-caption text-grey mt-2">
                        {{ currentQ.points }} punto{{
                            currentQ.points > 1 ? 's' : ''
                        }}
                    </div>
                </v-card-text>
            </v-card>

            <!-- Navigation -->
            <div class="d-flex justify-space-between">
                <v-btn
                    :disabled="currentQuestion === 0"
                    variant="outlined"
                    @click="currentQuestion--"
                >
                    <PhArrowLeft :size="18" class="mr-1" /> Anterior
                </v-btn>
                <v-btn
                    v-if="currentQuestion < totalQuestions - 1"
                    color="primary"
                    @click="currentQuestion++"
                >
                    Siguiente <PhArrowRight :size="18" class="ml-1" />
                </v-btn>
                <v-btn
                    v-else
                    color="success"
                    :loading="submitting"
                    :disabled="!allAnswered"
                    @click="submitQuiz"
                >
                    <PhCheck :size="18" class="mr-1" /> Enviar respuestas
                </v-btn>
            </div>
        </template>

        <!-- Quiz intro (not started) -->
        <template v-else-if="quiz">
            <v-card>
                <v-card-title class="text-h5">{{ quiz.title }}</v-card-title>
                <v-card-text>
                    <p v-if="quiz.description" class="mb-4">
                        {{ quiz.description }}
                    </p>
                    <v-list density="compact">
                        <v-list-item>
                            <template #prepend
                                ><v-icon>mdi-help-circle</v-icon></template
                            >
                            <v-list-item-title
                                >{{
                                    quiz.questions.length
                                }}
                                preguntas</v-list-item-title
                            >
                        </v-list-item>
                        <v-list-item>
                            <template #prepend
                                ><v-icon>mdi-target</v-icon></template
                            >
                            <v-list-item-title
                                >Puntuación mínima:
                                {{ quiz.passing_score }}%</v-list-item-title
                            >
                        </v-list-item>
                        <v-list-item>
                            <template #prepend
                                ><v-icon>mdi-refresh</v-icon></template
                            >
                            <v-list-item-title
                                >Máx. intentos:
                                {{ quiz.max_attempts }}</v-list-item-title
                            >
                        </v-list-item>
                        <v-list-item v-if="quiz.time_limit_minutes">
                            <template #prepend
                                ><v-icon>mdi-clock</v-icon></template
                            >
                            <v-list-item-title
                                >Límite de tiempo:
                                {{
                                    quiz.time_limit_minutes
                                }}
                                minutos</v-list-item-title
                            >
                        </v-list-item>
                    </v-list>
                </v-card-text>
                <v-card-actions>
                    <v-btn
                        color="primary"
                        size="large"
                        block
                        @click="startAttempt"
                    >
                        Comenzar Quiz
                    </v-btn>
                </v-card-actions>
            </v-card>
        </template>
    </v-container>
</template>
