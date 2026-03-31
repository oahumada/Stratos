<template>
    <div
        class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-8"
    >
        <!-- Header -->
        <div class="mb-12">
            <div class="mb-2 flex items-center gap-4">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-orange-500 to-red-500"
                >
                    <PhBell class="h-6 w-6 text-white" weight="fill" />
                </div>
                <h1 class="text-4xl font-bold text-white">
                    Centro de Control de Alertas
                </h1>
            </div>
            <p class="text-lg text-slate-400">
                Configurar umbrales, monitorear historial y gestionar
                escalaciones
            </p>
        </div>

        <!-- Quick Stats -->
        <div class="mb-12 grid grid-cols-1 gap-4 md:grid-cols-4">
            <div
                class="rounded-lg border border-white/10 bg-white/5 p-6 backdrop-blur"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-400">
                            Umbrales Activos
                        </p>
                        <p class="mt-2 text-3xl font-bold text-white">
                            {{ stats.activeThresholds }}
                        </p>
                    </div>
                    <PhCheckCircle
                        class="h-10 w-10 text-green-500/30"
                        weight="fill"
                    />
                </div>
            </div>

            <div
                class="rounded-lg border border-white/10 bg-white/5 p-6 backdrop-blur"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-400">
                            Alertas Críticas
                        </p>
                        <p class="mt-2 text-3xl font-bold text-red-400">
                            {{ stats.criticalAlerts }}
                        </p>
                    </div>
                    <PhWarning
                        class="h-10 w-10 text-red-500/30"
                        weight="fill"
                    />
                </div>
            </div>

            <div
                class="rounded-lg border border-white/10 bg-white/5 p-6 backdrop-blur"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-400">
                            Sin Reconocer
                        </p>
                        <p class="mt-2 text-3xl font-bold text-yellow-400">
                            {{ stats.unacknowledged }}
                        </p>
                    </div>
                    <PhClockClockwise
                        class="h-10 w-10 text-yellow-500/30"
                        weight="fill"
                    />
                </div>
            </div>

            <div
                class="rounded-lg border border-white/10 bg-white/5 p-6 backdrop-blur"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-400">
                            Tasa Reconocimiento
                        </p>
                        <p class="mt-2 text-3xl font-bold text-blue-400">
                            {{ stats.ackRate }}%
                        </p>
                    </div>
                    <PhTrendUp
                        class="h-10 w-10 text-blue-500/30"
                        weight="fill"
                    />
                </div>
            </div>
        </div>

        <!-- Main Options Grid -->
        <div class="mb-12 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Option 1: Configuration -->
            <div
                @click="activeTab = 'configuration'"
                :class="[
                    'group relative cursor-pointer overflow-hidden rounded-xl border-2 p-8 transition-all duration-300',
                    activeTab === 'configuration'
                        ? 'border-indigo-500 bg-gradient-to-br from-indigo-500/20 via-slate-800 to-slate-900 shadow-lg shadow-indigo-500/20'
                        : 'border-white/10 bg-white/5 hover:border-indigo-500/50 hover:bg-white/10',
                ]"
            >
                <div
                    class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                />

                <div class="relative z-10">
                    <div
                        class="mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500/80 to-indigo-600 shadow-lg"
                    >
                        <PhGear class="h-7 w-7 text-white" weight="fill" />
                    </div>

                    <h2 class="mb-2 text-2xl font-bold text-white">
                        Configuración
                    </h2>
                    <p class="mb-6 text-sm leading-relaxed text-slate-300">
                        Define umbrales de alertas por métrica. Establece
                        niveles de severidad y activa/desactiva monitoreo en
                        tiempo real.
                    </p>

                    <div class="mb-4 space-y-2">
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-indigo-400"
                                weight="bold"
                            />
                            <span>Crear nuevos umbrales</span>
                        </div>
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-indigo-400"
                                weight="bold"
                            />
                            <span>Editar configuración existente</span>
                        </div>
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-indigo-400"
                                weight="bold"
                            />
                            <span>Validar métricas y valores</span>
                        </div>
                    </div>

                    <button
                        class="w-full rounded-lg bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-3 font-semibold text-white shadow-lg transition hover:from-indigo-600 hover:to-indigo-700 hover:shadow-indigo-500/20"
                    >
                        Acceder →
                    </button>
                </div>
            </div>

            <!-- Option 2: History -->
            <div
                @click="activeTab = 'history'"
                :class="[
                    'group relative cursor-pointer overflow-hidden rounded-xl border-2 p-8 transition-all duration-300',
                    activeTab === 'history'
                        ? 'border-emerald-500 bg-gradient-to-br from-emerald-500/20 via-slate-800 to-slate-900 shadow-lg shadow-emerald-500/20'
                        : 'border-white/10 bg-white/5 hover:border-emerald-500/50 hover:bg-white/10',
                ]"
            >
                <div
                    class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                />

                <div class="relative z-10">
                    <div
                        class="mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500/80 to-emerald-600 shadow-lg"
                    >
                        <PhClockClockwise
                            class="h-7 w-7 text-white"
                            weight="fill"
                        />
                    </div>

                    <h2 class="mb-2 text-2xl font-bold text-white">
                        Historial
                    </h2>
                    <p class="mb-6 text-sm leading-relaxed text-slate-300">
                        Visualiza todas las alertas disparadas. Filtra por
                        severidad, estado y rango de fechas. Reconoce y resuelve
                        manualmente.
                    </p>

                    <div class="mb-4 space-y-2">
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-emerald-400"
                                weight="bold"
                            />
                            <span>Ver todas las alertas disparadas</span>
                        </div>
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-emerald-400"
                                weight="bold"
                            />
                            <span>Filtrar por severidad y estado</span>
                        </div>
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-emerald-400"
                                weight="bold"
                            />
                            <span>Reconocer, resolver, silenciar alertas</span>
                        </div>
                    </div>

                    <button
                        class="w-full rounded-lg bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-3 font-semibold text-white shadow-lg transition hover:from-emerald-600 hover:to-emerald-700 hover:shadow-emerald-500/20"
                    >
                        Acceder →
                    </button>
                </div>
            </div>

            <!-- Option 3: Escalation -->
            <div
                @click="activeTab = 'escalation'"
                :class="[
                    'group relative cursor-pointer overflow-hidden rounded-xl border-2 p-8 transition-all duration-300',
                    activeTab === 'escalation'
                        ? 'border-purple-500 bg-gradient-to-br from-purple-500/20 via-slate-800 to-slate-900 shadow-lg shadow-purple-500/20'
                        : 'border-white/10 bg-white/5 hover:border-purple-500/50 hover:bg-white/10',
                ]"
            >
                <div
                    class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                />

                <div class="relative z-10">
                    <div
                        class="mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-gradient-to-br from-purple-500/80 to-purple-600 shadow-lg"
                    >
                        <PhArrowUp class="h-7 w-7 text-white" weight="fill" />
                    </div>

                    <h2 class="mb-2 text-2xl font-bold text-white">
                        Escalación
                    </h2>
                    <p class="mb-6 text-sm leading-relaxed text-slate-300">
                        Configura políticas de escalación por severidad. Define
                        receptores, métodos de notificación y tiempos de delay.
                    </p>

                    <div class="mb-4 space-y-2">
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-purple-400"
                                weight="bold"
                            />
                            <span>Crear cadenas de escalación</span>
                        </div>
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-purple-400"
                                weight="bold"
                            />
                            <span
                                >Configurar notificaciones (email, Slack)</span
                            >
                        </div>
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-purple-400"
                                weight="bold"
                            />
                            <span>Gestionar receptores y niveles</span>
                        </div>
                    </div>

                    <button
                        class="w-full rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 px-4 py-3 font-semibold text-white shadow-lg transition hover:from-purple-600 hover:to-purple-700 hover:shadow-purple-500/20"
                    >
                        Acceder →
                    </button>
                </div>
            </div>
        </div>

        <!-- Audit Trail Grid -->
        <div class="mb-2">
            <h2 class="mb-4 text-xl font-bold text-slate-300">
                Seguimiento y Auditoría
            </h2>
        </div>
        <div class="mb-12 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Option 4: Audit Trail -->
            <div
                @click="activeTab = 'auditTrail'"
                :class="[
                    'group relative cursor-pointer overflow-hidden rounded-xl border-2 p-8 transition-all duration-300',
                    activeTab === 'auditTrail'
                        ? 'border-cyan-500 bg-gradient-to-br from-cyan-500/20 via-slate-800 to-slate-900 shadow-lg shadow-cyan-500/20'
                        : 'border-white/10 bg-white/5 hover:border-cyan-500/50 hover:bg-white/10',
                ]"
            >
                <div
                    class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                />

                <div class="relative z-10">
                    <div
                        class="mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-gradient-to-br from-cyan-500/80 to-cyan-600 shadow-lg"
                    >
                        <PhList class="h-7 w-7 text-white" weight="fill" />
                    </div>

                    <h2 class="mb-2 text-2xl font-bold text-white">
                        Registro de Cambios
                    </h2>
                    <p class="mb-6 text-sm leading-relaxed text-slate-300">
                        Visualiza el historial completo de cambios en
                        configuración, umbrales y políticas. Auditoría con
                        timestamps y usuarios.
                    </p>

                    <div class="mb-4 space-y-2">
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-cyan-400"
                                weight="bold"
                            />
                            <span>Timeline interactivo de cambios</span>
                        </div>
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-cyan-400"
                                weight="bold"
                            />
                            <span>Filtrar por acción y usuario</span>
                        </div>
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-cyan-400"
                                weight="bold"
                            />
                            <span>Detalles de cambios antes/después</span>
                        </div>
                    </div>

                    <button
                        class="w-full rounded-lg bg-gradient-to-r from-cyan-500 to-cyan-600 px-4 py-3 font-semibold text-white shadow-lg transition hover:from-cyan-600 hover:to-cyan-700 hover:shadow-cyan-500/20"
                    >
                        Acceder →
                    </button>
                </div>
            </div>

            <!-- Option 5: Audit Export -->
            <div
                @click="activeTab = 'auditExport'"
                :class="[
                    'group relative cursor-pointer overflow-hidden rounded-xl border-2 p-8 transition-all duration-300',
                    activeTab === 'auditExport'
                        ? 'border-amber-500 bg-gradient-to-br from-amber-500/20 via-slate-800 to-slate-900 shadow-lg shadow-amber-500/20'
                        : 'border-white/10 bg-white/5 hover:border-amber-500/50 hover:bg-white/10',
                ]"
            >
                <div
                    class="absolute inset-0 bg-gradient-to-br from-amber-500/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                />

                <div class="relative z-10">
                    <div
                        class="mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500/80 to-amber-600 shadow-lg"
                    >
                        <PhDownload class="h-7 w-7 text-white" weight="fill" />
                    </div>

                    <h2 class="mb-2 text-2xl font-bold text-white">
                        Exportar Auditoría
                    </h2>
                    <p class="mb-6 text-sm leading-relaxed text-slate-300">
                        Descarga reportes de cambios en formato CSV. Genera
                        informes personalizados por período, tipo de acción y
                        usuarios involucrados.
                    </p>

                    <div class="mb-4 space-y-2">
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-amber-400"
                                weight="bold"
                            />
                            <span>Exportar a CSV con preview</span>
                        </div>
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-amber-400"
                                weight="bold"
                            />
                            <span>Copiar al portapapeles</span>
                        </div>
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-amber-400"
                                weight="bold"
                            />
                            <span>Reportes por período</span>
                        </div>
                    </div>

                    <button
                        class="w-full rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-3 font-semibold text-white shadow-lg transition hover:from-amber-600 hover:to-amber-700 hover:shadow-amber-500/20"
                    >
                        Acceder →
                    </button>
                </div>
            </div>

            <!-- Option 6: Audit Heatmap -->
            <div
                @click="activeTab = 'auditHeatmap'"
                :class="[
                    'group relative cursor-pointer overflow-hidden rounded-xl border-2 p-8 transition-all duration-300',
                    activeTab === 'auditHeatmap'
                        ? 'border-rose-500 bg-gradient-to-br from-rose-500/20 via-slate-800 to-slate-900 shadow-lg shadow-rose-500/20'
                        : 'border-white/10 bg-white/5 hover:border-rose-500/50 hover:bg-white/10',
                ]"
            >
                <div
                    class="absolute inset-0 bg-gradient-to-br from-rose-500/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                />

                <div class="relative z-10">
                    <div
                        class="mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-gradient-to-br from-rose-500/80 to-rose-600 shadow-lg"
                    >
                        <PhChartBar class="h-7 w-7 text-white" weight="fill" />
                    </div>

                    <h2 class="mb-2 text-2xl font-bold text-white">
                        Análisis de Actividad
                    </h2>
                    <p class="mb-6 text-sm leading-relaxed text-slate-300">
                        Visualiza patrones de actividad horaria y tendencias
                        diarias. Identifica picos de cambios y períodos críticos
                        de uso del sistema.
                    </p>

                    <div class="mb-4 space-y-2">
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-rose-400"
                                weight="bold"
                            />
                            <span>Heatmap horario (24h)</span>
                        </div>
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-rose-400"
                                weight="bold"
                            />
                            <span>Tendencias diarias (7 días)</span>
                        </div>
                        <div
                            class="flex items-center gap-2 text-sm text-slate-400"
                        >
                            <PhCheck
                                class="h-4 w-4 text-rose-400"
                                weight="bold"
                            />
                            <span>Estadísticas de uso y picos</span>
                        </div>
                    </div>

                    <button
                        class="w-full rounded-lg bg-gradient-to-r from-rose-500 to-rose-600 px-4 py-3 font-semibold text-white shadow-lg transition hover:from-rose-600 hover:to-rose-700 hover:shadow-rose-500/20"
                    >
                        Acceder →
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="rounded-xl border border-white/10 bg-white/5 backdrop-blur">
            <div class="border-b border-white/10 px-8 py-4">
                <h3 class="text-lg font-bold text-white">
                    <span v-if="activeTab === 'configuration'"
                        >⚙️ Configuración de Umbrales</span
                    >
                    <span v-else-if="activeTab === 'history'"
                        >📊 Historial de Alertas</span
                    >
                    <span v-else-if="activeTab === 'escalation'"
                        >📤 Matriz de Escalación</span
                    >
                    <span v-else-if="activeTab === 'auditTrail'"
                        >📋 Registro de Cambios</span
                    >
                    <span v-else-if="activeTab === 'auditExport'"
                        >⬇️ Exportar Auditoría</span
                    >
                    <span v-else-if="activeTab === 'auditHeatmap'"
                        >🔥 Análisis de Actividad</span
                    >
                </h3>
            </div>

            <div class="p-8">
                <!-- Configuration Tab -->
                <AlertThresholdForm
                    v-show="activeTab === 'configuration'"
                    @submit="handleThresholdSubmit"
                    @cancel="resetForm"
                />

                <!-- History Tab -->
                <AlertHistoryTable
                    v-show="activeTab === 'history'"
                    @acknowledge="handleAcknowledge"
                    @resolve="handleResolve"
                    @mute="handleMute"
                />

                <!-- Escalation Tab -->
                <EscalationPolicyMatrix
                    v-show="activeTab === 'escalation'"
                    @edit="handleEditPolicy"
                    @delete="handleDeletePolicy"
                    @addLevel="handleAddLevel"
                />

                <!-- Audit Trail Tab -->
                <AuditTrail v-show="activeTab === 'auditTrail'" />

                <!-- Audit Export Tab -->
                <AuditExport v-show="activeTab === 'auditExport'" />

                <!-- Audit Heatmap Tab -->
                <AuditHeatmap v-show="activeTab === 'auditHeatmap'" />
            </div>
        </div>

        <!-- Footer Info -->
        <div class="mt-12 rounded-lg border border-white/10 bg-white/5 p-6">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div>
                    <h4 class="mb-2 font-semibold text-white">Documentación</h4>
                    <p class="text-sm text-slate-400">
                        Consulta la guía completa de alertas, auditoría y
                        escalación en la documentación del proyecto.
                    </p>
                </div>
                <div>
                    <h4 class="mb-2 font-semibold text-white">Soporte</h4>
                    <p class="text-sm text-slate-400">
                        Contacta al equipo de operaciones para asistencia con
                        configuraciones complejas de alertas y auditoría.
                    </p>
                </div>
                <div>
                    <h4 class="mb-2 font-semibold text-white">
                        Últimas Actualizaciones
                    </h4>
                    <p class="text-sm text-slate-400">
                        Centro de Control v1.3 - Audit Trail integrado (Mar 27,
                        2026)
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {
    PhArrowUp,
    PhBell,
    PhChartBar,
    PhCheck,
    PhCheckCircle,
    PhClockClockwise,
    PhDownload,
    PhGear,
    PhList,
    PhTrendUp,
    PhWarning,
} from '@phosphor-icons/vue';
import { reactive, ref } from 'vue';

import AlertHistoryTable from '@/components/Admin/AlertHistoryTable.vue';
import AlertThresholdForm from '@/components/Admin/AlertThresholdForm.vue';
import AuditExport from '@/components/Admin/AuditExport.vue';
import AuditHeatmap from '@/components/Admin/AuditHeatmap.vue';
import AuditTrail from '@/components/Admin/AuditTrail.vue';
import EscalationPolicyMatrix from '@/components/Admin/EscalationPolicyMatrix.vue';

const activeTab = ref<
    | 'configuration'
    | 'history'
    | 'escalation'
    | 'auditTrail'
    | 'auditExport'
    | 'auditHeatmap'
>('configuration');

const stats = reactive({
    activeThresholds: 12,
    criticalAlerts: 3,
    unacknowledged: 5,
    ackRate: 87,
});

const handleThresholdSubmit = async (data: any) => {
    console.log('Threshold submitted:', data);
    // API call to create/update threshold
};

const resetForm = () => {
    console.log('Form reset');
};

const handleAcknowledge = async (alertId: string, notes?: string) => {
    console.log('Acknowledging alert:', alertId, notes);
    // API call to acknowledge
};

const handleResolve = async (alertId: string) => {
    console.log('Resolving alert:', alertId);
    // API call to resolve
};

const handleMute = async (alertId: string) => {
    console.log('Muting alert:', alertId);
    // API call to mute
};

const handleEditPolicy = async (policyId: string) => {
    console.log('Editing policy:', policyId);
    // API call to update policy
};

const handleDeletePolicy = async (policyId: string) => {
    console.log('Deleting policy:', policyId);
    // API call to delete policy
};

const handleAddLevel = async (severity: string) => {
    console.log('Adding level for severity:', severity);
    // API call to add escalation level
};
</script>

<style scoped>
/* Smooth transitions */
:deep(.transition) {
    transition: all 0.3s ease;
}
</style>
