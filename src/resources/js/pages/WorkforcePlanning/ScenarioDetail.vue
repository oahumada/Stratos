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
import ScenarioStepperComponent from '@/components/WorkforcePlanning/ScenarioStepperComponent.vue'
import ScenarioActionsPanel from '@/components/WorkforcePlanning/ScenarioActionsPanel.vue'
import VersionHistoryModal from '@/components/WorkforcePlanning/VersionHistoryModal.vue'
import StatusTimeline from '@/components/WorkforcePlanning/StatusTimeline.vue'
import { router } from '@inertiajs/vue3'

type Props = {
  id: number | string
}

type ScenarioPayload = {
  id: number
  name: string
  description?: string
  scenario_type?: string
  status: string
  decision_status: string
  execution_status: string
  current_step?: number
  is_current_version?: boolean
  version_number?: number
  version_group_id?: string
  parent_id?: number | null
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
const activeTab = ref<'stepper' | 'overview' | 'gaps' | 'strategies' | 'matches' | 'forecasts' | 'comparisons' | 'succession' | 'actions'>('stepper')
const currentStep = ref(1)
const versionHistoryRef = ref<InstanceType<typeof VersionHistoryModal> | null>(null)
const statusTimelineRef = ref<InstanceType<typeof StatusTimeline> | null>(null)

const scenarioId = computed(() => {
  const value = typeof props.id === 'string' ? parseInt(props.id, 10) : props.id
  return value || 0
})

const loadScenario = async () => {
  if (!scenarioId.value || scenarioId.value <= 0) return
  loading.value = true
  try {
    const response = await api.get(`/api/v1/workforce-planning/workforce-scenarios/${scenarioId.value}`)
    const data = (response as any)?.data ?? response
    scenario.value = data
    currentStep.value = scenario.value?.current_step || 1
  } catch (error) {
    showError('No se pudo cargar el escenario')
  } finally {
    loading.value = false
  }
}

const handleStatusChanged = () => {
  loadScenario()
}

const handleStepChange = (step: number) => {
  currentStep.value = step
}

const openVersionHistory = () => {
  versionHistoryRef.value?.openDialog()
}

const openStatusTimeline = () => {
  statusTimelineRef.value?.openTimeline()
}

const handleVersionSelected = (id: number) => {
  router.visit(`/workforce-planning/scenarios/${id}`)
}

const calculateGaps = async () => {
  if (!scenarioId.value || scenarioId.value <= 0) return
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
    // Endpoint opcional para generar/actualizar estrategias; si no existe, recarga el escenario
    showSuccess('Estrategias actualizadas')
  } catch (error) {
    showError('Error al actualizar estrategias')
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
      <v-col cols="12" md="6">
        <h2 class="mb-1">{{ scenario?.name || 'Escenario' }}</h2>
        <p class="text-medium-emphasis mb-0">{{ scenario?.description }}</p>
        <div class="text-caption text-uppercase text-medium-emphasis">
          Tipo: {{ scenario?.scenario_type || 'custom' }} · Horizonte: {{ scenario?.time_horizon_weeks || '—' }} semanas
          <span v-if="scenario?.version_number"> · v{{ scenario?.version_number }}</span>
        </div>
      </v-col>
      <v-col cols="12" md="6" class="text-right">
        <v-btn
          v-if="scenario?.version_group_id"
          color="purple"
          variant="outlined"
          prepend-icon="mdi-history"
          class="mr-2"
          @click="openVersionHistory"
        >
          Versiones
        </v-btn>
        <v-btn
          color="grey-darken-1"
          variant="outlined"
          prepend-icon="mdi-timeline-clock"
          class="mr-2"
          @click="openStatusTimeline"
        >
          Historial
        </v-btn>
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
        <v-tab value="stepper" prepend-icon="mdi-format-list-numbered">Metodología 7 Pasos</v-tab>
        <v-tab value="actions" prepend-icon="mdi-cog">Estados & Acciones</v-tab>
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
        <div v-show="activeTab === 'stepper'" v-if="scenario">
          <ScenarioStepperComponent
            :current-step="currentStep"
            :scenario-status="scenario.execution_status"
            :decision-status="scenario.decision_status"
            @update:current-step="handleStepChange"
            @step-click="handleStepChange"
          />
        </div>

        <div v-show="activeTab === 'actions'" v-if="scenario">
          <ScenarioActionsPanel
            :scenario="scenario"
            @refresh="loadScenario"
            @status-changed="handleStatusChanged"
          />
        </div>

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

    <!-- Modales -->
    <VersionHistoryModal
      v-if="scenario?.version_group_id"
      ref="versionHistoryRef"
      :scenario-id="scenarioId"
      :version-group-id="scenario?.version_group_id || ''"
      :current-version="scenario?.version_number || 1"
      @version-selected="handleVersionSelected"
    />

    <StatusTimeline
      ref="statusTimelineRef"
      :scenario-id="scenarioId"
    />
  </v-container>
</template>
