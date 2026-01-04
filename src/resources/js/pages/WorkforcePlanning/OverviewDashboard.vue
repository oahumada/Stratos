<template>
  <div class="overview-dashboard">
    <v-container fluid>
      <v-row class="mb-4">
        <v-col cols="12" md="8">
          <h2>Scenario: {{ scenarioName }}</h2>
          <p class="text-subtitle-2">{{ scenarioDescription }}</p>
        </v-col>
        <v-col cols="12" md="4" class="text-right">
          <v-btn
            color="primary"
            @click="runAnalysis"
            :loading="analyzing"
            prepend-icon="mdi-refresh"
            class="mr-2"
          >
            Run Analysis
          </v-btn>
          <v-btn
            color="secondary"
            @click="downloadReport"
            prepend-icon="mdi-download"
          >
            Export
          </v-btn>
        </v-col>
      </v-row>

      <!-- Navigation Tabs -->
      <v-row class="mb-4">
        <v-col cols="12">
          <v-tabs v-model="activeTab" bg-color="primary">
            <v-tab value="overview">Overview</v-tab>
            <v-tab value="forecasts">Role Forecasts</v-tab>
            <v-tab value="matches">Talent Matches</v-tab>
            <v-tab value="gaps">Skill Gaps</v-tab>
            <v-tab value="succession">Succession Plans</v-tab>
          </v-tabs>
        </v-col>
      </v-row>

      <!-- Tab Content -->
      <div v-show="activeTab === 'overview'">

      <!-- KPI Cards -->
      <v-row class="mb-4">
        <v-col cols="12" md="3">
          <v-card>
            <v-card-text>
              <div class="text-overline">Total Headcount</div>
              <div class="text-h4">{{ analytics.total_headcount_current }}</div>
              <div class="text-caption">Current → {{ analytics.total_headcount_projected }}</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card>
            <v-card-text>
              <div class="text-overline">Net Growth</div>
              <div class="text-h4">{{ analytics.net_growth }}</div>
              <div class="text-caption">{{ analytics.net_growth > 0 ? 'Expansion' : 'Reduction' }}</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card>
            <v-card-text>
              <div class="text-overline">Internal Coverage</div>
              <div class="text-h4">{{ analytics.internal_coverage_percentage }}%</div>
              <div class="text-caption">External Gap: {{ analytics.external_gap_percentage }}%</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card>
            <v-card-text>
              <div class="text-overline">Succession Risk</div>
              <div class="text-h4">{{ analytics.succession_risk_percentage }}%</div>
              <div class="text-caption">Critical roles at risk</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Charts Row -->
      <v-row class="mb-4">
        <v-col cols="12" md="6">
          <v-card>
            <v-card-title>Headcount Forecast</v-card-title>
            <v-card-text>
              <canvas ref="headcountChart" />
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="6">
          <v-card>
            <v-card-title>Skill Coverage by Priority</v-card-title>
            <v-card-text>
              <canvas ref="skillCoverageChart" />
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Risk Summary -->
      <v-row>
        <v-col cols="12" md="6">
          <v-card>
            <v-card-title>Risk Summary</v-card-title>
            <v-list>
              <v-list-item>
                <v-list-item-title>High Risk Positions</v-list-item-title>
                <v-list-item-subtitle>{{ analytics.high_risk_positions }}</v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>Medium Risk Positions</v-list-item-title>
                <v-list-item-subtitle>{{ analytics.medium_risk_positions }}</v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>Critical Skills at Risk</v-list-item-title>
                <v-list-item-subtitle>{{ analytics.critical_skills_at_risk }}</v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card>
        </v-col>

        <v-col cols="12" md="6">
          <v-card>
            <v-card-title>Cost Estimates</v-card-title>
            <v-list>
              <v-list-item>
                <v-list-item-title>Recruitment Cost</v-list-item-title>
                <v-list-item-subtitle>${{ formatNumber(analytics.estimated_recruitment_cost) }}</v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>Training Cost</v-list-item-title>
                <v-list-item-subtitle>${{ formatNumber(analytics.estimated_training_cost) }}</v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>External Hiring Timeline</v-list-item-title>
                <v-list-item-subtitle>{{ analytics.estimated_external_hiring_months }} months</v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card>
        </v-col>
      </v-row>

      <!-- Action Buttons -->
      <v-row class="mt-4">
        <v-col cols="12">
          <v-btn
            color="primary"
            @click="runAnalysis"
            :loading="analyzing"
            prepend-icon="mdi-refresh"
          >
            Run Full Analysis
          </v-btn>
          <v-btn
            color="secondary"
            @click="downloadReport"
            prepend-icon="mdi-download"
            class="ml-2"
          >
            Download Report
          </v-btn>
        </v-col>
      </v-row>
      </div>

      <!-- Role Forecasts Tab -->
      <div v-show="activeTab === 'forecasts'">
        <RoleForecastsTable :scenarioId="scenarioId" />
      </div>

      <!-- Talent Matches Tab -->
      <div v-show="activeTab === 'matches'">
        <MatchingResults :scenarioId="scenarioId" />
      </div>

      <!-- Skill Gaps Tab -->
      <div v-show="activeTab === 'gaps'">
        <SkillGapsMatrix :scenarioId="scenarioId" />
      </div>

      <!-- Succession Plans Tab -->
      <div v-show="activeTab === 'succession'">
        <SuccessionPlanCard :scenarioId="scenarioId" />
      </div>

    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { useApi } from '@/composables/useApi'
import { useNotification } from '@/composables/useNotification'
import { Chart, registerables } from 'chart.js'
import RoleForecastsTable from './RoleForecastsTable.vue'
import MatchingResults from './MatchingResults.vue'
import SkillGapsMatrix from './SkillGapsMatrix.vue'
import SuccessionPlanCard from './SuccessionPlanCard.vue'

defineOptions({ layout: AppLayout })

Chart.register(...registerables)
interface Analytics {
  total_headcount_current: number
  total_headcount_projected: number
  net_growth: number
  internal_coverage_percentage: number
  external_gap_percentage: number
  total_skills_required: number
  skills_with_gaps: number
  critical_skills_at_risk: number
  critical_roles: number
  critical_roles_with_successor: number
  succession_risk_percentage: number
  estimated_recruitment_cost: number
  estimated_training_cost: number
  estimated_external_hiring_months: number
  high_risk_positions: number
  medium_risk_positions: number
}

interface Props {
  id: number | string
}

const props = defineProps<Props>()
const page = usePage()
const api = useApi()
const { showSuccess, showError } = useNotification()

const scenarioId = computed(() => props.id)
const scenarioName = ref('')
const scenarioDescription = ref('')
const analyzing = ref(false)
const activeTab = ref('overview')

const analytics = ref<Analytics>({
  total_headcount_current: 0,
  total_headcount_projected: 0,
  net_growth: 0,
  internal_coverage_percentage: 0,
  external_gap_percentage: 0,
  total_skills_required: 0,
  skills_with_gaps: 0,
  critical_skills_at_risk: 0,
  critical_roles: 0,
  critical_roles_with_successor: 0,
  succession_risk_percentage: 0,
  estimated_recruitment_cost: 0,
  estimated_training_cost: 0,
  estimated_external_hiring_months: 0,
  high_risk_positions: 0,
  medium_risk_positions: 0,
})

const headcountChart = ref()
const skillCoverageChart = ref()

const formatNumber = (num: number): string => {
  return new Intl.NumberFormat('en-US').format(num)
}

const loadScenario = async () => {
  try {
    const response = await api.get(`/api/v1/workforce-planning/scenarios/${scenarioId.value}`)
    const scenario = response.data
    scenarioName.value = scenario.name
    scenarioDescription.value = scenario.description

    await loadAnalytics()
  } catch (error) {
    showError('Failed to load scenario')
  }
}

const loadAnalytics = async () => {
  try {
    const response = await api.get(
      `/api/v1/workforce-planning/scenarios/${scenarioId.value}/analytics`
    )
    
    if (response.data) {
      analytics.value = response.data
      
      // Initialize charts after data is loaded
      setTimeout(() => {
        initializeCharts()
      }, 100)
    }
  } catch (error: any) {
    // If analytics don't exist yet (404), show a helpful message
    if (error.status === 404) {
      showError('No analytics available yet. Click "Run Analysis" to generate data.')
    } else {
      showError('Failed to load analytics')
    }
  }
}

const initializeCharts = () => {
  // Headcount forecast chart
  const headcountCtx = headcountChart.value?.getContext('2d')
  if (headcountCtx) {
    new Chart(headcountCtx, {
      type: 'line',
      data: {
        labels: ['Current', 'Projected'],
        datasets: [
          {
            label: 'Headcount',
            data: [analytics.value.total_headcount_current, analytics.value.total_headcount_projected],
            borderColor: '#1976d2',
            backgroundColor: 'rgba(25, 118, 210, 0.1)',
            tension: 0.3,
          },
        ],
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false,
          },
        },
      },
    })
  }

  // Skill coverage chart
  const skillCtx = skillCoverageChart.value?.getContext('2d')
  if (skillCtx) {
    new Chart(skillCtx, {
      type: 'doughnut',
      data: {
        labels: ['With Skill Gaps', 'Covered'],
        datasets: [
          {
            data: [
              analytics.value.skills_with_gaps,
              analytics.value.total_skills_required - analytics.value.skills_with_gaps,
            ],
            backgroundColor: ['#ff6b6b', '#51cf66'],
          },
        ],
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom',
          },
        },
      },
    })
  }
}

const runAnalysis = async () => {
  analyzing.value = true
  try {
    await api.post(`/api/v1/workforce-planning/scenarios/${scenarioId.value}/analyze`)
    showSuccess('Analysis completed successfully')
    await loadAnalytics()
  } catch (error) {
    showError('Failed to run analysis')
  } finally {
    analyzing.value = false
  }
}

const downloadReport = () => {
  // TODO: Implement report download
  showSuccess('Report download not yet implemented')
}

onMounted(() => {
  loadScenario()
})
</script>

<style scoped>
.overview-dashboard {
  padding: 20px;
}
</style>
