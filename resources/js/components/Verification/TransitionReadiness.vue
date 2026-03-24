<template>
  <div class="transition-readiness">
    <div class="grid grid-cols-2 gap-4 mb-6">
      <!-- Current Phase -->
      <div class="bg-card border border-border rounded-lg p-4">
        <h3 class="text-xs text-muted-foreground uppercase tracking-wide mb-3">Fase Actual</h3>
        <div class="text-3xl font-bold capitalize">{{ currentPhase }}</div>
        <p class="text-sm text-muted-foreground mt-2">{{ phaseDescription }}</p>
      </div>

      <!-- Days Until Transition -->
      <div class="bg-card border border-border rounded-lg p-4">
        <h3 class="text-xs text-muted-foreground uppercase tracking-wide mb-3">Estimado hasta próxima transición</h3>
        <div class="text-3xl font-bold">{{ daysUntilTransition }}</div>
        <p class="text-sm text-muted-foreground mt-2">{{ readinessText }}</p>
      </div>
    </div>

    <!-- Readiness Indicators -->
    <div class="bg-card border border-border rounded-lg p-4">
      <h3 class="font-semibold mb-4">📊 Métricas vs Umbrales</h3>

      <!-- Confidence Score -->
      <div class="mb-4">
        <div class="flex items-center justify-between mb-2">
          <label class="text-sm">Confianza</label>
          <span class="text-sm font-semibold">{{ metrics.confidence }}% / {{ thresholds.confidence }}%</span>
        </div>
        <div class="w-full bg-muted rounded-full h-2 overflow-hidden">
          <div 
            class="h-full bg-gradient-to-r from-red-500 to-green-500 transition-all"
            :style="{ width: Math.min(100, (metrics.confidence / thresholds.confidence) * 100) + '%' }"
          />
        </div>
        <p class="text-xs text-muted-foreground mt-1">
          {{ metricsStatus.confidence }}
        </p>
      </div>

      <!-- Error Rate -->
      <div class="mb-4">
        <div class="flex items-center justify-between mb-2">
          <label class="text-sm">Tasa de Error</label>
          <span class="text-sm font-semibold">{{ metrics.error_rate }}% / {{ thresholds.error_rate }}%</span>
        </div>
        <div class="w-full bg-muted rounded-full h-2 overflow-hidden">
          <div 
            class="h-full bg-gradient-to-r from-green-500 to-red-500 transition-all"
            :style="{ width: Math.min(100, (metrics.error_rate / thresholds.error_rate) * 100) + '%' }"
          />
        </div>
        <p class="text-xs text-muted-foreground mt-1">
          {{ metricsStatus.error_rate }}
        </p>
      </div>

      <!-- Retry Rate -->
      <div class="mb-4">
        <div class="flex items-center justify-between mb-2">
          <label class="text-sm">Tasa de Reintentos</label>
          <span class="text-sm font-semibold">{{ metrics.retry_rate }}% / {{ thresholds.retry_rate }}%</span>
        </div>
        <div class="w-full bg-muted rounded-full h-2 overflow-hidden">
          <div 
            class="h-full bg-gradient-to-r from-green-500 to-red-500 transition-all"
            :style="{ width: Math.min(100, (metrics.retry_rate / thresholds.retry_rate) * 100) + '%' }"
          />
        </div>
        <p class="text-xs text-muted-foreground mt-1">
          {{ metricsStatus.retry_rate }}
        </p>
      </div>

      <!-- Overall Status -->
      <div class="mt-4 pt-4 border-t border-border">
        <div class="flex items-center justify-between">
          <span class="text-sm font-semibold">¿Listo para transicionar?</span>
          <div class="flex items-center gap-2">
            <div v-if="canTransition" class="text-2xl">✅</div>
            <div v-else class="text-2xl">❌</div>
            <span class="font-semibold">{{ canTransition ? 'SÍ' : 'NO' }}</span>
          </div>
        </div>
        <p class="text-xs text-muted-foreground mt-2">{{ overallReason }}</p>
      </div>
    </div>

    <!-- Sample Size Warning -->
    <div v-if="metrics.sample_size < 100" class="mt-4 p-3 bg-yellow-500/10 border border-yellow-500/20 rounded text-sm text-yellow-700 dark:text-yellow-400">
      ⚠️ <strong>Tamaño de muestra insuficiente:</strong> {{ metrics.sample_size }} verificaciones. Se requieren 100 para evaluar transiciones.
    </div>

    <!-- Recent Transitions -->
    <div v-if="recentTransitions.length" class="mt-6 bg-card border border-border rounded-lg p-4">
      <h3 class="font-semibold mb-4">📋 Últimas Transiciones</h3>
      <div class="space-y-2">
        <div v-for="(trans, idx) in recentTransitions" :key="idx" class="flex items-center justify-between p-2 bg-muted/50 rounded text-sm">
          <div>
            <span class="capitalize">{{ trans.from_phase }}</span>
            <span class="mx-2">→</span>
            <span class="capitalize font-semibold">{{ trans.to_phase }}</span>
          </div>
          <div class="text-xs text-muted-foreground">{{ formatTime(trans.timestamp) }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

const currentPhase = ref('silent')
const metrics = ref({
  confidence: 78,
  error_rate: 25,
  retry_rate: 15,
  sample_size: 2450
})
const thresholds = ref({
  confidence: 90,
  error_rate: 40,
  retry_rate: 20
})
const recentTransitions = ref<Array<{ from_phase: string; to_phase: string; timestamp: string }>>([])

const phaseDescription = computed(() => {
  const desc: Record<string, string> = {
    silent: 'Modo silencioso - verifications but no flags',
    flagging: 'Verifications are flagged for review',
    reject: 'Verifications are rejected',
    tuning: 'Fine-tuning mode active'
  }
  return desc[currentPhase.value] || ''
})

const daysUntilTransition = computed(() => {
  const missingConfidence = Math.max(0, thresholds.value.confidence - metrics.value.confidence)
  const improvementRate = 0.5 // 0.5% improvement per day (example)
  
  if (missingConfidence <= 0) return 'Listo'
  const days = Math.ceil(missingConfidence / improvementRate)
  return `${Math.min(days, 999)} días`
})

const readinessText = computed(() => {
  if (canTransition.value) return '✅ Cumple todos los criterios'
  if (metrics.value.sample_size < 100) return '⏳ Recolectando datos...'
  return '⏳ Mejorando métricas...'
})

const metricsStatus = computed(() => ({
  confidence: metrics.value.confidence >= thresholds.value.confidence 
    ? `✅ ${metrics.value.confidence}% cumple el mínimo de ${thresholds.value.confidence}%`
    : `❌ ${metrics.value.confidence}% está por debajo de ${thresholds.value.confidence}% (faltan ${thresholds.value.confidence - metrics.value.confidence}%)`,
  error_rate: metrics.value.error_rate <= thresholds.value.error_rate
    ? `✅ ${metrics.value.error_rate}% está dentro del ${thresholds.value.error_rate}% permitido`
    : `❌ ${metrics.value.error_rate}% excede el ${thresholds.value.error_rate}% permitido`,
  retry_rate: metrics.value.retry_rate <= thresholds.value.retry_rate
    ? `✅ ${metrics.value.retry_rate}% está dentro del ${thresholds.value.retry_rate}% permitido`
    : `❌ ${metrics.value.retry_rate}% excede el ${thresholds.value.retry_rate}% permitido`
}))

const canTransition = computed(() => {
  return metrics.value.confidence >= thresholds.value.confidence &&
         metrics.value.error_rate <= thresholds.value.error_rate &&
         metrics.value.retry_rate <= thresholds.value.retry_rate &&
         metrics.value.sample_size >= 100
})

const overallReason = computed(() => {
  const issues = []
  
  if (metrics.value.sample_size < 100) {
    issues.push(`Solo ${metrics.value.sample_size}/100 verificaciones`)
  }
  if (metrics.value.confidence < thresholds.value.confidence) {
    issues.push(`Confianza baja (${metrics.value.confidence}%)`)
  }
  if (metrics.value.error_rate > thresholds.value.error_rate) {
    issues.push(`Tasa de error alta (${metrics.value.error_rate}%)`)
  }
  if (metrics.value.retry_rate > thresholds.value.retry_rate) {
    issues.push(`Tasa de reintentos alta (${metrics.value.retry_rate}%)`)
  }
  
  if (issues.length === 0) return 'Todas las métricas cumplen con los criterios de transición'
  return `Problemas: ${issues.join(', ')}`
})

const formatTime = (dateStr: string) => {
  const date = new Date(dateStr)
  return date.toLocaleString('es-ES', { 
    month: 'short', 
    day: 'numeric', 
    hour: '2-digit', 
    minute: '2-digit' 
  })
}

const fetchData = async () => {
  try {
    const response = await fetch('/api/deployment/verification/transitions?limit=5')
    const data = await response.json()
    recentTransitions.value = data.data
  } catch (error) {
    console.error('Failed to fetch transitions:', error)
  }
}

onMounted(() => {
  fetchData()
})
</script>
