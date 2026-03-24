<script setup lang="ts">
import { useVerificationDashboard } from '@/composables/useVerificationDashboard'
import { computed, onMounted, onUnmounted } from 'vue'

defineOptions({
    name: 'InsightsDashboard',
})

const {
    metrics,
    isLoading,
    fetchMetrics,
    startPolling,
} = useVerificationDashboard()

let unsubscribe: (() => void) | null = null

onMounted(() => {
    fetchMetrics()
    unsubscribe = startPolling(60000) // 1 minute refresh for insights
})

onUnmounted(() => {
    unsubscribe?.()
})

// AI-generated insights
const insights = computed(() => [
    {
        id: 1,
        type: 'recommendation',
        icon: '💡',
        title: 'Optimize Retry Strategy',
        description: 'Current retry rate is 18%, consider implementing exponential backoff',
        severity: 'info',
        action: 'View Details',
    },
    {
        id: 2,
        type: 'anomaly',
        icon: '🔍',
        title: 'Unusual Error Spike Detected',
        description: 'Error rate increased by 5% in the last 2 hours. Investigate metrics history.',
        severity: 'warning',
        action: 'Investigate',
    },
    {
        id: 3,
        type: 'prediction',
        icon: '🎯',
        title: 'Forecast: Phase Transition in 4h',
        description: 'Based on confidence trend, system should transition to next phase tonight',
        severity: 'info',
        action: 'Prepare',
    },
    {
        id: 4,
        type: 'optimization',
        icon: '⚡',
        title: 'Sample Size Optimization',
        description: 'Increase sample size by 15% to improve confidence score reliability',
        severity: 'info',
        action: 'Configure',
    },
])

// Trend analysis
const trendAnalysis = computed(() => [
    {
        metric: 'Confidence Score',
        current: metrics.value?.confidenceScore || 0,
        previous: 87,
        trend: 'up',
        change: 3,
    },
    {
        metric: 'Error Rate',
        current: metrics.value?.errorRate || 0,
        previous: 35,
        trend: 'up',
        change: 5,
    },
    {
        metric: 'Retry Rate',
        current: 18,
        previous: 16,
        trend: 'up',
        change: 2,
    },
    {
        metric: 'System Health',
        current: 92,
        previous: 89,
        trend: 'up',
        change: 3,
    },
])

// Recommendations based on metrics
const recommendations = computed(() => {
    const recs = []

    if ((metrics.value?.confidenceScore || 0) < 85) {
        recs.push({
            priority: 'high',
            text: 'Confidence below target. Increase sample size or optimize detection rules.',
        })
    }

    if ((metrics.value?.errorRate || 0) > 40) {
        recs.push({
            priority: 'critical',
            text: 'Error rate exceeds threshold. Immediate investigation required.',
        })
    }

    if ((metrics.value?.retryRate || 0) > 20) {
        recs.push({
            priority: 'medium',
            text: 'High retry rate detected. Review timeout and circuit breaker settings.',
        })
    }

    if ((metrics.value?.sampleSize || 0) < 100) {
        recs.push({
            priority: 'medium',
            text: 'Low sample size affecting accuracy. Gather more data before proceeding.',
        })
    }

    return recs
})

const getPriorityColor = (priority: string) => {
    switch (priority) {
        case 'critical':
            return 'bg-red-500/20 text-red-300 border-red-500/30'
        case 'high':
            return 'bg-orange-500/20 text-orange-300 border-orange-500/30'
        case 'medium':
            return 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30'
        default:
            return 'bg-blue-500/20 text-blue-300 border-blue-500/30'
    }
}

const getTrendIcon = (trend: string) => {
    return trend === 'up' ? '📈' : '📉'
}

const formatTrendChange = (change: number) => {
    return `${change > 0 ? '+' : ''}${change}%`
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 p-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">AI Insights & Recommendations</h1>
            <p class="mt-2 text-sm text-white/50">ML-driven analysis & predictive alerts</p>
        </div>

        <!-- Key Insights -->
        <div class="mb-8 space-y-4">
            <h2 class="text-lg font-semibold text-white">Key Insights</h2>
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div
                    v-for="insight in insights"
                    :key="insight.id"
                    class="rounded-lg border border-white/10 bg-white/5 p-5 backdrop-blur-xl hover:bg-white/10 transition"
                >
                    <div class="flex items-start gap-4">
                        <div class="text-3xl">{{ insight.icon }}</div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-white">{{ insight.title }}</h3>
                            <p class="mt-2 text-sm text-white/70">{{ insight.description }}</p>
                            <button
                                class="mt-3 inline-block rounded-lg bg-blue-500/20 px-3 py-1 text-xs font-medium text-blue-300 hover:bg-blue-500/30 transition"
                            >
                                {{ insight.action }} →
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trend Analysis -->
        <div class="mb-8 rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
            <h2 class="mb-6 text-lg font-semibold text-white">Metric Trends</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div v-for="item in trendAnalysis" :key="item.metric" class="space-y-3">
                    <p class="text-sm text-white/70">{{ item.metric }}</p>
                    <div class="flex items-end gap-3">
                        <p class="text-3xl font-bold text-white">{{ item.current }}</p>
                        <div
                            :class="`flex items-center gap-1 rounded-lg px-2 py-1 text-xs font-semibold ${
                                item.trend === 'up'
                                    ? 'bg-green-500/20 text-green-300'
                                    : 'bg-red-500/20 text-red-300'
                            }`"
                        >
                            <span>{{ getTrendIcon(item.trend) }}</span>
                            <span>{{ formatTrendChange(item.change) }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-white/50">was {{ item.previous }}</p>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Left: AI Recommendations -->
            <div class="lg:col-span-2 rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                <h2 class="mb-4 text-lg font-semibold text-white flex items-center gap-2">
                    <span>🤖</span>
                    <span>AI Recommendations</span>
                </h2>
                <div v-if="recommendations.length > 0" class="space-y-3">
                    <div
                        v-for="(rec, index) in recommendations"
                        :key="index"
                        :class="`rounded-lg border p-4 ${getPriorityColor(rec.priority)}`"
                    >
                        <p class="text-sm font-medium">{{ rec.text }}</p>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-xs uppercase font-bold">{{ rec.priority }}</span>
                            <button class="ml-auto text-xs text-white/70 hover:text-white">
                                Learn more →
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else class="py-8 text-center text-white/50">
                    <p class="text-sm">No recommendations at this time ✓</p>
                </div>
            </div>

            <!-- Right: System Health Score -->
            <div class="rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl flex flex-col">
                <h2 class="mb-6 text-lg font-semibold text-white">System Health Score</h2>

                <!-- Gauge-style indicator -->
                <div class="flex-1 flex flex-col items-center justify-center gap-6">
                    <!-- Score Circle -->
                    <div class="relative w-32 h-32 rounded-full border-4 border-white/10 flex items-center justify-center bg-gradient-to-br from-green-500/20 to-blue-500/20">
                        <div class="text-center">
                            <p class="text-4xl font-bold text-white">92</p>
                            <p class="text-xs text-white/70">/100</p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="text-center">
                        <p class="inline-block rounded-lg bg-green-500/20 px-3 py-1 text-sm font-medium text-green-300">
                            ✓ Excellent
                        </p>
                        <p class="mt-3 text-xs text-white/70">All systems operating normally</p>
                    </div>
                </div>

                <!-- Health Breakdown -->
                <div class="mt-6 space-y-3 border-t border-white/10 pt-6">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-white/70">Reliability</span>
                        <span class="font-semibold text-green-400">95%</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-white/70">Performance</span>
                        <span class="font-semibold text-green-400">88%</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-white/70">Compliance</span>
                        <span class="font-semibold text-green-400">94%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Anomaly Detection Timeline -->
        <div class="mt-8 rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
            <h2 class="mb-6 text-lg font-semibold text-white flex items-center gap-2">
                <span>🔍</span>
                <span>Anomaly Detection Timeline (24h)</span>
            </h2>
            <div class="space-y-4">
                <!-- Event -->
                <div class="flex gap-4">
                    <div class="flex flex-col items-center gap-2">
                        <div class="h-3 w-3 rounded-full bg-yellow-400"></div>
                        <div class="w-0.5 h-12 bg-white/10"></div>
                    </div>
                    <div class="pb-8">
                        <p class="font-semibold text-white">Error Rate Spike</p>
                        <p class="text-sm text-white/70">+5% increase detected at 14:32</p>
                        <p class="mt-2 text-xs text-white/50">Severity: Medium • Duration: 23 min</p>
                    </div>
                </div>

                <!-- Event -->
                <div class="flex gap-4">
                    <div class="flex flex-col items-center gap-2">
                        <div class="h-3 w-3 rounded-full bg-blue-400"></div>
                        <div class="w-0.5 h-12 bg-white/10"></div>
                    </div>
                    <div class="pb-8">
                        <p class="font-semibold text-white">Confidence Improvement</p>
                        <p class="text-sm text-white/70">Score increased to 90% at 11:15</p>
                        <p class="mt-2 text-xs text-white/50">Cause: Increased sample size</p>
                    </div>
                </div>

                <!-- Event -->
                <div class="flex gap-4">
                    <div class="flex flex-col items-center gap-2">
                        <div class="h-3 w-3 rounded-full bg-green-400"></div>
                    </div>
                    <div>
                        <p class="font-semibold text-white">System Stabilized</p>
                        <p class="text-sm text-white/70">All metrics within normal range at 08:00</p>
                        <p class="mt-2 text-xs text-white/50">Duration: Last 8 hours</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Future Forecast -->
        <div class="mt-8 rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
            <h2 class="mb-4 text-lg font-semibold text-white flex items-center gap-2">
                <span>🎯</span>
                <span>24h Forecast</span>
            </h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="space-y-2 rounded-lg border border-white/10 bg-white/5 p-4">
                    <p class="text-sm text-white/70">Predicted Phase</p>
                    <p class="text-2xl font-bold text-indigo-400">Tuning</p>
                    <p class="text-xs text-white/50">Probability: 87%</p>
                </div>
                <div class="space-y-2 rounded-lg border border-white/10 bg-white/5 p-4">
                    <p class="text-sm text-white/70">Expected Confidence</p>
                    <p class="text-2xl font-bold text-green-400">94%</p>
                    <p class="text-xs text-white/50">+4% from current</p>
                </div>
                <div class="space-y-2 rounded-lg border border-white/10 bg-white/5 p-4">
                    <p class="text-sm text-white/70">Predicted Error Rate</p>
                    <p class="text-2xl font-bold text-blue-400">32%</p>
                    <p class="text-xs text-white/50">-8% from current</p>
                </div>
                <div class="space-y-2 rounded-lg border border-white/10 bg-white/5 p-4">
                    <p class="text-sm text-white/70">System Health</p>
                    <p class="text-2xl font-bold text-green-400">96%</p>
                    <p class="text-xs text-white/50">+4% improvement</p>
                </div>
            </div>
        </div>
    </div>
</template>
