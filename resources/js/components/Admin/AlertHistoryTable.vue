<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center justify-between gap-4">
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Historial de Alertas</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Total: {{ totalAlerts }} alertas
          </p>
        </div>
        <div class="flex items-center gap-2">
          <button
            @click="applyFilter('all')"
            :class="[
              'px-3 py-1 rounded text-sm font-medium transition-colors',
              currentFilter === 'all'
                ? 'bg-blue-600 text-white'
                : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-300'
            ]"
          >
            Todas
          </button>
          <button
            @click="applyFilter('unacknowledged')"
            :class="[
              'px-3 py-1 rounded text-sm font-medium transition-colors',
              currentFilter === 'unacknowledged'
                ? 'bg-red-600 text-white'
                : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-300'
            ]"
          >
            Sin Confirmar ({{ unacknowledgedCount }})
          </button>
          <button
            @click="applyFilter('critical')"
            :class="[
              'px-3 py-1 rounded text-sm font-medium transition-colors',
              currentFilter === 'critical'
                ? 'bg-red-700 text-white'
                : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-300'
            ]"
          >
            Críticas ({{ criticalCount }})
          </button>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
          <tr>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Métrica</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Severidad</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Valor</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Disparado</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Estado</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <template v-if="filteredAlerts.length > 0">
            <tr
              v-for="alert in filteredAlerts"
              :key="alert.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <!-- Metric -->
              <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                {{ alert.alert_threshold?.metric || 'Unknown' }}
              </td>

              <!-- Severity Badge -->
              <td class="px-6 py-4">
                <span :class="[
                  'inline-block px-3 py-1 rounded-full text-xs font-semibold',
                  getSeverityClasses(alert.severity)
                ]">
                  {{ alert.severity.toUpperCase() }}
                </span>
              </td>

              <!-- Value -->
              <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                {{ alert.metric_value.toFixed(2) }}
              </td>

              <!-- Triggered Time -->
              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                {{ formatTime(alert.triggered_at) }}
              </td>

              <!-- Status Badge -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <span :class="getStatusClass(alert.status)" class="inline-block w-2 h-2 rounded-full"></span>
                  <span class="text-sm text-gray-900 dark:text-white capitalize">
                    {{ getStatusLabel(alert.status) }}
                  </span>
                </div>
              </td>

              <!-- Actions -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <button
                    v-if="!alert.acknowledged_at && alert.status === 'triggered'"
                    @click="acknowledgeAlert(alert)"
                    :disabled="isProcessing"
                    class="text-xs px-2 py-1 bg-green-600 hover:bg-green-700 disabled:bg-gray-400
                           text-white rounded transition-colors"
                  >
                    {{ isProcessing ? '...' : 'Confirmar' }}
                  </button>
                  <button
                    v-if="alert.status === 'acknowledged'"
                    @click="resolveAlert(alert)"
                    :disabled="isProcessing"
                    class="text-xs px-2 py-1 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400
                           text-white rounded transition-colors"
                  >
                    Resolver
                  </button>
                  <button
                    v-if="alert.status === 'triggered'"
                    @click="muteAlert(alert)"
                    :disabled="isProcessing"
                    class="text-xs px-2 py-1 bg-gray-500 hover:bg-gray-600 disabled:bg-gray-400
                           text-white rounded transition-colors"
                  >
                    Silenciar
                  </button>
                  <button
                    @click="viewDetails(alert)"
                    class="text-xs px-2 py-1 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600
                           dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded transition-colors"
                  >
                    Ver
                  </button>
                </div>
              </td>
            </tr>
          </template>
          <tr v-else>
            <td colspan="6" class="px-6 py-8 text-center">
              <p class="text-gray-600 dark:text-gray-400">
                {{ isLoading ? 'Cargando alertas...' : 'No hay alertas en este filtro' }}
              </p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="Math.ceil(totalAlerts / itemsPerPage) > 1" class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 flex items-center justify-between">
      <p class="text-sm text-gray-600 dark:text-gray-400">
        Página {{ currentPage }} de {{ Math.ceil(totalAlerts / itemsPerPage) }}
      </p>
      <div class="flex gap-2">
        <button
          @click="previousPage"
          :disabled="currentPage === 1"
          class="px-3 py-1 bg-gray-200 dark:bg-gray-600 text-gray-900 dark:text-white rounded disabled:opacity-50"
        >
          Anterior
        </button>
        <button
          @click="nextPage"
          :disabled="currentPage >= Math.ceil(totalAlerts / itemsPerPage)"
          class="px-3 py-1 bg-gray-200 dark:bg-gray-600 text-gray-900 dark:text-white rounded disabled:opacity-50"
        >
          Siguiente
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import type { AlertHistory, AlertThreshold } from '@/types'

interface Props {
  alerts?: (AlertHistory & { alert_threshold?: AlertThreshold })[]
  isLoading?: boolean
}

interface Emits {
  (e: 'acknowledge', alert: AlertHistory): void
  (e: 'resolve', alert: AlertHistory): void
  (e: 'mute', alert: AlertHistory): void
  (e: 'details', alert: AlertHistory): void
}

const props = withDefaults(defineProps<Props>(), {
  alerts: () => [],
  isLoading: false,
})

const emit = defineEmits<Emits>()

const currentPage = ref(1)
const itemsPerPage = 10
const currentFilter = ref<'all' | 'unacknowledged' | 'critical'>('all')
const isProcessing = ref(false)

const filteredAlerts = computed(() => {
  let filtered = props.alerts

  if (currentFilter.value === 'unacknowledged') {
    filtered = filtered.filter(a => !a.acknowledged_at && a.status === 'triggered')
  } else if (currentFilter.value === 'critical') {
    filtered = filtered.filter(a => ['critical', 'high'].includes(a.severity) && a.status !== 'resolved')
  }

  const start = (currentPage.value - 1) * itemsPerPage
  return filtered.slice(start, start + itemsPerPage)
})

const unacknowledgedCount = computed(() =>
  props.alerts.filter(a => !a.acknowledged_at && a.status === 'triggered').length
)

const criticalCount = computed(() =>
  props.alerts.filter(a => ['critical', 'high'].includes(a.severity) && a.status !== 'resolved').length
)

const totalAlerts = computed(() => props.alerts.length)

const severityColors: Record<string, string> = {
  critical: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
  high: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
  medium: 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
  low: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
  info: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
}

const statusClasses: Record<string, string> = {
  triggered: 'bg-red-500',
  acknowledged: 'bg-yellow-500',
  resolved: 'bg-green-500',
  muted: 'bg-gray-500',
}

const getSeverityClasses = (severity: string) =>
  severityColors[severity] || 'bg-gray-100 text-gray-800'

const getStatusClass = (status: string) =>
  statusClasses[status] || 'bg-gray-500'

const getStatusLabel = (status: string): string => {
  const labels: Record<string, string> = {
    triggered: 'Disparado',
    acknowledged: 'Confirmado',
    resolved: 'Resuelto',
    muted: 'Silenciado',
  }
  return labels[status] || status
}

const formatTime = (time: string): string => {
  const date = new Date(time)
  const now = new Date()
  const diff = now.getTime() - date.getTime()
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)

  if (minutes < 1) return 'Hace unos segundos'
  if (minutes < 60) return `Hace ${minutes}m`
  if (hours < 24) return `Hace ${hours}h`
  if (days < 7) return `Hace ${days}d`
  return date.toLocaleDateString('es-ES')
}

const acknowledgeAlert = async (alert: AlertHistory) => {
  isProcessing.value = true
  try {
    emit('acknowledge', alert)
  } finally {
    isProcessing.value = false
  }
}

const resolveAlert = async (alert: AlertHistory) => {
  isProcessing.value = true
  try {
    emit('resolve', alert)
  } finally {
    isProcessing.value = false
  }
}

const muteAlert = async (alert: AlertHistory) => {
  isProcessing.value = true
  try {
    emit('mute', alert)
  } finally {
    isProcessing.value = false
  }
}

const viewDetails = (alert: AlertHistory) => {
  emit('details', alert)
}

const applyFilter = (filter: typeof currentFilter.value) => {
  currentFilter.value = filter
  currentPage.value = 1
}

const previousPage = () => {
  if (currentPage.value > 1) currentPage.value--
}

const nextPage = () => {
  if (currentPage.value < Math.ceil(totalAlerts.value / itemsPerPage)) currentPage.value++
}
</script>
