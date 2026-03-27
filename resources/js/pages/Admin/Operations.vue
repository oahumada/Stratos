<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    PhActivity,
    PhArrowClockwise,
    PhCheckCircle,
    PhClock,
    PhDatabase,
    PhDownload,
    PhWarning,
} from '@phosphor-icons/vue';
import { computed, onMounted, ref } from 'vue';

defineOptions({
    layout: AppLayout,
});

interface Operation {
    id: string;
    name: string;
    status: 'pending' | 'processing' | 'completed' | 'failed' | 'rolled_back';
    progress: number;
    createdAt: string;
    startedAt?: string;
    completedAt?: string;
    error?: string;
    rollback?: {
        initiatedAt: string;
        completedAt?: string;
        reason: string;
    };
}

interface SystemMetrics {
    uptime: number;
    memoryUsage: number;
    cpuUsage: number;
    queuedJobs: number;
    failedJobs: number;
    totalOperations: number;
    lastCheck: string;
}

const operations = ref<Operation[]>([
    {
        id: '1',
        name: 'Messaging MVP Deployment',
        status: 'completed',
        progress: 100,
        createdAt: new Date(Date.now() - 3600000).toISOString(),
        startedAt: new Date(Date.now() - 3550000).toISOString(),
        completedAt: new Date(Date.now() - 1800000).toISOString(),
    },
    {
        id: '2',
        name: 'Database Optimization',
        status: 'processing',
        progress: 65,
        createdAt: new Date(Date.now() - 1800000).toISOString(),
        startedAt: new Date(Date.now() - 1700000).toISOString(),
    },
    {
        id: '3',
        name: 'Cache Warmup',
        status: 'completed',
        progress: 100,
        createdAt: new Date(Date.now() - 7200000).toISOString(),
        startedAt: new Date(Date.now() - 7100000).toISOString(),
        completedAt: new Date(Date.now() - 5400000).toISOString(),
    },
]);

const metrics = ref<SystemMetrics>({
    uptime: 99.98,
    memoryUsage: 62,
    cpuUsage: 34,
    queuedJobs: 12,
    failedJobs: 0,
    totalOperations: 42,
    lastCheck: new Date().toISOString(),
});

const filterStatus = ref<string>('all');
const autoRefresh = ref(true);

const filteredOperations = computed(() => {
    if (filterStatus.value === 'all') return operations.value;
    return operations.value.filter((op) => op.status === filterStatus.value);
});

const operationStats = computed(() => ({
    completed: operations.value.filter((op) => op.status === 'completed')
        .length,
    processing: operations.value.filter((op) => op.status === 'processing')
        .length,
    failed: operations.value.filter((op) => op.status === 'failed').length,
    rolledBack: operations.value.filter((op) => op.status === 'rolled_back')
        .length,
}));

const statusColor = (status: string) => {
    const colors: Record<string, string> = {
        completed: 'text-emerald-400 bg-emerald-500/10',
        processing: 'text-blue-400 bg-blue-500/10',
        failed: 'text-red-400 bg-red-500/10',
        pending: 'text-slate-400 bg-slate-500/10',
        rolled_back: 'text-amber-400 bg-amber-500/10',
    };
    return colors[status] || colors.pending;
};

const statusIcon = (status: string) => {
    const icons: Record<string, any> = {
        completed: PhCheckCircle,
        processing: PhArrowClockwise,
        failed: PhWarning,
        pending: PhClock,
        rolled_back: PhDatabase,
    };
    return icons[status] || PhClock;
};

function initiateRollback(operationId: string, reason: string) {
    const operation = operations.value.find((op) => op.id === operationId);
    if (operation) {
        operation.status = 'rolled_back';
        operation.rollback = {
            initiatedAt: new Date().toISOString(),
            reason: reason,
        };
    }
}

async function refreshMetrics() {
    // Simular fetch de métricas reales del backend
    // En producción: await fetch('/api/admin/operations/metrics')
    metrics.value.lastCheck = new Date().toISOString();
}

onMounted(() => {
    if (autoRefresh.value) {
        const interval = setInterval(refreshMetrics, 15000); // Cada 15 segundos
        return () => clearInterval(interval);
    }
});
</script>

<template>
    <div>
        <Head title="Admin Operations Dashboard" />

        <div
            class="min-h-screen space-y-6 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 p-6"
        >
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1
                        class="flex items-center gap-3 text-3xl font-bold text-white"
                    >
                        <PhDatabase :size="32" class="text-indigo-400" />
                        Operations Dashboard
                    </h1>
                    <p class="mt-2 text-sm text-slate-400">
                        Real-time system monitoring & operation tracking
                    </p>
                </div>
                <button
                    @click="refreshMetrics"
                    type="button"
                    class="flex items-center gap-2 rounded-lg bg-indigo-500 px-4 py-2 font-bold text-white transition-colors hover:bg-indigo-600"
                >
                    <PhArrowClockwise :size="18" />
                    Refresh
                </button>
            </div>

            <!-- System Metrics Grid -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Uptime -->
                <div
                    class="rounded-lg border border-white/10 bg-white/5 p-4 transition-all hover:border-emerald-500/50"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <p class="text-sm text-slate-400">System Uptime</p>
                        <PhActivity :size="20" class="text-emerald-400" />
                    </div>
                    <p class="text-3xl font-bold text-white">
                        {{ metrics.uptime }}%
                    </p>
                    <div
                        class="mt-3 h-2 overflow-hidden rounded-full bg-white/5"
                    >
                        <div
                            class="h-full bg-gradient-to-r from-emerald-500 to-green-500"
                            :style="{ width: `${metrics.uptime}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Memory Usage -->
                <div
                    class="rounded-lg border border-white/10 bg-white/5 p-4 transition-all hover:border-blue-500/50"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <p class="text-sm text-slate-400">Memory Usage</p>
                        <PhDatabase :size="20" class="text-blue-400" />
                    </div>
                    <p class="text-3xl font-bold text-white">
                        {{ metrics.memoryUsage }}%
                    </p>
                    <div
                        class="mt-3 h-2 overflow-hidden rounded-full bg-white/5"
                    >
                        <div
                            class="h-full bg-gradient-to-r from-blue-500 to-cyan-500"
                            :style="{ width: `${metrics.memoryUsage}%` }"
                        ></div>
                    </div>
                </div>

                <!-- CPU Usage -->
                <div
                    class="rounded-lg border border-white/10 bg-white/5 p-4 transition-all hover:border-indigo-500/50"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <p class="text-sm text-slate-400">CPU Usage</p>
                        <PhActivity :size="20" class="text-indigo-400" />
                    </div>
                    <p class="text-3xl font-bold text-white">
                        {{ metrics.cpuUsage }}%
                    </p>
                    <div
                        class="mt-3 h-2 overflow-hidden rounded-full bg-white/5"
                    >
                        <div
                            class="h-full bg-gradient-to-r from-indigo-500 to-purple-500"
                            :style="{ width: `${metrics.cpuUsage}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Queue Status -->
                <div
                    class="rounded-lg border border-white/10 bg-white/5 p-4 transition-all hover:border-amber-500/50"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <p class="text-sm text-slate-400">Queue Status</p>
                        <PhClock :size="20" class="text-amber-400" />
                    </div>
                    <p class="text-3xl font-bold text-white">
                        {{ metrics.queuedJobs }}
                    </p>
                    <p class="mt-3 text-xs text-slate-400">
                        <span v-if="metrics.failedJobs > 0" class="text-red-400"
                            >{{ metrics.failedJobs }} failed</span
                        >
                        <span v-else class="text-emerald-400">No failures</span>
                    </p>
                </div>
            </div>

            <!-- Operations Stats -->
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <div
                    class="rounded-lg border border-emerald-500/20 bg-emerald-500/10 p-4"
                >
                    <p class="text-xs font-semibold text-emerald-300">
                        Completed
                    </p>
                    <p class="mt-1 text-2xl font-bold text-emerald-400">
                        {{ operationStats.completed }}
                    </p>
                </div>
                <div
                    class="rounded-lg border border-blue-500/20 bg-blue-500/10 p-4"
                >
                    <p class="text-xs font-semibold text-blue-300">
                        Processing
                    </p>
                    <p class="mt-1 text-2xl font-bold text-blue-400">
                        {{ operationStats.processing }}
                    </p>
                </div>
                <div
                    class="rounded-lg border border-red-500/20 bg-red-500/10 p-4"
                >
                    <p class="text-xs font-semibold text-red-300">Failed</p>
                    <p class="mt-1 text-2xl font-bold text-red-400">
                        {{ operationStats.failed }}
                    </p>
                </div>
                <div
                    class="rounded-lg border border-amber-500/20 bg-amber-500/10 p-4"
                >
                    <p class="text-xs font-semibold text-amber-300">
                        Rolled Back
                    </p>
                    <p class="mt-1 text-2xl font-bold text-amber-400">
                        {{ operationStats.rolledBack }}
                    </p>
                </div>
            </div>

            <!-- Operations List -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2
                        class="flex items-center gap-2 text-xl font-bold text-white"
                    >
                        <PhArrowClockwise :size="24" class="text-indigo-400" />
                        Recent Operations
                    </h2>
                    <div class="flex gap-2">
                        <button
                            v-for="status in [
                                'all',
                                'completed',
                                'processing',
                                'failed',
                            ]"
                            :key="status"
                            @click="filterStatus = status"
                            type="button"
                            :class="{
                                'bg-indigo-500 text-white':
                                    filterStatus === status,
                                'bg-white/5 text-slate-300 hover:bg-white/10':
                                    filterStatus !== status,
                            }"
                            class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors"
                        >
                            {{
                                status.charAt(0).toUpperCase() + status.slice(1)
                            }}
                        </button>
                    </div>
                </div>

                <!-- Operations Table -->
                <div class="space-y-3">
                    <div
                        v-for="operation in filteredOperations"
                        :key="operation.id"
                        class="rounded-lg border border-white/10 bg-white/5 p-4 transition-all hover:border-indigo-500/50"
                    >
                        <div class="mb-3 flex items-start justify-between">
                            <div class="flex flex-1 items-center gap-3">
                                <component
                                    :is="statusIcon(operation.status)"
                                    :size="24"
                                    class="text-indigo-400"
                                />
                                <div>
                                    <p class="font-bold text-white">
                                        {{ operation.name }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        Started
                                        {{
                                            operation.createdAt
                                                ? new Date(
                                                      operation.createdAt,
                                                  ).toLocaleString()
                                                : 'N/A'
                                        }}
                                        ago
                                    </p>
                                </div>
                            </div>
                            <span
                                :class="statusColor(operation.status)"
                                class="rounded-full px-3 py-1 text-xs font-bold"
                            >
                                {{
                                    operation.status
                                        .replace('_', ' ')
                                        .toUpperCase()
                                }}
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-3">
                            <div class="mb-1 flex items-center justify-between">
                                <span class="text-xs text-slate-400"
                                    >Progress</span
                                >
                                <span class="text-xs font-bold text-slate-300"
                                    >{{ operation.progress }}%</span
                                >
                            </div>
                            <div
                                class="h-2 overflow-hidden rounded-full bg-white/5"
                            >
                                <div
                                    class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-300"
                                    :style="{ width: `${operation.progress}%` }"
                                ></div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div
                            class="mb-3 flex items-center gap-4 text-xs text-slate-400"
                        >
                            <span v-if="operation.startedAt">
                                Started:
                                {{
                                    new Date(
                                        operation.startedAt,
                                    ).toLocaleString()
                                }}
                            </span>
                            <span v-if="operation.completedAt">
                                Completed:
                                {{
                                    new Date(
                                        operation.completedAt,
                                    ).toLocaleString()
                                }}
                            </span>
                        </div>

                        <!-- Error Message -->
                        <div
                            v-if="operation.error"
                            class="mb-3 rounded-lg border border-red-500/30 bg-red-500/10 p-3"
                        >
                            <p class="text-xs font-semibold text-red-300">
                                Error
                            </p>
                            <p class="mt-1 text-xs text-red-200">
                                {{ operation.error }}
                            </p>
                        </div>

                        <!-- Rollback Info -->
                        <div
                            v-if="operation.rollback"
                            class="mb-3 rounded-lg border border-amber-500/30 bg-amber-500/10 p-3"
                        >
                            <p class="text-xs font-semibold text-amber-300">
                                Rolled Back
                            </p>
                            <p class="mt-1 text-xs text-amber-200">
                                Reason: {{ operation.rollback.reason }}
                            </p>
                            <p class="mt-1 text-xs text-amber-300">
                                Initiated:
                                {{
                                    new Date(
                                        operation.rollback.initiatedAt,
                                    ).toLocaleString()
                                }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <button
                                type="button"
                                class="rounded-lg bg-white/5 px-3 py-1.5 text-xs font-bold text-slate-300 transition-colors hover:bg-white/10"
                            >
                                View Details
                            </button>
                            <button
                                v-if="operation.status === 'processing'"
                                @click="
                                    initiateRollback(
                                        operation.id,
                                        'User initiated',
                                    )
                                "
                                type="button"
                                class="rounded-lg bg-red-500/10 px-3 py-1.5 text-xs font-bold text-red-300 transition-colors hover:bg-red-500/20"
                            >
                                Rollback
                            </button>
                            <button
                                type="button"
                                class="rounded-lg bg-white/5 px-3 py-1.5 text-xs font-bold text-slate-300 transition-colors hover:bg-white/10"
                            >
                                <PhDownload :size="14" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="filteredOperations.length === 0"
                    class="rounded-lg border border-white/10 bg-white/5 px-4 py-12 text-center"
                >
                    <p class="text-slate-400">
                        No operations found with this filter
                    </p>
                </div>
            </div>

            <!-- Last Update Info -->
            <div
                class="rounded-lg border border-white/10 bg-white/5 p-4 text-center"
            >
                <p class="text-xs text-slate-400">
                    Last update:
                    {{ new Date(metrics.lastCheck).toLocaleString() }}
                    <span v-if="autoRefresh" class="ml-2 text-indigo-400"
                        >• Auto-refresh enabled</span
                    >
                </p>
            </div>
        </div>
    </div>
</template>
