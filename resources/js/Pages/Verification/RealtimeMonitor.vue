<script setup lang="ts">
import { useVerificationDashboard } from '@/composables/useVerificationDashboard'
import { computed, onMounted, onUnmounted, ref } from 'vue'

defineOptions({
    name: 'RealtimeMonitor',
})

const {
    metrics,
    realtimeEvents,
    isLoading,
    subscribeToRealtimeEvents,
    fetchMetrics,
} = useVerificationDashboard()

const autoScroll = ref(true)
let unsubscribe: (() => void) | null = null

onMounted(() => {
    fetchMetrics()
    unsubscribe = subscribeToRealtimeEvents()
})

onUnmounted(() => {
    unsubscribe?.()
})

const eventStats = computed(() => {
    const stats = {
        total: realtimeEvents.value.length,
        transitions: realtimeEvents.value.filter(e => e.type === 'transition').length,
        alerts: realtimeEvents.value.filter(e => e.type === 'alert').length,
        config: realtimeEvents.value.filter(e => e.type === 'config').length,
        notifications: realtimeEvents.value.filter(e => e.type === 'notification').length,
    }
    return stats
})

const getEventIcon = (type: string) => {
    switch (type) {
        case 'transition':
            return '🔄'
        case 'alert':
            return '⚠️'
        case 'config':
            return '⚙️'
        case 'notification':
            return '📢'
        default:
            return '📋'
    }
}

const getSeverityColor = (severity: string) => {
    switch (severity) {
        case 'info':
            return 'bg-blue-500/20 text-blue-300 border-blue-500/30'
        case 'warning':
            return 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30'
        case 'error':
            return 'bg-red-500/20 text-red-300 border-red-500/30'
        default:
            return 'bg-gray-500/20 text-gray-300 border-gray-500/30'
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

const getRelativeTime = (isoString: string) => {
    try {
        const date = new Date(isoString)
        const now = new Date()
        const diffMs = now.getTime() - date.getTime()
        const diffSecs = Math.floor(diffMs / 1000)

        if (diffSecs < 60) return `${diffSecs}s ago`
        if (diffSecs < 3600) return `${Math.floor(diffSecs / 60)}m ago`
        if (diffSecs < 86400) return `${Math.floor(diffSecs / 3600)}h ago`
        return `${Math.floor(diffSecs / 86400)}d ago`
    } catch {
        return 'just now'
    }
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 p-8">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Real-time Monitor</h1>
                <p class="mt-2 text-sm text-white/50">Live event stream & system activity</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2 rounded-lg bg-white/10 p-2">
                    <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="text-sm font-medium text-white">LIVE</span>
                </div>
                <label class="ml-4 flex items-center gap-2 text-sm text-white/70">
                    <input v-model="autoScroll" type="checkbox" class="rounded" />
                    Auto scroll
                </label>
            </div>
        </div>

        <!-- Event Statistics -->
        <div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-5">
            <div class="rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur-xl text-center">
                <p class="text-2xl font-bold text-white">{{ eventStats.total }}</p>
                <p class="mt-1 text-xs text-white/70">Total Events</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur-xl text-center">
                <p class="text-2xl font-bold text-indigo-400">{{ eventStats.transitions }}</p>
                <p class="mt-1 text-xs text-white/70">Transitions</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur-xl text-center">
                <p class="text-2xl font-bold text-red-400">{{ eventStats.alerts }}</p>
                <p class="mt-1 text-xs text-white/70">Alerts</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur-xl text-center">
                <p class="text-2xl font-bold text-yellow-400">{{ eventStats.config }}</p>
                <p class="mt-1 text-xs text-white/70">Config Changes</p>
            </div>
            <div class="rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur-xl text-center">
                <p class="text-2xl font-bold text-green-400">{{ eventStats.notifications }}</p>
                <p class="mt-1 text-xs text-white/70">Notifications</p>
            </div>
        </div>

        <!-- Event Stream -->
        <div class="rounded-xl border border-white/10 bg-white/5 backdrop-blur-xl overflow-hidden">
            <!-- Header -->
            <div class="border-b border-white/10 bg-white/10 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Event Stream</h2>
            </div>

            <!-- Events List -->
            <div class="max-h-[calc(100vh-400px)] overflow-y-auto space-y-2 p-6">
                <div v-if="realtimeEvents.length === 0" class="py-12 text-center text-white/50">
                    <p class="text-sm">Waiting for events...</p>
                </div>

                <!-- Events -->
                <div
                    v-for="event in realtimeEvents.slice(0, 100)"
                    :key="event.id"
                    :class="`rounded-lg border p-4 transition ${getSeverityColor(event.severity)}`"
                >
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="mt-1 text-2xl">
                            {{ getEventIcon(event.type) }}
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="font-semibold capitalize">
                                        {{ event.type }}
                                    </h3>
                                    <p class="mt-1 text-sm break-words">{{ event.message }}</p>

                                    <!-- Event Details -->
                                    <div v-if="event.data" class="mt-2 space-y-1">
                                        <div
                                            v-for="(value, key) in event.data"
                                            :key="key"
                                            class="text-xs text-white/60"
                                        >
                                            <span class="font-semibold capitalize">{{ key }}:</span>
                                            {{ value }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Time -->
                                <div class="text-right whitespace-nowrap">
                                    <p class="text-xs font-medium">
                                        {{ formatTime(event.timestamp) }}
                                    </p>
                                    <p class="mt-1 text-xs text-white/60">
                                        {{ getRelativeTime(event.timestamp) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Metrics Footer -->
        <div v-if="metrics" class="mt-6 rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur-xl">
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="text-sm">
                    <p class="text-white/70">Current Phase</p>
                    <p class="mt-1 font-semibold text-white">{{ metrics.currentPhase }}</p>
                </div>
                <div class="text-sm">
                    <p class="text-white/70">Confidence</p>
                    <p class="mt-1 font-semibold text-white">{{ metrics.confidenceScore }}%</p>
                </div>
                <div class="text-sm">
                    <p class="text-white/70">Error Rate</p>
                    <p class="mt-1 font-semibold text-white">{{ metrics.errorRate }}%</p>
                </div>
                <div class="text-sm">
                    <p class="text-white/70">Sample Size</p>
                    <p class="mt-1 font-semibold text-white">{{ metrics.sampleSize }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
