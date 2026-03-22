<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useQualityMetrics } from '@/composables/useQualityMetrics';
import type { KpiCard } from '@/types/quality';
import { Head } from '@inertiajs/vue3';
import {
    PhActivity,
    PhCheckCircle,
    PhClock,
    PhFire,
    PhGauge,
    PhStackPlus,
    PhWarning,
    PhZap,
} from '@phosphor-icons/vue';
import { computed, onBeforeUnmount, onMounted } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

defineOptions({
    layout: AppLayout,
});

// Data
const { metrics, isLoading, error, lastUpdated, hallucination, qualityPassed, qualityFailed, topProvider, fetchMetrics, startPolling, stopPolling } = useQualityMetrics();

// KPI Cards
const kpiCards = computed<KpiCard[]>(() => [
    {
        title: 'Evaluaciones Totales',
        value: metrics.value.total_evaluations || 0,
        icon: 'mdi-counter',
        color: 'indigo',
        unit: 'análisis',
    },
    {
        title: 'Puntuación Promedio',
        value: (metrics.value.avg_composite_score * 100).toFixed(1),
        icon: 'mdi-gauge',
        color: 'emerald',
        unit: '%',
        thresholdWarning: 70,
    },
    {
        title: 'Tasa de Alucinación',
        value: hallucination.value,
        icon: 'mdi-alert',
        color: 'rose',
        unit: '%',
        thresholdWarning: 5,
    },
    {
        title: 'Evaluaciones Pasadas',
        value: qualityPassed.value,
        icon: 'mdi-check-circle',
        color: 'emerald',
        unit: 'modelos',
    },
]);

// Chart configs
const qualityDistributionChart = computed(() => ({
    options: {
        chart: {
            id: 'quality-distribution',
            type: 'pie',
            toolbar: { show: false },
        },
        labels: ['Excelente', 'Bueno', 'Aceptable', 'Pobre', 'Crítico'],
        colors: ['#10b981', '#06b6d4', '#f59e0b', '#f97316', '#ef4444'],
        legend: {
            position: 'bottom',
            labels: {
                colors: 'rgba(255,255,255,0.7)',
            },
        },
        theme: {
            mode: 'dark',
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    formatter: (val: number) => val.toFixed(0) + '%',
                },
            },
        },
    },
    series: [
        metrics.value.quality_distribution.excellent || 0,
        metrics.value.quality_distribution.good || 0,
        metrics.value.quality_distribution.acceptable || 0,
        metrics.value.quality_distribution.poor || 0,
        metrics.value.quality_distribution.critical || 0,
    ],
}));

const providerDistributionChart = computed(() => {
    const providers = Object.keys(metrics.value.provider_distribution || {});
    const counts = Object.values(metrics.value.provider_distribution || {});

    return {
        options: {
            chart: {
                id: 'provider-distribution',
                type: 'bar',
                toolbar: { show: false },
            },
            xaxis: {
                categories: providers,
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
            colors: ['#6366f1'],
            legend: {
                show: false,
            },
            theme: {
                mode: 'dark',
            },
            grid: {
                borderColor: 'rgba(255,255,255,0.1)',
            },
        },
        series: [
            {
                name: 'Evaluaciones',
                data: counts,
            },
        ],
    };
});

// Lifecycle
onMounted(() => {
    fetchMetrics();
    // Auto-refresh cada 30 segundos
    startPolling(30000);
});

onBeforeUnmount(() => {
    stopPolling();
});

// Format timestamp
const formatLastUpdated = (date: Date | null) => {
    if (!date) return 'Nunca';
    const now = new Date();
    const diff = Math.floor((now.getTime() - date.getTime()) / 1000);
    if (diff < 60) return 'Hace poco';
    if (diff < 3600) return `Hace ${Math.floor(diff / 60)}m`;
    return date.toLocaleTimeString();
};

// Helper para color de salud
const getHealthColor = (score: number) => {
    if (score >= 0.85) return 'text-emerald-400';
    if (score >= 0.7) return 'text-amber-400';
    return 'text-rose-400';
};

const getHealthBg = (score: number) => {
    if (score >= 0.85) return 'bg-emerald-500/10 border-emerald-500/30';
    if (score >= 0.7) return 'bg-amber-500/10 border-amber-500/30';
    return 'bg-rose-500/10 border-rose-500/30';
};
</script>

<template>
    <Head title="LLM Quality Dashboard" />

    <template #header>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-white drop-shadow-md">
                    <span class="mr-3">🎯</span> LLM Quality Dashboard
                </h1>
                <p class="mt-2 text-sm text-white/50">
                    Evaluación de calidad RAGAS · Monitoreo de alucinaciones ·
                    Salud de LLM
                </p>
            </div>
            <div class="text-right text-xs text-white/40">
                <p>{{ formatLastUpdated(lastUpdated) }}</p>
                <p v-if="isLoading" class="animate-pulse">Actualizando...</p>
            </div>
        </div>
    </template>

    <div class="mt-6 space-y-6">
        <!-- Loading State -->
        <div v-if="isLoading && !metrics.total_evaluations" class="space-y-4">
            <StCardGlass
                v-for="i in 4"
                :key="`skeleton-${i}`"
                class="animate-pulse h-32 bg-white/5"
            />
        </div>

        <!-- Error State -->
        <StCardGlass
            v-else-if="error"
            class="border-rose-500/30 bg-rose-500/10 p-6"
        >
            <div class="flex items-center gap-4">
                <PhWarning class="text-rose-400" :size="28" weight="duotone" />
                <div>
                    <h3 class="font-bold text-rose-300">Error cargando métricas</h3>
                    <p class="text-sm text-rose-400/70">{{ error }}</p>
                </div>
            </div>
        </StCardGlass>

        <!-- Content -->
        <div v-else-if="!isLoading || metrics.total_evaluations > 0" class="space-y-6">
            <!-- KPI Cards Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StCardGlass
                    v-for="card in kpiCards"
                    :key="card.title"
                    :indicator="card.color"
                    class="group relative overflow-hidden p-6 hover:border-white/20 transition-all"
                    :class="
                        card.color === 'indigo' ? 'border-indigo-500/20' :
                        card.color === 'emerald' ? 'border-emerald-500/20' :
                        card.color === 'rose' ? 'border-rose-500/20' :
                        'border-white/10'
                    "
                >
                    <!-- Background Icon -->
                    <div
                        class="absolute -right-6 -bottom-6 opacity-5 group-hover:opacity-10 transition-opacity"
                        :class="
                            card.color === 'indigo' ? 'text-indigo-500' :
                            card.color === 'emerald' ? 'text-emerald-500' :
                            card.color === 'rose' ? 'text-rose-500' :
                            'text-cyan-500'
                        "
                    >
                        <PhStackPlus :size="100" weight="duotone" />
                    </div>

                    <!-- Content -->
                    <div>
                        <p class="text-xs font-bold tracking-widest text-white/50 uppercase mb-3">
                            {{ card.title }}
                        </p>
                        <div class="flex items-baseline gap-2">
                            <span
                                class="text-3xl font-black tracking-tighter text-white"
                                :class="getHealthColor(parseFloat(String(card.value)) / (card.unit === '%' ? 100 : 1))"
                            >
                                {{ card.value }}
                            </span>
                            <span
                                v-if="card.unit"
                                class="text-xs font-semibold text-white/40"
                            >
                                {{ card.unit }}
                            </span>
                        </div>
                    </div>
                </StCardGlass>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Quality Distribution Pie -->
                <StCardGlass
                    class="border-white/10 p-0"
                    :no-hover="true"
                >
                    <div class="flex items-center gap-3 border-b border-white/10 bg-white/5 px-6 py-4">
                        <PhZap class="text-indigo-400" :size="20" weight="duotone" />
                        <h3 class="font-bold text-white">Distribución de Calidad</h3>
                    </div>
                    <div class="p-6">
                        <VueApexCharts
                            v-if="qualityDistributionChart.series.some((v: number) => v > 0)"
                            :options="qualityDistributionChart.options"
                            :series="qualityDistributionChart.series"
                            type="pie"
                            height="300"
                        />
                        <div v-else class="h-64 flex items-center justify-center text-white/40">
                            <p>Sin datos de evaluaciones</p>
                        </div>
                    </div>
                </StCardGlass>

                <!-- Provider Distribution Bar -->
                <StCardGlass
                    class="border-white/10 p-0"
                    :no-hover="true"
                >
                    <div class="flex items-center gap-3 border-b border-white/10 bg-white/5 px-6 py-4">
                        <PhActivity class="text-emerald-400" :size="20" weight="duotone" />
                        <h3 class="font-bold text-white">Evaluaciones por Proveedor</h3>
                    </div>
                    <div class="p-6">
                        <VueApexCharts
                            v-if="Object.keys(metrics.provider_distribution).length > 0"
                            :options="providerDistributionChart.options"
                            :series="providerDistributionChart.series"
                            type="bar"
                            height="300"
                        />
                        <div v-else class="h-64 flex items-center justify-center text-white/40">
                            <p>Sin datos por proveedor</p>
                        </div>
                    </div>
                </StCardGlass>
            </div>

            <!-- Detailed Stats -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <!-- Health Status -->
                <StCardGlass
                    :class="getHealthBg(metrics.avg_composite_score)"
                    class="border p-6"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold tracking-widest text-white/50 uppercase mb-2">
                                Estado de Salud
                            </p>
                            <p
                                class="text-2xl font-black"
                                :class="getHealthColor(metrics.avg_composite_score)"
                            >
                                {{
                                    metrics.avg_composite_score >= 0.85
                                        ? 'Excelente'
                                        : metrics.avg_composite_score >= 0.7
                                          ? 'Bueno'
                                          : 'Requiere Atención'
                                }}
                            </p>
                        </div>
                        <PhCheckCircle
                            :size="32"
                            weight="duotone"
                            :class="getHealthColor(metrics.avg_composite_score)"
                        />
                    </div>
                </StCardGlass>

                <!-- Last Evaluation -->
                <StCardGlass class="border-cyan-500/20 bg-cyan-500/10 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold tracking-widest text-white/50 uppercase mb-2">
                                Última Evaluación
                            </p>
                            <p class="text-sm text-cyan-300">
                                {{
                                    metrics.last_evaluation_at
                                        ? new Date(metrics.last_evaluation_at).toLocaleDateString('es-ES', {
                                              month: 'short',
                                              day: 'numeric',
                                              hour: '2-digit',
                                              minute: '2-digit',
                                          })
                                        : 'N/A'
                                }}
                            </p>
                        </div>
                        <PhClock
                            :size="32"
                            weight="duotone"
                            class="text-cyan-400"
                        />
                    </div>
                </StCardGlass>

                <!-- Top Provider Info -->
                <StCardGlass class="border-amber-500/20 bg-amber-500/10 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold tracking-widest text-white/50 uppercase mb-2">
                                Proveedor Dominante
                            </p>
                            <p class="text-lg font-bold text-amber-300 uppercase">
                                {{ topProvider }}
                            </p>
                        </div>
                        <PhFire :size="32" weight="duotone" class="text-amber-400" />
                    </div>
                </StCardGlass>
            </div>

            <!-- Quality Distribution Table -->
            <StCardGlass class="border-white/10 p-0" :no-hover="true">
                <div class="flex items-center gap-3 border-b border-white/10 bg-white/5 px-6 py-4">
                    <PhGauge class="text-indigo-400" :size="20" weight="duotone" />
                    <h3 class="font-bold text-white">Desglose de Calidad</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-white/10">
                                <th class="px-6 py-4 text-left text-xs font-bold tracking-widest text-white/50 uppercase">
                                    Nivel
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-bold tracking-widest text-white/50 uppercase">
                                    Cantidad
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-bold tracking-widest text-white/50 uppercase">
                                    % del Total
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t border-white/5 hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 text-emerald-400 font-semibold">Excelente</td>
                                <td class="px-6 py-4 text-right text-white">
                                    {{ metrics.quality_distribution.excellent }}
                                </td>
                                <td class="px-6 py-4 text-right text-white/60">
                                    {{
                                        metrics.total_evaluations > 0
                                            ? (
                                                (metrics.quality_distribution.excellent /
                                                    metrics.total_evaluations) *
                                                100
                                            ).toFixed(1)
                                            : 0
                                    }}%
                                </td>
                            </tr>
                            <tr class="border-t border-white/5 hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 text-cyan-400 font-semibold">Bueno</td>
                                <td class="px-6 py-4 text-right text-white">
                                    {{ metrics.quality_distribution.good }}
                                </td>
                                <td class="px-6 py-4 text-right text-white/60">
                                    {{
                                        metrics.total_evaluations > 0
                                            ? (
                                                (metrics.quality_distribution.good /
                                                    metrics.total_evaluations) *
                                                100
                                            ).toFixed(1)
                                            : 0
                                    }}%
                                </td>
                            </tr>
                            <tr class="border-t border-white/5 hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 text-amber-400 font-semibold">Aceptable</td>
                                <td class="px-6 py-4 text-right text-white">
                                    {{ metrics.quality_distribution.acceptable }}
                                </td>
                                <td class="px-6 py-4 text-right text-white/60">
                                    {{
                                        metrics.total_evaluations > 0
                                            ? (
                                                (metrics.quality_distribution.acceptable /
                                                    metrics.total_evaluations) *
                                                100
                                            ).toFixed(1)
                                            : 0
                                    }}%
                                </td>
                            </tr>
                            <tr class="border-t border-white/5 hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 text-orange-400 font-semibold">Pobre</td>
                                <td class="px-6 py-4 text-right text-white">
                                    {{ metrics.quality_distribution.poor }}
                                </td>
                                <td class="px-6 py-4 text-right text-white/60">
                                    {{
                                        metrics.total_evaluations > 0
                                            ? (
                                                (metrics.quality_distribution.poor /
                                                    metrics.total_evaluations) *
                                                100
                                            ).toFixed(1)
                                            : 0
                                    }}%
                                </td>
                            </tr>
                            <tr class="border-t border-white/5 hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 text-rose-400 font-semibold">Crítico</td>
                                <td class="px-6 py-4 text-right text-white">
                                    {{ metrics.quality_distribution.critical }}
                                </td>
                                <td class="px-6 py-4 text-right text-white/60">
                                    {{
                                        metrics.total_evaluations > 0
                                            ? (
                                                (metrics.quality_distribution.critical /
                                                    metrics.total_evaluations) *
                                                100
                                            ).toFixed(1)
                                            : 0
                                    }}%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </StCardGlass>

            <!-- Empty State -->
            <div v-if="metrics.total_evaluations === 0" class="text-center py-12">
                <PhActivity :size="48" class="mx-auto mb-4 text-white/20" />
                <p class="text-white/50">No hay evaluaciones registradas aún</p>
                <p class="text-xs text-white/30 mt-2">
                    Inicia evaluaciones de LLM usando /api/qa/llm-evaluations
                </p>
            </div>
        </div>
    </div>
</template>
