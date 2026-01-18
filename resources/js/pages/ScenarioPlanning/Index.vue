<template>
  <div class="p-6 h-screen bg-slate-50">
    <h1 class="text-2xl font-semibold mb-4">Escenario: {{ scenario.name }}</h1>
    <div class="flex gap-4 h-[80%]">
      <div class="flex-1 relative bg-white/5 backdrop-blur-md rounded-lg p-4 shadow-sm">
        <BubbleMap :capabilities="scenario.capabilities" @select="openNode" />
      </div>
      <div class="w-80">
        <div class="bg-white/5 backdrop-blur-md rounded-lg p-4 shadow-sm">
          <h2 class="font-medium">Resumen</h2>
          <p class="text-sm text-slate-400 mt-2">{{ scenario.description }}</p>
          <hr class="my-3" />
          <div v-for="cap in scenario.capabilities" :key="cap.id" class="mb-3">
            <button @click="openNode(cap)" class="w-full text-left p-2 rounded hover:bg-white/10">
              <div class="flex justify-between">
                <div>{{ cap.name }}</div>
                <div class="text-sm text-slate-400">Imp: {{ cap.importance }}</div>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <ScenarioNodeCard v-if="selected" :capability="selected" @close="selected = null" />
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import BubbleMap from './components/BubbleMap.vue'
import ScenarioNodeCard from './components/ScenarioNodeCard.vue'
import { scenarioMock } from './mocks/scenarioMock'

const props = defineProps<{ scenario?: any }>()
const scenarioProp = computed(() => props.scenario ?? scenarioMock)
const selected = ref(null as any)

function openNode(cap: any) {
  selected.value = cap
}
</script>

<style scoped>
/* minimal scoped styles — the app uses Tailwind, keep this tiny */
</style>
