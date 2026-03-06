<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { useNotification } from '@kyvg/vue3-notification';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { useTheme as useVuetifyTheme } from 'vuetify';

defineOptions({ layout: AppLayout });

const { notify } = useNotification();
const page = usePage();
const vuetifyTheme = useVuetifyTheme();

const headerGradient = computed(() => {
    const theme = vuetifyTheme.global.current.value;
    return `linear-gradient(135deg, ${theme.colors.primary} 0%, ${theme.colors.secondary} 100%)`;
});

interface JobOpening {
    id: number;
    title: string;
    description: string;
    required_skills: Skill[];
    match_percentage?: number;
    time_to_productivity?: number;
}

interface Skill {
    id: number;
    name: string;
    required_level?: number;
    current_level?: number;
}

interface Application {
    id: number;
    job_opening_id: number;
    status: string;
    created_at: string;
}

interface Candidate {
    id: number;
    name: string;
    current_role: string;
    match_percentage: number;
    time_to_productivity: number;
    category: string;
    missing_skills_count: number;
}

interface Position {
    id: number;
    title: string;
    role: string;
    department: string;
    deadline: string;
    status: string;
    candidates: Candidate[];
    total_candidates: number;
}

// State
const activeTab = ref<string>('recruiter'); // 'recruiter' o 'employee'
const opportunities = ref<JobOpening[]>([]);
const positions = ref<Position[]>([]);
const applications = ref<Application[]>([]);
const loading = ref(false);
const loadingRecruiter = ref(false);
const applying = ref<number | null>(null);
const filterStatus = ref<string>('open'); // mark unused bindings during refactor
const showAllCandidates = ref(false); // Mostrar todos los candidatos o solo Top 5
const externalSearchThreshold = ref(70); // Umbral para recomendar búsqueda externa
const candidateMatchFilter = ref<'all' | 'high' | 'medium' | 'low'>('all'); // Filtro por nivel de match

// AI Insights State
const aiInsightsDialog = ref(false);
const evaluatingCandidateId = ref<string | null>(null);
const aiInsightData = ref<any>(null);
const selectedCandidateName = ref('');
const selectedPositionTitle = ref('');

// Current user (get from inertia props)
const currentUserId = computed(() => {
    return (page.props as any).auth?.user?.id;
});

// Load recruiter view (positions with candidates)
const loadRecruiterView = async () => {
    loadingRecruiter.value = true;
    try {
        const response = await axios.get('/api/marketplace/recruiter');
        const data = response.data.data || response.data;
        positions.value = data.positions || [];
    } catch (err) {
        console.error('Failed to load recruiter view', err);
        notify({
            type: 'error',
            text: 'Error cargando vista de reclutador',
        });
    } finally {
        loadingRecruiter.value = false;
    }
};

// Load opportunities for current people
const loadOpportunities = async () => {
    if (!currentUserId.value) {
        notify({
            type: 'warning',
            text: 'Please log in to view opportunities',
        });
        return;
    }

    loading.value = true;
    try {
        const response = await axios.get(
            `/api/people/${currentUserId.value}/marketplace`,
        );
        const data = response.data.data || response.data;
        opportunities.value = data.opportunities || [];
    } catch (err) {
        console.error('Failed to load opportunities', err);
        notify({
            type: 'error',
            text: 'Error loading opportunities',
        });
    } finally {
        loading.value = false;
    }
};

// Load user's applications
const loadApplications = async () => {
    if (!currentUserId.value) return;

    try {
        const response = await axios.get('/api/applications');
        applications.value = response.data.data || response.data;
    } catch (err) {
        console.error('Failed to load applications', err);
    }
};

// Apply for position
const applyForPosition = async (jobOpeningId: number) => {
    if (!currentUserId.value) {
        notify({
            type: 'error',
            text: 'You must be logged in to apply',
        });
        return;
    }

    applying.value = jobOpeningId;
    try {
        await axios.post('/api/applications', {
            job_opening_id: jobOpeningId,
            people_id: currentUserId.value,
        });
        notify({
            type: 'success',
            text: 'Application submitted successfully',
        });
        await loadApplications();
    } catch (err: any) {
        console.error('Failed to apply', err);
        notify({
            type: 'error',
            text: err.response?.data?.message || 'Error submitting application',
        });
    } finally {
        applying.value = null;
    }
};

// Check if already applied
const hasApplied = (jobOpeningId: number): boolean => {
    return applications.value.some(
        (app) => app.job_opening_id === jobOpeningId,
    );
};

// Get match color
const getMatchColor = (percentage: number | undefined): string => {
    if (!percentage) return 'grey';
    if (percentage >= 80) return 'success';
    if (percentage >= 70) return 'success';
    if (percentage >= 50) return 'warning';
    if (percentage >= 30) return 'orange';
    return 'error';
};

// Get match category label
const getMatchCategory = (percentage: number): string => {
    if (percentage >= 80) return 'Excelente Match';
    if (percentage >= 70) return 'Buen Match';
    if (percentage >= 50) return 'Match Moderado';
    if (percentage >= 30) return 'Match Bajo';
    return 'Match Muy Bajo';
};

// Get match category icon
const getMatchIcon = (percentage: number): string => {
    if (percentage >= 70) return 'mdi-star-circle';
    if (percentage >= 50) return 'mdi-clock-alert-outline';
    if (percentage >= 30) return 'mdi-alert-circle-outline';
    return 'mdi-close-circle-outline';
};

// Filter candidates by match level
const filterCandidatesByMatch = (candidates: Candidate[]) => {
    if (candidateMatchFilter.value === 'all') return candidates;

    return candidates.filter((c) => {
        if (candidateMatchFilter.value === 'high')
            return c.match_percentage >= 70;
        if (candidateMatchFilter.value === 'medium')
            return c.match_percentage >= 50 && c.match_percentage < 70;
        if (candidateMatchFilter.value === 'low')
            return c.match_percentage < 50;
        return true;
    });
};

// Get status color
const getStatusColor = (status: string): string => {
    const statusMap: Record<string, string> = {
        open: 'success',
        closed: 'error',
        filled: 'warning',
    };
    return statusMap[status] || 'grey';
};

// Calculate summary metrics for recruiter view
const recruiterSummary = computed(() => {
    if (!positions.value || positions.value.length === 0) {
        return {
            totalPositions: 0,
            candidatesExcellentMatch: 0,
            candidatesGoodMatch: 0,
            candidatesModerateMatch: 0,
            candidatesNeedingExternalSearch: 0,
            avgMatchPercentage: 0,
            positionsWithoutViableCandidates: 0,
        };
    }

    let excellentCount = 0; // >= 80%
    let goodCount = 0; // 70-79%
    let moderateCount = 0; // 50-69%
    let lowCount = 0; // 40-49%
    let totalMatch = 0;
    let totalCandidates = 0;
    let positionsWithoutCandidates = 0;

    positions.value.forEach((position) => {
        // Si no hay candidatos viables, contar la posición
        if (position.candidates.length === 0) {
            positionsWithoutCandidates++;
        }

        // Contar TODOS los candidatos por rango
        position.candidates.forEach((candidate) => {
            const matchPct = candidate.match_percentage;

            if (matchPct >= 80) {
                excellentCount++;
            } else if (matchPct >= 70) {
                goodCount++;
            } else if (matchPct >= 50) {
                moderateCount++;
            } else if (matchPct >= 40) {
                lowCount++;
            }

            totalMatch += matchPct;
            totalCandidates++;
        });
    });

    return {
        totalPositions: positions.value.length,
        positionsWithExcellentMatch: excellentCount, // Mapping for usage below
        positionsWithGoodMatch: goodCount, // Mapping for usage below
        positionsWithModerateMatch: moderateCount, // Mapping for usage below
        positionsNeedingExternalSearch: positionsWithoutCandidates, // Mapping for usage below
        positionsRequiringImmediateExternal: positionsWithoutCandidates, // Mapping for usage below
        candidatesExcellentMatch: excellentCount,
        candidatesGoodMatch: goodCount,
        candidatesModerateMatch: moderateCount,
        candidatesLowMatch: lowCount,
        candidatesNeedingExternalSearch: lowCount,
        avgMatchPercentage:
            totalCandidates > 0 ? Math.round(totalMatch / totalCandidates) : 0,
        positionsWithoutViableCandidates: positionsWithoutCandidates,
    };
});

// Load AI Insights
const openAiInsightDialog = async (position: any, candidate: any) => {
    selectedCandidateName.value = candidate.name;
    selectedPositionTitle.value = position.title;
    evaluatingCandidateId.value = `${position.id}-${candidate.id}`;
    aiInsightData.value = null;

    try {
        const response = await axios.post(
            `/api/marketplace/positions/${position.id}/candidates/${candidate.id}/ai-insights`,
        );
        aiInsightData.value = response.data.data;
        aiInsightsDialog.value = true;
    } catch (error) {
        console.error('Error fetching AI insights:', error);
        notify({
            type: 'error',
            text: 'Error obteniendo insights de IA',
        });
    } finally {
        evaluatingCandidateId.value = null;
    }
};

onMounted(() => {
    loadRecruiterView(); // Vista por defecto para admin
    // loadOpportunities(); // Se cargará cuando se cambie al tab de empleado
    // loadApplications();
});
// mark some refs/functions referenced to avoid unused-var during refactor
void filterStatus.value;
void externalSearchThreshold.value;
void loadOpportunities;
</script>

<template>
    <div class="pa-4">
        <!-- Header -->
        <div
            class="d-flex justify-space-between align-center mb-4"
            :style="{ background: headerGradient }"
            style="padding: 1.5rem; border-radius: 8px"
        >
            <div>
                <h1 class="text-h4 font-weight-bold mb-2" style="color: white">
                    Marketplace de Oportunidades
                </h1>
                <p
                    class="text-subtitle-2"
                    style="color: rgba(255, 255, 255, 0.85)"
                >
                    Explora oportunidades internas y postula a roles que se
                    ajusten a tu perfil
                </p>
            </div>
        </div>

        <!-- Description Section -->
        <v-card class="mb-6" elevation="0" variant="outlined">
            <v-card-text class="pa-6">
                <div class="d-flex align-start gap-4">
                    <v-icon size="48" color="primary" class="mt-1"
                        >mdi-briefcase-search</v-icon
                    >
                    <div class="flex-grow-1">
                        <h2 class="text-h6 font-weight-bold mb-3">
                            ¿Qué es el Marketplace Interno?
                        </h2>
                        <p class="text-body-2 mb-3">
                            El
                            <strong>Marketplace de Oportunidades</strong>
                            facilita la movilidad interna conectando posiciones
                            abiertas con el talento disponible en tu
                            organización.
                        </p>
                        <v-alert
                            type="info"
                            variant="tonal"
                            class="mt-4"
                            density="compact"
                        >
                            <template #prepend>
                                <v-icon>mdi-information</v-icon>
                            </template>
                            <div>
                                <strong>Vista actual:</strong> Como
                                administrador, puedes ver qué candidatos tienen
                                mejor match para cada posición abierta.<br />
                                <strong>Umbral de viabilidad:</strong> Solo se
                                muestran candidatos con ≥40% de match.
                                Candidatos con match muy bajo (&lt;40%) no son
                                considerados viables.
                            </div>
                        </v-alert>
                    </div>
                </div>
            </v-card-text>
        </v-card>

        <!-- Tabs -->
        <v-tabs v-model="activeTab" class="mb-4" color="primary">
            <v-tab value="recruiter">
                <v-icon start>mdi-account-search</v-icon>
                Buscar Talento
            </v-tab>
            <v-tab value="employee" disabled>
                <v-icon start>mdi-briefcase-search-outline</v-icon>
                Mis Oportunidades
                <v-chip size="x-small" class="ml-2" color="grey" variant="flat"
                    >Próximamente</v-chip
                >
            </v-tab>
        </v-tabs>

        <!-- Vista de Reclutador: Posiciones con Candidatos -->
        <div v-if="activeTab === 'recruiter'">
            <!-- Summary Dashboard -->
            <v-row
                v-if="!loadingRecruiter && positions.length > 0"
                class="mb-6"
            >
                <v-col cols="12">
                    <h3 class="text-h6 font-weight-bold mb-4">
                        Resumen de Búsqueda de Talento
                    </h3>
                </v-col>

                <!-- Total Positions -->
                <v-col cols="12" md="3">
                    <v-card elevation="0" variant="outlined">
                        <v-card-text>
                            <div
                                class="d-flex align-center justify-space-between"
                            >
                                <div>
                                    <div
                                        class="text-caption text-medium-emphasis"
                                    >
                                        Posiciones Abiertas
                                    </div>
                                    <div class="text-h4 font-weight-bold mt-1">
                                        {{ recruiterSummary.totalPositions }}
                                    </div>
                                </div>
                                <v-avatar color="primary" size="48">
                                    <v-icon size="24"
                                        >mdi-briefcase-outline</v-icon
                                    >
                                </v-avatar>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Excellent Match (≥80%) -->
                <v-col cols="12" md="3">
                    <v-card elevation="0" variant="outlined">
                        <v-card-text>
                            <div
                                class="d-flex align-center justify-space-between"
                            >
                                <div>
                                    <div
                                        class="text-caption text-medium-emphasis"
                                    >
                                        Match Excelente
                                    </div>
                                    <div
                                        class="text-h4 font-weight-bold text-success mt-1"
                                    >
                                        {{
                                            recruiterSummary.positionsWithExcellentMatch
                                        }}
                                    </div>
                                    <div class="text-caption text-success">
                                        ≥80% · Listos
                                    </div>
                                </div>
                                <v-avatar color="success" size="48">
                                    <v-icon size="24">mdi-star-circle</v-icon>
                                </v-avatar>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Good Match (70-79%) -->
                <v-col cols="12" md="3">
                    <v-card elevation="0" variant="outlined">
                        <v-card-text>
                            <div
                                class="d-flex align-center justify-space-between"
                            >
                                <div>
                                    <div
                                        class="text-caption text-medium-emphasis"
                                    >
                                        Buen Match
                                    </div>
                                    <div
                                        class="text-h4 font-weight-bold text-success mt-1"
                                        style="opacity: 0.8"
                                    >
                                        {{
                                            recruiterSummary.positionsWithGoodMatch
                                        }}
                                    </div>
                                    <div class="text-caption text-success">
                                        70-79% · Viables
                                    </div>
                                </div>
                                <v-avatar
                                    color="success"
                                    size="48"
                                    style="opacity: 0.8"
                                >
                                    <v-icon size="24">mdi-account-check</v-icon>
                                </v-avatar>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Moderate Match (50-69%) -->
                <v-col cols="12" md="3">
                    <v-card elevation="0" variant="outlined">
                        <v-card-text>
                            <div
                                class="d-flex align-center justify-space-between"
                            >
                                <div>
                                    <div
                                        class="text-caption text-medium-emphasis"
                                    >
                                        Match Moderado
                                    </div>
                                    <div
                                        class="text-h4 font-weight-bold text-warning mt-1"
                                    >
                                        {{
                                            recruiterSummary.positionsWithModerateMatch
                                        }}
                                    </div>
                                    <div class="text-caption text-warning">
                                        50-69% · Capacitación
                                    </div>
                                </div>
                                <v-avatar color="warning" size="48">
                                    <v-icon size="24"
                                        >mdi-clock-alert-outline</v-icon
                                    >
                                </v-avatar>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Need External Search -->
                <v-col cols="12" md="3">
                    <v-card elevation="0" variant="outlined">
                        <v-card-text>
                            <div
                                class="d-flex align-center justify-space-between"
                            >
                                <div>
                                    <div
                                        class="text-caption text-medium-emphasis"
                                    >
                                        Búsqueda Externa
                                    </div>
                                    <div
                                        class="text-h4 font-weight-bold text-error mt-1"
                                    >
                                        {{
                                            recruiterSummary.positionsNeedingExternalSearch
                                        }}
                                    </div>
                                    <div class="text-caption text-error">
                                        &lt;50% · Buscar Mercado
                                    </div>
                                </div>
                                <v-avatar color="error" size="48">
                                    <v-icon size="24"
                                        >mdi-account-search-outline</v-icon
                                    >
                                </v-avatar>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Strategic Recommendations -->
                <v-col cols="12">
                    <!-- Immediate External Search Alert -->
                    <v-alert
                        v-if="
                            recruiterSummary.positionsRequiringImmediateExternal >
                            0
                        "
                        type="error"
                        variant="tonal"
                        density="comfortable"
                        prominent
                        class="mb-3"
                    >
                        <template #prepend>
                            <v-icon>mdi-alert-circle</v-icon>
                        </template>
                        <div class="font-weight-bold mb-1">
                            🚨 Acción Inmediata Requerida
                        </div>
                        <div class="text-body-2">
                            <strong>{{
                                recruiterSummary.positionsRequiringImmediateExternal
                            }}</strong>
                            posición(es) con match &lt;30%.
                            <strong
                                >Iniciar búsqueda externa de inmediato</strong
                            >
                            en paralelo a desarrollo interno.
                        </div>
                    </v-alert>

                    <!-- Moderate Match - Parallel Search Recommendation -->
                    <v-alert
                        v-if="recruiterSummary.positionsWithModerateMatch > 0"
                        type="warning"
                        variant="tonal"
                        density="comfortable"
                        class="mb-3"
                    >
                        <template #prepend>
                            <v-icon>mdi-information</v-icon>
                        </template>
                        <div class="font-weight-bold mb-1">
                            💡 Estrategia Dual Recomendada
                        </div>
                        <div class="text-body-2">
                            <strong>{{
                                recruiterSummary.positionsWithModerateMatch
                            }}</strong>
                            posición(es) con match 50-69%.
                            <strong>Sugerencia:</strong> Iniciar búsqueda
                            externa preventiva mientras se desarrolla talento
                            interno.
                        </div>
                    </v-alert>

                    <!-- Good Internal Talent Available -->
                    <v-alert
                        v-if="
                            recruiterSummary.positionsWithExcellentMatch +
                                recruiterSummary.positionsWithGoodMatch >
                            0
                        "
                        type="success"
                        variant="tonal"
                        density="comfortable"
                    >
                        <template #prepend>
                            <v-icon>mdi-check-circle</v-icon>
                        </template>
                        <div class="font-weight-bold mb-1">
                            ✅ Talento Interno Disponible
                        </div>
                        <div class="text-body-2">
                            <strong>{{
                                recruiterSummary.positionsWithExcellentMatch +
                                recruiterSummary.positionsWithGoodMatch
                            }}</strong>
                            posición(es) con candidatos internos listos (≥70%
                            match).
                            <strong>Priorizar proceso interno</strong> antes de
                            búsqueda externa.
                        </div>
                    </v-alert>
                </v-col>

                <!-- Filter Controls -->
                <v-col cols="12">
                    <v-card elevation="0" variant="outlined">
                        <v-card-text class="pa-4">
                            <div class="d-flex align-center flex-wrap gap-4">
                                <div class="flex-grow-0">
                                    <v-icon class="mr-2"
                                        >mdi-filter-variant</v-icon
                                    >
                                    <span class="font-weight-medium"
                                        >Filtros:</span
                                    >
                                </div>

                                <v-chip-group
                                    v-model="candidateMatchFilter"
                                    selected-class="text-primary"
                                    mandatory
                                >
                                    <v-chip
                                        value="all"
                                        variant="outlined"
                                        filter
                                    >
                                        <v-icon start size="small"
                                            >mdi-view-list</v-icon
                                        >
                                        Todos
                                    </v-chip>
                                    <v-chip
                                        value="high"
                                        variant="outlined"
                                        filter
                                        color="success"
                                    >
                                        <v-icon start size="small"
                                            >mdi-star</v-icon
                                        >
                                        Match Alto (≥70%)
                                    </v-chip>
                                    <v-chip
                                        value="medium"
                                        variant="outlined"
                                        filter
                                        color="warning"
                                    >
                                        <v-icon start size="small"
                                            >mdi-clock-outline</v-icon
                                        >
                                        Match Medio (50-69%)
                                    </v-chip>
                                    <v-chip
                                        value="low"
                                        variant="outlined"
                                        filter
                                        color="error"
                                    >
                                        <v-icon start size="small"
                                            >mdi-alert</v-icon
                                        >
                                        Match Bajo (&lt;50%)
                                    </v-chip>
                                </v-chip-group>

                                <v-spacer />

                                <v-switch
                                    v-model="showAllCandidates"
                                    color="primary"
                                    density="compact"
                                    hide-details
                                    label="Mostrar todos los candidatos"
                                />
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Loading State -->
            <v-card
                v-if="loadingRecruiter"
                class="mb-6"
                elevation="0"
                variant="outlined"
            >
                <v-card-text class="py-12 text-center">
                    <v-progress-circular
                        indeterminate
                        color="primary"
                        size="48"
                    />
                    <p class="text-medium-emphasis mt-4">
                        Analizando candidatos...
                    </p>
                </v-card-text>
            </v-card>

            <!-- Empty State -->
            <v-card
                v-else-if="positions.length === 0"
                class="mb-6"
                elevation="0"
                variant="outlined"
            >
                <v-card-text class="py-12 text-center">
                    <v-icon size="80" class="text-medium-emphasis mb-6"
                        >mdi-briefcase-search</v-icon
                    >
                    <div class="text-h6 mb-2">No hay posiciones abiertas</div>
                    <div class="text-body-2 text-medium-emphasis">
                        Crea nuevas posiciones para comenzar a buscar talento
                        interno
                    </div>
                </v-card-text>
            </v-card>

            <!-- Positions List -->
            <div v-else>
                <v-card
                    v-for="position in positions"
                    :key="position.id"
                    class="mb-4"
                    elevation="0"
                    variant="outlined"
                >
                    <v-card-title class="pa-6">
                        <div
                            class="d-flex align-center justify-space-between w-100"
                        >
                            <div class="grow">
                                <div class="text-h6 font-weight-bold">
                                    {{ position.title }}
                                </div>
                                <div
                                    class="text-body-2 text-medium-emphasis mt-1"
                                >
                                    {{ position.role }} ·
                                    {{ position.department }}
                                </div>
                            </div>
                            <v-chip
                                color="success"
                                variant="flat"
                                prepend-icon="mdi-account-group"
                            >
                                {{ position.total_candidates }} candidatos
                            </v-chip>
                        </div>
                    </v-card-title>

                    <v-divider />

                    <v-card-text class="pa-6">
                        <h3 class="text-subtitle-1 font-weight-bold mb-4">
                            Top Candidatos
                        </h3>

                        <v-card
                            v-if="position.candidates.length === 0"
                            variant="tonal"
                            color="error"
                        >
                            <v-card-text class="py-6 text-center">
                                <v-icon size="48" class="mb-2"
                                    >mdi-account-off-outline</v-icon
                                >
                                <div class="text-body-1 font-weight-bold mb-2">
                                    Sin candidatos viables
                                </div>
                                <div class="text-body-2">
                                    No hay candidatos internos con ≥40% de match
                                    para este rol.
                                </div>
                                <div
                                    class="text-body-2 text-medium-emphasis mt-2"
                                >
                                    <strong>Acción requerida:</strong> Iniciar
                                    búsqueda externa inmediata
                                </div>
                            </v-card-text>
                        </v-card>

                        <div v-else>
                            <!-- Indicador de mejor candidato -->
                            <v-alert
                                v-if="
                                    position.candidates.length > 0 &&
                                    position.candidates[0].match_percentage < 30
                                "
                                type="error"
                                variant="tonal"
                                density="compact"
                                class="mb-3"
                            >
                                <template #prepend>
                                    <v-icon>mdi-alert</v-icon>
                                </template>
                                <strong>Búsqueda Externa Recomendada:</strong>
                                El mejor candidato interno tiene solo
                                {{ position.candidates[0].match_percentage }}%
                                de match.
                            </v-alert>
                            <v-alert
                                v-else-if="
                                    position.candidates.length > 0 &&
                                    position.candidates[0].match_percentage >=
                                        70
                                "
                                type="success"
                                variant="tonal"
                                density="compact"
                                class="mb-3"
                            >
                                <template #prepend>
                                    <v-icon>mdi-check-circle</v-icon>
                                </template>
                                <strong>Talento Interno Disponible:</strong>
                                Candidato(s) con ≥70% de match. Priorizar
                                proceso interno.
                            </v-alert>
                            <v-alert
                                v-else-if="
                                    position.candidates.length > 0 &&
                                    position.candidates[0].match_percentage >=
                                        50
                                "
                                type="warning"
                                variant="tonal"
                                density="compact"
                                class="mb-3"
                            >
                                <template #prepend>
                                    <v-icon>mdi-information</v-icon>
                                </template>
                                <strong>Estrategia Dual:</strong> Candidatos
                                internos viables con capacitación. Considerar
                                búsqueda externa en paralelo.
                            </v-alert>

                            <v-list class="pa-0">
                                <template
                                    v-for="(
                                        candidate, index
                                    ) in filterCandidatesByMatch(
                                        showAllCandidates
                                            ? position.candidates
                                            : position.candidates.slice(0, 5),
                                    )"
                                    :key="candidate.id"
                                >
                                    <v-list-item
                                        class="px-4 py-3"
                                        :class="{
                                            'border-b':
                                                index <
                                                (showAllCandidates
                                                    ? position.candidates
                                                          .length - 1
                                                    : Math.min(
                                                          4,
                                                          position.candidates
                                                              .length - 1,
                                                      )),
                                        }"
                                    >
                                        <template #prepend>
                                            <v-avatar
                                                :color="
                                                    getMatchColor(
                                                        candidate.match_percentage,
                                                    )
                                                "
                                                size="44"
                                                class="mr-3"
                                            >
                                                <v-icon
                                                    :color="
                                                        candidate.match_percentage >=
                                                        70
                                                            ? 'white'
                                                            : undefined
                                                    "
                                                >
                                                    {{
                                                        getMatchIcon(
                                                            candidate.match_percentage,
                                                        )
                                                    }}
                                                </v-icon>
                                            </v-avatar>
                                        </template>

                                        <v-list-item-title
                                            class="font-weight-medium"
                                        >
                                            {{ candidate.name }}
                                            <v-chip
                                                v-if="index === 0"
                                                size="x-small"
                                                class="ml-2"
                                                color="primary"
                                                variant="flat"
                                            >
                                                Top Match
                                            </v-chip>
                                        </v-list-item-title>
                                        <v-list-item-subtitle
                                            class="text-caption"
                                        >
                                            {{ candidate.current_role }}
                                        </v-list-item-subtitle>

                                        <template #append>
                                            <div
                                                class="d-flex align-center gap-3"
                                            >
                                                <div class="text-center">
                                                    <div
                                                        :class="`text-h6 font-weight-bold text-${getMatchColor(candidate.match_percentage)}`"
                                                    >
                                                        {{
                                                            candidate.match_percentage
                                                        }}%
                                                    </div>
                                                    <div
                                                        class="text-caption text-medium-emphasis"
                                                    >
                                                        {{
                                                            getMatchCategory(
                                                                candidate.match_percentage,
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                                <v-divider vertical />
                                                <div
                                                    class="text-center"
                                                    style="min-width: 60px"
                                                >
                                                    <div
                                                        class="text-subtitle-2 font-weight-medium"
                                                    >
                                                        {{
                                                            candidate.time_to_productivity
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-caption text-medium-emphasis"
                                                    >
                                                        días TTP
                                                    </div>
                                                </div>
                                                <v-divider vertical />
                                                <div
                                                    class="text-center"
                                                    style="min-width: 60px"
                                                >
                                                    <div
                                                        :class="`text-subtitle-2 font-weight-medium ${candidate.missing_skills_count > 0 ? 'text-warning' : 'text-success'}`"
                                                    >
                                                        {{
                                                            candidate.missing_skills_count
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-caption text-medium-emphasis"
                                                    >
                                                        gaps
                                                    </div>
                                                </div>
                                                <v-divider
                                                    vertical
                                                    class="mx-2"
                                                />
                                                <v-btn
                                                    color="primary"
                                                    variant="tonal"
                                                    size="small"
                                                    prepend-icon="mdi-brain"
                                                    :loading="
                                                        evaluatingCandidateId ===
                                                        `${position.id}-${candidate.id}`
                                                    "
                                                    @click="
                                                        openAiInsightDialog(
                                                            position,
                                                            candidate,
                                                        )
                                                    "
                                                >
                                                    Match AI
                                                </v-btn>
                                            </div>
                                        </template>
                                    </v-list-item>
                                </template>
                            </v-list>

                            <v-btn
                                v-if="
                                    !showAllCandidates &&
                                    position.candidates.length > 5
                                "
                                variant="text"
                                color="primary"
                                class="mt-4"
                                block
                                @click="showAllCandidates = true"
                            >
                                Ver todos los
                                {{ position.candidates.length }} candidatos
                                <v-icon end>mdi-chevron-down</v-icon>
                            </v-btn>

                            <div
                                v-if="
                                    filterCandidatesByMatch(position.candidates)
                                        .length === 0
                                "
                                class="text-medium-emphasis py-4 text-center"
                            >
                                <v-icon size="32" class="mb-2"
                                    >mdi-filter-off</v-icon
                                >
                                <div class="text-caption">
                                    No hay candidatos en este rango de match
                                </div>
                            </div>
                        </div>
                    </v-card-text>
                </v-card>
            </div>
        </div>

        <!-- Vista de Empleado: Mis Oportunidades (Vista actual - para futuro) -->
        <div v-if="activeTab === 'employee'">
            <!-- Loading State -->
            <v-card v-if="loading" class="mb-6">
                <v-card-text class="py-8 text-center">
                    <v-progress-circular indeterminate color="primary" />
                    <p class="mt-4">Loading opportunities...</p>
                </v-card-text>
            </v-card>

            <!-- Opportunities Grid -->
            <div v-if="!loading">
                <v-card v-if="opportunities.length === 0" class="mb-6">
                    <v-card-text class="py-12 text-center">
                        <v-icon size="64" class="text-grey mb-4"
                            >mdi-briefcase-search</v-icon
                        >
                        <p class="text-body1 text-grey">
                            No opportunities available at the moment
                        </p>
                    </v-card-text>
                </v-card>

                <v-row v-else no-gutters class="gap-4">
                    <v-col
                        v-for="opportunity in opportunities"
                        :key="opportunity.id"
                        cols="12"
                        sm="6"
                        md="4"
                    >
                        <v-card
                            class="d-flex flex-column h-100"
                            elevation="0"
                            border
                        >
                            <!-- Header -->
                            <v-card-title class="pb-2">
                                <div
                                    class="d-flex justify-space-between align-center w-100"
                                >
                                    <div class="grow">
                                        <h3
                                            class="text-h6 font-weight-bold mb-0"
                                        >
                                            {{ opportunity.title }}
                                        </h3>
                                    </div>
                                    <v-chip
                                        :color="getStatusColor('open')"
                                        text-color="white"
                                        size="small"
                                        label
                                    >
                                        Open
                                    </v-chip>
                                </div>
                            </v-card-title>

                            <!-- Description -->
                            <v-card-text class="pb-2">
                                <p class="text-body2 text-grey mb-4">
                                    {{ opportunity.description }}
                                </p>

                                <!-- Match Indicator -->
                                <div
                                    v-if="
                                        opportunity.match_percentage !==
                                        undefined
                                    "
                                    class="mb-4"
                                >
                                    <div
                                        class="d-flex justify-space-between mb-2"
                                    >
                                        <span
                                            class="text-caption font-weight-medium"
                                            >Your Match</span
                                        >
                                        <span
                                            :class="`text-caption text-${getMatchColor(opportunity.match_percentage)} font-weight-bold`"
                                        >
                                            {{ opportunity.match_percentage }}%
                                        </span>
                                    </div>
                                    <v-progress-linear
                                        :value="opportunity.match_percentage"
                                        :color="
                                            getMatchColor(
                                                opportunity.match_percentage,
                                            )
                                        "
                                        height="6"
                                    />
                                </div>

                                <!-- Time to Productivity -->
                                <div
                                    v-if="
                                        opportunity.time_to_productivity !==
                                        undefined
                                    "
                                    class="mb-4"
                                >
                                    <p class="text-caption text-grey">
                                        <v-icon size="16" class="mr-1"
                                            >mdi-clock-outline</v-icon
                                        >
                                        ~{{ opportunity.time_to_productivity }}
                                        days to full productivity
                                    </p>
                                </div>

                                <!-- Required Skills -->
                                <div
                                    v-if="
                                        opportunity.required_skills?.length > 0
                                    "
                                >
                                    <p
                                        class="text-caption font-weight-medium mb-2"
                                    >
                                        Required Skills:
                                    </p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <v-chip
                                            v-for="skill in opportunity.required_skills.slice(
                                                0,
                                                3,
                                            )"
                                            :key="skill.id"
                                            size="small"
                                            variant="outlined"
                                        >
                                            {{ skill.name }}
                                        </v-chip>
                                        <v-chip
                                            v-if="
                                                opportunity.required_skills
                                                    .length > 3
                                            "
                                            size="small"
                                            variant="outlined"
                                            disabled
                                        >
                                            +{{
                                                opportunity.required_skills
                                                    .length - 3
                                            }}
                                            more
                                        </v-chip>
                                    </div>
                                </div>
                            </v-card-text>

                            <!-- Actions -->
                            <v-card-actions class="mt-auto">
                                <v-spacer />
                                <v-btn
                                    v-if="!hasApplied(opportunity.id)"
                                    color="primary"
                                    @click="applyForPosition(opportunity.id)"
                                    :loading="applying === opportunity.id"
                                >
                                    Apply Now
                                </v-btn>
                                <v-chip
                                    v-else
                                    color="success"
                                    text-color="white"
                                    label
                                >
                                    Applied
                                </v-chip>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>
            </div>
        </div>
        <!-- Cierre de activeTab === 'employee' -->

        <!-- AI Insights Dialog -->
        <v-dialog v-model="aiInsightsDialog" max-width="700">
            <v-card class="overflow-hidden rounded-xl" elevation="24">
                <div
                    class="d-flex align-center justify-space-between px-6 py-4"
                    style="
                        background: linear-gradient(
                            135deg,
                            #4f46e5 0%,
                            #7c3aed 100%
                        );
                        color: white;
                    "
                >
                    <div class="d-flex align-center gap-3">
                        <v-icon size="32" color="white">mdi-brain</v-icon>
                        <div>
                            <h3 class="text-h6 font-weight-bold mb-0">
                                AI Match Insights
                            </h3>
                            <div class="text-caption" style="opacity: 0.9">
                                {{ selectedCandidateName }} →
                                {{ selectedPositionTitle }}
                            </div>
                        </div>
                    </div>
                    <v-btn
                        icon="mdi-close"
                        variant="text"
                        color="white"
                        @click="aiInsightsDialog = false"
                    ></v-btn>
                </div>

                <v-card-text
                    class="pa-6"
                    style="background: rgb(var(--v-theme-surface))"
                >
                    <div v-if="aiInsightData">
                        <div class="d-flex mb-8 justify-center">
                            <div class="text-center">
                                <v-progress-circular
                                    :model-value="
                                        aiInsightData.fitness_score ||
                                        aiInsightData.hidden_potential_score
                                    "
                                    :color="
                                        getMatchColor(
                                            aiInsightData.fitness_score ||
                                                aiInsightData.hidden_potential_score,
                                        )
                                    "
                                    size="140"
                                    width="14"
                                >
                                    <div class="text-center">
                                        <div class="text-h4 font-weight-black">
                                            {{
                                                aiInsightData.fitness_score ||
                                                aiInsightData.hidden_potential_score
                                            }}%
                                        </div>
                                        <div
                                            class="text-[10px] font-black tracking-widest uppercase opacity-50"
                                        >
                                            Match DNA
                                        </div>
                                    </div>
                                </v-progress-circular>
                                <div class="mt-4">
                                    <v-chip
                                        size="small"
                                        :color="
                                            getMatchColor(
                                                aiInsightData.fitness_score ||
                                                    aiInsightData.hidden_potential_score,
                                            )
                                        "
                                        variant="flat"
                                        class="font-black"
                                    >
                                        POTENCIAL:
                                        {{
                                            aiInsightData.hidden_potential_label ||
                                            'Calculando...'
                                        }}
                                    </v-chip>
                                </div>
                            </div>
                        </div>

                        <!-- Strengths & Risks -->
                        <v-row class="mb-6">
                            <v-col cols="12" md="6">
                                <div class="d-flex align-center mb-3">
                                    <v-icon color="success" class="mr-2"
                                        >mdi-check-decagram</v-icon
                                    >
                                    <span
                                        class="text-success text-caption font-bold tracking-widest uppercase"
                                        >Fortalezas de Resonancia</span
                                    >
                                </div>
                                <div
                                    class="bg-success-lighten-5 pa-3 border-success-lighten-4 h-100 rounded-lg border"
                                >
                                    <ul class="text-body-2 pl-4">
                                        <li
                                            v-for="strength in aiInsightData.strengths"
                                            :key="strength"
                                            class="mb-2"
                                        >
                                            {{ strength }}
                                        </li>
                                    </ul>
                                </div>
                            </v-col>
                            <v-col cols="12" md="6">
                                <div class="d-flex align-center mb-3">
                                    <v-icon color="warning" class="mr-2"
                                        >mdi-shield-alert</v-icon
                                    >
                                    <span
                                        class="text-warning text-caption font-bold tracking-widest uppercase"
                                        >Brechas Críticas</span
                                    >
                                </div>
                                <div
                                    class="bg-warning-lighten-5 pa-3 border-warning-lighten-4 h-100 rounded-lg border"
                                >
                                    <ul class="text-body-2 pl-4">
                                        <li
                                            v-for="risk in aiInsightData.risks"
                                            :key="risk"
                                            class="mb-2"
                                        >
                                            {{ risk }}
                                        </li>
                                    </ul>
                                </div>
                            </v-col>
                        </v-row>

                        <!-- Retention & Mitigation -->
                        <v-row class="mb-6">
                            <v-col cols="12" md="6">
                                <div
                                    class="text-caption mb-1 font-black tracking-widest uppercase opacity-60"
                                >
                                    Pronóstico de Retención
                                </div>
                                <div class="text-body-2 font-bold">
                                    {{
                                        aiInsightData.retention_forecast ||
                                        'No disponible'
                                    }}
                                </div>
                            </v-col>
                            <v-col cols="12" md="6">
                                <div
                                    class="text-caption mb-1 font-black tracking-widest uppercase opacity-60"
                                >
                                    Plan de Mitigación (90 días)
                                </div>
                                <div class="text-body-2">
                                    {{
                                        aiInsightData.mitigation_plan ||
                                        'No disponible'
                                    }}
                                </div>
                            </v-col>
                        </v-row>

                        <!-- Strategic Rationale -->
                        <v-card
                            variant="flat"
                            color="blue-grey-lighten-5"
                            class="pa-2 rounded-xl border-s-4 border-primary"
                        >
                            <v-card-text>
                                <div class="d-flex align-center mb-2">
                                    <v-icon
                                        color="primary"
                                        size="small"
                                        class="mr-2"
                                        >mdi-brain</v-icon
                                    >
                                    <span
                                        class="text-caption font-black tracking-widest text-primary uppercase"
                                        >Veredicto del Matchmaker</span
                                    >
                                </div>
                                <div
                                    class="text-body-2 text-blue-grey-darken-3 italic"
                                    style="line-height: 1.6"
                                >
                                    "{{ aiInsightData.strategic_rationale }}"
                                </div>
                            </v-card-text>
                        </v-card>
                    </div>
                    <div v-else class="py-12 text-center">
                        <v-progress-circular
                            indeterminate
                            color="primary"
                            size="64"
                            width="6"
                            class="mb-6"
                        ></v-progress-circular>
                        <div class="text-h6 font-weight-bold">
                            Decodificando Talent DNA
                        </div>
                        <div class="text-body-2 text-medium-emphasis">
                            El motor de Resonancia está evaluando el fit
                            cultural y técnico...
                        </div>
                    </div>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
    <!-- Cierre de pa-4 -->
</template>

<style scoped>
.gap-4 {
    gap: 16px;
}

.h-100 {
    height: 100%;
}

.d-flex {
    display: flex;
}

.flex-column {
    flex-direction: column;
}

.flex-grow-1 {
    flex-grow: 1;
}

.justify-space-between {
    justify-content: space-between;
}

.align-center {
    align-items: center;
}

.gap-2 {
    gap: 8px;
}

.mb-2 {
    margin-bottom: 8px;
}

.mb-4 {
    margin-bottom: 16px;
}

.mb-0 {
    margin-bottom: 0;
}

.pb-2 {
    padding-bottom: 8px;
}

.w-100 {
    width: 100%;
}

.mr-1 {
    margin-right: 4px;
}

.text-grey {
    color: #757575;
}

.flex-wrap {
    flex-wrap: wrap;
}

.mt-auto {
    margin-top: auto;
}
</style>
