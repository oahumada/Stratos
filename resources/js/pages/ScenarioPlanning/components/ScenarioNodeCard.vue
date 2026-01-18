<template>
  <div class="fixed inset-0 flex items-center justify-center z-50">
    <div class="absolute inset-0 bg-black/40" @click="$emit('close')"></div>
    <div class="modal-panel bg-white/6 backdrop-blur-md rounded-lg p-6 w-[720px] max-w-[95%] shadow-xl relative">
      <button class="absolute top-3 right-3 text-slate-200" @click="$emit('close')">✕</button>
      <h3 class="text-xl font-semibold mb-2">{{ capability.name }}</h3>
        <div class="text-sm text-slate-400 mb-4">Importancia: {{ capability.importance }} — Gap: {{ capability.gap }}</div>

          <div v-for="skill in capability.skills" :key="skill.id" class="mb-4 bg-white/3 p-3 rounded">
        <div class="flex justify-between items-start">
          <div>
            <div class="font-medium">{{ skill.name }}</div>
            <div class="text-xs text-slate-400">Requerido: {{ skill.required }} — Actual: {{ skill.current }}</div>
          </div>
          <div class="w-40">
            <input type="range" min="1" max="5" v-model.number="localValues[skill.id]" />
            <div class="text-xs mt-1 text-slate-300">Nivel seleccionado: {{ localValues[skill.id] }}</div>
          </div>
        </div>

        <div class="mt-3">
          <div class="text-sm font-semibold mb-1">BARS</div>
          <div class="text-sm text-slate-200 p-2 bg-black/10 rounded">
            {{ skill.bars[ localValues[skill.id] - 1 ] }}
          </div>
        </div>
      </div>

      <div class="mt-4">
        <div class="mb-2 font-medium">Development Path</div>
        <div v-if="path">
          <DevelopmentPathStepper :path="path" />
        </div>
        <div v-else>
          <button class="px-3 py-2 rounded bg-blue-600 text-white" @click="generatePath">Generar Path (stub)</button>
        </div>
      </div>

      <div class="flex justify-end gap-2 mt-4">
        <button class="px-3 py-2 rounded bg-slate-600 text-white" @click="$emit('close')">Cerrar</button>
        <button class="px-3 py-2 rounded bg-emerald-500 text-white">Guardar (stub)</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, watchEffect, ref } from 'vue'
import DevelopmentPathStepper from './DevelopmentPathStepper.vue'
import { createMockPathFromCapability } from '../mocks/developmentPathMock'

const props = defineProps<{ capability: any }>()
const emit = defineEmits(['close'])

const localValues = reactive<Record<number, number>>({})
const path = ref(null as any)

// init local sliders
if (props.capability && props.capability.skills) {
  for (const s of props.capability.skills) {
    localValues[s.id] = s.current || 1
  }
}

function generatePath() {
  path.value = createMockPathFromCapability(props.capability)
}

watchEffect(() => {
  // if capability changes, reset
  if (props.capability) {
    path.value = null
    for (const s of props.capability.skills) {
      localValues[s.id] = s.current || 1
    }
  }
})
</script>

<style scoped>
.modal-panel { animation: modal-in .18s cubic-bezier(.2,.9,.3,1); border: 1px solid rgba(255,255,255,0.06); }

input[type="range"] { width: 100%; accent-color: #06b6d4; }

@keyframes modal-in {
  from { transform: translateY(6px) scale(.995); opacity: 0 }
  to { transform: translateY(0) scale(1); opacity: 1 }
}

.bg-white\/3 { background-color: rgba(255,255,255,0.03); }

</style>
