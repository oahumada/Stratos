<script setup lang="ts">
import type { RealtimeConnection } from '@/composables/useVerificationDashboardRealtime';
import { computed } from 'vue';

interface Props {
    status: RealtimeConnection;
    compact?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    compact: false,
});

const statusColor = computed(() => {
    if (!props.status.connected)
        return 'bg-red-500/20 text-red-300 border-red-500/30';
    if (props.status.type === 'websocket')
        return 'bg-green-500/20 text-green-300 border-green-500/30';
    if (props.status.type === 'sse')
        return 'bg-blue-500/20 text-blue-300 border-blue-500/30';
    return 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30';
});

const statusIcon = computed(() => {
    if (!props.status.connected) return '❌';
    if (props.status.type === 'websocket') return '⚡';
    if (props.status.type === 'sse') return '📡';
    return '🔄';
});

const statusText = computed(() => {
    if (!props.status.connected) return 'Offline';
    if (props.status.type === 'websocket') return 'WebSocket';
    if (props.status.type === 'sse') return 'Server-Sent Events';
    return 'Polling';
});

const lastHeartbeatText = computed(() => {
    if (!props.status.lastHeartbeat) return 'Never';
    const seconds = Math.floor(
        (Date.now() - props.status.lastHeartbeat.getTime()) / 1000,
    );
    if (seconds < 60) return `${seconds}s ago`;
    if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`;
    return `${Math.floor(seconds / 3600)}h ago`;
});
</script>

<template>
    <div
        v-if="compact"
        :class="`rounded-lg border px-2 py-1 text-xs font-medium ${statusColor}`"
    >
        <span>{{ statusIcon }} {{ statusText }}</span>
    </div>
    <div v-else :class="`rounded-lg border p-3 ${statusColor}`">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xl">{{ statusIcon }}</span>
                <div>
                    <p class="font-semibold">{{ statusText }}</p>
                    <p class="text-xs opacity-75">
                        {{
                            status.connected
                                ? `Connected • Last: ${lastHeartbeatText}`
                                : 'Disconnected'
                        }}
                    </p>
                </div>
            </div>
            <div
                v-if="status.connected"
                class="h-2 w-2 animate-pulse rounded-full bg-current"
            ></div>
        </div>
        <p v-if="status.error" class="mt-2 text-xs">{{ status.error }}</p>
    </div>
</template>
