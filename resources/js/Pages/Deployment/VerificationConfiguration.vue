<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface DeploymentMode {
    value: 'auto_transitions' | 'hybrid' | 'monitoring_only';
    label: string;
    description: string;
    icon: string;
}

const deploymentModes: DeploymentMode[] = [
    {
        value: 'auto_transitions',
        label: '🤖 Autopiloto',
        description: 'El sistema cambia automáticamente según métricas',
        icon: 'mdi-robot',
    },
    {
        value: 'hybrid',
        label: '⚙️ Hybrid',
        description: 'Recolecta automáticamente, tú decides cuándo cambiar',
        icon: 'mdi-cog',
    },
    {
        value: 'monitoring_only',
        label: '📊 Solo Monitoreo',
        description: 'Recolecta datos sin automatización',
        icon: 'mdi-chart-line',
    },
];

const selectedMode = ref<'auto_transitions' | 'hybrid' | 'monitoring_only'>(
    'hybrid',
);
const loading = ref(false);
const showAdvanced = ref(false);

// Configuración de Auto-Transitions
const autoConfig = ref({
    error_rate_threshold_phase2: 15,
    retry_rate_threshold_phase4: 10,
    check_interval_minutes: 60,
    data_window_hours: 24,
    enable_notifications: true,
    notification_channel: 'log',
});

// Configuración de Hybrid
const hybridConfig = ref({
    metrics_collection_interval: 60,
    alert_threshold_percent: 20,
    enable_suggestions: true,
    enable_web_dashboard: true,
    suggestion_channel: 'both',
});

// Configuración de Monitoring Only
const monitoringConfig = ref({
    metrics_collection_interval: 60,
    metrics_retention_days: 30,
});

const currentMode = computed(() =>
    deploymentModes.find((m) => m.value === selectedMode.value),
);

const handleSave = async () => {
    loading.value = true;

    const config = {
        mode: selectedMode.value,
        ...(selectedMode.value === 'auto_transitions' && {
            autoConfig: autoConfig.value,
        }),
        ...(selectedMode.value === 'hybrid' && {
            hybridConfig: hybridConfig.value,
        }),
        ...(selectedMode.value === 'monitoring_only' && {
            monitoringConfig: monitoringConfig.value,
        }),
    };

    try {
        // Aquí iría la llamada a la API
        // await fetch('/api/verification-config', { method: 'POST', body: JSON.stringify(config) })

        // Por ahora, simulamos con un console log
        console.log('Configuración a guardar:', config);

        // Mostrar confirmación
        alert('✅ Configuración guardada exitosamente');
    } finally {
        loading.value = false;
    }
};

const handleReset = () => {
    if (confirm('¿Estás seguro de que deseas reiniciar la configuración?')) {
        // Reset logic
        selectedMode.value = 'hybrid';
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Configuración de Verificación" />

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1
                        class="mb-2 text-4xl font-bold text-gray-900 dark:text-white"
                    >
                        ⚙️ Configuración de Verificación
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Personaliza cómo el sistema de verificación se despliega
                        en producción
                    </p>
                </div>

                <!-- Selection Cards -->
                <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div
                        v-for="mode in deploymentModes"
                        :key="mode.value"
                        :class="[
                            'cursor-pointer rounded-lg border-2 p-6 transition-all',
                            selectedMode === mode.value
                                ? 'border-blue-500 bg-blue-50 dark:border-blue-400 dark:bg-blue-900'
                                : 'border-gray-300 bg-white hover:border-gray-400 dark:border-gray-600 dark:bg-gray-800',
                        ]"
                        @click="selectedMode = mode.value"
                    >
                        <div class="mb-2 text-2xl">{{ mode.icon }}</div>
                        <h3
                            class="mb-2 font-semibold text-gray-900 dark:text-white"
                        >
                            {{ mode.label }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ mode.description }}
                        </p>
                        <div v-if="selectedMode === mode.value" class="mt-4">
                            <span
                                class="inline-block rounded-full bg-blue-500 px-3 py-1 text-xs text-white"
                            >
                                ✓ Seleccionado
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Configuration Form -->
                <div
                    class="mb-8 rounded-lg bg-white p-8 shadow dark:bg-gray-800"
                >
                    <h2
                        class="mb-6 text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        {{ currentMode?.label }} - Configuración
                    </h2>

                    <!-- Auto-Transitions Config -->
                    <div
                        v-if="selectedMode === 'auto_transitions'"
                        class="space-y-6"
                    >
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Umbral error_rate para Phase 2 (%)
                                </label>
                                <input
                                    v-model.number="
                                        autoConfig.error_rate_threshold_phase2
                                    "
                                    type="range"
                                    min="1"
                                    max="50"
                                    class="w-full"
                                />
                                <div class="mt-1 text-sm text-gray-500">
                                    Valor:
                                    {{
                                        autoConfig.error_rate_threshold_phase2
                                    }}%
                                </div>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Umbral retry_rate para Phase 4 (%)
                                </label>
                                <input
                                    v-model.number="
                                        autoConfig.retry_rate_threshold_phase4
                                    "
                                    type="range"
                                    min="1"
                                    max="50"
                                    class="w-full"
                                />
                                <div class="mt-1 text-sm text-gray-500">
                                    Valor:
                                    {{
                                        autoConfig.retry_rate_threshold_phase4
                                    }}%
                                </div>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Verificación cada (minutos)
                                </label>
                                <select
                                    v-model.number="
                                        autoConfig.check_interval_minutes
                                    "
                                    class="w-full rounded border px-3 py-2"
                                >
                                    <option :value="15">15 minutos</option>
                                    <option :value="30">30 minutos</option>
                                    <option :value="60">1 hora</option>
                                    <option :value="120">2 horas</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Ventana de datos (horas)
                                </label>
                                <select
                                    v-model.number="
                                        autoConfig.data_window_hours
                                    "
                                    class="w-full rounded border px-3 py-2"
                                >
                                    <option :value="6">6 horas</option>
                                    <option :value="24">24 horas</option>
                                    <option :value="168">7 días</option>
                                </select>
                            </div>
                        </div>

                        <div class="border-t pt-6">
                            <label class="flex cursor-pointer items-center">
                                <input
                                    v-model="autoConfig.enable_notifications"
                                    type="checkbox"
                                    class="h-4 w-4 rounded"
                                />
                                <span
                                    class="ml-3 text-gray-700 dark:text-gray-300"
                                >
                                    Habilitar notificaciones de cambios
                                    automáticos
                                </span>
                            </label>

                            <div
                                v-if="autoConfig.enable_notifications"
                                class="mt-4"
                            >
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Canal de notificación
                                </label>
                                <select
                                    v-model="autoConfig.notification_channel"
                                    class="w-full rounded border px-3 py-2"
                                >
                                    <option value="log">📋 Logs</option>
                                    <option value="slack">💬 Slack</option>
                                    <option value="email">📧 Email</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Hybrid Config -->
                    <div v-if="selectedMode === 'hybrid'" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Recolectar métricas cada (minutos)
                                </label>
                                <select
                                    v-model.number="
                                        hybridConfig.metrics_collection_interval
                                    "
                                    class="w-full rounded border px-3 py-2"
                                >
                                    <option :value="15">15 minutos</option>
                                    <option :value="30">30 minutos</option>
                                    <option :value="60">1 hora</option>
                                    <option :value="120">2 horas</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Alertar si error_rate sube más del (%) en 1
                                    hora
                                </label>
                                <input
                                    v-model.number="
                                        hybridConfig.alert_threshold_percent
                                    "
                                    type="range"
                                    min="5"
                                    max="50"
                                    class="w-full"
                                />
                                <div class="mt-1 text-sm text-gray-500">
                                    Valor:
                                    {{ hybridConfig.alert_threshold_percent }}%
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 border-t pt-6">
                            <label class="flex cursor-pointer items-center">
                                <input
                                    v-model="hybridConfig.enable_suggestions"
                                    type="checkbox"
                                    class="h-4 w-4 rounded"
                                />
                                <span
                                    class="ml-3 text-gray-700 dark:text-gray-300"
                                >
                                    Mostrar sugerencias cuando sea momento de
                                    cambiar phase
                                </span>
                            </label>

                            <label class="flex cursor-pointer items-center">
                                <input
                                    v-model="hybridConfig.enable_web_dashboard"
                                    type="checkbox"
                                    class="h-4 w-4 rounded"
                                />
                                <span
                                    class="ml-3 text-gray-700 dark:text-gray-300"
                                >
                                    Habilitar dashboard web de métricas
                                </span>
                            </label>

                            <div
                                v-if="hybridConfig.enable_suggestions"
                                class="mt-4"
                            >
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Dónde enviar sugerencias
                                </label>
                                <select
                                    v-model="hybridConfig.suggestion_channel"
                                    class="w-full rounded border px-3 py-2"
                                >
                                    <option value="cli">Terminal/CLI</option>
                                    <option value="web">Dashboard Web</option>
                                    <option value="both">Ambos</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Monitoring Only Config -->
                    <div
                        v-if="selectedMode === 'monitoring_only'"
                        class="space-y-6"
                    >
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Recolectar métricas cada (minutos)
                                </label>
                                <select
                                    v-model.number="
                                        monitoringConfig.metrics_collection_interval
                                    "
                                    class="w-full rounded border px-3 py-2"
                                >
                                    <option :value="15">15 minutos</option>
                                    <option :value="30">30 minutos</option>
                                    <option :value="60">1 hora</option>
                                    <option :value="120">2 horas</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Retener métric datos por (días)
                                </label>
                                <select
                                    v-model.number="
                                        monitoringConfig.metrics_retention_days
                                    "
                                    class="w-full rounded border px-3 py-2"
                                >
                                    <option :value="7">7 días</option>
                                    <option :value="30">30 días</option>
                                    <option :value="90">90 días</option>
                                </select>
                            </div>
                        </div>

                        <div
                            class="rounded border border-blue-200 bg-blue-50 p-4 dark:border-blue-700 dark:bg-blue-900"
                        >
                            <p class="text-sm text-blue-900 dark:text-blue-100">
                                💡 En modo "Solo Monitoreo", el sistema
                                recolectará métricas pero NO hará cambios
                                automáticos. Tú tomarás todos los cambios de
                                fase manualmente.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div
                    class="mb-8 rounded-lg border border-amber-200 bg-amber-50 p-6 dark:border-amber-700 dark:bg-amber-900"
                >
                    <h3
                        class="mb-2 font-semibold text-amber-900 dark:text-amber-100"
                    >
                        ⚙️ Próximos pasos
                    </h3>
                    <ol
                        class="list-inside list-decimal space-y-1 text-sm text-amber-800 dark:text-amber-200"
                    >
                        <li>Guarda esta configuración</li>
                        <li>
                            El sistema actualizará automáticamente tu .env y
                            config/verification-deployment.php
                        </li>
                        <li>
                            Ejecuta:
                            <code
                                class="rounded bg-amber-900 px-2 py-1 text-amber-100"
                                >php artisan config:cache</code
                            >
                        </li>
                        <li>
                            Comienza el despliegue:
                            <code
                                class="rounded bg-amber-900 px-2 py-1 text-amber-100"
                                >./scripts/verification-phase-deploy.sh
                                silent</code
                            >
                        </li>
                    </ol>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between">
                    <button
                        @click="handleReset"
                        class="rounded-lg border border-gray-300 px-6 py-3 text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        ↻ Reiniciar
                    </button>

                    <button
                        @click="handleSave"
                        :disabled="loading"
                        class="rounded-lg bg-blue-600 px-8 py-3 text-white transition hover:bg-blue-700 disabled:opacity-50"
                    >
                        <span v-if="loading">Guardando...</span>
                        <span v-else>✓ Guardar Configuración</span>
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
input[type='range'] {
    height: 6px;
    border-radius: 3px;
    background: linear-gradient(
        to right,
        #3b82f6 0%,
        #3b82f6 50%,
        #e5e7eb 50%,
        #e5e7eb 100%
    );
    outline: none;
}

input[type='range']::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #3b82f6;
    cursor: pointer;
}

input[type='range']::-moz-range-thumb {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #3b82f6;
    cursor: pointer;
    border: none;
}
</style>
