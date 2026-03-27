<template>
  <div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
        {{ isEditing ? 'Editar Umbral' : 'Nuevo Umbral de Alerta' }}
      </h3>

      <Form
        @submit="handleSubmit"
        :initial-values="formData"
        class="space-y-4"
      >
        <!-- Metric Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Métrica
          </label>
          <input
            v-model="form.metric"
            type="text"
            placeholder="ej: cpu_usage, memory_percent, response_time"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                   bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                   focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            :disabled="isEditing"
          />
          <p v-if="errors.metric" class="text-red-500 text-sm mt-1">{{ errors.metric }}</p>
        </div>

        <!-- Threshold Value -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Valor Umbral
            </label>
            <input
              v-model.number="form.threshold"
              type="number"
              step="0.01"
              placeholder="100"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                     bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                     focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <p v-if="errors.threshold" class="text-red-500 text-sm mt-1">{{ errors.threshold }}</p>
          </div>

          <!-- Severity -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Severidad
            </label>
            <select
              v-model="form.severity"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                     bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                     focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="info">ℹ️ Información</option>
              <option value="low">🔵 Baja</option>
              <option value="medium">🟡 Media</option>
              <option value="high">🔴 Alta</option>
              <option value="critical">🚨 Crítica</option>
            </select>
            <p v-if="errors.severity" class="text-red-500 text-sm mt-1">{{ errors.severity }}</p>
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Descripción
          </label>
          <textarea
            v-model="form.description"
            placeholder="Descripción opcional del umbral y qué significa"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                   bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                   focus:ring-2 focus:ring-blue-500 focus:border-transparent
                   h-20 resize-none"
          />
        </div>

        <!-- Active Toggle -->
        <div class="flex items-center gap-3">
          <input
            v-model="form.is_active"
            type="checkbox"
            id="is_active"
            class="w-4 h-4 text-blue-600 rounded border-gray-300 cursor-pointer"
          />
          <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Activar umbral
          </label>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
          <button
            type="submit"
            :disabled="isSubmitting"
            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400
                   text-white rounded-lg font-medium transition-colors"
          >
            {{ isSubmitting ? 'Guardando...' : isEditing ? 'Actualizar' : 'Crear' }}
          </button>
          <button
            type="button"
            @click="emit('cancel')"
            class="px-6 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600
                   text-gray-900 dark:text-white rounded-lg font-medium transition-colors"
          >
            Cancelar
          </button>
        </div>
      </Form>
    </div>

    <!-- Recent Thresholds -->
    <div v-if="recentThresholds.length > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
      <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Umbrales Recientes</h4>
      <div class="space-y-2">
        <div
          v-for="threshold in recentThresholds"
          :key="threshold.id"
          class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
        >
          <div>
            <p class="font-medium text-gray-900 dark:text-white">{{ threshold.metric }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Umbral: {{ threshold.threshold }}
              <span :class="getSeverityColor(threshold.severity)" class="ml-2">
                {{ threshold.severity }}
              </span>
            </p>
          </div>
          <button
            @click="edit(threshold)"
            class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400 text-sm"
          >
            Editar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { Form } from '@inertiajs/vue3'
import type { AlertThreshold } from '@/types'

interface Props {
  threshold?: AlertThreshold | null
  recentThresholds?: AlertThreshold[]
}

interface Emits {
  (e: 'submit', data: any): void
  (e: 'cancel'): void
}

const props = withDefaults(defineProps<Props>(), {
  threshold: null,
  recentThresholds: () => [],
})

const emit = defineEmits<Emits>()

const form = ref({
  metric: props.threshold?.metric || '',
  threshold: props.threshold?.threshold || 0,
  severity: props.threshold?.severity || 'medium',
  is_active: props.threshold?.is_active ?? true,
  description: props.threshold?.description || '',
})

const errors = ref({} as Record<string, string>)
const isSubmitting = ref(false)

const isEditing = computed(() => !!props.threshold)

const severityColors = {
  info: 'text-blue-600 dark:text-blue-400',
  low: 'text-cyan-600 dark:text-cyan-400',
  medium: 'text-amber-600 dark:text-amber-400',
  high: 'text-orange-600 dark:text-orange-400',
  critical: 'text-red-600 dark:text-red-400',
}

const getSeverityColor = (severity: string) => {
  return severityColors[severity as keyof typeof severityColors] || 'text-gray-600'
}

const handleSubmit = async () => {
  isSubmitting.value = true
  errors.value = {}

  try {
    emit('submit', form.value)
  } catch (error: any) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    }
  } finally {
    isSubmitting.value = false
  }
}

const edit = (threshold: AlertThreshold) => {
  emit('submit', threshold)
}
</script>
