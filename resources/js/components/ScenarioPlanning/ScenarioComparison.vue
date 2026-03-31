<template>
    <div
        class="scenario-comparison rounded-lg bg-white p-6 shadow-md dark:bg-gray-900"
    >
        <div class="mb-6">
            <h3
                class="mb-4 text-lg font-semibold text-gray-900 dark:text-white"
            >
                Scenario Comparison
            </h3>

            <!-- Comparison Controls -->
            <div class="mb-6 flex flex-col gap-4">
                <div class="flex gap-2">
                    <button
                        @click="showScenarioSelector = true"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700"
                    >
                        + Add Scenario
                    </button>
                    <button
                        @click="exportComparison"
                        class="rounded-lg bg-gray-600 px-4 py-2 text-white transition hover:bg-gray-700"
                    >
                        Export Comparison
                    </button>
                    <button
                        @click="clearComparison"
                        class="rounded-lg bg-red-600 px-4 py-2 text-white transition hover:bg-red-700"
                    >
                        Clear All
                    </button>
                </div>

                <!-- Selected Scenarios Display -->
                <div class="flex flex-wrap gap-2">
                    <div
                        v-for="(scenario, idx) in selectedScenarios"
                        :key="scenario.id"
                        class="flex items-center gap-2 rounded-lg bg-blue-100 px-3 py-2 dark:bg-blue-900"
                    >
                        <span
                            class="text-sm font-medium text-blue-900 dark:text-blue-100"
                        >
                            {{ scenario.name }}
                        </span>
                        <button
                            @click="removeScenario(idx)"
                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200"
                        >
                            ✕
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div
                v-if="loadingComparison"
                class="mb-4 flex items-center gap-3 rounded-lg bg-blue-50 px-4 py-3 text-blue-700 dark:bg-blue-950 dark:text-blue-300"
            >
                <svg
                    class="h-5 w-5 animate-spin"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    />
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                    />
                </svg>
                <span class="text-sm font-medium"
                    >Loading comparison data…</span
                >
            </div>

            <!-- Error State -->
            <div
                v-if="comparisonError"
                class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300"
            >
                {{ comparisonError }}
            </div>

            <!-- Comparison Grid -->
            <div v-if="selectedScenarios.length > 0" class="overflow-x-auto">
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr
                            class="border-b border-gray-300 bg-gray-100 dark:border-gray-700 dark:bg-gray-800"
                        >
                            <th
                                class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white"
                            >
                                Metric
                            </th>
                            <th
                                v-for="scenario in selectedScenarios"
                                :key="scenario.id"
                                class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-white"
                            >
                                {{ scenario.name }}
                            </th>
                            <th
                                class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-white"
                            >
                                Variance
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            class="border-b border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
                        >
                            <td
                                class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300"
                            >
                                IQ Score
                            </td>
                            <td
                                v-for="scenario in selectedScenarios"
                                :key="`iq-${scenario.id}`"
                                class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-white"
                            >
                                {{ scenario.iq }}
                            </td>
                            <td
                                class="px-4 py-3 text-center text-gray-900 dark:text-white"
                            >
                                {{ calculateVariance('iq') }}%
                            </td>
                        </tr>

                        <tr
                            class="border-b border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
                        >
                            <td
                                class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Status
                            </td>
                            <td
                                v-for="scenario in selectedScenarios"
                                :key="`status-${scenario.id}`"
                                class="px-4 py-3 text-center"
                            >
                                <span
                                    class="rounded-full px-2 py-1 text-xs font-semibold"
                                    :class="
                                        getStatusBadgeClass(scenario.status)
                                    "
                                >
                                    {{ scenario.status }}
                                </span>
                            </td>
                            <td
                                class="px-4 py-3 text-center text-gray-500 dark:text-gray-400"
                            >
                                -
                            </td>
                        </tr>

                        <tr
                            class="border-b border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
                        >
                            <td
                                class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Financial Impact
                            </td>
                            <td
                                v-for="scenario in selectedScenarios"
                                :key="`finance-${scenario.id}`"
                                class="px-4 py-3 text-center text-gray-900 dark:text-white"
                            >
                                ${{
                                    (
                                        scenario.financial_impact
                                            ?.total_impact / 1000
                                    ).toFixed(0)
                                }}K
                            </td>
                            <td
                                class="px-4 py-3 text-center text-gray-900 dark:text-white"
                            >
                                {{ calculateFinanceVariance() }}%
                            </td>
                        </tr>

                        <tr
                            class="border-b border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
                        >
                            <td
                                class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Risk Score
                            </td>
                            <td
                                v-for="scenario in selectedScenarios"
                                :key="`risk-${scenario.id}`"
                                class="px-4 py-3 text-center text-gray-900 dark:text-white"
                            >
                                {{ scenario.risk_score }}/100
                            </td>
                            <td
                                class="px-4 py-3 text-center text-gray-900 dark:text-white"
                            >
                                {{ calculateVariance('risk_score') }}%
                            </td>
                        </tr>

                        <tr
                            class="border-b border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
                        >
                            <td
                                class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Skill Gaps
                            </td>
                            <td
                                v-for="scenario in selectedScenarios"
                                :key="`gaps-${scenario.id}`"
                                class="px-4 py-3 text-center text-gray-900 dark:text-white"
                            >
                                {{ scenario.skill_gaps }} gaps
                            </td>
                            <td
                                class="px-4 py-3 text-center text-gray-900 dark:text-white"
                            >
                                {{ calculateVariance('skill_gaps') }}%
                            </td>
                        </tr>

                        <tr
                            class="border-b border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
                        >
                            <td
                                class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Timeline (Months)
                            </td>
                            <td
                                v-for="scenario in selectedScenarios"
                                :key="`timeline-${scenario.id}`"
                                class="px-4 py-3 text-center text-gray-900 dark:text-white"
                            >
                                {{ scenario.horizon_months ?? 'N/A' }} months
                            </td>
                            <td
                                class="px-4 py-3 text-center text-gray-500 dark:text-gray-400"
                            >
                                -
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td
                                class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Start Date
                            </td>
                            <td
                                v-for="scenario in selectedScenarios"
                                :key="`start-${scenario.id}`"
                                class="px-4 py-3 text-center text-sm text-gray-900 dark:text-white"
                            >
                                {{ formatDate(scenario.start_date) }}
                            </td>
                            <td
                                class="px-4 py-3 text-center text-gray-500 dark:text-gray-400"
                            >
                                -
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-else class="py-12 text-center">
                <p class="text-gray-500 dark:text-gray-400">
                    Select 2-4 scenarios to compare side-by-side
                </p>
            </div>
        </div>

        <!-- Scenario Selector Modal -->
        <div
            v-if="showScenarioSelector"
            class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black"
        >
            <div
                class="w-96 rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800"
            >
                <h4
                    class="mb-4 text-lg font-semibold text-gray-900 dark:text-white"
                >
                    Select Scenario to Compare
                </h4>

                <div class="mb-6 max-h-64 space-y-2 overflow-y-auto">
                    <button
                        v-for="scenario in availableScenarios"
                        :key="scenario.id"
                        @click="addScenario(scenario)"
                        class="w-full rounded px-4 py-2 text-left transition hover:bg-blue-100 dark:hover:bg-blue-900"
                    >
                        <div class="font-medium text-gray-900 dark:text-white">
                            {{ scenario.name }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ scenario.code }}
                        </div>
                    </button>
                </div>

                <div class="flex gap-2">
                    <button
                        @click="showScenarioSelector = false"
                        class="flex-1 rounded-lg bg-gray-300 px-4 py-2 text-gray-900 transition hover:bg-gray-400 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-700"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { apiClient } from '@/services/apiClient';
import { onMounted, ref, watch } from 'vue';

interface Scenario {
    id: number;
    name: string;
    code: string;
    status: string;
    iq: number;
    financial_impact: { total_impact: number };
    risk_score: number;
    skill_gaps: number;
    horizon_months?: number;
    start_date?: string;
    end_date?: string;
}

const showScenarioSelector = ref(false);
const selectedScenarios = ref<Scenario[]>([]);
const availableScenarios = ref<Scenario[]>([]);
const loadingComparison = ref(false);
const comparisonError = ref<string | null>(null);

onMounted(() => {
    loadAvailableScenarios();
});

const loadAvailableScenarios = async () => {
    try {
        const response = await apiClient.get('/api/scenarios');
        availableScenarios.value = Array.isArray(response.data)
            ? response.data
            : (response.data?.data ?? []);
    } catch (error) {
        console.error('Failed to load scenarios:', error);
    }
};

/**
 * Fetch enriched comparison data from the API when 2+ scenarios selected.
 * Merges IQ, financial_impact, risk_score and skill_gaps back into selectedScenarios.
 */
const fetchComparison = async () => {
    if (selectedScenarios.value.length < 2) {
        return;
    }

    loadingComparison.value = true;
    comparisonError.value = null;

    try {
        const ids = selectedScenarios.value.map((s) => s.id);
        const response = await apiClient.post('/api/scenarios/compare', {
            scenario_ids: ids,
        });

        const comparisonItems: Scenario[] = response.data?.comparison ?? [];

        // Enrich selectedScenarios with API data
        selectedScenarios.value = selectedScenarios.value.map((selected) => {
            const enriched = comparisonItems.find(
                (item) => item.id === selected.id,
            );
            return enriched ? { ...selected, ...enriched } : selected;
        });
    } catch (error: any) {
        console.error('Failed to fetch comparison data:', error);
        comparisonError.value =
            error?.response?.data?.message ?? 'Failed to load comparison data.';
    } finally {
        loadingComparison.value = false;
    }
};

// Auto-fetch comparison whenever the set of selected scenarios changes
watch(
    () => selectedScenarios.value.map((s) => s.id).join(','),
    () => {
        if (selectedScenarios.value.length >= 2) {
            fetchComparison();
        }
    },
);

const addScenario = (scenario: Scenario) => {
    if (
        selectedScenarios.value.length < 4 &&
        !selectedScenarios.value.find((s) => s.id === scenario.id)
    ) {
        selectedScenarios.value.push(scenario);
        showScenarioSelector.value = false;
    }
};

const removeScenario = (index: number) => {
    selectedScenarios.value.splice(index, 1);
};

const clearComparison = () => {
    selectedScenarios.value = [];
    comparisonError.value = null;
};

const calculateVariance = (metric: string): string => {
    if (selectedScenarios.value.length < 2) return '0';

    const values = selectedScenarios.value.map(
        (s) => s[metric as keyof Scenario] as number,
    );
    const max = Math.max(...values);
    const min = Math.min(...values);
    const variance = max > 0 ? ((max - min) / min) * 100 : 0;

    return variance.toFixed(0);
};

const calculateFinanceVariance = (): string => {
    if (selectedScenarios.value.length < 2) return '0';

    const values = selectedScenarios.value.map(
        (s) => s.financial_impact?.total_impact ?? 0,
    );
    const max = Math.max(...values);
    const min = Math.min(...values);
    const variance = max > 0 ? ((max - min) / min) * 100 : 0;

    return variance.toFixed(0);
};

const formatDate = (date: string | undefined): string => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const getStatusBadgeClass = (status: string): string => {
    const classes: Record<string, string> = {
        draft: 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100',
        in_review:
            'bg-blue-200 dark:bg-blue-900 text-blue-900 dark:text-blue-100',
        approved:
            'bg-green-200 dark:bg-green-900 text-green-900 dark:text-green-100',
        active: 'bg-emerald-200 dark:bg-emerald-900 text-emerald-900 dark:text-emerald-100',
        archived: 'bg-red-200 dark:bg-red-900 text-red-900 dark:text-red-100',
    };
    return (
        classes[status] ||
        'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100'
    );
};

const exportComparison = () => {
    // Export comparison as CSV
    const headers = [
        'Metric',
        ...selectedScenarios.value.map((s) => s.name),
        'Variance',
    ];
    const rows = [
        [
            'IQ Score',
            ...selectedScenarios.value.map((s) => (s.iq ?? 0).toString()),
            calculateVariance('iq') + '%',
        ],
        ['Status', ...selectedScenarios.value.map((s) => s.status), '-'],
        [
            'Financial Impact',
            ...selectedScenarios.value.map(
                (s) =>
                    '$' +
                    ((s.financial_impact?.total_impact ?? 0) / 1000).toFixed(
                        0,
                    ) +
                    'K',
            ),
            calculateFinanceVariance() + '%',
        ],
        [
            'Risk Score',
            ...selectedScenarios.value.map((s) => (s.risk_score ?? 0) + '/100'),
            calculateVariance('risk_score') + '%',
        ],
        [
            'Skill Gaps',
            ...selectedScenarios.value.map(
                (s) => (s.skill_gaps ?? 0) + ' gaps',
            ),
            calculateVariance('skill_gaps') + '%',
        ],
    ];

    const csv = [headers, ...rows].map((row) => row.join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'scenario-comparison.csv';
    link.click();
};
</script>

<style scoped>
.scenario-comparison {
    /* Component-specific styles */
}
</style>
