<script setup lang="ts">
import { useApi } from '@/composables/useApi';
import { index as mentoringIndex } from '@/routes/mentoring';
import { show as peopleShow } from '@/routes/people';
import { computed, onMounted, ref } from 'vue';

interface KpiSummary {
    total_courses: number;
    total_enrollments: number;
    completed_enrollments: number;
    completion_rate_pct: number;
    issued_certificates: number;
    certification_rate_pct: number;
    issued_certificates_last_7d: number;
    at_risk_enrollments: number;
    at_risk_rate_pct: number;
}

interface PerCourse {
    course_id: number;
    course_title: string;
    total_enrollments: number;
    completed_enrollments: number;
    completion_rate_pct: number;
    issued_certificates: number;
    certification_rate_pct: number;
}

interface AnalyticsData {
    summary: KpiSummary;
    per_course: PerCourse[];
    at_risk_learners: AtRiskLearner[];
}

interface AtRiskLearner {
    enrollment_id: number;
    course_id: number;
    course_title: string;
    user_id: number;
    user_name: string;
    user_email: string | null;
    development_action_id: number | null;
    status: string;
    progress_percentage: number;
    assessment_score: number | null;
    resource_completion_pct: number | null;
}

const { get, post, patch, isLoading, error } = useApi();

const analytics = ref<AnalyticsData | null>(null);
const interventionDialog = ref(false);
const selectedLearner = ref<AtRiskLearner | null>(null);
const interventionStatusesByEnrollment = ref<Record<string, string>>({});
const clearTrackingDialog = ref(false);
const feedbackSnackbar = ref(false);
const feedbackMessage = ref('');
const showCompletedInterventions = ref(false);

onMounted(async () => {
    try {
        const [analyticsResponse, interventionsResponse, preferencesResponse] =
            await Promise.all([
                get('/api/lms/analytics/overview'),
                get('/api/lms/interventions'),
                get('/api/lms/preferences'),
            ]);

        analytics.value = analyticsResponse?.data ?? null;
        interventionStatusesByEnrollment.value =
            interventionsResponse?.data?.statuses_by_enrollment ?? {};
        showCompletedInterventions.value =
            preferencesResponse?.data?.show_completed_interventions ?? false;
    } catch {
        // error already set by useApi
    }
});

const onToggleShowCompletedInterventions = async (
    value: boolean | null = false,
): Promise<void> => {
    const normalizedValue = value === true;
    const previous = showCompletedInterventions.value;
    showCompletedInterventions.value = normalizedValue;

    try {
        const response = await patch('/api/lms/preferences', {
            show_completed_interventions: normalizedValue,
        });

        showCompletedInterventions.value =
            response?.data?.show_completed_interventions ?? normalizedValue;
    } catch {
        showCompletedInterventions.value = previous;
        feedbackMessage.value =
            'No se pudo guardar la preferencia de visualización.';
        feedbackSnackbar.value = true;
    }
};

const kpiCards = [
    {
        key: 'total_courses' as const,
        label: 'Cursos activos',
        icon: 'mdi-book-open-variant',
        color: 'indigo',
        format: (v: number): string => v.toString(),
    },
    {
        key: 'total_enrollments' as const,
        label: 'Total inscripciones',
        icon: 'mdi-account-group',
        color: 'blue',
        format: (v: number): string => v.toString(),
    },
    {
        key: 'completion_rate_pct' as const,
        label: 'Tasa de completado',
        icon: 'mdi-check-circle-outline',
        color: 'green',
        format: (v: number) => `${v.toFixed(1)}%`,
    },
    {
        key: 'issued_certificates' as const,
        label: 'Certificados emitidos',
        icon: 'mdi-certificate-outline',
        color: 'amber',
        format: (v: number): string => v.toString(),
    },
    {
        key: 'certification_rate_pct' as const,
        label: 'Tasa de certificación',
        icon: 'mdi-trophy-outline',
        color: 'deep-purple',
        format: (v: number) => `${v.toFixed(1)}%`,
    },
    {
        key: 'issued_certificates_last_7d' as const,
        label: 'Certificados (7d)',
        icon: 'mdi-calendar-week',
        color: 'teal',
        format: (v: number): string => v.toString(),
    },
] as const;

const tableHeaders = [
    { title: 'Curso', key: 'course_title', sortable: true },
    { title: 'Inscripciones', key: 'total_enrollments', sortable: true },
    { title: 'Completados', key: 'completed_enrollments', sortable: true },
    {
        title: 'Completado %',
        key: 'completion_rate_pct',
        sortable: true,
    },
    { title: 'Certificados', key: 'issued_certificates', sortable: true },
    {
        title: 'Certificación %',
        key: 'certification_rate_pct',
        sortable: true,
    },
];

const atRiskHeaders = [
    { title: 'Learner', key: 'user_name', sortable: true },
    { title: 'Curso', key: 'course_title', sortable: true },
    { title: 'Seguimiento', key: 'follow_up', sortable: false },
    { title: 'Progreso %', key: 'progress_percentage', sortable: true },
    { title: 'Score', key: 'assessment_score', sortable: true },
    { title: 'Recursos %', key: 'resource_completion_pct', sortable: true },
    { title: 'Estado', key: 'status', sortable: true },
    { title: 'Acciones', key: 'actions', sortable: false },
];

const getInterventionStatus = (enrollmentId: number): string | null => {
    return interventionStatusesByEnrollment.value[String(enrollmentId)] ?? null;
};

const visibleAtRiskLearners = computed(() => {
    if (!analytics.value) {
        return [];
    }

    if (showCompletedInterventions.value) {
        return analytics.value.at_risk_learners;
    }

    return analytics.value.at_risk_learners.filter(
        (learner) =>
            getInterventionStatus(learner.enrollment_id) !== 'completed',
    );
});

const visibleAtRiskRatePct = computed(() => {
    if (!analytics.value) {
        return 0;
    }

    const totalEnrollments = analytics.value.summary.total_enrollments;
    if (totalEnrollments <= 0) {
        return 0;
    }

    return Number(
        ((visibleAtRiskLearners.value.length / totalEnrollments) * 100).toFixed(
            1,
        ),
    );
});

const markInterventionStarted = async (
    learner: AtRiskLearner,
): Promise<void> => {
    const enrollmentId = learner.enrollment_id;

    if (!Number.isFinite(enrollmentId)) {
        return;
    }

    if (getInterventionStatus(enrollmentId) !== 'started') {
        await post('/api/lms/interventions', {
            lms_enrollment_id: enrollmentId,
            lms_course_id: learner.course_id,
            user_id: learner.user_id > 0 ? learner.user_id : null,
            source: 'lms_risk',
            metadata: {
                progress_percentage: learner.progress_percentage,
                assessment_score: learner.assessment_score,
                resource_completion_pct: learner.resource_completion_pct,
            },
        });

        interventionStatusesByEnrollment.value = {
            ...interventionStatusesByEnrollment.value,
            [String(enrollmentId)]: 'started',
        };
        feedbackMessage.value = 'Intervención marcada como iniciada.';
        feedbackSnackbar.value = true;
    }
};

const clearInterventionTracking = (): void => {
    interventionStatusesByEnrollment.value = {};
};

const requestClearInterventionTracking = (): void => {
    clearTrackingDialog.value = true;
};

const cancelClearInterventionTracking = (): void => {
    clearTrackingDialog.value = false;
};

const confirmClearInterventionTracking = async (): Promise<void> => {
    await post('/api/lms/interventions/reset');

    clearInterventionTracking();
    clearTrackingDialog.value = false;
    feedbackMessage.value = 'Seguimiento limpiado correctamente.';
    feedbackSnackbar.value = true;
};

const goToMentoringFromDialog = async (): Promise<void> => {
    if (!selectedLearner.value) {
        return;
    }

    try {
        await markInterventionStarted(selectedLearner.value);

        const targetUrl = interventionHref();
        if (targetUrl) {
            globalThis.location.href = targetUrl;
        }
    } catch {
        feedbackMessage.value =
            'No se pudo registrar la intervención en backend.';
        feedbackSnackbar.value = true;
    }
};

const openInterventionDialog = (learner: AtRiskLearner): void => {
    selectedLearner.value = learner;
    interventionDialog.value = true;
};

const closeInterventionDialog = (): void => {
    interventionDialog.value = false;
    selectedLearner.value = null;
};

const interventionHref = (): string | undefined => {
    if (!selectedLearner.value || selectedLearner.value.user_id <= 0) {
        return undefined;
    }

    const learner = selectedLearner.value;
    const query: Record<string, string | number> = {
        people_id: learner.user_id,
        source: 'lms_risk',
        course_id: learner.course_id,
        enrollment_id: learner.enrollment_id,
        progress: learner.progress_percentage,
    };

    if (learner.development_action_id !== null) {
        query.development_action_id = learner.development_action_id;
    }

    if (learner.assessment_score !== null) {
        query.assessment = learner.assessment_score;
    }

    if (learner.resource_completion_pct !== null) {
        query.resource_completion = learner.resource_completion_pct;
    }

    return mentoringIndex({ query }).url;
};
</script>

<template>
    <div>
        <!-- Error -->
        <v-alert
            v-if="error"
            type="error"
            variant="tonal"
            class="mb-4"
            density="compact"
        >
            No se pudieron cargar los KPIs: {{ error }}
        </v-alert>

        <!-- KPI Cards skeleton -->
        <v-row v-if="isLoading" class="mb-4">
            <v-col v-for="i in 6" :key="i" cols="12" sm="6" md="4" lg="2">
                <v-skeleton-loader type="card" height="90" />
            </v-col>
        </v-row>

        <!-- KPI Cards -->
        <v-row v-else-if="analytics" class="mb-4">
            <v-col
                v-for="card in kpiCards"
                :key="card.key"
                cols="12"
                sm="6"
                md="4"
                lg="2"
            >
                <v-card
                    class="pa-4 rounded-lg"
                    elevation="0"
                    :color="`${card.color}-lighten-5`"
                >
                    <div class="d-flex align-center ga-2 mb-1">
                        <v-icon
                            :icon="card.icon"
                            :color="`${card.color}-darken-2`"
                            size="18"
                        />
                        <span
                            class="text-caption font-weight-medium"
                            :class="`text-${card.color}-darken-2`"
                        >
                            {{ card.label }}
                        </span>
                    </div>
                    <div class="text-h5 font-weight-bold">
                        {{ card.format(analytics.summary[card.key]) }}
                    </div>
                </v-card>
            </v-col>
        </v-row>

        <!-- Per-course table skeleton -->
        <v-skeleton-loader v-if="isLoading" type="table" />

        <!-- Per-course table -->
        <v-card
            v-else-if="analytics && analytics.per_course.length > 0"
            class="rounded-lg"
            elevation="0"
            variant="outlined"
        >
            <v-card-title class="text-subtitle-1 font-weight-bold pa-4 pb-2">
                KPIs por curso
            </v-card-title>
            <v-data-table
                :headers="tableHeaders"
                :items="analytics.per_course"
                density="compact"
                :items-per-page="10"
                class="text-body-2"
            >
                <template #item.completion_rate_pct="{ item }">
                    {{ item.completion_rate_pct.toFixed(1) }}%
                </template>
                <template #item.certification_rate_pct="{ item }">
                    {{ item.certification_rate_pct.toFixed(1) }}%
                </template>
            </v-data-table>
        </v-card>

        <v-card
            v-if="!isLoading && analytics"
            class="mt-4 rounded-lg"
            elevation="0"
            variant="outlined"
        >
            <v-card-title class="text-subtitle-1 font-weight-bold pa-4 pb-2">
                Learners en riesgo
            </v-card-title>

            <div class="d-flex align-center justify-space-between px-4 pb-2">
                <v-card-subtitle class="px-0 py-0">
                    {{ visibleAtRiskLearners.length }} inscripciones en riesgo
                    activas ({{ visibleAtRiskRatePct.toFixed(1) }}%)
                </v-card-subtitle>

                <div class="d-flex align-center ga-2">
                    <v-switch
                        :model-value="showCompletedInterventions"
                        color="primary"
                        density="compact"
                        hide-details
                        label="Mostrar completadas"
                        @update:model-value="onToggleShowCompletedInterventions"
                    />
                    <v-btn
                        size="small"
                        variant="text"
                        color="secondary"
                        prepend-icon="mdi-eraser"
                        :disabled="
                            Object.keys(interventionStatusesByEnrollment)
                                .length === 0
                        "
                        @click="requestClearInterventionTracking"
                    >
                        Limpiar seguimiento
                    </v-btn>
                </div>
            </div>

            <v-data-table
                v-if="visibleAtRiskLearners.length > 0"
                :headers="atRiskHeaders"
                :items="visibleAtRiskLearners"
                density="compact"
                :items-per-page="10"
                class="text-body-2"
            >
                <template #item.follow_up="{ item }">
                    <v-chip
                        v-if="
                            getInterventionStatus(item.enrollment_id) ===
                            'completed'
                        "
                        size="small"
                        color="info"
                        variant="tonal"
                    >
                        Intervención completada
                    </v-chip>
                    <v-chip
                        v-else-if="
                            getInterventionStatus(item.enrollment_id) ===
                            'started'
                        "
                        size="small"
                        color="success"
                        variant="tonal"
                    >
                        Intervención iniciada
                    </v-chip>
                    <span v-else class="text-caption text-medium-emphasis"
                        >Pendiente</span
                    >
                </template>

                <template #item.progress_percentage="{ item }">
                    {{ item.progress_percentage.toFixed(1) }}%
                </template>

                <template #item.assessment_score="{ item }">
                    {{
                        item.assessment_score !== null
                            ? `${item.assessment_score.toFixed(1)}%`
                            : 'N/A'
                    }}
                </template>

                <template #item.resource_completion_pct="{ item }">
                    {{
                        item.resource_completion_pct !== null
                            ? `${item.resource_completion_pct.toFixed(1)}%`
                            : 'N/A'
                    }}
                </template>

                <template #item.status="{ item }">
                    <v-chip size="small" color="warning" variant="tonal">
                        {{ item.status }}
                    </v-chip>
                </template>

                <template #item.actions="{ item }">
                    <div class="d-flex align-center ga-2">
                        <v-btn
                            size="small"
                            variant="text"
                            color="primary"
                            prepend-icon="mdi-account-arrow-right"
                            :href="
                                item.user_id > 0
                                    ? peopleShow(item.user_id).url
                                    : undefined
                            "
                            :disabled="item.user_id <= 0"
                        >
                            Ver perfil
                        </v-btn>

                        <v-btn
                            size="small"
                            variant="text"
                            color="secondary"
                            prepend-icon="mdi-account-plus-outline"
                            @click="openInterventionDialog(item)"
                            :disabled="item.user_id <= 0"
                        >
                            Crear intervención
                        </v-btn>
                    </div>
                </template>
            </v-data-table>

            <v-alert
                v-else
                type="success"
                variant="tonal"
                density="compact"
                class="ma-4"
            >
                Sin learners en riesgo por ahora.
            </v-alert>
        </v-card>

        <v-dialog v-model="interventionDialog" max-width="720">
            <v-card v-if="selectedLearner" class="rounded-lg">
                <v-card-title
                    class="text-subtitle-1 font-weight-bold d-flex align-center ga-2"
                >
                    <v-icon icon="mdi-account-plus-outline" color="secondary" />
                    Nueva intervención sugerida
                </v-card-title>

                <v-card-text class="pt-2">
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-text-field
                                label="Learner"
                                :model-value="selectedLearner.user_name"
                                readonly
                                density="comfortable"
                                variant="outlined"
                            />
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                label="Curso"
                                :model-value="selectedLearner.course_title"
                                readonly
                                density="comfortable"
                                variant="outlined"
                            />
                        </v-col>

                        <v-col cols="12" md="4">
                            <v-text-field
                                label="Progreso"
                                :model-value="`${selectedLearner.progress_percentage.toFixed(1)}%`"
                                readonly
                                density="comfortable"
                                variant="outlined"
                            />
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-text-field
                                label="Assessment"
                                :model-value="
                                    selectedLearner.assessment_score !== null
                                        ? `${selectedLearner.assessment_score.toFixed(1)}%`
                                        : 'N/A'
                                "
                                readonly
                                density="comfortable"
                                variant="outlined"
                            />
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-text-field
                                label="Recursos"
                                :model-value="
                                    selectedLearner.resource_completion_pct !==
                                    null
                                        ? `${selectedLearner.resource_completion_pct.toFixed(1)}%`
                                        : 'N/A'
                                "
                                readonly
                                density="comfortable"
                                variant="outlined"
                            />
                        </v-col>
                    </v-row>

                    <v-alert type="info" variant="tonal" density="compact">
                        Se abrirá Mentoring con contexto prellenado para
                        registrar la intervención sobre este learner.
                    </v-alert>
                </v-card-text>

                <v-card-actions class="px-6 pb-4">
                    <v-spacer />
                    <v-btn variant="text" @click="closeInterventionDialog">
                        Cancelar
                    </v-btn>
                    <v-btn
                        color="secondary"
                        variant="flat"
                        prepend-icon="mdi-open-in-new"
                        @click="goToMentoringFromDialog"
                        :disabled="!interventionHref()"
                    >
                        Ir a Mentoring
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="clearTrackingDialog" max-width="480">
            <v-card class="rounded-lg">
                <v-card-title
                    class="text-subtitle-1 font-weight-bold d-flex align-center ga-2"
                >
                    <v-icon icon="mdi-alert-circle-outline" color="warning" />
                    Confirmar limpieza
                </v-card-title>

                <v-card-text>
                    ¿Deseas borrar todas las marcas de seguimiento de
                    intervenciones iniciadas?
                </v-card-text>

                <v-card-actions class="px-6 pb-4">
                    <v-spacer />
                    <v-btn
                        variant="text"
                        @click="cancelClearInterventionTracking"
                    >
                        Cancelar
                    </v-btn>
                    <v-btn
                        color="warning"
                        variant="flat"
                        prepend-icon="mdi-eraser"
                        @click="confirmClearInterventionTracking"
                    >
                        Limpiar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-snackbar
            v-model="feedbackSnackbar"
            color="success"
            location="bottom right"
            :timeout="2500"
        >
            {{ feedbackMessage }}
        </v-snackbar>

        <v-alert
            v-else-if="
                !isLoading && analytics && analytics.per_course.length === 0
            "
            type="info"
            variant="tonal"
            density="compact"
        >
            No hay datos de cursos para esta organización todavía.
        </v-alert>
    </div>
</template>
