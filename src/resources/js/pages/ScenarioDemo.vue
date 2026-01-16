<template>
  <div class="p-6 min-h-screen bg-gradient-to-b from-slate-900 to-slate-800 text-white">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-2xl font-light mb-4">Scenario Canvas — Demo</h1>

      <div class="mb-4 flex gap-4 items-center">
        <button @click="reset" class="px-3 py-1 rounded bg-indigo-600 hover:bg-indigo-500">Reset layout</button>
        <div class="text-sm text-slate-300">Seleccionado: <span class="text-white">{{ selected?.id ?? '-' }}</span></div>
      </div>

      <div class="w-full h-[640px] rounded-lg shadow-lg overflow-hidden bg-[rgba(255,255,255,0.02)]">
        <ScenarioCanvas
          :width="1200"
          :height="700"
          :nodes="nodes"
          :links="links"
          containerClass="p-2"
          @select="onSelect"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import ScenarioCanvas from '@/components/ScenarioCanvas.vue'

const nodes = ref([
  { id: 'Plan', group: 1 },
  { id: 'Scope', group: 1 },
  { id: 'Roles', group: 2 },
  { id: 'Projects', group: 2 },
  { id: 'Risks', group: 3 },
  { id: 'Stakeholders', group: 3 }
])

const links = ref([
  { source: 'Plan', target: 'Scope' },
  { source: 'Plan', target: 'Roles' },
  { source: 'Scope', target: 'Projects' },
  { source: 'Roles', target: 'Risks' },
  { source: 'Stakeholders', target: 'Projects' }
])

const selected = ref(null as any)

function onSelect(node: any) {
  selected.value = node
}

function reset() {
  // reassign arrays to trigger recreation in the canvas
  nodes.value = JSON.parse(JSON.stringify(nodes.value))
  links.value = JSON.parse(JSON.stringify(links.value))
  selected.value = null
}
</script>

<style scoped>
:root {
  --card-bg: rgba(255,255,255,0.02);
}
</style>
