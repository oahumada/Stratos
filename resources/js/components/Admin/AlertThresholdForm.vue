<template>
    <div class="space-y-6">
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h3
                class="mb-4 text-lg font-semibold text-gray-900 dark:text-white"
            >
                {{ isEditing ? 'Editar Umbral' : 'Nuevo Umbral de Alerta' }}
            </h3>

            <Form
                @submit="handleSubmit"
                :initial-values="formData"
                class="space-y-4"
            >
                <!-- Metric Name -->
                <div>
                    <label
                        class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Métrica
                    </label>
                    <input
                        v-model="form.metric"
                        type="text"
                        placeholder="ej: cpu_usage, memory_percent, response_time"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        :disabled="isEditing"
                    />
                    <p v-if="errors.metric" class="mt-1 text-sm text-red-500">
                        {{ errors.metric }}
                    </p>
                </div>

                <!-- Threshold Value -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Valor Umbral
                        </label>
                        <input
                            v-model.number="form.threshold"
                            type="number"
                            step="0.01"
                            placeholder="100"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="errors.threshold"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ errors.threshold }}
                        </p>
                    </div>

                    <!-- Severity -->
                    <div>
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Severidad
                        </label>
                        <select
                            v-model="form.severity"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="info">ℹ️ Información</option>
                            <option value="low">🔵 Baja</option>
                            <option value="medium">🟡 Media</option>
                            <option value="high">🔴 Alta</option>
                            <option value="critical">🚨 Crítica</option>
                        </select>
                        <p
                            v-if="errors.severity"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ errors.severity }}
                        </p>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label
                        class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Descripción
                    </label>
                    <textarea
                        v-model="form.description"
                        placeholder="Descripción opcional del umbral y qué significa"
                        class="h-20 w-full resize-none rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-transparent focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center gap-3">
                    <input
                        v-model="form.is_active"
                        type="checkbox"
                        id="is_active"
                        class="h-4 w-4 cursor-pointer rounded border-gray-300 text-blue-600"
                    />
                    <label
                        for="is_active"
                        class="text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Activar umbral
                    </label>
                </div>

                <!-- Actions -->
                <div
                    class="flex gap-3 border-t border-gray-200 pt-4 dark:border-gray-700"
                >
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="rounded-lg bg-blue-600 px-6 py-2 font-medium text-white transition-colors hover:bg-blue-700 disabled:bg-gray-400"
                    >
                        {{
                            isSubmitting
                                ? 'Guardando...'
                                : isEditing
                                  ? 'Actualizar'
                                  : 'Crear'
                        }}
                    </button>
                    <button
                        type="button"
                        @click="emit('cancel')"
                        class="rounded-lg bg-gray-200 px-6 py-2 font-medium text-gray-900 transition-colors hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
                    >
                        Cancelar
                    </button>
                </div>
            </Form>
        </div>

        <!-- Recent Thresholds -->
        <div
            v-if="recentThresholds.length > 0"
            class="rounded-lg bg-white p-6 shadow dark:bg-gray-800"
        >
            <h4 class="mb-4 font-semibold text-gray-900 dark:text-white">
                Umbrales Recientes
            </h4>
            <div class="space-y-2">
                <div
                    v-for="threshold in recentThresholds"
                    :key="threshold.id"
                    class="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-gray-700"
                >
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ threshold.metric }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Umbral: {{ threshold.threshold }}
                            <span
                                :class="getSeverityColor(threshold.severity)"
                                class="ml-2"
                            >
                                {{ threshold.severity }}
                            </span>
                        </p>
                    </div>
                    <button
                        @click="edit(threshold)"
                        class="text-sm text-blue-600 hover:text-blue-900 dark:hover:text-blue-400"
                    >
                        Editar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { AlertThreshold } from '@/types';
import { Form } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Props {
    threshold?: AlertThreshold | null;
    recentThresholds?: AlertThreshold[];
}

interface Emits {
    (e: 'submit', data: any): void;
    (e: 'cancel'): void;
}

const props = withDefaults(defineProps<Props>(), {
    threshold: null,
    recentThresholds: () => [],
});

const emit = defineEmits<Emits>();

const form = ref({
    metric: props.threshold?.metric || '',
    threshold: props.threshold?.threshold || 0,
    severity: props.threshold?.severity || 'medium',
    is_active: props.threshold?.is_active ?? true,
    description: props.threshold?.description || '',
});

const errors = ref({} as Record<string, string>);
const isSubmitting = ref(false);

const isEditing = computed(() => !!props.threshold);

const severityColors = {
    info: 'text-blue-600 dark:text-blue-400',
    low: 'text-cyan-600 dark:text-cyan-400',
    medium: 'text-amber-600 dark:text-amber-400',
    high: 'text-orange-600 dark:text-orange-400',
    critical: 'text-red-600 dark:text-red-400',
};

const getSeverityColor = (severity: string) => {
    return (
        severityColors[severity as keyof typeof severityColors] ||
        'text-gray-600'
    );
};

const handleSubmit = async () => {
    isSubmitting.value = true;
    errors.value = {};

    try {
        emit('submit', form.value);
    } catch (error: any) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        }
    } finally {
        isSubmitting.value = false;
    }
};

const edit = (threshold: AlertThreshold) => {
    emit('submit', threshold);
};
</script>
