<template>
    <div class="executive-summary space-y-6">
        <!-- Header -->
        <div
            class="rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-white shadow-lg"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Executive Summary</h1>
                    <p class="mt-2 text-indigo-100">{{ scenario?.name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold">Decision Recommendation</p>
                    <p
                        :class="{
                            'text-2xl font-bold': true,
                            'text-green-300':
                                summary?.decision_recommendation
                                    ?.recommendation === 'proceed',
                            'text-yellow-300':
                                summary?.decision_recommendation
                                    ?.recommendation === 'revise',
                            'text-red-300':
                                summary?.decision_recommendation
                                    ?.recommendation === 'reject',
                        }"
                    >
                        {{
                            summary?.decision_recommendation?.recommendation?.toUpperCase()
                        }}
                    </p>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-sm">
                <span
                    >Confidence:
                    {{ summary?.decision_recommendation?.confidence }}%</span
                >
                <span v-if="summary?.decision_recommendation?.ready_to_activate"
                    >✓ Ready to Activate</span
                >
            </div>
        </div>

        <!-- KPI Cards Grid -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="(kpi, idx) in summary?.kpis"
                :key="idx"
                class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
            >
                <div class="flex items-center justify-between">
                    <h4 class="font-semibold text-gray-700 dark:text-gray-300">
                        {{ kpi.title }}
                    </h4>
                    <span class="text-2xl">{{ kpi.icon }}</span>
                </div>

                <div
                    :class="{
                        'mt-3 text-3xl font-bold': true,
                        'text-green-600': kpi.status === 'success',
                        'text-yellow-600': kpi.status === 'caution',
                        'text-red-600':
                            kpi.status === 'warning' || kpi.status === 'danger',
                        'text-gray-900 dark:text-white': kpi.status === 'info',
                    }"
                >
                    {{ kpi.value }}
                    <span class="text-lg">{{ kpi.unit }}</span>
                </div>

                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                    {{ kpi.comparison }}
                </div>

                <!-- Status Badge -->
                <div class="mt-3">
                    <span
                        :class="{
                            'inline-block rounded-full px-2 py-1 text-xs font-semibold': true,
                            'bg-green-100 text-green-800':
                                kpi.status === 'success',
                            'bg-yellow-100 text-yellow-800':
                                kpi.status === 'caution',
                            'bg-red-100 text-red-800':
                                kpi.status === 'warning' ||
                                kpi.status === 'danger',
                        }"
                    >
                        {{ kpi.status.toUpperCase() }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Decision Reasoning -->
        <div
            v-if="summary?.decision_recommendation"
            class="rounded-lg bg-blue-50 p-6 dark:bg-blue-900/20"
        >
            <h3 class="mb-3 font-semibold text-gray-800 dark:text-gray-200">
                Decision Rationale
            </h3>
            <ul class="space-y-2">
                <li
                    v-for="(reason, idx) in summary.decision_recommendation
                        .reasoning"
                    :key="idx"
                    class="flex items-start"
                >
                    <span class="mr-3 text-blue-600">▸</span>
                    <span class="text-gray-700 dark:text-gray-300">{{
                        reason
                    }}</span>
                </li>
            </ul>
        </div>

        <!-- Risk Heatmap -->
        <div
            v-if="summary?.risk_heatmap"
            class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800"
        >
            <h3 class="mb-4 font-semibold text-gray-800 dark:text-gray-200">
                Risk Assessment Heatmap
            </h3>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <!-- 2x2 Matrix -->
                <div class="grid grid-cols-2 gap-2">
                    <div
                        class="rounded bg-green-100 p-4 text-center dark:bg-green-900/20"
                    >
                        <p class="text-xs font-semibold">LOW</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{
                                summary?.risk_heatmap?.filter(
                                    (r) => r.likelihood === 'low',
                                ).length
                            }}
                        </p>
                    </div>
                    <div
                        class="rounded bg-yellow-100 p-4 text-center dark:bg-yellow-900/20"
                    >
                        <p class="text-xs font-semibold">MEDIUM</p>
                        <p class="text-2xl font-bold text-yellow-600">
                            {{
                                summary?.risk_heatmap?.filter(
                                    (r) => r.likelihood === 'medium',
                                ).length
                            }}
                        </p>
                    </div>
                    <div
                        class="rounded bg-orange-100 p-4 text-center dark:bg-orange-900/20"
                    >
                        <p class="text-xs font-semibold">HIGH</p>
                        <p class="text-2xl font-bold text-orange-600">
                            {{
                                summary?.risk_heatmap?.filter(
                                    (r) => r.likelihood === 'high',
                                ).length
                            }}
                        </p>
                    </div>
                    <div
                        class="rounded bg-red-100 p-4 text-center dark:bg-red-900/20"
                    >
                        <p class="text-xs font-semibold">CRITICAL</p>
                        <p class="text-2xl font-bold text-red-600">0</p>
                    </div>
                </div>

                <!-- Risk List -->
                <div class="space-y-2">
                    <div
                        v-for="risk in summary?.risk_heatmap?.slice(0, 5)"
                        :key="risk.type"
                        class="rounded border-l-4 border-yellow-500 bg-yellow-50 p-3 dark:bg-yellow-900/20"
                    >
                        <p
                            class="font-semibold text-gray-800 dark:text-gray-200"
                        >
                            {{ risk.type }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ risk.description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Readiness Assessment -->
        <div
            v-if="summary?.readiness_assessment"
            class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800"
        >
            <h3 class="mb-4 font-semibold text-gray-800 dark:text-gray-200">
                Activation Readiness
            </h3>

            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm font-semibold">Overall Readiness</span>
                    <span class="text-lg font-bold"
                        >{{
                            summary?.readiness_assessment?.ready_percentage
                        }}%</span
                    >
                </div>
                <div class="h-2 rounded-full bg-gray-200">
                    <div
                        class="h-2 rounded-full bg-indigo-600 transition-all"
                        :style="{
                            width: `${summary?.readiness_assessment?.ready_percentage}%`,
                        }"
                    ></div>
                </div>
            </div>

            <!-- Checks -->
            <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                <div
                    v-for="check in summary?.readiness_assessment?.checks"
                    :key="check.name"
                    :class="{
                        'flex items-center gap-3 rounded p-2': true,
                        'bg-green-50': check.status === 'pass',
                        'bg-yellow-50': check.status === 'pending',
                    }"
                >
                    <span v-if="check.status === 'pass'" class="text-green-600"
                        >✓</span
                    >
                    <span v-else class="text-yellow-600">○</span>
                    <div>
                        <p class="text-sm font-semibold">{{ check.name }}</p>
                        <p class="text-xs text-gray-600">
                            {{ check.description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div
            v-if="summary?.decision_recommendation?.next_steps"
            class="rounded-lg bg-indigo-50 p-6 dark:bg-indigo-900/20"
        >
            <h3 class="mb-3 font-semibold text-gray-800 dark:text-gray-200">
                Next Steps
            </h3>
            <ol class="space-y-2">
                <li
                    v-for="(step, idx) in summary.decision_recommendation
                        .next_steps"
                    :key="idx"
                    class="text-sm text-gray-700 dark:text-gray-300"
                >
                    {{ step }}
                </li>
            </ol>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3">
            <button
                class="flex-1 rounded-lg bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-700"
                @click="exportSummary('pdf')"
            >
                📥 Export as PDF
            </button>
            <button
                class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                @click="shareSummary"
            >
                🔗 Share
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { apiClient } from '@/services/apiClient';
import { computed, onMounted, ref } from 'vue';

interface Props {
    scenario: any;
    scenarioId?: number;
}

const props = withDefaults(defineProps<Props>(), {});

const summary = ref<any>(null);
const loading = ref(false);
const error = ref<string | null>(null);

const scenarioId = computed(() => props.scenarioId || props.scenario?.id);

onMounted(async () => {
    if (!scenarioId.value) return;

    loading.value = true;
    try {
        const response = await apiClient.get(
            `/strategic-planning/scenarios/${scenarioId.value}/executive-summary`,
        );
        summary.value = response.data?.data;
    } catch (err: any) {
        error.value = err.message || 'Failed to load executive summary';
        console.error('Executive summary error:', err);
    } finally {
        loading.value = false;
    }
});

async function exportSummary(format: 'pdf' | 'pptx') {
    try {
        const response = await apiClient.post(
            `/strategic-planning/scenarios/${scenarioId.value}/executive-summary/export`,
            { format },
        );
        if (response.data?.download_url) {
            window.location.href = response.data.download_url;
        }
    } catch (err) {
        error.value = 'Export failed';
        console.error('Export error:', err);
    }
}

function shareSummary() {
    const text = `Check out this scenario summary for ${props.scenario?.name}`;
    const url = `${window.location.origin}/scenarios/${scenarioId.value}`;

    if (navigator.share) {
        navigator.share({ title: 'Scenario Summary', text, url });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(url);
        alert('Link copied to clipboard');
    }
}
</script>

<style scoped>
.executive-summary {
    @apply bg-gray-50 p-6 dark:bg-gray-900;
}
</style>
