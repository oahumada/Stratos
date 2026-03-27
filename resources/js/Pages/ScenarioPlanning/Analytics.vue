<template>
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Scenario Planning
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    Compare, analyze, and plan organizational transformation scenarios
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
        <div class="border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
            <div class="flex space-x-8 min-w-max">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                        'py-3 px-1 font-medium text-sm transition',
                        activeTab === tab.id
                            ? 'border-b-2 border-emerald-600 text-emerald-600 dark:text-emerald-400'
                            : 'border-b-2 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300'
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
                    :risk-metrics="sampleRiskData"
                    :headcount-data="sampleHeadcountData"
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
                <ExecutionPlan :execution-plan="selectedScenarioExecutionPlan" />
            </div>

            <!-- Phase 2.5: Approval Dashboard Tab -->
            <div v-show="activeTab === 'dashboard'">
                <ApprovalDashboard />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import ScenarioComparison from '@/components/ScenarioPlanning/ScenarioComparison.vue'
import ScenarioMetrics from '@/components/ScenarioPlanning/ScenarioMetrics.vue'
import ScenarioTimeline from '@/components/ScenarioPlanning/ScenarioTimeline.vue'
import RiskAssessment from '@/components/ScenarioPlanning/RiskAssessment.vue'
import ApprovalMatrix from '@/components/ScenarioPlanning/ApprovalMatrix.vue'
import WorkflowTimeline from '@/components/ScenarioPlanning/WorkflowTimeline.vue'
import ExecutionPlan from '@/components/ScenarioPlanning/ExecutionPlan.vue'
import ApprovalDashboard from '@/components/ScenarioPlanning/ApprovalDashboard.vue'

// State
const activeTab = ref('comparison')
const selectedScenarioId = ref(1)
const selectedScenarioStatus = ref('draft')
const selectedScenarioSubmittedAt = ref(null)
const selectedScenarioApprovedAt = ref(null)
const selectedScenarioExecutionPlan = ref(null)
const showNewScenarioModal = ref(false)

// Tab Configuration
const tabs = [
    { id: 'comparison', name: '📊 Comparison' },
    { id: 'metrics', name: '📈 Metrics' },
    { id: 'timeline', name: '⏱️ Timeline' },
    { id: 'risk', name: '⚠️ Risk Assessment' },
    { id: 'approval', name: '✔️ Approvals' },
    { id: 'workflow', name: '🔄 Workflow' },
    { id: 'execution', name: '🚀 Execution' },
    { id: 'dashboard', name: '📋 Dashboard' }
]

// Sample Data (Replace with API calls in Phase 2)
const sampleScenarios = [
    {
        id: 1,
        name: 'Conservative Approach',
        iq: 72,
        timeline_months: 12,
        cost_estimate: '€2.5M'
    },
    {
        id: 2,
        name: 'Aggressive Modernization',
        iq: 88,
        timeline_months: 8,
        cost_estimate: '€4.2M'
    }
]

const sampleFinancialData = {
    total_impact: 285000,
    roi_percentage: 125.5,
    cost_breakdown: {
        training: 45000,
        hiring: 120000,
        reallocation: 78000,
        external_services: 42000
    },
    budget_allocation: {
        Q1: 2024,
        Q2: 2024,
        Q3: 2024,
        Q4: 2024
    },
    payback_period_months: 8.5
}

const sampleRiskData = {
    overall_risk: 35,
    probability: 0.45,
    impact: 0.65,
    risk_items: [
        { name: 'Talent Pool', probability: 0.75, impact: 0.8 },
        { name: 'Market Conditions', probability: 0.5, impact: 0.65 },
        { name: 'Adoption Rate', probability: 0.3, impact: 0.9 }
    ]
}

const sampleHeadcountData = {
    current: 150,
    target: 175,
    change_percent: 16.7,
    by_role: {
        'Data Analyst': 10,
        'ML Engineer': 8,
        'DevOps': 5,
        'Business Analyst': 2
    }
}
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
