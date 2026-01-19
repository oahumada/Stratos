<template>
    <div class="prototype-map-root">
        <div
            class="map-controls"
            style="
                margin-bottom: 8px;
                display: flex;
                gap: 8px;
                align-items: center;
            "
        >
            <div v-if="props.scenario && props.scenario.id">
                Escenario: {{ props.scenario?.name || '—' }}
            </div>
            <v-btn
                small
                color="primary"
                @click="savePositions"
                v-if="props.scenario && props.scenario.id"
                >Guardar posiciones</v-btn
            >
            <v-btn small @click="resetPositions" v-if="nodes.length > 0"
                >Reset posiciones</v-btn
            >
        </div>
        <div v-if="!loaded">Cargando mapa...</div>
        <div v-else>
            <svg
                :width="width"
                :height="height"
                :viewBox="`0 0 ${width} ${height}`"
                class="map-canvas"
                style="touch-action: none"
            >
                <defs>
                    <linearGradient id="bgGrad" x1="0" y1="0" x2="1" y2="1">
                        <stop
                            offset="0%"
                            stop-color="#071029"
                            stop-opacity="1"
                        />
                        <stop
                            offset="100%"
                            stop-color="#0b1f33"
                            stop-opacity="1"
                        />
                    </linearGradient>

                    <radialGradient id="nodeGrad" cx="45%" cy="30%" r="70%">
                        <stop
                            offset="0%"
                            stop-color="#ffffff"
                            stop-opacity="0.18"
                        />
                        <stop
                            offset="40%"
                            stop-color="#6fc3ff"
                            stop-opacity="0.95"
                        />
                        <stop
                            offset="100%"
                            stop-color="#0b66b2"
                            stop-opacity="1"
                        />
                    </radialGradient>

                    <filter
                        id="softGlow"
                        x="-50%"
                        y="-50%"
                        width="200%"
                        height="200%"
                    >
                        <feGaussianBlur stdDeviation="6" result="blur" />
                        <feMerge>
                            <feMergeNode in="blur" />
                            <feMergeNode in="SourceGraphic" />
                        </feMerge>
                    </filter>

                    <filter
                        id="innerShadow"
                        x="-20%"
                        y="-20%"
                        width="140%"
                        height="140%"
                    >
                        <feOffset dx="0" dy="2" result="off" />
                        <feGaussianBlur
                            in="off"
                            stdDeviation="2"
                            result="blur2"
                        />
                        <feComposite
                            in="SourceGraphic"
                            in2="blur2"
                            operator="over"
                        />
                    </filter>
                </defs>

                <!-- subtle background rect for contrast -->
                <rect
                    x="0"
                    y="0"
                    :width="width"
                    :height="height"
                    fill="url(#bgGrad)"
                />

                <!-- edges -->
                <g class="edges">
                    <line
                        v-for="(e, idx) in edges"
                        :key="`edge-${idx}`"
                        :x1="nodeById(e.source)?.x"
                        :y1="nodeById(e.source)?.y"
                        :x2="nodeById(e.target)?.x"
                        :y2="nodeById(e.target)?.y"
                        class="edge-line"
                    />
                </g>

                <!-- nodes -->
                <g class="nodes">
                    <g
                        v-for="node in nodes"
                        :key="node.id"
                        :transform="`translate(${node.x},${node.y})`"
                        class="node-group"
                        :class="{
                            critical: !!node.is_critical,
                            focused: dragging && dragging.id === node.id,
                        }"
                        @pointerdown.prevent="startDrag(node, $event)"
                    >
                        <title>{{ node.name }}</title>
                        <circle
                            class="node-circle"
                            :r="34"
                            fill="url(#nodeGrad)"
                            filter="url(#softGlow)"
                            stroke="#ffffff"
                            stroke-opacity="0.06"
                            stroke-width="1"
                        />
                        <circle
                            v-if="node.is_critical"
                            class="node-inner"
                            :r="12"
                            fill="#ff5050"
                            fill-opacity="0.95"
                        />
                        <text
                            :x="0"
                            :y="46"
                            text-anchor="middle"
                            class="node-label"
                        >
                            {{ node.name }}
                        </text>
                    </g>
                </g>
            </svg>
            <div class="cap-list" v-if="nodes.length === 0">
                No hay capacidades para mostrar.
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import * as d3 from 'd3';
import { onMounted, ref, watch } from 'vue';

interface NodeItem {
    id: number;
    name: string;
    x: number;
    y: number;
    is_critical?: boolean;
}
interface Props {
    scenario?: {
        id?: number;
        name?: string;
        capabilities?: any[];
        connections?: any[];
    };
}

const props = defineProps<Props>();
const api = useApi();
const loaded = ref(false);
const nodes = ref<Array<NodeItem>>([]);
const edges = ref<Array<{ source: number; target: number }>>([]);
const dragging = ref<any>(null);
const dragOffset = ref({ x: 0, y: 0 });
const { showSuccess, showError } = useNotification();
const width = 900;
const height = 600;

// Debug helpers removed

function buildNodesFromItems(items: any[]) {
    if (!Array.isArray(items) || items.length === 0) {
        nodes.value = [];
        return;
    }
    // prefer provided position_x/position_y or numeric x/y; if missing leave undefined so force layout can compute
    const centerX = width / 2;
    const centerY = height / 2 - 30;
    const radius = Math.min(width, height) / 3;
    const angleStep = (2 * Math.PI) / items.length;
    const mapped = items.map((it: any, idx: number) => {
        const rawX = it.position_x ?? it.x ?? it.cx ?? null;
        const rawY = it.position_y ?? it.y ?? it.cy ?? null;
        const parsedX = rawX != null ? parseFloat(String(rawX)) : NaN;
        const parsedY = rawY != null ? parseFloat(String(rawY)) : NaN;
        const hasPos = !Number.isNaN(parsedX) && !Number.isNaN(parsedY);
        const x = hasPos ? Math.round(parsedX) : undefined;
        const y = hasPos ? Math.round(parsedY) : undefined;
        // if missing position, place roughly on a circle initially (helps force start) but mark undefined so we can re-run force
        const fallbackX = Math.round(
            centerX + radius * Math.cos(idx * angleStep),
        );
        const fallbackY = Math.round(
            centerY + radius * Math.sin(idx * angleStep),
        );
        return {
            id: it.id,
            name: it.name,
            x: x ?? fallbackX,
            y: y ?? fallbackY,
            _hasCoords: hasPos,
            is_critical: it.is_critical ?? it.isCritical ?? false,
        };
    });
    nodes.value = mapped.map((m: any) => ({
        id: m.id,
        name: m.name,
        x: m.x,
        y: m.y,
        is_critical: !!m.is_critical,
    }));
    // build edges before attempting a force layout
    buildEdgesFromItems(items);
    // if any node originally lacked real coordinates, run a short force layout to compute nicer positions
    const missing = mapped.some((m: any) => !m._hasCoords);
    if (missing) runForceLayout();
}

function runForceLayout() {
    try {
        // prepare mutable nodes/links for simulation
        const simNodes = nodes.value.map((n) => ({
            id: n.id,
            x: n.x || 0,
            y: n.y || 0,
        }));
        const simLinks = edges.value.map((l) => ({
            source: l.source,
            target: l.target,
        }));
        const simulation = d3
            .forceSimulation(simNodes as any)
            .force(
                'link',
                (d3 as any)
                    .forceLink(simLinks)
                    .id((d: any) => d.id)
                    .distance(120)
                    .strength(0.5),
            )
            .force('charge', (d3 as any).forceManyBody().strength(-220))
            .force('center', (d3 as any).forceCenter(width / 2, height / 2));

        // run a fixed number of synchronous ticks to stabilise layout
        for (let i = 0; i < 300; i++) simulation.tick();
        simulation.stop();

        const pos = new Map(
            simNodes.map((n: any) => [
                n.id,
                { x: Math.round(n.x), y: Math.round(n.y) },
            ]),
        );
        nodes.value = nodes.value.map((n) => {
            const p = pos.get(n.id);
            return { id: n.id, name: n.name, x: p?.x ?? n.x, y: p?.y ?? n.y };
        });
    } catch (err) {
        // if simulation fails, silently skip (fallback positions already set)
        // console.warn('[PrototypeMap] force layout failed', err)
    }
}

function buildEdgesFromItems(items: any[]) {
    const result: Array<{ source: number; target: number }> = [];
    // support explicit connections list
    if (
        Array.isArray((props as any).scenario?.connections) &&
        (props as any).scenario.connections.length > 0
    ) {
        (props as any).scenario.connections.forEach((c: any) => {
            if (c.source != null && c.target != null)
                result.push({ source: c.source, target: c.target });
        });
    } else {
        // if items have linked_to or connected_to arrays
        items.forEach((it: any) => {
            if (Array.isArray(it.connected_to)) {
                it.connected_to.forEach((t: any) => {
                    result.push({ source: it.id, target: t });
                });
            }
            if (Array.isArray(it.links)) {
                it.links.forEach((t: any) =>
                    result.push({ source: it.id, target: t }),
                );
            }
        });
    }
    edges.value = result;
}

function nodeById(id: number) {
    return nodes.value.find((n) => n.id === id) || null;
}

function startDrag(node: any, event: PointerEvent) {
    dragging.value = node;
    dragOffset.value.x = event.clientX - node.x;
    dragOffset.value.y = event.clientY - node.y;
    window.addEventListener('pointermove', onPointerMove);
    window.addEventListener('pointerup', onPointerUp);
}

function onPointerMove(e: PointerEvent) {
    if (!dragging.value) return;
    dragging.value.x = Math.round(e.clientX - dragOffset.value.x);
    dragging.value.y = Math.round(e.clientY - dragOffset.value.y);
}

function onPointerUp() {
    if (dragging.value) {
        dragging.value = null;
    }
    window.removeEventListener('pointermove', onPointerMove);
    window.removeEventListener('pointerup', onPointerUp);
}

const resetPositions = () => {
    // clear positions so layout recomputes
    nodes.value = nodes.value.map((n) => ({
        ...n,
        x: undefined as any,
        y: undefined as any,
    }));
    // rebuild with defaults using buildNodesFromItems
    if (props.scenario && Array.isArray((props.scenario as any).capabilities)) {
        buildNodesFromItems((props.scenario as any).capabilities);
    }
};

const savePositions = async () => {
    if (!props.scenario || !props.scenario.id)
        return showError('Escenario no seleccionado');
    try {
        const payload = {
            positions: nodes.value.map((n) => ({ id: n.id, x: n.x, y: n.y })),
        };
        await api.post(
            `/api/strategic-planning/scenarios/${props.scenario.id}/capability-tree/save-positions`,
            payload,
        );
        showSuccess('Posiciones guardadas');
    } catch (e) {
        showError('Error al guardar posiciones');
    }
};

const loadTreeFromApi = async (scenarioId?: number) => {
    if (!scenarioId) {
        nodes.value = [];
        loaded.value = true;
        return;
    }
    try {
        // fetch capability-tree for scenario
        const tree = await api.get(
            `/api/strategic-planning/scenarios/${scenarioId}/capability-tree`,
        );
        const items = (tree as any) || [];
        // capability-tree response received
        buildNodesFromItems(items);
        // ensure edges are rebuilt from the fetched items
        buildEdgesFromItems(items);
    } catch (e) {
        // error loading capability-tree
        nodes.value = [];
    } finally {
        loaded.value = true;
    }
};

onMounted(() => {
    // prefer passed-in scenario.capabilities to avoid extra network roundtrip
    // onMounted: handle incoming props.scenario
    if (
        props.scenario &&
        Array.isArray((props.scenario as any).capabilities) &&
        (props.scenario as any).capabilities.length > 0
    ) {
        const caps = (props.scenario as any).capabilities;
        buildNodesFromItems(caps);
        buildEdgesFromItems(caps);
        loaded.value = true;
        return;
    }
    // otherwise fetch capability tree from API
    void loadTreeFromApi(props.scenario?.id);
});

// react to scenario prop updates (e.g., loaded after mount)
watch(
    () => props.scenario,
    (nv) => {
        if (!nv) return;
        if (
            Array.isArray((nv as any).capabilities) &&
            (nv as any).capabilities.length > 0
        ) {
            const caps = (nv as any).capabilities;
            buildNodesFromItems(caps);
            buildEdgesFromItems(caps);
            loaded.value = true;
        } else {
            void loadTreeFromApi((nv as any).id);
        }
    },
    { immediate: false, deep: true },
);

// ensure edges exist even if no capabilities loaded yet (avoids template warnings)
if (!edges.value) edges.value = [];
</script>

<style scoped>
.prototype-map-root {
    padding: 16px;
}
</style>

<style scoped>
.edges line {
    transition:
        x1 0.08s linear,
        y1 0.08s linear,
        x2 0.08s linear,
        y2 0.08s linear;
}

/* visual improvements */
.edge-line {
    stroke: rgba(255, 255, 255, 0.12);
    stroke-width: 1.5;
    transition:
        stroke 0.12s ease,
        stroke-width 0.12s ease,
        opacity 0.12s ease;
}

.node-group {
    cursor: grab;
    transition: transform 0.12s ease;
    transform-box: fill-box;
    transform-origin: center;
}
.node-group:active {
    cursor: grabbing;
}

/* scale slightly on hover */
.node-group:hover {
    transform: scale(1.04);
}

.node-circle {
    transition:
        r 0.12s ease,
        filter 0.12s ease,
        transform 0.12s ease;
}

.node-inner {
    pointer-events: none;
}

.node-label {
    fill: #ffffff;
    font-weight: 600;
    font-size: 12px;
    pointer-events: none;
    paint-order: stroke;
    stroke: rgba(0, 0, 0, 0.35);
    stroke-width: 0.6px;
}

/* critical pulse */
@keyframes pulse {
    0% {
        transform: scale(1);
        filter: drop-shadow(0 0 0 rgba(255, 0, 0, 0));
    }
    50% {
        transform: scale(1.08);
        filter: drop-shadow(0 0 12px rgba(255, 60, 60, 0.65));
    }
    100% {
        transform: scale(1);
        filter: drop-shadow(0 0 0 rgba(255, 0, 0, 0));
    }
}
.node-group.critical .node-circle {
    animation: pulse 1.6s infinite;
    stroke: rgba(255, 80, 80, 0.95);
    stroke-width: 2px;
}
</style>
