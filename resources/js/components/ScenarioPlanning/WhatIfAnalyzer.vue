<template>
    <div class="what-if-analyzer space-y-6">
        <!-- Title -->
        <div class="mb-4">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                What-If Analyzer
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Explore scenario impacts with interactive simulations
            </p>
        </div>

        <!-- Input Controls -->
        <div
            class="space-y-4 rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800"
        >
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <!-- Headcount Delta -->
                <div>
                    <label
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300"
                    >
                        Headcount Change
                    </label>
                    <div class="mt-2 flex items-center gap-2">
                        <input
                            v-model.number="params.headcount_delta"
                            type="range"
                            min="-100"
                            max="100"
                            step="5"
                            class="flex-1"
                            @input="runAnalysis"
                        />
                        <span
                            class="w-16 rounded bg-gray-100 p-2 text-center font-semibold dark:bg-gray-700"
                        >
                            {{ params.headcount_delta > 0 ? '+' : ''
                            }}{{ params.headcount_delta }}
                        </span>
                    </div>
                </div>

                <!-- Timeline -->
                <div>
                    <label
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300"
                    >
                        Timeline (weeks)
                    </label>
                    <div class="mt-2 flex items-center gap-2">
                        <input
                            v-model.number="params.timeline_weeks"
                            type="range"
                            min="4"
                            max="52"
                            step="1"
                            class="flex-1"
                            @input="runAnalysis"
                        />
                        <span
                            class="w-16 rounded bg-gray-100 p-2 text-center font-semibold dark:bg-gray-700"
                        >
                            {{ params.timeline_weeks }}w
                        </span>
                    </div>
                </div>

                <!-- Turnover Rate -->
                <div>
                    <label
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300"
                    >
                        Projected Turnover
                    </label>
                    <div class="mt-2 flex items-center gap-2">
                        <input
                            v-model.number="params.turnover_rate"
                            type="range"
                            min="0"
                            max="0.5"
                            step="0.05"
                            class="flex-1"
                            @input="runAnalysis"
                        />
                        <span
                            class="w-16 rounded bg-gray-100 p-2 text-center font-semibold dark:bg-gray-700"
                        >
                            {{ (params.turnover_rate * 100).toFixed(0) }}%
                        </span>
                    </div>
                </div>

                <!-- Complexity -->
                <div>
                    <label
                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300"
                    >
                        Complexity Factor
                    </label>
                    <div class="mt-2 flex items-center gap-2">
                        <input
                            v-model.number="params.complexity"
                            type="range"
                            min="0"
                            max="2"
                            step="0.1"
                            class="flex-1"
                            @input="runAnalysis"
                        />
                        <span
                            class="w-16 rounded bg-gray-100 p-2 text-center font-semibold dark:bg-gray-700"
                        >
                            {{ params.complexity.toFixed(1) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button
                    class="flex-1 rounded-lg bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"
                    @click="runAnalysis"
                    :disabled="analyzing"
                >
                    {{ analyzing ? 'Analyzing...' : 'Run Analysis' }}
                </button>
                <button
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                    @click="resetParams"
                >
                    Reset
                </button>
            </div>
        </div>

        <!-- Results Grid -->
        <div
            v-if="analysis"
            class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"
        >
            <!-- Headcount Impact -->
            <div
                class="rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 p-4 dark:from-blue-900/10 dark:to-blue-900/20"
            >
                <h4
                    class="text-sm font-semibold text-blue-900 dark:text-blue-100"
                >
                    Headcount Impact
                </h4>
                <p class="mt-2 text-2xl font-bold text-blue-600">
                    {{ analysis.headcount_impact?.new_headcount }}
                </p>
                <p class="text-xs text-blue-700 dark:text-blue-200">
                    Coverage:
                    {{ analysis.headcount_impact?.expected_coverage }}%
                </p>
            </div>

            <!-- Financial Impact -->
            <div
                class="rounded-lg bg-gradient-to-br from-green-50 to-green-100 p-4 dark:from-green-900/10 dark:to-green-900/20"
            >
                <h4
                    class="text-sm font-semibold text-green-900 dark:text-green-100"
                >
                    ROI
                </h4>
                <p
                    :class="{
                        'mt-2 text-2xl font-bold': true,
                        'text-green-600': analysis.financial_impact?.roi > 0,
                        'text-red-600': analysis.financial_impact?.roi <= 0,
                    }"
                >
                    {{ analysis.financial_impact?.roi?.toFixed(1) }}%
                </p>
                <p class="text-xs text-green-700 dark:text-green-200">
                    Cost: ${{
                        (
                            analysis.financial_impact?.total_cost / 1000000
                        ).toFixed(1)
                    }}M
                </p>
            </div>

            <!-- Timeline Impact -->
            <div
                class="rounded-lg bg-gradient-to-br from-purple-50 to-purple-100 p-4 dark:from-purple-900/10 dark:to-purple-900/20"
            >
                <h4
                    class="text-sm font-semibold text-purple-900 dark:text-purple-100"
                >
                    Timeline
                </h4>
                <p class="mt-2 text-2xl font-bold text-purple-600">
                    {{ analysis.headcount_impact?.weeks_to_full_capacity }}w
                </p>
                <p class="text-xs text-purple-700 dark:text-purple-200">
                    to full capacity
                </p>
            </div>

            <!-- Risk Score -->
            <div
                class="rounded-lg bg-gradient-to-br from-orange-50 to-orange-100 p-4 dark:from-orange-900/10 dark:to-orange-900/20"
            >
                <h4
                    class="text-sm font-semibold text-orange-900 dark:text-orange-100"
                >
                    Risk Level
                </h4>
                <p class="mt-2 text-2xl font-bold text-orange-600">
                    {{ analysis.risk_impact?.risk_level }}
                </p>
                <p class="text-xs text-orange-700 dark:text-orange-200">
                    Score:
                    {{
                        analysis.risk_impact?.overall_risk_score?.toFixed(1)
                    }}/10
                </p>
            </div>

            <!-- Success Probability -->
            <div
                class="rounded-lg bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 dark:from-indigo-900/10 dark:to-indigo-900/20"
            >
                <h4
                    class="text-sm font-semibold text-indigo-900 dark:text-indigo-100"
                >
                    Success Rate
                </h4>
                <p class="mt-2 text-2xl font-bold text-indigo-600">
                    {{
                        (
                            analysis.predicted_outcomes?.success_probability *
                            100
                        ).toFixed(0)
                    }}%
                </p>
                <p class="text-xs text-indigo-700 dark:text-indigo-200">
                    Estimated probability
                </p>
            </div>

            <!-- Hiring Needs -->
            <div
                class="rounded-lg bg-gradient-to-br from-cyan-50 to-cyan-100 p-4 dark:from-cyan-900/10 dark:to-cyan-900/20"
            >
                <h4
                    class="text-sm font-semibold text-cyan-900 dark:text-cyan-100"
                >
                    Hiring Needs
                </h4>
                <p class="mt-2 text-2xl font-bold text-cyan-600">
                    {{ analysis.headcount_impact?.hiring_needs }}
                </p>
                <p class="text-xs text-cyan-700 dark:text-cyan-200">
                    new positions
                </p>
            </div>
        </div>

        <!-- Key Risks -->
        <div
            v-if="analysis?.risk_impact?.individual_risks"
            class="rounded-lg bg-red-50 p-4 dark:bg-red-900/10"
        >
            <h3 class="mb-3 font-semibold text-red-900 dark:text-red-100">
                Key Risks Identified
            </h3>
            <div class="space-y-2">
                <div
                    v-for="risk in analysis.risk_impact.individual_risks.slice(
                        0,
                        3,
                    )"
                    :key="risk.type"
                    class="rounded bg-white p-2 text-sm dark:bg-gray-800"
                >
                    <p class="font-semibold text-gray-800 dark:text-gray-200">
                        {{ risk.type }}
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        {{ risk.description }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { apiClient } from '@/services/apiClient';
import { computed, onMounted, ref } from 'vue';

interface Props {
    scenario?: any;
    scenarioId?: number;
}

const props = withDefaults(defineProps<Props>(), {});

const params = ref({
    headcount_delta: 10,
    timeline_weeks: 12,
    turnover_rate: 0.1,
    complexity: 1,
});

const analysis = ref<any>(null);
const analyzing = ref(false);
const scenarioId = computed(() => props.scenarioId || props.scenario?.id);

async function runAnalysis() {
    if (!scenarioId.value) return;

    analyzing.value = true;
    try {
        const response = await apiClient.post(
            `/strategic-planning/what-if/comprehensive`,
            {
                scenario_id: scenarioId.value,
                ...params.value,
            },
        );
        analysis.value = response.data?.data;
    } catch (err) {
        console.error('Analysis error:', err);
    } finally {
        analyzing.value = false;
    }
}

function resetParams() {
    params.value = {
        headcount_delta: 10,
        timeline_weeks: 12,
        turnover_rate: 0.1,
        complexity: 1,
    };
    analysis.value = null;
}

onMounted(() => {
    runAnalysis();
});
</script>

<style scoped>
.what-if-analyzer {
    @apply bg-gray-50 p-4 dark:bg-gray-900;
}

input[type='range'] {
    @apply h-2 rounded-lg bg-gray-200 accent-indigo-600 dark:bg-gray-700;
}
</style>
