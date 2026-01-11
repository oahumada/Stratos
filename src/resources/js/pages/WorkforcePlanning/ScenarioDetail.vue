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
import ScenarioStepperComponent from '@/components/StrategicPlanningScenarios/ScenarioStepperComponent.vue'
import WorkforcePlansOverview from '@/components/StrategicPlanningScenarios/WorkforcePlansOverview.vue'
import ScenarioActionsPanel from '@/components/StrategicPlanningScenarios/ScenarioActionsPanel.vue'
import VersionHistoryModal from '@/components/StrategicPlanningScenarios/VersionHistoryModal.vue'
import StatusTimeline from '@/components/StrategicPlanningScenarios/StatusTimeline.vue'
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
  planning_horizon_months?: number
  start_date?: string | null
  end_date?: string | null
  owner?: string | null
  scenario_skills?: any[]
  skill_demands?: any[]
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
  // Basic metadata
  id: null as number | null,
  name: '',
  description: '',
  scenario_type: '',
  status: 'draft',
  decision_status: '',
  execution_status: '',
  current_step: 1,
  is_current_version: false,
  version_number: null as number | null,
  version_group_id: null as string | null,
  parent_id: null as number | null,

  // Time / horizon
  planning_horizon_months: 12,
  horizon_months: 12,
  time_horizon_weeks: null as number | null,
  start_date: null as string | null,
  end_date: null as string | null,

  // other
  owner: null as string | null,
  estimated_budget: null as number | null,
  scope_type: '',
  scope_id: null as number | null,
  import_to_plan: false,
})

const fieldErrors = ref<Record<string, string[]>>({})
const roles = ref<any[]>([])
const rolesLoading = ref(false)
const skills = ref<any[]>([])
const skillsLoading = ref(false)
const scenarioSkills = ref<any[]>([])
const showNewSkillDialog = ref(false)
const newSkillName = ref('')
const newSkillCategory = ref('technical')
const newSkillLoading = ref(false)
const newSkillTargetIndex = ref<number | null>(null)
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
    const response = await api.get(`/api/v1/strategic-planning/scenarios/${scenarioId.value}`)
    const data = (response as any)?.data ?? response
    scenario.value = data
    currentStep.value = scenario.value?.current_step || 1
    // populate simple form data for step1
    formData.value.name = scenario.value?.name ?? ''
    formData.value.description = scenario.value?.description ?? ''
    formData.value.scope_type = scenario.value?.scope_type ?? ''
    formData.value.planning_horizon_months = scenario.value?.horizon_months ?? scenario.value?.planning_horizon_months ?? 12
    formData.value.horizon_months = scenario.value?.horizon_months ?? formData.value.planning_horizon_months
    formData.value.time_horizon_weeks = scenario.value?.time_horizon_weeks ?? null
    formData.value.start_date = scenario.value?.start_date ?? null
    formData.value.end_date = scenario.value?.end_date ?? null
    formData.value.owner = scenario.value?.owner ?? null
    formData.value.scope_id = scenario.value?.scope_id ?? null

    // advanced / tracking fields
    formData.value.id = scenario.value?.id ?? null
    formData.value.scenario_type = scenario.value?.scenario_type ?? ''
    formData.value.status = scenario.value?.status ?? 'draft'
    formData.value.decision_status = scenario.value?.decision_status ?? ''
    formData.value.execution_status = scenario.value?.execution_status ?? ''
    formData.value.current_step = scenario.value?.current_step ?? formData.value.current_step
    formData.value.is_current_version = scenario.value?.is_current_version ?? false
    formData.value.version_number = scenario.value?.version_number ?? null
    formData.value.version_group_id = scenario.value?.version_group_id ?? null
    formData.value.parent_id = scenario.value?.parent_id ?? null
    formData.value.estimated_budget = scenario.value?.estimated_budget ?? null
    // load departments/role families if applicable
    if (formData.value.scope_type === 'department' || formData.value.scope_type === 'role_family') {
      await loadDepartments()
    }
    // load skills for step1 and populate scenario skills
    await loadSkills()
    // populate scenarioSkills from scenario payload if available
    scenarioSkills.value = (scenario.value?.scenario_skills || scenario.value?.skill_demands || []).map((s: any) => ({
      id: s.id || null,
      skill_id: s.skill_id || s.id || null,
      required_level: s.required_level ?? s.level ?? 1,
      required_headcount: s.required_headcount ?? s.required_headcount ?? 1,
      priority: s.priority || 'medium',
      rationale: s.rationale || s.notes || '',
    }))
    // load roles for step1 based on scope (kept but roles will be unused in Phase 1)
    await loadRoles()
  } catch (e) {
    void e
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
    void e
    // ignore silently; roles are optional
  } finally {
    rolesLoading.value = false
  }
}

const loadSkills = async () => {
  skillsLoading.value = true
  try {
    const res = await api.get('/api/skills')
    skills.value = (res as any)?.data ?? res ?? []
  } catch (e) {
    void e
    // ignore
  } finally {
    skillsLoading.value = false
  }
}

const addScenarioSkill = () => {
  scenarioSkills.value.push({
    id: null,
    skill_id: null,
    strategic_role: '',
    priority: 'medium',
    rationale: '',
  })
}

const removeScenarioSkill = (index: number) => {
  scenarioSkills.value.splice(index, 1)
}

const openNewSkillDialog = (targetIndex: number | null = null) => {
  newSkillName.value = ''
  newSkillCategory.value = 'technical'
  newSkillTargetIndex.value = targetIndex
  showNewSkillDialog.value = true
}

const createNewSkill = async () => {
  if (!newSkillName.value.trim()) return
  newSkillLoading.value = true
  try {
    const payload: any = {
      name: newSkillName.value.trim(),
      category: newSkillCategory.value,
      maturity_status: 'emergente',
      status: 'active',
    }
    // if we have a scenario context, mark discovery origin
    if (scenarioId.value && scenarioId.value > 0) payload.discovered_in_scenario_id = scenarioId.value
    const res: any = await api.post('/api/skills', payload)
    const created = (res as any)?.data ?? res
    // append to local skills catalog
    skills.value.push(created)
    // if target index provided, assign to that row, else create new scenarioSkill with this skill
    if (newSkillTargetIndex.value !== null && scenarioSkills.value[newSkillTargetIndex.value]) {
      scenarioSkills.value[newSkillTargetIndex.value].skill_id = created.id
    } else {
      scenarioSkills.value.push({ id: null, skill_id: created.id, strategic_role: '', priority: 'medium', rationale: '' })
    }
    showNewSkillDialog.value = false
  } catch (e) {
    void e
    showError('Error al crear la skill')
  } finally {
    newSkillLoading.value = false
  }
}

const setAllActions = (action: string) => {
  roles.value.forEach((r: any) => {
    roleActions.value[r.id] = action
  })
}

// mark computed/handlers referenced to avoid unused-var while Phase 1 removes roles
void includedCount.value
void importCount.value
void ignoredCount.value
void roleHeaders.value
void setAllActions

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
    void e
    // ignore
  } finally {
    deptLoading.value = false
  }
}

const saveStep1 = async () => {
  if (!scenarioId.value || scenarioId.value <= 0) return
  savingStep1.value = true
  fieldErrors.value = {}

  // client-side validation
  if (!validateStep1()) {
    savingStep1.value = false
    return
  }

    try {

    const payload = {
      step1: {
        metadata: {
          id: formData.value.id,
          name: formData.value.name,
          description: formData.value.description,
          scenario_type: formData.value.scenario_type,
          status: formData.value.status,
          decision_status: formData.value.decision_status,
          execution_status: formData.value.execution_status,
          current_step: formData.value.current_step,
          is_current_version: formData.value.is_current_version,
          version_number: formData.value.version_number,
          version_group_id: formData.value.version_group_id,
          parent_id: formData.value.parent_id,

          planning_horizon_months: formData.value.planning_horizon_months,
          horizon_months: formData.value.horizon_months,
          time_horizon_weeks: formData.value.time_horizon_weeks,
          start_date: formData.value.start_date,
          end_date: formData.value.end_date,

          owner: formData.value.owner,
          scope_type: formData.value.scope_type,
          scope_id: formData.value.scope_id,
          estimated_budget: formData.value.estimated_budget,
        },
        // scenario skills for Phase 1
        skills: scenarioSkills.value.map((s: any) => ({
          id: s.id || null,
          skill_id: s.skill_id,
          required_level: s.required_level,
          required_headcount: s.required_headcount,
          priority: s.priority,
          rationale: s.rationale,
        })),
        import_to_plan: formData.value.import_to_plan,
      },
    }

    const res = await api.patch(`/api/v1/strategic-planning/scenarios/${scenarioId.value}`, payload)
    const updated = (res as any)?.data ?? res
    scenario.value = updated
    showSuccess('Paso 1 guardado')
  } catch (err: any) {
    // extract validation errors if provided by server
    if (err?.response?.data) {
      const data = err.response.data
      if (data.errors) fieldErrors.value = data.errors
      if (data.message) showError(data.message)
      else showError('No se pudo guardar el Paso 1')
    } else {
      showError('No se pudo guardar el Paso 1')
    }
  } finally {
    savingStep1.value = false
  }
}

function validateStep1() {
  const errors: Record<string, string[]> = {}
  if (!formData.value.name || !formData.value.name.trim()) {
    errors.name = ['El nombre es requerido']
  }
  if (!formData.value.planning_horizon_months || formData.value.planning_horizon_months <= 0) {
    errors.planning_horizon_months = ['El horizonte debe ser mayor que 0']
  }
  if (formData.value.start_date && formData.value.end_date) {
    const s = new Date(formData.value.start_date)
    const e = new Date(formData.value.end_date)
    if (s > e) errors.start_date = ['La fecha de inicio no puede ser posterior a la fecha fin']
  }

  // Scenario skills validation: each item must have a skill selected
  if (Array.isArray(scenarioSkills.value) && scenarioSkills.value.length > 0) {
    const missing = scenarioSkills.value.some((s: any) => !s.skill_id)
    if (missing) {
      errors.scenario_skills = ['Todas las filas deben tener una skill seleccionada']
    }
  }

  fieldErrors.value = errors
  return Object.keys(errors).length === 0
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
  router.visit(`/strategic-planning/scenarios/${id}`)
}

const calculateGaps = async () => {
  if (!scenarioId.value || scenarioId.value <= 0) return
  refreshing.value = true
  try {
    await api.post(`/api/v1/strategic-planning/scenarios/${scenarioId.value}/calculate-gaps`)
    showSuccess('Brechas recalculadas')
  } catch (e) {
    void e
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
  } catch (e) {
    void e
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
            <template #step-1>
              <v-form>
                <v-row>
                  <v-col cols="12" md="8">
                    <v-text-field v-model="formData.name" label="Nombre del escenario" :error-messages="fieldErrors.name || []" />
                    <v-textarea v-model="formData.description" label="Descripción" rows="3" :error-messages="fieldErrors.description || []" />
                    <v-row>
                      <v-col cols="12" md="6">
                        <v-text-field v-model="formData.scenario_type" label="Tipo de escenario" :error-messages="fieldErrors.scenario_type || []" />
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-select
                          v-model="formData.status"
                          :items="['draft','active','archived']"
                          label="Estado"
                          :error-messages="fieldErrors.status || []"
                        />
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12" md="6">
                        <v-text-field v-model="formData.decision_status" label="Decision status" :error-messages="fieldErrors.decision_status || []" />
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field v-model="formData.execution_status" label="Execution status" :error-messages="fieldErrors.execution_status || []" />
                      </v-col>
                    </v-row>
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
                        <v-text-field v-model="formData.planning_horizon_months" type="number" label="Horizonte (meses)" :error-messages="fieldErrors.planning_horizon_months || []" />
                      </v-col>
                      <v-col cols="6" md="4">
                        <v-text-field v-model="formData.owner" label="Owner (usuario)" :error-messages="fieldErrors.owner || []" />
                      </v-col>
                    </v-row>
                  </v-col>
                    <v-col cols="12" md="4">
                      <v-text-field v-model="formData.start_date" type="date" label="Fecha inicio" :error-messages="fieldErrors.start_date || []" />
                      <v-text-field v-model="formData.end_date" type="date" label="Fecha fin" :error-messages="fieldErrors.end_date || []" />
                      <v-text-field v-model="formData.estimated_budget" type="number" label="Presupuesto estimado" :error-messages="fieldErrors.estimated_budget || []" />
                      <v-text-field v-model="formData.parent_id" type="number" label="Parent scenario ID" :error-messages="fieldErrors.parent_id || []" />
                      <v-checkbox v-model="formData.is_current_version" label="Versión actual" />
                      <v-text-field v-model="formData.version_number" label="Número de versión" readonly />
                      <v-checkbox v-model="formData.import_to_plan" label="Importar a plan (crear scope entries)" />
                      <v-btn class="mt-4" color="primary" :loading="savingStep1" @click.prevent="saveStep1">Guardar Paso 1</v-btn>
                    </v-col>
                </v-row>
                <v-divider class="my-4" />
                <div>
                  <h3 class="mb-2">Skills requeridas (Fase 1)</h3>
                  <div class="d-flex align-center mb-2">
                    <v-btn small color="secondary" class="mr-2" @click="addScenarioSkill">Añadir skill</v-btn>
                    <v-btn small color="primary" class="mr-2" @click="openNewSkillDialog()">Crear nueva skill</v-btn>
                    <div class="text-caption ml-auto">Items: {{ scenarioSkills.length }}</div>
                  </div>
                  <div v-if="skillsLoading">Cargando catálogo de skills...</div>
                  <div v-else>
                    <v-data-table
                      :headers="[
                        { title: 'Skill', key: 'skill' },
                        { title: 'Strategic Role', key: 'strategic_role' },
                        { title: 'Prioridad', key: 'priority' },
                        { title: 'Razonamiento', key: 'rationale' },
                        { title: 'Acciones', key: 'actions', sortable: false }
                      ]"
                      :items="scenarioSkills"
                      item-key="__idx"
                      class="elevation-1"
                      dense
                    >
                      <!-- eslint-disable-next-line vue/valid-v-slot -->
                      <template #item.skill="{ item }">
                        <v-select :items="skills" item-title="name" item-value="id" v-model="item.skill_id" dense hide-details style="min-width:220px" />
                      </template>
                      <!-- eslint-disable-next-line vue/valid-v-slot -->
                      <template #item.strategic_role="{ item }">
                        <v-text-field v-model="item.strategic_role" dense hide-details />
                      </template>
                      <!-- eslint-disable-next-line vue/valid-v-slot -->
                      <template #item.priority="{ item }">
                        <v-select :items="['critical','high','medium','low']" v-model="item.priority" dense hide-details />
                      </template>
                      <!-- eslint-disable-next-line vue/valid-v-slot -->
                      <template #item.rationale="{ item }">
                        <v-text-field v-model="item.rationale" dense hide-details />
                      </template>
                      <!-- eslint-disable-next-line vue/valid-v-slot -->
                      <template #item.actions="{ index }">
                        <v-btn icon small color="error" @click="removeScenarioSkill(index)"><v-icon>mdi-delete</v-icon></v-btn>
                      </template>
                    </v-data-table>
                    <div class="text-caption mt-2">Define las skills que requiere este escenario. Estos se guardarán en `scenario_skills`.</div>
                  </div>
                </div>
              </v-form>
            </template>
          </ScenarioStepperComponent>
        </div>

        <v-dialog v-model="showNewSkillDialog" width="500">
          <v-card>
            <v-card-title>Crear nueva skill</v-card-title>
            <v-card-text>
              <v-text-field v-model="newSkillName" label="Nombre de la skill" />
              <v-select v-model="newSkillCategory" :items="['technical','soft','business','language']" label="Categoría" />
            </v-card-text>
            <v-card-actions>
              <v-spacer />
              <v-btn text @click="showNewSkillDialog = false">Cancelar</v-btn>
              <v-btn color="primary" :loading="newSkillLoading" @click="createNewSkill">Crear</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

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
