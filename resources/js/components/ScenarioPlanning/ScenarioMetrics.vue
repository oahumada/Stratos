<template>
  <div class="scenario-metrics space-y-6">
    <!-- Primary Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Cost Metric Card -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
          <h4 class="font-semibold text-gray-700 dark:text-gray-300">Total Cost</h4>
          <span class="text-2xl text-blue-500">💰</span>
        </div>
        
        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
          {{ formatCurrency(totalCost) }}
        </div>
        
        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
          ROI: {{ roiPercentage }}%
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
          <div
            class="bg-blue-500 h-2 rounded-full transition-all"
            :style="{ width: `${Math.min(roiPercentage / 100 * 100, 100)}%` }"
          ></div>
        </div>
      </div>

      <!-- Headcount Metric Card -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
          <h4 class="font-semibold text-gray-700 dark:text-gray-300">Headcount Impact</h4>
          <span class="text-2xl text-purple-500">👥</span>
        </div>
        
        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
          +{{ headcountChange }}
        </div>
        
        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
          {{ headcountPercentage }}% increase from current
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
          <div
            class="bg-purple-500 h-2 rounded-full transition-all"
            :style="{ width: `${Math.min(Math.abs(headcountPercentage) / 100 * 100, 100)}%` }"
          ></div>
        </div>
      </div>

      <!-- Risk Score Card -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
          <h4 class="font-semibold text-gray-700 dark:text-gray-300">Risk Score</h4>
          <span class="text-2xl text-amber-500">⚠️</span>
        </div>
        
        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
          {{ riskScore }}/100
        </div>
        
        <div class="text-sm" :class="riskLevel.color">
          {{ riskLevel.label }}
        </div>

        <!-- Gauge visualization -->
        <div class="mt-4 w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
          <div
            class="h-full transition-all"
            :style="{ width: `${riskScore}%`, backgroundColor: riskLevel.bgColor }"
          ></div>
        </div>
      </div>

      <!-- ROI Score Card -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
          <h4 class="font-semibold text-gray-700 dark:text-gray-300">ROI Score</h4>
          <span class="text-2xl text-emerald-500">📈</span>
        </div>
        
        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
          {{ roiScoreValue }}/100
        </div>
        
        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
          Payback: {{ paybackMonths }} months
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
          <div
            class="bg-emerald-500 h-2 rounded-full transition-all"
            :style="{ width: `${roiScoreValue}%` }"
          ></div>
        </div>
      </div>
    </div>

    <!-- Breakdown Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <!-- Cost Breakdown -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Cost Breakdown</h4>
        
        <div class="space-y-3">
          <div v-for="(item, idx) in costBreakdown" :key="idx">
            <div class="flex justify-between items-center mb-1">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ item.label }}
              </span>
              <span class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ (item.percentage).toFixed(0) }}%
              </span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div
                class="h-2 rounded-full transition-all"
                :style="{ width: `${item.percentage}%`, backgroundColor: item.color }"
              ></div>
            </div>
            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
              {{ formatCurrency(item.amount) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Timeline Breakdown -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Cost by Phase</h4>
        
        <div class="space-y-3">
          <div v-for="(phase, idx) in phaseBreakdown" :key="idx">
            <div class="flex justify-between items-center mb-1">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ phase.label }}
              </span>
              <span class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ phase.percentage.toFixed(0) }}%
              </span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div
                class="h-2 rounded-full transition-all"
                :style="{ width: `${phase.percentage}%`, backgroundColor: phase.color }"
              ></div>
            </div>
            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
              {{ formatCurrency(phase.amount) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Key Metrics Summary -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900 dark:to-purple-900 rounded-lg p-6">
      <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Key Metrics Summary</h4>
      
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
          <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
            {{ timelineMonths }}
          </div>
          <div class="text-sm text-gray-600 dark:text-gray-400">Timeline (months)</div>
        </div>
        
        <div>
          <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
            {{ capacityUtilization }}%
          </div>
          <div class="text-sm text-gray-600 dark:text-gray-400">Capacity Utilization</div>
        </div>
        
        <div>
          <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
            {{ criticalgapCount }}
          </div>
          <div class="text-sm text-gray-600 dark:text-gray-400">Critical Gaps</div>
        </div>
        
        <div>
          <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">
            {{ successRate }}%
          </div>
          <div class="text-sm text-gray-600 dark:text-gray-400">Success Rate</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  scenarioId?: number
  financialImpact?: {
    total_impact: number
    roi_percentage: number
    cost_breakdown: Record<string, number>
    budget_allocation: Record<string, number>
    payback_period_months: number
  }
  riskMetrics?: {
    overall_risk: number
    probability: number
    impact: number
  }
  headcountData?: {
    current: number
    projected: number
    change: number
  }
}

const props = withDefaults(defineProps<Props>(), {
  scenarioId: 1,
  financialImpact: () => ({
    total_impact: 285000,
    roi_percentage: 125.5,
    cost_breakdown: {
      training: 85000,
      hiring: 45000,
      reallocation: 32000,
      external_services: 123000,
    },
    budget_allocation: {
      month_1: 45000,
      month_2: 60000,
      month_3: 90000,
      month_4: 45000,
      month_5: 45000,
    },
    payback_period_months: 8.5,
  }),
  riskMetrics: () => ({
    overall_risk: 35,
    probability: 0.45,
    impact: 0.65,
  }),
  headcountData: () => ({
    current: 250,
    projected: 285,
    change: 35,
  }),
})

const totalCost = computed(() => props.financialImpact?.total_impact ?? 0)
const roiPercentage = computed(() => props.financialImpact?.roi_percentage ?? 0)
const riskScore = computed(() => props.riskMetrics?.overall_risk ?? 0)
const paybackMonths = computed(() => props.financialImpact?.payback_period_months ?? 0)
const timelineMonths = computed(() => 12)
const capacityUtilization = computed(() => 82)
const criticalgapCount = computed(() => 8)
const successRate = computed(() => 92)

const headcountChange = computed(() => props.headcountData?.change ?? 0)
const headcountPercentage = computed(() => {
  const current = props.headcountData?.current ?? 250
  const projected = props.headcountData?.projected ?? 285
  return ((projected - current) / current * 100).toFixed(1)
})

const roiScoreValue = computed(() => {
  const roi = roiPercentage.value
  return Math.min(Math.max(roi / 200 * 100, 0), 100)
})

const riskLevel = computed(() => {
  const score = riskScore.value
  if (score < 30) return { label: 'Low Risk', color: 'text-emerald-600 dark:text-emerald-400', bgColor: '#10b981' }
  if (score < 60) return { label: 'Medium Risk', color: 'text-amber-600 dark:text-amber-400', bgColor: '#f59e0b' }
  return { label: 'High Risk', color: 'text-red-600 dark:text-red-400', bgColor: '#ef4444' }
})

const costBreakdown = computed(() => {
  const breakdown = props.financialImpact?.cost_breakdown ?? {}
  const total = Object.values(breakdown).reduce((a: number, b: number) => a + b, 0)
  
  return [
    { label: 'Training', amount: breakdown.training ?? 0, percentage: (breakdown.training ?? 0) / total * 100, color: '#3b82f6' },
    { label: 'Hiring', amount: breakdown.hiring ?? 0, percentage: (breakdown.hiring ?? 0) / total * 100, color: '#8b5cf6' },
    { label: 'Reallocation', amount: breakdown.reallocation ?? 0, percentage: (breakdown.reallocation ?? 0) / total * 100, color: '#10b981' },
    { label: 'Ext. Services', amount: breakdown.external_services ?? 0, percentage: (breakdown.external_services ?? 0) / total * 100, color: '#f59e0b' },
  ]
})

const phaseBreakdown = computed(() => {
  const allocation = props.financialImpact?.budget_allocation ?? {}
  const total = Object.values(allocation).reduce((a: number, b: number) => a + b, 0)
  
  return [
    { label: 'Month 1-2', amount: (allocation.month_1 ?? 0) + (allocation.month_2 ?? 0), percentage: ((allocation.month_1 ?? 0) + (allocation.month_2 ?? 0)) / total * 100, color: '#3b82f6' },
    { label: 'Month 3-4', amount: (allocation.month_3 ?? 0) + (allocation.month_4 ?? 0), percentage: ((allocation.month_3 ?? 0) + (allocation.month_4 ?? 0)) / total * 100, color: '#8b5cf6' },
    { label: 'Month 5+', amount: allocation.month_5 ?? 0, percentage: (allocation.month_5 ?? 0) / total * 100, color: '#10b981' },
  ]
})

const formatCurrency = (amount: number): string => {
  if (amount >= 1000000) return `$${(amount / 1000000).toFixed(1)}M`
  if (amount >= 1000) return `$${(amount / 1000).toFixed(0)}K`
  return `$${amount}`
}
</script>

<style scoped>
.scenario-metrics {
  /* Component-specific styles */
}
</style>
