<template>
    <AppLayout title="Centro de Control – Alertas">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Centro de Control
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Configuración de umbrales y canales de alerta
                    </p>
                </div>
                <v-btn
                    color="primary"
                    prepend-icon="mdi-plus"
                    @click="showCreateDialog = true"
                >
                    Nueva Alerta
                </v-btn>
            </div>

            <!-- Stats row -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <v-card class="pa-4" elevation="1">
                    <div class="text-sm text-gray-500">Alertas Activas</div>
                    <div class="text-3xl font-bold text-red-600">
                        {{ stats.activeCount ?? 0 }}
                    </div>
                </v-card>
                <v-card class="pa-4" elevation="1">
                    <div class="text-sm text-gray-500">Sin Atender (24h)</div>
                    <div class="text-3xl font-bold text-amber-500">
                        {{ stats.unacknowledgedCount ?? 0 }}
                    </div>
                </v-card>
                <v-card class="pa-4" elevation="1">
                    <div class="text-sm text-gray-500">
                        Umbrales Configurados
                    </div>
                    <div class="text-3xl font-bold text-blue-600">
                        {{ thresholds.length }}
                    </div>
                </v-card>
            </div>

            <!-- Critical alerts -->
            <v-card
                v-if="criticalAlerts.length"
                class="border-l-4 border-red-500"
                elevation="2"
            >
                <v-card-title class="flex items-center gap-2 text-red-700">
                    <v-icon color="red">mdi-alert-circle</v-icon>
                    Alertas Críticas
                </v-card-title>
                <v-card-text>
                    <v-list density="compact">
                        <v-list-item
                            v-for="alert in criticalAlerts"
                            :key="alert.id"
                            :subtitle="formatTime(alert.triggered_at)"
                        >
                            <template #title>
                                <span class="font-medium">{{
                                    alert.message
                                }}</span>
                            </template>
                            <template #append>
                                <v-btn
                                    size="small"
                                    variant="outlined"
                                    @click="acknowledge(alert.id)"
                                >
                                    Atender
                                </v-btn>
                            </template>
                        </v-list-item>
                    </v-list>
                </v-card-text>
            </v-card>

            <!-- Thresholds table -->
            <v-card elevation="1">
                <v-card-title>Umbrales de Alerta</v-card-title>
                <v-data-table
                    :headers="headers"
                    :items="thresholds"
                    :loading="loading"
                    item-value="id"
                    class="elevation-0"
                >
                    <template #item.metric_type="{ item }">
                        <v-chip size="small" color="blue" variant="tonal">{{
                            item.metric_type
                        }}</v-chip>
                    </template>
                    <template #item.severity="{ item }">
                        <v-chip
                            size="small"
                            :color="severityColor(item.severity)"
                            variant="tonal"
                            >{{ item.severity }}</v-chip
                        >
                    </template>
                    <template #item.is_active="{ item }">
                        <v-switch
                            :model-value="item.is_active"
                            density="compact"
                            hide-details
                            @change="toggleThreshold(item)"
                        />
                    </template>
                </v-data-table>
            </v-card>
        </div>

        <!-- Create dialog -->
        <v-dialog v-model="showCreateDialog" max-width="500">
            <v-card>
                <v-card-title>Nueva Alerta</v-card-title>
                <v-card-text>
                    <v-select
                        v-model="form.metric_type"
                        label="Métrica"
                        :items="metricTypes"
                        density="compact"
                        class="mb-3"
                    />
                    <v-select
                        v-model="form.severity"
                        label="Severidad"
                        :items="['info', 'warning', 'critical']"
                        density="compact"
                        class="mb-3"
                    />
                    <v-text-field
                        v-model.number="form.threshold_value"
                        label="Valor umbral"
                        type="number"
                        density="compact"
                        class="mb-3"
                    />
                    <v-text-field
                        v-model="form.notification_channel"
                        label="Canal (email, slack, telegram)"
                        density="compact"
                    />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn @click="showCreateDialog = false">Cancelar</v-btn>
                    <v-btn
                        color="primary"
                        :loading="saving"
                        @click="createThreshold"
                        >Crear</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { onMounted, ref } from 'vue';

interface AlertThreshold {
    id: number;
    metric_type: string;
    severity: string;
    threshold_value: number;
    notification_channel: string;
    is_active: boolean;
}

interface AlertHistory {
    id: number;
    message: string;
    triggered_at: string;
}

interface AlertStats {
    activeCount?: number;
    unacknowledgedCount?: number;
}

const loading = ref(false);
const saving = ref(false);
const showCreateDialog = ref(false);
const thresholds = ref<AlertThreshold[]>([]);
const criticalAlerts = ref<AlertHistory[]>([]);
const stats = ref<AlertStats>({});

const form = ref({
    metric_type: '',
    severity: 'warning',
    threshold_value: 0,
    notification_channel: 'email',
});

const metricTypes = [
    'performance_score',
    'completion_rate',
    'absence_rate',
    'engagement_score',
    'turnover_rate',
    'skills_gap',
];

const headers = [
    { title: 'Métrica', key: 'metric_type' },
    { title: 'Severidad', key: 'severity' },
    { title: 'Umbral', key: 'threshold_value' },
    { title: 'Canal', key: 'notification_channel' },
    { title: 'Activo', key: 'is_active' },
];

function severityColor(s: string) {
    return s === 'critical' ? 'red' : s === 'warning' ? 'amber' : 'blue';
}

function formatTime(dateString: string): string {
    const date = new Date(dateString);
    const seconds = Math.floor((Date.now() - date.getTime()) / 1000);
    if (seconds < 60) return 'ahora';
    if (seconds < 3600) return `${Math.floor(seconds / 60)}m`;
    if (seconds < 86400) return `${Math.floor(seconds / 3600)}h`;
    return `${Math.floor(seconds / 86400)}d`;
}

async function loadData() {
    loading.value = true;
    try {
        const [threshRes, critRes, statsRes] = await Promise.all([
            fetch('/api/alerts/thresholds').then((r) => r.json()),
            fetch('/api/alerts/critical').then((r) => r.json()),
            fetch('/api/alerts/statistics').then((r) => r.json()),
        ]);
        thresholds.value = threshRes.data ?? threshRes ?? [];
        criticalAlerts.value = critRes.data ?? critRes ?? [];
        stats.value = statsRes ?? {};
    } catch {
        // silent fail – page is still usable
    } finally {
        loading.value = false;
    }
}

async function createThreshold() {
    saving.value = true;
    try {
        await fetch('/api/alerts/thresholds', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(form.value),
        });
        showCreateDialog.value = false;
        await loadData();
    } finally {
        saving.value = false;
    }
}

async function acknowledge(alertId: number) {
    await fetch(`/api/alerts/history/${alertId}/acknowledge`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });
    criticalAlerts.value = criticalAlerts.value.filter((a) => a.id !== alertId);
}

async function toggleThreshold(item: AlertThreshold) {
    await fetch(`/api/alerts/thresholds/${item.id}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ is_active: !item.is_active }),
    });
    item.is_active = !item.is_active;
}

onMounted(loadData);
</script>
