<template>
    <div
        class="scenario-timeline rounded-lg bg-white p-6 shadow-md dark:bg-gray-900"
    >
        <h3 class="mb-6 text-lg font-semibold text-gray-900 dark:text-white">
            Implementation Timeline
        </h3>

        <!-- Timeline Navigation -->
        <div class="mb-6 flex gap-2">
            <button
                @click="selectedScenarioId = null"
                :class="[
                    'rounded-lg px-4 py-2 transition',
                    selectedScenarioId === null
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-200 text-gray-900 hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600',
                ]"
            >
                All Scenarios
            </button>
            <button
                v-for="scenario in scenarios"
                :key="scenario.id"
                @click="selectedScenarioId = scenario.id"
                :class="[
                    'rounded-lg px-4 py-2 transition',
                    selectedScenarioId === scenario.id
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-200 text-gray-900 hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600',
                ]"
            >
                {{ scenario.name }}
            </button>
        </div>

        <!-- Timeline Container -->
        <div class="relative">
            <!-- Timeline SVG -->
            <svg class="h-auto w-full" :viewBox="`0 0 1000 ${timelineHeight}`">
                <!-- Month markers -->
                <g class="month-markers">
                    <line
                        v-for="(month, idx) in 12"
                        :key="`month-${idx}`"
                        :x1="monthToX(idx)"
                        :y1="50"
                        :x2="monthToX(idx)"
                        :y2="60"
                        stroke="currentColor"
                        stroke-width="1"
                        class="text-gray-400 dark:text-gray-600"
                    />
                    <text
                        v-for="(month, idx) in [
                            'J',
                            'F',
                            'M',
                            'A',
                            'M',
                            'J',
                            'J',
                            'A',
                            'S',
                            'O',
                            'N',
                            'D',
                        ]"
                        :key="`label-${idx}`"
                        :x="monthToX(idx)"
                        :y="40"
                        text-anchor="middle"
                        class="fill-gray-600 text-xs dark:fill-gray-400"
                    >
                        {{ month }}
                    </text>
                </g>

                <!-- Baseline -->
                <line
                    x1="100"
                    :y1="70"
                    x2="900"
                    :y2="70"
                    stroke="currentColor"
                    stroke-width="2"
                    class="text-gray-300 dark:text-gray-700"
                />

                <!-- Phase blocks -->
                <g
                    v-for="(phase, phaseIdx) in displayPhases"
                    :key="`phase-${phaseIdx}`"
                >
                    <rect
                        :x="monthToX(phase.start_month)"
                        :y="100 + phaseIdx * 50"
                        :width="
                            monthToX(phase.end_month) -
                            monthToX(phase.start_month)
                        "
                        height="40"
                        :fill="getPhaseColor(phase.type)"
                        opacity="0.8"
                        class="transition hover:opacity-100"
                    />
                    <text
                        :x="
                            (monthToX(phase.start_month) +
                                monthToX(phase.end_month)) /
                            2
                        "
                        :y="130 + phaseIdx * 50"
                        text-anchor="middle"
                        class="fill-white text-xs font-semibold dark:fill-gray-900"
                    >
                        {{ phase.name }}
                    </text>
                </g>

                <!-- Milestones -->
                <g
                    v-for="(milestone, milestoneIdx) in displayMilestones"
                    :key="`milestone-${milestoneIdx}`"
                    class="milestones"
                >
                    <circle
                        :cx="monthToX(milestone.month)"
                        :cy="70"
                        r="5"
                        class="fill-emerald-500"
                    />
                    <text
                        :x="monthToX(milestone.month)"
                        :y="55"
                        text-anchor="middle"
                        class="fill-emerald-700 text-xs font-semibold dark:fill-emerald-400"
                    >
                        {{ milestone.name }}
                    </text>
                </g>
            </svg>
        </div>

        <!-- Phase Legend -->
        <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded bg-blue-500"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300"
                    >Planning</span
                >
            </div>
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded bg-purple-500"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300"
                    >Execution</span
                >
            </div>
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded bg-emerald-500"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300"
                    >Stabilization</span
                >
            </div>
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded bg-amber-500"></div>
                <span class="text-sm text-gray-700 dark:text-gray-300"
                    >Review</span
                >
            </div>
        </div>

        <!-- Timeline Details -->
        <div
            v-if="selectedScenario"
            class="mt-8 border-t border-gray-200 pt-6 dark:border-gray-700"
        >
            <h4 class="mb-4 font-semibold text-gray-900 dark:text-white">
                {{ selectedScenario.name }} - Timeline Details
            </h4>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900">
                    <div
                        class="text-sm font-medium text-blue-900 dark:text-blue-100"
                    >
                        Start Date
                    </div>
                    <div
                        class="mt-1 text-lg font-semibold text-blue-900 dark:text-blue-100"
                    >
                        {{ formatDate(selectedScenario.start_date) }}
                    </div>
                </div>

                <div class="rounded-lg bg-purple-50 p-4 dark:bg-purple-900">
                    <div
                        class="text-sm font-medium text-purple-900 dark:text-purple-100"
                    >
                        Duration
                    </div>
                    <div
                        class="mt-1 text-lg font-semibold text-purple-900 dark:text-purple-100"
                    >
                        {{ selectedScenario.horizon_months }} months
                    </div>
                </div>

                <div class="rounded-lg bg-emerald-50 p-4 dark:bg-emerald-900">
                    <div
                        class="text-sm font-medium text-emerald-900 dark:text-emerald-100"
                    >
                        End Date
                    </div>
                    <div
                        class="mt-1 text-lg font-semibold text-emerald-900 dark:text-emerald-100"
                    >
                        {{ formatDate(selectedScenario.end_date) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';

interface Phase {
    id: number;
    name: string;
    type: 'planning' | 'execution' | 'stabilization' | 'review';
    start_month: number;
    end_month: number;
    status: string;
}

interface Milestone {
    id: number;
    name: string;
    month: number;
    description: string;
}

interface Scenario {
    id: number;
    name: string;
    start_date: string;
    end_date: string;
    horizon_months: number;
    created_at: string;
    updated_at: string;
}

const props = defineProps<{
    scenarios: Scenario[];
}>();

const selectedScenarioId = ref<number | null>(null);
const timelineHeight = ref(400);

// Sample data - in real app, would come from API
const phases: Phase[] = [
    {
        id: 1,
        name: 'Assessment & Planning',
        type: 'planning',
        start_month: 0,
        end_month: 1,
        status: 'completed',
    },
    {
        id: 2,
        name: 'Stakeholder Alignment',
        type: 'planning',
        start_month: 1,
        end_month: 2,
        status: 'completed',
    },
    {
        id: 3,
        name: 'Capability Building',
        type: 'execution',
        start_month: 2,
        end_month: 6,
        status: 'in_progress',
    },
    {
        id: 4,
        name: 'Resource Acquisition',
        type: 'execution',
        start_month: 4,
        end_month: 8,
        status: 'in_progress',
    },
    {
        id: 5,
        name: 'Integration & Optimization',
        type: 'stabilization',
        start_month: 8,
        end_month: 11,
        status: 'pending',
    },
    {
        id: 6,
        name: 'Performance Review',
        type: 'review',
        start_month: 11,
        end_month: 12,
        status: 'pending',
    },
];

const milestones: Milestone[] = [
    {
        id: 1,
        name: 'Kickoff',
        month: 0,
        description: 'Project kickoff and team alignment',
    },
    {
        id: 2,
        name: 'Design Complete',
        month: 2,
        description: 'Capability design finalized',
    },
    {
        id: 3,
        name: 'Hiring Done',
        month: 6,
        description: 'All external hiring completed',
    },
    {
        id: 4,
        name: 'Training Complete',
        month: 9,
        description: 'All training programs completed',
    },
    {
        id: 5,
        name: 'Go Live',
        month: 11,
        description: 'Full scenario activation',
    },
];

const selectedScenario = computed(() => {
    if (selectedScenarioId.value === null) return null;
    return props.scenarios.find((s) => s.id === selectedScenarioId.value);
});

const displayPhases = computed(() => {
    return phases;
});

const displayMilestones = computed(() => {
    return milestones;
});

const monthToX = (month: number): number => {
    return 100 + month * 80;
};

const getPhaseColor = (type: string): string => {
    const colors: Record<string, string> = {
        planning: '#3b82f6', // blue
        execution: '#a855f7', // purple
        stabilization: '#10b981', // emerald
        review: '#f59e0b', // amber
    };
    return colors[type] || '#6b7280';
};

const formatDate = (date: string | undefined): string => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

<style scoped>
.scenario-timeline svg {
    overflow: visible;
}

.scenario-timeline circle:hover {
    @apply r-6;
}
</style>
