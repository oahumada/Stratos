<template>
  <div>
    <div class="mb-3 font-medium">{{ path.name }}</div>
    <div class="space-y-3">
      <div v-for="action in sortedActions" :key="action.order" class="p-3 rounded bg-white/3 flex items-start justify-between border-l-4" :style="{ borderColor: borderColor(action.status) }">
        <div>
          <div class="font-semibold">{{ action.order }}. {{ action.title }}</div>
          <div class="text-xs text-slate-400">Tipo: {{ action.type }} — Impacto: {{ Math.round((action.impact_weight || 0) * 100) }}%</div>
        </div>
        <div class="text-right">
          <div class="text-sm mb-2">Estado</div>
          <div class="text-sm font-medium" :class="statusClass(action.status)">{{ action.status }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{ path: any }>()

const sortedActions = computed(() => (props.path.actions || []).slice().sort((a: any, b: any) => (a.order || 0) - (b.order || 0)))

function statusClass(s: string) {
  if (s === 'completed') return 'text-emerald-400'
  if (s === 'in_progress') return 'text-amber-300'
  if (s === 'cancelled') return 'text-red-400'
  return 'text-slate-300'
}
</script>

<style scoped>
</style>
