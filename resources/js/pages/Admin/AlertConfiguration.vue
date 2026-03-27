<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-8">
    <!-- Header -->
    <div class="mb-12">
      <div class="flex items-center gap-4 mb-2">
        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-orange-500 to-red-500">
          <PhBell class="h-6 w-6 text-white" weight="fill" />
        </div>
        <h1 class="text-4xl font-bold text-white">Centro de Control de Alertas</h1>
      </div>
      <p class="text-slate-400 text-lg">Configurar umbrales, monitorear historial y gestionar escalaciones</p>
    </div>

    <!-- Quick Stats -->
    <div class="mb-12 grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="rounded-lg border border-white/10 bg-white/5 p-6 backdrop-blur">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-slate-400">Umbrales Activos</p>
            <p class="mt-2 text-3xl font-bold text-white">{{ stats.activeThresholds }}</p>
          </div>
          <PhCheckCircle class="h-10 w-10 text-green-500/30" weight="fill" />
        </div>
      </div>

      <div class="rounded-lg border border-white/10 bg-white/5 p-6 backdrop-blur">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-slate-400">Alertas Críticas</p>
            <p class="mt-2 text-3xl font-bold text-red-400">{{ stats.criticalAlerts }}</p>
          </div>
          <PhWarning class="h-10 w-10 text-red-500/30" weight="fill" />
        </div>
      </div>

      <div class="rounded-lg border border-white/10 bg-white/5 p-6 backdrop-blur">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-slate-400">Sin Reconocer</p>
            <p class="mt-2 text-3xl font-bold text-yellow-400">{{ stats.unacknowledged }}</p>
          </div>
          <PhClockClockwise class="h-10 w-10 text-yellow-500/30" weight="fill" />
        </div>
      </div>

      <div class="rounded-lg border border-white/10 bg-white/5 p-6 backdrop-blur">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-slate-400">Tasa Reconocimiento</p>
            <p class="mt-2 text-3xl font-bold text-blue-400">{{ stats.ackRate }}%</p>
          </div>
          <PhTrendUp class="h-10 w-10 text-blue-500/30" weight="fill" />
        </div>
      </div>
    </div>

    <!-- Main Options Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
      <!-- Option 1: Configuration -->
      <div
        @click="activeTab = 'configuration'"
        :class="[
          'group relative overflow-hidden rounded-xl border-2 p-8 cursor-pointer transition-all duration-300',
          activeTab === 'configuration'
            ? 'border-indigo-500 bg-gradient-to-br from-indigo-500/20 via-slate-800 to-slate-900 shadow-lg shadow-indigo-500/20'
            : 'border-white/10 bg-white/5 hover:border-indigo-500/50 hover:bg-white/10'
        ]"
      >
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
        
        <div class="relative z-10">
          <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500/80 to-indigo-600 shadow-lg">
            <PhGear class="h-7 w-7 text-white" weight="fill" />
          </div>

          <h2 class="mb-2 text-2xl font-bold text-white">Configuración</h2>
          <p class="mb-6 text-slate-300 text-sm leading-relaxed">
            Define umbrales de alertas por métrica. Establece niveles de severidad y activa/desactiva monitoreo en tiempo real.
          </p>

          <div class="mb-4 space-y-2">
            <div class="flex items-center gap-2 text-sm text-slate-400">
              <PhCheck class="h-4 w-4 text-indigo-400" weight="bold" />
              <span>Crear nuevos umbrales</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-400">
              <PhCheck class="h-4 w-4 text-indigo-400" weight="bold" />
              <span>Editar configuración existente</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-400">
              <PhCheck class="h-4 w-4 text-indigo-400" weight="bold" />
              <span>Validar métricas y valores</span>
            </div>
          </div>

          <button
            class="w-full rounded-lg bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-3 font-semibold text-white transition hover:from-indigo-600 hover:to-indigo-700 shadow-lg hover:shadow-indigo-500/20"
          >
            Acceder →
          </button>
        </div>
      </div>

      <!-- Option 2: History -->
      <div
        @click="activeTab = 'history'"
        :class="[
          'group relative overflow-hidden rounded-xl border-2 p-8 cursor-pointer transition-all duration-300',
          activeTab === 'history'
            ? 'border-emerald-500 bg-gradient-to-br from-emerald-500/20 via-slate-800 to-slate-900 shadow-lg shadow-emerald-500/20'
            : 'border-white/10 bg-white/5 hover:border-emerald-500/50 hover:bg-white/10'
        ]"
      >
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
        
        <div class="relative z-10">
          <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500/80 to-emerald-600 shadow-lg">
            <PhClockClockwise class="h-7 w-7 text-white" weight="fill" />
          </div>

          <h2 class="mb-2 text-2xl font-bold text-white">Historial</h2>
          <p class="mb-6 text-slate-300 text-sm leading-relaxed">
            Visualiza todas las alertas disparadas. Filtra por severidad, estado y rango de fechas. Reconoce y resuelve manualmente.
          </p>

          <div class="mb-4 space-y-2">
            <div class="flex items-center gap-2 text-sm text-slate-400">
              <PhCheck class="h-4 w-4 text-emerald-400" weight="bold" />
              <span>Ver todas las alertas disparadas</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-400">
              <PhCheck class="h-4 w-4 text-emerald-400" weight="bold" />
              <span>Filtrar por severidad y estado</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-400">
              <PhCheck class="h-4 w-4 text-emerald-400" weight="bold" />
              <span>Reconocer, resolver, silenciar alertas</span>
            </div>
          </div>

          <button
            class="w-full rounded-lg bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-3 font-semibold text-white transition hover:from-emerald-600 hover:to-emerald-700 shadow-lg hover:shadow-emerald-500/20"
          >
            Acceder →
          </button>
        </div>
      </div>

      <!-- Option 3: Escalation -->
      <div
        @click="activeTab = 'escalation'"
        :class="[
          'group relative overflow-hidden rounded-xl border-2 p-8 cursor-pointer transition-all duration-300',
          activeTab === 'escalation'
            ? 'border-purple-500 bg-gradient-to-br from-purple-500/20 via-slate-800 to-slate-900 shadow-lg shadow-purple-500/20'
            : 'border-white/10 bg-white/5 hover:border-purple-500/50 hover:bg-white/10'
        ]"
      >
        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
        
        <div class="relative z-10">
          <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-gradient-to-br from-purple-500/80 to-purple-600 shadow-lg">
            <PhArrowUp class="h-7 w-7 text-white" weight="fill" />
          </div>

          <h2 class="mb-2 text-2xl font-bold text-white">Escalación</h2>
          <p class="mb-6 text-slate-300 text-sm leading-relaxed">
            Configura políticas de escalación por severidad. Define receptores, métodos de notificación y tiempos de delay.
          </p>

          <div class="mb-4 space-y-2">
            <div class="flex items-center gap-2 text-sm text-slate-400">
              <PhCheck class="h-4 w-4 text-purple-400" weight="bold" />
              <span>Crear cadenas de escalación</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-400">
              <PhCheck class="h-4 w-4 text-purple-400" weight="bold" />
              <span>Configurar notificaciones (email, Slack)</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-400">
              <PhCheck class="h-4 w-4 text-purple-400" weight="bold" />
              <span>Gestionar receptores y niveles</span>
            </div>
          </div>

          <button
            class="w-full rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 px-4 py-3 font-semibold text-white transition hover:from-purple-600 hover:to-purple-700 shadow-lg hover:shadow-purple-500/20"
          >
            Acceder →
          </button>
        </div>
      </div>
    </div>

    <!-- Content Area -->
    <div class="rounded-xl border border-white/10 bg-white/5 backdrop-blur">
      <div class="border-b border-white/10 px-8 py-4">
        <h3 class="text-lg font-bold text-white">
          <span v-if="activeTab === 'configuration'">⚙️ Configuración de Umbrales</span>
          <span v-else-if="activeTab === 'history'">📊 Historial de Alertas</span>
          <span v-else-if="activeTab === 'escalation'">📤 Matriz de Escalación</span>
        </h3>
      </div>

      <div class="p-8">
        <!-- Configuration Tab -->
        <AlertThresholdForm 
          v-show="activeTab === 'configuration'"
          @submit="handleThresholdSubmit"
          @cancel="resetForm"
        />

        <!-- History Tab -->
        <AlertHistoryTable 
          v-show="activeTab === 'history'"
          @acknowledge="handleAcknowledge"
          @resolve="handleResolve"
          @mute="handleMute"
        />

        <!-- Escalation Tab -->
        <EscalationPolicyMatrix 
          v-show="activeTab === 'escalation'"
          @edit="handleEditPolicy"
          @delete="handleDeletePolicy"
          @addLevel="handleAddLevel"
        />
      </div>
    </div>

    <!-- Footer Info -->
    <div class="mt-12 rounded-lg border border-white/10 bg-white/5 p-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h4 class="mb-2 font-semibold text-white">Documentación</h4>
          <p class="text-sm text-slate-400">
            Consulta la guía completa de alertas y escalación en la documentación del proyecto.
          </p>
        </div>
        <div>
          <h4 class="mb-2 font-semibold text-white">Soporte</h4>
          <p class="text-sm text-slate-400">
            Contacta al equipo de operaciones para asistencia con configuraciones complejas.
          </p>
        </div>
        <div>
          <h4 class="mb-2 font-semibold text-white">Últimas Actualizaciones</h4>
          <p class="text-sm text-slate-400">
            Sistema de alertas v1.0 - Fase 2 completada (Mar 27, 2026)
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import {
  PhBell,
  PhCheckCircle,
  PhWarning,
  PhClockClockwise,
  PhTrendUp,
  PhGear,
  PhCheck,
  PhArrowUp,
} from '@phosphor-icons/vue'

import AlertThresholdForm from '@/components/Admin/AlertThresholdForm.vue'
import AlertHistoryTable from '@/components/Admin/AlertHistoryTable.vue'
import EscalationPolicyMatrix from '@/components/Admin/EscalationPolicyMatrix.vue'

const activeTab = ref<'configuration' | 'history' | 'escalation'>('configuration')

const stats = reactive({
  activeThresholds: 12,
  criticalAlerts: 3,
  unacknowledged: 5,
  ackRate: 87
})

const handleThresholdSubmit = async (data: any) => {
  console.log('Threshold submitted:', data)
  // API call to create/update threshold
}

const resetForm = () => {
  console.log('Form reset')
}

const handleAcknowledge = async (alertId: string, notes?: string) => {
  console.log('Acknowledging alert:', alertId, notes)
  // API call to acknowledge
}

const handleResolve = async (alertId: string) => {
  console.log('Resolving alert:', alertId)
  // API call to resolve
}

const handleMute = async (alertId: string) => {
  console.log('Muting alert:', alertId)
  // API call to mute
}

const handleEditPolicy = async (policyId: string) => {
  console.log('Editing policy:', policyId)
  // API call to update policy
}

const handleDeletePolicy = async (policyId: string) => {
  console.log('Deleting policy:', policyId)
  // API call to delete policy
}

const handleAddLevel = async (severity: string) => {
  console.log('Adding level for severity:', severity)
  // API call to add escalation level
}
</script>

<style scoped>
/* Smooth transitions */
:deep(.transition) {
  transition: all 0.3s ease;
}
</style>
