<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { ref, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import RoleForecastsTable from './RoleForecastsTable.vue';
import MatchingResults from './MatchingResults.vue';
import SkillGapsMatrix from './SkillGapsMatrix.vue';
import SuccessionPlanCard from './SuccessionPlanCard.vue';
import HeadcountChart from './Charts/HeadcountChart.vue';
import CoverageChart from './Charts/CoverageChart.vue';
import SkillGapsChart from './Charts/SkillGapsChart.vue';
import SuccessionRiskChart from './Charts/SuccessionRiskChart.vue';
import ReadinessTimelineChart from './Charts/ReadinessTimelineChart.vue';
import MatchScoreDistributionChart from './Charts/MatchScoreDistributionChart.vue';
import DepartmentGapsChart from './Charts/DepartmentGapsChart.vue';

defineOptions({ layout: AppLayout });

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

const scenarioId = computed(() => {
  const id = props.id
  return typeof id === 'string' ? parseInt(id, 10) : (id || 0)
})
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

const formatNumber = (num: number): string => {
  return new Intl.NumberFormat('en-US').format(num)
}

// Helper functions for chart data aggregation
const countGapsByPriority = (priority: string): number => {
  // This will be populated by data from the store in the next phase
  // For now return mock data that will be replaced by store getters
  const gapPriorities: { [key: string]: number } = {
    critical: 3,
    high: 4,
    medium: 5,
    low: 2
  }
  return gapPriorities[priority] || 0
}

const countByReadiness = (level: string): number => {
  // Mock data - will be populated from store
  const readinessCounts: { [key: string]: number } = {
    immediately: 3,
    within_six: 4,
    within_twelve: 2,
    beyond_twelve: 1
  }
  return readinessCounts[level] || 0
}

const getAllMatchScores = (): number[] => {
  // Mock data - will be populated from store
  return [95, 87, 92, 78, 84, 91, 56, 71, 88, 82]
}

const getDepartments = (): string[] => {
  return ['Engineering', 'Sales', 'Marketing', 'HR', 'Finance']
}

const getGapCountsByDepartment = (): number[] => {
  return [3, 2, 4, 1, 2]
}

const loadScenario = async () => {
  try {
    const response = await api.get(`/api/v1/workforce-planning/workforce-scenarios/${scenarioId.value}`)
    const scenario = (response as any).data
    scenarioName.value = scenario.name
    scenarioDescription.value = scenario.description
    // Analytics legacy deshabilitado temporalmente
  } catch (error) {
    showError('Failed to load scenario')
  }
}

// Analytics legacy deshabilitado temporalmente hasta migrar al nuevo módulo
const loadAnalytics = async () => {
  return
}

const runAnalysis = async () => {
  if (!scenarioId.value) return
  try {
    analyzing.value = true
    const res = await api.post(`/api/v1/workforce-planning/workforce-scenarios/${scenarioId.value}/calculate-gaps`, {})
    const result: any = (res as any).data || res
    if (result && result.summary) {
      // Mapear algunos KPIs a tarjetas existentes cuando sea posible
      analytics.value.total_skills_required = result.summary.total_required_skills || analytics.value.total_skills_required
      analytics.value.skills_with_gaps = result.summary.critical_skills_count ?? analytics.value.skills_with_gaps
      analytics.value.internal_coverage_percentage = Math.round((result.summary.coverage_pct ?? 0))
    }
    showSuccess('Brechas calculadas correctamente')
  } catch (e:any) {
    console.error('calculate-gaps error', e)
    const msg = e?.response?.data?.message || e?.message || 'Error al calcular brechas'
    showError(msg)
  } finally {
    analyzing.value = false
  }
}

const generateStrategies = async () => {
  if (!scenarioId.value) return
  try {
    const res = await api.post(`/api/v1/workforce-planning/workforce-scenarios/${scenarioId.value}/refresh-suggested-strategies`, {})
    const created = (res as any)?.created ?? 0
    showSuccess(`Estrategias sugeridas actualizadas (${created} nuevas)`) 
  } catch (e:any) {
    console.error('refresh-suggested-strategies error', e)
    const msg = e?.response?.data?.message || e?.message || 'Error al generar estrategias'
    showError(msg)
  }
}

const downloadReport = () => {
  // TODO: Implement report download
  showSuccess('Descarga de reporte aún no implementada')
}

onMounted(() => {
  loadScenario()
})
</script>

<template>
  <div class="overview-dashboard">
    <v-container fluid>
      <v-row class="mb-4">
        <v-col cols="12" md="8">
          <h2>Escenario: {{ scenarioName }}</h2>
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
            Ejecutar Análisis
          </v-btn>
          <v-btn
            color="success"
            @click="generateStrategies"
            prepend-icon="mdi-lightbulb-on"
            class="mr-2"
          >
            Generar Estrategias
          </v-btn>
          <v-btn
            color="secondary"
            @click="downloadReport"
            prepend-icon="mdi-download"
          >
            Exportar
          </v-btn>
        </v-col>
      </v-row>

      <!-- Navigation Tabs -->
      <v-row class="mb-4">
        <v-col cols="12">
          <v-tabs v-model="activeTab" bg-color="primary">
            <v-tab value="overview">Resumen</v-tab>
            <v-tab value="forecasts">Proyecciones de Roles</v-tab>
            <v-tab value="matches">Coincidencias de Talento</v-tab>
            <v-tab value="gaps">Brechas de Habilidades</v-tab>
            <v-tab value="succession">Planes de Sucesión</v-tab>
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
              <div class="text-overline">Dotación Total</div>
              <div class="text-h4">{{ analytics.total_headcount_current || '0' }}</div>
              <div class="text-caption">Actual → {{ analytics.total_headcount_projected || '0' }}</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card>
            <v-card-text>
              <div class="text-overline">Crecimiento Neto</div>
              <div class="text-h4">{{ analytics.net_growth || '0' }}</div>
              <div class="text-caption">{{ analytics.net_growth > 0 ? 'Expansión' : 'Reducción' }}</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card>
            <v-card-text>
              <div class="text-overline">Cobertura Interna</div>
              <div class="text-h4">{{ analytics.internal_coverage_percentage || '0' }}%</div>
              <div class="text-caption">Brecha Externa: {{ analytics.external_gap_percentage || '0' }}%</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card>
            <v-card-text>
              <div class="text-overline">Riesgo de Sucesión</div>
              <div class="text-h4">{{ analytics.succession_risk_percentage || '0' }}%</div>
              <div class="text-caption">Roles críticos en riesgo</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Primary Charts Row -->
      <v-row class="mb-4">
        <v-col cols="12" md="6">
          <v-card>
            <v-card-title>Proyección de Dotación</v-card-title>
            <v-card-text>
              <HeadcountChart
                :currentHeadcount="analytics.total_headcount_current"
                :projectedHeadcount="analytics.total_headcount_projected"
              />
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="6">
          <v-card>
            <v-card-title>Cobertura Interna</v-card-title>
            <v-card-text>
              <CoverageChart
                :internalCoverage="analytics.internal_coverage_percentage"
                :externalGap="analytics.external_gap_percentage"
              />
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Secondary Charts Row -->
      <v-row class="mb-4">
        <v-col cols="12" md="6">
          <v-card>
            <v-card-title>Brechas de Habilidades por Prioridad</v-card-title>
            <v-card-text>
              <SkillGapsChart
                :criticalGaps="countGapsByPriority('critical')"
                :highGaps="countGapsByPriority('high')"
                :mediumGaps="countGapsByPriority('medium')"
                :lowGaps="countGapsByPriority('low')"
              />
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="6">
          <v-card>
            <v-card-title>Evaluación de Riesgo de Sucesión</v-card-title>
            <v-card-text>
              <SuccessionRiskChart
                :riskPercentage="analytics.succession_risk_percentage"
              />
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Tertiary Charts Row -->
      <v-row class="mb-4">
        <v-col cols="12" md="4">
          <v-card>
            <v-card-title>Cronograma de Preparación</v-card-title>
            <v-card-text>
              <ReadinessTimelineChart
                :immediatelyReady="countByReadiness('immediately')"
                :readyWithinSix="countByReadiness('within_six')"
                :readyWithinTwelve="countByReadiness('within_twelve')"
                :beyondTwelve="countByReadiness('beyond_twelve')"
              />
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="4">
          <v-card>
            <v-card-title>Distribución de Puntuación</v-card-title>
            <v-card-text>
              <MatchScoreDistributionChart
                :scores="getAllMatchScores()"
              />
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="4">
          <v-card>
            <v-card-title>Brechas por Departamento</v-card-title>
            <v-card-text>
              <DepartmentGapsChart
                :departments="getDepartments()"
                :gapCounts="getGapCountsByDepartment()"
              />
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Risk Summary -->
      <v-row>
        <v-col cols="12" md="6">
          <v-card>
            <v-card-title>Resumen de Riesgos</v-card-title>
            <v-list>
              <v-list-item>
                <v-list-item-title>Posiciones de Alto Riesgo</v-list-item-title>
                <v-list-item-subtitle>{{ analytics.high_risk_positions }}</v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>Posiciones de Riesgo Medio</v-list-item-title>
                <v-list-item-subtitle>{{ analytics.medium_risk_positions }}</v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>Habilidades Críticas en Riesgo</v-list-item-title>
                <v-list-item-subtitle>{{ analytics.critical_skills_at_risk }}</v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card>
        </v-col>

        <v-col cols="12" md="6">
          <v-card>
            <v-card-title>Estimaciones de Costo</v-card-title>
            <v-list>
              <v-list-item>
                <v-list-item-title>Costo de Reclutamiento</v-list-item-title>
                <v-list-item-subtitle>${{ formatNumber(analytics.estimated_recruitment_cost) }}</v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>Costo de Capacitación</v-list-item-title>
                <v-list-item-subtitle>${{ formatNumber(analytics.estimated_training_cost) }}</v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-title>Cronograma de Contratación Externa</v-list-item-title>
                <v-list-item-subtitle>{{ analytics.estimated_external_hiring_months }} meses</v-list-item-subtitle>
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
            Ejecutar Análisis Completo
          </v-btn>
          <v-btn
            color="secondary"
            @click="downloadReport"
            prepend-icon="mdi-download"
            class="ml-2"
          >
            Descargar Reporte
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


<style scoped>
.overview-dashboard {
  padding: 20px;
}
</style>
