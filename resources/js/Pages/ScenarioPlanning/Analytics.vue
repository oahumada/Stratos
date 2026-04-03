<template>
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Scenario Planning
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    Compare, analyze, and plan organizational transformation
                    scenarios
                </p>
            </div>
            <button
                @click="showNewScenarioModal = true"
                class="rounded-lg bg-emerald-600 px-4 py-2 text-white transition hover:bg-emerald-700 dark:hover:bg-emerald-800"
            >
                + New Scenario
            </button>
        </div>

        <!-- Tab Navigation -->
        <div
            class="overflow-x-auto border-b border-gray-200 dark:border-gray-700"
        >
            <div class="flex min-w-max space-x-8">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                        'px-1 py-3 text-sm font-medium transition',
                        activeTab === tab.id
                            ? 'border-b-2 border-emerald-600 text-emerald-600 dark:text-emerald-400'
                            : 'border-b-2 border-transparent text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300',
                    ]"
                >
                    {{ tab.name }}
                </button>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="min-h-96">
            <!-- Comparison Tab -->
            <div v-show="activeTab === 'comparison'">
                <ScenarioComparison />
            </div>

            <!-- Metrics Tab -->
            <div v-show="activeTab === 'metrics'">
                <ScenarioMetrics
                    :scenario-id="selectedScenarioId"
                    :financial-impact="sampleFinancialData"
                    :risk-metrics="metricsRiskData ?? sampleRiskData"
                    :headcount-data="
                        metricsHeadcountData ?? sampleHeadcountData
                    "
                />
            </div>

            <!-- Timeline Tab -->
            <div v-show="activeTab === 'timeline'">
                <ScenarioTimeline :scenarios="sampleScenarios" />
            </div>

            <!-- Risk Assessment Tab -->
            <div v-show="activeTab === 'risk'">
                <RiskAssessment />
            </div>

            <!-- Phase 2: Approval Tab -->
            <div v-show="activeTab === 'approval'">
                <ApprovalMatrix :scenario-id="selectedScenarioId" />
            </div>

            <!-- Phase 2: Workflow Tab -->
            <div v-show="activeTab === 'workflow'">
                <WorkflowTimeline
                    :decision-status="selectedScenarioStatus"
                    :submitted-at="selectedScenarioSubmittedAt"
                    :approved-at="selectedScenarioApprovedAt"
                />
            </div>

            <!-- Phase 2: Execution Tab -->
            <div v-show="activeTab === 'execution'">
                <ExecutionPlan
                    :execution-plan="selectedScenarioExecutionPlan"
                />
            </div>

            <!-- Phase 2.5: Approval Dashboard Tab -->
            <div v-show="activeTab === 'dashboard'">
                <ApprovalDashboard />
            </div>

            <!-- Phase 3.3: Executive Summary Tab -->
            <div v-show="activeTab === 'executive'">
                <ExecutiveSummary :scenario-id="selectedScenarioId" />
            </div>

            <!-- Phase 3.4: Org Chart Tab -->
            <div v-show="activeTab === 'orgchart'">
                <OrgChartOverlay :scenario-id="selectedScenarioId" />
            </div>

            <!-- Phase 3.4: What-If Analysis Tab -->
            <div v-show="activeTab === 'whatif'">
                <WhatIfAnalyzer :scenario-id="selectedScenarioId" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import ApprovalDashboard from '@/components/ScenarioPlanning/ApprovalDashboard.vue';
import ApprovalMatrix from '@/components/ScenarioPlanning/ApprovalMatrix.vue';
import ExecutionPlan from '@/components/ScenarioPlanning/ExecutionPlan.vue';
import RiskAssessment from '@/components/ScenarioPlanning/RiskAssessment.vue';
import ScenarioComparison from '@/components/ScenarioPlanning/ScenarioComparison.vue';
import ScenarioMetrics from '@/components/ScenarioPlanning/ScenarioMetrics.vue';
import ScenarioTimeline from '@/components/ScenarioPlanning/ScenarioTimeline.vue';
import WorkflowTimeline from '@/components/ScenarioPlanning/WorkflowTimeline.vue';
// Phase 3.3-3.4: Executive Summary & Visualization Components
import ExecutiveSummary from '@/components/ScenarioPlanning/ExecutiveSummary.vue';
import OrgChartOverlay from '@/components/ScenarioPlanning/OrgChartOverlay.vue';
import WhatIfAnalyzer from '@/components/ScenarioPlanning/WhatIfAnalyzer.vue';
import { useApi } from '@/composables/useApi';
import { onMounted, ref, watch } from 'vue';

// State
const activeTab = ref('comparison');
const selectedScenarioId = ref(1);
const selectedScenarioStatus = ref('draft');
const selectedScenarioSubmittedAt = ref(null);
const selectedScenarioApprovedAt = ref(null);
const selectedScenarioExecutionPlan = ref(null);
const showNewScenarioModal = ref(false);
const metricsRiskData = ref<any | null>(null);
const metricsHeadcountData = ref<any | null>(null);
const { get } = useApi();

const fetchPeopleExperience = async (): Promise<void> => {
    try {
        const response = await get(
            `/api/scenarios/${selectedScenarioId.value}/people-experience`,
        );

        if (response?.headcount) {
            metricsHeadcountData.value = {
                current: Number(response.headcount.current ?? 0),
                projected: Number(response.headcount.projected ?? 0),
                change: Number(response.headcount.change ?? 0),
            };
        }

        if (
            response?.people_experience?.avg_stress_level !== undefined &&
            response?.people_experience?.avg_stress_level !== null
        ) {
            const stress = Number(response.people_experience.avg_stress_level);
            const overallRisk = Math.min(
                100,
                Math.max(0, Math.round((stress / 5) * 100)),
            );
            metricsRiskData.value = {
                overall_risk: overallRisk,
                probability: Number((overallRisk / 100).toFixed(2)),
                impact: Number((overallRisk / 100).toFixed(2)),
            };
        }
    } catch {
        metricsRiskData.value = null;
        metricsHeadcountData.value = null;
    }
};

onMounted(() => {
    fetchPeopleExperience();
});

watch(selectedScenarioId, () => {
    fetchPeopleExperience();
});

// Tab Configuration
const tabs = [
    { id: 'comparison', name: '📊 Comparison' },
    { id: 'metrics', name: '📈 Metrics' },
    { id: 'timeline', name: '⏱️ Timeline' },
    { id: 'risk', name: '⚠️ Risk Assessment' },
    { id: 'approval', name: '✔️ Approvals' },
    { id: 'workflow', name: '🔄 Workflow' },
    { id: 'execution', name: '🚀 Execution' },
    { id: 'dashboard', name: '📋 Dashboard' },
    // Phase 3.3-3.4: Executive Insights
    { id: 'executive', name: '👔 Executive Summary' },
    { id: 'orgchart', name: '🏢 Org Chart' },
    { id: 'whatif', name: '🎯 What-If Analysis' },
];

// Sample Data (Replace with API calls in Phase 2)
const sampleScenarios = [
    {
        id: 1,
        name: 'Conservative Approach',
        iq: 72,
        timeline_months: 12,
        cost_estimate: '€2.5M',
    },
    {
        id: 2,
        name: 'Aggressive Modernization',
        iq: 88,
        timeline_months: 8,
        cost_estimate: '€4.2M',
    },
];

const sampleFinancialData = {
    total_impact: 285000,
    roi_percentage: 125.5,
    cost_breakdown: {
        training: 45000,
        hiring: 120000,
        reallocation: 78000,
        external_services: 42000,
    },
    budget_allocation: {
        Q1: 2024,
        Q2: 2024,
        Q3: 2024,
        Q4: 2024,
    },
    payback_period_months: 8.5,
};

const sampleRiskData = {
    overall_risk: 35,
    probability: 0.45,
    impact: 0.65,
    risk_items: [
        { name: 'Talent Pool', probability: 0.75, impact: 0.8 },
        { name: 'Market Conditions', probability: 0.5, impact: 0.65 },
        { name: 'Adoption Rate', probability: 0.3, impact: 0.9 },
    ],
};

const sampleHeadcountData = {
    current: 150,
    projected: 175,
    change: 25,
};
</script>

<style scoped>
/* Smooth fade transitions for tab content */
div[v-show] {
    animation: fadeIn 0.2s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
