<template>
  <div class="flex flex-col h-full bg-white dark:bg-gray-900">
    <!-- Header -->
    <div class="border-b border-gray-200 dark:border-gray-800 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mensajes</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gestor de conversaciones</p>
        </div>
        <button
          @click="isCreating = true"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition"
        >
          + Nueva Conversación
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-1 overflow-hidden">
      <!-- Conversations List -->
      <div class="w-80 border-r border-gray-200 dark:border-gray-800 flex flex-col overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-800">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Buscar conversaciones..."
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
          />
        </div>

        <div class="flex-1 overflow-y-auto">
          <div
            v-for="conv in filteredConversations"
            :key="conv.id"
            @click="selectedConversation = conv"
            :class="[
              'px-4 py-3 border-b border-gray-200 dark:border-gray-800 cursor-pointer transition',
              selectedConversation?.id === conv.id
                ? 'bg-blue-50 dark:bg-blue-900/20'
                : 'hover:bg-gray-50 dark:hover:bg-gray-800/50'
            ]"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ conv.title }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 truncate mt-1">
                  {{ conv.last_message || 'Sin mensajes' }}
                </p>
              </div>
              <span
                v-if="conv.unread_count"
                class="ml-2 px-2 py-1 bg-red-600 text-white text-xs rounded-full font-semibold"
              >
                {{ conv.unread_count }}
              </span>
            </div>
          </div>

          <div v-if="filteredConversations.length === 0" class="p-4 text-center text-gray-500">
            No hay conversaciones
          </div>
        </div>
      </div>

      <!-- Conversation Detail -->
      <div v-if="selectedConversation" class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="border-b border-gray-200 dark:border-gray-800 p-4 flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ selectedConversation.title }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ selectedConversation.participant_count }} participantes</p>
          </div>
          <button
            @click="selectedConversation = null"
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-800/50">
          <div
            v-for="msg in selectedConversation.messages || []"
            :key="msg.id"
            :class="[
              'mb-4 flex',
              msg.is_from_me ? 'justify-end' : 'justify-start'
            ]"
          >
            <div
              :class="[
                'max-w-xs px-4 py-2 rounded-lg',
                msg.is_from_me
                  ? 'bg-blue-600 text-white rounded-br-none'
                  : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-bl-none'
              ]"
            >
              <p class="text-sm">{{ msg.body }}</p>
              <p :class="['text-xs mt-1', msg.is_from_me ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400']">
                {{ formatTime(msg.created_at) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Message Input -->
        <div class="border-t border-gray-200 dark:border-gray-800 p-4">
          <form @submit.prevent="sendMessage" class="flex gap-2">
            <input
              v-model="messageBody"
              type="text"
              placeholder="Escribe un mensaje..."
              class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500"
            />
            <button
              type="submit"
              :disabled="!messageBody.trim() || isSending"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg transition"
            >
              {{ isSending ? 'Enviando...' : 'Enviar' }}
            </button>
          </form>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="flex-1 flex items-center justify-center text-gray-500">
        <div class="text-center">
          <p class="text-lg">Selecciona una conversación para comenzar</p>
        </div>
      </div>
    </div>

    <!-- Create Conversation Modal -->
    <CreateConversationModal
      v-if="isCreating"
      @close="isCreating = false"
      @created="onConversationCreated"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { api } from '@/utils/api'
import CreateConversationModal from './CreateConversationModal.vue'

interface Conversation {
  id: string
  title: string
  participant_count: number
  unread_count: number
  last_message?: string
  last_message_at?: string
  is_active: boolean
  messages?: Message[]
}

interface Message {
  id: string
  body: string
  state: string
  sender: { id: number; name: string }
  created_at: string
  is_from_me?: boolean
}

const conversations = ref<Conversation[]>([])
const selectedConversation = ref<Conversation | null>(null)
const searchQuery = ref('')
const messageBody = ref('')
const isSending = ref(false)
const isCreating = ref(false)

const filteredConversations = computed(() => {
  return conversations.value.filter(conv =>
    conv.title.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

onMounted(async () => {
  await loadConversations()
})

async function loadConversations() {
  try {
    const response = await api.get('/messaging/conversations')
    conversations.value = response.data.data
  } catch (error) {
    console.error('Error loading conversations:', error)
  }
}

async function sendMessage() {
  if (!messageBody.value.trim() || !selectedConversation.value) return

  isSending.value = true
  try {
    await api.post(`/messaging/conversations/${selectedConversation.value.id}/messages`, {
      body: messageBody.value
    })
    messageBody.value = ''
    await loadConversations()
  } catch (error) {
    console.error('Error sending message:', error)
  } finally {
    isSending.value = false
  }
}

function onConversationCreated(conv: Conversation) {
  conversations.value.unshift(conv)
  selectedConversation.value = conv
  isCreating.value = false
}

function formatTime(date: string): string {
  return new Date(date).toLocaleTimeString('es-ES', {
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
