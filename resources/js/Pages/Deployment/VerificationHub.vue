<script setup lang="ts">
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import SchedulerStatus from '@/components/Verification/SchedulerStatus.vue'
import NotificationCenter from '@/components/Verification/NotificationCenter.vue'
import ChannelConfig from '@/components/Verification/ChannelConfig.vue'
import TransitionReadiness from '@/components/Verification/TransitionReadiness.vue'

const activeTab = ref('overview')

const tabs = [
  { id: 'overview', label: '📊 Resumen', icon: 'chart-line' },
  { id: 'control', label: '🎮 Control', icon: 'gear' },
  { id: 'notifications', label: '🔔 Notificaciones', icon: 'bell' },
  { id: 'config', label: '⚙️ Configuración', icon: 'sliders-horizontal' },
  { id: 'audit', label: '📋 Auditoría', icon: 'clock' },
]
</script>

<template>
  <Head title="Verification Hub - Panel de Control" />

  <AuthenticatedLayout>
    <div class="py-8">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold">🔍 Verification Hub</h1>
          <p class="text-muted-foreground mt-2">Panel centralizado de monitoreo y control del sistema de verificación</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex gap-2 mb-6 border-b border-border pb-2 flex-wrap">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'px-4 py-2 rounded-t-lg font-medium transition-colors text-sm',
              activeTab === tab.id
                ? 'bg-primary text-primary-foreground'
                : 'text-muted-foreground hover:text-foreground'
            ]"
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- Tab Content -->
        <div class="space-y-6">
          <!-- Overview Tab -->
          <div v-show="activeTab === 'overview'" class="space-y-6">
            <h2 class="text-xl font-semibold">📊 Estado General del Sistema</h2>
            
            <!-- Quick Status Cards -->
            <div class="grid grid-cols-4 gap-4">
              <div class="bg-card border border-border rounded-lg p-4">
                <p class="text-xs text-muted-foreground uppercase tracking-wide">Fase Actual</p>
                <p class="text-2xl font-bold mt-1">Silent</p>
              </div>
              <div class="bg-card border border-border rounded-lg p-4">
                <p class="text-xs text-muted-foreground uppercase tracking-wide">Confianza Promedio</p>
                <p class="text-2xl font-bold mt-1">78%</p>
              </div>
              <div class="bg-card border border-border rounded-lg p-4">
                <p class="text-xs text-muted-foreground uppercase tracking-wide">Tasa de Error</p>
                <p class="text-2xl font-bold mt-1">25%</p>
              </div>
              <div class="bg-card border border-border rounded-lg p-4">
                <p class="text-xs text-muted-foreground uppercase tracking-wide">Notificaciones</p>
                <p class="text-2xl font-bold mt-1 text-orange-500">12</p>
              </div>
            </div>

            <!-- Scheduler Status & Transition Readiness -->
            <div class="grid grid-cols-2 gap-6">
              <div class="col-span-2">
                <h3 class="text-lg font-semibold mb-4">⏰ Estado del Scheduler</h3>
                <SchedulerStatus />
              </div>
              <div class="col-span-2">
                <h3 class="text-lg font-semibold mb-4">📊 Readiness para Transición</h3>
                <TransitionReadiness />
              </div>
            </div>
          </div>

          <!-- Control Center Tab -->
          <div v-show="activeTab === 'control'" class="space-y-6">
            <h2 class="text-xl font-semibold">🎮 Centro de Control</h2>
            
            <div class="grid grid-cols-2 gap-4">
              <button class="bg-card border border-border rounded-lg p-4 text-left hover:border-primary transition-colors h-24 flex flex-col justify-center">
                <div class="text-2xl mb-2">🔄</div>
                <h3 class="font-semibold">Ejecutar Scheduler Ahora</h3>
                <p class="text-xs text-muted-foreground mt-1">Fuerza evaluación inmediata de transiciones</p>
              </button>
              
              <button class="bg-card border border-border rounded-lg p-4 text-left hover:border-primary transition-colors h-24 flex flex-col justify-center">
                <div class="text-2xl mb-2">🧪</div>
                <h3 class="font-semibold">Ejecutar Dry-Run</h3>
                <p class="text-xs text-muted-foreground mt-1">Simula transiciones sin aplicar cambios</p>
              </button>
              
              <button class="bg-card border border-border rounded-lg p-4 text-left hover:border-primary transition-colors h-24 flex flex-col justify-center">
                <div class="text-2xl mb-2">🔔</div>
                <h3 class="font-semibold">Enviar Notificación de Prueba</h3>
                <p class="text-xs text-muted-foreground mt-1">Test todos los canales configurados</p>
              </button>
              
              <button class="bg-card border border-border rounded-lg p-4 text-left hover:border-primary transition-colors h-24 flex flex-col justify-center">
                <div class="text-2xl mb-2">⚙️</div>
                <h3 class="font-semibold">Configuración Rápida</h3>
                <p class="text-xs text-muted-foreground mt-1">Asistente de configuración inicial</p>
              </button>
            </div>

            <!-- Toggles Section -->
            <div class="bg-card border border-border rounded-lg p-4">
              <h3 class="font-semibold mb-4">🔧 Controles de Activación</h3>
              <div class="space-y-3">
                <label class="flex items-center gap-3 cursor-pointer">
                  <input type="checkbox" checked class="rounded" />
                  <span>Scheduler automático habilitado</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                  <input type="checkbox" checked class="rounded" />
                  <span>Notificaciones habilitadas</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                  <input type="checkbox" class="rounded" />
                  <span>Modo mantenimiento (pausa todo)</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Notifications Tab -->
          <div v-show="activeTab === 'notifications'" class="space-y-6">
            <h2 class="text-xl font-semibold">🔔 Centro de Notificaciones</h2>
            <NotificationCenter />
          </div>

          <!-- Configuration Tab -->
          <div v-show="activeTab === 'config'" class="space-y-6">
            <h2 class="text-xl font-semibold">⚙️ Configuración de Canales</h2>
            <ChannelConfig />
          </div>

          <!-- Audit Tab -->
          <div v-show="activeTab === 'audit'" class="space-y-6">
            <h2 class="text-xl font-semibold">📋 Registro de Auditoría</h2>
            <div class="bg-card border border-border rounded-lg p-4">
              <p class="text-muted-foreground">El explorador de auditoría estará disponible próximamente</p>
              <p class="text-sm text-muted-foreground mt-2">Mostrará:</p>
              <ul class="list-disc list-inside text-sm text-muted-foreground mt-2 space-y-1">
                <li>Historial completo de transiciones de fase</li>
                <li>Cambios de configuración con usuario y timestamp</li>
                <li>Acciones manuales del sistema</li>
                <li>Filtros avanzados y exportación</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
