<template>
    <div class="risk-assessment space-y-6">
        <!-- Overall Risk Score Card -->
        <div class="rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Risk Assessment & Mitigation
                </h3>
                <div
                    :class="[
                        'rounded-full px-4 py-2 font-semibold text-white',
                        riskLevel === 'low'
                            ? 'bg-emerald-500'
                            : riskLevel === 'medium'
                              ? 'bg-amber-500'
                              : 'bg-red-500',
                    ]"
                >
                    {{ overallRisk.toFixed(0) }}/100 - {{ riskLevelLabel }}
                </div>
            </div>

            <!-- Risk Gauge -->
            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <h4
                        class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Probability vs Impact
                    </h4>
                    <svg viewBox="0 0 200 200" class="h-auto w-full">
                        <!-- Grid background -->
                        <g opacity="0.1">
                            <line
                                x1="0"
                                y1="100"
                                x2="200"
                                y2="100"
                                stroke="currentColor"
                                stroke-width="1"
                            />
                            <line
                                x1="100"
                                y1="0"
                                x2="100"
                                y2="200"
                                stroke="currentColor"
                                stroke-width="1"
                            />
                            <line
                                x1="0"
                                y1="50"
                                x2="200"
                                y2="50"
                                stroke="currentColor"
                                stroke-width="1"
                            />
                            <line
                                x1="0"
                                y1="150"
                                x2="200"
                                y2="150"
                                stroke="currentColor"
                                stroke-width="1"
                            />
                            <line
                                x1="50"
                                y1="0"
                                x2="50"
                                y2="200"
                                stroke="currentColor"
                                stroke-width="1"
                            />
                            <line
                                x1="150"
                                y1="0"
                                x2="150"
                                y2="200"
                                stroke="currentColor"
                                stroke-width="1"
                            />
                        </g>

                        <!-- Quadrants -->
                        <rect
                            x="0"
                            y="0"
                            width="100"
                            height="100"
                            fill="#10b981"
                            opacity="0.1"
                        />
                        <rect
                            x="100"
                            y="0"
                            width="100"
                            height="100"
                            fill="#f59e0b"
                            opacity="0.1"
                        />
                        <rect
                            x="0"
                            y="100"
                            width="100"
                            height="100"
                            fill="#f59e0b"
                            opacity="0.1"
                        />
                        <rect
                            x="100"
                            y="100"
                            width="100"
                            height="100"
                            fill="#ef4444"
                            opacity="0.1"
                        />

                        <!-- Labels -->
                        <text
                            x="50"
                            y="50"
                            text-anchor="middle"
                            class="fill-emerald-600 text-xs dark:fill-emerald-400"
                        >
                            LOW
                        </text>
                        <text
                            x="150"
                            y="50"
                            text-anchor="middle"
                            class="fill-amber-600 text-xs dark:fill-amber-400"
                        >
                            MED
                        </text>
                        <text
                            x="50"
                            y="150"
                            text-anchor="middle"
                            class="fill-amber-600 text-xs dark:fill-amber-400"
                        >
                            MED
                        </text>
                        <text
                            x="150"
                            y="150"
                            text-anchor="middle"
                            class="fill-red-600 text-xs dark:fill-red-400"
                        >
                            HIGH
                        </text>

                        <!-- Current risk point -->
                        <circle
                            :cx="64 + probabilityScore * 0.72"
                            :cy="64 + impactScore * 0.72"
                            r="5"
                            fill="#3b82f6"
                        />
                        <line
                            x1="100"
                            y1="100"
                            :x2="64 + probabilityScore * 0.72"
                            :y2="64 + impactScore * 0.72"
                            stroke="#3b82f6"
                            stroke-width="1"
                        />

                        <!-- Axis labels -->
                        <text
                            x="190"
                            y="110"
                            class="fill-gray-600 text-xs dark:fill-gray-400"
                        >
                            Probability →
                        </text>
                        <text
                            x="5"
                            y="15"
                            class="fill-gray-600 text-xs dark:fill-gray-400"
                        >
                            ← Impact
                        </text>
                    </svg>
                </div>

                <!-- Key Metrics -->
                <div class="space-y-3">
                    <div
                        class="rounded-lg bg-gradient-to-r from-blue-50 to-blue-100 p-4 dark:from-blue-900 dark:to-blue-800"
                    >
                        <div
                            class="text-sm font-medium text-blue-900 dark:text-blue-200"
                        >
                            Probability
                        </div>
                        <div
                            class="mt-1 text-2xl font-bold text-blue-900 dark:text-blue-100"
                        >
                            {{ (probabilityScore * 100).toFixed(0) }}%
                        </div>
                        <div
                            class="mt-1 text-xs text-blue-700 dark:text-blue-300"
                        >
                            Likelihood of risk occurring
                        </div>
                    </div>

                    <div
                        class="rounded-lg bg-gradient-to-r from-purple-50 to-purple-100 p-4 dark:from-purple-900 dark:to-purple-800"
                    >
                        <div
                            class="text-sm font-medium text-purple-900 dark:text-purple-200"
                        >
                            Impact
                        </div>
                        <div
                            class="mt-1 text-2xl font-bold text-purple-900 dark:text-purple-100"
                        >
                            {{ (impactScore * 100).toFixed(0) }}%
                        </div>
                        <div
                            class="mt-1 text-xs text-purple-700 dark:text-purple-300"
                        >
                            Severity if risk occurs
                        </div>
                    </div>

                    <div
                        class="rounded-lg bg-gradient-to-r from-red-50 to-red-100 p-4 dark:from-red-900 dark:to-red-800"
                    >
                        <div
                            class="text-sm font-medium text-red-900 dark:text-red-200"
                        >
                            Overall Risk
                        </div>
                        <div
                            class="mt-1 text-2xl font-bold text-red-900 dark:text-red-100"
                        >
                            {{ (overallRisk / 100).toFixed(2) }}
                        </div>
                        <div
                            class="mt-1 text-xs text-red-700 dark:text-red-300"
                        >
                            Combined risk score
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Items -->
        <div class="rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
            <h4
                class="text-md mb-4 font-semibold text-gray-900 dark:text-white"
            >
                Identified Risks
            </h4>

            <div class="space-y-3">
                <div
                    v-for="(risk, idx) in riskItems"
                    :key="idx"
                    class="rounded border-l-4 p-4"
                    :class="[
                        getRiskItemBgClass(risk.score),
                        'border-' + getRiskBorderColor(risk.score),
                    ]"
                >
                    <div class="mb-2 flex items-start justify-between">
                        <h5 class="font-semibold text-gray-900 dark:text-white">
                            {{ risk.title }}
                        </h5>
                        <span
                            :class="[
                                'rounded px-2 py-1 text-xs font-semibold text-white',
                                'bg-' + getRiskBgColor(risk.score),
                            ]"
                        >
                            {{ risk.score }}/100
                        </span>
                    </div>

                    <div class="mb-3 grid grid-cols-2 gap-3">
                        <div>
                            <span
                                class="text-xs font-medium text-gray-600 dark:text-gray-400"
                                >Probability</span
                            >
                            <div
                                class="text-sm font-semibold text-gray-900 capitalize dark:text-white"
                            >
                                {{ risk.probability }}
                            </div>
                        </div>
                        <div>
                            <span
                                class="text-xs font-medium text-gray-600 dark:text-gray-400"
                                >Impact</span
                            >
                            <div
                                class="text-sm font-semibold text-gray-900 capitalize dark:text-white"
                            >
                                {{ risk.impact }}
                            </div>
                        </div>
                    </div>

                    <!-- Risk Score Progress -->
                    <div
                        class="mb-3 h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700"
                    >
                        <div
                            class="h-2 rounded-full transition-all"
                            :style="{
                                width: `${risk.score}%`,
                                backgroundColor: getRiskScoreColor(risk.score),
                            }"
                        ></div>
                    </div>

                    <p class="mb-3 text-xs text-gray-600 dark:text-gray-400">
                        {{ risk.description }}
                    </p>

                    <details class="cursor-pointer">
                        <summary
                            class="text-xs font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                        >
                            Mitigation Strategies
                        </summary>
                        <div
                            class="mt-2 space-y-2 border-l-2 border-gray-300 pl-4 dark:border-gray-600"
                        >
                            <div
                                v-for="(
                                    strategy, sidx
                                ) in risk.mitigation_strategies"
                                :key="sidx"
                                class="text-xs text-gray-600 dark:text-gray-400"
                            >
                                ✓ {{ strategy }}
                            </div>
                        </div>
                    </details>
                </div>
            </div>
        </div>

        <!-- Mitigation Strategies Summary -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
                <h4
                    class="text-md mb-4 font-semibold text-gray-900 dark:text-white"
                >
                    Recommended Actions
                </h4>

                <ul class="space-y-2">
                    <li
                        v-for="(action, idx) in recommendedActions"
                        :key="idx"
                        class="flex gap-3"
                    >
                        <span class="font-bold text-emerald-500">✓</span>
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ action }}
                        </span>
                    </li>
                </ul>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
                <h4
                    class="text-md mb-4 font-semibold text-gray-900 dark:text-white"
                >
                    Success Factors
                </h4>

                <ul class="space-y-2">
                    <li
                        v-for="(factor, idx) in successFactors"
                        :key="idx"
                        class="flex gap-3"
                    >
                        <span class="font-bold text-blue-500">★</span>
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            {{ factor }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';

interface RiskItem {
    id: number;
    title: string;
    description: string;
    probability: 'low' | 'medium' | 'high';
    impact: 'low' | 'medium' | 'high';
    score: number;
    mitigation_strategies: string[];
}

const props = defineProps<{
    scenarioId?: number;
}>();

// Sample risk data
const riskData = ref<RiskItem[]>([
    {
        id: 1,
        title: 'Limited talent pool for specialized roles',
        description:
            'Market constraints for AI/ML specialists may delay hiring',
        probability: 'high',
        impact: 'high',
        score: 75,
        mitigation_strategies: [
            'Start recruitment pipeline 6 months early',
            'Partner with training institutions',
            'Consider remote hiring from other regions',
        ],
    },
    {
        id: 2,
        title: 'External market conditions',
        description:
            'Economic downturn or industry changes could affect execution',
        probability: 'medium',
        impact: 'medium',
        score: 50,
        mitigation_strategies: [
            'Establish contingency budget (15%)',
            'Build flexible timeline into plan',
            'Regular market monitoring',
        ],
    },
    {
        id: 3,
        title: 'Internal adoption challenges',
        description: 'Resistance to change from existing teams',
        probability: 'medium',
        impact: 'low',
        score: 30,
        mitigation_strategies: [
            'Comprehensive change management program',
            'Early stakeholder engagement',
            'Success stories and quick wins',
        ],
    },
]);

// Computed values
const overallRisk = computed(() => {
    const probabilityScore = 0.45;
    const impactScore = 0.65;
    return probabilityScore * impactScore * 100;
});

const probabilityScore = computed(() => 0.45);
const impactScore = computed(() => 0.65);

const riskItems = computed(() => riskData.value);

const riskLevel = computed(() => {
    const risk = overallRisk.value;
    if (risk < 30) return 'low';
    if (risk < 60) return 'medium';
    return 'high';
});

const riskLevelLabel = computed(() => {
    return riskLevel.value.charAt(0).toUpperCase() + riskLevel.value.slice(1);
});

const recommendedActions = computed(() => [
    'Establish executive steering committee to oversee scenario execution',
    'Create contingency budget (+15%) for unexpected costs',
    'Implement change management program starting month 1',
    'Schedule monthly risk reviews with stakeholders',
    'Build early warning system for key metrics',
]);

const successFactors = computed(() => [
    'Strong executive sponsorship and alignment',
    'Clear accountability and ownership assignment',
    'Regular communication and stakeholder engagement',
    'Milestone-based progress tracking',
    'Flexibility to adapt based on market changes',
]);

// Helper functions
const getRiskItemBgClass = (score: number): string => {
    if (score < 40) return 'bg-emerald-50 dark:bg-emerald-900';
    if (score < 70) return 'bg-amber-50 dark:bg-amber-900';
    return 'bg-red-50 dark:bg-red-900';
};

const getRiskBorderColor = (score: number): string => {
    if (score < 40) return 'emerald-500';
    if (score < 70) return 'amber-500';
    return 'red-500';
};

const getRiskBgColor = (score: number): string => {
    if (score < 40) return 'emerald-500';
    if (score < 70) return 'amber-500';
    return 'red-500';
};

const getRiskScoreColor = (score: number): string => {
    if (score < 40) return '#10b981'; // emerald
    if (score < 70) return '#f59e0b'; // amber
    return '#ef4444'; // red
};
</script>

<style scoped>
.risk-assessment {
    /* Component-specific styles */
}

details > summary::-webkit-details-marker {
    display: none;
}

details > summary {
    list-style: none;
}
</style>
