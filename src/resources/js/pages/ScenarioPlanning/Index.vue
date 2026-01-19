<template>
  <div class="prototype-map-root">
    <div class="map-controls" style="margin-bottom:8px; display:flex; gap:8px; align-items:center;">
      <div v-if="props.scenario && props.scenario.id">Escenario: {{ props.scenario?.name || '—' }}</div>
      <v-btn small color="primary" @click="savePositions" v-if="props.scenario && props.scenario.id">Guardar posiciones</v-btn>
      <v-btn small @click="resetPositions" v-if="nodes.length>0">Reset posiciones</v-btn>
    </div>
    <div v-if="!loaded">Cargando mapa...</div>
    <div v-else>
      <svg :width="width" :height="height" class="map-canvas" style="touch-action: none;">
        <!-- edges -->
        <g class="edges">
          <line v-for="(e, idx) in edges" :key="`edge-${idx}`" :x1="nodeById(e.source)?.x" :y1="nodeById(e.source)?.y" :x2="nodeById(e.target)?.x" :y2="nodeById(e.target)?.y" stroke="rgba(255,255,255,0.12)" stroke-width="1.5" />
        </g>

        <!-- nodes -->
        <g class="nodes">
          <g v-for="node in nodes" :key="node.id" :transform="`translate(${node.x},${node.y})`" class="node-group" @pointerdown.prevent="startDrag(node, $event)">
            <circle :r="28" fill="#1976d2" />
            <text :x="0" :y="44" text-anchor="middle" class="node-label">{{ node.name }}</text>
          </g>
        </g>
      </svg>
      <div class="cap-list" v-if="nodes.length === 0">No hay capacidades para mostrar.</div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useApi } from '@/composables/useApi'
import { useNotification } from '@/composables/useNotification'

interface NodeItem { id: number; name: string; x: number; y: number }
interface Props { scenario?: { id?: number; name?: string; capabilities?: any[]; connections?: any[] } }

const props = defineProps<Props>()
const api = useApi()
const loaded = ref(false)
const nodes = ref<Array<NodeItem>>([])
const edges = ref<Array<{ source: number; target: number }>>([])
const dragging = ref<any>(null)
const dragOffset = ref({ x: 0, y: 0 })
const { showSuccess, showError } = useNotification()
const width = 900
const height = 600

// Debug helpers removed

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

function buildEdgesFromItems(items: any[]) {
  const result: Array<{ source: number; target: number }> = []
  // support explicit connections list
  if (Array.isArray((props as any).scenario?.connections) && (props as any).scenario.connections.length > 0) {
    (props as any).scenario.connections.forEach((c: any) => {
      if (c.source != null && c.target != null) result.push({ source: c.source, target: c.target })
    })
  } else {
    // if items have linked_to or connected_to arrays
    items.forEach((it: any) => {
      if (Array.isArray(it.connected_to)) {
        it.connected_to.forEach((t: any) => {
          result.push({ source: it.id, target: t })
        })
      }
      if (Array.isArray(it.links)) {
        it.links.forEach((t: any) => result.push({ source: it.id, target: t }))
      }
    })
  }
  edges.value = result
}

function nodeById(id: number) {
  return nodes.value.find((n) => n.id === id) || null
}

function startDrag(node: any, event: PointerEvent) {
  dragging.value = node
  dragOffset.value.x = event.clientX - node.x
  dragOffset.value.y = event.clientY - node.y
  window.addEventListener('pointermove', onPointerMove)
  window.addEventListener('pointerup', onPointerUp)
}

function onPointerMove(e: PointerEvent) {
  if (!dragging.value) return
  dragging.value.x = Math.round(e.clientX - dragOffset.value.x)
  dragging.value.y = Math.round(e.clientY - dragOffset.value.y)
}

function onPointerUp() {
  if (dragging.value) {
    dragging.value = null
  }
  window.removeEventListener('pointermove', onPointerMove)
  window.removeEventListener('pointerup', onPointerUp)
}

const resetPositions = () => {
  // clear positions so layout recomputes
  nodes.value = nodes.value.map((n) => ({ ...n, x: undefined as any, y: undefined as any }))
  // rebuild with defaults using buildNodesFromItems
  if (props.scenario && Array.isArray((props.scenario as any).capabilities)) {
    buildNodesFromItems((props.scenario as any).capabilities)
  }
}

const savePositions = async () => {
  if (!props.scenario || !props.scenario.id) return showError('Escenario no seleccionado')
  try {
    const payload = {
      positions: nodes.value.map((n) => ({ id: n.id, x: n.x, y: n.y })),
    }
    await api.post(`/api/strategic-planning/scenarios/${props.scenario.id}/capability-tree/save-positions`, payload)
    showSuccess('Posiciones guardadas')
  } catch (e) {
    showError('Error al guardar posiciones')
  }
}

const loadTreeFromApi = async (scenarioId?: number) => {
  if (!scenarioId) {
    nodes.value = []
    loaded.value = true
    return
  }
  try {
    // fetch capability-tree for scenario
    const tree = await api.get(`/api/strategic-planning/scenarios/${scenarioId}/capability-tree`)
    const items = (tree as any) || []
    // capability-tree response received
    buildNodesFromItems(items)
  } catch (e) {
    // error loading capability-tree
    nodes.value = []
  } finally {
    loaded.value = true
  }
}

onMounted(() => {
  // prefer passed-in scenario.capabilities to avoid extra network roundtrip
  // onMounted: handle incoming props.scenario
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
