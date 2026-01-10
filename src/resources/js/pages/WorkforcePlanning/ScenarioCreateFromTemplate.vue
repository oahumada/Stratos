<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { useApi } from '@/composables/useApi'
import { useNotification } from '@/composables/useNotification'
import ParentScenarioSelector from '@/components/WorkforcePlanning/ParentScenarioSelector.vue'

interface ScenarioTemplate {
  id: number
  name: string
  description: string
  scenario_type: string
  config?: Record<string, any>
  icon?: string
}

const emit = defineEmits<{
  (e: 'created'): void
  (e: 'close'): void
}>()

const api = useApi()
const { showSuccess, showError } = useNotification()

const loading = ref(false)
const submitting = ref(false)
const templates = ref<ScenarioTemplate[]>([])
const selectedTemplateId = ref<number | null>(null)
const selectedTemplate = computed(() => templates.value.find(t => t.id === selectedTemplateId.value) || null)

const customizations = ref({
  name: '',
  description: '',
  time_horizon_weeks: 52,
  estimated_budget: 500000,
  scenario_type: 'growth',
  scope_type: 'organization' as 'organization' | 'department' | 'role_family',
  parent_id: null as number | null,
})

const loadTemplates = async () => {
  loading.value = true
  try {
    const res: any = await api.get('/api/v1/workforce-planning/scenario-templates')
    templates.value = Array.isArray(res?.data) ? res.data : Array.isArray(res) ? res : []
  } catch (error) {
    showError('No se pudieron cargar las plantillas')
  } finally {
    loading.value = false
  }
}

const applyTemplateDefaults = () => {
  if (!selectedTemplate.value) return
  customizations.value.name = selectedTemplate.value.name
  customizations.value.description = selectedTemplate.value.description || ''
  // Backend accepted values: succession,growth,cost_optimization,restructuring,capacity_planning
  const allowed = ['succession','growth','cost_optimization','restructuring','capacity_planning']
  const tplType = selectedTemplate.value.scenario_type
  customizations.value.scenario_type = allowed.includes(tplType) ? tplType : 'growth'
}

const createScenario = async () => {
  if (!selectedTemplate.value) {
    showError('Selecciona una plantilla')
    return
  }
  if (!customizations.value.name.trim()) {
    showError('El nombre del escenario es obligatorio')
    return
  }
  submitting.value = true
  try {
    // debug payload to help diagnose 422 validation errors
    console.debug('instantiate-from-template payload', { customizations: customizations.value })
    const res: any = await api.post(`/api/v1/workforce-planning/workforce-scenarios/${selectedTemplate.value.id}/instantiate-from-template`, {
      customizations: customizations.value,
    })
    const createdId = res?.data?.id ?? res?.id ?? res?.scenario?.id ?? null

    // Store minimal customizations to prefill Phase 1 wizard after navigation
    try {
        const plan = res?.workforce_plan ?? res?.data?.workforce_plan ?? null
        if (plan) {
          // prefer backend-created plan for accurate defaults
          localStorage.setItem('wfp_prefill', JSON.stringify({
            name: plan.name,
            scope_type: plan.scope_type === 'organization_wide' ? 'organization' : (plan.scope_type === 'department' ? 'department' : 'role_family'),
            description: plan.description,
            time_horizon_weeks: (plan.planning_horizon_months || 0) * 4,
          }))
        } else {
          localStorage.setItem('wfp_prefill', JSON.stringify({
            name: customizations.value.name,
            scope_type: customizations.value.scope_type,
            description: customizations.value.description,
            time_horizon_weeks: customizations.value.time_horizon_weeks,
          }))
        }
    } catch (e) {
      // ignore storage errors
    }

    showSuccess('Escenario creado correctamente')
    // Navigate to scenario detail if id available, otherwise emit events as before
    if (createdId) {
      router.visit(`/workforce-planning/${createdId}`)
    } else {
      emit('created')
      emit('close')
    }
  } catch (error: any) {
    // Log full server response to help debug 422 validation errors
    console.error('instantiate-from-template error:', error?.response?.data ?? error)
    const apiMessage = error?.response?.data?.message
    const firstError = error?.response?.data?.errors ? Object.values(error.response.data.errors).flat()[0] : null
    showError(apiMessage || (firstError as string) || 'No se pudo crear el escenario')
  } finally {
    submitting.value = false
  }
}

const goToScenario = (id: number) => {
  router.visit(`/workforce-planning/${id}`)
}

onMounted(() => {
  loadTemplates()
})
</script>

<template>
  <v-card>
    <v-card-title class="d-flex align-center justify-space-between">
      <div>
        <div class="text-h6">Crear escenario desde plantilla</div>
        <div class="text-caption text-medium-emphasis">Selecciona una plantilla y ajusta los parámetros clave</div>
      </div>
      <v-btn icon="mdi-close" variant="text" @click="emit('close')" />
    </v-card-title>

    <v-divider></v-divider>

    <v-card-text>
      <v-row>
        <v-col cols="12" md="4">
          <v-skeleton-loader v-if="loading" type="list-item-two-line" class="mb-4"/>
          <v-list v-else density="comfortable" nav class="rounded-lg border">
            <v-list-item
              v-for="tpl in templates"
              :key="tpl.id"
              :value="tpl.id"
              :active="tpl.id === selectedTemplateId"
              @click="selectedTemplateId = tpl.id; applyTemplateDefaults()"
              class="mb-1"
            >
              <template #prepend>
                <v-icon :icon="tpl.icon || 'mdi-shape-outline'" color="primary" class="mr-2" />
              </template>
              <v-list-item-title>{{ tpl.name }}</v-list-item-title>
              <v-list-item-subtitle>{{ tpl.description }}</v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </v-col>

        <v-col cols="12" md="8">
          <v-alert v-if="!selectedTemplate" type="info" class="mb-4" variant="tonal">
            Selecciona una plantilla para ver los detalles y continuar.
          </v-alert>

          <div v-else>
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="customizations.name"
                  label="Nombre del escenario"
                  required
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-select
                  v-model="customizations.scenario_type"
                  :items="[
                    { value: 'succession', title: 'Sucesión' },
                    { value: 'growth', title: 'Crecimiento' },
                    { value: 'cost_optimization', title: 'Optimización de Costos' },
                    { value: 'restructuring', title: 'Reestructuración' },
                    { value: 'capacity_planning', title: 'Planificación de Capacidad' },
                  ]"
                  label="Tipo de escenario"
                />
              </v-col>
              <v-col cols="12">
                <v-textarea
                  v-model="customizations.description"
                  label="Descripción"
                  rows="3"
                  auto-grow
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" md="4">
                <v-text-field
                  v-model.number="customizations.time_horizon_weeks"
                  type="number"
                  label="Horizonte (semanas)"
                  min="4"
                  step="4"
                />
              </v-col>
              <v-col cols="12" md="4">
                <v-select
                  v-model="customizations.scope_type"
                  :items="[
                    { value: 'organization', title: '🏢 Organización' },
                    { value: 'department', title: '🏢 Departamento' },
                    { value: 'role_family', title: '👥 Familia de Roles' },
                  ]"
                  label="Nivel de alcance (Scope)"
                  hint="Define el nivel de jerarquía del escenario"
                  persistent-hint
                />
              </v-col>
            </v-row>

            <v-row v-if="customizations.scope_type !== 'organization'" class="mt-2">
              <v-col cols="12">
                <ParentScenarioSelector
                  v-model="customizations.parent_id"
                  :organization-id="1"
                  :scope-type="customizations.scope_type"
                  label="Escenario Padre (Opcional)"
                  hint="Selecciona un escenario padre para heredar skills obligatorias"
                />
              </v-col>
            </v-row>

            <v-divider class="my-4" />

            <h4 class="mb-2">Skills predefinidos</h4>
            <v-chip-group v-if="selectedTemplate?.config?.predefined_skills" column>
              <v-chip
                v-for="skill in selectedTemplate.config.predefined_skills"
                :key="skill.skill_id || skill.skill"
                color="primary"
                variant="tonal"
                class="ma-1"
              >
                {{ skill.skill || ('Skill #' + skill.skill_id) }} - {{ skill.priority || 'medium' }}
              </v-chip>
            </v-chip-group>
            <v-alert v-else type="warning" variant="tonal" class="mb-4">
              La plantilla no define skills preconfigurados.
            </v-alert>

            <h4 class="mt-4 mb-2">KPIs sugeridos</h4>
            <v-chip-group v-if="selectedTemplate?.config?.kpis" column>
              <v-chip
                v-for="kpi in selectedTemplate.config.kpis"
                :key="kpi"
                color="secondary"
                variant="tonal"
                class="ma-1"
              >
                {{ kpi }}
              </v-chip>
            </v-chip-group>
          </div>
        </v-col>
      </v-row>
    </v-card-text>

    <v-divider></v-divider>

    <v-card-actions class="justify-end">
      <v-btn variant="text" @click="emit('close')">Cancelar</v-btn>
      <v-btn :disabled="!selectedTemplate" color="primary" :loading="submitting" @click="createScenario" prepend-icon="mdi-rocket">
        Crear escenario
      </v-btn>
    </v-card-actions>
  </v-card>
</template>
