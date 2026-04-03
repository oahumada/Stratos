<template>
    <div class="pa-4">
        <h1 class="text-h5 mb-4">
            Editar política de certificado — {{ course.title }}
        </h1>

        <v-alert
            v-if="fetchError"
            type="error"
            variant="tonal"
            class="mb-4"
            :text="fetchError"
        />

        <v-alert
            v-if="saveSuccess"
            type="success"
            variant="tonal"
            class="mb-4"
            text="Política actualizada correctamente"
        />

        <v-alert
            v-if="saveError"
            type="error"
            variant="tonal"
            class="mb-4"
            :text="saveError"
        />

        <v-form ref="formRef" @submit.prevent="save">
            <v-progress-linear
                v-if="loadingCourse"
                indeterminate
                color="primary"
                class="mb-4"
                aria-label="Cargando política del curso"
            />

            <v-select
                v-model="form.cert_template_id"
                :items="certificateTemplates"
                item-title="name"
                item-value="id"
                clearable
                label="Plantilla de certificado"
                hint="Selecciona la plantilla que se usará para certificados emitidos por este curso"
                persistent-hint
                :disabled="loadingCourse || savingPolicy || loadingTemplates"
                :loading="loadingTemplates"
                class="mb-2"
                aria-label="Selector de plantilla de certificado"
            />

            <v-text-field
                v-model.number="form.cert_min_resource_completion_ratio"
                label="Ratio mínimo recursos (0-1)"
                type="number"
                step="0.01"
                min="0"
                max="1"
                :rules="resourceCompletionRules"
                validate-on="input"
                :disabled="loadingCourse || savingPolicy"
                class="mb-2"
                aria-label="Ratio mínimo de recursos completados"
            />

            <v-switch
                v-model="form.cert_require_assessment_score"
                label="Requiere puntuación de evaluación"
                :disabled="loadingCourse || savingPolicy"
                class="mb-2"
            />

            <v-text-field
                v-model.number="form.cert_min_assessment_score"
                label="Puntuación mínima evaluación (0-100)"
                type="number"
                min="0"
                max="100"
                :rules="assessmentScoreRules"
                validate-on="input"
                :disabled="
                    loadingCourse ||
                    savingPolicy ||
                    !form.cert_require_assessment_score
                "
                class="mb-4"
                aria-label="Puntuación mínima requerida en evaluación"
            />

            <div class="d-flex ga-3">
                <v-btn
                    type="submit"
                    color="primary"
                    :loading="savingPolicy"
                    :disabled="loadingCourse || savingPolicy"
                >
                    Guardar
                </v-btn>

                <v-btn
                    variant="text"
                    :disabled="loadingCourse || savingPolicy"
                    @click="resetForm"
                >
                    Restablecer
                </v-btn>
            </div>
        </v-form>
    </div>
</template>

<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

interface CoursePolicyProps {
    course?: {
        id: string | number;
        title: string;
        cert_min_resource_completion_ratio: number | null;
        cert_require_assessment_score: boolean;
        cert_min_assessment_score: number | null;
        cert_template_id: number | null;
    };
    route: {
        params?: {
            id?: string | number;
        };
    };
}

interface CertificateTemplateOption {
    id: number;
    name: string;
    description: string | null;
    is_default: boolean;
}

const pageProps = usePage().props.value as CoursePolicyProps;
const courseId = pageProps.route.params?.id || pageProps.course?.id;

const course = ref<any>(pageProps.course ?? { title: 'Curso' });
const formRef = ref();
const loadingCourse = ref(false);
const savingPolicy = ref(false);
const loadingTemplates = ref(false);
const fetchError = ref('');
const saveError = ref('');
const saveSuccess = ref(false);
const certificateTemplates = ref<CertificateTemplateOption[]>([]);

const form = ref({
    cert_template_id: course.value.cert_template_id ?? null,
    cert_min_resource_completion_ratio:
        course.value.cert_min_resource_completion_ratio ?? null,
    cert_require_assessment_score:
        course.value.cert_require_assessment_score ?? false,
    cert_min_assessment_score: course.value.cert_min_assessment_score ?? null,
});

const resourceCompletionRules = [
    (value: number | null) => value !== null || 'El ratio es obligatorio',
    (value: number | null) =>
        value === null ||
        (value >= 0 && value <= 1) ||
        'Debe estar entre 0 y 1',
];

const assessmentScoreRules = computed(() => {
    if (!form.value.cert_require_assessment_score) {
        return [];
    }

    return [
        (value: number | null) =>
            value !== null ||
            'La puntuación es obligatoria cuando está habilitada',
        (value: number | null) =>
            value === null ||
            (value >= 0 && value <= 100) ||
            'Debe estar entre 0 y 100',
    ];
});

function resetForm() {
    form.value = {
        cert_template_id: course.value.cert_template_id ?? null,
        cert_min_resource_completion_ratio:
            course.value.cert_min_resource_completion_ratio ?? null,
        cert_require_assessment_score:
            course.value.cert_require_assessment_score ?? false,
        cert_min_assessment_score:
            course.value.cert_min_assessment_score ?? null,
    };
    saveError.value = '';
    saveSuccess.value = false;
}

async function loadTemplates() {
    loadingTemplates.value = true;

    try {
        const response = await fetch('/api/lms/certificate-templates');

        if (!response.ok) {
            throw new Error(
                'No se pudieron cargar las plantillas de certificado',
            );
        }

        const json = await response.json();
        certificateTemplates.value = Array.isArray(json?.templates)
            ? json.templates
            : [];
    } catch (error) {
        saveError.value =
            error instanceof Error
                ? error.message
                : 'Error inesperado al cargar plantillas';
    } finally {
        loadingTemplates.value = false;
    }
}

onMounted(async () => {
    await loadTemplates();

    if (!pageProps.course) {
        loadingCourse.value = true;
        fetchError.value = '';

        try {
            const res = await fetch(`/api/lms/courses/${courseId}`);

            if (!res.ok) {
                throw new Error('No se pudo cargar la política del curso');
            }

            const json = await res.json();
            course.value = json;
            form.value = {
                cert_min_resource_completion_ratio:
                    json.cert_min_resource_completion_ratio,
                cert_require_assessment_score:
                    json.cert_require_assessment_score,
                cert_min_assessment_score: json.cert_min_assessment_score,
                cert_template_id: json.cert_template_id,
            };
        } catch (error) {
            fetchError.value =
                error instanceof Error
                    ? error.message
                    : 'Error inesperado al cargar el curso';
        } finally {
            loadingCourse.value = false;
        }
    }
});

async function save() {
    saveError.value = '';
    saveSuccess.value = false;

    const validation = await formRef.value?.validate?.();

    if (validation && validation.valid === false) {
        return;
    }

    savingPolicy.value = true;

    const payload = {
        cert_template_id: form.value.cert_template_id,
        cert_min_resource_completion_ratio:
            form.value.cert_min_resource_completion_ratio,
        cert_require_assessment_score: form.value.cert_require_assessment_score,
        cert_min_assessment_score: form.value.cert_require_assessment_score
            ? form.value.cert_min_assessment_score
            : null,
    };

    try {
        const response = await fetch(`/api/lms/courses/${courseId}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            const errorJson = await response.json().catch(() => null);
            throw new Error(
                errorJson?.message ??
                    'No se pudo guardar la política del curso',
            );
        }

        saveSuccess.value = true;
        router.reload({ only: ['course'] });
    } catch (error) {
        saveError.value =
            error instanceof Error
                ? error.message
                : 'Error inesperado al guardar';
    } finally {
        savingPolicy.value = false;
    }
}
</script>
