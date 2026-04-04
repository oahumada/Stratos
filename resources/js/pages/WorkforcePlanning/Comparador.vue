<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, ref } from 'vue';

// ── Types ──────────────────────────────────────────────────────────────────

interface ScenarioRanking {
    rank: number;
    scenario_id: number;
    coverage_pct: number;
    total_cost: number;
    risk_score: number;
    composite_score: number;
}

interface DimensionMatrix {
    dimension: string;
    [scenarioId: string]: number | string;
}

interface CompareResult {
    ranking: ScenarioRanking[];
    dimension_matrix: DimensionMatrix[];
    winner_scenario_id: number;
}

interface SweepStep {
    variable_value: number;
    coverage_pct: number;
    gap_fte: number;
    estimated_cost: number;
    is_optimal?: boolean;
}

interface SweepResult {
    steps: SweepStep[];
    optimal_step: SweepStep;
    interpretation: string;
    variable: string;
}

// ── State ──────────────────────────────────────────────────────────────────

const activeTab = ref('compare');

// Tab 1 — Compare
const scenarioIdsInput = ref<string>('');
const compareResult = ref<CompareResult | null>(null);
const compareLoading = ref(false);
const compareError = ref<string | null>(null);

const rankingHeaders = [
    { title: 'Rank', key: 'rank' },
    { title: 'Escenario', key: 'scenario_id' },
    { title: 'Cobertura %', key: 'coverage_pct' },
    { title: 'Costo Total', key: 'total_cost' },
    { title: 'Risk Score', key: 'risk_score' },
    { title: 'Score Compuesto', key: 'composite_score' },
];

const scenarioIds = computed<number[]>(() => {
    return scenarioIdsInput.value
        .split(',')
        .map((s) => parseInt(s.trim(), 10))
        .filter((n) => !isNaN(n))
        .slice(0, 4);
});

const dimensionMatrixHeaders = computed(() => {
    if (!compareResult.value?.dimension_matrix?.length) return [];
    const cols = Object.keys(compareResult.value.dimension_matrix[0]).filter(
        (k) => k !== 'dimension',
    );
    return [
        { title: 'Dimensión', key: 'dimension' },
        ...cols.map((c) => ({ title: `Escenario ${c}`, key: c })),
    ];
});

async function compareScenarios() {
    if (scenarioIds.value.length < 2) return;
    compareLoading.value = true;
    compareError.value = null;
    compareResult.value = null;
    try {
        const res = await fetch(
            '/api/workforce-planning/scenarios/compare-multi',
            {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN':
                        (
                            document.querySelector(
                                'meta[name="csrf-token"]',
                            ) as HTMLMetaElement
                        )?.content ?? '',
                },
                body: JSON.stringify({ scenario_ids: scenarioIds.value }),
            },
        );
        if (!res.ok) throw new Error(`Error ${res.status}`);
        const data = await res.json();
        compareResult.value = data.data ?? data;
    } catch (e: any) {
        compareError.value = e.message;
    } finally {
        compareLoading.value = false;
    }
}

// Tab 2 — Sensitivity Sweep
const sweepScenarioId = ref<string>('');
const sweepVariable = ref<string>('productivity_factor');
const sweepMin = ref<number>(0.5);
const sweepMax = ref<number>(1.5);
const sweepSteps = ref<number>(10);
const sweepCostPerGap = ref<number | null>(null);
const sweepResult = ref<SweepResult | null>(null);
const sweepLoading = ref(false);
const sweepError = ref<string | null>(null);

const variableOptions = [
    { title: 'Factor de Productividad', value: 'productivity_factor' },
    { title: 'Cobertura Objetivo %', value: 'coverage_target_pct' },
    { title: 'Factor de Ramp', value: 'ramp_factor' },
];

const sweepHeaders = [
    { title: 'Valor Variable', key: 'variable_value' },
    { title: 'Cobertura %', key: 'coverage_pct' },
    { title: 'Gap FTE', key: 'gap_fte' },
    { title: 'Costo Estimado', key: 'estimated_cost' },
];

async function runSweep() {
    if (!sweepScenarioId.value) return;
    sweepLoading.value = true;
    sweepError.value = null;
    sweepResult.value = null;
    try {
        const body: Record<string, any> = {
            variable: sweepVariable.value,
            min_value: sweepMin.value,
            max_value: sweepMax.value,
            steps: sweepSteps.value,
        };
        if (
            sweepCostPerGap.value !== null &&
            sweepCostPerGap.value !== undefined
        ) {
            body.cost_per_gap_hh = sweepCostPerGap.value;
        }
        const res = await fetch(
            `/api/workforce-planning/scenarios/${sweepScenarioId.value}/sensitivity-sweep`,
            {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN':
                        (
                            document.querySelector(
                                'meta[name="csrf-token"]',
                            ) as HTMLMetaElement
                        )?.content ?? '',
                },
                body: JSON.stringify(body),
            },
        );
        if (!res.ok) throw new Error(`Error ${res.status}`);
        const data = await res.json();
        sweepResult.value = data.data ?? data;
    } catch (e: any) {
        sweepError.value = e.message;
    } finally {
        sweepLoading.value = false;
    }
}

function isOptimalStep(step: SweepStep, result: SweepResult): boolean {
    return (
        step.is_optimal === true ||
        step.variable_value === result.optimal_step?.variable_value
    );
}
</script>

<template>
    <AppLayout title="Comparador Multi-Escenario">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2
                        class="text-3xl font-black tracking-tight text-white drop-shadow-md"
                    >
                        <span class="mr-3">🔬</span>Comparador Multi-Escenario
                    </h2>
                    <p class="mt-2 text-sm text-white/50">
                        Fase 4 — Comparación de escenarios y análisis de
                        sensibilidad
                    </p>
                </div>
            </div>
        </template>

        <div class="mt-6 p-4">
            <v-tabs v-model="activeTab" color="primary" class="mb-6">
                <v-tab value="compare">
                    <v-icon start>mdi-compare</v-icon>
                    Comparar Escenarios
                </v-tab>
                <v-tab value="sweep">
                    <v-icon start>mdi-tune-variant</v-icon>
                    Sensitivity Sweep
                </v-tab>
            </v-tabs>

            <v-tabs-window v-model="activeTab">
                <!-- ── Tab 1: Comparar ─────────────────────────────── -->
                <v-tabs-window-item value="compare">
                    <div class="space-y-6">
                        <v-card class="dark:bg-gray-800" elevation="2">
                            <v-card-title class="dark:text-white"
                                >Seleccionar Escenarios</v-card-title
                            >
                            <v-card-text>
                                <div class="flex flex-wrap items-start gap-4">
                                    <v-text-field
                                        v-model="scenarioIdsInput"
                                        label="IDs de escenarios (hasta 4, separados por coma)"
                                        placeholder="Ej: 1, 2, 3"
                                        variant="outlined"
                                        density="compact"
                                        hide-details
                                        class="min-w-64"
                                    />
                                    <v-btn
                                        color="primary"
                                        variant="elevated"
                                        :loading="compareLoading"
                                        :disabled="scenarioIds.length < 2"
                                        prepend-icon="mdi-compare-horizontal"
                                        @click="compareScenarios"
                                    >
                                        Comparar
                                    </v-btn>
                                </div>
                                <p
                                    class="mt-2 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Se tomarán hasta 4 IDs. Mínimo 2 requeridos.
                                    <span v-if="scenarioIds.length > 0">
                                        IDs detectados:
                                        {{ scenarioIds.join(', ') }}
                                    </span>
                                </p>
                            </v-card-text>
                        </v-card>

                        <v-alert
                            v-if="compareError"
                            type="error"
                            variant="tonal"
                            closable
                            @click:close="compareError = null"
                        >
                            {{ compareError }}
                        </v-alert>

                        <div
                            v-if="compareLoading"
                            class="flex justify-center py-12"
                        >
                            <v-progress-circular
                                indeterminate
                                color="primary"
                                size="48"
                            />
                        </div>

                        <template v-else-if="compareResult">
                            <!-- Winner badge -->
                            <v-card
                                color="success"
                                variant="tonal"
                                elevation="2"
                            >
                                <v-card-text class="flex items-center gap-4">
                                    <v-icon size="40" color="success"
                                        >mdi-trophy-outline</v-icon
                                    >
                                    <div>
                                        <p
                                            class="text-sm font-semibold text-gray-600 dark:text-gray-300"
                                        >
                                            Mejor Escenario
                                        </p>
                                        <p
                                            class="text-3xl font-black text-green-600 dark:text-green-400"
                                        >
                                            Escenario #{{
                                                compareResult.winner_scenario_id
                                            }}
                                        </p>
                                    </div>
                                </v-card-text>
                            </v-card>

                            <!-- Ranking table -->
                            <v-card class="dark:bg-gray-800" elevation="2">
                                <v-card-title class="dark:text-white"
                                    >Ranking</v-card-title
                                >
                                <v-data-table
                                    :headers="rankingHeaders"
                                    :items="compareResult.ranking"
                                    density="compact"
                                    hide-default-footer
                                    class="dark:bg-gray-800"
                                >
                                    <template #item.rank="{ item }">
                                        <v-chip
                                            :color="
                                                item.rank === 1
                                                    ? 'success'
                                                    : item.rank === 2
                                                      ? 'primary'
                                                      : 'default'
                                            "
                                            size="small"
                                            variant="flat"
                                        >
                                            #{{ item.rank }}
                                        </v-chip>
                                    </template>
                                    <template #item.total_cost="{ item }">
                                        ${{ item.total_cost?.toLocaleString() }}
                                    </template>
                                    <template #item.composite_score="{ item }">
                                        <v-chip
                                            color="primary"
                                            size="small"
                                            variant="tonal"
                                        >
                                            {{ item.composite_score }}
                                        </v-chip>
                                    </template>
                                </v-data-table>
                            </v-card>

                            <!-- Dimension matrix -->
                            <v-card
                                v-if="compareResult.dimension_matrix?.length"
                                class="dark:bg-gray-800"
                                elevation="2"
                            >
                                <v-card-title class="dark:text-white"
                                    >Matriz de Dimensiones</v-card-title
                                >
                                <v-data-table
                                    :headers="dimensionMatrixHeaders"
                                    :items="compareResult.dimension_matrix"
                                    density="compact"
                                    hide-default-footer
                                    class="dark:bg-gray-800"
                                />
                            </v-card>
                        </template>
                    </div>
                </v-tabs-window-item>

                <!-- ── Tab 2: Sensitivity Sweep ───────────────────── -->
                <v-tabs-window-item value="sweep">
                    <div class="space-y-6">
                        <v-card class="dark:bg-gray-800" elevation="2">
                            <v-card-title class="dark:text-white"
                                >Parámetros del Sweep</v-card-title
                            >
                            <v-card-text>
                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"
                                >
                                    <v-text-field
                                        v-model="sweepScenarioId"
                                        label="ID de Escenario"
                                        variant="outlined"
                                        density="compact"
                                        hide-details
                                    />
                                    <v-select
                                        v-model="sweepVariable"
                                        :items="variableOptions"
                                        label="Variable"
                                        variant="outlined"
                                        density="compact"
                                        hide-details
                                    />
                                    <v-text-field
                                        v-model.number="sweepMin"
                                        label="Valor Mínimo"
                                        type="number"
                                        variant="outlined"
                                        density="compact"
                                        hide-details
                                    />
                                    <v-text-field
                                        v-model.number="sweepMax"
                                        label="Valor Máximo"
                                        type="number"
                                        variant="outlined"
                                        density="compact"
                                        hide-details
                                    />
                                    <v-text-field
                                        v-model.number="sweepSteps"
                                        label="Pasos"
                                        type="number"
                                        min="2"
                                        max="50"
                                        variant="outlined"
                                        density="compact"
                                        hide-details
                                    />
                                    <v-text-field
                                        v-model.number="sweepCostPerGap"
                                        label="Costo por Gap HH (opcional)"
                                        type="number"
                                        variant="outlined"
                                        density="compact"
                                        hide-details
                                    />
                                </div>
                                <div class="mt-4">
                                    <v-btn
                                        color="primary"
                                        variant="elevated"
                                        :loading="sweepLoading"
                                        :disabled="!sweepScenarioId"
                                        prepend-icon="mdi-play-circle-outline"
                                        @click="runSweep"
                                    >
                                        Ejecutar Sweep
                                    </v-btn>
                                </div>
                            </v-card-text>
                        </v-card>

                        <v-alert
                            v-if="sweepError"
                            type="error"
                            variant="tonal"
                            closable
                            @click:close="sweepError = null"
                        >
                            {{ sweepError }}
                        </v-alert>

                        <div
                            v-if="sweepLoading"
                            class="flex justify-center py-12"
                        >
                            <v-progress-circular
                                indeterminate
                                color="primary"
                                size="48"
                            />
                        </div>

                        <template v-else-if="sweepResult">
                            <!-- Results table -->
                            <v-card class="dark:bg-gray-800" elevation="2">
                                <v-card-title class="dark:text-white">
                                    Resultados — Variable:
                                    {{ sweepResult.variable }}
                                </v-card-title>
                                <v-data-table
                                    :headers="sweepHeaders"
                                    :items="sweepResult.steps"
                                    density="compact"
                                    hide-default-footer
                                    class="dark:bg-gray-800"
                                >
                                    <template #item="{ item }">
                                        <tr
                                            :class="
                                                isOptimalStep(
                                                    item,
                                                    sweepResult!,
                                                )
                                                    ? 'bg-green-50 font-semibold dark:bg-green-900/30'
                                                    : ''
                                            "
                                        >
                                            <td
                                                class="px-4 py-2 dark:text-gray-200"
                                            >
                                                {{ item.variable_value }}
                                                <v-chip
                                                    v-if="
                                                        isOptimalStep(
                                                            item,
                                                            sweepResult!,
                                                        )
                                                    "
                                                    color="success"
                                                    size="x-small"
                                                    class="ml-1"
                                                >
                                                    Óptimo
                                                </v-chip>
                                            </td>
                                            <td
                                                class="px-4 py-2 dark:text-gray-200"
                                            >
                                                {{ item.coverage_pct }}%
                                            </td>
                                            <td
                                                class="px-4 py-2 dark:text-gray-200"
                                            >
                                                {{ item.gap_fte }}
                                            </td>
                                            <td
                                                class="px-4 py-2 dark:text-gray-200"
                                            >
                                                ${{
                                                    item.estimated_cost?.toLocaleString()
                                                }}
                                            </td>
                                        </tr>
                                    </template>
                                </v-data-table>
                            </v-card>

                            <!-- Interpretation -->
                            <v-card
                                v-if="sweepResult.interpretation"
                                color="info"
                                variant="tonal"
                                elevation="2"
                            >
                                <v-card-title>
                                    <v-icon start
                                        >mdi-information-outline</v-icon
                                    >
                                    Interpretación
                                </v-card-title>
                                <v-card-text class="dark:text-gray-200">
                                    {{ sweepResult.interpretation }}
                                </v-card-text>
                            </v-card>
                        </template>
                    </div>
                </v-tabs-window-item>
            </v-tabs-window>
        </div>
    </AppLayout>
</template>
