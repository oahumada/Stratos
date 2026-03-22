<script setup lang="ts">
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useIntelligenceMetrics } from '@/composables/useIntelligenceMetrics';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

defineOptions({
    layout: AppLayout,
});

const {
    aggregates,
    summary,
    isLoading,
    error,
    lastUpdated,
    timeSeriesData,
    fetchAggregates,
    fetchSummary,
    startPolling,
    stopPolling,
} = useIntelligenceMetrics();

// Filters
const selectedMetricType = ref<string>('');
const dateFrom = ref<string>(
    new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
);
const dateTo = ref<string>(new Date().toISOString().split('T')[0]);

const applyFilters = async () => {
    await fetchAggregates({
        metric_type: selectedMetricType.value as any,
        date_from: dateFrom.value,
        date_to: dateTo.value,
    });
    await fetchSummary({
        metric_type: selectedMetricType.value as any,
        date_from: dateFrom.value,
        date_to: dateTo.value,
    });
};

// KPI Cards
const kpiCards = computed(() => [
    {
        title: 'Llamadas Totales',
        value: summary.value?.total_calls || 0,
        icon: 'mdi-phone',
        color: 'indigo',
        unit: 'llamadas',
    },
    {
        title: 'Tasa de Éxito',
        value: summary.value?.success_rate
            ? (summary.value.success_rate * 100).toFixed(1)
            : 0,
        icon: 'mdi-check-circle',
        color: 'emerald',
        unit: '%',
        thresholdWarning: 90,
    },
    {
        title: 'Latencia Promedio',
        value: summary.value?.avg_duration_ms || 0,
        icon: 'mdi-clock',
        color: 'cyan',
        unit: 'ms',
    },
    {
        title: 'P95 Latencia',
        value: summary.value?.p95_duration_ms || 0,
        icon: 'mdi-speedometer',
        color: 'amber',
        unit: 'ms',
        thresholdWarning: 2000,
    },
    {
        title: 'Confianza Promedio',
        value: summary.value?.avg_confidence
            ? (summary.value.avg_confidence * 100).toFixed(1)
            : 0,
        icon: 'mdi-head-check',
        color: 'rose',
        unit: '%',
    },
    {
        title: 'Contexto Promedio',
        value: summary.value?.avg_context_count?.toFixed(1) || 0,
        icon: 'mdi-file-document-multiple',
        color: 'violet',
        unit: 'docs',
    },
]);

// Line Chart: Success Rate over Time
const successRateChart = computed(() => ({
    options: {
        chart: {
            id: 'success-rate-chart',
            type: 'line',
            toolbar: { show: true },
            fontFamily: 'inherit',
        },
        xaxis: {
            categories: timeSeriesData.value.labels,
            labels: {
                style: {
                    colors: 'rgba(255,255,255,0.7)',
                },
            },
        },
        yaxis: {
            labels: {
                style: {
                    colors: 'rgba(255,255,255,0.7)',
                },
            },
            min: 0,
            max: 100,
        },
        colors: ['#10b981'],
        stroke: {
            width: 3,
            curve: 'smooth',
        },
        theme: {
            mode: 'dark',
        },
        grid: {
            borderColor: 'rgba(255,255,255,0.1)',
        },
        tooltip: {
            theme: 'dark',
        },
    },
    series: [
        {
            name: 'Tasa de Éxito (%)',
            data: timeSeriesData.value.successRate,
        },
    ],
}));

// Line Chart: Latency Trend
const latencyChart = computed(() => ({
    options: {
        chart: {
            id: 'latency-chart',
            type: 'line',
            toolbar: { show: true },
            fontFamily: 'inherit',
        },
        xaxis: {
            categories: timeSeriesData.value.labels,
            labels: {
                style: {
                    colors: 'rgba(255,255,255,0.7)',
                },
            },
        },
        yaxis: {
            labels: {
                style: {
                    colors: 'rgba(255,255,255,0.7)',
                },
            },
        },
        colors: ['#3b82f6', '#f59e0b'],
        stroke: {
            width: 2,
            curve: 'smooth',
        },
        theme: {
            mode: 'dark',
        },
        grid: {
            borderColor: 'rgba(255,255,255,0.1)',
        },
        tooltip: {
            theme: 'dark',
        },
        legend: {
            labels: {
                colors: 'rgba(255,255,255,0.7)',
            },
        },
    },
    series: [
        {
            name: 'Promedio (ms)',
            data: timeSeriesData.value.avgLatency,
        },
        {
            name: 'P95 (ms)',
            data: timeSeriesData.value.p95Latency,
        },
    ],
}));

// Formatters
const formatLastUpdated = (date: Date | null) => {
    if (!date) return 'Nunca';
    return date.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
};

onMounted(() => {
    startPolling(30000); // Poll every 30 seconds
});

onBeforeUnmount(() => {
    stopPolling();
});
</script>

<template>
    <Head title="Intelligence Metrics Dashboard" />

    <div class="mx-auto max-w-7xl space-y-8 p-8">
        <!-- Header -->
        <div class="mb-12 animate-in duration-700 fade-in slide-in-from-top-4">
            <h1 class="text-3xl font-bold tracking-tight text-white">
                📊 Intelligence Metrics Dashboard
            </h1>
            <p class="mt-2 text-sm text-white/60">
                Observabilidad de agregados de métricas de inteligencia y RAG
            </p>
            <div v-if="lastUpdated" class="mt-2 text-xs text-white/40">
                Última actualización: {{ formatLastUpdated(lastUpdated) }}
            </div>
        </div>

        <!-- Filters -->
        <StCardGlass class="mb-8" :no-hover="true">
            <div
                class="flex flex-col justify-between gap-4 md:flex-row md:items-end"
            >
                <div class="space-y-2">
                    <label class="text-sm font-medium text-white/80"
                        >Período de análisis</label
                    >
                    <div class="flex gap-4">
                        <div>
                            <label class="text-xs text-white/50">Desde</label>
                            <input
                                v-model="dateFrom"
                                type="date"
                                class="mt-1 rounded bg-white/5 px-3 py-2 text-white placeholder-white/30"
                            />
                        </div>
                        <div>
                            <label class="text-xs text-white/50">Hasta</label>
                            <input
                                v-model="dateTo"
                                type="date"
                                class="mt-1 rounded bg-white/5 px-3 py-2 text-white placeholder-white/30"
                            />
                        </div>
                        <div>
                            <label class="text-xs text-white/50"
                                >Tipo de métrica</label
                            >
                            <select
                                v-model="selectedMetricType"
                                class="mt-1 rounded bg-white/5 px-3 py-2 text-white"
                            >
                                <option value="">Todas</option>
                                <option value="rag">RAG</option>
                                <option value="llm_call">LLM Call</option>
                                <option value="agent">Agent</option>
                                <option value="evaluation">Evaluation</option>
                            </select>
                        </div>
                    </div>
                </div>
                <StButtonGlass variant="primary" @click="applyFilters">
                    Aplicar filtros
                </StButtonGlass>
            </div>
        </StCardGlass>

        <!-- Loading State -->
        <div v-if="isLoading" class="space-y-4">
            <v-progress-linear indeterminate class="rounded" />
        </div>

        <!-- Error State -->
        <div
            v-if="error"
            class="rounded-lg bg-rose-500/10 px-4 py-3 text-rose-300"
        >
            {{ error }}
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <StCardGlass
                v-for="card in kpiCards"
                :key="card.title"
                class="group relative p-6 hover:bg-white/10"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="text-xs font-semibold tracking-widest text-white/50 uppercase"
                        >
                            {{ card.title }}
                        </p>
                        <p class="mt-2 text-3xl font-black text-white">
                            {{ card.value
                            }}<span class="ml-1 text-sm text-white/50">{{
                                card.unit
                            }}</span>
                        </p>
                    </div>
                    <div class="text-4xl opacity-20">
                        <v-icon :icon="card.icon" :color="card.color" />
                    </div>
                </div>
            </StCardGlass>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Success Rate Chart -->
            <StCardGlass class="p-6">
                <h2 class="mb-4 text-lg font-semibold text-white">
                    Tasa de Éxito (Tendencia)
                </h2>
                <div v-if="!isLoading && timeSeriesData.labels.length > 0">
                    <VueApexCharts
                        type="line"
                        :options="successRateChart.options"
                        :series="successRateChart.series"
                        height="300"
                    />
                </div>
                <div v-else class="flex items-center justify-center py-16">
                    <p class="text-sm text-white/40">Sin datos disponibles</p>
                </div>
            </StCardGlass>

            <!-- Latency Chart -->
            <StCardGlass class="p-6">
                <h2 class="mb-4 text-lg font-semibold text-white">
                    Latencia (Promedio vs P95)
                </h2>
                <div v-if="!isLoading && timeSeriesData.labels.length > 0">
                    <VueApexCharts
                        type="line"
                        :options="latencyChart.options"
                        :series="latencyChart.series"
                        height="300"
                    />
                </div>
                <div v-else class="flex items-center justify-center py-16">
                    <p class="text-sm text-white/40">Sin datos disponibles</p>
                </div>
            </StCardGlass>
        </div>

        <!-- Data Table -->
        <StCardGlass
            v-if="!isLoading && aggregates.length > 0"
            class="overflow-hidden p-0"
        >
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-white/10 bg-white/5">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-white/70"
                            >
                                Fecha
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-white/70"
                            >
                                Tipo
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-white/70"
                            >
                                Fuente
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-semibold text-white/70"
                            >
                                Llamadas
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-semibold text-white/70"
                            >
                                Éxito
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-semibold text-white/70"
                            >
                                Latencia (ms)
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-semibold text-white/70"
                            >
                                P95 (ms)
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr
                            v-for="agg in aggregates.slice(0, 20)"
                            :key="`${agg.date_key}-${agg.metric_type}`"
                            class="hover:bg-white/5"
                        >
                            <td class="px-6 py-3 text-sm text-white">
                                {{ agg.date_key }}
                            </td>
                            <td class="px-6 py-3">
                                <span
                                    class="rounded-full bg-indigo-500/20 px-2.5 py-1 text-xs font-semibold text-indigo-300"
                                >
                                    {{ agg.metric_type }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm text-white/70">
                                {{ agg.source_type || '—' }}
                            </td>
                            <td
                                class="px-6 py-3 text-right text-sm font-medium text-white"
                            >
                                {{ agg.total_count }}
                            </td>
                            <td
                                class="px-6 py-3 text-right text-sm font-medium text-emerald-400"
                            >
                                {{ (agg.success_rate * 100).toFixed(1) }}%
                            </td>
                            <td
                                class="px-6 py-3 text-right text-sm text-white/70"
                            >
                                {{ agg.avg_duration_ms }}
                            </td>
                            <td
                                class="px-6 py-3 text-right text-sm text-white/70"
                            >
                                {{ agg.p95_duration_ms }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </StCardGlass>

        <!-- Empty State -->
        <StCardGlass
            v-if="!isLoading && aggregates.length === 0"
            class="p-24 text-center"
        >
            <div class="flex flex-col items-center justify-center gap-4">
                <v-icon
                    size="64"
                    icon="mdi-chart-box-outline"
                    class="opacity-20"
                />
                <p class="text-2xl font-medium text-white/40">
                    No hay datos disponibles
                </p>
                <p class="text-sm text-white/30">
                    Intenta cambiar los filtros de fecha
                </p>
            </div>
        </StCardGlass>
    </div>
</template>
