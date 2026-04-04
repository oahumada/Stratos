<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import axios from 'axios';
import {
    PhArrowLeft,
    PhArrowRight,
    PhCheck,
    PhMagicWand,
    PhPencilLine,
    PhPlus,
    PhSpinner,
    PhTrash,
} from '@phosphor-icons/vue';
import { ref, computed } from 'vue';

defineOptions({ layout: AppLayout });

// --- Types ---
interface LessonDraft {
    title: string;
    description: string;
    content_type: string;
    content_body: string;
    content_url: string;
    order: number;
    duration_minutes: number;
    status: 'pendiente' | 'generado' | 'editado';
}

interface ModuleDraft {
    title: string;
    description: string;
    order: number;
    lessons: LessonDraft[];
}

// --- Wizard state ---
const currentStep = ref(1);
const loading = ref(false);
const error = ref('');

// Step 1 — AI Design
const topic = ref('');
const targetAudience = ref('');
const skillGapsText = ref('');
const durationTarget = ref(8);
const level = ref<'beginner' | 'intermediate' | 'advanced'>('intermediate');
const learningObjectives = ref<string[]>([]);
const assessmentPlan = ref('');
const courseOutline = ref('');

// Step 2 — Structure
const modules = ref<ModuleDraft[]>([]);

// Step 4 — Settings
const courseTitle = ref('');
const courseDescription = ref('');
const courseCategory = ref('');
const courseXpPoints = ref(100);
const certMinCompletion = ref(80);
const certRequireAssessment = ref(false);
const certMinAssessmentScore = ref(70);
const certTemplateId = ref<number | null>(null);
const certificateTemplates = ref<{ id: number; name: string }[]>([]);

// Step 5 — Review
const reviewResult = ref<{
    score: number;
    strengths: string[];
    improvements: string[];
    suggestions: string[];
} | null>(null);
const savedCourseId = ref<number | null>(null);

const levelOptions = [
    { title: 'Principiante', value: 'beginner' },
    { title: 'Intermedio', value: 'intermediate' },
    { title: 'Avanzado', value: 'advanced' },
];

const contentTypeOptions = [
    { title: 'Artículo', value: 'article' },
    { title: 'Video', value: 'video' },
    { title: 'Ejercicio', value: 'exercise' },
    { title: 'Quiz', value: 'quiz' },
];

const estimatedDurationMinutes = computed(() => {
    return modules.value.reduce(
        (total, m) =>
            total +
            m.lessons.reduce((lt, l) => lt + (l.duration_minutes || 0), 0),
        0,
    );
});

const statusColor = (status: string) => {
    if (status === 'generado') return 'success';
    if (status === 'editado') return 'info';
    return 'warning';
};

// --- API calls ---
async function generateOutline() {
    loading.value = true;
    error.value = '';
    try {
        const skillGaps = skillGapsText.value
            ? skillGapsText.value.split(',').map((s: string) => s.trim()).filter(Boolean)
            : [];
        const { data } = await axios.post('/api/lms/course-designer/generate-outline', {
            topic: topic.value,
            target_audience: targetAudience.value,
            skill_gaps: skillGaps,
            duration_target: durationTarget.value,
            level: level.value,
        });

        const response = data.response ? JSON.parse(data.response) : data;

        courseOutline.value = response.course_outline || '';
        learningObjectives.value = response.learning_objectives || [];
        assessmentPlan.value = response.assessment_plan || '';
        courseTitle.value = topic.value;
        courseDescription.value = response.course_outline || '';

        modules.value = (response.modules || []).map(
            (m: any, idx: number) => ({
                title: m.title,
                description: m.description || '',
                order: idx + 1,
                lessons: (m.lessons || []).map(
                    (l: any, lidx: number): LessonDraft => ({
                        title: l.title,
                        description: '',
                        content_type: l.content_type || 'article',
                        content_body: '',
                        content_url: '',
                        order: lidx + 1,
                        duration_minutes: l.duration_minutes || 30,
                        status: 'pendiente',
                    }),
                ),
            }),
        );
    } catch (e: any) {
        error.value =
            e.response?.data?.message || 'Error generando outline con IA';
    } finally {
        loading.value = false;
    }
}

async function generateLessonContent(
    moduleIndex: number,
    lessonIndex: number,
) {
    const lesson = modules.value[moduleIndex].lessons[lessonIndex];
    const mod = modules.value[moduleIndex];
    lesson.status = 'pendiente';
    loading.value = true;
    error.value = '';
    try {
        const { data } = await axios.post(
            '/api/lms/course-designer/generate-content',
            {
                lesson_title: lesson.title,
                module_context: mod.title,
                course_topic: topic.value,
                content_type: lesson.content_type,
            },
        );
        lesson.content_body = data.body || '';
        lesson.duration_minutes =
            data.estimated_duration || lesson.duration_minutes;
        lesson.status = 'generado';
    } catch (e: any) {
        error.value =
            e.response?.data?.message || 'Error generando contenido';
    } finally {
        loading.value = false;
    }
}

async function loadTemplates() {
    try {
        const { data } = await axios.get('/api/lms/certificate-templates');
        certificateTemplates.value = data.templates || [];
    } catch {
        // Templates are optional
    }
}

async function persistCourse(isActive: boolean) {
    loading.value = true;
    error.value = '';
    try {
        const { data } = await axios.post('/api/lms/course-designer/persist', {
            title: courseTitle.value,
            description: courseDescription.value,
            category: courseCategory.value,
            level: level.value,
            estimated_duration_minutes: estimatedDurationMinutes.value,
            xp_points: courseXpPoints.value,
            cert_min_resource_completion_ratio: certMinCompletion.value / 100,
            cert_require_assessment_score: certRequireAssessment.value,
            cert_min_assessment_score: certMinAssessmentScore.value,
            cert_template_id: certTemplateId.value,
            is_active: isActive,
            modules: modules.value.map((m) => ({
                title: m.title,
                order: m.order,
                lessons: m.lessons.map((l) => ({
                    title: l.title,
                    description: l.description,
                    content_type: l.content_type,
                    content_body: l.content_body,
                    content_url: l.content_url,
                    order: l.order,
                    duration_minutes: l.duration_minutes,
                })),
            })),
        });
        savedCourseId.value = data.course?.id || null;
    } catch (e: any) {
        error.value = e.response?.data?.message || 'Error guardando curso';
    } finally {
        loading.value = false;
    }
}

async function reviewCourseAI() {
    if (!savedCourseId.value) return;
    loading.value = true;
    error.value = '';
    try {
        const { data } = await axios.post(
            `/api/lms/course-designer/${savedCourseId.value}/review`,
        );
        const response = data.response ? JSON.parse(data.response) : data;
        reviewResult.value = response;
    } catch (e: any) {
        error.value = e.response?.data?.message || 'Error en revisión IA';
    } finally {
        loading.value = false;
    }
}

// --- Structure helpers ---
function addModule() {
    modules.value.push({
        title: `Módulo ${modules.value.length + 1}`,
        description: '',
        order: modules.value.length + 1,
        lessons: [],
    });
}

function removeModule(index: number) {
    modules.value.splice(index, 1);
    modules.value.forEach((m, i) => (m.order = i + 1));
}

function moveModule(index: number, direction: -1 | 1) {
    const target = index + direction;
    if (target < 0 || target >= modules.value.length) return;
    const temp = modules.value[index];
    modules.value[index] = modules.value[target];
    modules.value[target] = temp;
    modules.value.forEach((m, i) => (m.order = i + 1));
}

function addLesson(moduleIndex: number) {
    const mod = modules.value[moduleIndex];
    mod.lessons.push({
        title: `Lección ${mod.lessons.length + 1}`,
        description: '',
        content_type: 'article',
        content_body: '',
        content_url: '',
        order: mod.lessons.length + 1,
        duration_minutes: 30,
        status: 'pendiente',
    });
}

function removeLesson(moduleIndex: number, lessonIndex: number) {
    modules.value[moduleIndex].lessons.splice(lessonIndex, 1);
    modules.value[moduleIndex].lessons.forEach((l, i) => (l.order = i + 1));
}

function goToStep(step: number) {
    if (step === 4) loadTemplates();
    currentStep.value = step;
}
</script>

<template>
    <v-container fluid class="pa-6 bg-grey-lighten-4">
        <v-row>
            <v-col cols="12">
                <h1 class="text-h4 font-weight-bold mb-2">
                    <PhPencilLine :size="32" class="mr-2" />
                    Diseñador de Cursos
                </h1>
                <p class="text-subtitle-1 text-grey-darken-1 mb-4">
                    Crea cursos con asistencia de IA paso a paso
                </p>
            </v-col>
        </v-row>

        <v-alert v-if="error" type="error" closable class="mb-4" @click:close="error = ''">
            {{ error }}
        </v-alert>

        <!-- Stepper Header -->
        <v-stepper v-model="currentStep" :items="[
            { title: 'Diseño IA', value: 1 },
            { title: 'Estructura', value: 2 },
            { title: 'Contenido', value: 3 },
            { title: 'Configuración', value: 4 },
            { title: 'Revisión', value: 5 },
        ]" alt-labels>
            <template #[`item.1`]>
                <v-card flat class="pa-6">
                    <v-card-title class="text-h6">Paso 1 — Diseño con IA</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-text-field v-model="topic" label="Tema del curso" variant="outlined" />
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field v-model="targetAudience" label="Audiencia objetivo" variant="outlined" />
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field v-model="skillGapsText" label="Brechas de habilidades (separadas por coma)" variant="outlined" />
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-text-field v-model.number="durationTarget" label="Duración objetivo (horas)" type="number" variant="outlined" :min="1" :max="200" />
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-select v-model="level" :items="levelOptions" item-title="title" item-value="value" label="Nivel" variant="outlined" />
                            </v-col>
                        </v-row>
                        <v-btn color="primary" :loading="loading" :disabled="!topic || !targetAudience" @click="generateOutline" class="mt-2">
                            <PhMagicWand :size="20" class="mr-2" />
                            Generar Outline con IA
                        </v-btn>

                        <div v-if="learningObjectives.length" class="mt-6">
                            <h3 class="text-subtitle-1 font-weight-bold mb-2">Objetivos de Aprendizaje</h3>
                            <v-chip v-for="(obj, i) in learningObjectives" :key="i" color="primary" variant="tonal" class="ma-1">
                                {{ obj }}
                            </v-chip>
                        </div>

                        <div v-if="modules.length" class="mt-4">
                            <h3 class="text-subtitle-1 font-weight-bold mb-2">Módulos ({{ modules.length }})</h3>
                            <v-expansion-panels variant="accordion">
                                <v-expansion-panel v-for="(mod, mi) in modules" :key="mi">
                                    <v-expansion-panel-title>{{ mod.order }}. {{ mod.title }}</v-expansion-panel-title>
                                    <v-expansion-panel-text>
                                        <v-list density="compact">
                                            <v-list-item v-for="(lesson, li) in mod.lessons" :key="li">
                                                <template #prepend>
                                                    <v-chip size="x-small" :color="statusColor(lesson.status)">{{ lesson.content_type }}</v-chip>
                                                </template>
                                                {{ lesson.title }} — {{ lesson.duration_minutes }} min
                                            </v-list-item>
                                        </v-list>
                                    </v-expansion-panel-text>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn color="primary" :disabled="!modules.length" @click="goToStep(2)">
                            Siguiente <PhArrowRight :size="18" class="ml-1" />
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </template>

            <template #[`item.2`]>
                <v-card flat class="pa-6">
                    <v-card-title class="text-h6">Paso 2 — Estructura</v-card-title>
                    <v-card-text>
                        <v-btn color="secondary" variant="tonal" @click="addModule" class="mb-4">
                            <PhPlus :size="18" class="mr-1" /> Agregar módulo
                        </v-btn>
                        <v-expansion-panels variant="accordion">
                            <v-expansion-panel v-for="(mod, mi) in modules" :key="mi">
                                <v-expansion-panel-title>
                                    {{ mod.order }}. {{ mod.title }}
                                    <template #actions>
                                        <v-btn icon size="x-small" variant="text" @click.stop="moveModule(mi, -1)" :disabled="mi === 0">▲</v-btn>
                                        <v-btn icon size="x-small" variant="text" @click.stop="moveModule(mi, 1)" :disabled="mi === modules.length - 1">▼</v-btn>
                                        <v-btn icon size="x-small" variant="text" color="error" @click.stop="removeModule(mi)">
                                            <PhTrash :size="16" />
                                        </v-btn>
                                    </template>
                                </v-expansion-panel-title>
                                <v-expansion-panel-text>
                                    <v-text-field v-model="mod.title" label="Título del módulo" variant="outlined" density="compact" class="mb-2" />
                                    <v-divider class="mb-3" />
                                    <div v-for="(lesson, li) in mod.lessons" :key="li" class="d-flex align-center ga-2 mb-2">
                                        <v-text-field v-model="lesson.title" label="Lección" variant="outlined" density="compact" hide-details />
                                        <v-select v-model="lesson.content_type" :items="contentTypeOptions" item-title="title" item-value="value" label="Tipo" variant="outlined" density="compact" hide-details style="max-width: 160px" />
                                        <v-text-field v-model.number="lesson.duration_minutes" label="Min" type="number" variant="outlined" density="compact" hide-details style="max-width: 80px" />
                                        <v-btn icon size="x-small" variant="text" color="error" @click="removeLesson(mi, li)">
                                            <PhTrash :size="16" />
                                        </v-btn>
                                    </div>
                                    <v-btn size="small" variant="text" @click="addLesson(mi)">
                                        <PhPlus :size="16" class="mr-1" /> Agregar lección
                                    </v-btn>
                                </v-expansion-panel-text>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn @click="goToStep(1)"><PhArrowLeft :size="18" class="mr-1" /> Anterior</v-btn>
                        <v-spacer />
                        <v-btn color="primary" @click="goToStep(3)">
                            Siguiente <PhArrowRight :size="18" class="ml-1" />
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </template>

            <template #[`item.3`]>
                <v-card flat class="pa-6">
                    <v-card-title class="text-h6">Paso 3 — Contenido</v-card-title>
                    <v-card-text>
                        <v-expansion-panels variant="accordion">
                            <v-expansion-panel v-for="(mod, mi) in modules" :key="mi">
                                <v-expansion-panel-title>{{ mod.order }}. {{ mod.title }}</v-expansion-panel-title>
                                <v-expansion-panel-text>
                                    <div v-for="(lesson, li) in mod.lessons" :key="li" class="mb-4">
                                        <div class="d-flex align-center ga-2 mb-2">
                                            <strong>{{ lesson.title }}</strong>
                                            <v-chip size="x-small" :color="statusColor(lesson.status)">
                                                {{ lesson.status }}
                                            </v-chip>
                                            <v-btn size="small" color="primary" variant="tonal" :loading="loading" @click="generateLessonContent(mi, li)">
                                                <PhMagicWand :size="16" class="mr-1" /> Generar contenido IA
                                            </v-btn>
                                        </div>
                                        <v-card v-if="lesson.content_body" variant="outlined" class="mb-2">
                                            <v-card-text>
                                                <div v-html="lesson.content_body" />
                                            </v-card-text>
                                        </v-card>
                                        <v-textarea v-model="lesson.content_body" label="Contenido (HTML)" variant="outlined" density="compact" rows="4" @input="lesson.status = 'editado'" />
                                    </div>
                                </v-expansion-panel-text>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn @click="goToStep(2)"><PhArrowLeft :size="18" class="mr-1" /> Anterior</v-btn>
                        <v-spacer />
                        <v-btn color="primary" @click="goToStep(4)">
                            Siguiente <PhArrowRight :size="18" class="ml-1" />
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </template>

            <template #[`item.4`]>
                <v-card flat class="pa-6">
                    <v-card-title class="text-h6">Paso 4 — Configuración</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-text-field v-model="courseTitle" label="Título del curso" variant="outlined" />
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field v-model="courseCategory" label="Categoría" variant="outlined" />
                            </v-col>
                            <v-col cols="12">
                                <v-textarea v-model="courseDescription" label="Descripción" variant="outlined" rows="3" />
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-select v-model="level" :items="levelOptions" item-title="title" item-value="value" label="Nivel" variant="outlined" />
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field v-model.number="courseXpPoints" label="Puntos XP" type="number" variant="outlined" />
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field :model-value="estimatedDurationMinutes" label="Duración estimada (min)" variant="outlined" readonly />
                            </v-col>
                        </v-row>

                        <v-divider class="my-4" />
                        <h3 class="text-subtitle-1 font-weight-bold mb-3">Política de Certificación</h3>
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-slider v-model="certMinCompletion" label="Completitud mínima (%)" :min="0" :max="100" :step="5" thumb-label />
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-switch v-model="certRequireAssessment" label="Requiere evaluación" color="primary" />
                            </v-col>
                            <v-col v-if="certRequireAssessment" cols="12" md="6">
                                <v-text-field v-model.number="certMinAssessmentScore" label="Puntaje mínimo de evaluación" type="number" variant="outlined" :min="0" :max="100" />
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-select v-model="certTemplateId" :items="certificateTemplates" item-title="name" item-value="id" label="Plantilla de certificado" variant="outlined" clearable />
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn @click="goToStep(3)"><PhArrowLeft :size="18" class="mr-1" /> Anterior</v-btn>
                        <v-spacer />
                        <v-btn color="primary" @click="goToStep(5)">
                            Siguiente <PhArrowRight :size="18" class="ml-1" />
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </template>

            <template #[`item.5`]>
                <v-card flat class="pa-6">
                    <v-card-title class="text-h6">Paso 5 — Revisión y Publicación</v-card-title>
                    <v-card-text>
                        <v-card variant="outlined" class="mb-4">
                            <v-card-title>{{ courseTitle }}</v-card-title>
                            <v-card-subtitle>{{ courseCategory }} · {{ level }} · {{ estimatedDurationMinutes }} min · {{ courseXpPoints }} XP</v-card-subtitle>
                            <v-card-text>
                                <p>{{ courseDescription }}</p>
                                <v-divider class="my-3" />
                                <strong>{{ modules.length }} módulos</strong> ·
                                <strong>{{ modules.reduce((t, m) => t + m.lessons.length, 0) }} lecciones</strong>
                                <v-list density="compact" class="mt-2">
                                    <v-list-item v-for="(mod, mi) in modules" :key="mi">
                                        <strong>{{ mod.order }}. {{ mod.title }}</strong> — {{ mod.lessons.length }} lecciones
                                    </v-list-item>
                                </v-list>
                            </v-card-text>
                        </v-card>

                        <div v-if="reviewResult" class="mb-4">
                            <v-card variant="tonal" :color="(reviewResult.score ?? 0) >= 70 ? 'success' : 'warning'">
                                <v-card-title>Puntuación IA: {{ reviewResult.score }}/100</v-card-title>
                                <v-card-text>
                                    <div v-if="reviewResult.strengths?.length" class="mb-2">
                                        <strong>Fortalezas:</strong>
                                        <v-chip v-for="(s, i) in reviewResult.strengths" :key="i" color="success" variant="tonal" size="small" class="ma-1">{{ s }}</v-chip>
                                    </div>
                                    <div v-if="reviewResult.improvements?.length" class="mb-2">
                                        <strong>Mejoras:</strong>
                                        <v-chip v-for="(s, i) in reviewResult.improvements" :key="i" color="warning" variant="tonal" size="small" class="ma-1">{{ s }}</v-chip>
                                    </div>
                                    <div v-if="reviewResult.suggestions?.length">
                                        <strong>Sugerencias:</strong>
                                        <v-chip v-for="(s, i) in reviewResult.suggestions" :key="i" color="info" variant="tonal" size="small" class="ma-1">{{ s }}</v-chip>
                                    </div>
                                </v-card-text>
                            </v-card>
                        </div>

                        <v-alert v-if="savedCourseId" type="success" class="mb-4">
                            Curso guardado exitosamente (ID: {{ savedCourseId }})
                        </v-alert>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn @click="goToStep(4)"><PhArrowLeft :size="18" class="mr-1" /> Anterior</v-btn>
                        <v-spacer />
                        <v-btn v-if="savedCourseId" color="info" variant="tonal" :loading="loading" @click="reviewCourseAI">
                            <PhMagicWand :size="18" class="mr-1" /> Revisión IA
                        </v-btn>
                        <v-btn color="secondary" :loading="loading" :disabled="!courseTitle" @click="persistCourse(false)">
                            <PhPencilLine :size="18" class="mr-1" /> Guardar como Borrador
                        </v-btn>
                        <v-btn color="primary" :loading="loading" :disabled="!courseTitle" @click="persistCourse(true)">
                            <PhCheck :size="18" class="mr-1" /> Publicar
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </template>
        </v-stepper>
    </v-container>
</template>
