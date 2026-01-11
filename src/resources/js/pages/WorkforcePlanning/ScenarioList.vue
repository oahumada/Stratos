<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { useApi } from '@/composables/useApi'
import { useNotification } from '@/composables/useNotification'
import { useStrategicPlanningScenariosStore } from '@/stores/strategicPlanningScenariosStore'
import ScenarioCreateFromTemplate from './ScenarioCreateFromTemplate.vue'

defineOptions({ layout: AppLayout })

type ScenarioStatus = 'draft' | 'active' | 'completed' | 'archived'

type ScenarioListItem = {
  id: number
  name: string
  description: string
  scenario_type: string
  status: ScenarioStatus
  decision_status?: 'draft' | 'pending_approval' | 'approved' | 'rejected'
  execution_status?: 'planned' | 'in_progress' | 'paused' | 'completed'
  is_current_version?: boolean
  version_number?: number
  parent_id?: number | null
  time_horizon_weeks?: number
  fiscal_year?: number
  created_at: string
}

const api = useApi()
const { showSuccess, showError } = useNotification()
const store = useStrategicPlanningScenariosStore()

const scenarios = ref<ScenarioListItem[]>([])
const loading = ref(false)
const showCreateFromTemplate = ref(false)
const selectedScenarioIds = ref<number[]>([])

const filters = ref<{ status: ScenarioStatus | null; type: string | null }>({
  status: null,
  type: null,
})

const statusOptions: ScenarioStatus[] = ['draft', 'active', 'completed', 'archived']
const typeOptions = [
  { value: 'transformation', title: 'Transformación' },
  { value: 'growth', title: 'Crecimiento' },
  { value: 'automation', title: 'Automatización' },
  { value: 'merger', title: 'Fusión' },
  { value: 'custom', title: 'Personalizado' },
]

const tableHeaders = [
  { title: 'Nombre', key: 'name' },
  { title: 'Tipo', key: 'scenario_type' },
  { title: 'Estado (Decisión)', key: 'decision_status' },
  { title: 'Estado (Ejecución)', key: 'execution_status' },
  { title: 'Versión', key: 'version_number' },
  { title: 'Horizonte (semanas)', key: 'time_horizon_weeks' },
  { title: 'Acciones', key: 'actions', sortable: false },
]

const filteredScenarios = computed(() => {
  return scenarios.value.filter((s) => {
    const matchStatus = filters.value.status ? s.status === filters.value.status : true
    const matchType = filters.value.type ? s.scenario_type === filters.value.type : true
    return matchStatus && matchType
  })
})

const statusColor = (status: ScenarioStatus) => {
  const map: Record<ScenarioStatus, string> = {
    draft: 'warning',
    active: 'success',
    completed: 'info',
    archived: 'grey',
  }
  return map[status] || 'default'
}

// mark as referenced to avoid unused-var during refactor
void statusColor

const decisionStatusColor = (status?: string) => {
  const map: Record<string, string> = {
    draft: 'grey',
    pending_approval: 'warning',
    approved: 'success',
    rejected: 'error',
  }
  return map[status || 'draft'] || 'grey'
}

const executionStatusColor = (status?: string) => {
  const map: Record<string, string> = {
    planned: 'grey-lighten-1',
    in_progress: 'primary',
    paused: 'warning',
    completed: 'success',
  }
  return map[status || 'planned'] || 'grey'
}

const decisionStatusText = (status?: string) => {
  const map: Record<string, string> = {
    draft: 'Borrador',
    pending_approval: 'Pendiente',
    approved: 'Aprobado',
    rejected: 'Rechazado',
  }
  return map[status || 'draft']
}

const executionStatusText = (status?: string) => {
  const map: Record<string, string> = {
    planned: 'Planificado',
    in_progress: 'En ejecución',
    paused: 'Pausado',
    completed: 'Completado',
  }
  return map[status || 'planned']
}

const loadScenarios = async () => {
  loading.value = true
  try {
    const res: any = await api.get('/api/v1/strategic-planning/workforce-scenarios')
    const data = Array.isArray(res?.data) ? res.data : Array.isArray(res) ? res : []
    scenarios.value = data
    store.scenarios = data
  } catch (e) {
      void e
      showError('No se pudieron cargar los escenarios')
  } finally {
    loading.value = false
  }
}

const goToDetail = (scenario: ScenarioListItem) => {
  router.visit(`/strategic-planning/${scenario.id}`)
}

const deleteScenario = async (scenario: ScenarioListItem) => {
  const ok = window.confirm(`¿Eliminar escenario "${scenario.name}"? Esta acción no se puede deshacer.`)
  if (!ok) return

  try {
    await api.delete(`/api/v1/strategic-planning/workforce-scenarios/${scenario.id}`)
    showSuccess('Escenario eliminado')
    // reload list
    await loadScenarios()
  } catch (err: any) {
    showError(err?.response?.data?.message || 'Error al eliminar el escenario')
  }
}

const openCreateFromTemplate = () => {
  showCreateFromTemplate.value = true
}

onMounted(() => {
  loadScenarios()
})
</script>

<template>
  <div class="scenario-list-page">
    <v-container fluid>
      <v-row class="mb-4 align-center">
        <v-col cols="12" md="6">
          <h2 class="mb-0">Workforce Planning - Escenarios</h2>
          <p class="text-medium-emphasis mb-0">Administra, filtra y navega escenarios con metodología 7 pasos</p>
        </v-col>
      </v-row>

      <v-card class="mb-4">
        <v-card-text>
          <v-row>
            <v-col cols="12" md="3">
              <v-select
                v-model="filters.status"
                :items="statusOptions"
                label="Estado (Legacy)"
                clearable
              />
            </v-col>
            <v-col cols="12" md="3">
              <v-select
                v-model="filters.type"
                :items="typeOptions"
                item-title="title"
                item-value="value"
                label="Tipo de escenario"
                clearable
              />
            </v-col>
            <v-col cols="12" md="2" class="text-right">
              <v-btn variant="outlined" color="primary" @click="loadScenarios" prepend-icon="mdi-refresh">
                Refrescar
              </v-btn>
            </v-col>
            <v-col cols="12" md="4" class="text-right">
              <v-btn color="primary" prepend-icon="mdi-plus" @click="openCreateFromTemplate">
                Nuevo desde plantilla
              </v-btn>
            </v-col>
          </v-row>
          <v-alert type="info" variant="tonal" density="compact" class="mt-2">
            <strong>Estados duales:</strong> Los escenarios ahora tienen estado de decisión (borrador/pendiente/aprobado/rechazado) y estado de ejecución (planificado/en progreso/pausado/completado).
          </v-alert>
        </v-card-text>

        <v-data-table
          :headers="tableHeaders"
          :items="filteredScenarios"
          :loading="loading"
          item-key="id"
          show-select
          v-model:selected="selectedScenarioIds"
          class="elevation-0"
        >
          <!-- eslint-disable-next-line vue/valid-v-slot -->
          <template #item.decision_status="{ item }">
            <v-chip
              :color="decisionStatusColor(item.decision_status)"
              size="small"
              variant="flat"
              class="text-uppercase"
            >
              {{ decisionStatusText(item.decision_status) }}
            </v-chip>
          </template>

          <!-- eslint-disable-next-line vue/valid-v-slot -->
          <template #item.execution_status="{ item }">
            <v-chip
              v-if="item.decision_status === 'approved'"
              :color="executionStatusColor(item.execution_status)"
              size="small"
              variant="outlined"
              class="text-uppercase"
            >
              {{ executionStatusText(item.execution_status) }}
            </v-chip>
            <v-chip v-else size="small" color="grey-lighten-2" variant="flat" disabled>
              N/A
            </v-chip>
          </template>

          <!-- eslint-disable-next-line vue/valid-v-slot -->
          <template #item.version_number="{ item }">
            <div v-if="item.version_number" class="text-caption">
              <v-icon icon="mdi-history" size="x-small" class="mr-1" />
              v{{ item.version_number }}
              <v-chip v-if="item.is_current_version" size="x-small" color="primary" variant="flat" class="ml-1">
                Actual
              </v-chip>
            </div>
            <span v-else class="text-medium-emphasis">—</span>
          </template>

          <!-- eslint-disable-next-line vue/valid-v-slot -->
          <template #item.actions="{ item }">
            <div class="d-flex gap-1">
              <v-tooltip text="Ver detalle">
                <template #activator="{ props }">
                  <v-btn
                    v-bind="props"
                    size="small"
                    variant="text"
                    icon="mdi-eye"
                    color="primary"
                    @click="goToDetail(item)"
                  />
                </template>
              </v-tooltip>

              <v-tooltip text="Borrar">
                <template #activator="{ props }">
                  <v-btn
                    v-bind="props"
                    size="small"
                    variant="text"
                    icon="mdi-delete"
                    color="error"
                    @click="deleteScenario(item)"
                  />
                </template>
              </v-tooltip>

              <v-tooltip v-if="item.parent_id" text="Escenario hijo">
                <template #activator="{ props }">
                  <v-icon v-bind="props" icon="mdi-file-tree" size="small" color="info" />
                </template>
              </v-tooltip>

              <v-menu>
                <template #activator="{ props }">
                  <v-btn
                    v-bind="props"
                    size="small"
                    variant="text"
                    icon="mdi-dots-vertical"
                    color="grey-darken-1"
                  />
                </template>

                <v-list density="compact">
                  <v-list-item
                    v-if="item.decision_status === 'approved' && item.is_current_version"
                    prepend-icon="mdi-content-copy"
                    title="Crear nueva versión"
                    @click="goToDetail(item)"
                  />
                  <v-list-item
                    v-if="item.decision_status !== 'approved'"
                    prepend-icon="mdi-pencil"
                    title="Editar"
                    @click="goToDetail(item)"
                  />
                  <v-list-item
                    v-if="item.parent_id"
                    prepend-icon="mdi-sync"
                    title="Sincronizar desde padre"
                    @click="goToDetail(item)"
                  />
                  <v-divider />
                  <v-list-item
                    prepend-icon="mdi-history"
                    title="Ver historial de versiones"
                    @click="goToDetail(item)"
                  />
                </v-list>
              </v-menu>
            </div>
          </template>

          <template #no-data>
            <div class="pa-6 text-center text-medium-emphasis">
              No hay escenarios todavía. Crea uno desde plantilla.
            </div>
          </template>
        </v-data-table>
      </v-card>
    </v-container>

    <v-dialog v-model="showCreateFromTemplate" max-width="900px">
      <ScenarioCreateFromTemplate @created="loadScenarios" @close="showCreateFromTemplate = false" />
    </v-dialog>
  </div>
</template>
