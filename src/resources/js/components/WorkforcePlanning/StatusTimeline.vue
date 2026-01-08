<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import { useNotification } from '@/composables/useNotification'

interface Props {
  scenarioId: number
}

interface StatusEvent {
  id: number
  from_decision_status: string | null
  to_decision_status: string | null
  from_execution_status: string | null
  to_execution_status: string | null
  changed_by: {
    name: string
    email: string
  }
  notes: string | null
  created_at: string
}

const props = defineProps<Props>()

const api = useApi()
const { showError } = useNotification()

const loading = ref(false)
const events = ref<StatusEvent[]>([])
const showTimeline = ref(false)

const loadStatusEvents = async () => {
  loading.value = true
  try {
    const res = await api.get(`/api/v1/workforce-planning/scenarios/${props.scenarioId}`)
    events.value = res.data?.status_events || []
  } catch (error) {
    showError('Error al cargar historial de cambios')
  } finally {
    loading.value = false
  }
}

const openTimeline = () => {
  showTimeline.value = true
  if (events.value.length === 0) {
    loadStatusEvents()
  }
}

const sortedEvents = computed(() => {
  return [...events.value].sort((a, b) => 
    new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
  )
})

const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getEventIcon = (event: StatusEvent): string => {
  if (event.to_decision_status === 'approved') return 'mdi-check-circle'
  if (event.to_decision_status === 'rejected') return 'mdi-close-circle'
  if (event.to_decision_status === 'pending_approval') return 'mdi-clock-alert'
  if (event.to_execution_status === 'in_progress') return 'mdi-play-circle'
  if (event.to_execution_status === 'paused') return 'mdi-pause-circle'
  if (event.to_execution_status === 'completed') return 'mdi-check-bold'
  return 'mdi-arrow-right-circle'
}

const getEventColor = (event: StatusEvent): string => {
  if (event.to_decision_status === 'approved') return 'success'
  if (event.to_decision_status === 'rejected') return 'error'
  if (event.to_decision_status === 'pending_approval') return 'warning'
  if (event.to_execution_status === 'in_progress') return 'primary'
  if (event.to_execution_status === 'paused') return 'warning'
  if (event.to_execution_status === 'completed') return 'success'
  return 'grey'
}

const getEventDescription = (event: StatusEvent): string => {
  let description = ''
  
  if (event.from_decision_status && event.to_decision_status) {
    description += `Estado de decisión: ${event.from_decision_status} → ${event.to_decision_status}`
  } else if (event.to_decision_status) {
    description += `Estado de decisión: ${event.to_decision_status}`
  }
  
  if (event.from_execution_status && event.to_execution_status) {
    if (description) description += ' | '
    description += `Estado de ejecución: ${event.from_execution_status} → ${event.to_execution_status}`
  } else if (event.to_execution_status) {
    if (description) description += ' | '
    description += `Estado de ejecución: ${event.to_execution_status}`
  }
  
  return description || 'Cambio de estado'
}

defineExpose({ openTimeline })
</script>

<template>
  <div class="status-timeline">
    <v-btn
      color="grey-darken-1"
      variant="outlined"
      prepend-icon="mdi-timeline-clock"
      @click="openTimeline"
    >
      Ver Historial de Cambios
    </v-btn>

    <v-dialog v-model="showTimeline" max-width="800" scrollable>
      <v-card>
        <v-card-title class="d-flex align-center">
          <v-icon icon="mdi-timeline-clock" class="mr-2" />
          Audit Trail - Historial de Cambios de Estado
          <v-spacer />
          <v-chip
            color="primary"
            variant="flat"
            size="small"
          >
            {{ events.length }} eventos
          </v-chip>
        </v-card-title>

        <v-divider />

        <v-card-text style="max-height: 600px;">
          <v-alert
            v-if="events.length === 0 && !loading"
            type="info"
            variant="tonal"
          >
            No hay eventos de cambio de estado registrados
          </v-alert>

          <v-timeline
            side="end"
            align="start"
            density="compact"
            line-inset="12"
          >
            <v-timeline-item
              v-for="event in sortedEvents"
              :key="event.id"
              :dot-color="getEventColor(event)"
              size="small"
            >
              <template #icon>
                <v-icon
                  :icon="getEventIcon(event)"
                  size="small"
                />
              </template>

              <v-card elevation="2" class="mb-3">
                <v-card-title class="text-body-1 pb-2">
                  {{ getEventDescription(event) }}
                </v-card-title>

                <v-card-subtitle class="pb-2">
                  <v-icon icon="mdi-account" size="x-small" class="mr-1" />
                  {{ event.changed_by.name }}
                  <span class="text-caption text-medium-emphasis ml-2">
                    · {{ formatDate(event.created_at) }}
                  </span>
                </v-card-subtitle>

                <v-card-text v-if="event.notes">
                  <v-alert
                    type="info"
                    variant="outlined"
                    density="compact"
                    class="text-body-2"
                  >
                    <strong>Notas:</strong> {{ event.notes }}
                  </v-alert>
                </v-card-text>

                <v-card-text v-else class="text-caption text-medium-emphasis">
                  Sin notas adicionales
                </v-card-text>
              </v-card>
            </v-timeline-item>
          </v-timeline>

          <v-progress-linear v-if="loading" indeterminate color="primary" class="mt-4" />
        </v-card-text>

        <v-divider />

        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="showTimeline = false">Cerrar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<style scoped>
.status-timeline {
  display: inline-block;
}
</style>
