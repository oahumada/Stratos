<script setup lang="ts">
import PendingFeedback from '@/components/Assessments/PendingFeedback.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, ref } from 'vue';
import { useTheme as useVuetifyTheme } from 'vuetify';

defineOptions({ layout: AppLayout });

// State
const vuetifyTheme = useVuetifyTheme();
const _loading = ref(false);
const selectedPeriod = ref('month');
const dashboardData = ref({
    talentMetrics: {
        totalEmployees: 124,
        employeesWithGaps: 87,
        employeesReadyForPromotion: 23,
        newHiresLast30Days: 8,
    },
    marketplaceMetrics: {
        openPositions: 12,
        candidatesExcellent: 7,
        candidatesGood: 12,
        candidatesModerate: 5,
        candidatesLow: 3,
        positionsWithoutCandidates: 2,
    },
    recruitmentMetrics: {
        averageTimeToHire: 32,
        internalPlacementRate: 65,
        externalHires: 5,
        internalPromotions: 3,
        retentionRate: 92,
    },
    developmentMetrics: {
        employeesInDevelopment: 45,
        completedCourses: 18,
        skillsCovered: 156,
        criticalGaps: 34,
        developmentPlansActive: 12,
    },
    riskMetrics: {
        talentAtRisk: 5,
        vacanciesUrgent: 2,
        employeesUnderperforming: 3,
        skillsDeprecating: 7,
    },
});

// Computed properties for KPIs
const keyMetrics = computed(() => [
    {
        title: 'Empleados Totales',
        value: dashboardData.value.talentMetrics.totalEmployees,
        icon: 'mdi-account-group',
        color: 'primary',
        subtitle: 'Activos en la organización',
    },
    {
        title: 'Posiciones Abiertas',
        value: dashboardData.value.marketplaceMetrics.openPositions,
        icon: 'mdi-briefcase-outline',
        color: 'warning',
        subtitle: 'Vacantes por cubrir',
    },
    {
        title: 'Tasa de Colocación Interna',
        value: `${dashboardData.value.recruitmentMetrics.internalPlacementRate}%`,
        icon: 'mdi-trending-up',
        color: 'success',
        subtitle: 'De vacantes cubiertas internamente',
    },
    {
        title: 'Retención',
        value: `${dashboardData.value.recruitmentMetrics.retentionRate}%`,
        icon: 'mdi-heart',
        color: 'error',
        subtitle: 'Tasa de retención anual',
    },
]);

const talentReadiness = computed(() => [
    {
        category: 'Listos para Promoción',
        value: dashboardData.value.talentMetrics.employeesReadyForPromotion,
        color: 'success',
        icon: 'mdi-trending-up',
    },
    {
        category: 'En Desarrollo',
        value: dashboardData.value.developmentMetrics.employeesInDevelopment,
        color: 'warning',
        icon: 'mdi-school',
    },
    {
        category: 'Con Gaps Críticos',
        value: dashboardData.value.talentMetrics.employeesWithGaps,
        color: 'error',
        icon: 'mdi-alert-circle',
    },
]);

const candidateDistribution = computed(() => [
    {
        label: 'Excelente (≥80%)',
        value: dashboardData.value.marketplaceMetrics.candidatesExcellent,
        color: 'success',
        percentage: Math.round(
            (dashboardData.value.marketplaceMetrics.candidatesExcellent /
                (dashboardData.value.marketplaceMetrics.candidatesExcellent +
                    dashboardData.value.marketplaceMetrics.candidatesGood +
                    dashboardData.value.marketplaceMetrics.candidatesModerate +
                    dashboardData.value.marketplaceMetrics.candidatesLow)) *
                100,
        ),
    },
    {
        label: 'Bueno (70-79%)',
        value: dashboardData.value.marketplaceMetrics.candidatesGood,
        color: 'info',
        percentage: Math.round(
            (dashboardData.value.marketplaceMetrics.candidatesGood /
                (dashboardData.value.marketplaceMetrics.candidatesExcellent +
                    dashboardData.value.marketplaceMetrics.candidatesGood +
                    dashboardData.value.marketplaceMetrics.candidatesModerate +
                    dashboardData.value.marketplaceMetrics.candidatesLow)) *
                100,
        ),
    },
    {
        label: 'Moderado (50-69%)',
        value: dashboardData.value.marketplaceMetrics.candidatesModerate,
        color: 'warning',
        percentage: Math.round(
            (dashboardData.value.marketplaceMetrics.candidatesModerate /
                (dashboardData.value.marketplaceMetrics.candidatesExcellent +
                    dashboardData.value.marketplaceMetrics.candidatesGood +
                    dashboardData.value.marketplaceMetrics.candidatesModerate +
                    dashboardData.value.marketplaceMetrics.candidatesLow)) *
                100,
        ),
    },
    {
        label: 'Bajo (40-49%)',
        value: dashboardData.value.marketplaceMetrics.candidatesLow,
        color: 'error',
        percentage: Math.round(
            (dashboardData.value.marketplaceMetrics.candidatesLow /
                (dashboardData.value.marketplaceMetrics.candidatesExcellent +
                    dashboardData.value.marketplaceMetrics.candidatesGood +
                    dashboardData.value.marketplaceMetrics.candidatesModerate +
                    dashboardData.value.marketplaceMetrics.candidatesLow)) *
                100,
        ),
    },
]);

const riskIndicators = computed(() => [
    {
        title: 'Talento en Riesgo',
        value: dashboardData.value.riskMetrics.talentAtRisk,
        icon: 'mdi-alert-octagon',
        color: 'error',
        trend: -2,
        subtitle: 'Empleados en riesgo de salida',
    },
    {
        title: 'Vacantes Urgentes',
        value: dashboardData.value.riskMetrics.vacanciesUrgent,
        icon: 'mdi-clock-alert',
        color: 'error',
        trend: 1,
        subtitle: 'Posiciones críticas sin candidatos',
    },
    {
        title: 'Desempeño Bajo',
        value: dashboardData.value.riskMetrics.employeesUnderperforming,
        icon: 'mdi-trending-down',
        color: 'warning',
        trend: 0,
        subtitle: 'Requieren intervención',
    },
    {
        title: 'Habilidades Depreciadas',
        value: dashboardData.value.riskMetrics.skillsDeprecating,
        icon: 'mdi-progress-clock',
        color: 'warning',
        trend: 3,
        subtitle: 'Tecnologías en declive',
    },
]);

const _getStatusColor = (percentage: number): string => {
    if (percentage >= 80) return 'success';
    if (percentage >= 60) return 'warning';
    return 'error';
};

const getTrendIcon = (trend: number): string => {
    if (trend > 0) return 'mdi-trending-up';
    if (trend < 0) return 'mdi-trending-down';
    return 'mdi-minus';
};

const getTrendColor = (trend: number): string => {
    if (trend > 0) return 'error'; // Más riesgo es malo
    if (trend < 0) return 'success'; // Menos riesgo es bueno
    return 'grey';
};

// mark intentionally-unused bindings to satisfy linter during refactor
void _loading.value;
void _getStatusColor;
</script>

<template>
    <div class="pa-8">
        <PendingFeedback />
        <!-- Encabezado Ejecutivo -->
        <header class="mb-10">
            <div
                class="d-flex align-center justify-space-between flex-wrap gap-4"
            >
                <div>
                    <div class="d-flex align-center mb-2 gap-2">
                        <v-icon color="indigo-accent-2" size="small"
                            >mdi-chart-box-outline</v-icon
                        >
                        <span
                            class="text-overline font-weight-bold text-slate-400"
                            >Hub de Inteligencia</span
                        >
                    </div>
                    <h1 class="text-h3 font-weight-black mb-2 text-white">
                        Analítica de
                        <span class="text-indigo-accent-2">Talento</span>
                    </h1>
                    <p class="text-subtitle-1 text-slate-400">
                        Visión estratégica de la salud organizacional y del
                        potencial de la fuerza laboral
                    </p>
                </div>
            </div>
        </header>

        <!-- Selector de Período -->
        <StCardGlass class="mb-8" :no-hover="true">
            <div
                class="d-flex align-center justify-space-between flex-wrap gap-4"
            >
                <div class="d-flex align-center gap-4">
                    <span
                        class="text-subtitle-2 font-weight-bold text-slate-300"
                        >Período de análisis:</span
                    >
                    <v-chip-group
                        v-model="selectedPeriod"
                        selected-class="text-indigo-accent-2 bg-indigo-500/10"
                        mandatory
                    >
                        <v-chip value="week" variant="text" class="text-none"
                            >Semana</v-chip
                        >
                        <v-chip value="month" variant="text" class="text-none"
                            >Mes</v-chip
                        >
                        <v-chip value="quarter" variant="text" class="text-none"
                            >Trimestre</v-chip
                        >
                        <v-chip value="year" variant="text" class="text-none"
                            >Año</v-chip
                        >
                    </v-chip-group>
                </div>
                <div
                    class="d-flex align-center text-caption rounded-pill gap-2 bg-black/20 px-3 py-1 text-slate-500"
                >
                    <v-icon size="12">mdi-refresh</v-icon>
                    Última actualización: Hoy, 14:32
                </div>
            </div>
        </StCardGlass>

        <!-- Fila de KPIs -->
        <div class="mb-10">
            <div class="d-flex align-center justify-space-between mb-6">
                <h2 class="text-h6 font-weight-bold text-slate-200">
                    Panorama Ejecutivo (KPIs)
                </h2>
                <v-btn
                    variant="text"
                    density="comfortable"
                    icon="mdi-dots-horizontal"
                    color="slate-400"
                ></v-btn>
            </div>
            <v-row class="gap-0">
                <v-col
                    v-for="metric in keyMetrics"
                    :key="metric.title"
                    cols="12"
                    sm="6"
                    md="3"
                >
                    <StCardGlass class="pa-6 h-100">
                        <div
                            class="d-flex align-center justify-space-between mb-4"
                        >
                            <v-avatar
                                :color="`${metric.color}-lighten-4`"
                                size="44"
                                class="rounded-lg"
                                variant="tonal"
                            >
                                <v-icon :color="metric.color" size="24">{{
                                    metric.icon
                                }}</v-icon>
                            </v-avatar>
                            <div class="st-badge-live">Tiempo real</div>
                        </div>
                        <div class="text-h4 font-weight-black mb-1 text-white">
                            {{ metric.value }}
                        </div>
                        <div
                            class="text-overline font-weight-bold mb-2 text-slate-500"
                        >
                            {{ metric.title }}
                        </div>
                        <div class="text-body-2 text-slate-400">
                            {{ metric.subtitle }}
                        </div>
                    </StCardGlass>
                </v-col>
            </v-row>
        </div>

        <!-- Disponibilidad de Talento + Distribución de Candidatos -->
        <v-row class="mb-10">
            <!-- Disponibilidad de Talento -->
            <v-col cols="12" md="6">
                <h2 class="text-h6 font-weight-bold mb-4 text-slate-200">
                    Índice de Preparación de la Fuerza Laboral
                </h2>
                <StCardGlass class="pa-6" :no-hover="true">
                    <v-list class="pa-0 bg-transparent" dark>
                        <template
                            v-for="(item, index) in talentReadiness"
                            :key="item.category"
                        >
                            <v-list-item class="px-0 py-4">
                                <template #prepend>
                                    <v-avatar
                                        :color="`${item.color}-lighten-4`"
                                        size="40"
                                        class="mr-4 rounded-lg"
                                        variant="tonal"
                                    >
                                        <v-icon size="20" :color="item.color">{{
                                            item.icon
                                        }}</v-icon>
                                    </v-avatar>
                                </template>
                                <v-list-item-title
                                    class="font-weight-bold text-slate-200"
                                >
                                    {{ item.category }}
                                </v-list-item-title>
                                <template #append>
                                    <div
                                        class="text-h5 font-weight-black text-white"
                                    >
                                        {{ item.value }}
                                    </div>
                                </template>
                            </v-list-item>
                            <v-divider
                                v-if="index < talentReadiness.length - 1"
                                class="border-white/5"
                            />
                        </template>
                    </v-list>
                </StCardGlass>
            </v-col>

            <!-- Distribución de Candidatos -->
            <v-col cols="12" md="6">
                <h2 class="text-h6 font-weight-bold mb-4 text-slate-200">
                    Espectro de Calidad de Habilidades
                </h2>
                <StCardGlass class="pa-6" :no-hover="true">
                    <v-list class="pa-0 bg-transparent">
                        <template
                            v-for="(item, index) in candidateDistribution"
                            :key="item.label"
                        >
                            <v-list-item class="px-0 py-4">
                                <template #prepend>
                                    <div
                                        style="min-width: 60px"
                                        class="mr-4 text-center"
                                    >
                                        <div
                                            class="text-h6 font-weight-black text-white"
                                        >
                                            {{ item.percentage }}%
                                        </div>
                                        <div
                                            class="text-tiny font-weight-bold text-slate-500 uppercase"
                                        >
                                            Ajuste
                                        </div>
                                    </div>
                                </template>
                                <v-list-item-title
                                    class="font-weight-bold text-slate-200"
                                >
                                    {{ item.label }}
                                </v-list-item-title>
                                <template #append>
                                    <div class="d-flex align-center gap-2">
                                        <span
                                            class="text-h5 font-weight-black text-white"
                                            >{{ item.value }}</span
                                        >
                                        <v-icon size="small" :color="item.color"
                                            >mdi-chart-line</v-icon
                                        >
                                    </div>
                                </template>
                            </v-list-item>
                            <v-divider
                                v-if="index < candidateDistribution.length - 1"
                                class="border-white/5"
                            />
                        </template>
                    </v-list>
                </StCardGlass>
            </v-col>
        </v-row>

        <!-- Indicadores de Riesgo -->
        <div class="mb-10">
            <h2
                class="text-h6 font-weight-bold flex-center mb-6 gap-2 text-slate-200"
            >
                <v-icon color="rose-accent-4">mdi-shield-alert-outline</v-icon>
                Análisis de Riesgos y Vulnerabilidades
            </h2>
            <v-row class="gap-0">
                <v-col
                    v-for="risk in riskIndicators"
                    :key="risk.title"
                    cols="12"
                    sm="6"
                    md="3"
                >
                    <StCardGlass
                        class="pa-6 h-100"
                        :style="{
                            borderTopColor: `rgba(var(--v-theme-${risk.color}), 0.3) !important`,
                            borderTopWidth: '3px !important',
                        }"
                    >
                        <div
                            class="d-flex align-center justify-space-between mb-4"
                        >
                            <v-avatar
                                :color="`${risk.color}`"
                                size="36"
                                class="rounded-lg"
                                variant="tonal"
                            >
                                <v-icon size="20">{{ risk.icon }}</v-icon>
                            </v-avatar>
                            <v-icon
                                :color="getTrendColor(risk.trend)"
                                size="20"
                                >{{ getTrendIcon(risk.trend) }}</v-icon
                            >
                        </div>
                        <div
                            class="text-h4 font-weight-black text-rose-accent-1 mb-1"
                        >
                            {{ risk.value }}
                        </div>
                        <div
                            class="text-overline font-weight-bold mb-2 text-slate-500"
                        >
                            {{ risk.title }}
                        </div>
                        <div class="text-body-2 text-slate-400">
                            {{ risk.subtitle }}
                        </div>
                    </StCardGlass>
                </v-col>
            </v-row>
        </div>

        <!-- Sección de Desarrollo y Habilidades -->
        <v-row class="mb-10">
            <v-col cols="12" md="6">
                <h2 class="text-h6 font-weight-bold mb-4 text-slate-200">
                    Crecimiento y Capacidades
                </h2>
                <StCardGlass class="pa-8" :no-hover="true">
                    <div class="mb-8">
                        <div
                            class="d-flex justify-space-between align-center mb-3"
                        >
                            <span
                                class="text-subtitle-1 font-weight-bold text-slate-300"
                                >Nodos Activos de Aprendizaje</span
                            >
                            <span
                                class="text-h5 font-weight-black text-emerald-accent-2"
                            >
                                {{
                                    dashboardData.developmentMetrics
                                        .developmentPlansActive
                                }}
                            </span>
                        </div>
                        <v-progress-linear
                            model-value="65"
                            color="emerald-accent-2"
                            height="6"
                            rounded
                            class="mb-1 opacity-60"
                        />
                    </div>
                    <v-divider class="mb-8 border-white/5" />
                    <div class="mb-8">
                        <div
                            class="d-flex justify-space-between align-center mb-3"
                        >
                            <span
                                class="text-subtitle-1 font-weight-bold text-slate-300"
                                >Certificaciones de Maestría</span
                            >
                            <span
                                class="text-h5 font-weight-black text-cyan-accent-2"
                            >
                                {{
                                    dashboardData.developmentMetrics
                                        .completedCourses
                                }}
                            </span>
                        </div>
                        <v-progress-linear
                            model-value="82"
                            color="cyan-accent-2"
                            height="6"
                            rounded
                            class="mb-1 opacity-60"
                        />
                    </div>
                    <v-divider class="mb-8 border-white/5" />
                    <div>
                        <div
                            class="d-flex justify-space-between align-center mb-3"
                        >
                            <span
                                class="text-subtitle-1 font-weight-bold text-slate-300"
                                >Densidad del Mapa de Habilidades</span
                            >
                            <span
                                class="text-h5 font-weight-black text-indigo-accent-2"
                            >
                                {{
                                    dashboardData.developmentMetrics
                                        .skillsCovered
                                }}
                                Unidades
                            </span>
                        </div>
                        <v-progress-linear
                            model-value="45"
                            color="indigo-accent-2"
                            height="6"
                            rounded
                            class="mb-1 opacity-60"
                        />
                    </div>
                </StCardGlass>
            </v-col>

            <v-col cols="12" md="6">
                <h2 class="text-h6 font-weight-bold mb-4 text-slate-200">
                    Pipeline de Adquisición
                </h2>
                <StCardGlass class="pa-4" :no-hover="true">
                    <v-list class="pa-0 bg-transparent">
                        <v-list-item class="border-b border-white/5 px-3 py-4">
                            <v-list-item-title
                                class="font-weight-bold text-slate-300"
                                >Tiempo Promedio de Cobertura</v-list-item-title
                            >
                            <template #append>
                                <div
                                    class="st-badge-live bg-indigo-500/10 text-indigo-300"
                                >
                                    {{
                                        dashboardData.recruitmentMetrics
                                            .averageTimeToHire
                                    }}
                                    días
                                </div>
                            </template>
                        </v-list-item>
                        <v-list-item class="border-b border-white/5 px-3 py-4">
                            <v-list-item-title
                                class="font-weight-bold text-slate-300"
                                >Colocaciones Inmersivas</v-list-item-title
                            >
                            <template #append>
                                <span
                                    class="text-h6 font-weight-black text-white"
                                    >{{
                                        dashboardData.recruitmentMetrics
                                            .internalPromotions
                                    }}</span
                                >
                            </template>
                        </v-list-item>
                        <v-list-item class="border-b border-white/5 px-3 py-4">
                            <v-list-item-title
                                class="font-weight-bold text-slate-300"
                                >Contrataciones Externas</v-list-item-title
                            >
                            <template #append>
                                <span
                                    class="text-h6 font-weight-black text-white"
                                    >{{
                                        dashboardData.recruitmentMetrics
                                            .externalHires
                                    }}</span
                                >
                            </template>
                        </v-list-item>
                        <v-list-item class="px-3 py-4">
                            <v-list-item-title
                                class="font-weight-bold text-slate-300"
                                >Velocidad (últimos 30 días)</v-list-item-title
                            >
                            <template #append>
                                <div class="d-flex align-center gap-2">
                                    <v-icon
                                        color="emerald-accent-2"
                                        size="small"
                                        >mdi-trending-up</v-icon
                                    >
                                    <span
                                        class="text-h5 font-weight-black text-emerald-accent-2"
                                        >+{{
                                            dashboardData.talentMetrics
                                                .newHiresLast30Days
                                        }}</span
                                    >
                                </div>
                            </template>
                        </v-list-item>
                    </v-list>
                </StCardGlass>
            </v-col>
        </v-row>

        <!-- Alertas y Recomendaciones -->
        <div class="mb-10">
            <h2
                class="text-h6 font-weight-bold flex-center mb-6 gap-2 text-slate-200"
            >
                <v-icon color="indigo-accent-2">mdi-lighthouse</v-icon>
                Insights y Directrices de IA
            </h2>
            <StCardGlass
                class="pa-0 mb-4 overflow-hidden"
                :no-hover="true"
                style="border-left: 4px solid #f43f5e !important"
            >
                <div class="pa-6 d-flex align-start gap-4">
                    <v-avatar
                        color="rose-accent-4"
                        rounded="lg"
                        size="44"
                        variant="tonal"
                    >
                        <v-icon color="rose-accent-2"
                            >mdi-alert-decagram</v-icon
                        >
                    </v-avatar>
                    <div>
                        <div
                            class="text-h6 font-weight-black text-rose-accent-2 mb-1"
                        >
                            Riesgo Crítico de Fuga de Talento
                        </div>
                        <div class="text-body-1 text-slate-300">
                            <strong
                                >{{
                                    dashboardData.riskMetrics.talentAtRisk
                                }}
                                empleados clave</strong
                            >
                            muestran alta probabilidad de salida. Se recomienda
                            activar una estrategia inmediata de retención para
                            estos nodos críticos.
                        </div>
                    </div>
                </div>
            </StCardGlass>

            <StCardGlass
                class="pa-0 mb-4 overflow-hidden"
                :no-hover="true"
                style="border-left: 4px solid #fbbf24 !important"
            >
                <div class="pa-6 d-flex align-start gap-4">
                    <v-avatar
                        color="amber-accent-4"
                        rounded="lg"
                        size="44"
                        variant="tonal"
                    >
                        <v-icon color="amber-accent-2"
                            >mdi-account-search</v-icon
                        >
                    </v-avatar>
                    <div>
                        <div
                            class="text-h6 font-weight-black text-amber-accent-2 mb-1"
                        >
                            Déficit de Oferta en Marketplace
                        </div>
                        <div class="text-body-1 text-slate-300">
                            <strong
                                >{{
                                    dashboardData.marketplaceMetrics
                                        .positionsWithoutCandidates
                                }}
                                posiciones técnicas</strong
                            >
                            no cuentan con candidatos internos viables. Se
                            recomienda iniciar búsqueda externa para clusters
                            Senior Backend y Data.
                        </div>
                    </div>
                </div>
            </StCardGlass>

            <StCardGlass
                class="pa-0 overflow-hidden"
                :no-hover="true"
                style="border-left: 4px solid #10b981 !important"
            >
                <div class="pa-6 d-flex align-start gap-4">
                    <v-avatar
                        color="emerald-accent-4"
                        rounded="lg"
                        size="44"
                        variant="tonal"
                    >
                        <v-icon color="emerald-accent-2"
                            >mdi-rocket-launch</v-icon
                        >
                    </v-avatar>
                    <div>
                        <div
                            class="text-h6 font-weight-black text-emerald-accent-2 mb-1"
                        >
                            Ventana de Oportunidad de Promoción
                        </div>
                        <div class="text-body-1 text-slate-300">
                            <strong
                                >{{
                                    dashboardData.talentMetrics
                                        .employeesReadyForPromotion
                                }}
                                talentos de alto potencial</strong
                            >
                            alcanzaron ≥80% de ajuste de habilidades para roles
                            avanzados. Se sugiere acelerar su ruta de carrera.
                        </div>
                    </div>
                </div>
            </StCardGlass>
        </div>

        <!-- Nota de Sistema -->
        <StCardGlass class="pa-4 bg-black/40" :no-hover="true">
            <div class="d-flex align-center gap-3">
                <v-icon size="20" color="slate-500"
                    >mdi-information-outline</v-icon
                >
                <div class="text-body-2 text-slate-400">
                    <strong class="text-slate-300">Nota del sistema:</strong>
                    Este reporte de inteligencia usa actualmente datasets
                    simulados de alta fidelidad. La integración con nodos ERP
                    organizacionales en vivo está pendiente para el despliegue
                    de Fase 2.
                </div>
            </div>
        </StCardGlass>
    </div>
</template>

<style scoped>
.text-tiny {
    font-size: 0.65rem;
}

.text-slate-200 {
    color: #e2e8f0;
}
.text-slate-300 {
    color: #cbd5e1;
}
.text-slate-400 {
    color: #94a3b8;
}
.text-slate-500 {
    color: #64748b;
}

.h-100 {
    height: 100%;
}
</style>
