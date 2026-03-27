<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
          Mapa de Actividad
        </h3>
        <p class="text-sm text-slate-500 dark:text-slate-400">
          Visualización de patrones de actividad
        </p>
      </div>

      <!-- Refresh Button -->
      <button
        @click="fetchHeatmap"
        :disabled="loading"
        class="inline-flex items-center gap-2 rounded-lg bg-indigo-500 px-3 py-2 text-sm font-medium text-white transition hover:bg-indigo-600 disabled:opacity-50"
      >
        <PhArrowClockwise :size="16" :class="{ 'animate-spin': loading }" />
        {{ loading ? 'Actualizando...' : 'Actualizar' }}
      </button>
    </div>

    <!-- Hour of Day Heatmap -->
    <div class="space-y-2">
      <h4 class="text-sm font-semibold text-slate-900 dark:text-white">
        Actividad por hora del día (últimos 7 días)
      </h4>
      <div class="overflow-x-auto">
        <div v-if="heatmap.length > 0" class="flex gap-1 pb-4">
          <div
            v-for="(count, hour) in heatmap"
            :key="hour"
            class="flex flex-col items-center"
          >
            <div
              :style="{ backgroundColor: getHeatColor(count, maxHeat) }"
              class="h-12 w-6 rounded transition-all hover:ring-2 hover:ring-indigo-400 dark:ring-offset-slate-800"
              :title="`${hour}:00 - ${count} eventos`"
            />
            <span class="mt-1 text-xs text-slate-600 dark:text-slate-400">
              {{hour}}h
            </span>
          </div>
        </div>
        <div
          v-else
          class="flex items-center justify-center rounded-lg border border-slate-200 bg-slate-50 p-8 dark:border-slate-700 dark:bg-slate-800"
        >
          <p class="text-slate-500 dark:text-slate-400">Sin datos disponibles</p>
        </div>
      </div>
    </div>

    <!-- Trend Chart -->
    <div class="space-y-2">
      <h4 class="text-sm font-semibold text-slate-900 dark:text-white">
        Tasa diaria de actividad (últimos 30 días)
      </h4>
      <div
        class="rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800"
      >
        <div v-if="dailyActivity.length > 0" class="h-40">
          <svg
            viewBox="0 0 800 100"
            class="h-full w-full"
            preserveAspectRatio="none"
          >
            <!-- Y-axis lines -->
            <line
              x1="0"
              y1="25"
              x2="800"
              y2="25"
              stroke="currentColor"
              class="stroke-slate-200 dark:stroke-slate-700"
              stroke-dasharray="4"
            />
            <line
              x1="0"
              y1="50"
              x2="800"
              y2="50"
              stroke="currentColor"
              class="stroke-slate-200 dark:stroke-slate-700"
              stroke-dasharray="4"
            />
            <line
              x1="0"
              y1="75"
              x2="800"
              y2="75"
              stroke="currentColor"
              class="stroke-slate-200 dark:stroke-slate-700"
              stroke-dasharray="4"
            />

            <!-- Line chart -->
            <polyline
              :points="getLinePoints()"
              fill="none"
              stroke="currentColor"
              class="stroke-indigo-500"
              stroke-width="2"
              vector-effect="non-scaling-stroke"
            />

            <!-- Area under curve -->
            <polygon
              :points="getAreaPoints()"
              fill="currentColor"
              class="fill-indigo-500/10"
            />

            <!-- Data points -->
            <circle
              v-for="(point, index) in getChartPoints()"
              :key="index"
              :cx="point.x"
              :cy="point.y"
              r="3"
              class="fill-indigo-500 hover:fill-indigo-600"
            />
          </svg>
        </div>
        <div
          v-else
          class="flex items-center justify-center p-8"
        >
          <p class="text-slate-500 dark:text-slate-400">Sin datos disponibles</p>
        </div>
      </div>

      <!-- Legend -->
      <div class="flex items-center gap-4 text-xs">
        <div class="flex items-center gap-2">
          <div class="h-2 w-4 bg-indigo-500" />
          <span class="text-slate-600 dark:text-slate-400">Eventos por día</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="h-2 w-4 bg-indigo-500/10" />
          <span class="text-slate-600 dark:text-slate-400">Área de cobertura</span>
        </div>
      </div>
    </div>

    <!-- Activity Stats -->
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
      <div
        class="rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800"
      >
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
          Promedio por hora
        </p>
        <p class="mt-1 text-lg font-bold text-indigo-600 dark:text-indigo-400">
          {{ averagePerHour.toFixed(1) }}
        </p>
      </div>

      <div
        class="rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800"
      >
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
          Hora más activa
        </p>
        <p class="mt-1 text-lg font-bold text-slate-900 dark:text-white">
          {{ mostActiveHour }}h
        </p>
      </div>

      <div
        class="rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800"
      >
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
          Eventos en pico
        </p>
        <p class="mt-1 text-lg font-bold text-blue-600 dark:text-blue-400">
          {{ peakActivity || 0 }}
        </p>
      </div>

      <div
        class="rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800"
      >
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
          Total (7 días)
        </p>
        <p class="mt-1 text-lg font-bold text-slate-900 dark:text-white">
          {{ totalActivity }}
        </p>
      </div>
    </div>

    <!-- Color Scale Legend -->
    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800">
      <p class="mb-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
        Escala de color (eventos)
      </p>
      <div class="flex items-center gap-1">
        <div
          v-for="level in 5"
          :key="level"
          :style="{ backgroundColor: getHeatColor(level * (maxHeat / 5), maxHeat) }"
          class="h-6 flex-1 rounded border border-slate-200 dark:border-slate-600"
        />
      </div>
      <div class="mt-1 flex justify-between text-xs text-slate-600 dark:text-slate-400">
        <span>Bajo</span>
        <span>Medio</span>
        <span>Alto</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PhArrowClockwise } from '@phosphor-icons/vue'

const heatmap = ref([])
const dailyActivity = ref([])
const loading = ref(false)

const maxHeat = computed(() => Math.max(...heatmap.value, 1))
const totalActivity = computed(() => heatmap.value.reduce((a, b) => a + b, 0))
const averagePerHour = computed(() => totalActivity.value / 24)
const peakActivity = computed(() => Math.max(...heatmap.value, 0))
const mostActiveHour = computed(() => {
  const index = heatmap.value.indexOf(peakActivity.value)
  return index >= 0 ? index : 0
})

async function fetchHeatmap() {
  loading.value = true
  try {
    const response = await fetch('/api/audit-logs/heatmap')
    const data = await response.json()

    heatmap.value = data.hourly || []
    dailyActivity.value = data.daily || []
  } catch (error) {
    console.error('Error fetching heatmap:', error)
  } finally {
    loading.value = false
  }
}

function getHeatColor(value, max) {
  if (max === 0) max = 1
  const ratio = value / max

  // Color gradient: cyan → green → yellow → orange → red
  if (ratio < 0.2) return '#06b6d4'
  if (ratio < 0.4) return '#10b981'
  if (ratio < 0.6) return '#eab308'
  if (ratio < 0.8) return '#f97316'
  return '#ef4444'
}

function getLinePoints() {
  if (dailyActivity.value.length === 0) return ''

  const max = Math.max(...dailyActivity.value, 1)
  const points = dailyActivity.value.map((value, index) => {
    const x = (index / (dailyActivity.value.length - 1 || 1)) * 800
    const y = 100 - (value / max) * 80 - 10
    return `${x},${y}`
  })

  return points.join(' ')
}

function getAreaPoints() {
  if (dailyActivity.value.length === 0) return ''

  const max = Math.max(...dailyActivity.value, 1)
  let points = `0,100`

  dailyActivity.value.forEach((value, index) => {
    const x = (index / (dailyActivity.value.length - 1 || 1)) * 800
    const y = 100 - (value / max) * 80 - 10
    points += ` ${x},${y}`
  })

  points += ` 800,100`
  return points
}

function getChartPoints() {
  if (dailyActivity.value.length === 0) return []

  const max = Math.max(...dailyActivity.value, 1)
  return dailyActivity.value.map((value, index) => ({
    x: (index / (dailyActivity.value.length - 1 || 1)) * 800,
    y: 100 - (value / max) * 80 - 10,
  }))
}

onMounted(() => {
  fetchHeatmap()
})
</script>
