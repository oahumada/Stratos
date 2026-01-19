<script setup>
import { Head } from '@inertiajs/vue3'
import CapabilityEcosystem from './BrainCanvas.vue'
import InsightsPanel from './InsightsPanel.vue'
const props = defineProps({
    scenario: Object,
    capabilities: Array,
    connections: Array  // Nueva prop para las conexiones
})

const handleNodeClick = (node) => {
  console.log('Capacidad seleccionada:', node)
  // Aquí puedes abrir un modal, navegar a detalle, etc.
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-stratos-darker via-stratos-dark to-blue-950">
    <!-- Header existente -->

    <div class="flex h-[calc(100vh-73px)]">
      <!-- Canvas Central con el Cerebro -->
      <main class="flex-1 relative overflow-hidden">
        <BrainCanvas 
          :capabilities="capabilities" 
          :connections="connections"
          @node-click="handleNodeClick"
        />
      </main>

      <!-- Panel lateral existente -->
      <aside class="w-96 bg-black/20 backdrop-blur-md p-6 overflow-y-auto">
        <InsightsPanel />
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