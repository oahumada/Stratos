<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { useApi } from '@/composables/useApi'
import { useNotification } from '@/composables/useNotification'
import { useWorkforcePlanningStore } from '@/stores/workforcePlanningStore'
import ScenarioCreateFromTemplate from './ScenarioCreateFromTemplate.vue'

defineOptions({ layout: AppLayout })

type ScenarioStatus = 'draft' | 'active' | 'completed' | 'archived'

type ScenarioListItem = {
  id: number
  name: string
  description: string
  scenario_type: string
  status: ScenarioStatus
  time_horizon_weeks?: number
  fiscal_year?: number
  created_at: string
}

const api = useApi()
const { showSuccess, showError } = useNotification()
const store = useWorkforcePlanningStore()

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
  { title: 'Estado', key: 'status' },
  { title: 'Horizonte (semanas)', key: 'time_horizon_weeks' },
  { title: 'Fiscal year', key: 'fiscal_year' },
  { title: 'Creado', key: 'created_at' },
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

const loadScenarios = async () => {
  loading.value = true
  try {
    const res: any = await api.get('/api/v1/workforce-planning/workforce-scenarios')
    const data = Array.isArray(res?.data) ? res.data : Array.isArray(res) ? res : []
    scenarios.value = data
    store.scenarios = data
  } catch (error) {
    showError('No se pudieron cargar los escenarios')
  } finally {
    loading.value = false
  }
}

const goToDetail = (scenario: ScenarioListItem) => {
  router.visit(`/workforce-planning/${scenario.id}`)
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
          <p class="text-medium-emphasis mb-0">Administra, filtra y navega escenarios</p>
        </v-col>
        <v-col cols="12" md="6" class="text-right">
          <v-btn color="primary" prepend-icon="mdi-plus" @click="openCreateFromTemplate">
            Nuevo desde plantilla
          </v-btn>
        </v-col>
      </v-row>

      <v-card class="mb-4">
        <v-card-text>
          <v-row>
            <v-col cols="12" md="4">
              <v-select
                v-model="filters.status"
                :items="statusOptions"
                label="Estado"
                clearable
              />
            </v-col>
            <v-col cols="12" md="4">
              <v-select
                v-model="filters.type"
                :items="typeOptions"
                item-title="title"
                item-value="value"
                label="Tipo de escenario"
                clearable
              />
            </v-col>
            <v-col cols="12" md="4" class="text-right">
              <v-btn variant="outlined" color="primary" @click="loadScenarios" prepend-icon="mdi-refresh">
                Refrescar
              </v-btn>
            </v-col>
          </v-row>
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
          <template #item.status="{ item }">
            <v-chip :color="statusColor(item.status)" size="small" variant="flat" class="text-uppercase">
              {{ item.status }}
            </v-chip>
          </template>

          <template #item.actions="{ item }">
            <v-btn size="small" variant="text" color="primary" @click="goToDetail(item)" prepend-icon="mdi-eye">
              Ver
            </v-btn>
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
