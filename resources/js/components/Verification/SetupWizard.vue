<template>
    <div class="setup-wizard">
        <div class="mx-auto max-w-2xl">
            <!-- Progress Indicator -->
            <div class="mb-8">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-sm font-semibold"
                        >Paso {{ currentStep }} de 5</span
                    >
                    <span class="text-xs text-muted-foreground">{{
                        getStepName(currentStep)
                    }}</span>
                </div>
                <div class="h-2 w-full rounded-full bg-muted">
                    <div
                        class="h-2 rounded-full bg-primary transition-all duration-300"
                        :style="{ width: `${(currentStep / 5) * 100}%` }"
                    ></div>
                </div>
            </div>

            <!-- Step Content -->
            <div
                class="mb-6 min-h-64 rounded-lg border border-border bg-card p-6"
            >
                <!-- Step 1: Mode Selection -->
                <div v-if="currentStep === 1">
                    <h2 class="mb-4 text-xl font-bold">
                        Selecciona el modo de operación
                    </h2>
                    <p class="mb-6 text-sm text-muted-foreground">
                        Elige cómo deseas que el sistema verifique transiciones
                        de fase.
                    </p>

                    <div class="space-y-3">
                        <button
                            v-for="mode in modes"
                            :key="mode.value"
                            @click="form.mode = mode.value"
                            :class="
                                form.mode === mode.value
                                    ? 'ring-2 ring-primary'
                                    : 'border border-border'
                            "
                            class="w-full rounded-lg p-4 text-left transition-colors hover:bg-accent"
                        >
                            <p class="font-semibold">{{ mode.label }}</p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ mode.description }}
                            </p>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Configure Mode -->
                <div v-else-if="currentStep === 2">
                    <h2 class="mb-4 text-xl font-bold">
                        Configura el modo seleccionado
                    </h2>
                    <div
                        v-if="form.mode === 'auto_transitions'"
                        class="space-y-4"
                    >
                        <div>
                            <label class="mb-2 block text-sm font-semibold"
                                >Intervalo de verificación (horas)</label
                            >
                            <input
                                v-model.number="form.verify_interval"
                                type="number"
                                min="1"
                                max="720"
                                class="w-full rounded-lg border border-border px-3 py-2"
                            />
                            <p class="mt-1 text-xs text-muted-foreground">
                                El sistema verificará transiciones cada X horas
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold"
                                >Máximo de reintentos</label
                            >
                            <input
                                v-model.number="form.max_retries"
                                type="number"
                                min="1"
                                max="10"
                                class="w-full rounded-lg border border-border px-3 py-2"
                            />
                        </div>

                        <div
                            class="flex items-center gap-2 rounded-lg bg-primary/5 p-3"
                        >
                            <input
                                type="checkbox"
                                v-model="form.notify_on_transition"
                                id="notify-auto"
                            />
                            <label for="notify-auto" class="text-sm"
                                >Notificar cuando se complete una transición
                                automática</label
                            >
                        </div>
                    </div>

                    <div v-else-if="form.mode === 'hybrid'" class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-semibold"
                                >Umbral de confianza para auto-transición
                                (%)</label
                            >
                            <input
                                v-model.number="form.confidence_threshold"
                                type="number"
                                min="80"
                                max="100"
                                step="5"
                                class="w-full rounded-lg border border-border px-3 py-2"
                            />
                            <p class="mt-1 text-xs text-muted-foreground">
                                Requiere aprobación manual si está por debajo
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold"
                                >Tasa de error tolerada (%)</label
                            >
                            <input
                                v-model.number="form.error_rate_threshold"
                                type="number"
                                min="0"
                                max="50"
                                step="5"
                                class="w-full rounded-lg border border-border px-3 py-2"
                            />
                        </div>
                    </div>

                    <div
                        v-else-if="form.mode === 'monitoring_only'"
                        class="space-y-4"
                    >
                        <p class="mb-4 text-sm text-muted-foreground">
                            El sistema solo monitorea y alerta sobre cambios
                            potenciales. Las transiciones requieren aprobación
                            manual.
                        </p>
                        <div
                            class="flex items-center gap-2 rounded-lg bg-primary/5 p-3"
                        >
                            <input
                                type="checkbox"
                                v-model="form.alert_on_changes"
                                id="alert-changes"
                            />
                            <label for="alert-changes" class="text-sm"
                                >Alertar cuando se detecten cambios de
                                métricas</label
                            >
                        </div>
                    </div>
                </div>

                <!-- Step 3: Notification Channels -->
                <div v-else-if="currentStep === 3">
                    <h2 class="mb-4 text-xl font-bold">
                        Configura canales de notificación
                    </h2>
                    <div class="space-y-4">
                        <div class="rounded-lg border border-border p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <div>
                                    <p class="font-semibold">Slack</p>
                                    <p class="text-xs text-muted-foreground">
                                        Notificaciones en tiempo real
                                    </p>
                                </div>
                                <input
                                    type="checkbox"
                                    v-model="form.channels.slack.enabled"
                                />
                            </div>
                            <div
                                v-if="form.channels.slack.enabled"
                                class="mt-3"
                            >
                                <input
                                    v-model="form.channels.slack.webhook"
                                    type="text"
                                    placeholder="https://hooks.slack.com/..."
                                    class="w-full rounded-lg border border-border px-3 py-2 text-xs"
                                />
                            </div>
                        </div>

                        <div class="rounded-lg border border-border p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <div>
                                    <p class="font-semibold">Email</p>
                                    <p class="text-xs text-muted-foreground">
                                        Notificaciones por correo
                                    </p>
                                </div>
                                <input
                                    type="checkbox"
                                    v-model="form.channels.email.enabled"
                                />
                            </div>
                            <div
                                v-if="form.channels.email.enabled"
                                class="mt-3"
                            >
                                <input
                                    v-model="form.channels.email.recipients"
                                    type="text"
                                    placeholder="admin@example.com, ops@example.com"
                                    class="w-full rounded-lg border border-border px-3 py-2 text-xs"
                                />
                            </div>
                        </div>

                        <div class="rounded-lg border border-border p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <div>
                                    <p class="font-semibold">Base de datos</p>
                                    <p class="text-xs text-muted-foreground">
                                        Guardar auditoría local
                                    </p>
                                </div>
                                <input
                                    type="checkbox"
                                    v-model="form.channels.database.enabled"
                                />
                            </div>
                            <div
                                v-if="form.channels.database.enabled"
                                class="mt-3"
                            >
                                <label class="mb-2 block text-xs"
                                    >Retener por (días)</label
                                >
                                <input
                                    v-model.number="
                                        form.channels.database.retention_days
                                    "
                                    type="number"
                                    min="7"
                                    max="365"
                                    class="w-full rounded-lg border border-border px-3 py-2"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Alert Thresholds -->
                <div v-else-if="currentStep === 4">
                    <h2 class="mb-4 text-xl font-bold">
                        Configura umbrales de alerta
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-semibold">
                                Confianza mínima:
                                <strong
                                    >{{
                                        form.thresholds.confidence_min
                                    }}%</strong
                                >
                            </label>
                            <input
                                v-model.number="form.thresholds.confidence_min"
                                type="range"
                                min="50"
                                max="100"
                                step="5"
                                class="w-full"
                            />
                            <p class="mt-1 text-xs text-muted-foreground">
                                Alerta si cae por debajo
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold">
                                Tasa de error máxima:
                                <strong
                                    >{{
                                        form.thresholds.error_rate_max
                                    }}%</strong
                                >
                            </label>
                            <input
                                v-model.number="form.thresholds.error_rate_max"
                                type="range"
                                min="10"
                                max="50"
                                step="5"
                                class="w-full"
                            />
                            <p class="mt-1 text-xs text-muted-foreground">
                                Alerta si excede
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold">
                                Tasa de reintentos máxima:
                                <strong
                                    >{{
                                        form.thresholds.retry_rate_max
                                    }}%</strong
                                >
                            </label>
                            <input
                                v-model.number="form.thresholds.retry_rate_max"
                                type="range"
                                min="5"
                                max="50"
                                step="5"
                                class="w-full"
                            />
                            <p class="mt-1 text-xs text-muted-foreground">
                                Alerta si excede
                            </p>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold">
                                Tamaño mínimo de muestra:
                                <strong>{{
                                    form.thresholds.sample_size_min
                                }}</strong>
                            </label>
                            <input
                                v-model.number="form.thresholds.sample_size_min"
                                type="range"
                                min="50"
                                max="1000"
                                step="50"
                                class="w-full"
                            />
                            <p class="mt-1 text-xs text-muted-foreground">
                                Requerido para evaluar transición
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Review & Save -->
                <div v-else-if="currentStep === 5">
                    <h2 class="mb-4 text-xl font-bold">
                        Revisa tu configuración
                    </h2>
                    <div class="space-y-3 text-sm">
                        <div class="rounded bg-muted p-3">
                            <p class="text-muted-foreground">Modo:</p>
                            <p class="font-semibold">
                                {{ getModeLabel(form.mode) }}
                            </p>
                        </div>

                        <div
                            v-if="enabledChannels.length > 0"
                            class="rounded bg-muted p-3"
                        >
                            <p class="text-muted-foreground">
                                Canales activos:
                            </p>
                            <p class="font-semibold">
                                {{ enabledChannels.join(', ') }}
                            </p>
                        </div>

                        <div class="rounded bg-muted p-3">
                            <p class="text-muted-foreground">
                                Umbrales principales:
                            </p>
                            <p class="font-semibold">
                                Confianza:
                                {{ form.thresholds.confidence_min }}%, Error:
                                {{ form.thresholds.error_rate_max }}%
                            </p>
                        </div>

                        <div
                            class="mt-6 rounded-lg border border-green-500/20 bg-green-500/5 p-4"
                        >
                            <p
                                class="text-sm text-green-700 dark:text-green-400"
                            >
                                ✓ La configuración está lista para ser guardada.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-between">
                <button
                    @click="previousStep"
                    :disabled="currentStep === 1"
                    class="rounded-lg border border-border px-4 py-2 hover:bg-accent disabled:opacity-50"
                >
                    ← Atrás
                </button>

                <button
                    @click="saveConfiguration"
                    v-if="currentStep === 5"
                    :disabled="saving"
                    class="rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700 disabled:opacity-50"
                >
                    {{ saving ? '💾 Guardando...' : '✓ Guardar & Completar' }}
                </button>

                <button
                    @click="nextStep"
                    v-else
                    :disabled="!canProceed || currentStep === 5"
                    class="rounded-lg bg-primary px-4 py-2 text-primary-foreground hover:bg-primary/90 disabled:opacity-50"
                >
                    Siguiente →
                </button>
            </div>

            <!-- Success Message -->
            <div
                v-if="successMessage"
                class="mt-6 rounded-lg border border-green-500/20 bg-green-500/10 p-4"
            >
                <p class="text-sm text-green-700 dark:text-green-400">
                    {{ successMessage }}
                </p>
                <Link
                    href="/deployment/verification-hub"
                    class="mt-2 inline-block text-sm font-semibold text-green-600 hover:underline dark:text-green-400"
                >
                    ← Ir al Verification Hub
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface FormData {
    mode: string;
    verify_interval: number;
    max_retries: number;
    notify_on_transition: boolean;
    confidence_threshold: number;
    error_rate_threshold: number;
    alert_on_changes: boolean;
    channels: {
        slack: { enabled: boolean; webhook: string };
        email: { enabled: boolean; recipients: string };
        database: { enabled: boolean; retention_days: number };
    };
    thresholds: {
        confidence_min: number;
        error_rate_max: number;
        retry_rate_max: number;
        sample_size_min: number;
    };
}

const currentStep = ref(1);
const saving = ref(false);
const successMessage = ref('');

const modes = [
    {
        value: 'auto_transitions',
        label: '🔄 Transiciones automáticas',
        description:
            'El sistema evalúa y ejecuta transiciones automáticamente cada X horas',
    },
    {
        value: 'hybrid',
        label: '🤝 Modo híbrido',
        description:
            'Transiciones automáticas si confianza es alta, requiere aprobación si es baja',
    },
    {
        value: 'monitoring_only',
        label: '👁️ Solo monitoreo',
        description:
            'El sistema solo monitorea, todas las transiciones requieren aprobación manual',
    },
];

const form = ref<FormData>({
    mode: 'monitoring_only',
    verify_interval: 24,
    max_retries: 3,
    notify_on_transition: true,
    confidence_threshold: 90,
    error_rate_threshold: 40,
    alert_on_changes: true,
    channels: {
        slack: { enabled: false, webhook: '' },
        email: { enabled: false, recipients: '' },
        database: { enabled: true, retention_days: 90 },
    },
    thresholds: {
        confidence_min: 90,
        error_rate_max: 40,
        retry_rate_max: 20,
        sample_size_min: 100,
    },
});

const canProceed = computed(() => {
    if (currentStep.value === 1) return form.value.mode;
    if (currentStep.value === 2) return true;
    if (currentStep.value === 3) return enabledChannels.value.length > 0;
    if (currentStep.value === 4) return true;
    return true;
});

const enabledChannels = computed(() => {
    const channels = [];
    if (form.value.channels.slack.enabled) channels.push('Slack');
    if (form.value.channels.email.enabled) channels.push('Email');
    if (form.value.channels.database.enabled) channels.push('Base de datos');
    return channels;
});

const getStepName = (step: number) => {
    const names = [
        'Modo',
        'Configuración',
        'Notificaciones',
        'Umbrales',
        'Revisión',
    ];
    return names[step - 1] || '';
};

const getModeLabel = (mode: string) => {
    return modes.find((m) => m.value === mode)?.label || mode;
};

const previousStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const nextStep = () => {
    if (currentStep.value < 5 && canProceed.value) {
        currentStep.value++;
    }
};

const saveConfiguration = async () => {
    saving.value = true;
    try {
        const response = await fetch(
            '/api/deployment/verification/configuration',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token':
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute('content') || '',
                },
                body: JSON.stringify(form.value),
            },
        );

        if (response.ok) {
            successMessage.value =
                '✓ Configuración guardada exitosamente. El sistema está listo para comenzar.';
            setTimeout(() => {
                window.location.href = '/deployment/verification-hub';
            }, 2000);
        }
    } catch (error) {
        console.error('Failed to save configuration:', error);
    } finally {
        saving.value = false;
    }
};
</script>
