<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { useApi } from '@/composables/useApi'
import { useNotification } from '@/composables/useNotification'
import OverviewDashboard from './OverviewDashboard.vue'
import SkillGapsMatrix from './SkillGapsMatrix.vue'
import MatchingResults from './MatchingResults.vue'
import RoleForecastsTable from './RoleForecastsTable.vue'
import SuccessionPlanCard from './SuccessionPlanCard.vue'
import ClosureStrategies from './ClosureStrategies.vue'
import ScenarioComparison from './ScenarioComparison.vue'

interface Props {
  id: number | string
}

interface ScenarioPayload {
  id: number
  name: string
  description?: string
  scenario_type?: string
  status: string
  time_horizon_weeks?: number
  estimated_budget?: number
  created_at?: string
}

defineOptions({ layout: AppLayout })

const props = defineProps<Props>()
const api = useApi()
const { showSuccess, showError } = useNotification()

const scenario = ref<ScenarioPayload | null>(null)
const loading = ref(false)
const refreshing = ref(false)
const activeTab = ref<'overview' | 'gaps' | 'strategies' | 'matches' | 'forecasts' | 'comparisons' | 'succession'>('overview')

const scenarioId = computed(() => {
  const value = typeof props.id === 'string' ? parseInt(props.id, 10) : props.id
  return value || 0
})

const loadScenario = async () => {
  loading.value = true
  try {
    const res: any = await api.get(`/api/v1/workforce-planning/workforce-scenarios/${scenarioId.value}`)
    scenario.value = res?.data || res
  } catch (error) {
    showError('No se pudo cargar el escenario')
  } finally {
    loading.value = false
  }
}

const calculateGaps = async () => {
  refreshing.value = true
  try {
    await api.post(`/api/v1/workforce-planning/workforce-scenarios/${scenarioId.value}/calculate-gaps`)
    showSuccess('Brechas recalculadas')
  } catch (error) {
    showError('Error al recalcular brechas')
  } finally {
    refreshing.value = false
  }
}

const refreshStrategies = async () => {
  refreshing.value = true
  try {
    await api.post(`/api/v1/workforce-planning/workforce-scenarios/${scenarioId.value}/refresh-suggested-strategies`)
    showSuccess('Estrategias sugeridas actualizadas')
  } catch (error) {
    showError('Error al refrescar estrategias')
  } finally {
    refreshing.value = false
  }
}

onMounted(() => {
  loadScenario()
})
</script>

<template>
  <v-container fluid class="scenario-detail">
    <v-row class="mb-4 align-center">
      <v-col cols="12" md="8">
        <h2 class="mb-1">{{ scenario?.name || 'Escenario' }}</h2>
        <p class="text-medium-emphasis mb-0">{{ scenario?.description }}</p>
        <div class="text-caption text-uppercase text-medium-emphasis">
          Tipo: {{ scenario?.scenario_type || 'custom' }} · Estado: {{ scenario?.status }} · Horizonte: {{ scenario?.time_horizon_weeks || '—' }} semanas
        </div>
      </v-col>
      <v-col cols="12" md="4" class="text-right">
        <v-btn class="mr-2" color="primary" variant="outlined" :loading="refreshing" @click="calculateGaps" prepend-icon="mdi-calculator-variant">
          Calcular brechas
        </v-btn>
        <v-btn color="primary" :loading="refreshing" @click="refreshStrategies" prepend-icon="mdi-lightbulb-on-outline">
          Sugerir estrategias
        </v-btn>
      </v-col>
    </v-row>

    <v-card>
      <v-tabs v-model="activeTab" bg-color="surface">
        <v-tab value="overview" prepend-icon="mdi-view-dashboard">Overview</v-tab>
        <v-tab value="gaps" prepend-icon="mdi-matrix">Brechas</v-tab>
        <v-tab value="strategies" prepend-icon="mdi-source-branch">Estrategias</v-tab>
        <v-tab value="matches" prepend-icon="mdi-account-search">Matching</v-tab>
        <v-tab value="forecasts" prepend-icon="mdi-chart-timeline-variant">Forecasts</v-tab>
        <v-tab value="comparisons" prepend-icon="mdi-compare">Comparaciones</v-tab>
        <v-tab value="succession" prepend-icon="mdi-family-tree">Sucesión</v-tab>
      </v-tabs>

      <v-divider></v-divider>

      <v-card-text>
        <div v-show="activeTab === 'overview'">
          <OverviewDashboard :id="scenarioId" />
        </div>
        <div v-show="activeTab === 'gaps'">
          <SkillGapsMatrix :scenario-id="scenarioId" />
        </div>
        <div v-show="activeTab === 'strategies'">
          <ClosureStrategies :scenario-id="scenarioId" @refreshed="refreshStrategies" />
        </div>
        <div v-show="activeTab === 'matches'">
          <MatchingResults :scenario-id="scenarioId" />
        </div>
        <div v-show="activeTab === 'forecasts'">
          <RoleForecastsTable :scenario-id="scenarioId" />
        </div>
        <div v-show="activeTab === 'comparisons'">
          <ScenarioComparison :scenario-id="scenarioId" />
        </div>
        <div v-show="activeTab === 'succession'">
          <SuccessionPlanCard :scenario-id="scenarioId" />
        </div>
      </v-card-text>
    </v-card>
  </v-container>
</template>
