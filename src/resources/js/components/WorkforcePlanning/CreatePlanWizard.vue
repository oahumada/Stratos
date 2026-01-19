<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { useWorkforcePlanning } from '@/composables/useStrategicPlanningScenarios'
import { useNotification } from '@/composables/useNotification'
import { useApi } from '@/composables/useApi'

const props = defineProps<{ scenarioId?: number }>()

const api = useApi()

const { createPlan } = useWorkforcePlanning()
const { showSuccess, showError } = useNotification()

const loading = ref(false)
const form = ref({
  name: '',
  description: '',
  start_date: '',
  end_date: '',
  planning_horizon_months: 12,
  scope_type: 'organization_wide',
  owner_user_id: null as number | null,
  sponsor_user_id: null as number | null,
})

const planData = ref<any>(null)
const skillDemands = ref<any[]>([])
const scenarioData = ref<any>(null)
const showRolesDialog = ref(false)
const showSkillsDialog = ref(false)

const rolesCount = () => {
  const roles = planData.value?.scope_roles || []
  return Array.isArray(roles) ? roles.length : 0
}

const skillsCount = () => {
  const skills = planData.value?.skill_demands || skillDemands.value || []
  return Array.isArray(skills) ? skills.length : 0
}
const editedSkills = ref<any[]>([])
const editedRoles = ref<any[]>([])

const initEdited = () => {
  const skills = planData.value?.skill_demands || skillDemands.value || []
  editedSkills.value = skills.map((s: any) => ({
    id: s.id,
    skill_id: s.skill_id,
    include: true,
    action: 'include', // include | ignore | create_role | map_role
    role_id: s.role_id || null,
    required_headcount: s.required_headcount || 0,
    required_level: s.required_level || null,
    priority: s.priority || 'medium',
    rationale: s.rationale || '',
  }))

  const roles = planData.value?.scope_roles || []
  editedRoles.value = roles.map((r: any) => ({
    id: r.id,
    role_id: r.role_id,
    inclusion_reason: r.inclusion_reason,
    notes: r.notes || '',
    include: true,
  }))
}

const users = ref<any[]>([])
const loadUsers = async () => {
  try {
    const res: any = await api.get('/api/users')
    users.value = res?.data ?? res ?? []
  } catch (e) {
    void e
  }
}

const mapScope = (s: string) => {
  // map template scope values -> plan scope values
  switch (s) {
    case 'organization':
      return 'organization_wide'
    case 'department':
      return 'department'
    case 'role_family':
      return 'critical_roles_only'
    default:
      return 'organization_wide'
  }
}

onMounted(async () => {
  // First, try localStorage prefill (created-from-template fallback)
  try {
    const raw = localStorage.getItem('wfp_prefill')
    if (raw) {
      const data = JSON.parse(raw)
      if (data.name) form.value.name = data.name
      if (data.description) form.value.owner_user_id = null
      if (data.time_horizon_weeks) form.value.planning_horizon_months = Math.round((data.time_horizon_weeks || 0) / 4)
      if (data.scope_type) form.value.scope_type = mapScope(data.scope_type)

      // remove prefill after reading
      localStorage.removeItem('wfp_prefill')
    }
    } catch (e) {
      void e
    }

  // If we have a scenarioId, prefer fetching the scenario (and its workforce_plan)
  if (props.scenarioId) {
    try {
      const resp: any = await api.get(`/api/strategic-planning/scenarios/${props.scenarioId}`)
      const data = resp?.data ?? resp
      scenarioData.value = data
      const plan = data?.workforce_plan ?? null
      if (plan) {
        planData.value = plan
        initEdited()
        if (plan.name) form.value.name = plan.name
        if (plan.planning_horizon_months) form.value.planning_horizon_months = plan.planning_horizon_months
        if (plan.start_date) form.value.start_date = plan.start_date
        if (plan.end_date) form.value.end_date = plan.end_date
        if (plan.scope_type) {
          form.value.scope_type = plan.scope_type
        }
      }
      // also capture scenario-level skill_demands (from template)
      if (data?.skill_demands && Array.isArray(data.skill_demands)) {
        skillDemands.value = data.skill_demands
      }
      await loadUsers()
    } catch (e) {
      void e
    }
  }
})

const submit = async () => {
  loading.value = true
  try {
    const payload = { ...form.value }
    const plan = await createPlan(payload as any)
    showSuccess('Plan creado correctamente')
    // update local plan data so wizard shows created entries
    if (plan) {
      planData.value = plan
    }
    return plan
  } catch (e: any) {
    void e
    showError('Error al crear el plan')
    throw e
  } finally {
    loading.value = false
  }
}

const openPlan = () => {
  if (planData.value && planData.value.id) {
    router.visit(`/strategic-planning/workforce-plans/${planData.value.id}`)
  }
}

const importRolesFromSkills = async () => {
  if (!planData.value || !planData.value.id) {
    showError('Crea primero el Plan antes de importar roles')
    return
  }
  const planId = planData.value.id
  const wp = useWorkforcePlanning()
  const items = (planData.value?.skill_demands || skillDemands.value || []).filter((s: any) => s.role_id)
  if (!items.length) {
    showError('No hay roles detectados en las skills importadas')
    return
  }
  try {
    const promises = items.map((s: any) => wp.addScopeRole(planId, { role_id: s.role_id, inclusion_reason: 'critical', notes: s.rationale || '' }))
    await Promise.all(promises)
    showSuccess(`Importados ${items.length} roles al alcance del plan`)
  } catch (e) {
    void e
    showError('Error al importar roles')
  }
}

const importUnitsFromSkills = async () => {
  if (!planData.value || !planData.value.id) {
    showError('Crea primero el Plan antes de importar unidades')
    return
  }
  const planId = planData.value.id
  const wp = useWorkforcePlanning()
  const items = (planData.value?.skill_demands || skillDemands.value || []).filter((s: any) => s.department || s.unit_name || s.unit_id)
  if (!items.length) {
    showError('No hay unidades detectadas en las skills importadas')
    return
  }
  try {
    const promises = items.map((s: any) => wp.addScopeUnit(planId, { unit_type: s.unit_type || 'department', unit_id: s.unit_id || null, unit_name: s.unit_name || s.department || 'Imported unit', inclusion_reason: 'growth', notes: s.rationale || '' }))
    await Promise.all(promises)
    showSuccess(`Importadas ${items.length} unidades al alcance del plan`)
  } catch (e) {
    void e
    showError('Error al importar unidades')
  }
}
const saveStep1 = async () => {
  if (!props.scenarioId) {
    showError('No hay escenario seleccionado')
    return
  }
  const payload = {
    step1: {
      metadata: {
        name: form.value.name,
        start_date: form.value.start_date,
        end_date: form.value.end_date,
        planning_horizon_months: form.value.planning_horizon_months,
        scope_type: form.value.scope_type,
      },
      skills: editedSkills.value,
      roles: editedRoles.value,
    }
  }

  try {
    await api.patch(`/api/strategic-planning/scenarios/${props.scenarioId}`, payload)
    showSuccess('Cambios guardados en el escenario')
  } catch (e) {
    void e
    showError('Error al guardar los cambios')
  }
}
</script>

<template>
  <div class="create-plan-wizard pa-4">
    <!-- Si ya existe un Workforce Plan asociado, ocultar el formulario de creación
         y mostrar acciones / navegación hacia el plan y los componentes de Fase 1 -->
    <div v-if="!planData">
      <v-form>
        <v-row>
          <v-col cols="12" md="8">
            <v-text-field v-model="form.name" label="Nombre del plan" required />
            <v-textarea v-model="form.description" label="Descripción" rows="2" />
          </v-col>
          <v-col cols="12" md="4">
            <v-select v-model="form.scope_type" :items="['organization_wide','business_unit','department','critical_roles_only']" label="Tipo de alcance" />
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12" md="4">
            <v-text-field v-model="form.start_date" label="Fecha inicio (YYYY-MM-DD)" />
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field v-model="form.end_date" label="Fecha término (YYYY-MM-DD)" />
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field v-model="form.planning_horizon_months" type="number" label="Horizonte (meses)" />
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12" md="4">
            <v-select v-model="form.owner_user_id" :items="users" item-title="name" item-value="id" label="Owner" dense clearable />
          </v-col>
          <v-col cols="12" md="4">
            <v-select v-model="form.sponsor_user_id" :items="users" item-title="name" item-value="id" label="Sponsor" dense clearable />
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field v-model="form.fiscal_year" type="number" label="Año fiscal" />
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12">
            <v-textarea v-model="form.strategic_context" label="Contexto estratégico" rows="2" />
          </v-col>
        </v-row>

        <v-row class="mt-3">
          <v-col class="text-right">
            <v-btn color="primary" :loading="loading" @click="submit">Crear Plan</v-btn>
          </v-col>
        </v-row>
      </v-form>
    </div>

    <div v-else class="mb-4">
      <v-row>
        <v-col cols="12" class="d-flex justify-end">
          <v-btn color="primary" variant="outlined" @click="openPlan" prepend-icon="mdi-open-in-new">Abrir Plan</v-btn>
        </v-col>
      </v-row>
      <v-alert type="info" class="mt-2">Este escenario ya tiene un Plan de Dotación asociado. Aquí se muestran los elementos importados de Fase 1.</v-alert>
    </div>
    <!-- Quick import actions -->
    <div class="mt-2" v-if="(planData || (skillDemands && skillDemands.length))">
      <v-row class="mb-3">
        <v-col cols="12" md="6">
          <v-btn color="secondary" variant="outlined" @click="importRolesFromSkills" :disabled="!skillDemands || !skillDemands.length">Importar roles desde skills</v-btn>
        </v-col>
        <v-col cols="12" md="6">
          <v-btn color="secondary" variant="outlined" @click="importUnitsFromSkills" :disabled="!skillDemands || !skillDemands.length">Importar unidades desde skills</v-btn>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" class="text-right">
          <v-btn color="primary" @click="saveStep1">Guardar Step 1</v-btn>
        </v-col>
      </v-row>
    </div>
    <div class="mt-6">
      <v-divider class="mb-4" />
      <h4>Datos prellenados (Fase 1)</h4>
      <div v-if="planData || (skillDemands && skillDemands.length)">
        <v-row>
          <v-col cols="12" md="6">
            <h5>Unidades incluidas</h5>
            <v-list v-if="planData.scope_units && planData.scope_units.length">
              <v-list-item v-for="u in planData.scope_units" :key="u.id">
                <v-list-item-content>
                  <v-list-item-title>{{ u.unit_name }}</v-list-item-title>
                  <v-list-item-subtitle>{{ u.unit_type }} — {{ u.inclusion_reason }}</v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-list>
            <v-alert v-else type="info">No hay unidades predefinidas.</v-alert>
          </v-col>
          <v-col cols="12" md="6">
            <h5>Skills predefinidas</h5>
            <div v-if="(planData && planData.skill_demands && planData.skill_demands.length) || (skillDemands && skillDemands.length)">
              <div v-if="(planData?.scope_type === 'organization_wide') || skillsCount() > 10">
                <div>Skills transversales: {{ skillsCount() }}</div>
                <v-btn text small color="primary" @click="showSkillsDialog = true">Ver detalle</v-btn>
              </div>
              <v-list v-else>
                <v-list-item v-for="s in (planData?.skill_demands || skillDemands)" :key="s.id">
                  <v-list-item-content>
                    <v-list-item-title>Skill #{{ s.skill_id || s.skill }}</v-list-item-title>
                    <v-list-item-subtitle>Requeridos: {{ s.required_headcount }} · Nivel: {{ s.required_level }} · Prioridad: {{ s.priority }}</v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </div>
            <v-alert v-else type="info">No hay skills predefinidas.</v-alert>
          </v-col>
          <v-col cols="12" md="6">
            <h5>Roles incluidos</h5>
            <div v-if="planData.scope_roles && planData.scope_roles.length">
              <div v-if="(planData.scope_type === 'organization_wide') || rolesCount() > 10">
                <div>Roles afectados: {{ rolesCount() }}</div>
                <v-btn text small color="primary" @click="showRolesDialog = true">Ver detalle</v-btn>
              </div>
              <v-list v-else>
                <v-list-item v-for="r in planData.scope_roles" :key="r.id">
                  <v-list-item-content>
                    <v-list-item-title>Role #{{ r.role_id }}</v-list-item-title>
                    <v-list-item-subtitle>{{ r.inclusion_reason }} — {{ r.notes }}</v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </div>
            <v-alert v-else type="info">No hay roles predefinidos.</v-alert>
          </v-col>
        </v-row>

        <v-row class="mt-4">
          <v-col cols="12">
            <h5>Proyectos de transformación</h5>
            <v-list v-if="planData.transformation_projects && planData.transformation_projects.length">
              <v-list-item v-for="p in planData.transformation_projects" :key="p.id">
                <v-list-item-content>
                  <v-list-item-title>{{ p.project_name }}</v-list-item-title>
                  <v-list-item-subtitle>{{ p.project_type }} — Impacto: {{ p.estimated_fte_impact || '—' }}</v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-list>
            <v-alert v-else type="info">No hay proyectos predefinidos.</v-alert>
          </v-col>
        </v-row>
      </div>
      <div v-else>
        <v-alert type="info">No hay datos de Fase 1 asociados. Puedes crear el plan manualmente.</v-alert>
      </div>
    </div>
    <!-- Roles detail dialog -->
    <v-dialog v-model="showRolesDialog" width="800">
      <v-card>
        <v-card-title>Roles afectados</v-card-title>
        <v-card-text>
          <v-row v-for="(r, idx) in editedRoles" :key="r.id" class="mb-2">
            <v-col cols="12" md="4">
              <div class="text-subtitle-1">Role #{{ r.role_id }}</div>
            </v-col>
            <v-col cols="12" md="3">
              <v-select v-model="editedRoles[idx].inclusion_reason" :items="['critical','hard_to_fill','transformation','high_risk','other']" label="Razón de inclusión" dense />
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field v-model="editedRoles[idx].notes" label="Notas" dense />
            </v-col>
            <v-col cols="12" md="1">
              <v-checkbox v-model="editedRoles[idx].include" label="Incluir" dense />
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text color="primary" @click="showRolesDialog = false">Cerrar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Skills detail dialog -->
    <v-dialog v-model="showSkillsDialog" width="900">
      <v-card>
        <v-card-title>Skills predefinidas</v-card-title>
        <v-card-text>
          <v-row class="mb-2" v-for="(s, idx) in editedSkills" :key="s.id">
            <v-col cols="12" md="3">
              <div class="text-subtitle-2">Skill #{{ s.skill_id }}</div>
            </v-col>
            <v-col cols="12" md="2">
              <v-text-field v-model="editedSkills[idx].required_headcount" type="number" label="Headcount" dense />
            </v-col>
            <v-col cols="12" md="2">
              <v-text-field v-model="editedSkills[idx].required_level" label="Nivel" dense />
            </v-col>
            <v-col cols="12" md="2">
              <v-select v-model="editedSkills[idx].priority" :items="['low','medium','high','critical']" label="Prioridad" dense />
            </v-col>
            <v-col cols="12" md="2">
              <v-select v-model="editedSkills[idx].action" :items="['include','ignore','create_role','map_role']" label="Acción" dense />
            </v-col>
            <v-col cols="12" md="1">
              <v-checkbox v-model="editedSkills[idx].include" label="Incluir" dense />
            </v-col>
            <v-col cols="12" md="12">
              <v-text-field v-model="editedSkills[idx].rationale" label="Rationale / Notes" dense />
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text color="primary" @click="showSkillsDialog = false">Cerrar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<style scoped>
.create-plan-wizard { max-width: 980px; }
</style>
