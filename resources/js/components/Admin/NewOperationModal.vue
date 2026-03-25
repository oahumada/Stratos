<template>
    <div
        class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black p-4"
    >
        <div
            class="w-full max-w-md rounded-lg bg-white shadow-xl dark:bg-gray-800"
        >
            <!-- Header -->
            <div
                class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700"
            >
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    Create New Operation
                </h2>
                <button
                    @click="$emit('close')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    ✕
                </button>
            </div>

            <!-- Content -->
            <div class="space-y-4 px-6 py-4">
                <!-- Operation Type Select -->
                <div>
                    <label
                        for="operationType"
                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Operation Type
                    </label>
                    <select
                        id="operationType"
                        v-model="formData.operationType"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        aria-labelledby="operationType"
                    >
                        <option value="">Select an operation...</option>
                        <option value="backfill">
                            Backfill Intelligence Metrics
                        </option>
                        <option value="generate">Generate Scenarios</option>
                        <option value="import">Import Data</option>
                        <option value="cleanup">Cleanup Old Data</option>
                        <option value="rebuild">Rebuild Indexes</option>
                    </select>
                </div>

                <!-- Operation Name -->
                <div>
                    <label
                        for="operationName"
                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Operation Name
                    </label>
                    <input
                        id="operationName"
                        v-model="formData.operationName"
                        type="text"
                        placeholder="e.g., Backfill Q1 Metrics"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                </div>

                <!-- Parameters (JSON) -->
                <div>
                    <label
                        for="parametersJson"
                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Parameters (JSON)
                    </label>
                    <textarea
                        id="parametersJson"
                        v-model="formData.parametersJson"
                        placeholder='{ "key": "value" }'
                        rows="4"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 font-mono text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                    <p v-if="paramError" class="mt-1 text-sm text-red-600">
                        {{ paramError }}
                    </p>
                </div>

                <!-- Confirmation notice -->
                <div class="rounded bg-yellow-50 p-3 dark:bg-yellow-900/20">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        ⚠️ This operation will first run as
                        <strong>dry-run</strong>. You'll review the preview
                        before applying.
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div
                class="flex gap-2 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-800"
            >
                <button
                    @click="submitForm"
                    :disabled="!isFormValid"
                    :class="[
                        'rounded px-4 py-2 transition',
                        isFormValid
                            ? 'cursor-pointer bg-blue-600 text-white hover:bg-blue-700'
                            : 'cursor-not-allowed bg-gray-400 text-gray-700',
                    ]"
                >
                    Create & Preview
                </button>
                <button
                    @click="$emit('close')"
                    class="ml-auto rounded bg-gray-300 px-4 py-2 text-gray-900 transition hover:bg-gray-400 dark:bg-gray-700 dark:text-white"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';

const emit = defineEmits<{
    close: [];
    create: [data: any];
}>();

interface FormData {
    operationType: string;
    operationName: string;
    parametersJson: string;
}

const formData = ref<FormData>({
    operationType: '',
    operationName: '',
    parametersJson: '{}',
});
const paramError = ref('');

const isFormValid = computed(() => {
    return (
        formData.value.operationType &&
        formData.value.operationName &&
        isJsonValid(formData.value.parametersJson)
    );
});

function isJsonValid(json: string): boolean {
    try {
        JSON.parse(json);
        return true;
    } catch {
        return false;
    }
}

function submitForm() {
    paramError.value = '';

    if (!isJsonValid(formData.value.parametersJson)) {
        paramError.value = 'Invalid JSON parameters';
        return;
    }

    emit('create', {
        operationType: formData.value.operationType,
        operationName: formData.value.operationName,
        parameters: JSON.parse(formData.value.parametersJson),
    });

    emit('close');
}
</script>
