<template>
  <div class="verification-scheduler">
    <!-- Scheduler Status Card -->
    <div class="grid grid-cols-3 gap-4 mb-6">
      <!-- Status -->
      <div class="bg-card rounded-lg p-4 border border-border">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-muted-foreground uppercase tracking-wide">Estado</p>
            <p class="text-lg font-semibold mt-1">
              {{ status.enabled ? '✅ Activo' : '⏸️ Inactivo' }}
            </p>
          </div>
          <div class="text-3xl">
            <PhosphorIcon :name="status.enabled ? 'play-circle' : 'pause-circle'" weight="fill" class="text-primary" />
          </div>
        </div>
      </div>

      <!-- Next Execution -->
      <div class="bg-card rounded-lg p-4 border border-border">
        <div>
          <p class="text-xs text-muted-foreground uppercase tracking-wide">Próxima ejecución</p>
          <p class="text-lg font-semibold mt-1">{{ nextExecutionIn }}</p>
          <p class="text-xs text-muted-foreground mt-2">{{ nextRunTime }}</p>
        </div>
      </div>

      <!-- Recent Status -->
      <div class="bg-card rounded-lg p-4 border border-border">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-muted-foreground uppercase tracking-wide">Última ejecución</p>
            <p class="text-lg font-semibold mt-1">{{ lastRunStatus }}</p>
            <p class="text-xs text-muted-foreground mt-2">{{ lastRunTime }}</p>
          </div>
          <div class="text-3xl" :class="lastRunStatusClass">
            <PhosphorIcon :name="lastRunIcon" weight="fill" />
          </div>
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-3 mb-6">
      <button 
        @click="triggerNow"
        :disabled="loading"
        class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
      >
        {{ loading ? 'Ejecutando...' : '🔄 Ejecutar Ahora' }}
      </button>
      <button 
        @click="toggleScheduler"
        :disabled="loading"
        class="px-4 py-2 border border-border rounded-lg hover:bg-accent transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {{ status.enabled ? '⏸️ Desactivar' : '▶️ Activar' }}
      </button>
    </div>

    <!-- Recent Executions -->
    <div class="bg-card rounded-lg border border-border p-4">
      <h3 class="font-semibold mb-4">Últimas 5 ejecuciones</h3>
      <div class="space-y-2">
        <div v-for="(exec, idx) in status.recent_executions" :key="idx" class="flex items-center justify-between p-3 bg-muted/50 rounded text-sm">
          <div class="flex items-center gap-2 flex-1">
            <span class="text-xs text-muted-foreground">{{ formatTime(exec.timestamp) }}</span>
            <span class="font-medium capitalize">{{ exec.status }}</span>
            <span v-if="exec.message" class="text-xs text-muted-foreground">{{ exec.message }}</span>
          </div>
          <div class="text-xs text-muted-foreground">
            {{ exec.transitions_evaluated }} evaluadas, {{ exec.transitions_executed }} ejecutadas
          </div>
        </div>
        <div v-if="!status.recent_executions?.length" class="text-center py-4 text-muted-foreground text-sm">
          Sin ejecuciones registradas
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import PhosphorIcon from '@/components/PhosphorIcon.vue'

interface StatusData {
  enabled: boolean
  mode: string
  last_run?: string
  last_run_status?: string
  next_run: string
  seconds_until_next: number
  recent_executions: Array<{
    timestamp: string
    status: string
    transitions_evaluated: number
    transitions_executed: number
    message?: string
  }>
}

const loading = ref(false)
const status = ref<StatusData | null>(null)
let countdownInterval: number | null = null

const nextExecutionIn = computed(() => {
  if (!status.value) return '--'
  const seconds = status.value.seconds_until_next
  const minutes = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${minutes}m ${secs}s`
})

const nextRunTime = computed(() => {
  if (!status.value) return '--'
  return new Date(status.value.next_run).toLocaleString('es-ES', {
    hour: '2-digit',
    minute: '2-digit',
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
})

const lastRunTime = computed(() => {
  if (!status.value?.last_run) return 'Nunca'
  const date = new Date(status.value.last_run)
  const now = new Date()
  const diffMinutes = Math.floor((now.getTime() - date.getTime()) / 60000)
  
  if (diffMinutes < 1) return 'Hace poco'
  if (diffMinutes < 60) return `Hace ${diffMinutes}m`
  
  const diffHours = Math.floor(diffMinutes / 60)
  if (diffHours < 24) return `Hace ${diffHours}h`
  
  return date.toLocaleString('es-ES', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
})

const lastRunStatus = computed(() => {
  if (!status.value?.last_run_status) return 'Sin info'
  const stat = status.value.last_run_status
  const map: Record<string, string> = {
    'success': '✅ Éxito',
    'failed': '❌ Error',
    'partial': '⚠️ Parcial',
    'skipped': '⏭️ Omitida'
  }
  return map[stat] || stat
})

const lastRunStatusClass = computed(() => {
  const stat = status.value?.last_run_status
  return {
    'text-green-500': stat === 'success',
    'text-red-500': stat === 'failed',
    'text-yellow-500': stat === 'partial',
    'text-gray-500': stat === 'skipped' || !stat
  }
})

const lastRunIcon = computed(() => {
  const stat = status.value?.last_run_status
  const map: Record<string, string> = {
    'success': 'check-circle',
    'failed': 'x-circle',
    'partial': 'warning',
    'skipped': 'pause-circle'
  }
  return map[stat] || 'question'
})

const formatTime = (dateStr: string) => {
  return new Date(dateStr).toLocaleTimeString('es-ES', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
}

const fetchStatus = async () => {
  try {
    const response = await fetch('/api/deployment/verification/scheduler-status')
    status.value = await response.json()
  } catch (error) {
    console.error('Failed to fetch scheduler status:', error)
  }
}

const triggerNow = async () => {
  loading.value = true
  try {
    const response = await fetch('/api/deployment/verification/process-transition', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    if (response.ok) {
      await fetchStatus()
    }
  } catch (error) {
    console.error('Failed to trigger scheduler:', error)
  } finally {
    loading.value = false
  }
}

const toggleScheduler = async () => {
  // This would need a new endpoint to enable/disable
  console.log('Toggle scheduler (not yet implemented)')
}

const startCountdown = () => {
  countdownInterval = window.setInterval(() => {
    if (status.value) {
      status.value.seconds_until_next = Math.max(0, status.value.seconds_until_next - 1)
      if (status.value.seconds_until_next === 0) {
        fetchStatus()
      }
    }
  }, 1000)
}

onMounted(() => {
  fetchStatus()
  startCountdown()
})

onBeforeUnmount(() => {
  if (countdownInterval) clearInterval(countdownInterval)
})
</script>
