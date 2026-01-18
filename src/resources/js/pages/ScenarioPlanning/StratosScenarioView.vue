<template>
  <div class="min-h-screen bg-gradient-to-br from-stratos-darker via-stratos-dark to-blue-950 text-white">
    <!-- Header -->
    <header class="flex items-center justify-between px-8 py-4 border-b border-white/5">
      <div class="flex items-center gap-4">
        <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-lg flex items-center justify-center font-bold text-lg">
          S
        </div>
        <div>
          <h1 class="text-xl font-semibold">Stratos — AI Adoption</h1>
          <p class="text-xs text-white/40">Scenario: IA Adoption Accelerator</p>
        </div>
      </div>
      <div class="text-sm text-white/50">
        Última ejecución: 2026-01-12 · Owner: Omar
      </div>
    </header>

    <div class="flex h-[calc(100vh-73px)]">
      <!-- Canvas Central -->
      <div class="flex-1 relative p-8">
        <!-- Mini-map (opcional) -->
        <div class="absolute top-8 right-8 w-48 h-32 glass-panel rounded-xl flex items-center justify-center text-xs text-white/30">
          Mini-map
        </div>

        <!-- Capability Ecosystem -->
        <CapabilityEcosystem :capabilities="capabilities" />

        <!-- Bottom Info Bar -->
        <div class="absolute bottom-8 left-8 right-96 glass-panel-strong rounded-2xl px-6 py-4 flex items-center justify-between">
          <div>
            <div class="text-xs text-white/50 mb-1">Health: 72% · Coverage</div>
            <div class="text-sm">Top risks: System Design, Kubernetes, Mentoring</div>
          </div>
          <div class="flex items-center gap-4">
            <div class="text-xs text-white/50">Horizon: 12 months</div>
            <button class="px-6 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-lg text-sm font-semibold hover:shadow-lg hover:shadow-cyan-500/50 transition-all">
              Run full analysis
            </button>
          </div>
        </div>
      </div>

      <!-- Insights Panel -->
      <aside class="w-96 glass-panel-strong border-l border-white/10 p-6 overflow-y-auto">
        <InsightsPanel :scenario="scenario" />
      </aside>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import CapabilityEcosystem from './CapabilityEcosystem.vue'
import InsightsPanel from './InsightsPanel.vue'

const scenario = ref({
  name: 'IA Adoption Accelerator',
  health: 72,
  coverage: 68,
  risk: 3
})

const capabilities = ref([
  { id: 1, name: 'System Design', level: 2, required: 4, importance: 5, x: 50, y: 40 },
  { id: 2, name: 'Data Governance', level: 3, required: 4, importance: 4, x: 30, y: 60 },
  { id: 3, name: 'MLOps', level: 2, required: 5, importance: 5, x: 70, y: 50 },
  { id: 4, name: 'Prompt Engineering', level: 4, required: 4, importance: 3, x: 40, y: 25 },
  { id: 5, name: 'Risk Analysis', level: 3, required: 3, importance: 3, x: 60, y: 70 },
  { id: 6, name: 'Change Management', level: 3, required: 4, importance: 4, x: 20, y: 35 },
])
</script>