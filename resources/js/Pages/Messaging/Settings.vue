<template>
  <div class="messaging-settings-page">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
        Messaging Settings
      </h1>
      <p class="mt-2 text-gray-600 dark:text-gray-400">
        Configure messaging policies, retention, and permissions
      </p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="space-y-4">
      <div class="h-32 bg-gray-200 dark:bg-gray-700 rounded animate-pulse" />
      <div class="h-32 bg-gray-200 dark:bg-gray-700 rounded animate-pulse" />
    </div>

    <!-- Main Content -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Settings Panel -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Retention Policy -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Retention Policy
          </h2>
          
          <form @submit.prevent="updateSettings" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Retention Days
              </label>
              <input
                v-model.number="formData.retention_days"
                type="number"
                min="1"
                max="365"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
              />
              <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Messages older than this will be archived
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Max Participants per Conversation
              </label>
              <input
                v-model.number="formData.max_participants"
                type="number"
                min="1"
                max="1000"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
              />
            </div>

            <button
              type="submit"
              :disabled="updating"
              class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition"
            >
              {{ updating ? 'Saving...' : 'Save Settings' }}
            </button>
          </form>
        </div>

        <!-- Feature Toggles -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Features
          </h2>

          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900 dark:text-white">
                  Read Receipts
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Show when messages are read
                </p>
              </div>
              <input
                v-model="formData.enable_read_receipts"
                type="checkbox"
                class="w-5 h-5 text-blue-600"
              />
            </div>

            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-gray-900 dark:text-white">
                  Typing Indicators
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Show who is typing
                </p>
              </div>
              <input
                v-model="formData.enable_typing_indicators"
                type="checkbox"
                class="w-5 h-5 text-blue-600"
              />
            </div>
          </div>
        </div>

        <!-- Context Types -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Enabled Context Types
          </h2>

          <div class="grid grid-cols-2 gap-4">
            <label v-for="type in availableContexts" :key="type" class="flex items-center">
              <input
                :value="type"
                v-model="formData.allowed_context_types"
                type="checkbox"
                class="w-4 h-4 text-blue-600"
              />
              <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                {{ formatContextType(type) }}
              </span>
            </label>
          </div>
        </div>
      </div>

      <!-- Metrics Sidebar -->
      <div class="lg:col-span-1 space-y-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg p-6">
          <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-4">
            Usage Metrics
          </h3>

          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-blue-700 dark:text-blue-200">Conversations</span>
              <span class="font-bold text-blue-900 dark:text-blue-100">
                {{ metrics.total_conversations }}
              </span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-blue-700 dark:text-blue-200">Total Messages</span>
              <span class="font-bold text-blue-900 dark:text-blue-100">
                {{ metrics.total_messages }}
              </span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-blue-700 dark:text-blue-200">Unread</span>
              <span class="font-bold text-blue-900 dark:text-blue-100">
                {{ metrics.unread_messages }}
              </span>
            </div>
            <div class="flex justify-between pt-3 border-t border-blue-200 dark:border-blue-700">
              <span class="text-sm text-blue-700 dark:text-blue-200">Delivery Rate</span>
              <span class="font-bold text-blue-900 dark:text-blue-100">
                {{ metrics.delivery_success_rate }}%
              </span>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
            Quick Actions
          </h3>

          <button
            @click="archiveOldMessages"
            class="w-full px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition"
          >
            Archive Old Messages
          </button>

          <button
            @click="exportSettings"
            class="w-full px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition mt-2"
          >
            Export Settings
          </button>
        </div>
      </div>
    </div>

    <!-- Success Message -->
    <div
      v-if="successMessage"
      class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow"
    >
      {{ successMessage }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';

const loading = ref(false);
const updating = ref(false);
const successMessage = ref('');

const formData = ref({
  retention_days: 90,
  max_participants: 500,
  enable_read_receipts: true,
  enable_typing_indicators: true,
  allowed_context_types: [
    'scenario',
    'learning_path',
    'project',
    'evaluation',
    'alert',
    'general',
  ],
});

const metrics = ref({
  total_conversations: 0,
  active_conversations: 0,
  archived_conversations: 0,
  total_messages: 0,
  unread_messages: 0,
  delivery_success_rate: 0,
  read_rate: 0,
});

const availableContexts = [
  'scenario',
  'learning_path',
  'project',
  'evaluation',
  'alert',
  'general',
];

const formatContextType = (type: string): string => {
  return type
    .split('_')
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};

onMounted(async () => {
  await loadSettings();
  await loadMetrics();
});

const loadSettings = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/api/messaging/settings');
    Object.assign(formData.value, response.data.data);
  } catch (error) {
    console.error('Failed to load settings:', error);
  } finally {
    loading.value = false;
  }
};

const loadMetrics = async () => {
  try {
    const response = await axios.get('/api/messaging/metrics');
    Object.assign(metrics.value, response.data.data);
  } catch (error) {
    console.error('Failed to load metrics:', error);
  }
};

const updateSettings = async () => {
  try {
    updating.value = true;
    await axios.put('/api/messaging/settings', formData.value);
    successMessage.value = 'Settings saved successfully!';
    setTimeout(() => {
      successMessage.value = '';
    }, 3000);
  } catch (error) {
    console.error('Failed to update settings:', error);
  } finally {
    updating.value = false;
  }
};

const archiveOldMessages = async () => {
  if (confirm('Archive messages older than retention period?')) {
    successMessage.value = 'Archiving messages... This may take a few minutes.';
  }
};

const exportSettings = async () => {
  const dataStr = JSON.stringify(formData.value, null, 2);
  const dataUri = 'data:application/json;charset=utf-8,' + encodeURIComponent(dataStr);

  const exportFileDefaultName = `messaging-settings-${new Date().toISOString().split('T')[0]}.json`;

  const linkElement = document.createElement('a');
  linkElement.setAttribute('href', dataUri);
  linkElement.setAttribute('download', exportFileDefaultName);
  linkElement.click();
};
</script>

<style scoped>
.messaging-settings-page {
  @apply p-4 md:p-6;
}
</style>
