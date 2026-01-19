<template>
  <div class="prototype-map-root">
    <div v-if="!loaded">Cargando mapa...</div>
    <div v-else>
      <svg :width="width" :height="height" class="map-canvas">
        <g v-for="node in nodes" :key="node.id">
          <circle :cx="node.x" :cy="node.y" r="28" fill="#1976d2" />
          <text :x="node.x" :y="node.y + 44" text-anchor="middle" class="node-label">{{ node.name }}</text>
        </g>
      </svg>
      <div class="cap-list" v-if="nodes.length === 0">No hay capacidades para mostrar.</div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useApi } from '@/composables/useApi'

interface NodeItem { id: number; name: string; x: number; y: number }
interface Props { scenario?: { id?: number; capabilities?: any[] } }

const props = defineProps<Props>()
const api = useApi()
const loaded = ref(false)
const nodes = ref<Array<NodeItem>>([])
const width = 900
const height = 600

function buildNodesFromItems(items: any[]) {
  if (!Array.isArray(items) || items.length === 0) {
    nodes.value = []
    return
  }
  const centerX = width / 2
  const centerY = height / 2 - 30
  const radius = Math.min(width, height) / 3
  const angleStep = (2 * Math.PI) / items.length
  nodes.value = items.map((it: any, idx: number) => {
    // prefer provided position_x/position_y or numeric x/y, fallback to circle layout
    const rawX = it.position_x ?? it.x ?? it.cx ?? null
    const rawY = it.position_y ?? it.y ?? it.cy ?? null
    const parsedX = rawX != null ? parseFloat(String(rawX)) : NaN
    const parsedY = rawY != null ? parseFloat(String(rawY)) : NaN
    const x = !Number.isNaN(parsedX)
      ? Math.round(parsedX)
      : Math.round(centerX + radius * Math.cos(idx * angleStep))
    const y = !Number.isNaN(parsedY)
      ? Math.round(parsedY)
      : Math.round(centerY + radius * Math.sin(idx * angleStep))
    return { id: it.id, name: it.name, x, y }
  })
}

const loadTreeFromApi = async (scenarioId?: number) => {
  if (!scenarioId) {
    nodes.value = []
    loaded.value = true
    return
  }
  try {
    const tree = await api.get(`/api/strategic-planning/scenarios/${scenarioId}/capability-tree`)
    const items = (tree as any) || []
    buildNodesFromItems(items)
  } catch (e) {
    nodes.value = []
  } finally {
    loaded.value = true
  }
}

onMounted(() => {
  // prefer passed-in scenario.capabilities to avoid extra network roundtrip
  if (props.scenario && Array.isArray((props.scenario as any).capabilities) && (props.scenario as any).capabilities.length > 0) {
    buildNodesFromItems((props.scenario as any).capabilities)
    loaded.value = true
    return
  }
  // otherwise fetch capability tree from API
  void loadTreeFromApi(props.scenario?.id)
})

// react to scenario prop updates (e.g., loaded after mount)
watch(() => props.scenario, (nv) => {
  if (!nv) return
  if (Array.isArray((nv as any).capabilities) && (nv as any).capabilities.length > 0) {
    buildNodesFromItems((nv as any).capabilities)
    loaded.value = true
  } else {
    void loadTreeFromApi((nv as any).id)
  }
}, { immediate: false, deep: true })
</script>

<style scoped>
.prototype-map-root {
  padding: 16px;
}
</style>
