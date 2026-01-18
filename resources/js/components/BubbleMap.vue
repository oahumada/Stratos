<template>
  <div class="relative w-full h-full" ref="container">
    <div v-for="cap in capabilities" :key="cap.id" :style="bubbleStyle(cap)" class="absolute flex items-center justify-center cursor-pointer" @click="$emit('select', cap)" @mouseenter="hover = cap" @mouseleave="hover = null">
      <div :class="['rounded-full flex items-center justify-center text-white font-medium shadow-lg', colorClass(cap), cap.gap >= 3 ? 'pulse-alert' : '']" :style="{ width: size(cap) + 'px', height: size(cap) + 'px', background: gradient(cap) }">
        {{ cap.name.split(' ').map(s=>s[0]).join('').slice(0,3) }}
      </div>
    </div>

    <div v-if="hover" class="absolute z-20 pointer-events-none" :style="tooltipStyle(hover)">
      <div class="bg-white/8 backdrop-blur-md text-sm text-white p-2 rounded shadow-lg max-w-xs">
        <div class="font-semibold">{{ hover.name }}</div>
        <div class="text-xs text-slate-200">Importancia: {{ hover.importance }} — Gap: {{ hover.gap }}</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
const props = defineProps<{ capabilities: any[] }>()
const container = ref<HTMLElement | null>(null)
const hover = ref<any | null>(null)

function size(cap: any) {
  return 34 + cap.importance * 12
}

function gradient(cap: any) {
  // subtle glass gradient overlay
  if (cap.gap >= 3) return 'linear-gradient(135deg,#f97316,#ef4444)'
  if (cap.gap === 2) return 'linear-gradient(135deg,#fb923c,#f97316)'
  if (cap.gap === 1) return 'linear-gradient(135deg,#f59e0b,#fbbf24)'
  if (cap.gap === 0) return 'linear-gradient(135deg,#94a3b8,#64748b)'
  return 'linear-gradient(135deg,#10b981,#059669)'
}

function colorClass(cap: any) {
  return 'text-white'
}

function bubbleStyle(cap: any) {
  // position_x and position_y treated as percentages
  return {
    left: `calc(${cap.position_x}% - ${size(cap)/2}px)`,
    top: `calc(${cap.position_y}% - ${size(cap)/2}px)`
  }
}

function tooltipStyle(cap: any) {
  return {
    left: `calc(${cap.position_x}% + 12%)`,
    top: `calc(${cap.position_y}% - 4%)`
  }
}
</script>

<style scoped>
.rounded-full { transition: transform .18s ease, box-shadow .18s ease; border: 1px solid rgba(255,255,255,0.06); }
.rounded-full:hover { transform: scale(1.08); box-shadow: 0 8px 24px rgba(2,6,23,0.6); }
.pulse-alert { animation: pulse 1.6s infinite; }
@keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(239,68,68,0.4); } 70% { box-shadow: 0 0 0 12px rgba(239,68,68,0); } 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); } }

/* Tooltip adjustments */
.pointer-events-none { pointer-events: none; }

</style>
