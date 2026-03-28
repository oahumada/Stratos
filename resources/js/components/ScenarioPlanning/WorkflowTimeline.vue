<template>
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Workflow Timeline
            </h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Current stage and historical progression
            </p>
        </div>

        <!-- Current Status Badge -->
        <div
            class="flex items-center justify-between rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-700 dark:bg-blue-900/20"
        >
            <div>
                <p class="text-sm font-medium text-blue-600 dark:text-blue-300">
                    Current Status
                </p>
                <p
                    class="mt-1 text-lg font-bold text-blue-900 dark:text-blue-100"
                >
                    {{ formatStatus(currentStatus) }}
                </p>
            </div>
            <div class="text-3xl text-blue-400">
                {{ getStatusIcon(currentStatus) }}
            </div>
        </div>

        <!-- Timeline Visual -->
        <div class="relative">
            <!-- Timeline Container -->
            <div class="space-y-4">
                <div
                    v-for="(stage, index) in stages"
                    :key="index"
                    class="flex gap-4"
                >
                    <!-- Timeline Marker -->
                    <div class="flex flex-col items-center">
                        <!-- Circle -->
                        <div
                            :class="[
                                'flex h-10 w-10 items-center justify-center rounded-full font-bold text-white transition-all',
                                isStageComplete(stage.status)
                                    ? 'bg-green-500 ring-4 ring-green-200 dark:ring-green-900'
                                    : isStageActive(stage.status)
                                      ? 'animate-pulse bg-blue-500 ring-4 ring-blue-200 dark:ring-blue-900'
                                      : 'bg-gray-300 dark:bg-gray-700',
                            ]"
                        >
                            {{ index + 1 }}
                        </div>

                        <!-- Connecting Line -->
                        <div
                            v-if="index < stages.length - 1"
                            :class="[
                                'mt-2 mb-2 w-1 transition-all',
                                isStageComplete(stage.status)
                                    ? 'h-16 bg-green-500'
                                    : 'h-16 bg-gray-300 dark:bg-gray-600',
                            ]"
                        />
                    </div>

                    <!-- Stage Content -->
                    <div class="flex-1 pt-1 pb-4">
                        <div
                            :class="[
                                'rounded-lg border p-4 transition-all',
                                isStageActive(stage.status)
                                    ? 'border-blue-300 bg-blue-50 dark:border-blue-700 dark:bg-blue-900/20'
                                    : isStageComplete(stage.status)
                                      ? 'border-green-300 bg-green-50 dark:border-green-700 dark:bg-green-900/20'
                                      : 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800',
                            ]"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4
                                        :class="[
                                            'mb-1 font-semibold',
                                            isStageActive(stage.status)
                                                ? 'text-blue-900 dark:text-blue-100'
                                                : isStageComplete(stage.status)
                                                  ? 'text-green-900 dark:text-green-100'
                                                  : 'text-gray-900 dark:text-gray-100',
                                        ]"
                                    >
                                        {{ stage.name }}
                                    </h4>
                                    <p
                                        :class="[
                                            'text-sm',
                                            isStageActive(stage.status)
                                                ? 'text-blue-700 dark:text-blue-300'
                                                : isStageComplete(stage.status)
                                                  ? 'text-green-700 dark:text-green-300'
                                                  : 'text-gray-600 dark:text-gray-400',
                                        ]"
                                    >
                                        {{ stage.description }}
                                    </p>
                                </div>

                                <!-- Status Badge -->
                                <span
                                    :class="[
                                        'ml-2 rounded-full px-3 py-1 text-xs font-medium whitespace-nowrap text-white',
                                        isStageComplete(stage.status)
                                            ? 'bg-green-500'
                                            : isStageActive(stage.status)
                                              ? 'bg-blue-500'
                                              : 'bg-gray-400',
                                    ]"
                                >
                                    {{ stage.status }}
                                </span>
                            </div>

                            <!-- Stage Details -->
                            <div
                                v-if="stage.timestamp"
                                class="border-opacity-10 mt-3 border-t border-current pt-3"
                            >
                                <p
                                    class="text-xs text-gray-600 dark:text-gray-400"
                                >
                                    {{ formatDateTime(stage.timestamp) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div
            class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/30"
        >
            <h4 class="mb-3 font-semibold text-gray-900 dark:text-white">
                Legend
            </h4>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="flex items-center gap-3">
                    <div class="h-6 w-6 rounded-full bg-green-500"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300"
                        >Completed</span
                    >
                </div>
                <div class="flex items-center gap-3">
                    <div
                        class="h-6 w-6 animate-pulse rounded-full bg-blue-500"
                    ></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300"
                        >Current Stage</span
                    >
                </div>
                <div class="flex items-center gap-3">
                    <div class="h-6 w-6 rounded-full bg-gray-400"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300"
                        >Pending</span
                    >
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Stage {
    name: string;
    description: string;
    status: 'completed' | 'active' | 'pending';
    timestamp?: string;
}

const props = defineProps<{
    decisionStatus: string;
    submittedAt?: string;
    approvedAt?: string;
}>();

const stages = computed<Stage[]>(() => {
    const baseStages: Stage[] = [
        {
            name: 'Draft',
            description: 'Scenario created and ready for submission',
            status: props.decisionStatus === 'draft' ? 'active' : 'completed',
        },
        {
            name: 'Pending Approval',
            description: 'Waiting for stakeholder approval',
            status:
                props.decisionStatus === 'pending_approval'
                    ? 'active'
                    : ['approved', 'active', 'archived'].includes(
                            props.decisionStatus,
                        )
                      ? 'completed'
                      : 'pending',
            timestamp: props.submittedAt,
        },
        {
            name: 'Approved',
            description: 'All stakeholders have approved',
            status:
                props.decisionStatus === 'approved'
                    ? 'active'
                    : ['active', 'archived'].includes(props.decisionStatus)
                      ? 'completed'
                      : 'pending',
            timestamp: props.approvedAt,
        },
        {
            name: 'Active',
            description: 'Ready for execution',
            status:
                props.decisionStatus === 'active'
                    ? 'active'
                    : props.decisionStatus === 'archived'
                      ? 'completed'
                      : 'pending',
        },
    ];

    return baseStages;
});

const currentStatus = computed(() => {
    return props.decisionStatus || 'draft';
});

const isStageComplete = (status: string): boolean => {
    return status === 'completed';
};

const isStageActive = (status: string): boolean => {
    return status === 'active';
};

const getStatusIcon = (status: string): string => {
    const icons: Record<string, string> = {
        draft: '✏️',
        pending_approval: '⏳',
        approved: '✅',
        active: '🚀',
        archived: '📦',
        rejected: '❌',
    };
    return icons[status] || '📋';
};

const formatStatus = (status: string): string => {
    return status
        .split('_')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const formatDateTime = (date: string | undefined): string => {
    if (!date) return 'Unknown date';
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<style scoped>
@keyframes pulse-ring {
    0%,
    100% {
        box-shadow: 0 0 0 0 currentColor;
    }
    50% {
        box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
    }
}

.animate-pulse-ring {
    animation: pulse-ring 2s infinite;
}
</style>
