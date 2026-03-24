<template>
    <div class="compliance-report-generator">
        <!-- Report Generation Section -->
        <div class="mb-6 rounded-lg border border-border bg-card p-6">
            <h3 class="mb-4 text-lg font-semibold">
                Generar reporte de cumplimiento
            </h3>

            <div class="mb-4 grid grid-cols-3 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-semibold"
                        >Fecha inicial</label
                    >
                    <input
                        v-model="filters.dateFrom"
                        type="date"
                        class="w-full rounded-lg border border-border bg-card px-3 py-2 text-sm"
                        :max="filters.dateTo"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold"
                        >Fecha final</label
                    >
                    <input
                        v-model="filters.dateTo"
                        type="date"
                        class="w-full rounded-lg border border-border bg-card px-3 py-2 text-sm"
                        :min="filters.dateFrom"
                        :max="today"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold"
                        >Formato de exportación</label
                    >
                    <select
                        v-model="exportFormat"
                        class="w-full rounded-lg border border-border bg-card px-3 py-2 text-sm"
                    >
                        <option value="json">JSON</option>
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-semibold"
                        >Filtrar por acción</label
                    >
                    <select
                        v-model="filters.action"
                        multiple
                        class="w-full rounded-lg border border-border bg-card px-3 py-2 text-sm"
                        size="3"
                    >
                        <option value="phase_transition">
                            Transición de fase
                        </option>
                        <option value="config_change">
                            Cambio de configuración
                        </option>
                        <option value="manual_override">
                            Anulación manual
                        </option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold"
                        >Filtrar por usuario</label
                    >
                    <input
                        v-model="filters.userSearch"
                        type="text"
                        placeholder="Buscar usuario..."
                        class="w-full rounded-lg border border-border bg-card px-3 py-2 text-sm"
                    />
                </div>
            </div>

            <div class="flex gap-2">
                <button
                    @click="generateReport"
                    :disabled="loading"
                    class="rounded-lg bg-primary px-4 py-2 text-primary-foreground hover:bg-primary/90 disabled:opacity-50"
                >
                    {{ loading ? '⏳ Generando...' : '📊 Generar reporte' }}
                </button>
                <button
                    @click="resetFilters"
                    class="rounded-lg border border-border px-4 py-2 hover:bg-accent"
                >
                    🔄 Restablecer
                </button>
            </div>
        </div>

        <!-- Report Display Section -->
        <div v-if="reportData" class="space-y-6">
            <!-- Summary Stats -->
            <div class="grid grid-cols-4 gap-4">
                <div
                    class="rounded-lg border border-blue-500/20 bg-blue-500/10 p-4"
                >
                    <p class="text-xs text-muted-foreground">
                        Total de eventos
                    </p>
                    <p
                        class="text-3xl font-bold text-blue-600 dark:text-blue-400"
                    >
                        {{ reportData.summary.total_events }}
                    </p>
                </div>

                <div
                    class="rounded-lg border border-green-500/20 bg-green-500/10 p-4"
                >
                    <p class="text-xs text-muted-foreground">
                        Transiciones de fase
                    </p>
                    <p
                        class="text-3xl font-bold text-green-600 dark:text-green-400"
                    >
                        {{ reportData.summary.phase_transitions }}
                    </p>
                </div>

                <div
                    class="rounded-lg border border-orange-500/20 bg-orange-500/10 p-4"
                >
                    <p class="text-xs text-muted-foreground">
                        Cambios de configuración
                    </p>
                    <p
                        class="text-3xl font-bold text-orange-600 dark:text-orange-400"
                    >
                        {{ reportData.summary.config_changes }}
                    </p>
                </div>

                <div
                    class="rounded-lg border border-red-500/20 bg-red-500/10 p-4"
                >
                    <p class="text-xs text-muted-foreground">Usuarios únicos</p>
                    <p
                        class="text-3xl font-bold text-red-600 dark:text-red-400"
                    >
                        {{ reportData.summary.unique_users }}
                    </p>
                </div>
            </div>

            <!-- Breakdown by Trigger Source -->
            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-lg border border-border bg-card p-4">
                    <h4 class="mb-3 font-semibold">Origen de eventos</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Automático:</span>
                            <strong>{{
                                reportData.summary.by_trigger?.automatic || 0
                            }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Manual:</span>
                            <strong>{{
                                reportData.summary.by_trigger?.manual || 0
                            }}</strong>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-border bg-card p-4">
                    <h4 class="mb-3 font-semibold">Distribución de acciones</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Transiciones:</span>
                            <strong>{{
                                reportData.summary.phase_transitions
                            }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Configuraciones:</span>
                            <strong>{{
                                reportData.summary.config_changes
                            }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Anulaciones:</span>
                            <strong>{{
                                reportData.summary.manual_overrides || 0
                            }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Events Timeline -->
            <div class="rounded-lg border border-border bg-card p-4">
                <h4 class="mb-4 font-semibold">Línea de tiempo de eventos</h4>
                <div class="max-h-96 space-y-3 overflow-y-auto">
                    <div
                        v-for="(event, idx) in reportData.events"
                        :key="idx"
                        class="cursor-pointer rounded border border-border p-3 hover:bg-accent"
                        @click="
                            expandedEventId =
                                expandedEventId === idx ? null : idx
                        "
                    >
                        <div class="mb-2 flex items-start justify-between">
                            <div>
                                <p class="font-semibold capitalize">
                                    {{ event.action.replace(/_/g, ' ') }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ formatTime(event.timestamp) }}
                                </p>
                            </div>
                            <span
                                :class="getActionColor(event.action)"
                                class="rounded px-2 py-1 text-xs"
                            >
                                {{ event.action }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <span>{{ event.user || 'Sistema' }}</span>
                            <span
                                v-if="event.phase_from"
                                class="text-xs text-muted-foreground"
                            >
                                {{ event.phase_from }} → {{ event.phase_to }}
                            </span>
                        </div>

                        <!-- Expanded Details -->
                        <div
                            v-if="expandedEventId === idx"
                            class="mt-3 border-t border-border pt-3"
                        >
                            <div v-if="event.reason" class="mb-2 text-xs">
                                <strong>Razón:</strong> {{ event.reason }}
                            </div>
                            <div
                                v-if="event.change_summary"
                                class="mb-2 text-xs"
                            >
                                <strong>Cambios:</strong>
                                {{ event.change_summary }}
                            </div>
                            <pre
                                class="max-h-32 overflow-auto rounded bg-muted p-2 text-xs"
                                >{{ JSON.stringify(event, null, 2) }}</pre
                            >
                        </div>
                    </div>

                    <div
                        v-if="!reportData.events.length"
                        class="py-8 text-center text-muted-foreground"
                    >
                        No hay eventos en este período
                    </div>
                </div>
            </div>

            <!-- Export Section -->
            <div class="rounded-lg border border-border bg-card p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold">Descargar reporte</p>
                        <p class="text-xs text-muted-foreground">
                            Formato seleccionado:
                            {{ exportFormat.toUpperCase() }}
                        </p>
                    </div>
                    <button
                        @click="downloadReport"
                        class="rounded-lg bg-primary px-4 py-2 text-primary-foreground hover:bg-primary/90"
                    >
                        ⬇️ Descargar
                    </button>
                </div>
            </div>

            <!-- Schedule Export -->
            <div class="rounded-lg border border-primary/20 bg-primary/5 p-4">
                <div class="flex items-start gap-4">
                    <div class="flex-1">
                        <p class="mb-2 font-semibold">
                            Programar exportación automática
                        </p>
                        <p class="mb-3 text-sm text-muted-foreground">
                            Recibe reportes periódicos por correo
                        </p>

                        <div class="grid grid-cols-2 gap-3">
                            <select
                                v-model="scheduleFrequency"
                                class="rounded-lg border border-border bg-card px-3 py-2 text-sm"
                            >
                                <option value="weekly">Semanal</option>
                                <option value="monthly">Mensual</option>
                                <option value="quarterly">Trimestral</option>
                            </select>

                            <input
                                v-model="scheduleEmail"
                                type="email"
                                placeholder="correo@ejemplo.com"
                                class="rounded-lg border border-border bg-card px-3 py-2 text-sm"
                            />
                        </div>
                    </div>

                    <button
                        @click="scheduleExport"
                        class="h-fit rounded-lg bg-primary px-4 py-2 text-primary-foreground hover:bg-primary/90"
                    >
                        ✓ Programar
                    </button>
                </div>
            </div>
        </div>

        <!-- No Report Generated -->
        <div
            v-else-if="!loading"
            class="rounded-lg bg-muted/50 p-8 text-center text-muted-foreground"
        >
            <p class="mb-2">
                📊 Usa los controles arriba para generar tu primer reporte
            </p>
            <p class="text-sm">
                Selecciona un período de tiempo y haz clic en "Generar reporte"
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface ComplianceReport {
    summary: {
        total_events: number;
        phase_transitions: number;
        config_changes: number;
        manual_overrides?: number;
        unique_users: number;
        by_trigger?: {
            automatic: number;
            manual: number;
        };
    };
    events: Array<{
        timestamp: string;
        action: string;
        user?: string;
        phase_from?: string;
        phase_to?: string;
        reason?: string;
        change_summary?: string;
        [key: string]: any;
    }>;
}

const loading = ref(false);
const reportData = ref<ComplianceReport | null>(null);
const expandedEventId = ref<number | null>(null);
const exportFormat = ref('pdf');
const scheduleFrequency = ref('weekly');
const scheduleEmail = ref('');

const today = new Date().toISOString().split('T')[0];

const filters = ref({
    dateFrom: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000)
        .toISOString()
        .split('T')[0],
    dateTo: today,
    action: [] as string[],
    userSearch: '',
});

const formatTime = (dateStr: string) => {
    const date = new Date(dateStr);
    return date.toLocaleString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getActionColor = (action: string) => {
    const colors: Record<string, string> = {
        phase_transition: 'bg-blue-500/10 text-blue-700 dark:text-blue-400',
        config_change: 'bg-green-500/10 text-green-700 dark:text-green-400',
        manual_override:
            'bg-orange-500/10 text-orange-700 dark:text-orange-400',
    };
    return colors[action] || 'bg-gray-500/10';
};

const generateReport = async () => {
    loading.value = true;
    try {
        const params = new URLSearchParams({
            date_from: filters.value.dateFrom,
            date_to: filters.value.dateTo,
            format: exportFormat.value,
        });

        const response = await fetch(
            `/api/deployment/verification/compliance-report?${params}`,
        );
        const data = await response.json();
        reportData.value = data.data;
    } catch (error) {
        console.error('Failed to generate report:', error);
    } finally {
        loading.value = false;
    }
};

const resetFilters = () => {
    filters.value = {
        dateFrom: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000)
            .toISOString()
            .split('T')[0],
        dateTo: today,
        action: [],
        userSearch: '',
    };
    reportData.value = null;
};

const downloadReport = () => {
    if (!reportData.value) return;

    const content = generateReportContent();

    let mimeType = 'application/json';
    let extension = 'json';

    if (exportFormat.value === 'csv') {
        mimeType = 'text/csv';
        extension = 'csv';
    } else if (exportFormat.value === 'pdf') {
        mimeType = 'text/plain';
        extension = 'txt';
    }

    const blob = new Blob([content], { type: mimeType });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `compliance-report-${filters.value.dateFrom}-to-${filters.value.dateTo}.${extension}`;
    link.click();
};

const generateReportContent = () => {
    if (!reportData.value) return '';

    if (exportFormat.value === 'json') {
        return JSON.stringify(reportData.value, null, 2);
    } else if (exportFormat.value === 'csv') {
        let csv = 'Timestamp,Action,User,Phase From,Phase To,Trigger,Reason\n';
        reportData.value.events.forEach((event) => {
            csv +=
                [
                    event.timestamp,
                    event.action,
                    event.user || 'System',
                    event.phase_from || '',
                    event.phase_to || '',
                    event.triggered_by || '',
                    event.reason || '',
                ]
                    .map((v) => `"${String(v).replace(/"/g, '""')}"`)
                    .join(',') + '\n';
        });
        return csv;
    } else {
        // PDF as text (for demo)
        let text = `REPORTE DE CUMPLIMIENTO\n`;
        text += `Período: ${filters.value.dateFrom} a ${filters.value.dateTo}\n`;
        text += `Generado: ${new Date().toLocaleString('es-ES')}\n`;
        text += `\n═════════════════════════════════════════\n`;
        text += `RESUMEN\n`;
        text += `═════════════════════════════════════════\n`;
        text += `Total de eventos: ${reportData.value.summary.total_events}\n`;
        text += `Transiciones de fase: ${reportData.value.summary.phase_transitions}\n`;
        text += `Cambios de configuración: ${reportData.value.summary.config_changes}\n`;
        text += `Usuarios únicos: ${reportData.value.summary.unique_users}\n`;
        text += `\n═════════════════════════════════════════\n`;
        text += `EVENTOS\n`;
        text += `═════════════════════════════════════════\n`;
        reportData.value.events.forEach((event) => {
            text += `\n[${event.timestamp}] ${event.action.toUpperCase()}\n`;
            text += `Usuario: ${event.user || 'Sistema'}\n`;
            if (event.phase_from)
                text += `Transición: ${event.phase_from} → ${event.phase_to}\n`;
            if (event.reason) text += `Razón: ${event.reason}\n`;
        });
        return text;
    }
};

const scheduleExport = async () => {
    if (!scheduleEmail.value) {
        alert('Por favor ingresa un correo electrónico');
        return;
    }

    try {
        const response = await fetch(
            '/api/deployment/verification/schedule-report',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token':
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    email: scheduleEmail.value,
                    frequency: scheduleFrequency.value,
                    format: exportFormat.value,
                }),
            },
        );

        if (response.ok) {
            alert('✓ Exportación programada exitosamente');
        }
    } catch (error) {
        console.error('Failed to schedule export:', error);
        alert('Error al programar exportación');
    }
};
</script>
