<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white dark:text-gray-100">
                    Approval Dashboard
                </h2>
                <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">
                    Monitor approval requests and workflow status
                </p>
            </div>
            <button
                @click="refreshData"
                class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700"
            >
                <IconRefresh class="h-4 w-4" />
                Refresh
            </button>
        </div>

        <!-- Metrics Cards Row -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Pending Approvals Card -->
            <div
                class="rounded-lg border border-amber-500/20 bg-gradient-to-br from-amber-500/10 to-amber-600/5 p-4 transition-colors hover:border-amber-500/40"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p
                            class="text-sm font-medium text-amber-600 dark:text-amber-400"
                        >
                            Pending
                        </p>
                        <p
                            class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-300"
                        >
                            {{ metrics.pending }}
                        </p>
                    </div>
                    <IconClock class="h-8 w-8 text-amber-500/30" />
                </div>
                <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                    Awaiting your decision
                </p>
            </div>

            <!-- Approved Card -->
            <div
                class="rounded-lg border border-green-500/20 bg-gradient-to-br from-green-500/10 to-green-600/5 p-4 transition-colors hover:border-green-500/40"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p
                            class="text-sm font-medium text-green-600 dark:text-green-400"
                        >
                            Approved
                        </p>
                        <p
                            class="mt-2 text-3xl font-bold text-green-600 dark:text-green-300"
                        >
                            {{ metrics.approved }}
                        </p>
                    </div>
                    <IconCheck class="h-8 w-8 text-green-500/30" />
                </div>
                <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                    This month
                </p>
            </div>

            <!-- Rejected Card -->
            <div
                class="rounded-lg border border-red-500/20 bg-gradient-to-br from-red-500/10 to-red-600/5 p-4 transition-colors hover:border-red-500/40"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p
                            class="text-sm font-medium text-red-600 dark:text-red-400"
                        >
                            Rejected
                        </p>
                        <p
                            class="mt-2 text-3xl font-bold text-red-600 dark:text-red-300"
                        >
                            {{ metrics.rejected }}
                        </p>
                    </div>
                    <IconX class="h-8 w-8 text-red-500/30" />
                </div>
                <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                    This month
                </p>
            </div>

            <!-- Approval Rate Card -->
            <div
                class="rounded-lg border border-blue-500/20 bg-gradient-to-br from-blue-500/10 to-blue-600/5 p-4 transition-colors hover:border-blue-500/40"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p
                            class="text-sm font-medium text-blue-600 dark:text-blue-400"
                        >
                            Rate
                        </p>
                        <p
                            class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-300"
                        >
                            {{ metrics.approval_rate }}
                        </p>
                    </div>
                    <IconTrendingUp class="h-8 w-8 text-blue-500/30" />
                </div>
                <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                    Success percentage
                </p>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Pending Approvals List -->
            <div
                class="rounded-lg border border-gray-700 bg-gray-800/50 p-6 lg:col-span-2 dark:border-gray-800 dark:bg-gray-900/50"
            >
                <div class="mb-4 flex items-center justify-between">
                    <h3
                        class="text-lg font-semibold text-white dark:text-gray-100"
                    >
                        Pending Approvals
                    </h3>
                    <span
                        class="rounded bg-amber-500/20 px-2 py-1 text-xs text-amber-400"
                        >{{ pendingApprovals.length }}</span
                    >
                </div>

                <!-- Pending Approvals Table -->
                <div v-if="pendingApprovals.length > 0" class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="border-b border-gray-700 dark:border-gray-800"
                            >
                                <th
                                    class="px-4 py-3 text-left font-medium text-gray-400 dark:text-gray-500"
                                >
                                    Scenario
                                </th>
                                <th
                                    class="px-4 py-3 text-left font-medium text-gray-400 dark:text-gray-500"
                                >
                                    Submitter
                                </th>
                                <th
                                    class="px-4 py-3 text-left font-medium text-gray-400 dark:text-gray-500"
                                >
                                    Days Pending
                                </th>
                                <th
                                    class="px-4 py-3 text-left font-medium text-gray-400 dark:text-gray-500"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="approval in pendingApprovals"
                                :key="approval.id"
                                class="border-b border-gray-700/50 transition-colors hover:bg-gray-700/30 dark:border-gray-800/50"
                            >
                                <td class="px-4 py-3">
                                    <a
                                        :href="`/scenarios/${approval.scenario_id}`"
                                        class="font-medium text-blue-400 hover:text-blue-300"
                                    >
                                        {{ approval.scenario_name }}
                                    </a>
                                </td>
                                <td
                                    class="px-4 py-3 text-gray-300 dark:text-gray-400"
                                >
                                    {{ approval.submitter }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        :class="{
                                            'text-yellow-400':
                                                approval.days_pending < 3,
                                            'text-orange-400':
                                                approval.days_pending >= 3 &&
                                                approval.days_pending < 7,
                                            'text-red-400':
                                                approval.days_pending >= 7,
                                        }"
                                    >
                                        {{ approval.days_pending }} days
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <a
                                            :href="`/scenarios/${approval.scenario_id}`"
                                            class="rounded bg-blue-500/10 px-2 py-1 text-xs text-blue-400 transition-colors hover:bg-blue-500/20 hover:text-blue-300"
                                        >
                                            Review
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-else class="py-12 text-center">
                    <IconCheckCircle
                        class="mx-auto mb-3 h-12 w-12 text-green-500/30"
                    />
                    <p class="font-medium text-gray-400 dark:text-gray-500">
                        No pending approvals
                    </p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-600">
                        You're all caught up!
                    </p>
                </div>
            </div>

            <!-- Approval Metrics Sidebar -->
            <div
                class="rounded-lg border border-gray-700 bg-gray-800/50 p-6 dark:border-gray-800 dark:bg-gray-900/50"
            >
                <h3
                    class="mb-4 text-lg font-semibold text-white dark:text-gray-100"
                >
                    Response Time
                </h3>

                <div class="space-y-4">
                    <!-- Average Response Time -->
                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <p
                                class="text-sm font-medium text-gray-300 dark:text-gray-400"
                            >
                                Average Response
                            </p>
                            <p class="text-lg font-bold text-blue-400">
                                {{ metrics.average_response_days }}
                            </p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-600">
                            days to respond
                        </p>
                    </div>

                    <div class="h-px bg-gray-700 dark:bg-gray-800"></div>

                    <!-- Approval Stats -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-400 dark:text-gray-500">
                                This Month
                            </p>
                            <p class="text-sm font-semibold text-green-400">
                                {{ metrics.approved + metrics.rejected }}
                            </p>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-400 dark:text-gray-500">
                                Still Pending
                            </p>
                            <p class="text-sm font-semibold text-amber-400">
                                {{ metrics.pending }}
                            </p>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-400 dark:text-gray-500">
                                Success Rate
                            </p>
                            <p class="text-sm font-semibold text-green-400">
                                {{ metrics.approval_rate }}
                            </p>
                        </div>
                    </div>

                    <div class="h-px bg-gray-700 dark:bg-gray-800"></div>

                    <!-- Quick Actions -->
                    <div class="pt-2">
                        <p
                            class="mb-3 text-xs font-medium text-gray-500 dark:text-gray-600"
                        >
                            Quick Actions
                        </p>
                        <div class="space-y-2">
                            <button
                                class="flex w-full items-center gap-2 rounded bg-blue-500/10 px-3 py-2 text-left text-sm text-blue-400 transition-colors hover:bg-blue-500/20"
                            >
                                <IconDownload class="h-4 w-4" />
                                Export CSV
                            </button>
                            <button
                                class="flex w-full items-center gap-2 rounded bg-green-500/10 px-3 py-2 text-left text-sm text-green-400 transition-colors hover:bg-green-500/20"
                            >
                                <IconRefresh class="h-4 w-4" />
                                Refresh Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Chain Visualization -->
        <div
            class="rounded-lg border border-gray-700 bg-gray-800/50 p-6 dark:border-gray-800 dark:bg-gray-900/50"
        >
            <h3
                class="mb-4 text-lg font-semibold text-white dark:text-gray-100"
            >
                Approval Status Overview
            </h3>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Approval Status Breakdown -->
                <div class="space-y-3">
                    <div>
                        <div class="mb-1 flex items-center justify-between">
                            <p class="text-sm text-gray-300 dark:text-gray-400">
                                Pending
                            </p>
                            <span
                                class="text-xs font-semibold text-amber-400"
                                >{{ metrics.pending }}</span
                            >
                        </div>
                        <div
                            class="h-2 w-full rounded-full bg-gray-700 dark:bg-gray-800"
                        >
                            <div
                                class="h-2 rounded-full bg-amber-500 transition-all duration-500"
                                :style="{
                                    width: getPercentage('pending') + '%',
                                }"
                            ></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-1 flex items-center justify-between">
                            <p class="text-sm text-gray-300 dark:text-gray-400">
                                Approved
                            </p>
                            <span
                                class="text-xs font-semibold text-green-400"
                                >{{ metrics.approved }}</span
                            >
                        </div>
                        <div
                            class="h-2 w-full rounded-full bg-gray-700 dark:bg-gray-800"
                        >
                            <div
                                class="h-2 rounded-full bg-green-500 transition-all duration-500"
                                :style="{
                                    width: getPercentage('approved') + '%',
                                }"
                            ></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-1 flex items-center justify-between">
                            <p class="text-sm text-gray-300 dark:text-gray-400">
                                Rejected
                            </p>
                            <span class="text-xs font-semibold text-red-400">{{
                                metrics.rejected
                            }}</span>
                        </div>
                        <div
                            class="h-2 w-full rounded-full bg-gray-700 dark:bg-gray-800"
                        >
                            <div
                                class="h-2 rounded-full bg-red-500 transition-all duration-500"
                                :style="{
                                    width: getPercentage('rejected') + '%',
                                }"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Legend & Info -->
                <div class="flex flex-col justify-center space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 rounded-full bg-amber-500"></div>
                        <div>
                            <p
                                class="text-sm font-medium text-gray-300 dark:text-gray-400"
                            >
                                Awaiting Action
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-600">
                                Requires your review
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 rounded-full bg-green-500"></div>
                        <div>
                            <p
                                class="text-sm font-medium text-gray-300 dark:text-gray-400"
                            >
                                Approved
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-600">
                                Processed successfully
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 rounded-full bg-red-500"></div>
                        <div>
                            <p
                                class="text-sm font-medium text-gray-300 dark:text-gray-400"
                            >
                                Rejected
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-600">
                                Returned for revision
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {
    IconCheck,
    IconCheckCircle,
    IconClock,
    IconDownload,
    IconRefresh,
    IconTrendingUp,
    IconX,
} from '@tabler/icons-vue';
import { onMounted, ref } from 'vue';

interface Metrics {
    pending: number;
    approved: number;
    rejected: number;
    approval_rate: string;
    average_response_days: number;
}

interface PendingApproval {
    id: number;
    scenario_id: number;
    scenario_name: string;
    submitter: string;
    status: string;
    created_at: string;
    days_pending: number;
}

const metrics = ref<Metrics>({
    pending: 0,
    approved: 0,
    rejected: 0,
    approval_rate: '0%',
    average_response_days: 0,
});

const pendingApprovals = ref<PendingApproval[]>([]);
const isLoading = ref(false);

const getPercentage = (status: 'pending' | 'approved' | 'rejected') => {
    const total =
        metrics.value.pending + metrics.value.approved + metrics.value.rejected;
    if (total === 0) return 0;
    return Math.round((metrics.value[status] / total) * 100);
};

const refreshData = async () => {
    isLoading.value = true;
    try {
        const response = await fetch('/api/approvals-summary');
        const data = await response.json();

        if (data.status === 'success') {
            metrics.value = data.metrics;
            pendingApprovals.value = data.pending_approvals;
        }
    } catch (error) {
        console.error('Failed to fetch approval summary:', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    refreshData();
});
</script>

<style scoped>
/* Animations for progress bars */
.transition-all {
    transition: all 0.5s ease-in-out;
}

/* Hover effects for cards */
.hover\:border-amber-500\/40:hover {
    border-color: rgba(217, 119, 6, 0.4);
}

.hover\:border-green-500\/40:hover {
    border-color: rgba(34, 197, 94, 0.4);
}

.hover\:border-red-500\/40:hover {
    border-color: rgba(239, 68, 68, 0.4);
}

.hover\:border-blue-500\/40:hover {
    border-color: rgba(59, 130, 246, 0.4);
}
</style>
