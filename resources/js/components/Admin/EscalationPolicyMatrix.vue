<template>
  <div class="space-y-6">
    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Políticas Activas</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ activePolicies }}</p>
          </div>
          <div class="text-3xl">✅</div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Severidades Cubiertas</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ uniqueSeverities }}</p>
          </div>
          <div class="text-3xl">📊</div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Niveles Escalación</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ maxLevel }}</p>
          </div>
          <div class="text-3xl">📈</div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Email/Slack</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ notificationMethods }}</p>
          </div>
          <div class="text-3xl">🔔</div>
        </div>
      </div>
    </div>

    <!-- Escalation Matrix by Severity -->
    <div class="space-y-4">
      <div v-for="severity in severityOrder" :key="severity">
        <div
          :class="[
            'rounded-lg p-6 shadow',
            getSeverityBg(severity)
          ]"
        >
          <!-- Severity Header -->
          <div class="flex items-center gap-3 mb-4">
            <span class="text-2xl">{{ getSeverityIcon(severity) }}</span>
            <div>
              <h3 class="font-bold text-lg text-white">{{ getSeverityLabel(severity) }}</h3>
              <p class="text-sm opacity-90">Cadena de escalación</p>
            </div>
          </div>

          <!-- Escalation Chain -->
          <div class="space-y-3">
            <div v-if="getPoliciesForSeverity(severity).length === 0"
              class="text-center py-4 bg-opacity-20 bg-white rounded">
              <p class="text-white opacity-70">Sin políticas configuradas</p>
            </div>

            <template v-else>
              <div v-for="(policy, index) in getPoliciesForSeverity(severity)"
                :key="policy.id"
                class="relative">
                <!-- Connector Line -->
                <div v-if="index < getPoliciesForSeverity(severity).length - 1"
                  class="absolute left-6 top-12 w-0.5 h-16 bg-white opacity-30"></div>

                <!-- Policy Card -->
                <div class="bg-white bg-opacity-10 backdrop-blur rounded-lg p-4 border border-white border-opacity-20">
                  <div class="flex items-start justify-between">
                    <!-- Level Badge -->
                    <div class="flex items-start gap-4">
                      <div class="flex items-center justify-center w-12 h-12 rounded-full bg-white bg-opacity-20 border-2 border-white text-sm font-bold text-white">
                        {{ policy.escalation_level }}
                      </div>
                      <div>
                        <!-- Timing -->
                        <p v-if="policy.escalation_level === 1" class="text-sm text-white font-medium">
                          ⚡ Immediatamente
                        </p>
                        <p v-else class="text-sm text-white font-medium">
                          ⏱️ Después de {{ policy.delay_minutes }} minutos
                        </p>

                        <!-- Recipients -->
                        <div class="mt-2">
                          <p class="text-xs text-white font-semibold opacity-70">DESTINATARIOS:</p>
                          <ul class="text-sm text-white mt-1 space-y-1">
                            <li v-for="recipient in policy.recipients" :key="recipient"
                              class="flex items-center gap-2">
                              <span v-if="isEmailValid(recipient)" class="text-yellow-200">📧</span>
                              <span v-else class="text-blue-200">👤</span>
                              {{ recipient }}
                            </li>
                          </ul>
                        </div>

                        <!-- Notification Methods -->
                        <div class="mt-3 flex gap-2">
                          <span v-if="policy.hasEmailNotification"
                            class="inline-block px-2 py-1 bg-yellow-400 text-yellow-900 rounded text-xs font-semibold">
                            📧 Email
                          </span>
                          <span v-if="policy.hasSlackNotification"
                            class="inline-block px-2 py-1 bg-blue-400 text-blue-900 rounded text-xs font-semibold">
                            💬 Slack
                          </span>
                        </div>
                      </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-1">
                      <button
                        @click="editPolicy(policy)"
                        class="p-2 rounded hover:bg-white hover:bg-opacity-20 text-white"
                        title="Editar"
                      >
                        ✏️
                      </button>
                      <button
                        @click="deletePolicy(policy)"
                        class="p-2 rounded hover:bg-red-500 hover:bg-opacity-50 text-white"
                        title="Eliminar"
                      >
                        🗑️
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>

          <!-- Add Level Button -->
          <button
            @click="addLevel(severity)"
            class="mt-4 w-full py-2 px-4 rounded-lg bg-white bg-opacity-20 hover:bg-opacity-30
                   text-white font-medium transition-all border border-white border-opacity-30"
          >
            + Agregar Nivel
          </button>
        </div>
      </div>
    </div>

    <!-- Legend -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
      <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Leyenda de Severidades</h4>
      <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div v-for="sev in severityOrder" :key="sev" class="flex items-center gap-2">
          <span :class="['w-3 h-3 rounded-full', getSeverityBgSmall(sev)]"></span>
          <span class="text-sm text-gray-700 dark:text-gray-300">{{ getSeverityLabel(sev) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { EscalationPolicy } from '@/types'

interface Props {
  policies?: EscalationPolicy[]
}

interface Emits {
  (e: 'edit', policy: EscalationPolicy): void
  (e: 'delete', policy: EscalationPolicy): void
  (e: 'addLevel', severity: string): void
}

const props = withDefaults(defineProps<Props>(), {
  policies: () => [],
})

const emit = defineEmits<Emits>()

const severityOrder = ['critical', 'high', 'medium', 'low', 'info']

const severityColors = {
  critical: 'bg-gradient-to-r from-red-600 to-red-700',
  high: 'bg-gradient-to-r from-orange-600 to-orange-700',
  medium: 'bg-gradient-to-r from-amber-600 to-amber-700',
  low: 'bg-gradient-to-r from-blue-600 to-blue-700',
  info: 'bg-gradient-to-r from-cyan-600 to-cyan-700',
}

const severityBgsSmall = {
  critical: 'bg-red-500',
  high: 'bg-orange-500',
  medium: 'bg-amber-500',
  low: 'bg-blue-500',
  info: 'bg-cyan-500',
}

const severityIcons = {
  critical: '🚨',
  high: '🔴',
  medium: '🟡',
  low: '🔵',
  info: 'ℹ️',
}

const severityLabels = {
  critical: 'Crítica',
  high: 'Alta',
  medium: 'Media',
  low: 'Baja',
  info: 'Información',
}

const activePolicies = computed(() =>
  props.policies.filter(p => p.is_active).length
)

const uniqueSeverities = computed(() =>
  new Set(props.policies.map(p => p.severity)).size
)

const maxLevel = computed(() =>
  props.policies.length > 0
    ? Math.max(...props.policies.map(p => p.escalation_level))
    : 0
)

const notificationMethods = computed(() => {
  const methods = new Set<string>()
  props.policies.forEach(p => {
    if (p.notification_type.includes('email')) methods.add('Email')
    if (p.notification_type.includes('slack')) methods.add('Slack')
  })
  return `${methods.size}/2`
})

const getSeverityBg = (severity: string) =>
  severityColors[severity as keyof typeof severityColors] || 'bg-gray-600'

const getSeverityBgSmall = (severity: string) =>
  severityBgsSmall[severity as keyof typeof severityBgsSmall] || 'bg-gray-500'

const getSeverityIcon = (severity: string) =>
  severityIcons[severity as keyof typeof severityIcons] || '❓'

const getSeverityLabel = (severity: string) =>
  severityLabels[severity as keyof typeof severityLabels] || severity

const getPoliciesForSeverity = (severity: string) =>
  props.policies
    .filter(p => p.severity === severity && p.is_active)
    .sort((a, b) => a.escalation_level - b.escalation_level)

const isEmailValid = (email: string) =>
  /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)

const editPolicy = (policy: EscalationPolicy) => {
  emit('edit', policy)
}

const deletePolicy = (policy: EscalationPolicy) => {
  emit('delete', policy)
}

const addLevel = (severity: string) => {
  emit('addLevel', severity)
}
</script>
