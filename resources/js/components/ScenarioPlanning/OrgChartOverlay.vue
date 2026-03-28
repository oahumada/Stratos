<template>
    <div class="org-chart-overlay space-y-4">
        <!-- Controls -->
        <div class="flex items-center gap-3">
            <button
                :class="{
                    'rounded-lg px-4 py-2 font-semibold transition-all': true,
                    'bg-indigo-600 text-white': view === 'current',
                    'bg-gray-200 text-gray-700': view !== 'current',
                }"
                @click="view = 'current'"
            >
                Current Org
            </button>
            <button
                :class="{
                    'rounded-lg px-4 py-2 font-semibold transition-all': true,
                    'bg-indigo-600 text-white': view === 'overlay',
                    'bg-gray-200 text-gray-700': view !== 'overlay',
                }"
                @click="view = 'overlay'"
            >
                With Changes
            </button>
            <button
                :class="{
                    'rounded-lg px-4 py-2 font-semibold transition-all': true,
                    'bg-indigo-600 text-white': view === 'delta',
                    'bg-gray-200 text-gray-700': view !== 'delta',
                }"
                @click="view = 'delta'"
            >
                Changes Only
            </button>
        </div>

        <!-- Legend -->
        <div class="flex gap-4 text-sm">
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded bg-blue-100"></div>
                <span>Current Role</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded bg-green-100"></div>
                <span>New Position</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded border-2 border-red-400"></div>
                <span>Departure</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded border-2 border-yellow-400"></div>
                <span>Successor Candidate</span>
            </div>
        </div>

        <!-- Org Chart Canvas -->
        <div
            class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800"
        >
            <div v-if="loading" class="py-8 text-center text-gray-500">
                Loading org chart...
            </div>

            <svg
                v-else
                :width="svgWidth"
                :height="svgHeight"
                class="mb-4 rounded border border-gray-100"
            >
                <!-- Connections -->
                <g class="connections">
                    <line
                        v-for="(line, idx) in connectionLines"
                        :key="`line-${idx}`"
                        :x1="line.x1"
                        :y1="line.y1"
                        :x2="line.x2"
                        :y2="line.y2"
                        stroke="#ccc"
                        stroke-width="2"
                    />
                </g>

                <!-- Nodes (Roles) -->
                <g class="nodes">
                    <g
                        v-for="node in visibleNodes"
                        :key="`node-${node.id}`"
                        @click="
                            selectedRole =
                                selectedRole?.id === node.id ? null : node
                        "
                        class="cursor-pointer"
                    >
                        <!-- Role Box -->
                        <rect
                            :x="node.x"
                            :y="node.y"
                            width="140"
                            height="80"
                            :fill="getRoleFill(node)"
                            stroke="#333"
                            stroke-width="2"
                            rx="4"
                        />

                        <!-- Role Title -->
                        <text
                            :x="node.x + 70"
                            :y="node.y + 25"
                            text-anchor="middle"
                            class="font-semibold text-gray-800"
                            font-size="12"
                        >
                            {{ node.title }}
                        </text>

                        <!-- Headcount -->
                        <text
                            :x="node.x + 70"
                            :y="node.y + 45"
                            text-anchor="middle"
                            class="text-gray-600"
                            font-size="11"
                        >
                            {{ node.headcount }}
                        </text>

                        <!-- Status Badge -->
                        <text
                            v-if="node.status"
                            :x="node.x + 5"
                            :y="node.y + 70"
                            class="font-tiny"
                            font-size="10"
                            :fill="getStatusColor(node.status)"
                        >
                            {{ node.statusLabel }}
                        </text>
                    </g>
                </g>
            </svg>

            <!-- Selected Role Details -->
            <div
                v-if="selectedRole"
                class="space-y-3 rounded-lg bg-indigo-50 p-4 dark:bg-indigo-900/20"
            >
                <h4 class="font-semibold">{{ selectedRole.title }}</h4>

                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="block text-xs font-semibold text-gray-600"
                            >Current Count</span
                        >
                        <span class="text-lg font-bold">{{
                            selectedRole.current_count
                        }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-600"
                            >Planned Change</span
                        >
                        <span
                            :class="{
                                'text-lg font-bold': true,
                                'text-green-600': selectedRole.delta > 0,
                                'text-red-600': selectedRole.delta < 0,
                            }"
                        >
                            {{ selectedRole.delta > 0 ? '+' : ''
                            }}{{ selectedRole.delta }}
                        </span>
                    </div>
                </div>

                <div v-if="selectedRole.successors?.length">
                    <p class="text-xs font-semibold text-gray-600">
                        Successor Candidates
                    </p>
                    <ul class="mt-2 space-y-1">
                        <li
                            v-for="succ in selectedRole.successors"
                            :key="succ"
                            class="text-sm text-gray-700"
                        >
                            • {{ succ }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <div class="rounded-lg bg-blue-50 p-3 dark:bg-blue-900/20">
                <p class="text-xs text-gray-600">Total Roles</p>
                <p class="text-2xl font-bold text-blue-600">
                    {{ orgChart?.roles?.length || 0 }}
                </p>
            </div>
            <div class="rounded-lg bg-green-50 p-3 dark:bg-green-900/20">
                <p class="text-xs text-gray-600">New Positions</p>
                <p class="text-2xl font-bold text-green-600">
                    +{{ org ChartHeadcountAdded }}
                </p>
            </div>
            <div class="rounded-lg bg-red-50 p-3 dark:bg-red-900/20">
                <p class="text-xs text-gray-600">Reductions</p>
                <p class="text-2xl font-bold text-red-600">
                    -{{ orgChartHeadcountRemoved }}
                </p>
            </div>
            <div class="rounded-lg bg-yellow-50 p-3 dark:bg-yellow-900/20">
                <p class="text-xs text-gray-600">Impact</p>
                <p class="text-2xl font-bold text-yellow-600">
                    {{ orgChartHeadcountAdded - orgChartHeadcountRemoved }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { apiClient } from '@/services/apiClient';

interface Props {
    scenario: any;
    scenarioId?: number;
}

const props = withDefaults(defineProps<Props>(), {});

const view = ref<'current' | 'overlay' | 'delta'>('overlay');
const orgChart = ref<any>(null);
const selectedRole = ref<any>(null);
const loading = ref(false);
const svgWidth = ref(1000);
const svgHeight = ref(600);

const scenarioId = computed(() => props.scenarioId || props.scenario?.id);

const visibleNodes = computed(() => {
    if (!orgChart.value?.roles) return [];

    return orgChart.value.roles.map((role: any, idx: number) => ({
        id: role.id,
        title: role.name,
        headcount: `${role.count} roles`,
        current_count: role.count,
        delta: role.delta || 0,
        status: role.delta > 0 ? 'new' : role.delta < 0 ? 'remove' : null,
        statusLabel:
            role.delta > 0
                ? '+' + role.delta
                : role.delta < 0
                  ? role.delta
                  : 'stable',
        successors: role.successors || [],
        x: (idx % 3) * 250 + 50,
        y: Math.floor(idx / 3) * 150 + 50,
    }));
});

const connectionLines = computed(() => {
    // Generate connector lines based on hierarchy
    return [];
});

const orgChartHeadcountAdded = computed(() => {
    return (
        orgChart.value?.roles?.reduce(
            (sum: number, r: any) => sum + Math.max(0, r.delta || 0),
            0,
        ) || 0
    );
});

const orgChartHeadcountRemoved = computed(() => {
    return (
        orgChart.value?.roles?.reduce(
            (sum: number, r: any) => sum + Math.max(0, -(r.delta || 0)),
            0,
        ) || 0
    );
});

onMounted(async () => {
    if (!scenarioId.value) return;

    loading.value = true;
    try {
        const response = await apiClient.get(
            `/strategic-planning/scenarios/${scenarioId.value}/org-chart`,
        );
        orgChart.value = response.data?.data;
    } catch (err) {
        console.error('Org chart error:', err);
    } finally {
        loading.value = false;
    }
});

function getRoleFill(node: any): string {
    if (!node.status) return '#dbeafe'; // blue-100
    if (node.status === 'new') return '#dcfce7'; // green-100
    if (node.status === 'remove') return '#fee2e2'; // red-100
    return '#fef3c7'; // yellow-100
}

function getStatusColor(status: string): string {
    if (status === 'new') return '#16a34a'; // green-600
    if (status === 'remove') return '#dc2626'; // red-600
    return '#ca8a04'; // yellow-600
}
</script>

<style scoped>
.org-chart-overlay {
    @apply bg-gray-50 p-4 dark:bg-gray-900;
}

svg {
    background: white;
}
</style>
