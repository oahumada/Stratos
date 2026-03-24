<template>
  <div class="dry-run-simulator">
    <!-- Current State -->
    <div class="grid grid-cols-3 gap-4 mb-6">
      <div class="bg-card border border-border rounded-lg p-4">
        <p class="text-xs text-muted-foreground mb-2">Fase actual</p>
        <p class="text-3xl font-bold mb-2">{{ currentPhase }}</p>
        <p class="text-xs text-muted-foreground">Sin cambios realizados</p>
      </div>

      <div class="bg-card border border-border rounded-lg p-4 flex items-center justify-center">
        <div class="text-4xl">{{ simulation?.would_transition ? '✓' : '✗' }}</div>
      </div>

      <div v-if="simulation" class="bg-card border border-border rounded-lg p-4">
        <p class="text-xs text-muted-foreground mb-2">Próxima fase</p>
        <p class="text-3xl font-bold mb-2">{{ simulation.next_phase || '-' }}</p>
        <p class="text-xs text-muted-foreground">{{ simulation.would_transition ? 'Transición posible' : 'No cumple criterios' }}</p>
      </div>
    </div>

    <!-- Current Metrics -->
    <div class="bg-card border border-border rounded-lg p-4 mb-6">
      <h3 class="font-semibold mb-4">Métricas actuales</h3>
      <div class="grid grid-cols-4 gap-4">
        <div v-if="simulation && simulation.metrics" v-for="(value, key) in simulation.metrics" :key="key">
          <div class="flex justify-between items-center mb-2">
            <label class="text-sm capitalize">{{ formatLabel(key) }}</label>
            <span class="font-bold">{{ value === 'N/A' ? 'N/A' : (typeof value === 'number' ? value.toFixed(2) : value) }}</span>
          </div>
          <div class="w-full bg-muted rounded-full h-2">
            <div class="bg-primary h-2 rounded-full" :style="{ width: calculateMetricWidth(key, value) }"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Threshold Adjusters -->
    <div class="bg-card border border-border rounded-lg p-4 mb-6">
      <h3 class="font-semibold mb-4">Ajustar umbrales (Para simular)</h3>
      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="text-sm block mb-2">
            Confianza mínima: <strong>{{ adjustedThresholds.confidence_threshold }}%</strong>
          </label>
          <input 
            v-model.number="adjustedThresholds.confidence_threshold"
            type="range"
            min="0"
            max="100"
            step="5"
            class="w-full"
          />
        </div>

        <div>
          <label class="text-sm block mb-2">
            Tasa de error máxima: <strong>{{ adjustedThresholds.error_rate_threshold }}%</strong>
          </label>
          <input 
            v-model.number="adjustedThresholds.error_rate_threshold"
            type="range"
            min="0"
            max="50"
            step="2"
            class="w-full"
          />
        </div>

        <div>
          <label class="text-sm block mb-2">
            Tasa de reintentos máxima: <strong>{{ adjustedThresholds.retry_rate_threshold }}%</strong>
          </label>
          <input 
            v-model.number="adjustedThresholds.retry_rate_threshold"
            type="range"
            min="0"
            max="50"
            step="2"
            class="w-full"
          />
        </div>
      </div>

      <div class="flex gap-3 mt-4">
        <button @click="runSimulation" :disabled="loading" class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 disabled:opacity-50">
          {{ loading ? '⏳ Simulando...' : '▶️ Ejecutar simulación' }}
        </button>
        <button @click="resetThresholds" class="px-4 py-2 border border-border rounded-lg hover:bg-accent">
          🔄 Restablecer
        </button>
      </div>
    </div>

    <!-- Gap Analysis -->
    <div v-if="simulation && simulation.gaps && simulation.gaps.length > 0" class="bg-orange-500/5 border border-orange-500/20 rounded-lg p-4 mb-6">
      <h3 class="font-semibold mb-4 text-orange-700 dark:text-orange-400">⚠️ Áreas de mejora necesarias</h3>
      <div class="space-y-3">
        <div v-for="(gap, idx) in simulation.gaps" :key="idx" class="bg-card border border-orange-500/20 rounded p-3">
          <div class="flex justify-between items-start mb-1">
            <span class="font-semibold capitalize">{{ gap.metric }}</span>
            <span class="text-xs text-muted-foreground">{{ gap.days_to_meet || 'N/A' }} días estimados</span>
          </div>
          <p class="text-sm text-muted-foreground mb-2">{{ gap.reason }}</p>
          <div class="text-xs">
            <span class="bg-orange-100 dark:bg-orange-900/30 px-2 py-1 rounded">
              Actual: {{ gap.current_value ? gap.current_value.toFixed(2) : 'N/A' }} | Requerido: {{ gap.required_value?.toFixed(2) || 'N/A' }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Transition Details -->
    <div v-if="simulation" class="bg-card border border-border rounded-lg p-4 mb-6">
      <h3 class="font-semibold mb-4">Resultado de la simulación</h3>
      <div class="space-y-3">
        <div class="flex justify-between p-2 bg-muted/50 rounded">
          <span>Estado de transición:</span>
          <strong :class="simulation.would_transition ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
            {{ simulation.would_transition ? '✓ Posible' : '✗ No disponible' }}
          </strong>
        </div>
        <div class="flex justify-between p-2 bg-muted/50 rounded">
          <span>Razón:</span>
          <span class="text-sm text-right">{{ simulation.reason }}</span>
        </div>
        <div class="flex justify-between p-2 bg-muted/50 rounded">
          <span>Resumen:</span>
          <span class="text-sm text-right">{{ simulation.summary }}</span>
        </div>
      </div>
    </div>

    <!-- Export Simulation -->
    <div v-if="simulation" class="flex gap-2">
      <button @click="downloadSimulationReport('json')" class="px-4 py-2 border border-border rounded-lg hover:bg-accent">
        📋 JSON
      </button>
      <button @click="downloadSimulationReport('pdf')" class="px-4 py-2 border border-border rounded-lg hover:bg-accent">
        📄 PDF
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

interface SimulationResult {
  current_phase: string
  would_transition: boolean
  next_phase: string | null
  reason: string
  summary: string
  metrics: Record<string, number | string>
  thresholds: Record<string, number>
  gaps: Array<{
    metric: string
    reason: string
    current_value?: number
    required_value?: number
    days_to_meet?: number
  }>
}

const currentPhase = ref('---')
const simulation = ref<SimulationResult | null>(null)
const loading = ref(false)

const adjustedThresholds = ref({
  confidence_threshold: 90,
  error_rate_threshold: 40,
  retry_rate_threshold: 20
})

const defaultThresholds = {
  confidence_threshold: 90,
  error_rate_threshold: 40,
  retry_rate_threshold: 20
}

const formatLabel = (key: string) => {
  const labels: Record<string, string> = {
    'confidence': 'Confianza',
    'error_rate': 'Tasa error',
    'retry_rate': 'Tasa reintentos',
    'sample_size': 'Tamaño muestra'
  }
  return labels[key] || key.replace(/_/g, ' ')
}

const calculateMetricWidth = (key: string, value: any): string => {
  if (value === 'N/A' || typeof value !== 'number') return '0%'
  
  const maxValues: Record<string, number> = {
    'confidence': 100,
    'error_rate': 50,
    'retry_rate': 50,
    'sample_size': 1000
  }
  
  const max = maxValues[key] || 100
  return `${Math.min((value / max) * 100, 100)}%`
}

const fetchCurrentMetrics = async () => {
  try {
    const response = await fetch('/api/deployment/verification/scheduler-status')
    const data = await response.json()
    // Extract phase from metrics if available
    if (data.current_phase) {
      currentPhase.value = data.current_phase
    }
  } catch (error) {
    console.error('Failed to fetch current metrics:', error)
  }
}

const runSimulation = async () => {
  loading.value = true
  try {
    const response = await fetch('/api/deployment/verification/dry-run', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({
        confidence_threshold: adjustedThresholds.value.confidence_threshold,
        error_rate_threshold: adjustedThresholds.value.error_rate_threshold,
        retry_rate_threshold: adjustedThresholds.value.retry_rate_threshold
      })
    })

    const data = await response.json()
    simulation.value = data.data
    currentPhase.value = data.data.current_phase
  } catch (error) {
    console.error('Failed to run simulation:', error)
  } finally {
    loading.value = false
  }
}

const resetThresholds = () => {
  adjustedThresholds.value = { ...defaultThresholds }
  simulation.value = null
}

const downloadSimulationReport = (format: 'json' | 'pdf') => {
  if (!simulation.value) return

  const reportData = {
    timestamp: new Date().toISOString(),
    current_phase: simulation.value.current_phase,
    would_transition: simulation.value.would_transition,
    next_phase: simulation.value.next_phase,
    reason: simulation.value.reason,
    summary: simulation.value.summary,
    metrics: simulation.value.metrics,
    thresholds: adjustedThresholds.value,
    gaps: simulation.value.gaps
  }

  if (format === 'json') {
    const blob = new Blob([JSON.stringify(reportData, null, 2)], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `dry-run-report-${new Date().toISOString().split('T')[0]}.json`
    link.click()
  } else if (format === 'pdf') {
    // Basic PDF generation - in production use jsPDF library
    const content = `
Reporte de Simulación - ${new Date().toLocaleDateString('es-ES')}
═════════════════════════════════════════

Fase Actual: ${simulation.value.current_phase}
¿Transición Posible?: ${simulation.value.would_transition ? 'Sí' : 'No'}
Próxima Fase: ${simulation.value.next_phase || '---'}

Resumen: ${simulation.value.summary}
Razón: ${simulation.value.reason}

Métricas:
${Object.entries(simulation.value.metrics).map(([k, v]) => `  ${k}: ${v}`).join('\n')}

Umbrales Ajustados:
  Confianza: ${adjustedThresholds.value.confidence_threshold}%
  Error: ${adjustedThresholds.value.error_rate_threshold}%
  Reintentos: ${adjustedThresholds.value.retry_rate_threshold}%

Áreas de Mejora:
${simulation.value.gaps.map(gap => `  - ${gap.metric}: ${gap.reason}`).join('\n')}
    `

    const blob = new Blob([content], { type: 'text/plain' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = `dry-run-report-${new Date().toISOString().split('T')[0]}.txt`
    link.click()
  }
}

onMounted(async () => {
  await fetchCurrentMetrics()
  // Auto-run initial simulation
  await runSimulation()
})
</script>
