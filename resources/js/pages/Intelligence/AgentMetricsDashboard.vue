<script setup lang="ts">
import { useAgentMetrics } from '@/composables/useAgentMetrics';
import AppLayout from '@/layouts/AppLayout.vue';
import type { KpiCard } from '@/types/quality';
import {
    PhChartLine,
    PhWarning,
    PhCheckCircle,
    PhClock,
} from '@phosphor-icons/vue';
import { computed, onBeforeUnmount, onMounted } from 'vue';
import ApexChart from 'apexcharts';
import { VChart } from 'vue-echarts';
import * as echarts from 'echarts';

defineOptions({
    layout: AppLayout,
});

// Data
const {
    metrics,
    failingAgents,
    agentLatencies,
    isLoading,
    error,
    lastUpdated,
    successRate,
    totalInteractions,
    avgLatency,
    topFailingAgent,
    topSlowAgent,
    fetchAllMetrics,
    startPolling,
    stopPolling,
} = useAgentMetrics();

// KPI Cards
const kpiCards = computed<KpiCard[]>(() => [
    {
        title: 'Interacciones Totales',
        value: totalInteractions.value,
        icon: 'mdi-counter',
        color: 'indigo',
        unit: 'llamadas',
    },
    {
        title: 'Tasa de Éxito',
        value: (successRate.value * 100).toFixed(1),
        icon: 'mdi-check-circle',
        color: 'emerald',
        unit: '%',
        thresholdWarning: 90,
    },
    {
        title: 'Latencia Promedio',
        value: avgLatency.value,
        icon: 'mdi-clock',
        color: 'blue',
        unit: 'ms',
    },
    {
        title: 'Agentes Fallidos',
        value: failingAgents.value.length,
        icon: 'mdi-alert-circle',
        color: 'rose',
        unit: 'agentes',
    },
]);

// Chart: Interacciones por agente
const agentInteractionsChart = computed(() => ({
    options: {
        chart: {
            id: 'agent-interactions',
            type: 'bar',
            toolbar: { show: false },
            fontFamily: 'inherit',
        },
        xaxis: {
            categories: metrics.value.by_agent.map((a) => a.agent_name) || [],
            labels: {
                style: {
                    colors: 'rgba(255,255,255,0.7)',
                    fontSize: '12px',
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
        colors: ['#6366f1', '#10b981'],
        legend: {
            position: 'top',
            labels: {
                colors: 'rgba(255,255,255,0.7)',
            },
        },
        theme: {
            mode: 'dark',
        },
        grid: {
            borderColor: 'rgba(255,255,255,0.1)',
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: false,
                },
            },
        },
    },
    series: [
        {
            name: 'Interacciones',
            data: metrics.value.by_agent.map((a) => a.count) || [],
        },
        {
            name: 'Tasa Éxito',
            data: metrics.value.by_agent.map((a) => a.success_rate * 100) || [],
        },
    ],
}));

// Chart: Distribución por proveedor
const providerDistributionChart = computed(() => {
    const providers = Object.keys(metrics.value.by_provider || {});
    const counts = Object.values(metrics.value.by_provider || {});

    return {
        options: {
            chart: {
                id: 'provider-distribution',
                type: 'pie',
                toolbar: { show: false },
            },
            labels: providers,
            colors: ['#6366f1', '#10b981', '#f59e0b', '#f97316', '#ef4444'],
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
        series: counts,
    };
});

// Chart: Tendencia diaria (usando echarts line chart)
const dailyTrendOption = computed(() => ({
    title: {
        text: 'Tendencia Diaria de Interacciones',
        textStyle: {
            color: 'rgba(255,255,255,0.87)',
        },
    },
    tooltip: {
        trigger: 'axis',
        backgroundColor: 'rgba(0,0,0,0.8)',
        textStyle: {
            color: 'rgba(255,255,255,0.87)',
        },
    },
    legend: {
        data: ['Total', 'Exitosas', 'Errores'],
        textStyle: {
            color: 'rgba(255,255,255,0.7)',
        },
    },
    grid: {
        backgroundColor: 'transparent',
        borderColor: 'rgba(255,255,255,0.1)',
    },
    xAxis: {
        type: 'category',
        data: metrics.value.daily_trend.map((d) => d.date) || [],
        axisLabel: {
            color: 'rgba(255,255,255,0.7)',
        },
        axisLine: {
            lineStyle: {
                color: 'rgba(255,255,255,0.1)',
            },
        },
    },
    yAxis: {
        type: 'value',
        axisLabel: {
            color: 'rgba(255,255,255,0.7)',
        },
        splitLine: {
            lineStyle: {
                color: 'rgba(255,255,255,0.1)',
            },
        },
    },
    series: [
        {
            name: 'Total',
            data: metrics.value.daily_trend.map((d) => d.total) || [],
            type: 'line',
            itemStyle: { color: '#6366f1' },
            smooth: true,
        },
        {
            name: 'Exitosas',
            data: metrics.value.daily_trend.map((d) => d.success) || [],
            type: 'line',
            itemStyle: { color: '#10b981' },
            smooth: true,
        },
        {
            name: 'Errores',
            data: metrics.value.daily_trend.map((d) => d.error) || [],
            type: 'line',
            itemStyle: { color: '#ef4444' },
            smooth: true,
        },
    ],
}));

// Chart: Distribución de errores (top 5)
const errorDistributionChart = computed(() => {
    const topErrors = metrics.value.error_distribution.slice(0, 5);
    return {
        options: {
            chart: {
                id: 'error-distribution',
                type: 'barH',
                toolbar: { show: false },
            },
            xaxis: {
                categories: topErrors.map((e) => e.error || 'Unknown') || [],
                labels: {
                    style: {
                        colors: 'rgba(255,255,255,0.7)',
                        fontSize: '11px',
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
            colors: ['#ef4444'],
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
                name: 'Error Count',
                data: topErrors.map((e) => e.count) || [],
            },
        ],
    };
});

// Lifecycle
onMounted(() => {
    fetchAllMetrics();
    // Auto-refresh cada 30 segundos
    startPolling(30000);
});

onBeforeUnmount(() => {
    stopPolling();
});
</script>

<template>
    <div class="w-full min-h-screen bg-slate-950">
        <!-- Header -->
        <div class="border-b border-slate-800 bg-slate-900 px-6 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Métricas de Agentes IA
                    </h1>
                    <p class="mt-2 text-slate-400">
                        Monitoreo de latencia, éxito y performance
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-slate-500">
                        Última actualización:
                    </p>
                    <p class="text-lg font-mono text-slate-300">
                        {{
                            lastUpdated
                                ? lastUpdated.toLocaleTimeString()
                                : 'N/A'
                        }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="space-y-6 p-6">
            <!-- Loading State -->
            <div v-if="isLoading" class="text-center py-12">
                <div class="inline-flex items-center space-x-2">
                    <div class="h-4 w-4 animate-spin rounded-full border-2 border-slate-600 border-t-indigo-500"></div>
                    <span class="text-slate-400">Cargando métricas...</span>
                </div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="rounded-lg bg-rose-950 p-4 text-rose-200">
                {{ error }}
            </div>

            <!-- KPI Cards -->
            <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="card in kpiCards"
                    :key="card.title"
                    class="rounded-lg border border-slate-700 bg-slate-900 p-6 hover:border-slate-600 transition"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-400">
                                {{ card.title }}
                            </p>
                            <p class="mt-2 text-2xl font-bold text-white">
                                {{ card.value
                                }}<span class="text-sm text-slate-400 ml-1">{{
                                    card.unit
                                }}</span>
                            </p>
                        </div>
                        <component
                            v-if="card.icon"
                            :is="card.icon"
                            class="h-6 w-6 text-slate-500"
                        />
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div v-if="!isLoading && !error" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Agent Interactions Chart -->
                <div class="rounded-lg border border-slate-700 bg-slate-900 p-6">
                    <h2 class="mb-4 text-lg font-semibold text-white">
                        Interacciones por Agente
                    </h2>
                    <apexchart
                        type="bar"
                        :options="agentInteractionsChart.options"
                        :series="agentInteractionsChart.series"
                        height="300"
                    />
                </div>

                <!-- Provider Distribution Chart -->
                <div class="rounded-lg border border-slate-700 bg-slate-900 p-6">
                    <h2 class="mb-4 text-lg font-semibold text-white">
                        Distribución por Proveedor
                    </h2>
                    <apexchart
                        type="pie"
                        :options="providerDistributionChart.options"
                        :series="providerDistributionChart.series"
                        height="300"
                    />
                </div>

                <!-- Daily Trend Chart -->
                <div class="col-span-1 lg:col-span-2 rounded-lg border border-slate-700 bg-slate-900 p-6">
                    <h2 class="mb-4 text-lg font-semibold text-white">
                        Tendencia Diaria
                    </h2>
                    <v-chart :option="dailyTrendOption" autoresize style="height: 300px" />
                </div>

                <!-- Error Distribution Chart -->
                <div class="col-span-1 lg:col-span-1 rounded-lg border border-slate-700 bg-slate-900 p-6">
                    <h2 class="mb-4 text-lg font-semibold text-white">
                        Top Errores
                    </h2>
                    <apexchart
                        type="barH"
                        :options="errorDistributionChart.options"
                        :series="errorDistributionChart.series"
                        height="250"
                    />
                </div>

                <!-- Failing Agents Table -->
                <div class="rounded-lg border border-slate-700 bg-slate-900 p-6">
                    <h2 class="mb-4 text-lg font-semibold text-white">
                        Agentes Fallidos
                    </h2>
                    <div class="space-y-2">
                        <div
                            v-if="failingAgents.length === 0"
                            class="py-4 text-center text-slate-400"
                        >
                            No hay agentes fallidos
                        </div>
                        <div
                            v-for="agent in failingAgents.slice(0, 5)"
                            :key="agent.agent_name"
                            class="flex items-center justify-between rounded border border-slate-700 bg-slate-800 p-3"
                        >
                            <span class="text-sm text-slate-200">{{ agent.agent_name }}</span>
                            <span class="inline-flex items-center rounded-full bg-rose-950 px-2.5 py-0.5 text-xs font-medium text-rose-200">
                                {{ agent.error_count }} errores
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latency Percentiles -->
            <div v-if="!isLoading && !error" class="rounded-lg border border-slate-700 bg-slate-900 p-6">
                <h2 class="mb-4 text-lg font-semibold text-white">
                    Percentiles de Latencia
                </h2>
                <div class="grid grid-cols-3 gap-4">
                    <div class="rounded border border-slate-700 bg-slate-800 p-4">
                        <p class="text-sm text-slate-400">P50 (Mediana)</p>
                        <p class="mt-2 text-2xl font-bold text-indigo-400">
                            {{ metrics.latency_percentiles.p50 }} ms
                        </p>
                    </div>
                    <div class="rounded border border-slate-700 bg-slate-800 p-4">
                        <p class="text-sm text-slate-400">P95</p>
                        <p class="mt-2 text-2xl font-bold text-yellow-400">
                            {{ metrics.latency_percentiles.p95 }} ms
                        </p>
                    </div>
                    <div class="rounded border border-slate-700 bg-slate-800 p-4">
                        <p class="text-sm text-slate-400">P99</p>
                        <p class="mt-2 text-2xl font-bold text-rose-400">
                            {{ metrics.latency_percentiles.p99 }} ms
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
