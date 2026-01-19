<template>
  <g>
    <!-- Linea base -->
    <line 
      :x1="source.x" 
      :y1="source.y"
      :x2="target.x" 
      :y2="target.y"
      :stroke="strokeColor"
      :stroke-width="strokeWidth"
      class="transition-all duration-300"
      :class="{ 'animate-pulse': isCritical }"
      stroke-linecap="round"
    />

    <!-- Particulas que viajan por la linea -->
    <circle
      v-if="isActive"
      :cx="particleX"
      :cy="particleY"
      r="3"
      :fill="particleColor"
      class="animate-ping opacity-75"
    />
  </g>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'

const props = defineProps({
  source: { type: Object, required: true },
  target: { type: Object, required: true },
  isCritical: { type: Boolean, default: false },
  isActive: { type: Boolean, default: false }
})

// Calcular gap promedio entre source y target
const avgGap = computed(() => {
  const sourceGap = props.source.required - props.source.level
  const targetGap = props.target.required - props.target.level
  return (sourceGap + targetGap) / 2
})

// Color dinamico segun salud
const strokeColor = computed(() => {
  if (props.isCritical || avgGap.value > 2) return 'url(#criticalLink)'
  if (avgGap.value > 1) return 'rgba(251, 191, 36, 0.4)'
  return 'url(#healthyLink)'
})

// Grosor segun importancia
const strokeWidth = computed(() => {
  const avgImportance = (props.source.importance + props.target.importance) / 2
  return props.isCritical ? 3 : 1 + (avgImportance * 0.3)
})

// Particula animada (simula flujo de talento)
const particleProgress = ref(0)
const particleX = computed(() => {
  const t = particleProgress.value
  return props.source.x + (props.target.x - props.source.x) * t
})
const particleY = computed(() => {
  const t = particleProgress.value
  return props.source.y + (props.target.y - props.source.y) * t
})
const particleColor = computed(() => avgGap.value > 2 ? '#ef4444' : '#06b6d4')

// Animar particula
onMounted(() => {
  if (props.isActive) {
    setInterval(() => {
      particleProgress.value = (particleProgress.value + 0.01) % 1
    }, 50)
  }
})
</script>
