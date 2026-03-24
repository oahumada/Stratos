<script setup lang="ts">
import { useVerificationDashboard } from '@/composables/useVerificationDashboard';
import { computed, onMounted, onUnmounted } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

defineOptions({
    name: 'ExecutiveDashboard',
});

const {
    metrics,
    complianceData,
    isLoading,
    lastUpdated,
    systemStatus,
    startPolling,
    fetchMetrics,
    fetchComplianceData,
    exportMetrics,
} = useVerificationDashboard();

let stopPolling: (() => void) | null = null;

onMounted(() => {
    fetchMetrics();
    fetchComplianceData();
    stopPolling = startPolling(30000);
});

onUnmounted(() => {
    stopPolling?.();
});

const kpiCards = computed(() => [
    {
        label: 'Current Phase',
        value: metrics.value?.currentPhase || 'N/A',
        icon: 'mdi-transit-transfer',
        color: 'indigo',
        change: '',
    },
    {
        label: 'Confidence Score',
        value: `${metrics.value?.confidenceScore || 0}%`,
        icon: 'mdi-target',
        color: 'success',
        change: '+2.5%',
    },
    {
        label: 'Error Rate',
        value: `${metrics.value?.errorRate || 0}%`,
        icon: 'mdi-alert-circle',
        color: 'warning',
        change: '-1.2%',
    },
    {
        label: 'Compliance Score',
        value: `${complianceData.value?.score || 0}%`,
        icon: 'mdi-check-circle',
        color: 'success',
        change: complianceData.value?.trend === 'up' ? '↑' : '↓',
    },
]);

const transitionsChartOptions = computed(() => ({
    chart: {
        type: 'area',
        sparkline: { enabled: false },
        toolbar: { show: false },
        background: 'transparent',
    },
    colors: ['#818cf8', '#10b981'],
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0 } },
    xaxis: {
        categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        labels: { style: { colors: 'rgba(255,255,255,0.5)' } },
    },
    yaxis: { labels: { style: { colors: 'rgba(255,255,255,0.5)' } } },
    grid: { borderColor: 'rgba(255,255,255,0.1)' },
    tooltip: { theme: 'dark' },
}));

const transitionsChartSeries = computed(() => [
    {
        name: 'Successful',
        data: [4, 5, 6, 5, 7, 8, 6],
    },
    {
        name: 'Failed',
        data: [1, 0, 1, 2, 1, 0, 1],
    },
]);

const formatDate = (date: Date | null) => {
    if (!date) return 'Never';
    return date.toLocaleString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getSystemStatusColor = (status: string) => {
    switch (status) {
        case 'healthy':
            return 'bg-green-500/20 text-green-300';
        case 'warning':
            return 'bg-yellow-500/20 text-yellow-300';
        case 'critical':
            return 'bg-red-500/20 text-red-300';
        default:
            return 'bg-gray-500/20 text-gray-300';
    }
};
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 p-8"
    >
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">
                    Executive Dashboard
                </h1>
                <p class="mt-2 text-sm text-white/50">
                    Real-time verification hub insights & metrics
                </p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-xs text-white/50">System Status</p>
                    <div
                        :class="`${getSystemStatusColor(systemStatus)} rounded-full px-4 py-2 text-sm font-semibold`"
                    >
                        {{ systemStatus.toUpperCase() }}
                    </div>
                </div>
                <button
                    @click="exportMetrics('pdf')"
                    class="rounded-lg bg-indigo-600/20 px-4 py-2 text-sm font-semibold text-indigo-300 hover:bg-indigo-600/30"
                >
                    Export
                </button>
            </div>
        </div>

        <!-- KPI Cards Grid -->
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="card in kpiCards"
                :key="card.label"
                class="rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl transition hover:bg-white/10"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/70">
                            {{ card.label }}
                        </p>
                        <p class="mt-2 text-2xl font-bold text-white">
                            {{ card.value }}
                        </p>
                        <p class="mt-1 text-xs text-green-400">
                            {{ card.change }}
                        </p>
                    </div>
                    <div
                        :class="`text-2xl ${card.color === 'indigo' ? 'text-indigo-400' : card.color === 'success' ? 'text-green-400' : 'text-yellow-400'}`"
                    >
                        <!-- Icon would go here -->
                        📊
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Transitions Chart -->
            <div
                class="rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl"
            >
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">
                        Transitions (7 Days)
                    </h2>
                    <span class="text-xs text-white/50">Last 7 days</span>
                </div>
                <VueApexCharts
                    type="area"
                    :options="transitionsChartOptions"
                    :series="transitionsChartSeries"
                    height="300"
                />
            </div>

            <!-- Health Overview -->
            <div
                class="rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl"
            >
                <h2 class="mb-6 text-lg font-semibold text-white">
                    System Health
                </h2>
                <div class="space-y-4">
                    <div v-if="isLoading" class="text-center text-white/50">
                        Loading...
                    </div>
                    <div v-else-if="metrics" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-white/70">Uptime</span>
                            <span class="font-semibold text-green-400"
                                >99.9%</span
                            >
                        </div>
                        <div class="h-1 w-full rounded-full bg-white/10">
                            <div
                                class="h-1 w-[99.9%] rounded-full bg-green-500"
                            ></div>
                        </div>

                        <div class="flex items-center justify-between pt-4">
                            <span class="text-white/70">Availability</span>
                            <span class="font-semibold text-green-400"
                                >99.95%</span
                            >
                        </div>
                        <div class="h-1 w-full rounded-full bg-white/10">
                            <div
                                class="h-1 w-[99.95%] rounded-full bg-green-500"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transitions & Stats -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Recent Transitions -->
            <div
                class="rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl lg:col-span-2"
            >
                <h2 class="mb-4 text-lg font-semibold text-white">
                    Recent Transitions
                </h2>
                <div class="space-y-2">
                    <div v-if="metrics?.recentTransitions">
                        <div
                            v-for="transition in metrics.recentTransitions.slice(
                                0,
                                5,
                            )"
                            :key="transition.id"
                            class="flex items-center justify-between rounded-lg bg-white/5 p-3 text-sm"
                        >
                            <span class="text-white">{{
                                transition.phase
                            }}</span>
                            <span class="text-xs text-green-400">{{
                                transition.date
                            }}</span>
                        </div>
                    </div>
                    <div v-else class="text-center text-white/50">
                        No recent transitions
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div
                class="rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl"
            >
                <h2 class="mb-4 text-lg font-semibold text-white">
                    Quick Stats
                </h2>
                <div class="space-y-3">
                    <div class="text-sm text-white/70">
                        <span class="font-semibold text-white"
                            >Last Updated</span
                        >
                        <p class="mt-1 text-xs text-white/50">
                            {{ formatDate(lastUpdated) }}
                        </p>
                    </div>
                    <div class="text-sm text-white/70">
                        <span class="font-semibold text-white"
                            >Sample Size</span
                        >
                        <p class="mt-1 text-lg font-bold text-white">
                            {{ metrics?.sampleSize || 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
