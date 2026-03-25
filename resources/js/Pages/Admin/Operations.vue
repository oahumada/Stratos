<template>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Admin Operations
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    Manage critical operational tasks with full audit trail
                </p>
            </div>
            <button
                @click="showNewOperationModal = true"
                class="rounded-lg bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700"
            >
                + New Operation
            </button>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-4 gap-4">
            <StatsCard
                label="Total Operations"
                :value="stats.total_operations"
                icon="📊"
            />
            <StatsCard
                label="Successful"
                :value="stats.successful"
                :class="'text-green-600'"
                icon="✅"
            />
            <StatsCard
                label="Failed"
                :value="stats.failed"
                :class="'text-red-600'"
                icon="❌"
            />
            <StatsCard
                label="Pending"
                :value="stats.pending"
                :class="'text-yellow-600'"
                icon="⏳"
            />
        </div>

        <!-- Recent Operations Table -->
        <div class="rounded-lg bg-white shadow dark:bg-gray-800">
            <div
                class="border-b border-gray-200 px-6 py-4 dark:border-gray-700"
            >
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Recent Operations (Last 30 days)
                </h2>
            </div>

            <div v-if="loading" class="p-6 text-center">
                <div class="inline-block">
                    <div class="h-8 w-8 animate-spin text-blue-600"></div>
                </div>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Loading operations...
                </p>
            </div>

            <div
                v-else-if="operations.length === 0"
                class="p-6 text-center text-gray-600 dark:text-gray-400"
            >
                No operations found
            </div>

            <table v-else class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase dark:text-gray-300"
                        >
                            Operation
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase dark:text-gray-300"
                        >
                            Type
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase dark:text-gray-300"
                        >
                            Status
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase dark:text-gray-300"
                        >
                            Duration
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase dark:text-gray-300"
                        >
                            Affected
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase dark:text-gray-300"
                        >
                            User
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase dark:text-gray-300"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr
                        v-for="op in operations"
                        :key="op.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        <td
                            class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            {{ op.operation_name }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"
                        >
                            <span
                                class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-800"
                            >
                                {{ op.operation_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <StatusBadge :status="op.status" />
                        </td>
                        <td
                            class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"
                        >
                            {{
                                op.duration_seconds
                                    ? `${op.duration_seconds.toFixed(2)}s`
                                    : '—'
                            }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"
                        >
                            {{ op.records_affected }}
                        </td>
                        <td
                            class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400"
                        >
                            {{ op.user?.name }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <button
                                @click="selectOperation(op)"
                                class="text-blue-600 underline hover:text-blue-700"
                            >
                                View
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Operation Detail Modal -->
        <OperationDetailModal
            v-if="selectedOperation"
            :operation="selectedOperation"
            @close="selectedOperation = null"
            @preview="previewOperation"
            @execute="executeOperation"
            @cancel="cancelOperation"
        />

        <!-- New Operation Modal -->
        <NewOperationModal
            v-if="showNewOperationModal"
            @close="showNewOperationModal = false"
            @create="createOperation"
        />
    </div>
</template>

<script setup lang="ts">
import NewOperationModal from '@/components/Admin/NewOperationModal.vue';
import OperationDetailModal from '@/components/Admin/OperationDetailModal.vue';
import StatsCard from '@/components/Admin/StatsCard.vue';
import StatusBadge from '@/components/Admin/StatusBadge.vue';
import { axios } from '@/lib/axios';
import { onMounted, ref } from 'vue';

interface AdminOperation {
    id: number;
    operation_name: string;
    operation_type: string;
    status: string;
    duration_seconds?: number;
    records_affected: number;
    user: { name: string };
    created_at: string;
}

interface Stats {
    total_operations: number;
    successful: number;
    failed: number;
    pending: number;
}

const operations = ref<AdminOperation[]>([]);
const stats = ref<Stats>({
    total_operations: 0,
    successful: 0,
    failed: 0,
    pending: 0,
});
const selectedOperation = ref<AdminOperation | null>(null);
const showNewOperationModal = ref(false);
const loading = ref(false);

onMounted(async () => {
    await loadOperations();
});

async function loadOperations() {
    loading.value = true;
    try {
        const response = await axios.get('/api/admin/operations');
        operations.value = response.data.data;
        stats.value = response.data.stats;
    } catch (error) {
        console.error('Failed to load operations:', error);
    } finally {
        loading.value = false;
    }
}

function selectOperation(op: AdminOperation) {
    selectedOperation.value = op;
}

async function previewOperation(operationId: number) {
    try {
        const response = await axios.post(
            `/api/admin/operations/${operationId}/preview`,
        );
        selectedOperation.value = response.data.data;
    } catch (error) {
        console.error('Preview failed:', error);
    }
}

async function executeOperation(operationId: number) {
    try {
        const response = await axios.post(
            `/api/admin/operations/${operationId}/execute`,
            {
                confirmed: true,
            },
        );
        selectedOperation.value = response.data.data;
        await loadOperations();
    } catch (error) {
        console.error('Execution failed:', error);
    }
}

async function cancelOperation(operationId: number) {
    try {
        const response = await axios.post(
            `/api/admin/operations/${operationId}/cancel`,
        );
        selectedOperation.value = response.data.data;
        await loadOperations();
    } catch (error) {
        console.error('Cancellation failed:', error);
    }
}

async function createOperation(data: any) {
    // This would be extended to create actual operations
    await loadOperations();
    showNewOperationModal.value = false;
}
</script>

<style scoped>
/* Component-specific styles */
</style>
