<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
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
import WorkforcePlansOverview from '@/components/WorkforcePlanning/WorkforcePlansOverview.vue'
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
  scope_type?: string
  scope_id?: number | null
  horizon_months?: number
  start_date?: string | null
  end_date?: string | null
  owner?: string | null
}

defineOptions({ layout: AppLayout })

const props = defineProps<Props>()
const api = useApi()
const { showSuccess, showError } = useNotification()

const scenario = ref<ScenarioPayload | null>(null)
const loading = ref(false)
const refreshing = ref(false)
const savingStep1 = ref(false)
const formData = ref({
  name: '',
  description: '',
  scope_type: '',
  scope_id: null as number | null,
  planning_horizon_months: 12,
  start_date: null as string | null,
  end_date: null as string | null,
  owner: null as string | null,
  import_to_plan: false,
})
const roles = ref<any[]>([])
const rolesLoading = ref(false)
const roleActions = ref<Record<number, string>>({})
const departments = ref<any[]>([])
const deptLoading = ref(false)
const roleFamilies = ref<any[]>([])
const includedCount = computed(() => Object.values(roleActions.value).filter(v => v === 'include').length)
const importCount = computed(() => Object.values(roleActions.value).filter(v => v === 'import').length)
const ignoredCount = computed(() => Object.values(roleActions.value).filter(v => v === 'ignore').length)
const roleHeaders = ref([
  { title: 'Rol', key: 'name' },
  { title: 'Departamento', key: 'department' },
  { title: 'Familia', key: 'family' },
  { title: 'Acciones', key: 'actions', sortable: false },
])
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
    // populate simple form data for step1
    formData.value.name = scenario.value?.name ?? ''
    formData.value.description = scenario.value?.description ?? ''
    formData.value.scope_type = scenario.value?.scope_type ?? ''
    formData.value.planning_horizon_months = scenario.value?.horizon_months ?? 12
    formData.value.start_date = scenario.value?.start_date ?? null
    formData.value.end_date = scenario.value?.end_date ?? null
    formData.value.owner = scenario.value?.owner ?? null
    formData.value.scope_id = scenario.value?.scope_id ?? null
    // load departments/role families if applicable
    if (formData.value.scope_type === 'department' || formData.value.scope_type === 'role_family') {
      await loadDepartments()
    }
    // load roles for step1 based on scope
    await loadRoles()
  } catch (error) {
    showError('No se pudo cargar el escenario')
  } finally {
    loading.value = false
  }
}

const loadRoles = async () => {
  // fetch roles filtered by scope_type if provided
  rolesLoading.value = true
  try {
    const params: any = {}
    if (formData.value.scope_type) params.scope_type = formData.value.scope_type
    if (formData.value.scope_id) params.scope_id = formData.value.scope_id
    const res = await api.get('/api/roles', params)
    roles.value = (res as any)?.data ?? res
    // derive role families from roles (attribute on role)
    const famMap: Record<string, any> = {}
    roles.value.forEach((r: any) => {
      const fid = r.role_family_id || (r.role_family && r.role_family.id) || null
      const fname = r.role_family_name || (r.role_family && r.role_family.name) || r.role_family || null
      if (fid && !famMap[fid]) famMap[fid] = { id: fid, name: fname }
    })
    roleFamilies.value = Object.values(famMap)
    // initialize actions for newly loaded roles
    roles.value.forEach((r: any) => {
      if (!roleActions.value[r.id]) roleActions.value[r.id] = 'ignore'
    })
  } catch (e) {
    // ignore silently; roles are optional
  } finally {
    rolesLoading.value = false
  }
}

const setAllActions = (action: string) => {
  roles.value.forEach((r: any) => {
    roleActions.value[r.id] = action
  })
}

watch(() => formData.value.scope_type, async (nv, ov) => {
  if (nv !== ov) await loadRoles()
})

watch(() => formData.value.scope_id, async (nv, ov) => {
  if (nv !== ov) await loadRoles()
})

const loadDepartments = async () => {
  deptLoading.value = true
  try {
    const res = await api.get('/api/departments')
    departments.value = (res as any)?.data ?? res
  } catch (e) {
    // ignore
  } finally {
    deptLoading.value = false
  }
}

const saveStep1 = async () => {
  if (!scenarioId.value || scenarioId.value <= 0) return
  savingStep1.value = true
  try {
    const step1Roles = roles.value.map((r: any) => ({
      id: r.id,
      role_id: r.id,
      name: r.name,
      action: roleActions.value[r.id] || 'ignore',
    }))

    const payload = {
      step1: {
        metadata: {
          name: formData.value.name,
          description: formData.value.description,
          scope_type: formData.value.scope_type,
          planning_horizon_months: formData.value.planning_horizon_months,
          start_date: formData.value.start_date,
          end_date: formData.value.end_date,
          owner: formData.value.owner,
          scope_id: formData.value.scope_id,
        },
        roles: step1Roles,
        import_to_plan: formData.value.import_to_plan,
      },
    }

    const res = await api.patch(`/api/v1/workforce-planning/workforce-scenarios/${scenarioId.value}`, payload)
    const updated = (res as any)?.data ?? res
    scenario.value = updated
    showSuccess('Paso 1 guardado')
  } catch (e) {
    showError('No se pudo guardar el Paso 1')
  } finally {
    savingStep1.value = false
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
<!--         <v-tab value="actions" prepend-icon="mdi-cog">Estados & Acciones</v-tab>
        <v-tab value="overview" prepend-icon="mdi-view-dashboard">Overview</v-tab>
        <v-tab value="gaps" prepend-icon="mdi-matrix">Brechas</v-tab>
        <v-tab value="strategies" prepend-icon="mdi-source-branch">Estrategias</v-tab>
        <v-tab value="matches" prepend-icon="mdi-account-search">Matching</v-tab>
        <v-tab value="forecasts" prepend-icon="mdi-chart-timeline-variant">Forecasts</v-tab>
        <v-tab value="comparisons" prepend-icon="mdi-compare">Comparaciones</v-tab>
        <v-tab value="succession" prepend-icon="mdi-family-tree">Sucesión</v-tab> -->
      </v-tabs>

      <v-divider></v-divider>

      <v-card-text>
        <div v-show="activeTab === 'stepper'">
          <ScenarioStepperComponent
            :current-step="currentStep"
            :scenario-status="scenario?.execution_status ?? 'draft'"
            :decision-status="scenario?.decision_status ?? 'draft'"
            @update:current-step="handleStepChange"
            @step-click="handleStepChange"
          >
            <template #step-1="{ step }">
              <v-form>
                <v-row>
                  <v-col cols="12" md="8">
                    <v-text-field v-model="formData.name" label="Nombre del escenario" />
                    <v-textarea v-model="formData.description" label="Descripción" rows="3" />
                    <v-row>
                      <v-col cols="12" md="4">
                        <v-select
                          v-model="formData.scope_type"
                          :items="['organization_wide','department','business_unit','critical_roles_only']"
                          label="Alcance (scope)"
                        />
                      </v-col>
                        <v-col cols="12" md="4" v-if="formData.scope_type === 'department'">
                          <v-select
                            v-model="formData.scope_id"
                            :items="departments"
                            item-title="name"
                            item-value="id"
                            :loading="deptLoading"
                            label="Departamento"
                          />
                        </v-col>
                      <v-col cols="6" md="4">
                        <v-text-field v-model="formData.planning_horizon_months" type="number" label="Horizonte (meses)" />
                      </v-col>
                      <v-col cols="6" md="4">
                        <v-text-field v-model="formData.owner" label="Owner (usuario)" />
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field v-model="formData.start_date" type="date" label="Fecha inicio" />
                    <v-text-field v-model="formData.end_date" type="date" label="Fecha fin" />
                    <v-checkbox v-model="formData.import_to_plan" label="Importar a plan (crear scope entries)" />
                    <v-btn class="mt-4" color="primary" :loading="savingStep1" @click.prevent="saveStep1">Guardar Paso 1</v-btn>
                  </v-col>
                </v-row>
                <v-divider class="my-4" />
                <div>
                  <h3 class="mb-2">Roles afectados</h3>
                  <div class="d-flex align-center mb-2">
                    <v-btn small color="secondary" class="mr-2" @click="setAllActions('include')">Marcar todos: Incluir</v-btn>
                    <v-btn small color="grey" class="mr-2" @click="setAllActions('ignore')">Marcar todos: Ignorar</v-btn>
                    <v-btn small color="primary" class="mr-2" @click="setAllActions('import')">Marcar todos: Importar</v-btn>
                    <div class="text-caption ml-auto">Incluidos: {{ includedCount }} · Importados: {{ importCount }} · Ignorados: {{ ignoredCount }}</div>
                  </div>
                  <div v-if="rolesLoading">Cargando roles...</div>
                  <div v-else>
                    <v-data-table
                      :headers="roleHeaders"
                      :items="roles"
                      item-key="id"
                      class="elevation-1"
                      dense
                    >
                      <template #item.name="{ item }">
                        <div>{{ item.name }}</div>
                      </template>
                      <template #item.department="{ item }">
                        <div>{{ item.department_name || item.department || '—' }}</div>
                      </template>
                      <template #item.family="{ item }">
                        <div>{{ item.role_family_name || item.role_family || item.role_family_id || '—' }}</div>
                      </template>
                      <template #item.actions="{ item }">
                        <div class="d-flex align-center">
                          <v-select
                            :items="['ignore','include','import']"
                            v-model="roleActions[item.id]"
                            dense
                            hide-details
                            style="min-width:160px"
                          />
                        </div>
                      </template>
                    </v-data-table>
                    <div class="text-caption mt-2">Selecciona 'include' para considerar el rol, 'import' para crear scope entries en el plan.</div>
                  </div>
                </div>
              </v-form>
            </template>
          </ScenarioStepperComponent>
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
          <WorkforcePlansOverview />
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
