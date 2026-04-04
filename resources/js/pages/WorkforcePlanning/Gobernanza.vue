<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';

interface RiskSemaphore {
    risk_level: 'verde' | 'amarillo' | 'rojo';
    risk_score: number;
    risk_factors: string[];
}

interface Summary {
    total_actions: number;
    completed_actions: number;
    avg_progress_pct: number;
    overdue_actions: number;
}

interface BudgetSummary {
    total_budget: number;
    total_actual_cost: number;
    variance_pct: number;
    over_budget: boolean;
}

interface Alert {
    type:
        | 'overdue'
        | 'blocked'
        | 'budget'
        | 'progress'
        | 'hybrid_talent'
        | string;
    message: string;
}

interface ByLeverRow {
    lever: string;
    count: number;
    avg_progress_pct: number;
}

interface ByUnitRow {
    unit_name: string;
    count: number;
    avg_progress_pct: number;
}

interface DashboardData {
    scenario_id: number;
    risk_semaphore: RiskSemaphore;
    summary: Summary;
    budget_summary: BudgetSummary;
    alerts: Alert[];
    by_lever: ByLeverRow[];
    by_unit: ByUnitRow[];
}

const scenarioId = ref<string>('');
const dashboard = ref<DashboardData | null>(null);
const loading = ref(false);
const error = ref<string | null>(null);

const semaphoreColor: Record<string, string> = {
    verde: 'success',
    amarillo: 'warning',
    rojo: 'error',
};

const alertIcons: Record<string, string> = {
    overdue: 'mdi-clock-alert-outline',
    blocked: 'mdi-alert-outline',
    budget: 'mdi-currency-usd',
    progress: 'mdi-chart-line',
    hybrid_talent: 'mdi-account-group-outline',
};

const alertColors: Record<string, string> = {
    overdue: 'warning',
    blocked: 'error',
    budget: 'error',
    progress: 'info',
    hybrid_talent: 'purple',
};

const leverTableHeaders = [
    { title: 'Palanca', key: 'lever' },
    { title: 'Acciones', key: 'count' },
    { title: 'Progreso Prom.', key: 'avg_progress_pct' },
];

const unitTableHeaders = [
    { title: 'Unidad', key: 'unit_name' },
    { title: 'Acciones', key: 'count' },
    { title: 'Progreso Prom.', key: 'avg_progress_pct' },
];

async function loadDashboard() {
    if (!scenarioId.value) return;
    loading.value = true;
    error.value = null;
    dashboard.value = null;
    try {
        const res = await fetch(
            `/api/workforce-planning/scenarios/${scenarioId.value}/execution-dashboard`,
            { headers: { 'X-Requested-With': 'XMLHttpRequest' } },
        );
        if (!res.ok) throw new Error(`Error ${res.status}`);
        const data = await res.json();
        dashboard.value = data.data ?? data;
    } catch (e: any) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <AppLayout title="Gobernanza — Dashboard de Ejecución">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2
                        class="text-3xl font-black tracking-tight text-white drop-shadow-md"
                    >
                        <span class="mr-3">📊</span>Gobernanza — Dashboard de
                        Ejecución
                    </h2>
                    <p class="mt-2 text-sm text-white/50">
                        Fase 3 — Seguimiento de ejecución con semáforo de riesgo
                        y budget tracking
                    </p>
                </div>
            </div>
        </template>

        <div class="mt-6 space-y-6 p-4">
            <!-- Scenario selector -->
            <v-card class="dark:bg-gray-800" elevation="2">
                <v-card-text>
                    <div class="flex flex-wrap items-center gap-4">
                        <v-text-field
                            v-model="scenarioId"
                            label="ID de Escenario"
                            placeholder="Ingresa el ID del escenario"
                            variant="outlined"
                            density="compact"
                            hide-details
                            class="max-w-xs"
                        />
                        <v-btn
                            color="primary"
                            variant="elevated"
                            :loading="loading"
                            :disabled="!scenarioId"
                            prepend-icon="mdi-refresh"
                            @click="loadDashboard"
                        >
                            Cargar
                        </v-btn>
                    </div>
                </v-card-text>
            </v-card>

            <!-- Error -->
            <v-alert
                v-if="error"
                type="error"
                variant="tonal"
                closable
                @click:close="error = null"
            >
                {{ error }}
            </v-alert>

            <!-- Loading -->
            <div v-if="loading" class="flex justify-center py-12">
                <v-progress-circular indeterminate color="primary" size="48" />
            </div>

            <template v-else-if="dashboard">
                <!-- Semáforo card -->
                <v-card
                    class="dark:bg-gray-800"
                    :color="semaphoreColor[dashboard.risk_semaphore.risk_level]"
                    variant="tonal"
                    elevation="3"
                >
                    <v-card-text>
                        <div class="flex flex-wrap items-center gap-6">
                            <div class="flex flex-col items-center gap-1">
                                <v-chip
                                    :color="
                                        semaphoreColor[
                                            dashboard.risk_semaphore.risk_level
                                        ]
                                    "
                                    variant="flat"
                                    size="x-large"
                                    class="text-2xl font-black uppercase"
                                >
                                    {{ dashboard.risk_semaphore.risk_level }}
                                </v-chip>
                                <span
                                    class="text-xs font-semibold dark:text-white"
                                    >Nivel de Riesgo</span
                                >
                            </div>
                            <div>
                                <p class="text-4xl font-black dark:text-white">
                                    {{ dashboard.risk_semaphore.risk_score }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-300"
                                >
                                    Risk Score
                                </p>
                            </div>
                            <div class="flex-1">
                                <p
                                    class="mb-1 text-sm font-semibold dark:text-white"
                                >
                                    Factores de riesgo:
                                </p>
                                <ul class="list-disc pl-4">
                                    <li
                                        v-for="(factor, i) in dashboard
                                            .risk_semaphore.risk_factors"
                                        :key="i"
                                        class="text-sm dark:text-gray-300"
                                    >
                                        {{ factor }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </v-card-text>
                </v-card>

                <!-- Stats row -->
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <v-card class="text-center dark:bg-gray-800" elevation="2">
                        <v-card-text>
                            <p class="text-3xl font-black dark:text-white">
                                {{ dashboard.summary.total_actions }}
                            </p>
                            <p
                                class="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-gray-400"
                            >
                                Total Acciones
                            </p>
                        </v-card-text>
                    </v-card>
                    <v-card class="text-center dark:bg-gray-800" elevation="2">
                        <v-card-text>
                            <p
                                class="text-success text-3xl font-black dark:text-green-400"
                            >
                                {{ dashboard.summary.completed_actions }}
                            </p>
                            <p
                                class="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-gray-400"
                            >
                                Completadas
                            </p>
                        </v-card-text>
                    </v-card>
                    <v-card class="text-center dark:bg-gray-800" elevation="2">
                        <v-card-text>
                            <p class="text-3xl font-black text-blue-500">
                                {{ dashboard.summary.avg_progress_pct }}%
                            </p>
                            <p
                                class="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-gray-400"
                            >
                                Progreso Prom.
                            </p>
                        </v-card-text>
                    </v-card>
                    <v-card class="text-center dark:bg-gray-800" elevation="2">
                        <v-card-text>
                            <p
                                class="text-3xl font-black"
                                :class="
                                    dashboard.summary.overdue_actions > 0
                                        ? 'text-red-500'
                                        : 'dark:text-white'
                                "
                            >
                                {{ dashboard.summary.overdue_actions }}
                            </p>
                            <p
                                class="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-gray-400"
                            >
                                Vencidas
                            </p>
                        </v-card-text>
                    </v-card>
                </div>

                <!-- Budget card -->
                <v-card
                    class="dark:bg-gray-800"
                    :color="
                        dashboard.budget_summary.over_budget
                            ? 'error'
                            : undefined
                    "
                    :variant="
                        dashboard.budget_summary.over_budget
                            ? 'tonal'
                            : 'elevated'
                    "
                    elevation="2"
                >
                    <v-card-title class="dark:text-white">
                        <v-icon class="mr-2">mdi-currency-usd</v-icon>
                        Resumen de Presupuesto
                        <v-chip
                            v-if="dashboard.budget_summary.over_budget"
                            color="error"
                            size="small"
                            class="ml-2"
                        >
                            Over Budget
                        </v-chip>
                    </v-card-title>
                    <v-card-text>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <p
                                    class="text-xs text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Presupuesto Total
                                </p>
                                <p class="text-2xl font-bold dark:text-white">
                                    ${{
                                        dashboard.budget_summary.total_budget.toLocaleString()
                                    }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Costo Real
                                </p>
                                <p class="text-2xl font-bold dark:text-white">
                                    ${{
                                        dashboard.budget_summary.total_actual_cost.toLocaleString()
                                    }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Variación
                                </p>
                                <p
                                    class="text-2xl font-bold"
                                    :class="
                                        dashboard.budget_summary.variance_pct >
                                        0
                                            ? 'text-red-500'
                                            : 'text-green-500'
                                    "
                                >
                                    {{
                                        dashboard.budget_summary.variance_pct >
                                        0
                                            ? '+'
                                            : ''
                                    }}{{
                                        dashboard.budget_summary.variance_pct
                                    }}%
                                </p>
                            </div>
                        </div>
                    </v-card-text>
                </v-card>

                <!-- Alerts -->
                <v-card
                    v-if="dashboard.alerts?.length"
                    class="dark:bg-gray-800"
                    elevation="2"
                >
                    <v-card-title class="dark:text-white">
                        <v-icon class="mr-2">mdi-bell-outline</v-icon>
                        Alertas ({{ dashboard.alerts.length }})
                    </v-card-title>
                    <v-card-text class="space-y-2">
                        <v-alert
                            v-for="(alert, i) in dashboard.alerts"
                            :key="i"
                            :type="alertColors[alert.type] as any"
                            :icon="
                                alertIcons[alert.type] ??
                                'mdi-alert-circle-outline'
                            "
                            variant="tonal"
                            density="compact"
                        >
                            {{ alert.message }}
                        </v-alert>
                    </v-card-text>
                </v-card>

                <!-- By Lever & By Unit tables -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <v-card class="dark:bg-gray-800" elevation="2">
                        <v-card-title class="dark:text-white"
                            >Por Palanca</v-card-title
                        >
                        <v-data-table
                            :headers="leverTableHeaders"
                            :items="dashboard.by_lever"
                            density="compact"
                            hide-default-footer
                            class="dark:bg-gray-800"
                        >
                            <template #item.avg_progress_pct="{ item }">
                                <div class="flex items-center gap-2">
                                    <v-progress-linear
                                        :model-value="item.avg_progress_pct"
                                        color="primary"
                                        rounded
                                        height="6"
                                        class="flex-1"
                                    />
                                    <span class="text-xs dark:text-gray-300"
                                        >{{ item.avg_progress_pct }}%</span
                                    >
                                </div>
                            </template>
                        </v-data-table>
                    </v-card>

                    <v-card class="dark:bg-gray-800" elevation="2">
                        <v-card-title class="dark:text-white"
                            >Por Unidad</v-card-title
                        >
                        <v-data-table
                            :headers="unitTableHeaders"
                            :items="dashboard.by_unit"
                            density="compact"
                            hide-default-footer
                            class="dark:bg-gray-800"
                        >
                            <template #item.avg_progress_pct="{ item }">
                                <div class="flex items-center gap-2">
                                    <v-progress-linear
                                        :model-value="item.avg_progress_pct"
                                        color="secondary"
                                        rounded
                                        height="6"
                                        class="flex-1"
                                    />
                                    <span class="text-xs dark:text-gray-300"
                                        >{{ item.avg_progress_pct }}%</span
                                    >
                                </div>
                            </template>
                        </v-data-table>
                    </v-card>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
