<template>
  <Teleport to="body">
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl w-full max-w-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Nueva Conversación</h2>

        <form @submit.prevent="submitForm">
          <!-- Title Input -->
          <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
              Título
            </label>
            <input
              v-model="form.title"
              type="text"
              placeholder="Ej: Sprint Planning Q2"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500"
              :class="{ 'border-red-500': errors.title }"
            />
            <p v-if="errors.title" class="text-sm text-red-600 mt-1">{{ errors.title }}</p>
          </div>

          <!-- Participants -->
          <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
              Participantes
            </label>
            <div class="space-y-2 mb-3 max-h-48 overflow-y-auto">
              <label v-for="person in availablePeople" :key="person.id" class="flex items-center">
                <input
                  type="checkbox"
                  :value="person.id"
                  v-model="form.participant_ids"
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span class="ml-2 text-gray-700 dark:text-gray-300">{{ person.name }}</span>
              </label>
            </div>
            <p v-if="errors.participant_ids" class="text-sm text-red-600">{{ errors.participant_ids }}</p>
          </div>

          <!-- Context Type -->
          <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
              Contexto (Opcional)
            </label>
            <select
              v-model="form.context_type"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
            >
              <option value="none">Ninguno</option>
              <option value="learning_assignment">Asignación de Aprendizaje</option>
              <option value="performance_review">Revisión de Desempeño</option>
              <option value="incident">Incidente</option>
              <option value="survey">Encuesta</option>
              <option value="onboarding">Onboarding</option>
            </select>
          </div>

          <!-- Buttons -->
          <div class="flex gap-3">
            <button
              type="button"
              @click="$emit('close')"
              class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg transition"
            >
              {{ isSubmitting ? 'Creando...' : 'Crear' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import { api } from '@/utils/api'

interface Person {
  id: number
  name: string
}

const emit = defineEmits<{
  close: []
  created: [conversation: any]
}>()

const availablePeople = ref<Person[]>([])
const isSubmitting = ref(false)
const form = reactive({
  title: '',
  participant_ids: [] as number[],
  context_type: 'none'
})
const errors = reactive<Record<string, string>>({})

onMounted(async () => {
  await loadAvailablePeople()
})

async function loadAvailablePeople() {
  try {
    const response = await api.get('/organization/people')
    availablePeople.value = response.data.data
  } catch (error) {
    console.error('Error loading people:', error)
  }
}

async function submitForm() {
  Object.keys(errors).forEach(key => delete errors[key])

  if (!form.title.trim()) {
    errors.title = 'El título es requerido'
    return
  }

  if (form.participant_ids.length === 0) {
    errors.participant_ids = 'Debes seleccionar al menos un participante'
    return
  }

  isSubmitting.value = true
  try {
    const response = await api.post('/messaging/conversations', {
      title: form.title,
      participant_ids: form.participant_ids,
      context_type: form.context_type
    })

    emit('created', response.data.data)
  } catch (error: any) {
    if (error.response?.data?.errors) {
      Object.assign(errors, error.response.data.errors)
    } else {
      errors.form = 'Error al crear conversación'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>
