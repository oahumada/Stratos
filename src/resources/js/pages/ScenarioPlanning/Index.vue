<template>
    <div class="prototype-map-root" ref="mapRoot">
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
            <v-btn
                small
                icon
                color="primary"
                @click="toggleSidebar"
                title="Información del escenario"
            >
                <v-icon icon="mdi-information" />
            </v-btn>
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
                    <!-- coalesce null to undefined so template typings accept Numberish | undefined -->
                    <line
                        v-for="(e, idx) in edges"
                        :key="`edge-${idx}`"
                        :x1="nodeById(e.source)?.x ?? undefined"
                        :y1="nodeById(e.source)?.y ?? undefined"
                        :x2="nodeById(e.target)?.x ?? undefined"
                        :y2="nodeById(e.target)?.y ?? undefined"
                        class="edge-line"
                    />
                </g>

                <!-- nodes -->
                <g class="nodes">
                    <!-- child edges -->
                    <g class="child-edges">
                        <line
                            v-for="(e, idx) in childEdges"
                            :key="`child-edge-${idx}`"
                            :x1="nodeById(e.source)?.x ?? (e.source < 0 ? (childNodeById(e.source)?.x ?? undefined) : undefined)"
                            :y1="nodeById(e.source)?.y ?? (e.source < 0 ? (childNodeById(e.source)?.y ?? undefined) : undefined)"
                            :x2="childNodeById(e.target)?.x ?? undefined"
                            :y2="childNodeById(e.target)?.y ?? undefined"
                            class="edge-line child-edge"
                        />
                    </g>

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
                        @click.stop="(e) => handleNodeClick(node, e)"
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

                    <!-- child nodes (competencies) -->
                    <g class="child-nodes">
                        <g
                            v-for="c in childNodes"
                            :key="c.id"
                            :transform="`translate(${c.x},${c.y})`"
                            class="node-group child-node"
                            @click.stop="(e) => handleNodeClick(c, e)"
                        >
                            <title>{{ c.name }}</title>
                            <circle
                                class="node-circle"
                                :r="20"
                                fill="#2b2b2b"
                                stroke="#ffffff"
                                stroke-opacity="0.06"
                                stroke-width="1"
                            />
                            <text :x="0" :y="28" text-anchor="middle" class="node-label" style="font-size:10px">{{ c.name }}</text>
                        </g>
                    </g>
                </g>
            </svg>

            <!-- Tooltip / details panel -->
            <transition name="fade">
                <div
                    v-if="focusedNode"
                    class="glass-panel-strong absolute z-50 max-w-sm rounded-2xl p-4"
                    :style="{ position: 'absolute', left: tooltipX + 'px', top: tooltipY + 'px' }"
                >
                    <div class="d-flex justify-space-between align-center mb-2">
                        <strong>{{ focusedNode.name }}</strong>
                        <v-btn icon small variant="text" @click="closeTooltip">
                            <v-icon icon="mdi-close" />
                        </v-btn>
                    </div>
                    <div class="text-small text-medium-emphasis mb-2">
                        <!-- If focusedNode is a competency (child node), show its attributes -->
                        <template v-if="(focusedNode as any).skills || (focusedNode as any).compId">
                            <div v-if="(focusedNode as any).description">{{ (focusedNode as any).description }}</div>
                            <div class="text-xs text-white/60">Readiness: {{ (focusedNode as any).readiness ?? '—' }}%</div>
                            <div class="mt-2 text-xs text-white/60 mb-1">Skills</div>
                            <ul class="pl-3 mb-0">
                                <li v-for="(s, idx) in (focusedNode as any).skills" :key="idx">
                                    {{ s.name }} <span class="text-white/50">(weight: {{ s.weight ?? s.pivot?.weight ?? '—' }}, readiness: {{ s.readiness ?? '—' }}%)</span>
                                </li>
                            </ul>
                        </template>

                        <!-- If focusedNode is a capability, show its competencies list -->
                        <template v-else>
                            <div v-if="(focusedNode as any).description">{{ (focusedNode as any).description }}</div>
                        </template>
                    </div>
                    <div v-if="(focusedNode as any).competencies && (focusedNode as any).competencies.length > 0">
                        <div class="text-xs text-white/60 mb-1">Competencias</div>
                        <ul class="pl-3 mb-0">
                            <li v-for="(c, i) in (focusedNode as any).competencies" :key="i">{{ c.name || c }}</li>
                        </ul>
                    </div>
                    <div v-else-if="!(focusedNode as any).skills"> 
                        <div class="text-xs text-white/50">No hay competencias registradas.</div>
                    </div>
                </div>
            </transition>
            
            <!-- Scenario side panel -->
            <transition name="slide-fade">
                <aside
                    v-if="showSidebar"
                    class="scenario-sidebar"
                    role="region"
                    aria-label="Información del escenario"
                >
                    <div class="sidebar-header d-flex justify-space-between align-center mb-4">
                        <h3 class="mb-0">Escenario</h3>
                        <v-btn icon small variant="text" @click="toggleSidebar">
                            <v-icon icon="mdi-close" />
                        </v-btn>
                    </div>

                    <div class="sidebar-body text-sm">
                        <div class="mb-2"><strong>Nombre:</strong> {{ props.scenario?.name || '—' }}</div>
                        <div v-if="props.scenario?.description" class="mb-2"><strong>Descripción:</strong> {{ props.scenario.description }}</div>
                        <div class="mb-2"><strong>ID:</strong> {{ props.scenario?.id ?? '—' }}</div>
                        <div class="mb-2"><strong>Estado:</strong> {{ props.scenario?.status ?? '—' }}</div>
                        <div class="mb-2"><strong>Año fiscal:</strong> {{ props.scenario?.fiscal_year ?? '—' }}</div>
                        <div class="mb-2"><strong>Organización:</strong> {{ props.scenario?.organization_id ?? '—' }}</div>
                        <div v-if="props.scenario?.created_at" class="mb-2"><strong>Creado:</strong> {{ props.scenario.created_at }}</div>
                        <div v-if="props.scenario?.updated_at" class="mb-2"><strong>Actualizado:</strong> {{ props.scenario.updated_at }}</div>
                        <div class="mb-2"><strong>Capacidades:</strong> {{ (props.scenario?.capabilities ?? []).length }}</div>
                    </div>
                </aside>
            </transition>
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
import { onMounted, ref, watch, onBeforeUnmount, computed } from 'vue';
import type { NodeItem, Edge, ConnectionPayload, ScenarioShape } from '@/types/brain';
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
const edges = ref<Array<Edge>>([]);
const dragging = ref<any>(null);
const dragOffset = ref({ x: 0, y: 0 });
const { showSuccess, showError } = useNotification();
const width = ref(900);
const height = ref(600);
const mapRoot = ref<HTMLElement | null>(null);
let lastItems: any[] = [];
const focusedNode = ref<NodeItem | null>(null);
const tooltipX = ref(0);
const tooltipY = ref(0);
const childNodes = ref<Array<NodeItem>>([]);
const childEdges = ref<Array<Edge>>([]);
const showSidebar = ref(false);

const toggleSidebar = () => {
    showSidebar.value = !showSidebar.value;
};

function computeInitialPosition(idx: number, total: number) {
    const centerX = width.value / 2;
    const centerY = height.value / 2 - 30;
    const sx = Math.min(240, width.value / 4); // horizontal spacing
    const sy = Math.min(140, height.value / 4); // vertical spacing

    if (total === 1) {
        return { x: Math.round(centerX), y: Math.round(centerY) };
    }
    if (total === 2) {
        return {
            x: Math.round(centerX + (idx === 0 ? -sx / 2 : sx / 2)),
            y: Math.round(centerY),
        };
    }
    if (total === 3) {
        if (idx === 0) return { x: Math.round(centerX), y: Math.round(centerY - sy) };
        return {
            x: Math.round(centerX + (idx === 1 ? -sx / 1.6 : sx / 1.6)),
            y: Math.round(centerY + sy / 1.2),
        };
    }
    if (total === 4) {
        const cols = [-sx / 2, sx / 2];
        const rows = [-sy / 2, sy / 2];
        const col = idx % 2;
        const row = Math.floor(idx / 2);
        return { x: Math.round(centerX + cols[col]), y: Math.round(centerY + rows[row]) };
    }
    // even split into two columns for small even totals (6,8,10)
    if (total % 2 === 0 && total <= 10) {
        const half = total / 2;
        const col = idx < half ? -1 : 1;
        const posInCol = idx < half ? idx : idx - half;
        const spacing = Math.min((half - 1) * 40, 200);
        const startY = centerY - spacing / 2;
        const y = Math.round(startY + posInCol * (spacing / Math.max(half - 1, 1)));
        return { x: Math.round(centerX + col * sx / 1.5), y };
    }
    // fallback: circle
    const angle = ((2 * Math.PI) / total) * idx;
    const r = Math.min(width.value, height.value) / 3;
    return { x: Math.round(centerX + r * Math.cos(angle)), y: Math.round(centerY + r * Math.sin(angle)) };
}

// Debug helpers removed

function buildNodesFromItems(items: any[]) {
    if (!Array.isArray(items) || items.length === 0) {
        nodes.value = [];
        return;
    }
    // prefer provided position_x/position_y or numeric x/y; if missing leave undefined so force layout can compute
    const centerX = width.value / 2;
    const centerY = height.value / 2 - 30;
    const radius = Math.min(width.value, height.value) / 3;
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
        const fallbackPos = computeInitialPosition(idx, items.length);
        const fallbackX = Math.round(fallbackPos.x);
        const fallbackY = Math.round(fallbackPos.y);
        return {
            id: Number(it.id),
            name: it.name,
            x: x ?? fallbackX,
            y: y ?? fallbackY,
            _hasCoords: hasPos,
            is_critical: it.is_critical ?? it.isCritical ?? false,
            description: it.description ?? it.desc ?? null,
            competencies: Array.isArray(it.competencies)
                ? it.competencies
                : Array.isArray(it.competency)
                ? it.competency
                : [],
            importance: it.importance ?? it.rank ?? null,
            level: it.level ?? null,
            required: it.required ?? null,
            raw: it,
        } as any;
    });
    nodes.value = mapped.map((m: any) => ({
        id: m.id,
        name: m.name,
        x: m.x,
        y: m.y,
        is_critical: !!m.is_critical,
        description: m.description,
        competencies: m.competencies,
        importance: m.importance,
        level: m.level,
        required: m.required,
    }));
    // build edges before attempting a force layout
    buildEdgesFromItems(items);
    // if any node originally lacked real coordinates, run a short force layout to compute nicer positions
    const missing = mapped.some((m: any) => !m._hasCoords);
    if (missing) runForceLayout();
    // store last items so we can recompute layout on resize
    lastItems = items;
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
            .force('center', (d3 as any).forceCenter(width.value / 2, height.value / 2));

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
    const result: Edge[] = [];
    // support explicit connections list from scenario.connections
    const conns = (props as any).scenario?.connections;
    if (Array.isArray(conns) && conns.length > 0) {
        (conns as ConnectionPayload[]).forEach((c) => {
            const s = c.source ?? c.source_id ?? null;
            const t = c.target ?? c.target_id ?? null;
            if (s != null && t != null) {
                result.push({ source: Number(s), target: Number(t), isCritical: !!c.is_critical });
            }
        });
    } else {
        // if items have linked_to, connected_to, or links arrays
        items.forEach((it: any) => {
            if (Array.isArray(it.connected_to)) {
                it.connected_to.forEach((t: any) => {
                    result.push({ source: Number(it.id), target: Number(t) });
                });
            }
            if (Array.isArray(it.links)) {
                it.links.forEach((t: any) =>
                    result.push({ source: Number(it.id), target: Number(t) }),
                );
            }
            // support nested connections on item (source/target or source_id/target_id)
            if (Array.isArray(it.connections)) {
                it.connections.forEach((c: ConnectionPayload) => {
                    const s = c.source ?? c.source_id ?? null;
                    const t = c.target ?? c.target_id ?? null;
                    if (s != null && t != null)
                        result.push({ source: Number(s), target: Number(t), isCritical: !!c.is_critical });
                });
            }
        });
    }
    edges.value = result;
}

function nodeById(id: number) {
    return nodes.value.find((n) => n.id === id) || null;
}

function childNodeById(id: number) {
    return childNodes.value.find((n) => n.id === id) || null;
}

const handleNodeClick = (node: NodeItem, event?: MouseEvent) => {
    console.log('[PrototypeMap] clicked node', node);
    // focus node and compute tooltip position relative to svg
    focusedNode.value = node;
    // expand competencies as child nodes (TheBrain style)
    expandCompetencies(node);
    // if mouse event provided, use client coords; else derive from node position
    if (event) {
        tooltipX.value = event.clientX + 12;
        tooltipY.value = event.clientY + 12;
    } else {
        tooltipX.value = (node.x ?? 0) + 12;
        tooltipY.value = (node.y ?? 0) + 12;
    }
};

const closeTooltip = () => {
    focusedNode.value = null;
    childNodes.value = [];
    childEdges.value = [];
};

function expandCompetencies(node: NodeItem) {
    childNodes.value = [];
    childEdges.value = [];
    const comps = (node as any).competencies ?? [];
    if (!Array.isArray(comps) || comps.length === 0) return;
    const angleStep = (2 * Math.PI) / comps.length;
    const radius = 90; // distance from parent
    const cx = node.x ?? width / 2;
    const cy = node.y ?? height / 2;
    comps.forEach((c: any, i: number) => {
        const angle = i * angleStep;
        const x = Math.round(cx + radius * Math.cos(angle));
        const y = Math.round(cy + radius * Math.sin(angle));
        // create a unique negative id to avoid collision with real nodes
        const id = -(node.id * 1000 + i + 1);
        // preserve competency attributes so tooltip can show them
        childNodes.value.push({
            id,
            compId: c.id ?? null,
            name: c.name ?? c,
            x,
            y,
            is_critical: false,
            description: c.description ?? null,
            readiness: c.readiness ?? null,
            skills: Array.isArray(c.skills) ? c.skills : [],
            raw: c,
        });
        childEdges.value.push({ source: node.id, target: id });
    });
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

    // initialize responsive sizing and observe container
    const el = mapRoot.value as HTMLElement | null;
    const applySize = (w?: number, h?: number) => {
        const computedWidth = w ?? el?.clientWidth ?? 900;
        // compute available height inside the container: prefer container height if available,
        // otherwise use viewport remaining space below the container's top.
        let containerHeight = el?.clientHeight ?? 0;
        if (!containerHeight || containerHeight === 0) {
            const top = el?.getBoundingClientRect().top ?? 0;
            containerHeight = Math.max(320, Math.round(window.innerHeight - top - 24));
        }
        const controlsEl = el?.querySelector('.map-controls') as HTMLElement | null;
        const controlsH = controlsEl?.offsetHeight ?? 0;
        const computedHeight = h ?? Math.max(300, containerHeight - controlsH - 12);

        width.value = computedWidth;
        height.value = computedHeight;

        // if we have previously loaded items, rebuild positions to adapt
        if (Array.isArray(lastItems) && lastItems.length > 0) {
            buildNodesFromItems(lastItems);
            buildEdgesFromItems(lastItems);
        }
    };
    applySize();
    let ro: ResizeObserver | null = null;
    if (el && (window as any).ResizeObserver) {
        ro = new ResizeObserver((entries: any) => {
            for (const entry of entries) {
                const w = Math.round(entry.contentRect.width);
                const h = Math.round(entry.contentRect.height);
                applySize(w, h);
            }
        });
        ro.observe(el);
    }
    const onWindowResize = () => applySize();
    window.addEventListener('resize', onWindowResize);
    onBeforeUnmount(() => {
        if (ro) ro.disconnect();
        window.removeEventListener('resize', onWindowResize);
    });
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
    position: relative;
    /* ensure there's room for the sidebar to anchor against */
    min-height: 420px;
    display: flex;
    flex-direction: column;
}

/* sidebar styles - anchored to the right */
.scenario-sidebar {
    position: absolute;
    right: 16px;
    top: 16px;
    bottom: 16px;
    width: 340px;
    max-width: calc(100% - 48px);
    background: rgba(18, 24, 32, 0.95);
    color: #fff;
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 12px 40px rgba(2,6,23,0.6);
    z-index: 60;
    overflow: auto;
}
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 240ms ease;
}
.slide-fade-enter-from {
    transform: translateX(12px);
    opacity: 0;
}
.slide-fade-enter-to {
    transform: translateX(0);
    opacity: 1;
}
.slide-fade-leave-from {
    transform: translateX(0);
    opacity: 1;
}
.slide-fade-leave-to {
    transform: translateX(12px);
    opacity: 0;
}

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

/* make SVG canvas expand to available space */
.map-canvas {
    display: block;
    width: 100%;
    height: 100%;
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

/* hover scale removed to disable hover animation */

.node-circle {
    transition:
        r 0.12s ease,
        filter 0.12s ease,
        transform 0.12s ease;
}

.child-node .node-circle {
    fill: #1f2937;
}
.child-edge {
    stroke: rgba(200,200,200,0.12);
    stroke-dasharray: 2 3;
}

.node-inner {
    pointer-events: none;
}

</style>
