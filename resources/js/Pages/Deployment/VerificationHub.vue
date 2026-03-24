<script setup lang="ts">
import AuditLogExplorer from '@/components/Verification/AuditLogExplorer.vue';
import ChannelConfig from '@/components/Verification/ChannelConfig.vue';
import ComplianceReportGenerator from '@/components/Verification/ComplianceReportGenerator.vue';
import DryRunSimulator from '@/components/Verification/DryRunSimulator.vue';
import NotificationCenter from '@/components/Verification/NotificationCenter.vue';
import SchedulerStatus from '@/components/Verification/SchedulerStatus.vue';
import SetupWizard from '@/components/Verification/SetupWizard.vue';
import TransitionReadiness from '@/components/Verification/TransitionReadiness.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const activeTab = ref('overview');

const tabs = [
    { id: 'overview', label: '📊 Resumen', icon: 'chart-line' },
    { id: 'control', label: '🎮 Control', icon: 'gear' },
    { id: 'notifications', label: '🔔 Notificaciones', icon: 'bell' },
    { id: 'config', label: '⚙️ Configuración', icon: 'sliders-horizontal' },
    { id: 'audit', label: '📋 Auditoría', icon: 'clock' },
];
</script>

<template>
    <Head title="Verification Hub - Panel de Control" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold">🔍 Verification Hub</h1>
                    <p class="mt-2 text-muted-foreground">
                        Panel centralizado de monitoreo y control del sistema de
                        verificación
                    </p>
                </div>

                <!-- Navigation Tabs -->
                <div
                    class="mb-6 flex flex-wrap gap-2 border-b border-border pb-2"
                >
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        :class="[
                            'rounded-t-lg px-4 py-2 text-sm font-medium transition-colors',
                            activeTab === tab.id
                                ? 'bg-primary text-primary-foreground'
                                : 'text-muted-foreground hover:text-foreground',
                        ]"
                    >
                        {{ tab.label }}
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="space-y-6">
                    <!-- Overview Tab -->
                    <div v-show="activeTab === 'overview'" class="space-y-6">
                        <h2 class="text-xl font-semibold">
                            📊 Estado General del Sistema
                        </h2>

                        <!-- Quick Status Cards -->
                        <div class="grid grid-cols-4 gap-4">
                            <div
                                class="rounded-lg border border-border bg-card p-4"
                            >
                                <p
                                    class="text-xs tracking-wide text-muted-foreground uppercase"
                                >
                                    Fase Actual
                                </p>
                                <p class="mt-1 text-2xl font-bold">Silent</p>
                            </div>
                            <div
                                class="rounded-lg border border-border bg-card p-4"
                            >
                                <p
                                    class="text-xs tracking-wide text-muted-foreground uppercase"
                                >
                                    Confianza Promedio
                                </p>
                                <p class="mt-1 text-2xl font-bold">78%</p>
                            </div>
                            <div
                                class="rounded-lg border border-border bg-card p-4"
                            >
                                <p
                                    class="text-xs tracking-wide text-muted-foreground uppercase"
                                >
                                    Tasa de Error
                                </p>
                                <p class="mt-1 text-2xl font-bold">25%</p>
                            </div>
                            <div
                                class="rounded-lg border border-border bg-card p-4"
                            >
                                <p
                                    class="text-xs tracking-wide text-muted-foreground uppercase"
                                >
                                    Notificaciones
                                </p>
                                <p
                                    class="mt-1 text-2xl font-bold text-orange-500"
                                >
                                    12
                                </p>
                            </div>
                        </div>

                        <!-- Scheduler Status & Transition Readiness -->
                        <div class="grid grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <h3 class="mb-4 text-lg font-semibold">
                                    ⏰ Estado del Scheduler
                                </h3>
                                <SchedulerStatus />
                            </div>
                            <div class="col-span-2">
                                <h3 class="mb-4 text-lg font-semibold">
                                    📊 Readiness para Transición
                                </h3>
                                <TransitionReadiness />
                            </div>
                        </div>
                    </div>

                    <!-- Control Center Tab -->
                    <div v-show="activeTab === 'control'" class="space-y-6">
                        <h2 class="text-xl font-semibold">
                            🎮 Centro de Control
                        </h2>

                        <div class="grid grid-cols-2 gap-6">
                            <!-- Dry Run Simulator -->
                            <div
                                class="col-span-1 rounded-lg border border-border bg-card p-6"
                            >
                                <h3 class="mb-4 font-semibold">
                                    🧪 Simulador de Transiciones
                                </h3>
                                <DryRunSimulator />
                            </div>

                            <!-- Setup Wizard -->
                            <div
                                class="col-span-1 rounded-lg border border-border bg-card p-6"
                            >
                                <h3 class="mb-4 font-semibold">
                                    🪄 Asistente de Configuración
                                </h3>
                                <SetupWizard />
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="grid grid-cols-2 gap-4">
                            <button
                                class="flex h-24 flex-col justify-center rounded-lg border border-border bg-card p-4 text-left transition-colors hover:border-primary"
                            >
                                <div class="mb-2 text-2xl">🔄</div>
                                <h3 class="font-semibold">
                                    Ejecutar Scheduler Ahora
                                </h3>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    Fuerza evaluación inmediata
                                </p>
                            </button>

                            <button
                                class="flex h-24 flex-col justify-center rounded-lg border border-border bg-card p-4 text-left transition-colors hover:border-primary"
                            >
                                <div class="mb-2 text-2xl">🔔</div>
                                <h3 class="font-semibold">
                                    Test de Notificaciones
                                </h3>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    Prueba todos los canales
                                </p>
                            </button>
                        </div>

                        <!-- System Controls -->
                        <div
                            class="rounded-lg border border-border bg-card p-4"
                        >
                            <h3 class="mb-4 font-semibold">
                                🔧 Controles de Activación
                            </h3>
                            <div class="space-y-3">
                                <label
                                    class="flex cursor-pointer items-center gap-3"
                                >
                                    <input
                                        type="checkbox"
                                        checked
                                        class="rounded"
                                    />
                                    <span>Scheduler automático habilitado</span>
                                </label>
                                <label
                                    class="flex cursor-pointer items-center gap-3"
                                >
                                    <input
                                        type="checkbox"
                                        checked
                                        class="rounded"
                                    />
                                    <span>Notificaciones habilitadas</span>
                                </label>
                                <label
                                    class="flex cursor-pointer items-center gap-3"
                                >
                                    <input type="checkbox" class="rounded" />
                                    <span>Modo mantenimiento (pausa todo)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications Tab -->
                    <div
                        v-show="activeTab === 'notifications'"
                        class="space-y-6"
                    >
                        <h2 class="text-xl font-semibold">
                            🔔 Centro de Notificaciones
                        </h2>
                        <NotificationCenter />
                    </div>

                    <!-- Configuration Tab -->
                    <div v-show="activeTab === 'config'" class="space-y-6">
                        <h2 class="text-xl font-semibold">
                            ⚙️ Configuración de Canales
                        </h2>
                        <ChannelConfig />
                    </div>

                    <!-- Audit Tab -->
                    <div v-show="activeTab === 'audit'" class="space-y-6">
                        <h2 class="text-xl font-semibold">
                            📋 Registro de Auditoría
                        </h2>

                        <!-- Audit Log Explorer -->
                        <div
                            class="rounded-lg border border-border bg-card p-6"
                        >
                            <AuditLogExplorer />
                        </div>

                        <!-- Compliance Reports -->
                        <div
                            class="rounded-lg border border-border bg-card p-6"
                        >
                            <h3 class="mb-4 text-lg font-semibold">
                                📊 Generador de Reportes de Cumplimiento
                            </h3>
                            <ComplianceReportGenerator />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
