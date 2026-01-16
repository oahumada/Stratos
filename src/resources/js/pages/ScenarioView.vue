<script setup>
import { Head } from '@inertiajs/vue3'
import CapabilityEcosystem from './CapabilityEcosystem.vue'
import InsightsPanel from './InsightsPanel.vue'

// Inertia inyecta automáticamente los datos del controlador aquí
const props = defineProps({
    scenario: Object,
    capabilities: Array
})
</script>

<template>
  <Head title="Stratos - Scenario Analysis" />
  
  <div class="min-h-screen bg-gradient-to-br from-stratos-darker via-stratos-dark to-blue-950 text-white overflow-hidden">
    <!-- Header -->
    <header class="flex items-center justify-between px-8 py-4 border-b border-white/5 bg-black/20 backdrop-blur-md">
      <div class="flex items-center gap-4">
        <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-lg flex items-center justify-center font-bold text-lg shadow-lg shadow-cyan-500/20">
          S
        </div>
        <div>
          <h1 class="text-xl font-semibold tracking-tight">Stratos — AI Adoption</h1>
          <p class="text-[10px] uppercase tracking-widest text-white/40">Scenario: {{ scenario.name }}</p>
        </div>
      </div>
      <div class="text-xs font-mono text-white/30">
        Última ejecución: 2026-01-12 · Owner: Omar
      </div>
    </header>

    <div class="flex h-[calc(100vh-73px)]">
      <!-- Canvas Central -->
      <main class="flex-1 relative overflow-hidden">
        <!-- Capability Ecosystem -->
        <CapabilityEcosystem :capabilities="capabilities" />

        <!-- Bottom Info Bar (Glassmorphism) -->
        <div class="absolute bottom-8 left-8 right-96 glass-panel-strong rounded-2xl px-8 py-5 flex items-center justify-between shadow-2xl">
          <div class="flex gap-8">
            <div>
              <div class="text-[10px] uppercase tracking-widest text-white/40 mb-1 font-bold">Health Status</div>
              <div class="text-lg font-semibold flex items-center gap-2">
                <span class="text-cyan-400">{{ scenario.health }}%</span>
                <span class="text-white/20">|</span>
                <span class="text-sm text-white/70 font-normal">Coverage: {{ scenario.coverage }}%</span>
              </div>
            </div>
            <div class="border-l border-white/10 pl-8">
              <div class="text-[10px] uppercase tracking-widest text-white/40 mb-1 font-bold">Critical Risks</div>
              <div class="text-sm text-white/80">System Design, Kubernetes, Mentoring</div>
            </div>
          </div>
          
          <button class="px-8 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-xl text-sm font-bold hover:scale-105 hover:shadow-[0_0_20px_rgba(6,182,212,0.4)] transition-all active:scale-95">
            RUN FULL ANALYSIS
          </button>
        </div>
      </main>

      <!-- Insights Panel -->
      <aside class="w-96 glass-panel-strong border-l border-white/10 p-8 overflow-y-auto shadow-2xl">
        <InsightsPanel :scenario="scenario" />
      </aside>
    </div>
  </div>
</template>

<style scoped>
/* Estilos específicos para el efecto de profundidad */
.glass-panel-strong {
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(24px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}
</style>