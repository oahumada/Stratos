<script setup lang="ts">
import { useVerificationDashboard } from '@/composables/useVerificationDashboard'
import { computed, onMounted, onUnmounted } from 'vue'

defineOptions({
    name: 'OperationalDashboard',
})

const {
    metrics,
    realtimeEvents,
    isLoading,
    recentAlerts,
    transitionReadiness,
    startPolling,
    fetchMetrics,
    subscribeToRealtimeEvents,
} = useVerificationDashboard()

let stopPolling: (() => void) | null = null
let unsubscribe: (() => void) | null = null

onMounted(() => {
    fetchMetrics()
    stopPolling = startPolling(15000) // More frequent for operational view
    unsubscribe = subscribeToRealtimeEvents()
})

onUnmount(() => {
    stopPolling?.()
    unsubscribe?.()
})

const operationalMetrics = computed(() => {
    if (!metrics.value) return null

    return {
        phase: metrics.value.currentPhase,
        confidence: metrics.value.confidenceScore,
        errorRate: metrics.value.errorRate,
        retryRate: metrics.value.retryRate,
        statusIndicators: [
            {
                label: 'Phase',
                value: metrics.value.currentPhase,
                status: 'active' as const,
            },
            {
                label: 'Confidence',
                value: `${metrics.value.confidenceScore}%`,
                status: metrics.value.confidenceScore >= 90 ? ('healthy' as const) : ('warning' as const),
            },
            {
                label: 'Error Rate',
                value: `${metrics.value.errorRate}%`,
                status: metrics.value.errorRate <= 40 ? ('healthy' as const) : ('critical' as const),
            },
        ],
    }
})

const getStatusColor = (status: string) => {
    switch (status) {
        case 'healthy':
            return 'bg-green-500/20 text-green-300'
        case 'warning':
            return 'bg-yellow-500/20 text-yellow-300'
        case 'critical':
            return 'bg-red-500/20 text-red-300'
        default:
            return 'bg-gray-500/20 text-gray-300'
    }
}

const getEventColor = (severity: string) => {
    switch (severity) {
        case 'info':
            return 'bg-blue-500/20 text-blue-300'
        case 'warning':
            return 'bg-yellow-500/20 text-yellow-300'
        case 'error':
            return 'bg-red-500/20 text-red-300'
        default:
            return 'bg-gray-500/20 text-gray-300'
    }
}

const formatTime = (isoString: string) => {
    try {
        const date = new Date(isoString)
        return date.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        })
    } catch {
        return isoString
    }
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 p-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">Operational Dashboard</h1>
            <p class="mt-2 text-sm text-white/50">Real-time system status & alerts</p>
        </div>

        <!-- Status Grid -->
        <div v-if="operationalMetrics" class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div
                v-for="indicator in operationalMetrics.statusIndicators"
                :key="indicator.label"
                :class="`rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl ${getStatusColor(indicator.status)}`"
            >
                <p class="text-xs font-medium text-white/70">{{ indicator.label }}</p>
                <p class="mt-2 text-2xl font-bold">{{ indicator.value }}</p>
                <div class="mt-2 h-1 w-full rounded-full bg-white/20">
                    <div
                        :class="`h-1 rounded-full ${indicator.status === 'healthy' ? 'bg-green-500 w-full' : indicator.status === 'warning' ? 'bg-yellow-500 w-3/4' : 'bg-red-500 w-1/2'}`"
                    ></div>
                </div>
            </div>
        </div>

        <!-- Alerts Section -->
        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Recent Alerts -->
            <div class="rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                <h2 class="mb-4 flex items-center text-lg font-semibold text-white">
                    <span class="mr-2 inline-block h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                    Recent Alerts
                </h2>
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    <div v-if="recentAlerts.length === 0" class="text-center text-white/50">
                        No alerts
                    </div>
                    <div
                        v-for="alert in recentAlerts"
                        :key="alert.id"
                        :class="`rounded-lg p-3 text-sm ${getEventColor(alert.severity)}`"
                    >
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-semibold">{{ alert.type }}</p>
                                <p class="mt-1 text-xs">{{ alert.message }}</p>
                            </div>
                            <span class="text-xs">{{ formatTime(alert.timestamp) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Events -->
            <div class="rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                <h2 class="mb-4 text-lg font-semibold text-white">Live Events</h2>
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    <div v-if="realtimeEvents.length === 0" class="text-center text-white/50">
                        No recent events
                    </div>
                    <div
                        v-for="event in realtimeEvents.slice(0, 10)"
                        :key="event.id"
                        :class="`rounded-lg border border-white/10 p-3 text-sm ${getEventColor(event.severity)}`"
                    >
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-semibold capitalize">{{ event.type }}</p>
                                <p class="mt-1 text-xs">{{ event.message }}</p>
                            </div>
                            <span class="text-xs text-white/50">{{ formatTime(event.timestamp) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transition Readiness -->
        <div v-if="transitionReadiness" class="rounded-xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
            <h2 class="mb-6 text-lg font-semibold text-white">Transition Readiness</h2>
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Ready Indicator -->
                <div :class="`rounded-lg p-4 ${transitionReadiness.isReady ? 'bg-green-500/20' : 'bg-yellow-500/20'}`">
                    <p class="text-sm font-medium text-white/70">Status</p>
                    <p :class="`mt-2 text-2xl font-bold ${transitionReadiness.isReady ? 'text-green-300' : 'text-yellow-300'}`">
                        {{ transitionReadiness.isReady ? 'READY' : 'NOT READY' }}
                    </p>
                </div>

                <!-- Blockers (if any) -->
                <div class="rounded-lg border border-red-500/20 bg-red-500/10 p-4">
                    <p class="text-sm font-medium text-white/70">
                        Blockers: {{ transitionReadiness.blockers.length }}
                    </p>
                    <div v-if="transitionReadiness.blockers.length === 0" class="mt-2 text-sm text-red-300">
                        No blockers
                    </div>
                    <div v-else class="mt-2 space-y-1">
                        <div
                            v-for="blocker in transitionReadiness.blockers"
                            :key="blocker.metric"
                            class="text-xs text-red-300"
                        >
                            {{ blocker.metric }}: {{ blocker.current }} (need {{ blocker.required }})
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onUnmounted } from 'vue'
</script>
