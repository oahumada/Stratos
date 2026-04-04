<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {
    PhArrowDown,
    PhArrowUp,
    PhFloppyDisk,
    PhMagicWand,
    PhPlus,
    PhTrash,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { computed, ref } from 'vue';

defineOptions({ layout: AppLayout });

interface QuestionDraft {
    question_text: string;
    question_type:
        | 'multiple_choice'
        | 'true_false'
        | 'fill_blank'
        | 'matching'
        | 'short_answer';
    options: { id: string; text: string }[];
    correct_answer: any[];
    points: number;
    explanation: string;
    order: number;
}

const props = defineProps<{ quizId?: number; lessonId?: number }>();

const loading = ref(false);
const saving = ref(false);
const generating = ref(false);
const error = ref('');
const success = ref('');

const title = ref('');
const description = ref('');
const passingScore = ref(70);
const maxAttempts = ref(3);
const timeLimitMinutes = ref<number | null>(null);
const shuffleQuestions = ref(false);
const questions = ref<QuestionDraft[]>([]);

const questionTypes = [
    { value: 'multiple_choice', title: 'Opción múltiple' },
    { value: 'true_false', title: 'Verdadero/Falso' },
    { value: 'fill_blank', title: 'Completar' },
    { value: 'short_answer', title: 'Respuesta corta' },
];

const totalPoints = computed(() =>
    questions.value.reduce((sum, q) => sum + q.points, 0),
);

function addQuestion() {
    questions.value.push({
        question_text: '',
        question_type: 'multiple_choice',
        options: [
            { id: 'a', text: '' },
            { id: 'b', text: '' },
            { id: 'c', text: '' },
            { id: 'd', text: '' },
        ],
        correct_answer: [],
        points: 1,
        explanation: '',
        order: questions.value.length,
    });
}

function removeQuestion(index: number) {
    questions.value.splice(index, 1);
    questions.value.forEach((q, i) => (q.order = i));
}

function moveQuestion(index: number, direction: -1 | 1) {
    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= questions.value.length) return;
    const temp = questions.value[index];
    questions.value[index] = questions.value[newIndex];
    questions.value[newIndex] = temp;
    questions.value.forEach((q, i) => (q.order = i));
}

function addOption(qIndex: number) {
    const q = questions.value[qIndex];
    const nextId = String.fromCharCode(97 + q.options.length);
    q.options.push({ id: nextId, text: '' });
}

function removeOption(qIndex: number, optIndex: number) {
    questions.value[qIndex].options.splice(optIndex, 1);
}

async function saveQuiz() {
    try {
        saving.value = true;
        error.value = '';
        const payload = {
            title: title.value,
            description: description.value,
            passing_score: passingScore.value,
            max_attempts: maxAttempts.value,
            time_limit_minutes: timeLimitMinutes.value,
            shuffle_questions: shuffleQuestions.value,
            lesson_id: props.lessonId ?? null,
            questions: questions.value,
        };
        if (props.quizId) {
            await axios.put(`/api/lms/quizzes/${props.quizId}`, payload);
        } else {
            await axios.post('/api/lms/quizzes', payload);
        }
        success.value = 'Quiz guardado exitosamente';
    } catch (e: any) {
        error.value = e.response?.data?.message ?? 'Error al guardar';
    } finally {
        saving.value = false;
    }
}

async function generateWithAi() {
    if (!props.quizId) {
        error.value = 'Guarda el quiz primero para generar preguntas con IA';
        return;
    }
    try {
        generating.value = true;
        error.value = '';
        const { data } = await axios.post(
            `/api/lms/quizzes/${props.quizId}/generate-questions`,
            {
                lesson_content: '',
            },
        );
        const generated = data.data?.questions ?? data.questions ?? [];
        generated.forEach((q: any) => {
            questions.value.push({
                question_text: q.question_text ?? q.text ?? '',
                question_type: q.question_type ?? 'multiple_choice',
                options: q.options ?? [],
                correct_answer: q.correct_answer ?? [],
                points: q.points ?? 1,
                explanation: q.explanation ?? '',
                order: questions.value.length,
            });
        });
        success.value = `${generated.length} preguntas generadas con IA`;
    } catch (e: any) {
        error.value = e.response?.data?.message ?? 'Error generando preguntas';
    } finally {
        generating.value = false;
    }
}
</script>

<template>
    <v-container class="py-6" max-width="1000">
        <v-card class="mb-4">
            <v-card-title class="text-h5">
                {{ props.quizId ? 'Editar Quiz' : 'Crear Quiz' }}
            </v-card-title>
        </v-card>

        <v-alert
            v-if="error"
            type="error"
            class="mb-4"
            closable
            @click:close="error = ''"
            >{{ error }}</v-alert
        >
        <v-alert
            v-if="success"
            type="success"
            class="mb-4"
            closable
            @click:close="success = ''"
            >{{ success }}</v-alert
        >

        <!-- Quiz settings -->
        <v-card class="mb-4">
            <v-card-title>Configuración</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="title"
                            label="Título del quiz"
                            variant="outlined"
                        />
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="description"
                            label="Descripción (opcional)"
                            variant="outlined"
                        />
                    </v-col>
                    <v-col cols="6" md="3">
                        <v-text-field
                            v-model.number="passingScore"
                            label="% mínimo para aprobar"
                            type="number"
                            variant="outlined"
                            min="0"
                            max="100"
                        />
                    </v-col>
                    <v-col cols="6" md="3">
                        <v-text-field
                            v-model.number="maxAttempts"
                            label="Máx. intentos"
                            type="number"
                            variant="outlined"
                            min="1"
                        />
                    </v-col>
                    <v-col cols="6" md="3">
                        <v-text-field
                            v-model.number="timeLimitMinutes"
                            label="Límite (min)"
                            type="number"
                            variant="outlined"
                            clearable
                        />
                    </v-col>
                    <v-col cols="6" md="3">
                        <v-switch
                            v-model="shuffleQuestions"
                            label="Aleatorizar preguntas"
                            color="primary"
                        />
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Questions -->
        <div class="d-flex justify-space-between align-center mb-3">
            <h3>
                Preguntas ({{ questions.length }}) · {{ totalPoints }} puntos
            </h3>
            <div>
                <v-btn
                    variant="outlined"
                    color="secondary"
                    class="mr-2"
                    :loading="generating"
                    @click="generateWithAi"
                >
                    <PhMagicWand :size="18" class="mr-1" /> Generar con IA
                </v-btn>
                <v-btn color="primary" @click="addQuestion">
                    <PhPlus :size="18" class="mr-1" /> Agregar pregunta
                </v-btn>
            </div>
        </div>

        <v-card v-for="(q, qi) in questions" :key="qi" class="mb-3">
            <v-card-text>
                <div class="d-flex justify-space-between align-center mb-3">
                    <span class="text-subtitle-1 font-weight-bold"
                        >Pregunta {{ qi + 1 }}</span
                    >
                    <div>
                        <v-btn
                            icon
                            size="small"
                            variant="text"
                            :disabled="qi === 0"
                            @click="moveQuestion(qi, -1)"
                        >
                            <PhArrowUp :size="16" />
                        </v-btn>
                        <v-btn
                            icon
                            size="small"
                            variant="text"
                            :disabled="qi === questions.length - 1"
                            @click="moveQuestion(qi, 1)"
                        >
                            <PhArrowDown :size="16" />
                        </v-btn>
                        <v-btn
                            icon
                            size="small"
                            variant="text"
                            color="error"
                            @click="removeQuestion(qi)"
                        >
                            <PhTrash :size="16" />
                        </v-btn>
                    </div>
                </div>

                <v-row>
                    <v-col cols="12" md="8">
                        <v-textarea
                            v-model="q.question_text"
                            label="Texto de la pregunta"
                            variant="outlined"
                            rows="2"
                        />
                    </v-col>
                    <v-col cols="6" md="2">
                        <v-select
                            v-model="q.question_type"
                            :items="questionTypes"
                            item-value="value"
                            item-title="title"
                            label="Tipo"
                            variant="outlined"
                        />
                    </v-col>
                    <v-col cols="6" md="2">
                        <v-text-field
                            v-model.number="q.points"
                            label="Puntos"
                            type="number"
                            variant="outlined"
                            min="1"
                        />
                    </v-col>
                </v-row>

                <!-- Options for multiple choice -->
                <template v-if="q.question_type === 'multiple_choice'">
                    <div
                        v-for="(opt, oi) in q.options"
                        :key="oi"
                        class="d-flex align-center mb-2"
                    >
                        <v-checkbox
                            :model-value="q.correct_answer.includes(opt.id)"
                            @update:model-value="
                                (v: any) =>
                                    v
                                        ? q.correct_answer.push(opt.id)
                                        : (q.correct_answer =
                                              q.correct_answer.filter(
                                                  (a: string) => a !== opt.id,
                                              ))
                            "
                            hide-details
                            density="compact"
                            class="mr-2"
                        />
                        <v-text-field
                            v-model="opt.text"
                            :label="`Opción ${opt.id.toUpperCase()}`"
                            variant="outlined"
                            density="compact"
                            hide-details
                            class="mr-2 flex-grow-1"
                        />
                        <v-btn
                            icon
                            size="x-small"
                            variant="text"
                            color="error"
                            @click="removeOption(qi, oi)"
                        >
                            <PhTrash :size="14" />
                        </v-btn>
                    </div>
                    <v-btn size="small" variant="text" @click="addOption(qi)">
                        <PhPlus :size="14" class="mr-1" /> Agregar opción
                    </v-btn>
                </template>

                <!-- True/False -->
                <template v-else-if="q.question_type === 'true_false'">
                    <v-radio-group v-model="q.correct_answer[0]" inline>
                        <v-radio label="Verdadero" :value="true" />
                        <v-radio label="Falso" :value="false" />
                    </v-radio-group>
                </template>

                <!-- Fill blank -->
                <template v-else-if="q.question_type === 'fill_blank'">
                    <v-text-field
                        v-model="q.correct_answer[0]"
                        label="Respuesta correcta"
                        variant="outlined"
                    />
                </template>

                <v-text-field
                    v-model="q.explanation"
                    label="Explicación (mostrada después de responder)"
                    variant="outlined"
                    class="mt-2"
                />
            </v-card-text>
        </v-card>

        <!-- Save -->
        <div class="d-flex mt-4 justify-end">
            <v-btn
                color="success"
                size="large"
                :loading="saving"
                @click="saveQuiz"
            >
                <PhFloppyDisk :size="20" class="mr-1" /> Guardar Quiz
            </v-btn>
        </div>
    </v-container>
</template>
