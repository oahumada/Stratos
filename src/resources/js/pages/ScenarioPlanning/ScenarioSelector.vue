<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';

defineOptions({ layout: AppLayout });

interface Scenario {
  id: number
  name: string
  description: string
  status: string
  fiscal_year: number
  horizon_months: number
  created_at: string
}

const api = useApi()
const { showSuccess, showError } = useNotification()

const scenarios = ref<Scenario[]>([])
const loading = ref(false)
const showCreateDialog = ref(false)
const editingScenario = ref<Scenario | null>(null)

const formData = ref({
  name: '',
  description: '',
    scenario_type: 'growth',
  horizon_months: 12,
  fiscal_year: new Date().getFullYear(),
})

const filters = ref({
  status: null as string | null,
  fiscalYear: null as number | null,
})

const statusOptions = [
  'draft',
  'active',
  'completed',
  'archived',
]
const scenarioTypeOptions = [
  { value: 'growth', title: 'Crecimiento' },
  { value: 'transformation', title: 'Transformación' },
  { value: 'optimization', title: 'Optimización' },
  { value: 'crisis', title: 'Crisis' },
  { value: 'custom', title: 'Personalizado' },
]


const availableYears = computed(() => {
  const currentYear = new Date().getFullYear()
  return Array.from({ length: 5 }, (_, i) => currentYear + i)
})

const tableHeaders = [
  { title: 'Name', key: 'name' },
  { title: 'Status', key: 'status' },
  { title: 'Fiscal Year', key: 'fiscal_year' },
  { title: 'Horizon (months)', key: 'horizon_months' },
  { title: 'Created', key: 'created_at' },
  { title: 'Actions', key: 'actions', sortable: false },
]

const getStatusColor = (status: string): string => {
  const colors: Record<string, string> = {
    draft: 'warning',
    active: 'success',
    completed: 'info',
    archived: 'grey',
  }
  return colors[status] || 'default'
}

const loadScenarios = async () => {
  loading.value = true
  try {
    const params: Record<string, any> = {}
    if (filters.value.status) params.status = filters.value.status
    if (filters.value.fiscalYear) params.fiscal_year = filters.value.fiscalYear

    const res = await api.get('/api/workforce-planning/workforce-scenarios', { params })
    // useApi.get devuelve response.data directamente
    // El backend responde { success, data: [...], pagination }
    scenarios.value = Array.isArray((res as any).data) ? (res as any).data : (Array.isArray(res) ? res : [])
    } catch (e) {
      void e
      showError('Failed to load scenarios')
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  loadScenarios()
}

const selectScenario = (event: any, row: any) => {
  // v-data-table en Vuetify 3 pasa el item como row.item
  const scenario = row?.item || event
  if (scenario && scenario.id) {
    router.visit(`/strategic-planning/${scenario.id}`)
  } else {
    console.error('Invalid scenario selected:', scenario)
  }
}

const editScenario = (scenario: Scenario) => {
  editingScenario.value = scenario
  formData.value = {
    name: scenario.name,
    description: scenario.description,
      scenario_type: (scenario as any).scenario_type || 'growth',
    horizon_months: scenario.horizon_months,
    fiscal_year: scenario.fiscal_year,
  }
  showCreateDialog.value = true
}

const saveScenario = async () => {
  try {
    if (editingScenario.value) {
      await api.put(`/api/workforce-planning/workforce-scenarios/${editingScenario.value.id}`, formData.value)
      showSuccess('Scenario updated successfully')
    } else {
      await api.post('/api/workforce-planning/workforce-scenarios', formData.value)
      showSuccess('Scenario created successfully')
    }
    showCreateDialog.value = false
    editingScenario.value = null
    loadScenarios()
  } catch (error: any) {
    console.error('Save scenario error:', error)
    // Intentar extraer mensaje de validación o backend
    const apiMessage = error?.response?.data?.message
    const errors = error?.response?.data?.errors
    const firstError = errors ? Object.values(errors).flat()[0] : null
    showError(apiMessage || firstError || error?.message || 'Failed to save scenario')
  }
}

const deleteScenario = async (id: number) => {
  if (confirm('Are you sure you want to delete this scenario?')) {
    try {
      await api.delete(`/api/workforce-planning/workforce-scenarios/${id}`)
      showSuccess('Scenario deleted successfully')
      loadScenarios()
    } catch (e) {
      void e
      showError('Failed to delete scenario')
    }
  }
}

onMounted(() => {
  loadScenarios()
})
</script>

<template>
  <div class="scenario-selector">
    <v-container fluid>
      <v-row class="mb-4">
        <v-col cols="12" md="6">
          <h2>Planning Scenarios</h2>
        </v-col>
        <v-col cols="12" md="6" class="text-right">
          <v-btn
            color="primary"
            @click="showCreateDialog = true"
            prepend-icon="mdi-plus"
          >
            New Scenario
          </v-btn>
        </v-col>
      </v-row>

      <!-- Filters -->
      <v-row class="mb-4">
        <v-col cols="12" md="4">
          <v-select
            v-model="filters.status"
            :items="statusOptions"
            label="Filter by Status"
            clearable
            @update:model-value="applyFilters"
          />
        </v-col>
        <v-col cols="12" md="4">
          <v-select
            v-model="filters.fiscalYear"
            :items="availableYears"
            label="Fiscal Year"
            clearable
            @update:model-value="applyFilters"
          />
        </v-col>
      </v-row>

      <!-- Scenarios Table -->
      <v-data-table
        :headers="tableHeaders"
        :items="scenarios"
        :loading="loading"
        :items-per-page="10"
        class="elevation-1"
        @click:row="selectScenario"
      >
        <!-- eslint-disable-next-line vue/valid-v-slot -->
        <template v-slot:item.status="{ item }">
          <v-chip
            :color="getStatusColor(item.status)"
            text-color="white"
            size="small"
          >
            {{ item.status }}
          </v-chip>
        </template>

        <!-- eslint-disable-next-line vue/valid-v-slot -->
        <template v-slot:item.actions="{ item }">
          <v-btn
            icon
            size="small"
            @click.stop="editScenario(item)"
            title="Edit"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn
            icon
            size="small"
            @click.stop="deleteScenario(item.id)"
            title="Delete"
            color="error"
          >
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>

      <!-- Create/Edit Dialog -->
      <v-dialog v-model="showCreateDialog" max-width="600px">
        <v-card>
          <v-card-title>{{ editingScenario ? 'Edit Scenario' : 'Create Scenario' }}</v-card-title>
          <v-card-text>
            <v-text-field
              v-model="formData.name"
              label="Scenario Name"
              required
              class="mb-3"
            />
            <v-textarea
              v-model="formData.description"
              label="Description"
              rows="3"
              class="mb-3"
            />
            <v-select
              v-model="formData.scenario_type"
              :items="scenarioTypeOptions"
              label="Tipo de Escenario"
              required
              class="mb-3"
            />
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model.number="formData.horizon_months"
                  label="Planning Horizon (months)"
                  type="number"
                  min="1"
                  max="36"
                  required
                />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model.number="formData.fiscal_year"
                  label="Fiscal Year"
                  type="number"
                  required
                />
              </v-col>
            </v-row>
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn @click="showCreateDialog = false">Cancel</v-btn>
            <v-btn color="primary" @click="saveScenario">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </div>
</template>



<style scoped>
.scenario-selector {
  padding: 20px;
}
</style>
