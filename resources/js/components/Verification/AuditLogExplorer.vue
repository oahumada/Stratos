<template>
  <div class="audit-log-explorer">
    <!-- Filters -->
    <div class="grid grid-cols-4 gap-3 mb-4">
      <select v-model="filters.action" class="px-3 py-2 border border-border rounded-lg text-sm bg-card">
        <option value="">Todas las acciones</option>
        <option value="phase_transition">Transición de fase</option>
        <option value="config_change">Cambio de configuración</option>
        <option value="manual_override">Anulación manual</option>
      </select>

      <input 
        v-model="filters.dateFrom" 
        type="date" 
        class="px-3 py-2 border border-border rounded-lg text-sm bg-card"
        :max="filters.dateTo"
      />

      <input 
        v-model="filters.dateTo" 
        type="date" 
        class="px-3 py-2 border border-border rounded-lg text-sm bg-card"
        :min="filters.dateFrom"
        :max="today"
      />

      <button @click="fetchLogs" class="px-3 py-2 bg-primary text-primary-foreground rounded-lg text-sm hover:bg-primary/90">
        🔍 Buscar
      </button>
    </div>

    <!-- Export Options -->
    <div class="flex gap-2 mb-4">
      <button @click="exportFormat = 'json'" :class="exportFormat === 'json' ? 'bg-primary text-primary-foreground' : 'border border-border'" class="px-3 py-2 rounded text-sm">
        JSON
      </button>
      <button @click="exportFormat = 'csv'" :class="exportFormat === 'csv' ? 'bg-primary text-primary-foreground' : 'border border-border'" class="px-3 py-2 rounded text-sm">
        CSV
      </button>
      <button @click="downloadReport" class="px-3 py-2 border border-border rounded text-sm hover:bg-accent ml-auto">
        ⬇️ Descargar
      </button>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-5 gap-3 mb-4">
      <div class="bg-card border border-border rounded p-3 text-center">
        <p class="text-xs text-muted-foreground">Total de eventos</p>
        <p class="text-2xl font-bold">{{ summary?.total_events || 0 }}</p>
      </div>
      <div class="bg-card border border-border rounded p-3 text-center">
        <p class="text-xs text-muted-foreground">Transiciones</p>
        <p class="text-2xl font-bold">{{ summary?.phase_transitions || 0 }}</p>
      </div>
      <div class="bg-card border border-border rounded p-3 text-center">
        <p class="text-xs text-muted-foreground">Cambios de config</p>
        <p class="text-2xl font-bold">{{ summary?.config_changes || 0 }}</p>
      </div>
      <div class="bg-card border border-border rounded p-3 text-center">
        <p class="text-xs text-muted-foreground">Anulaciones</p>
        <p class="text-2xl font-bold">{{ summary?.manual_overrides || 0 }}</p>
      </div>
      <div class="bg-card border border-border rounded p-3 text-center">
        <p class="text-xs text-muted-foreground">Usuarios únicos</p>
        <p class="text-2xl font-bold">{{ summary?.unique_users || 0 }}</p>
      </div>
    </div>

    <!-- Audit Log Table -->
    <div class="bg-card border border-border rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-muted border-b border-border">
            <tr>
              <th class="px-4 py-3 text-left font-semibold">Timestamp</th>
              <th class="px-4 py-3 text-left font-semibold">Acción</th>
              <th class="px-4 py-3 text-left font-semibold">Usuario</th>
              <th class="px-4 py-3 text-left font-semibold">Cambios</th>
              <th class="px-4 py-3 text-left font-semibold">Origen</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="log in logs" :key="log.id" class="border-b border-border hover:bg-muted/50 cursor-pointer" @click="expandedId = expandedId === log.id ? null : log.id">
              <td class="px-4 py-3">{{ formatTime(log.created_at) }}</td>
              <td class="px-4 py-3 capitalize">
                <span :class="getActionBadge(log.action)">{{ log.action.replace(/_/g, ' ') }}</span>
              </td>
              <td class="px-4 py-3">{{ log.user?.name || 'Sistema' }}</td>
              <td class="px-4 py-3">
                <span v-if="log.phase_from && log.phase_to" class="text-xs bg-muted px-2 py-1 rounded">
                  {{ log.phase_from }} → {{ log.phase_to }}
                </span>
                <span v-else-if="log.reason" class="text-xs text-muted-foreground">{{ log.reason }}</span>
              </td>
              <td class="px-4 py-3 capitalize text-xs text-muted-foreground">{{ log.triggered_by }}</td>
            </tr>
            <tr v-if="!logs.length && !loading" class="border-b border-border">
              <td colspan="5" class="px-4 py-8 text-center text-muted-foreground">
                Sin registros en este período
              </td>
            </tr>
            <tr v-if="loading">
              <td colspan="5" class="px-4 py-4 text-center">⏳ Cargando...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination && pagination.total > 0" class="mt-4 flex items-center justify-between text-sm text-muted-foreground">
      <p>{{ pagination.count }} de {{ pagination.total }} registros</p>
      <div class="flex gap-2">
        <button @click="previousPage" :disabled="pagination.current_page === 1" class="px-3 py-1 border border-border rounded hover:bg-accent disabled:opacity-50">
          ← Anterior
        </button>
        <button @click="nextPage" :disabled="pagination.current_page * pagination.per_page >= pagination.total" class="px-3 py-1 border border-border rounded hover:bg-accent disabled:opacity-50">
          Siguiente →
        </button>
      </div>
    </div>

    <!-- Expanded Details -->
    <div v-if="expandedId" class="mt-4 bg-muted/50 rounded-lg p-4">
      <div v-for="log in logs" :key="log.id">
        <div v-if="log.id === expandedId">
          <h4 class="font-semibold mb-2">Detalles completos:</h4>
          <pre class="text-xs bg-card p-3 rounded overflow-auto max-h-64">{{ JSON.stringify(log, null, 2) }}</pre>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'

interface AuditLog {
  id: number
  action: string
  user?: { name: string }
  phase_from?: string
  phase_to?: string
  reason?: string
  triggered_by: string
  created_at: string
}

const logs = ref<AuditLog[]>([])
const loading = ref(false)
const pagination = ref(null)
const expandedId = ref<number | null>(null)
const exportFormat = ref('json')
const currentPage = ref(1)

const today = new Date().toISOString().split('T')[0]
const filters = ref({
  action: '',
  dateFrom: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
  dateTo: today
})

const summary = ref(null)

const formatTime = (dateStr: string) => {
  const date = new Date(dateStr)
  return date.toLocaleString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getActionBadge = (action: string): string => {
  const badges: Record<string, string> = {
    'phase_transition': 'bg-blue-500/10 text-blue-700 dark:text-blue-400 px-2 py-1 rounded text-xs',
    'config_change': 'bg-green-500/10 text-green-700 dark:text-green-400 px-2 py-1 rounded text-xs',
    'manual_override': 'bg-orange-500/10 text-orange-700 dark:text-orange-400 px-2 py-1 rounded text-xs'
  }
  return badges[action] || 'bg-gray-500/10 px-2 py-1 rounded text-xs'
}

const fetchLogs = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams({
      limit: '50',
      page: String(currentPage.value),
      date_from: filters.value.dateFrom,
      date_to: filters.value.dateTo,
      ...(filters.value.action && { action: filters.value.action })
    })

    const response = await fetch(`/api/deployment/verification/audit-logs?${params}`)
    const data = await response.json()
    logs.value = data.data
    pagination.value = data.pagination

    // Fetch summary
    const summaryResponse = await fetch(`/api/deployment/verification/compliance-report?date_from=${filters.value.dateFrom}&date_to=${filters.value.dateTo}`)
    const summaryData = await summaryResponse.json()
    summary.value = summaryData.data.summary
  } catch (error) {
    console.error('Failed to fetch audit logs:', error)
  } finally {
    loading.value = false
  }
}

const downloadReport = () => {
  if (!logs.value.length) return

  const data = {
    summary: summary.value,
    events: logs.value,
    exported_at: new Date().toISOString()
  }

  const content = exportFormat.value === 'json' 
    ? JSON.stringify(data, null, 2)
    : convertToCSV(data.events)

  const blob = new Blob([content], { type: exportFormat.value === 'json' ? 'application/json' : 'text/csv' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `audit-report.${exportFormat.value}`
  link.click()
}

const convertToCSV = (data: any[]) => {
  const headers = ['Timestamp', 'Action', 'User', 'Phase From', 'Phase To', 'Triggered By', 'Reason']
  const rows = data.map(log => [
    log.created_at,
    log.action,
    log.user?.name || 'System',
    log.phase_from || '',
    log.phase_to || '',
    log.triggered_by,
    log.reason || ''
  ])

  return [
    headers.join(','),
    ...rows.map(row => row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(','))
  ].join('\n')
}

const nextPage = () => {
  currentPage.value++
  fetchLogs()
}

const previousPage = () => {
  currentPage.value--
  fetchLogs()
}

onMounted(() => {
  fetchLogs()
})

watch(() => [filters.value.action], () => {
  currentPage.value = 1
  fetchLogs()
})
</script>
