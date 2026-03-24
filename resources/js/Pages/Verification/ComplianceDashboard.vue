<script setup lang="ts">
import { useVerificationDashboard } from '@/composables/useVerificationDashboard';
import { computed, onMounted, onUnmounted } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

defineOptions({
    name: 'ComplianceDashboard',
});

const {
    complianceData,
    isLoading,
    startPolling,
    fetchComplianceData,
    exportMetrics,
} = useVerificationDashboard();

let stopPolling: (() => void) | null = null;

onMounted(() => {
    fetchComplianceData();
    stopPolling = startPolling(60000);
});

onUnmounted(() => {
    stopPolling?.();
});

const complianceHistory = computed(() => {
    // Mock data for compliance trend
    return {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        scores: [85, 87, 89, 88, 91, 94],
    };
});

const complianceTrendOptions = computed(() => ({
    chart: {
        type: 'line',
        sparkline: { enabled: false },
        toolbar: { show: true },
        background: 'transparent',
    },
    colors: ['#10b981'],
    stroke: { curve: 'smooth', width: 3 },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0 } },
    xaxis: {
        categories: complianceHistory.value.labels,
        labels: { style: { colors: 'rgba(255,255,255,0.5)' } },
    },
    yaxis: {
        min: 0,
        max: 100,
        labels: { style: { colors: 'rgba(255,255,255,0.5)' } },
    },
    grid: { borderColor: 'rgba(255,255,255,0.1)' },
    tooltip: { theme: 'dark' },
}));

const complianceTrendSeries = computed(() => [
    {
        name: 'Compliance Score',
        data: complianceHistory.value.scores,
    },
]);

const auditHistory = computed(() => [
    {
        id: 1,
        name: 'Q1 2026 Audit',
        date: '2026-03-31',
        status: 'passed',
        score: 94,
        highlights: '12/12 checks passed',
    },
    {
        id: 2,
        name: 'Q4 2025 Audit',
        date: '2025-12-31',
        status: 'passed',
        score: 91,
        highlights: '11/12 checks passed',
    },
    {
        id: 3,
        name: 'Q3 2025 Audit',
        date: '2025-09-30',
        status: 'passed',
        score: 88,
        highlights: '10/12 checks passed',
    },
]);

const getStatusColor = (status: string) => {
    switch (status) {
        case 'passed':
            return 'bg-green-500/20 text-green-300';
        case 'failed':
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
                    Compliance Dashboard
                </h1>
                <p class="mt-2 text-sm text-white/50">
                    Audit history & compliance metrics
                </p>
            </div>
            <button
                @click="exportMetrics('pdf')"
                class="rounded-lg bg-indigo-600/20 px-4 py-2 text-sm font-semibold text-indigo-300 hover:bg-indigo-600/30"
            >
                Export Report
            </button>
        </div>

        <!-- Compliance Score Cards -->
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div
                class="rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl"
            >
                <p class="text-sm font-medium text-white/70">Current Score</p>
                <p
                    v-if="complianceData"
                    class="mt-2 text-4xl font-bold text-green-400"
                >
                    {{ complianceData.score }}%
                </p>
                <div
                    v-if="complianceData"
                    class="mt-4 h-1 w-full rounded-full bg-white/10"
                >
                    <div
                        :style="`width: ${complianceData.score}%`"
                        class="h-1 rounded-full bg-green-500"
                    ></div>
                </div>
            </div>

            <div
                class="rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl"
            >
                <p class="text-sm font-medium text-white/70">Passed Tests</p>
                <p
                    v-if="complianceData"
                    class="mt-2 text-4xl font-bold text-white"
                >
                    {{ complianceData.passedTests }}/12
                </p>
                <p class="mt-2 text-xs text-green-400">All critical passed</p>
            </div>

            <div
                class="rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl"
            >
                <p class="text-sm font-medium text-white/70">Trend</p>
                <p
                    v-if="complianceData"
                    :class="`mt-2 text-2xl font-bold ${complianceData.trend === 'up' ? 'text-green-400' : complianceData.trend === 'down' ? 'text-red-400' : 'text-yellow-400'}`"
                >
                    {{
                        complianceData.trend === 'up'
                            ? '↑'
                            : complianceData.trend === 'down'
                              ? '↓'
                              : '→'
                    }}
                    {{ Math.abs(3) }}%
                </p>
            </div>
        </div>

        <!-- Compliance Trend Chart -->
        <div
            class="mb-8 rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl"
        >
            <h2 class="mb-4 text-lg font-semibold text-white">
                Score Trend (6 Months)
            </h2>
            <VueApexCharts
                type="line"
                :options="complianceTrendOptions"
                :series="complianceTrendSeries"
                height="250"
            />
        </div>

        <!-- Audit History -->
        <div
            class="rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl"
        >
            <h2 class="mb-6 text-lg font-semibold text-white">Audit History</h2>
            <div class="space-y-3">
                <div
                    v-for="audit in auditHistory"
                    :key="audit.id"
                    class="rounded-lg border border-white/10 bg-white/5 p-4"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="font-semibold text-white">
                                {{ audit.name }}
                            </h3>
                            <p class="mt-1 text-sm text-white/70">
                                {{ audit.date }} · {{ audit.highlights }}
                            </p>
                        </div>
                        <div class="text-right">
                            <div
                                :class="`inline-block rounded-lg px-3 py-2 text-sm font-semibold ${getStatusColor(audit.status)}`"
                            >
                                {{ audit.status.toUpperCase() }}
                            </div>
                            <p class="mt-2 text-lg font-bold text-white">
                                {{ audit.score }}%
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
