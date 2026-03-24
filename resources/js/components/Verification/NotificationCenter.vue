<template>
  <div class="notification-center">
    <!-- Filters -->
    <div class="flex gap-3 mb-4">
      <select v-model="filters.type" class="px-3 py-2 border border-border rounded-lg text-sm bg-card">
        <option value="">Todos los tipos</option>
        <option value="phase_transition">Transición de fase</option>
        <option value="alert_threshold">Alerta de umbral</option>
        <option value="violation_detected">Violación detectada</option>
      </select>
      <select v-model="filters.severity" class="px-3 py-2 border border-border rounded-lg text-sm bg-card">
        <option value="">Todas las severidades</option>
        <option value="info">ℹ️ Información</option>
        <option value="warning">⚠️ Advertencia</option>
        <option value="critical">🔴 Crítico</option>
      </select>
      <select v-model="filters.read" class="px-3 py-2 border border-border rounded-lg text-sm bg-card">
        <option value="">Todos</option>
        <option value="unread">📬 No leídas</option>
        <option value="read">📭 Leídas</option>
      </select>
      <button @click="markAllAsRead" class="px-3 py-2 text-sm border border-border rounded-lg hover:bg-accent transition-colors">
        ✓ Marcar como leídas
      </button>
    </div>

    <!-- Notifications List -->
    <div class="space-y-2">
      <div 
        v-for="notif in notifications" 
        :key="notif.id"
        @click="toggleNotification(notif.id)"
        class="bg-card border border-border rounded-lg p-4 cursor-pointer hover:border-primary/50 transition-colors"
        :class="{ 'bg-muted/50': notif.read_at }"
      >
        <div class="flex items-start justify-between">
          <div class="flex items-start gap-3 flex-1">
            <!-- Severity Icon -->
            <div class="text-xl mt-1">
              <span v-if="notif.severity === 'info'">ℹ️</span>
              <span v-else-if="notif.severity === 'warning'">⚠️</span>
              <span v-else-if="notif.severity === 'critical'">🔴</span>
            </div>
            
            <!-- Content -->
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <h4 class="font-semibold capitalize">{{ notif.type.replace(/_/g, ' ') }}</h4>
                <span v-if="!notif.read_at" class="w-2 h-2 bg-primary rounded-full"></span>
              </div>
              <p class="text-sm text-muted-foreground mt-1">{{ notif.data.message || notif.data.title }}</p>
              <p class="text-xs text-muted-foreground mt-2">{{ formatTime(notif.created_at) }}</p>
            </div>
          </div>

          <!-- Status -->
          <div class="text-sm text-muted-foreground">
            {{ notif.read_at ? '✓' : '●' }}
          </div>
        </div>

        <!-- Expandable Details -->
        <div v-if="expandedId === notif.id" class="mt-3 pt-3 border-t border-border">
          <pre class="text-xs bg-muted/50 p-2 rounded overflow-auto max-h-40">{{ JSON.stringify(notif.data, null, 2) }}</pre>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && !notifications.length" class="text-center py-8 text-muted-foreground">
        <p class="text-sm">No hay notificaciones</p>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-4">
        <div class="animate-spin">⏳</div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination && pagination.total > 0" class="mt-4 flex items-center justify-between text-sm text-muted-foreground">
      <p>{{ pagination.count }} de {{ pagination.total }} notificaciones</p>
      <div class="flex gap-2">
        <button 
          @click="previousPage"
          :disabled="pagination.current_page === 1"
          class="px-3 py-1 border border-border rounded hover:bg-accent disabled:opacity-50 disabled:cursor-not-allowed"
        >
          ← Anterior
        </button>
        <button 
          @click="nextPage"
          :disabled="pagination.current_page * pagination.per_page >= pagination.total"
          class="px-3 py-1 border border-border rounded hover:bg-accent disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Siguiente →
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

interface Notification {
  id: number
  type: string
  severity: string
  message: string
  data: Record<string, any>
  read_at?: string
  created_at: string
}

interface Pagination {
  total: number
  count: number
  per_page: number
  current_page: number
}

const notifications = ref<Notification[]>([])
const pagination = ref<Pagination | null>(null)
const loading = ref(false)
const expandedId = ref<number | null>(null)
const currentPage = ref(1)

const filters = ref({
  type: '',
  severity: '',
  read: ''
})

const formatTime = (dateStr: string) => {
  const date = new Date(dateStr)
  const now = new Date()
  const diffMinutes = Math.floor((now.getTime() - date.getTime()) / 60000)
  
  if (diffMinutes < 1) return 'Hace poco'
  if (diffMinutes < 60) return `Hace ${diffMinutes}m`
  
  const diffHours = Math.floor(diffMinutes / 60)
  if (diffHours < 24) return `Hace ${diffHours}h`
  
  return date.toLocaleString('es-ES', { 
    month: 'short', 
    day: 'numeric', 
    hour: '2-digit', 
    minute: '2-digit' 
  })
}

const fetchNotifications = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams({
      limit: '20',
      page: String(currentPage.value),
      ...(filters.value.type && { type: filters.value.type }),
      ...(filters.value.severity && { severity: filters.value.severity }),
      ...(filters.value.read && { read: filters.value.read })
    })
    
    const response = await fetch(`/api/deployment/verification/notifications?${params}`)
    const data = await response.json()
    notifications.value = data.data
    pagination.value = data.pagination
  } catch (error) {
    console.error('Failed to fetch notifications:', error)
  } finally {
    loading.value = false
  }
}

const toggleNotification = (id: number) => {
  expandedId.value = expandedId.value === id ? null : id
}

const markAllAsRead = async () => {
  // This would need a new endpoint
  console.log('Mark all as read (not yet implemented)')
}

const nextPage = () => {
  currentPage.value++
  fetchNotifications()
}

const previousPage = () => {
  currentPage.value--
  fetchNotifications()
}

onMounted(() => {
  fetchNotifications()
})

// Watch for filter changes
import { watch } from 'vue'
watch(() => [filters.value.type, filters.value.severity, filters.value.read], () => {
  currentPage.value = 1
  fetchNotifications()
})
</script>
