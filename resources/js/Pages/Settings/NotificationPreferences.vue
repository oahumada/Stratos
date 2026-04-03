<template>
  <div class="notification-preferences">
    <v-card>
      <v-card-title class="d-flex align-center gap-2">
        <v-icon>mdi-bell-cog</v-icon>
        Notification Preferences
      </v-card-title>

      <v-card-text>
        <v-alert
          v-if="loading"
          type="info"
          title="Loading..."
        >
          Fetching your notification preferences...
        </v-alert>

        <div v-else>
          <!-- Current Preferences -->
          <v-subheader>Your Active Channels</v-subheader>
          <div v-if="preferences.length === 0" class="text-center py-6">
            <p class="text-subtitle-2 text-disabled">
              No channels configured. Add one below to start receiving notifications.
            </p>
          </div>

          <v-list v-else>
            <v-list-item
              v-for="pref in preferences"
              :key="pref.channel_type"
              class="mb-2"
            >
              <template #prepend>
                <v-icon
                  :icon="getChannelIcon(pref.channel_type)"
                  :color="getChannelColor(pref.channel_type)"
                />
              </template>

              <v-list-item-title>
                {{ formatChannelType(pref.channel_type) }}
              </v-list-item-title>
              <v-list-item-subtitle>
                Added {{ formatDate(pref.created_at) }}
              </v-list-item-subtitle>

              <template #append>
                <v-switch
                  :model-value="pref.is_active"
                  :loading="togglingChannel === pref.channel_type"
                  @change="toggleChannel(pref.channel_type)"
                />
                <v-btn
                  icon
                  size="small"
                  variant="text"
                  :loading="deletingChannel === pref.channel_type"
                  @click="deleteChannel(pref.channel_type)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
            </v-list-item>
          </v-list>

          <!-- Add New Channel -->
          <v-divider class="my-6" />

          <v-subheader>Add Channel</v-subheader>

          <v-form ref="form" @submit.prevent="addChannel">
            <v-select
              v-model="newChannel.type"
              label="Channel Type"
              :items="availableChannels"
              item-title="text"
              item-value="value"
              outlined
              required
              class="mb-4"
            />

            <!-- Channel-specific config -->
            <div v-if="newChannel.type === 'telegram'" class="mb-4">
              <v-text-field
                v-model="newChannel.config.bot_token"
                label="Bot Token"
                hint="Your Telegram Bot API token"
                required
              />
              <v-text-field
                v-model="newChannel.config.chat_id"
                label="Chat ID"
                hint="Your Telegram chat ID or channel ID"
                required
              />
            </div>

            <div v-if="newChannel.type === 'slack'" class="mb-4">
              <v-text-field
                v-model="newChannel.config.webhook_url"
                label="Webhook URL"
                hint="Slack incoming webhook URL"
                required
              />
            </div>

            <v-alert v-if="error" type="error" class="mb-4">
              {{ error }}
            </v-alert>

            <v-btn
              type="submit"
              color="primary"
              :loading="adding"
              block
            >
              Add Channel
            </v-btn>
          </v-form>
        </div>
      </v-card-text>
    </v-card>

    <!-- Success notification -->
    <v-snackbar
      v-model="showSuccess"
      color="success"
      timeout="3000"
    >
      {{ successMessage }}
    </v-snackbar>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { apiClient } from '@/services/api'

interface Preference {
  channel_type: string
  is_active: boolean
  created_at: string
}

interface ChannelConfig {
  bot_token?: string
  chat_id?: string
  webhook_url?: string
}

const router = useRouter()

const loading = ref(true)
const adding = ref(false)
const togglingChannel = ref<string | null>(null)
const deletingChannel = ref<string | null>(null)
const error = ref('')
const showSuccess = ref(false)
const successMessage = ref('')

const preferences = ref<Preference[]>([])
const availableChannels = ref<Array<{ text: string; value: string }>>([])

const newChannel = ref({
  type: '',
  config: {} as ChannelConfig,
})

const form = ref()

onMounted(async () => {
  await fetchPreferences()
})

async function fetchPreferences() {
  try {
    loading.value = true
    const response = await apiClient.get('/notification-preferences')
    preferences.value = response.data.preferences
    availableChannels.value = response.data.available_channels.map((ch: string) => ({
      text: formatChannelType(ch),
      value: ch,
    }))
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load preferences'
  } finally {
    loading.value = false
  }
}

async function addChannel() {
  if (!form.value.validate()) return

  try {
    adding.value = true
    await apiClient.post('/notification-preferences', {
      channel_type: newChannel.value.type,
      channel_config: newChannel.value.config,
      is_active: true,
    })

    successMessage.value = `${formatChannelType(newChannel.value.type)} channel added!`
    showSuccess.value = true

    newChannel.value = { type: '', config: {} }
    form.value.reset()
    await fetchPreferences()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to add channel'
  } finally {
    adding.value = false
  }
}

async function toggleChannel(channelType: string) {
  try {
    togglingChannel.value = channelType
    await apiClient.post(`/notification-preferences/${channelType}/toggle`)

    const pref = preferences.value.find(p => p.channel_type === channelType)
    if (pref) {
      pref.is_active = !pref.is_active
    }

    successMessage.value = `${formatChannelType(channelType)} ${pref?.is_active ? 'enabled' : 'disabled'}`
    showSuccess.value = true
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to toggle channel'
  } finally {
    togglingChannel.value = null
  }
}

async function deleteChannel(channelType: string) {
  if (!confirm(`Delete ${formatChannelType(channelType)} channel?`)) return

  try {
    deletingChannel.value = channelType
    await apiClient.delete(`/notification-preferences/${channelType}`)

    preferences.value = preferences.value.filter(p => p.channel_type !== channelType)

    successMessage.value = `${formatChannelType(channelType)} channel removed`
    showSuccess.value = true
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to delete channel'
  } finally {
    deletingChannel.value = null
  }
}

function formatChannelType(type: string): string {
  const map: Record<string, string> = {
    slack: 'Slack',
    telegram: 'Telegram',
    email: 'Email',
  }
  return map[type] || type
}

function getChannelIcon(type: string): string {
  const map: Record<string, string> = {
    slack: 'mdi-slack',
    telegram: 'mdi-send',
    email: 'mdi-email',
  }
  return map[type] || 'mdi-bell'
}

function getChannelColor(type: string): string {
  const map: Record<string, string> = {
    slack: '#36C5F0',
    telegram: '#0088cc',
    email: '#EA4335',
  }
  return map[type] || 'gray'
}

function formatDate(date: string): string {
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}
</script>

<style scoped>
.notification-preferences {
  max-width: 600px;
}
</style>
