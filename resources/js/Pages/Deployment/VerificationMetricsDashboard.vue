<template>
    <div class="space-y-8 pb-12">
        <!-- Header -->
        <div
            class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between"
        >
            <div>
                <h1
                    class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white"
                >
                    {{ $t('verification.metrics_dashboard') }}
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    {{ $t('verification.monitor_system_health') }}
                </p>
            </div>
            <div class="flex gap-3">
                <button
                    @click="refreshMetrics"
                    :disabled="loading"
                    class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-200 disabled:opacity-50 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700"
                >
                    <Icon icon="ph:arrow-clockwise-bold" :size="16" />
                    {{ loading ? $t('common.loading') : $t('common.refresh') }}
                </button>
                <select
                    v-model="windowHours"
                    @change="refreshMetrics"
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                >
                    <option value="6">{{ $t('common.last') }} 6h</option>
                    <option value="24">{{ $t('common.last') }} 24h</option>
                    <option value="72">{{ $t('common.last') }} 72h</option>
                    <option value="168">{{ $t('common.last') }} 1w</option>
                </select>
            </div>
        </div>

        <!-- Loading State -->
        <div
            v-if="loading"
            class="rounded-lg border border-gray-200 p-12 text-center dark:border-gray-700"
        >
            <Icon
                icon="ph:spinner-bold"
                :size="32"
                class="mx-auto animate-spin text-gray-400"
            />
            <p class="mt-4 text-gray-600 dark:text-gray-400">
                {{ $t('common.loading') }}...
            </p>
        </div>

        <!-- Error State -->
        <div
            v-else-if="error"
            class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-900 dark:bg-red-950"
        >
            <p class="text-sm font-medium text-red-900 dark:text-red-100">
                {{ error }}
            </p>
        </div>

        <!-- Empty State -->
        <div
            v-else-if="!metrics"
            class="rounded-lg border border-gray-200 p-12 text-center dark:border-gray-700"
        >
            <Icon
                icon="ph:chart-line-up-bold"
                :size="48"
                class="mx-auto text-gray-400"
            />
            <h3
                class="mt-4 text-lg font-semibold text-gray-900 dark:text-white"
            >
                {{ $t('verification.no_metrics_yet') }}
            </h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ $t('verification.metrics_will_appear') }}
            </p>
        </div>

        <!-- Metrics Grid -->
        <div v-else class="space-y-8">
            <!-- Summary Cards -->
            <div class="grid gap-4 md:grid-cols-4">
                <MetricCard
                    :label="$t('verification.total_verifications')"
                    :value="metrics.total_verifications"
                    :icon="'ph:list-numbers-bold'"
                    :color="'blue'"
                />
                <MetricCard
                    :label="$t('verification.avg_confidence')"
                    :value="`${metrics.avg_confidence_score}%`"
                    :icon="'ph:check-circle-bold'"
                    :color="getConfidenceColor(metrics.avg_confidence_score)"
                />
                <MetricCard
                    :label="$t('verification.error_rate')"
                    :value="`${metrics.error_rate}%`"
                    :icon="'ph:warning-bold'"
                    :color="getErrorColor(metrics.error_rate)"
                />
                <MetricCard
                    :label="$t('verification.current_phase')"
                    :value="$t(`verification.phase_${metrics.current_phase}`)"
                    :icon="'ph:gear-bold'"
                    :color="'purple'"
                />
            </div>

            <!-- Charts Row -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Confidence Score Chart -->
                <div
                    class="rounded-lg border border-gray-200 p-6 dark:border-gray-700"
                >
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        {{ $t('verification.confidence_trend') }}
                    </h3>
                    <div class="mt-4 h-64 w-full">
                        <LineChart
                            :value="confidenceChartData"
                            :options="chartOptions"
                            class="w-full"
                        />
                    </div>
                </div>

                <!-- Error Rate Chart -->
                <div
                    class="rounded-lg border border-gray-200 p-6 dark:border-gray-700"
                >
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        {{ $t('verification.error_rate_trend') }}
                    </h3>
                    <div class="mt-4 h-64 w-full">
                        <LineChart
                            :value="errorChartData"
                            :options="chartOptions"
                            class="w-full"
                        />
                    </div>
                </div>
            </div>

            <!-- Recommendation Breakdown -->
            <div
                class="rounded-lg border border-gray-200 p-6 dark:border-gray-700"
            >
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $t('verification.recommendation_breakdown') }}
                </h3>
                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    <div class="rounded-lg bg-green-50 p-4 dark:bg-green-950">
                        <p
                            class="text-sm font-medium text-green-900 dark:text-green-100"
                        >
                            {{ $t('verification.accepted') }}
                        </p>
                        <p
                            class="mt-2 text-3xl font-bold text-green-600 dark:text-green-300"
                        >
                            {{ metrics.recommendation_breakdown.accepted }}
                        </p>
                        <p
                            class="mt-1 text-xs text-green-700 dark:text-green-200"
                        >
                            {{
                                (
                                    (metrics.recommendation_breakdown.accepted /
                                        metrics.total_verifications) *
                                    100
                                ).toFixed(1)
                            }}% {{ $t('common.of_total') }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-yellow-50 p-4 dark:bg-yellow-950">
                        <p
                            class="text-sm font-medium text-yellow-900 dark:text-yellow-100"
                        >
                            {{ $t('verification.review_needed') }}
                        </p>
                        <p
                            class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-300"
                        >
                            {{ metrics.recommendation_breakdown.review_needed }}
                        </p>
                        <p
                            class="mt-1 text-xs text-yellow-700 dark:text-yellow-200"
                        >
                            {{
                                (
                                    (metrics.recommendation_breakdown
                                        .review_needed /
                                        metrics.total_verifications) *
                                    100
                                ).toFixed(1)
                            }}% {{ $t('common.of_total') }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-red-50 p-4 dark:bg-red-950">
                        <p
                            class="text-sm font-medium text-red-900 dark:text-red-100"
                        >
                            {{ $t('verification.rejected') }}
                        </p>
                        <p
                            class="mt-2 text-3xl font-bold text-red-600 dark:text-red-300"
                        >
                            {{ metrics.recommendation_breakdown.rejected }}
                        </p>
                        <p class="mt-1 text-xs text-red-700 dark:text-red-200">
                            {{
                                (
                                    (metrics.recommendation_breakdown.rejected /
                                        metrics.total_verifications) *
                                    100
                                ).toFixed(1)
                            }}% {{ $t('common.of_total') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Metrics by Type -->
            <div
                v-if="metricsByType && metricsByType.length"
                class="rounded-lg border border-gray-200 p-6 dark:border-gray-700"
            >
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $t('verification.metrics_by_agent') }}
                </h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="border-b border-gray-200 dark:border-gray-700"
                            >
                                <th
                                    class="px-4 py-2 text-left font-medium text-gray-600 dark:text-gray-400"
                                >
                                    {{ $t('verification.agent') }}
                                </th>
                                <th
                                    class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-400"
                                >
                                    {{ $t('verification.events') }}
                                </th>
                                <th
                                    class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-400"
                                >
                                    {{ $t('verification.avg_confidence') }}
                                </th>
                                <th
                                    class="px-4 py-2 text-right font-medium text-gray-600 dark:text-gray-400"
                                >
                                    {{ $t('verification.violations') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <tr
                                v-for="agent in metricsByType"
                                :key="agent.agent_name"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800"
                            >
                                <td
                                    class="px-4 py-3 font-medium text-gray-900 dark:text-white"
                                >
                                    {{ agent.agent_name }}
                                </td>
                                <td
                                    class="px-4 py-3 text-right text-gray-600 dark:text-gray-400"
                                >
                                    {{ agent.event_count }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span
                                        class="inline-block rounded-full px-3 py-1 text-xs font-medium"
                                        :class="
                                            getConfidenceClass(
                                                agent.avg_confidence,
                                            )
                                        "
                                    >
                                        {{ agent.avg_confidence.toFixed(1) }}%
                                    </span>
                                </td>
                                <td
                                    class="px-4 py-3 text-right text-gray-600 dark:text-gray-400"
                                >
                                    {{ agent.total_violations }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Export Button -->
            <div class="flex justify-end">
                <button
                    @click="exportMetrics"
                    :disabled="loading"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                >
                    <Icon icon="ph:download-bold" :size="16" />
                    {{ $t('common.export') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import MetricCard from '@/components/Verification/MetricCard.vue';
import { Icon } from '@phosphor-icons/vue';
import {
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    LineElement,
    PointElement,
    Title,
    Tooltip,
} from 'chart.js';
import { computed, onMounted, ref } from 'vue';
import { LineChart } from 'vue-chartjs';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
);

const loading = ref(false);
const error = ref<string | null>(null);
const metrics = ref<any>(null);
const metricsByType = ref<any[]>([]);
const windowHours = ref(24);

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            labels: {
                color: '#6B7280',
            },
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            max: 100,
            ticks: {
                callback: function (value: any) {
                    return value + '%';
                },
            },
        },
    },
}));

const confidenceChartData = computed(() => ({
    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
    datasets: [
        {
            label: 'Confidence Score',
            data: [metrics.value?.avg_confidence_score || 0, 85, 88, 92],
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
        },
    ],
}));

const errorChartData = computed(() => ({
    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
    datasets: [
        {
            label: 'Error Rate',
            data: [metrics.value?.error_rate || 0, 18, 15, 10],
            borderColor: '#EF4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4,
        },
    ],
}));

const getConfidenceColor = (score: number) => {
    if (score >= 90) return 'green';
    if (score >= 75) return 'yellow';
    return 'red';
};

const getErrorColor = (rate: number) => {
    if (rate <= 10) return 'green';
    if (rate <= 30) return 'yellow';
    return 'red';
};

const getConfidenceClass = (score: number) => {
    if (score >= 90)
        return 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-200';
    if (score >= 75)
        return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950 dark:text-yellow-200';
    return 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-200';
};

const refreshMetrics = async () => {
    loading.value = true;
    error.value = null;

    try {
        const [metricsRes, typeRes] = await Promise.all([
            fetch(
                `/api/deployment/verification-metrics?hours=${windowHours.value}`,
            ),
            fetch(
                `/api/deployment/verification-metrics/by-type?hours=${windowHours.value}`,
            ),
        ]);

        if (metricsRes.ok && metricsRes.status !== 204) {
            const metricsData = await metricsRes.json();
            metrics.value = metricsData.data;
        }

        if (typeRes.ok) {
            const typeData = await typeRes.json();
            metricsByType.value = typeData.data;
        }
    } catch (err) {
        error.value = 'Failed to load metrics';
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const exportMetrics = async () => {
    try {
        const res = await fetch(
            `/api/deployment/verification-metrics/export?hours=${windowHours.value}`,
        );

        if (res.ok) {
            const data = await res.json();
            const json = JSON.stringify(data.export_data, null, 2);
            const blob = new Blob([json], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `verification-metrics-${new Date().toISOString().split('T')[0]}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    } catch (err) {
        error.value = 'Failed to export metrics';
        console.error(err);
    }
};

onMounted(() => {
    refreshMetrics();

    // Auto-refresh every 5 minutes
    setInterval(refreshMetrics, 5 * 60 * 1000);
});
</script>
