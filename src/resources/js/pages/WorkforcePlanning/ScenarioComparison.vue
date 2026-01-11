<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import { useNotification } from '@/composables/useNotification'
import { useStrategicPlanningScenariosStore } from '@/stores/strategicPlanningScenariosStore'

interface ComparisonScenario {
  scenario_id: number
  name: string
  total_cost?: number
  max_timeline_weeks?: number
  average_risk_score?: number
  expected_coverage?: number
  estimated_roi?: number
}

interface ComparisonResult {
  id: number
  comparison_name?: string
  scenarios: ComparisonScenario[]
  comparison_summary?: Record<string, any>
}

const props = defineProps<{ scenarioId?: number }>()

const api = useApi()
const { showError } = useNotification()
const store = useStrategicPlanningScenariosStore()

const loading = ref(false)
const comparing = ref(false)
const comparison = ref<ComparisonResult | null>(null)
const selectedScenarioIds = ref<number[]>(props.scenarioId ? [props.scenarioId] : [])

const availableScenarios = computed(() => store.scenarios || [])

const loadScenariosIfEmpty = async () => {
  if (store.scenarios.length > 0) return
  loading.value = true
  try {
     const res: any = await api.get('/api/v1/strategic-planning/workforce-scenarios')
    const data = Array.isArray(res?.data) ? res.data : Array.isArray(res) ? res : []
    store.scenarios = data
  } catch (error) {
     void error
     showError('No se pudieron cargar los escenarios para comparar')
  } finally {
    loading.value = false
  }
}

const compare = async () => {
  if (!selectedScenarioIds.value.length) {
    showError('Selecciona al menos un escenario para comparar')
    return
  }
  comparing.value = true
  try {
      const res: any = await api.post('/api/v1/strategic-planning/scenario-comparisons', {
      scenario_ids: selectedScenarioIds.value,
      comparison_criteria: { cost: true, time: true, risk: true, coverage: true, roi: true },
    })
    comparison.value = res?.data || res
  } catch (error) {
      void error
      showError('Error al comparar escenarios')
  } finally {
    comparing.value = false
  }
}

const formatMoney = (value?: number) => {
  if (value === undefined || value === null) return '—'
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(value)
}

const formatPct = (value?: number) => {
  if (value === undefined || value === null) return '—'
  return `${Math.round(value * 100)}%`
}

onMounted(() => {
  loadScenariosIfEmpty()
})
</script>

<template>
  <div class="scenario-comparison">
    <v-card class="mb-4">
      <v-card-title>Comparar escenarios</v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="8">
            <v-select
              v-model="selectedScenarioIds"
              :items="availableScenarios"
              item-title="name"
              item-value="id"
              label="Selecciona escenarios"
              multiple
              chips
              closable-chips
              :loading="loading"
            />
          </v-col>
          <v-col cols="12" md="4" class="d-flex align-end justify-end">
            <v-btn color="primary" :loading="comparing" @click="compare" prepend-icon="mdi-compare">
              Comparar
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <v-card v-if="comparison">
      <v-card-title>Resultado de comparación</v-card-title>
      <v-data-table
        :items="comparison.scenarios"
        :headers="[
          { title: 'Escenario', key: 'name' },
          { title: 'Costo total', key: 'total_cost' },
          { title: 'Timeline (semanas)', key: 'max_timeline_weeks' },
          { title: 'Riesgo', key: 'average_risk_score' },
          { title: 'Cobertura', key: 'expected_coverage' },
          { title: 'ROI', key: 'estimated_roi' },
        ]"
        item-key="scenario_id"
        class="elevation-0"
      >
        <!-- eslint-disable-next-line vue/valid-v-slot -->
        <template #item.total_cost="{ item }">{{ formatMoney(item.total_cost) }}</template>
        <!-- eslint-disable-next-line vue/valid-v-slot -->
        <template #item.max_timeline_weeks="{ item }">{{ item.max_timeline_weeks || '—' }}</template>
        <!-- eslint-disable-next-line vue/valid-v-slot -->
        <template #item.average_risk_score="{ item }">{{ item.average_risk_score?.toFixed(2) ?? '—' }}</template>
        <!-- eslint-disable-next-line vue/valid-v-slot -->
        <template #item.expected_coverage="{ item }">{{ formatPct(item.expected_coverage) }}</template>
        <!-- eslint-disable-next-line vue/valid-v-slot -->
        <template #item.estimated_roi="{ item }">{{ item.estimated_roi ? item.estimated_roi.toFixed(2) + 'x' : '—' }}</template>
      </v-data-table>

      <v-card-text v-if="comparison?.comparison_summary">
        <div class="text-subtitle-1 mb-2">Resumen</div>
        <pre class="text-caption">{{ comparison.comparison_summary }}</pre>
      </v-card-text>
    </v-card>
  </div>
</template>
