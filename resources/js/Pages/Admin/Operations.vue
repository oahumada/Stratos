<template>
    <AppLayout title="Admin Operations">
        <div class="space-y-8">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1
                        class="text-3xl font-bold text-gray-900 dark:text-white"
                    >
                        Admin Operations
                    </h1>
                    <p
                        class="mt-1 flex items-center gap-2 text-gray-600 dark:text-gray-400"
                    >
                        <span
                            v-if="isStreamConnected"
                            class="inline-block h-2 w-2 animate-pulse rounded-full bg-green-500"
                        ></span>
                        <span
                            v-else
                            class="inline-block h-2 w-2 rounded-full bg-gray-500"
                        ></span>
                        {{
                            isStreamConnected
                                ? 'Real-time connected'
                                : 'Offline mode'
                        }}
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
                    label="Running"
                    :value="stats.running"
                    :class="'text-blue-600'"
                    icon="⚡"
                />
            </div>

            <!-- Active Operations Alert -->
            <div
                v-if="activeOperations.length > 0"
                class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20"
            >
                <div class="flex items-start gap-3">
                    <div class="pt-0.5">
                        <svg
                            class="h-5 w-5 text-blue-600"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3
                            class="font-medium text-blue-900 dark:text-blue-100"
                        >
                            {{ activeOperations.length }} Operation{{
                                activeOperations.length !== 1 ? 's' : ''
                            }}
                            Running
                        </h3>
                        <div class="mt-2 space-y-2">
                            <div
                                v-for="op in activeOperations"
                                :key="op.id"
                                class="flex items-center justify-between rounded bg-white p-2 dark:bg-gray-800"
                            >
                                <div class="flex-1">
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ op.operation_name }}
                                    </p>
                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        {{ op.operation_type }} • Started
                                        {{ formatTime(op.started_at) }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="h-2 w-32 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
                                    >
                                        <div
                                            class="h-full w-1/3 animate-pulse bg-blue-600"
                                        ></div>
                                    </div>
                                    <span
                                        class="text-xs font-medium text-gray-600 dark:text-gray-400"
                                    >
                                        Running...
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Operations Table -->
            <div class="rounded-lg bg-white shadow dark:bg-gray-800">
                <div
                    class="border-b border-gray-200 px-6 py-4 dark:border-gray-700"
                >
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
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
                    <tbody
                        class="divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <tr
                            v-for="op in operations"
                            :key="op.id"
                            class="transition hover:bg-gray-50 dark:hover:bg-gray-700"
                            :class="{
                                'bg-blue-50 dark:bg-blue-900/20':
                                    isOperationRunning(op.id),
                            }"
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
    </AppLayout>
</template>

<script setup lang="ts">
import NewOperationModal from '@/components/Admin/NewOperationModal.vue';
import OperationDetailModal from '@/components/Admin/OperationDetailModal.vue';
import StatsCard from '@/components/Admin/StatsCard.vue';
import StatusBadge from '@/components/Admin/StatusBadge.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { axios } from '@/lib/axios';
import { onBeforeUnmount, onMounted, ref } from 'vue';

interface AdminOperation {
    id: number;
    operation_name: string;
    operation_type: string;
    status: string;
    duration_seconds?: number;
    records_affected: number;
    records_processed: number;
    user: { name: string };
    created_at: string;
    started_at?: string;
    completed_at?: string;
}

interface Stats {
    total_operations: number;
    successful: number;
    failed: number;
    running: number;
}

const operations = ref<AdminOperation[]>([]);
const stats = ref<Stats>({
    total_operations: 0,
    successful: 0,
    failed: 0,
    running: 0,
});
const selectedOperation = ref<AdminOperation | null>(null);
const showNewOperationModal = ref(false);
const loading = ref(false);
const isStreamConnected = ref(false);
let eventSource: EventSource | null = null;

onMounted(async () => {
    await loadOperations();
    connectToStream();
});

onBeforeUnmount(() => {
    disconnectFromStream();
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

function connectToStream() {
    try {
        eventSource = new EventSource('/api/admin/operations/monitor/stream');

        eventSource.addEventListener(
            'operation.queued',
            (event: MessageEvent) => {
                const data = JSON.parse(event.data);
                handleOperationQueued(data);
            },
        );

        eventSource.addEventListener(
            'operation.started',
            (event: MessageEvent) => {
                const data = JSON.parse(event.data);
                handleOperationStarted(data);
            },
        );

        eventSource.addEventListener(
            'operation.completed',
            (event: MessageEvent) => {
                const data = JSON.parse(event.data);
                handleOperationCompleted(data);
            },
        );

        eventSource.addEventListener(
            'operation.failed',
            (event: MessageEvent) => {
                const data = JSON.parse(event.data);
                handleOperationFailed(data);
            },
        );

        eventSource.addEventListener(
            'operation.rolled_back',
            (event: MessageEvent) => {
                const data = JSON.parse(event.data);
                handleOperationRolledBack(data);
            },
        );

        eventSource.onopen = () => {
            isStreamConnected.value = true;
        };

        eventSource.onerror = () => {
            isStreamConnected.value = false;
            eventSource?.close();
        };
    } catch (error) {
        console.error('Failed to connect to stream:', error);
    }
}

function disconnectFromStream() {
    if (eventSource) {
        eventSource.close();
        isStreamConnected.value = false;
    }
}

function handleOperationQueued(data: any) {
    updateOperation(data);
    stats.value.running++;
}

function handleOperationStarted(data: any) {
    updateOperation(data);
}

function handleOperationCompleted(data: any) {
    updateOperation(data);
    stats.value.running--;
    stats.value.successful++;
}

function handleOperationFailed(data: any) {
    updateOperation(data);
    stats.value.running--;
    stats.value.failed++;
}

function handleOperationRolledBack(data: any) {
    updateOperation(data);
    stats.value.running--;
}

function updateOperation(data: any) {
    const index = operations.value.findIndex((op) => op.id === data.id);
    if (index !== -1) {
        operations.value[index] = { ...operations.value[index], ...data };
    } else {
        // New operation, add to beginning
        loadOperations();
    }

    // Update selected operation if it matches
    if (selectedOperation.value?.id === data.id) {
        selectedOperation.value = { ...selectedOperation.value, ...data };
    }
}

function isOperationRunning(operationId: number): boolean {
    return operations.value.some(
        (op) => op.id === operationId && op.status === 'running',
    );
}

function activeOperations(): AdminOperation[] {
    return operations.value.filter((op) => op.status === 'running');
}

Object.defineProperty(this, 'activeOperations', {
    get() {
        return operations.value.filter((op) => op.status === 'running');
    },
});

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
    await loadOperations();
    showNewOperationModal.value = false;
}

function formatTime(dateString?: string): string {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now.getTime() - date.getTime()) / 1000);

    if (seconds < 60) return 'just now';
    if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`;
    if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`;
    return `${Math.floor(seconds / 86400)}d ago`;
}
</script>
