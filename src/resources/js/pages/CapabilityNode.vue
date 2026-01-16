<template>
  <div 
    class="absolute transform -translate-x-1/2 -translate-y-1/2 cursor-pointer transition-all duration-500 hover:scale-110 group"
    :style="{ width: nodeSize, height: nodeSize }"
  >
    <!-- Glow exterior -->
    <div 
      class="absolute inset-0 rounded-full blur-2xl opacity-30 animate-pulse"
      :class="glowColor"
    ></div>
    
    <!-- Nodo principal -->
    <div 
      class="relative w-full h-full rounded-full flex items-center justify-center border-2 transition-all duration-300"
      :class="[borderColor, bgColor, 'backdrop-blur-xl']"
    >
      <!-- Contenido -->
      <div class="text-center px-4">
        <div class="text-xs font-semibold text-white/90">{{ capability.name }}</div>
        <div class="text-[10px] text-white/50 mt-1">
          Gap: {{ gap }} levels · Confidence: {{ confidence }}%
        </div>
      </div>

      <!-- Tooltip on hover -->
      <div class="absolute -bottom-16 left-1/2 transform -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
        <div class="glass-panel-strong rounded-lg px-3 py-2 text-xs whitespace-nowrap">
          <div>Current: <span class="text-cyan-400">N{{ capability.level }}</span></div>
          <div>Required: <span class="text-purple-400">R{{ capability.required }}</span></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps(['capability'])

const gap = computed(() => props.capability.required - props.capability.level)
const confidence = computed(() => Math.max(50, 100 - (gap.value * 15)))

const nodeSize = computed(() => {
  const base = 120
  const scale = props.capability.importance * 20
  return `${base + scale}px`
})

const borderColor = computed(() => {
  if (gap.value <= 0) return 'border-cyan-400/60'
  if (gap.value === 1) return 'border-teal-400/60'
  if (gap.value === 2) return 'border-yellow-400/60'
  return 'border-red-400/60'
})

const bgColor = computed(() => {
  if (gap.value <= 0) return 'bg-cyan-500/10'
  if (gap.value === 1) return 'bg-teal-500/10'
  if (gap.value === 2) return 'bg-yellow-500/10'
  return 'bg-red-500/10'
})

const glowColor = computed(() => {
  if (gap.value <= 0) return 'bg-cyan-400'
  if (gap.value === 1) return 'bg-teal-400'
  if (gap.value === 2) return 'bg-yellow-400'
  return 'bg-red-400'
})
</script>