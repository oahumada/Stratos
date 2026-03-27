<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
          Registro de Auditoría
        </h3>
        <p class="text-sm text-slate-500 dark:text-slate-400">
          Historial completo de cambios y operaciones
        </p>
      </div>

      <!-- Refresh Button -->
      <button
        @click="fetchLogs"
        :disabled="loading"
        class="inline-flex items-center gap-2 rounded-lg bg-indigo-500 px-3 py-2 text-sm font-medium text-white transition hover:bg-indigo-600 disabled:opacity-50"
      >
        <PhArrowClockwise :size="16" :class="{ 'animate-spin': loading }" />
        {{ loading ? 'Actualizando...' : 'Actualizar' }}
      </button>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-4">
      <div>
        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300">
          Acción
        </label>
        <select
          v-model="filters.action"
          @change="fetchLogs"
          class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-1 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white"
        >
          <option value="">Todas</option>
          <option value="created">Creado</option>
          <option value="updated">Actualizado</option>
          <option value="deleted">Eliminado</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300">
          Entidad
        </label>
        <select
          v-model="filters.entity_type"
          @change="fetchLogs"
          class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-1 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white"
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
          v-model.number="filters.days"
          @change="fetchLogs"
          class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-1 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white"
        >
          <option :value="1">Hoy</option>
          <option :value="7">Últimos 7 días</option>
          <option :value="30">Últimos 30 días</option>
          <option :value="90">Últimos 90 días</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-medium text-slate-700 dark:text-slate-300">
          Usuario
        </label>
        <select
          v-model.number="filters.user_id"
          @change="fetchLogs"
          class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-1 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-white"
        >
          <option :value="null">Todos</option>
          <option v-for="user in users" :key="user.id" :value="user.id">
            {{ user.name }}
          </option>
        </select>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
      <div
        class="rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800"
      >
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
          Total de eventos
        </p>
        <p class="mt-1 text-xl font-bold text-slate-900 dark:text-white">
          {{ stats.total_events || 0 }}
        </p>
      </div>

      <div
        class="rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800"
      >
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
          Creados
        </p>
        <p class="mt-1 text-xl font-bold text-green-600 dark:text-green-400">
          {{ stats.creates || 0 }}
        </p>
      </div>

      <div
        class="rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800"
      >
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
          Actualizados
        </p>
        <p class="mt-1 text-xl font-bold text-blue-600 dark:text-blue-400">
          {{ stats.updates || 0 }}
        </p>
      </div>

      <div
        class="rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800"
      >
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
          Eliminados
        </p>
        <p class="mt-1 text-xl font-bold text-red-600 dark:text-red-400">
          {{ stats.deletes || 0 }}
        </p>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
            <th
              class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300"
            >
              Timestamp
            </th>
            <th
              class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300"
            >
              Acción
            </th>
            <th
              class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300"
            >
              Entidad
            </th>
            <th
              class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300"
            >
              Usuario
            </th>
            <th
              class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-slate-300"
            >
              Cambios
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-if="!loading && logs.length === 0"
            class="border-b border-slate-200 dark:border-slate-700"
          >
            <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
              Sin registros en este período
            </td>
          </tr>

          <tr
            v-for="log in logs"
            :key="log.id"
            class="border-b border-slate-200 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800"
          >
            <!-- Timestamp -->
            <td class="whitespace-nowrap px-4 py-3">
              <span class="text-xs text-slate-600 dark:text-slate-300">
                {{ formatDate(log.created_at) }}
              </span>
            </td>

            <!-- Action Badge -->
            <td class="px-4 py-3">
              <span
                :class="getActionBadgeClass(log.action)"
                class="inline-block rounded-full px-2 py-1 text-xs font-semibold"
              >
                {{ getActionLabel(log.action) }}
              </span>
            </td>

            <!-- Entity Type -->
            <td class="px-4 py-3">
              <span class="text-slate-700 dark:text-slate-300">
                {{ log.entity_type }}
              </span>
            </td>

            <!-- User -->
            <td class="px-4 py-3">
              <span class="text-slate-600 dark:text-slate-400">
                {{ log.user?.name || 'Sistema' }}
              </span>
            </td>

            <!-- Changes Summary -->
            <td class="px-4 py-3">
              <span
                v-if="log.changes"
                class="text-xs text-slate-600 dark:text-slate-400"
              >
                {{ getChangeSummary(log.changes) }}
              </span>
              <span v-else class="text-xs text-slate-400">—</span>
            </td>
          </tr>

          <tr v-if="loading">
            <td colspan="5" class="px-4 py-4 text-center">
              <span class="text-slate-600 dark:text-slate-400">⏳ Cargando...</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="pagination" class="flex items-center justify-between border-t border-slate-200 pt-4 dark:border-slate-700">
      <span class="text-xs text-slate-600 dark:text-slate-400">
        Página {{ pagination.current_page }} de {{ pagination.last_page }}
        ({{ pagination.total }} total)
      </span>
      <div class="flex gap-2">
        <button
          v-if="pagination.current_page > 1"
          @click="goToPage(pagination.current_page - 1)"
          class="rounded-lg border border-slate-300 px-3 py-1 text-xs font-medium transition hover:bg-slate-100 dark:border-slate-600 dark:hover:bg-slate-700"
        >
          Anterior
        </button>
        <button
          v-if="pagination.current_page < pagination.last_page"
          @click="goToPage(pagination.current_page + 1)"
          class="rounded-lg border border-slate-300 px-3 py-1 text-xs font-medium transition hover:bg-slate-100 dark:border-slate-600 dark:hover:bg-slate-700"
        >
          Siguiente
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { PhArrowClockwise } from '@phosphor-icons/vue'

const logs = ref([])
const stats = ref({})
const loading = ref(false)
const pagination = ref(null)
const users = ref([])

const filters = ref({
  action: '',
  entity_type: '',
  user_id: null,
  days: 7,
})

const currentPage = ref(1)

async function fetchLogs() {
  loading.value = true
  try {
    const params = new URLSearchParams({
      action: filters.value.action || '',
      entity_type: filters.value.entity_type || '',
      user_id: filters.value.user_id || '',
      days: filters.value.days,
      page: currentPage.value,
    })

    const response = await fetch(`/api/audit-logs?${params}`)
    const data = await response.json()

    logs.value = data.data || []
    pagination.value = data.pagination
    stats.value = data.stats || {}
  } catch (error) {
    console.error('Error fetching logs:', error)
  } finally {
    loading.value = false
  }
}

function goToPage(page) {
  currentPage.value = page
  fetchLogs()
}

function formatDate(date) {
  return new Date(date).toLocaleString('es-ES', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
  })
}

function getActionLabel(action) {
  const labels = {
    created: 'Creado',
    updated: 'Actualizado',
    deleted: 'Eliminado',
  }
  return labels[action] || action
}

function getActionBadgeClass(action) {
  const classes = {
    created: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    updated: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    deleted: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
  }
  return classes[action] || 'bg-slate-100 text-slate-800 dark:bg-slate-900 dark:text-slate-200'
}

function getChangeSummary(changes) {
  if (!changes || Object.keys(changes).length === 0) return '—'

  const entries = Object.entries(changes).slice(0, 2)
  return entries
    .map(([field, [oldVal, newVal]]) => `${field}: ${oldVal} → ${newVal}`)
    .join('; ')
    .substring(0, 60) + '...'
}

onMounted(() => {
  // Load users for filter
  fetch('/api/users')
    .then((r) => r.json())
    .then((data) => {
      users.value = data.data || []
    })
    .catch(() => {})

  fetchLogs()
})
</script>
