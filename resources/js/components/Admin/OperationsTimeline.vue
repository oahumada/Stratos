<script setup lang="ts">
import { PhCheckCircle, PhWarning, PhXCircle, PhArrowClockwise } from '@phosphor-icons/vue';
import { computed } from 'vue';

interface Operation {
    id: string;
    name: string;
    status: 'pending' | 'processing' | 'completed' | 'failed' | 'rolled_back';
    progress: number;
    createdAt: string;
    startedAt?: string;
    completedAt?: string;
    rollback?: {
        initiatedAt: string;
        completedAt?: string;
        reason: string;
    };
}

interface Props {
    operations: Operation[];
}

defineProps<Props>();

const formatTime = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const formatDuration = (start: string, end?: string) => {
    const startDate = new Date(start);
    const endDate = end ? new Date(end) : new Date();
    const diff = (endDate.getTime() - startDate.getTime()) / 1000;

    if (diff < 60) return `${Math.round(diff)}s`;
    if (diff < 3600) return `${Math.round(diff / 60)}m`;
    return `${Math.round(diff / 3600)}h`;
};

const statusColors = computed(() => ({
    pending: 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600',
    processing:
        'bg-blue-50 dark:bg-blue-950 border-blue-300 dark:border-blue-600 animate-pulse',
    completed:
        'bg-green-50 dark:bg-green-950 border-green-300 dark:border-green-600',
    failed: 'bg-red-50 dark:bg-red-950 border-red-300 dark:border-red-600',
    rolled_back:
        'bg-amber-50 dark:bg-amber-950 border-amber-300 dark:border-amber-600',
}));

const statusIcons = {
    pending: null,
    processing: 'processing',
    completed: 'completed',
    failed: 'failed',
    rolled_back: 'rolled_back',
};

const getIcon = (status: string) => {
    return statusIcons[status as keyof typeof statusIcons];
};
</script>

<template>
    <div class="space-y-4">
        <div v-for="operation in operations" :key="operation.id" class="relative">
            <!-- Timeline line -->
            <div class="absolute left-6 top-12 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700" />

            <!-- Card -->
            <div
                :class="[
                    'rounded-lg border-l-4 p-4 transition-all',
                    statusColors[operation.status],
                ]"
            >
                <div class="flex items-start gap-3">
                    <!-- Icon -->
                    <div class="mt-1 flex-shrink-0">
                        <PhCheckCircle
                            v-if="getIcon(operation.status) === 'completed'"
                            weight="fill"
                            class="text-green-600 dark:text-green-400"
                            :size="24"
                        />
                        <PhWarning
                            v-else-if="getIcon(operation.status) === 'processing'"
                            weight="fill"
                            class="text-blue-600 dark:text-blue-400 animate-spin"
                            :size="24"
                        />
                        <PhXCircle
                            v-else-if="getIcon(operation.status) === 'failed'"
                            weight="fill"
                            class="text-red-600 dark:text-red-400"
                            :size="24"
                        />
                        <PhArrowClockwise
                            v-else-if="getIcon(operation.status) === 'rolled_back'"
                            weight="fill"
                            class="text-amber-600 dark:text-amber-400 animate-spin"
                            :size="24"
                        />
                        <div v-else class="h-6 w-6 rounded-full bg-gray-300 dark:bg-gray-600" />
                    </div>

                    <!-- Details -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-semibold text-gray-900 dark:text-white truncate">
                                {{ operation.name }}
                            </h3>
                            <span
                                :class="[
                                    'inline-block px-2 py-1 text-xs font-medium rounded whitespace-nowrap',
                                    {
                                        'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300':
                                            operation.status === 'pending',
                                        'bg-blue-200 dark:bg-blue-900 text-blue-700 dark:text-blue-300':
                                            operation.status === 'processing',
                                        'bg-green-200 dark:bg-green-900 text-green-700 dark:text-green-300':
                                            operation.status === 'completed',
                                        'bg-red-200 dark:bg-red-900 text-red-700 dark:text-red-300':
                                            operation.status === 'failed',
                                        'bg-amber-200 dark:bg-amber-900 text-amber-700 dark:text-amber-300':
                                            operation.status === 'rolled_back',
                                    },
                                ]"
                            >
                                {{ operation.status.replace('_', ' ').toUpperCase() }}
                            </span>
                        </div>

                        <!-- Progress bar -->
                        <div v-if="operation.status === 'processing'" class="mt-2 w-full">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs text-gray-600 dark:text-gray-400">Progress</p>
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                    {{ operation.progress }}%
                                </p>
                            </div>
                            <div class="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                <div
                                    class="h-full bg-blue-500 dark:bg-blue-400 transition-all"
                                    :style="{ width: `${operation.progress}%` }"
                                />
                            </div>
                        </div>

                        <!-- Time info -->
                        <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-600 dark:text-gray-400">
                            <span>Created: {{ formatTime(operation.createdAt) }}</span>
                            <span v-if="operation.startedAt">
                                Started: {{ formatTime(operation.startedAt) }}
                            </span>
                            <span v-if="operation.completedAt">
                                Duration: {{ formatDuration(operation.startedAt!, operation.completedAt) }}
                            </span>
                        </div>

                        <!-- Error message -->
                        <div
                            v-if="operation.status === 'failed' && operation.error"
                            class="mt-2 p-2 rounded bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 text-xs"
                        >
                            {{ operation.error }}
                        </div>

                        <!-- Rollback info -->
                        <div
                            v-if="operation.rollback"
                            class="mt-2 p-2 rounded bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 text-xs"
                        >
                            <p class="font-semibold">Rollback initiated</p>
                            <p>Reason: {{ operation.rollback.reason }}</p>
                            <p v-if="operation.rollback.completedAt">
                                Completed: {{ formatTime(operation.rollback.completedAt) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
