<template>
    <div class="channel-config">
        <div class="space-y-4">
            <!-- Slack Channel -->
            <div class="rounded-lg border border-border bg-card p-4">
                <div class="flex items-start justify-between">
                    <div class="flex flex-1 items-start gap-3">
                        <div class="text-2xl">💬</div>
                        <div class="flex-1">
                            <h3 class="font-semibold">Slack</h3>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Recibe notificaciones en Slack vía webhook
                            </p>
                            <div class="mt-3 space-y-2">
                                <div class="flex items-center gap-2">
                                    <input
                                        v-model="config.slack_webhook"
                                        type="password"
                                        placeholder="https://hooks.slack.com/..."
                                        class="flex-1 rounded border border-border bg-muted px-3 py-2 text-sm"
                                    />
                                    <button
                                        @click="testChannel('slack')"
                                        class="rounded border border-border px-3 py-2 text-sm hover:bg-accent"
                                    >
                                        🧪 Test
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <label class="flex cursor-pointer items-center gap-2">
                        <input
                            v-model="config.slack_enabled"
                            type="checkbox"
                            class="rounded"
                        />
                        <span class="text-sm">{{
                            config.slack_enabled ? 'Activo' : 'Inactivo'
                        }}</span>
                    </label>
                </div>
            </div>

            <!-- Email Channel -->
            <div class="rounded-lg border border-border bg-card p-4">
                <div class="flex items-start justify-between">
                    <div class="flex flex-1 items-start gap-3">
                        <div class="text-2xl">📧</div>
                        <div class="flex-1">
                            <h3 class="font-semibold">Email</h3>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Envía notificaciones por correo electrónico
                            </p>
                            <div class="mt-3 space-y-2">
                                <div>
                                    <label class="text-xs text-muted-foreground"
                                        >Receptores (separados por comas)</label
                                    >
                                    <input
                                        v-model="config.email_recipients"
                                        type="text"
                                        placeholder="admin@example.com, team@example.com"
                                        class="mt-1 w-full rounded border border-border bg-muted px-3 py-2 text-sm"
                                    />
                                </div>
                                <button
                                    @click="testChannel('email')"
                                    class="rounded border border-border px-3 py-2 text-sm hover:bg-accent"
                                >
                                    🧪 Enviar Email de Prueba
                                </button>
                            </div>
                        </div>
                    </div>
                    <label class="flex cursor-pointer items-center gap-2">
                        <input
                            v-model="config.email_enabled"
                            type="checkbox"
                            class="rounded"
                        />
                        <span class="text-sm">{{
                            config.email_enabled ? 'Activo' : 'Inactivo'
                        }}</span>
                    </label>
                </div>
            </div>

            <!-- Database Channel -->
            <div class="rounded-lg border border-border bg-card p-4">
                <div class="flex items-start justify-between">
                    <div class="flex flex-1 items-start gap-3">
                        <div class="text-2xl">💾</div>
                        <div class="flex-1">
                            <h3 class="font-semibold">Base de Datos</h3>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Guarda notificaciones en la base de datos
                            </p>
                            <div class="mt-3">
                                <label class="text-xs text-muted-foreground"
                                    >Retención (días)</label
                                >
                                <input
                                    v-model.number="config.db_retention_days"
                                    type="number"
                                    min="1"
                                    max="365"
                                    class="mt-1 w-full rounded border border-border bg-muted px-3 py-2 text-sm"
                                />
                            </div>
                        </div>
                    </div>
                    <label class="flex cursor-pointer items-center gap-2">
                        <input
                            v-model="config.db_enabled"
                            type="checkbox"
                            class="rounded"
                        />
                        <span class="text-sm">{{
                            config.db_enabled ? 'Activo' : 'Inactivo'
                        }}</span>
                    </label>
                </div>
            </div>

            <!-- Log Channel -->
            <div class="rounded-lg border border-border bg-card p-4">
                <div class="flex items-start justify-between">
                    <div class="flex flex-1 items-start gap-3">
                        <div class="text-2xl">📝</div>
                        <div class="flex-1">
                            <h3 class="font-semibold">Registro (Log)</h3>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Registra notificaciones en los logs del sistema
                            </p>
                            <div class="mt-3">
                                <label class="text-xs text-muted-foreground"
                                    >Nivel de Log</label
                                >
                                <select
                                    v-model="config.log_level"
                                    class="mt-1 w-full rounded border border-border bg-card px-3 py-2 text-sm"
                                >
                                    <option value="info">ℹ️ Info</option>
                                    <option value="warning">⚠️ Warning</option>
                                    <option value="error">❌ Error</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <label class="flex cursor-pointer items-center gap-2">
                        <input
                            v-model="config.log_enabled"
                            type="checkbox"
                            class="rounded"
                        />
                        <span class="text-sm">{{
                            config.log_enabled ? 'Activo' : 'Inactivo'
                        }}</span>
                    </label>
                </div>
            </div>

            <!-- Alert Thresholds Section -->
            <div class="mt-4 border-t border-border pt-4">
                <h3 class="mb-4 font-semibold">⚠️ Umbrales de Alertas</h3>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm text-muted-foreground"
                            >Umbral de Tasa de Error (%)</label
                        >
                        <div class="mt-2 flex items-center gap-2">
                            <input
                                v-model.number="config.error_rate_threshold"
                                type="range"
                                min="0"
                                max="100"
                                class="flex-1"
                            />
                            <span class="w-12 text-right text-sm font-semibold"
                                >{{ config.error_rate_threshold }}%</span
                            >
                        </div>
                    </div>

                    <div>
                        <label class="text-sm text-muted-foreground"
                            >Umbral de Confianza (%)</label
                        >
                        <div class="mt-2 flex items-center gap-2">
                            <input
                                v-model.number="config.confidence_threshold"
                                type="range"
                                min="0"
                                max="100"
                                class="flex-1"
                            />
                            <span class="w-12 text-right text-sm font-semibold"
                                >{{ config.confidence_threshold }}%</span
                            >
                        </div>
                    </div>

                    <div>
                        <label class="text-sm text-muted-foreground"
                            >Umbral de Reintentos (%)</label
                        >
                        <div class="mt-2 flex items-center gap-2">
                            <input
                                v-model.number="config.retry_threshold"
                                type="range"
                                min="0"
                                max="100"
                                class="flex-1"
                            />
                            <span class="w-12 text-right text-sm font-semibold"
                                >{{ config.retry_threshold }}%</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Throttle Settings -->
                <div class="mt-4 border-t border-border pt-4">
                    <label class="flex cursor-pointer items-center gap-2">
                        <input
                            v-model="config.throttle_enabled"
                            type="checkbox"
                            class="rounded"
                        />
                        <span class="text-sm">Evitar alertas duplicadas</span>
                    </label>
                    <div v-if="config.throttle_enabled" class="mt-2">
                        <label class="text-xs text-muted-foreground"
                            >Ventana de deduplicación (minutos)</label
                        >
                        <input
                            v-model.number="config.throttle_minutes"
                            type="number"
                            min="1"
                            max="60"
                            class="mt-1 w-full rounded border border-border bg-muted px-3 py-2 text-sm"
                        />
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex gap-3 pt-4">
                <button
                    @click="saveConfig"
                    :disabled="saving"
                    class="rounded bg-primary px-4 py-2 text-primary-foreground hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{
                        saving ? '💾 Guardando...' : '💾 Guardar Configuración'
                    }}
                </button>
                <button
                    @click="resetConfig"
                    class="rounded border border-border px-4 py-2 hover:bg-accent"
                >
                    ↺ Restablecer
                </button>
            </div>

            <!-- Status Messages -->
            <div
                v-if="testResult"
                class="mt-4 rounded p-3"
                :class="
                    testResult.success
                        ? 'border border-green-500/20 bg-green-500/10 text-green-700 dark:text-green-400'
                        : 'border border-red-500/20 bg-red-500/10 text-red-700 dark:text-red-400'
                "
            >
                {{ testResult.message }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';

const config = ref({
    slack_enabled: false,
    slack_webhook: '',
    email_enabled: false,
    email_recipients: '',
    db_enabled: true,
    db_retention_days: 90,
    log_enabled: true,
    log_level: 'info',
    error_rate_threshold: 40,
    confidence_threshold: 85,
    retry_threshold: 20,
    throttle_enabled: true,
    throttle_minutes: 5,
});

const saving = ref(false);
const testResult = ref<{ success: boolean; message: string } | null>(null);

const fetchConfig = async () => {
    try {
        const response = await fetch(
            '/api/deployment/verification/configuration',
        );
        const data = await response.json();

        config.value = {
            slack_enabled: data.notification_channels.slack.enabled,
            slack_webhook: '',
            email_enabled: data.notification_channels.email.enabled,
            email_recipients: '',
            db_enabled: data.notification_channels.database.enabled,
            db_retention_days:
                data.notification_channels.database.retention_days,
            log_enabled: data.notification_channels.log.enabled,
            log_level: data.notification_channels.log.level,
            error_rate_threshold: data.alert_thresholds.error_rate_threshold,
            confidence_threshold:
                data.alert_thresholds.confidence_score_threshold,
            retry_threshold: data.alert_thresholds.retry_rate_threshold,
            throttle_enabled: data.throttle_settings.enabled,
            throttle_minutes: data.throttle_settings.window_minutes,
        };
    } catch (error) {
        console.error('Failed to fetch configuration:', error);
    }
};

const saveConfig = async () => {
    saving.value = true;
    try {
        // This would need a new endpoint to save configuration
        console.log('Save config:', config.value);
    } catch (error) {
        console.error('Failed to save configuration:', error);
    } finally {
        saving.value = false;
    }
};

const resetConfig = async () => {
    await fetchConfig();
};

const testChannel = async (channel: string) => {
    testResult.value = null;
    try {
        const response = await fetch(
            '/api/deployment/verification/test-notification',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ channel }),
            },
        );
        const data = await response.json();
        testResult.value = {
            success: data.success,
            message: data.message,
        };
        setTimeout(() => {
            testResult.value = null;
        }, 5000);
    } catch (error) {
        testResult.value = { success: false, message: 'Error en la prueba' };
        console.error('Test notification failed:', error);
    }
};

onMounted(() => {
    fetchConfig();
});
</script>
