<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
import AppLayout from '@/layouts/app/AppLayout.vue';
import axios from 'axios';
import { computed, onMounted, ref, watch } from 'vue';

// ────────────────────────────────────────────────────────────────────────────
// Types
// ────────────────────────────────────────────────────────────────────────────
interface QualityDistribution {
    excellent: number;
    good: number;
    acceptable: number;
    poor: number;
    critical: number;
}

interface SummaryMetrics {
    total_evaluations: number;
    avg_composite_score: number;
    max_composite_score: number;
    min_composite_score: number;
    quality_distribution: QualityDistribution;
    provider_distribution: Record<string, number>;
    last_evaluation_at: string | null;
}

interface Evaluation {
    id: number;
    status: string;
    llm_provider: string;
    llm_model: string | null;
    quality_level: string | null;
    created_at: string;
    metrics?: {
        composite_score: number;
        normalized_score: number;
        quality_level: string;
        individual_scores: {
            faithfulness: number;
            relevance: number;
            context_alignment: number;
            coherence: number;
            hallucination_rate: number;
        };
    };
}

// ────────────────────────────────────────────────────────────────────────────
// State
// ────────────────────────────────────────────────────────────────────────────
const loading = ref(true);
const loadingList = ref(false);
const summary = ref<SummaryMetrics | null>(null);
const evaluations = ref<Evaluation[]>([]);
const selectedProvider = ref<string>('');
const providers = ['deepseek', 'abacus', 'openai', 'intel', 'mock'];

// ────────────────────────────────────────────────────────────────────────────
// API calls
// ────────────────────────────────────────────────────────────────────────────
const fetchSummary = async () => {
    try {
        const params = selectedProvider.value
            ? { provider: selectedProvider.value }
            : {};
        const { data } = await axios.get(
            '/api/qa/llm-evaluations/metrics/summary',
            { params },
        );
        summary.value = data.data;
    } catch {
        summary.value = null;
    }
};

const fetchEvaluations = async () => {
    loadingList.value = true;
    try {
        const params: Record<string, string | number> = { per_page: 15 };
        if (selectedProvider.value) params.provider = selectedProvider.value;
        const { data } = await axios.get('/api/qa/llm-evaluations', { params });
        evaluations.value = data.data ?? [];
    } catch {
        evaluations.value = [];
    } finally {
        loadingList.value = false;
    }
};

const fetchAll = async () => {
    loading.value = true;
    await Promise.all([fetchSummary(), fetchEvaluations()]);
    loading.value = false;
};

onMounted(fetchAll);
watch(selectedProvider, fetchAll);

// ────────────────────────────────────────────────────────────────────────────
// Quality helpers
// ────────────────────────────────────────────────────────────────────────────
const qualityColors: Record<string, string> = {
    excellent: '#4CAF50',
    good: '#8BC34A',
    acceptable: '#FFC107',
    poor: '#FF9800',
    critical: '#F44336',
};

const qualityColor = (level: string | null) =>
    qualityColors[level ?? ''] ?? '#9E9E9E';

const qualityChip = (level: string | null): string => {
    const map: Record<string, string> = {
        excellent: 'success',
        good: 'success',
        acceptable: 'warning',
        poor: 'orange',
        critical: 'error',
    };
    return map[level ?? ''] ?? 'default';
};

const providerIcon: Record<string, string> = {
    deepseek: 'mdi-brain',
    abacus: 'mdi-calculator',
    openai: 'mdi-robot',
    intel: 'mdi-chip',
    mock: 'mdi-test-tube',
};

const scoreColor = (score: number) => {
    if (score >= 0.88) return 'success';
    if (score >= 0.8) return 'warning';
    return 'error';
};

const formatScore = (score: number) => (score * 100).toFixed(0) + '%';
const formatDate = (iso: string) =>
    new Date(iso).toLocaleDateString('es-CL', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });

// ────────────────────────────────────────────────────────────────────────────
// Computed: dominant quality level
// ────────────────────────────────────────────────────────────────────────────
const dominantQuality = computed(() => {
    if (!summary.value) return null;
    const dist = summary.value.quality_distribution;
    return Object.entries(dist).reduce(
        (a, b) => (b[1] > a[1] ? b : a),
        ['none', 0],
    )[0];
});

// ────────────────────────────────────────────────────────────────────────────
// ApexCharts: Donut - quality distribution
// ────────────────────────────────────────────────────────────────────────────
const donutSeries = computed(() => {
    if (!summary.value) return [];
    const d = summary.value.quality_distribution;
    return [d.excellent, d.good, d.acceptable, d.poor, d.critical];
});

const donutOptions = computed(() => ({
    chart: {
        type: 'donut',
        toolbar: { show: false },
        background: 'transparent',
    },
    labels: ['Excelente', 'Bueno', 'Aceptable', 'Deficiente', 'Crítico'],
    colors: [
        qualityColors.excellent,
        qualityColors.good,
        qualityColors.acceptable,
        qualityColors.poor,
        qualityColors.critical,
    ],
    legend: { position: 'bottom', labels: { colors: '#CBD5E1' } },
    dataLabels: { enabled: true, style: { colors: ['#1E293B'] } },
    plotOptions: {
        pie: {
            donut: {
                size: '62%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Total',
                        color: '#94A3B8',
                        formatter: (w: {
                            globals: { seriesTotals: number[] };
                        }) =>
                            w.globals.seriesTotals
                                .reduce((a: number, b: number) => a + b, 0)
                                .toString(),
                    },
                },
            },
        },
    },
    theme: { mode: 'dark' },
    tooltip: { theme: 'dark' },
}));

// ────────────────────────────────────────────────────────────────────────────
// ApexCharts: Bar - provider distribution
// ────────────────────────────────────────────────────────────────────────────
const barSeries = computed(() => {
    if (!summary.value) return [];
    const dist = summary.value.provider_distribution;
    return [{ name: 'Evaluaciones', data: Object.values(dist) }];
});

const barOptions = computed(() => {
    const dist = summary.value?.provider_distribution ?? {};
    return {
        chart: {
            type: 'bar',
            toolbar: { show: false },
            background: 'transparent',
        },
        xaxis: {
            categories: Object.keys(dist),
            labels: { style: { colors: '#94A3B8' } },
        },
        yaxis: { labels: { style: { colors: '#94A3B8' } } },
        colors: ['#6366F1'],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '50%' } },
        dataLabels: { enabled: true, style: { colors: ['#fff'] } },
        grid: { borderColor: '#1E293B' },
        theme: { mode: 'dark' },
        tooltip: { theme: 'dark' },
    };
});

// ────────────────────────────────────────────────────────────────────────────
// Table headers
// ────────────────────────────────────────────────────────────────────────────
const headers = [
    { title: 'Proveedor', key: 'llm_provider', sortable: true },
    { title: 'Estado', key: 'status', sortable: true },
    { title: 'Nivel', key: 'quality_level', sortable: true },
    { title: 'Score', key: 'composite_score', sortable: true },
    { title: 'Fecha', key: 'created_at', sortable: true },
];
</script>

<template>
    <AppLayout title="RAGAS — LLM Quality Metrics">
        <v-container fluid class="pa-6">
            <!-- Header -->
            <div
                class="d-flex align-center justify-space-between mb-6 flex-wrap gap-4"
            >
                <div>
                    <h1 class="text-h5 font-weight-bold mb-1 text-white">
                        <v-icon
                            icon="mdi-chart-bar"
                            class="mr-2 text-indigo-400"
                        />
                        RAGAS — LLM Quality Dashboard
                    </h1>
                    <p class="text-body-2 text-slate-400">
                        Métricas de fidelidad y calidad de generaciones LLM por
                        proveedor
                    </p>
                </div>

                <!-- Provider filter -->
                <v-select
                    v-model="selectedProvider"
                    :items="[
                        { title: 'Todos los proveedores', value: '' },
                        ...providers.map((p) => ({
                            title: p.toUpperCase(),
                            value: p,
                        })),
                    ]"
                    item-title="title"
                    item-value="value"
                    variant="outlined"
                    density="compact"
                    hide-details
                    style="max-width: 220px"
                    prepend-inner-icon="mdi-filter"
                    class="text-slate-200"
                />
            </div>

            <!-- Loading skeleton -->
            <template v-if="loading">
                <v-row>
                    <v-col v-for="n in 4" :key="n" cols="12" sm="6" md="3">
                        <v-skeleton-loader type="card" class="rounded-xl" />
                    </v-col>
                </v-row>
                <v-row class="mt-4">
                    <v-col cols="12" md="5">
                        <v-skeleton-loader
                            type="card"
                            height="340"
                            class="rounded-xl"
                        />
                    </v-col>
                    <v-col cols="12" md="7">
                        <v-skeleton-loader
                            type="card"
                            height="340"
                            class="rounded-xl"
                        />
                    </v-col>
                </v-row>
            </template>

            <!-- No data state -->
            <template v-else-if="!summary || summary.total_evaluations === 0">
                <StCardGlass class="pa-12 text-center">
                    <v-icon
                        icon="mdi-chart-timeline-variant"
                        size="72"
                        class="mb-4 text-slate-600"
                    />
                    <p class="text-h6 mb-2 text-slate-400">
                        Sin evaluaciones aún
                    </p>
                    <p class="text-body-2 text-slate-500">
                        Las métricas aparecerán aquí una vez que se procesen
                        generaciones LLM.
                    </p>
                </StCardGlass>
            </template>

            <!-- Dashboard content -->
            <template v-else>
                <!-- ── Summary cards ────────────────────────────────────── -->
                <v-row class="mb-2">
                    <!-- Total evaluations -->
                    <v-col cols="12" sm="6" md="3">
                        <StCardGlass class="pa-5 h-100">
                            <div
                                class="d-flex align-center justify-space-between mb-3"
                            >
                                <span
                                    class="text-caption text-uppercase tracking-wider text-slate-400"
                                    >Total evaluaciones</span
                                >
                                <v-icon
                                    icon="mdi-counter"
                                    size="20"
                                    class="text-indigo-400"
                                />
                            </div>
                            <div class="text-h3 font-weight-black text-white">
                                {{ summary.total_evaluations }}
                            </div>
                            <div
                                v-if="summary.last_evaluation_at"
                                class="text-caption mt-1 text-slate-500"
                            >
                                Última:
                                {{ formatDate(summary.last_evaluation_at) }}
                            </div>
                        </StCardGlass>
                    </v-col>

                    <!-- Avg composite score -->
                    <v-col cols="12" sm="6" md="3">
                        <StCardGlass class="pa-5 h-100">
                            <div
                                class="d-flex align-center justify-space-between mb-3"
                            >
                                <span
                                    class="text-caption text-uppercase tracking-wider text-slate-400"
                                    >Score promedio</span
                                >
                                <v-icon
                                    icon="mdi-gauge"
                                    size="20"
                                    class="text-emerald-400"
                                />
                            </div>
                            <div
                                class="text-h3 font-weight-black"
                                :class="`text-${scoreColor(summary.avg_composite_score)}`"
                            >
                                {{ formatScore(summary.avg_composite_score) }}
                            </div>
                            <v-progress-linear
                                :model-value="summary.avg_composite_score * 100"
                                :color="scoreColor(summary.avg_composite_score)"
                                bg-color="surface-variant"
                                rounded
                                height="4"
                                class="mt-2"
                            />
                        </StCardGlass>
                    </v-col>

                    <!-- Max score -->
                    <v-col cols="12" sm="6" md="3">
                        <StCardGlass class="pa-5 h-100">
                            <div
                                class="d-flex align-center justify-space-between mb-3"
                            >
                                <span
                                    class="text-caption text-uppercase tracking-wider text-slate-400"
                                    >Mejor score</span
                                >
                                <v-icon
                                    icon="mdi-trophy"
                                    size="20"
                                    class="text-amber-400"
                                />
                            </div>
                            <div class="text-h3 font-weight-black text-white">
                                {{ formatScore(summary.max_composite_score) }}
                            </div>
                            <div class="text-caption mt-1 text-slate-500">
                                Mín:
                                {{ formatScore(summary.min_composite_score) }}
                            </div>
                        </StCardGlass>
                    </v-col>

                    <!-- Dominant quality -->
                    <v-col cols="12" sm="6" md="3">
                        <StCardGlass class="pa-5 h-100">
                            <div
                                class="d-flex align-center justify-space-between mb-3"
                            >
                                <span
                                    class="text-caption text-uppercase tracking-wider text-slate-400"
                                    >Nivel dominante</span
                                >
                                <v-icon
                                    icon="mdi-star-circle"
                                    size="20"
                                    class="text-purple-400"
                                />
                            </div>
                            <div class="mt-1">
                                <v-chip
                                    :color="qualityChip(dominantQuality)"
                                    variant="flat"
                                    size="large"
                                    class="text-h6 font-weight-bold px-4"
                                >
                                    {{ dominantQuality ?? '—' }}
                                </v-chip>
                            </div>
                            <div class="text-caption mt-3 text-slate-500">
                                {{
                                    summary.quality_distribution[
                                        dominantQuality as keyof QualityDistribution
                                    ]
                                }}
                                evaluaciones
                            </div>
                        </StCardGlass>
                    </v-col>
                </v-row>

                <!-- ── Charts row ────────────────────────────────────────── -->
                <v-row class="mt-2">
                    <!-- Quality donut -->
                    <v-col cols="12" md="5">
                        <StCardGlass class="pa-5 h-100">
                            <p
                                class="text-subtitle-1 font-weight-bold mb-4 text-white"
                            >
                                <v-icon
                                    icon="mdi-chart-donut"
                                    class="mr-1 text-indigo-400"
                                    size="18"
                                />
                                Distribución de calidad
                            </p>
                            <apexchart
                                v-if="donutSeries.some((v) => v > 0)"
                                type="donut"
                                :options="donutOptions"
                                :series="donutSeries"
                                height="280"
                            />
                            <div v-else class="pa-8 text-center text-slate-500">
                                Sin datos de calidad aún
                            </div>
                        </StCardGlass>
                    </v-col>

                    <!-- Provider bar chart -->
                    <v-col cols="12" md="7">
                        <StCardGlass class="pa-5 h-100">
                            <p
                                class="text-subtitle-1 font-weight-bold mb-4 text-white"
                            >
                                <v-icon
                                    icon="mdi-server"
                                    class="mr-1 text-indigo-400"
                                    size="18"
                                />
                                Evaluaciones por proveedor
                            </p>
                            <apexchart
                                v-if="barSeries[0]?.data?.length"
                                type="bar"
                                :options="barOptions"
                                :series="barSeries"
                                height="280"
                            />
                            <div v-else class="pa-8 text-center text-slate-500">
                                Sin distribución por proveedor
                            </div>
                        </StCardGlass>
                    </v-col>
                </v-row>

                <!-- ── Quality level breakdown ────────────────────────── -->
                <v-row class="mt-2">
                    <v-col cols="12">
                        <StCardGlass class="pa-5">
                            <p
                                class="text-subtitle-1 font-weight-bold mb-4 text-white"
                            >
                                <v-icon
                                    icon="mdi-poll"
                                    class="mr-1 text-indigo-400"
                                    size="18"
                                />
                                Breakdown por nivel de calidad
                            </p>
                            <v-row>
                                <v-col
                                    v-for="(
                                        count, level
                                    ) in summary.quality_distribution"
                                    :key="level"
                                    cols="6"
                                    sm="4"
                                    md="2"
                                >
                                    <div class="text-center">
                                        <div
                                            class="text-h4 font-weight-black mb-1"
                                            :style="{
                                                color: qualityColor(level),
                                            }"
                                        >
                                            {{ count }}
                                        </div>
                                        <v-chip
                                            :color="qualityChip(level)"
                                            size="small"
                                            variant="tonal"
                                        >
                                            {{ level }}
                                        </v-chip>
                                        <div
                                            class="text-caption mt-1 text-slate-500"
                                        >
                                            {{
                                                summary.total_evaluations > 0
                                                    ? Math.round(
                                                          (count /
                                                              summary.total_evaluations) *
                                                              100,
                                                      )
                                                    : 0
                                            }}%
                                        </div>
                                    </div>
                                </v-col>
                            </v-row>
                        </StCardGlass>
                    </v-col>
                </v-row>

                <!-- ── Recent evaluations table ───────────────────────── -->
                <v-row class="mt-2">
                    <v-col cols="12">
                        <StCardGlass class="pa-5">
                            <p
                                class="text-subtitle-1 font-weight-bold mb-4 text-white"
                            >
                                <v-icon
                                    icon="mdi-history"
                                    class="mr-1 text-indigo-400"
                                    size="18"
                                />
                                Evaluaciones recientes
                            </p>
                            <v-data-table
                                :headers="headers"
                                :items="evaluations"
                                :loading="loadingList"
                                density="compact"
                                hover
                                class="bg-transparent text-slate-200"
                                no-data-text="Sin evaluaciones para este filtro"
                            >
                                <!-- Provider -->
                                <template #[`item.llm_provider`]="{ item }">
                                    <div class="d-flex align-center gap-2">
                                        <v-icon
                                            :icon="
                                                providerIcon[
                                                    item.llm_provider
                                                ] ?? 'mdi-robot'
                                            "
                                            size="16"
                                            class="text-indigo-400"
                                        />
                                        <span
                                            class="text-caption font-weight-medium"
                                            >{{ item.llm_provider }}</span
                                        >
                                    </div>
                                </template>

                                <!-- Status -->
                                <template #[`item.status`]="{ item }">
                                    <v-chip
                                        :color="
                                            item.status === 'completed'
                                                ? 'success'
                                                : item.status === 'failed'
                                                  ? 'error'
                                                  : 'warning'
                                        "
                                        size="x-small"
                                        variant="tonal"
                                    >
                                        {{ item.status }}
                                    </v-chip>
                                </template>

                                <!-- Quality level -->
                                <template #[`item.quality_level`]="{ item }">
                                    <v-chip
                                        v-if="item.quality_level"
                                        :color="qualityChip(item.quality_level)"
                                        size="x-small"
                                        variant="flat"
                                    >
                                        {{ item.quality_level }}
                                    </v-chip>
                                    <span
                                        v-else
                                        class="text-caption text-slate-500"
                                        >—</span
                                    >
                                </template>

                                <!-- Composite score -->
                                <template #[`item.composite_score`]="{ item }">
                                    <span
                                        v-if="item.metrics?.composite_score"
                                        :class="`text-${scoreColor(item.metrics.composite_score)}`"
                                        class="font-weight-medium text-caption"
                                    >
                                        {{
                                            formatScore(
                                                item.metrics.composite_score,
                                            )
                                        }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-caption text-slate-500"
                                        >—</span
                                    >
                                </template>

                                <!-- Date -->
                                <template #[`item.created_at`]="{ item }">
                                    <span class="text-caption text-slate-400">{{
                                        formatDate(item.created_at)
                                    }}</span>
                                </template>
                            </v-data-table>
                        </StCardGlass>
                    </v-col>
                </v-row>
            </template>
        </v-container>
    </AppLayout>
</template>
