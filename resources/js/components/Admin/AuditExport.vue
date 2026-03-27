<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
          Exportar Auditoría
        </h3>
        <p class="text-sm text-slate-500 dark:text-slate-400">
          Descargar registros en formato CSV
        </p>
      </div>
    </div>

    <!-- Export Options -->
    <div class="space-y-4 rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800">
      <!-- Filters before export -->
      <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div>
          <label class="block text-xs font-medium text-slate-700 dark:text-slate-300">
            Tipo de Acción
          </label>
          <select
            v-model="exportFilters.action"
            class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-2 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white"
          >
            <option value="">Todas</option>
            <option value="created">Creado</option>
            <option value="updated">Actualizado</option>
            <option value="deleted">Eliminado</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-medium text-slate-700 dark:text-slate-300">
            Tipo de Entidad
          </label>
          <select
            v-model="exportFilters.entity_type"
            class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-2 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white"
          >
            <option value="">Todas</option>
            <option value="AlertThreshold">Umbral de Alerta</option>
            <option value="AlertHistory">Historial de Alertas</option>
            <option value="EscalationPolicy">Política de Escalada</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-medium text-slate-700 dark:text-slate-300">
            Período
          </label>
          <select
            v-model.number="exportFilters.days"
            class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-2 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white"
          >
            <option :value="7">Últimos 7 días</option>
            <option :value="30">Últimos 30 días</option>
            <option :value="90">Últimos 90 días</option>
            <option :value="365">Último año</option>
          </select>
        </div>
      </div>

      <!-- Export Button -->
      <div class="flex gap-2 pt-2">
        <button
          @click="exportCSV"
          :disabled="exporting"
          class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-600 disabled:opacity-50"
        >
          <PhDownload :size="18" :class="{ 'animate-spin': exporting }" />
          {{ exporting ? 'Exportando...' : 'Descargar CSV' }}
        </button>

        <button
          @click="copyToClipboard"
          :disabled="exporting"
          class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600"
        >
          <PhCopy :size="18" />
          Copiar al portapapeles
        </button>
      </div>

      <!-- Info -->
      <p class="text-xs text-slate-600 dark:text-slate-400">
        Se exportarán los registros con los filtros seleccionados.
        Máximo 10,000 registros por exportación.
      </p>
    </div>

    <!-- Preview -->
    <div v-if="csvPreview" class="space-y-2">
      <h4 class="text-sm font-semibold text-slate-900 dark:text-white">
        Vista previa (primeras líneas)
      </h4>
      <div
        class="max-h-64 overflow-auto rounded-lg border border-slate-200 bg-slate-50 p-3 font-mono text-xs dark:border-slate-700 dark:bg-slate-800"
      >
        <pre class="whitespace-pre-wrap break-words text-slate-700 dark:text-slate-300">{{ csvPreview }}</pre>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { PhDownload, PhCopy } from '@phosphor-icons/vue'

const exporting = ref(false)
const csvPreview = ref('')

const exportFilters = ref({
  action: '',
  entity_type: '',
  days: 30,
})

async function exportCSV() {
  exporting.value = true
  try {
    const params = new URLSearchParams({
      action: exportFilters.value.action || '',
      entity_type: exportFilters.value.entity_type || '',
      days: exportFilters.value.days,
      format: 'csv',
    })

    const response = await fetch(`/api/audit-logs/export?${params}`)
    const csvData = await response.text()

    // Show preview
    csvPreview.value = csvData.split('\n').slice(0, 10).join('\n')

    // Create download
    const blob = new Blob([csvData], { type: 'text/csv' })
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `audit-logs-${new Date().toISOString().split('T')[0]}.csv`
    link.click()
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Error exporting:', error)
    alert('Error al exportar los registros')
  } finally {
    exporting.value = false
  }
}

async function copyToClipboard() {
  if (!csvPreview.value) {
    alert('No hay datos para copiar. Ejecuta una exportación primero.')
    return
  }

  try {
    await navigator.clipboard.writeText(csvPreview.value)
    alert('¡Copiado al portapapeles!')
  } catch (error) {
    console.error('Error copying:', error)
  }
}
</script>
