<template>
    <div
        class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black p-4"
    >
        <div
            class="max-h-96 w-full max-w-2xl overflow-y-auto rounded-lg bg-white shadow-xl dark:bg-gray-800"
        >
            <!-- Header -->
            <div
                class="flex items-start justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700"
            >
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ operation.operation_name }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        ID: {{ operation.id }}
                    </p>
                </div>
                <button
                    @click="$emit('close')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    ✕
                </button>
            </div>

            <!-- Content -->
            <div class="space-y-4 px-6 py-4">
                <!-- Status -->
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 dark:text-gray-400"
                        >Status:</span
                    >
                    <StatusBadge :status="operation.status" />
                </div>

                <!-- Type -->
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Type:</span>
                    <span class="text-gray-900 dark:text-white">{{
                        operation.operation_type
                    }}</span>
                </div>

                <!-- Created at -->
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 dark:text-gray-400"
                        >Created at:</span
                    >
                    <span class="text-gray-900 dark:text-white">{{
                        formatDate(operation.created_at)
                    }}</span>
                </div>

                <!-- Duration (if available) -->
                <div
                    v-if="operation.duration_seconds"
                    class="flex items-center justify-between"
                >
                    <span class="text-gray-600 dark:text-gray-400"
                        >Duration:</span
                    >
                    <span class="text-gray-900 dark:text-white"
                        >{{ operation.duration_seconds.toFixed(2) }}s</span
                    >
                </div>

                <!-- Records Affected -->
                <div
                    v-if="operation.status === 'success'"
                    class="flex items-center justify-between rounded bg-green-50 p-3 dark:bg-green-900/20"
                >
                    <span class="text-gray-600 dark:text-gray-400"
                        >Records Affected:</span
                    >
                    <span class="font-semibold text-gray-900 dark:text-white">{{
                        operation.records_affected
                    }}</span>
                </div>

                <!-- Error Message (if failed) -->
                <div
                    v-if="operation.status === 'failed'"
                    class="rounded bg-red-50 p-3 dark:bg-red-900/20"
                >
                    <p
                        class="text-sm font-semibold text-red-800 dark:text-red-200"
                    >
                        Error:
                    </p>
                    <p class="mt-1 text-sm text-red-700 dark:text-red-300">
                        {{ operation.error_message }}
                    </p>
                </div>

                <!-- Dry Run Preview (if available) -->
                <div
                    v-if="operation.dry_run_preview"
                    class="rounded bg-blue-50 p-3 dark:bg-blue-900/20"
                >
                    <p
                        class="text-sm font-semibold text-blue-800 dark:text-blue-200"
                    >
                        Preview:
                    </p>
                    <pre
                        class="mt-2 overflow-x-auto text-xs text-blue-700 dark:text-blue-300"
                        >{{
                            JSON.stringify(operation.dry_run_preview, null, 2)
                        }}</pre
                    >
                </div>

                <!-- Result (if successful) -->
                <div
                    v-if="operation.result"
                    class="rounded bg-gray-50 p-3 dark:bg-gray-700"
                >
                    <p
                        class="text-sm font-semibold text-gray-800 dark:text-gray-200"
                    >
                        Result:
                    </p>
                    <pre
                        class="mt-2 overflow-x-auto text-xs text-gray-700 dark:text-gray-300"
                        >{{ JSON.stringify(operation.result, null, 2) }}</pre
                    >
                </div>
            </div>

            <!-- Actions -->
            <div
                class="flex gap-2 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800"
            >
                <button
                    v-if="operation.status === 'pending'"
                    @click="$emit('preview')"
                    class="rounded bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700"
                >
                    Preview (Dry-Run)
                </button>
                <button
                    v-if="operation.status === 'dry_run'"
                    @click="$emit('execute')"
                    class="rounded bg-green-600 px-4 py-2 text-white transition hover:bg-green-700"
                >
                    Execute (Apply)
                </button>
                <button
                    v-if="['pending', 'dry_run'].includes(operation.status)"
                    @click="$emit('cancel')"
                    class="rounded bg-red-600 px-4 py-2 text-white transition hover:bg-red-700"
                >
                    Cancel
                </button>
                <button
                    @click="$emit('close')"
                    class="ml-auto rounded bg-gray-300 px-4 py-2 text-gray-900 transition hover:bg-gray-400 dark:bg-gray-700 dark:text-white"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import StatusBadge from './StatusBadge.vue';

interface Operation {
    id: number;
    operation_name: string;
    operation_type: string;
    status: string;
    created_at: string;
    duration_seconds?: number;
    records_affected: number;
    error_message?: string;
    dry_run_preview?: any;
    result?: any;
}

defineProps<{
    operation: Operation;
}>();

defineEmits<{
    close: [];
    preview: [];
    execute: [];
    cancel: [];
}>();

function formatDate(date: string): string {
    return new Date(date).toLocaleString();
}
</script>
