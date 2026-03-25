import { ref, reactive, computed } from 'vue'
import { api } from '@/utils/api'

export interface Conversation {
  id: string
  title: string
  organization_id: number
  created_by: number
  is_active: boolean
  participant_count: number
  unread_count: number
  last_message_at?: string
  context_type: string
  created_at: string
  updated_at: string
}

export interface Message {
  id: string
  conversation_id: string
  sender_id: number
  body: string
  state: 'sent' | 'delivered' | 'read' | 'failed'
  reply_to_message_id?: string
  created_at: string
  updated_at: string
  sender?: {
    id: number
    name: string
  }
}

export interface ConversationParticipant {
  id: string
  conversation_id: string
  people_id: number
  can_send: boolean
  can_read: boolean
  joined_at: string
  left_at?: string
}

export function useMessaging() {
  const conversations = ref<Conversation[]>([])
  const currentConversation = ref<Conversation | null>(null)
  const messages = ref<Message[]>([])
  const participants = ref<ConversationParticipant[]>([])
  
  const loading = ref(false)
  const error = ref<string | null>(null)

  const unreadCount = computed(() => {
    return conversations.value.reduce((sum, conv) => sum + (conv.unread_count || 0), 0)
  })

  async function fetchConversations(page = 1) {
    loading.value = true
    error.value = null
    try {
      const response = await api.get('/messaging/conversations', {
        params: { page, per_page: 15 }
      })
      conversations.value = response.data.data
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error loading conversations'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchConversation(id: string) {
    loading.value = true
    error.value = null
    try {
      const response = await api.get(`/messaging/conversations/${id}`)
      currentConversation.value = response.data.data
      messages.value = response.data.data.messages || []
      participants.value = response.data.data.participants || []
      return response.data.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error loading conversation'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function createConversation(data: {
    title: string
    participant_ids: number[]
    context_type?: string
    context_id?: string
  }) {
    loading.value = true
    error.value = null
    try {
      const response = await api.post('/messaging/conversations', data)
      const newConv = response.data.data
      conversations.value.unshift(newConv)
      return newConv
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error creating conversation'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function updateConversation(id: string, data: Partial<Conversation>) {
    loading.value = true
    error.value = null
    try {
      const response = await api.put(`/messaging/conversations/${id}`, data)
      const updated = response.data.data
      
      const index = conversations.value.findIndex(c => c.id === id)
      if (index >= 0) {
        conversations.value[index] = updated
      }
      
      if (currentConversation.value?.id === id) {
        currentConversation.value = updated
      }
      
      return updated
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error updating conversation'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function archiveConversation(id: string) {
    loading.value = true
    error.value = null
    try {
      await api.delete(`/messaging/conversations/${id}`)
      
      conversations.value = conversations.value.filter(c => c.id !== id)
      if (currentConversation.value?.id === id) {
        currentConversation.value = null
        messages.value = []
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error archiving conversation'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function sendMessage(conversationId: string, body: string, replyToId?: string) {
    loading.value = true
    error.value = null
    try {
      const response = await api.post(
        `/messaging/conversations/${conversationId}/messages`,
        {
          body,
          reply_to_message_id: replyToId
        }
      )
      
      const newMessage = response.data.data
      messages.value.push(newMessage)
      
      // Update conversation last_message_at
      if (currentConversation.value?.id === conversationId) {
        currentConversation.value.last_message_at = new Date().toISOString()
      }
      
      return newMessage
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error sending message'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function markMessageAsRead(messageId: string) {
    error.value = null
    try {
      await api.post(`/messaging/messages/${messageId}/read`)
      
      const message = messages.value.find(m => m.id === messageId)
      if (message) {
        message.state = 'read'
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error marking message as read'
      throw err
    }
  }

  async function addParticipant(conversationId: string, peopleId: number) {
    loading.value = true
    error.value = null
    try {
      const response = await api.post(
        `/messaging/conversations/${conversationId}/participants`,
        { people_id: peopleId }
      )
      
      const newParticipant = response.data.data
      participants.value.push(newParticipant)
      
      if (currentConversation.value?.id === conversationId) {
        currentConversation.value.participant_count += 1
      }
      
      return newParticipant
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error adding participant'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function removeParticipant(conversationId: string, peopleId: number) {
    loading.value = true
    error.value = null
    try {
      await api.delete(`/messaging/conversations/${conversationId}/participants/${peopleId}`)
      
      participants.value = participants.value.filter(p => p.people_id !== peopleId)
      
      if (currentConversation.value?.id === conversationId) {
        currentConversation.value.participant_count -= 1
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error removing participant'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    // State
    conversations,
    currentConversation,
    messages,
    participants,
    loading,
    error,
    unreadCount,

    // Methods
    fetchConversations,
    fetchConversation,
    createConversation,
    updateConversation,
    archiveConversation,
    sendMessage,
    markMessageAsRead,
    addParticipant,
    removeParticipant
  }
}
