<template>
    <div class="brain-canvas relative h-full w-full overflow-hidden">
        <!-- SVG Layer para las conexiones -->
        <svg class="pointer-events-none absolute inset-0 z-0 h-full w-full">
            <defs>
                <linearGradient
                    id="healthyLink"
                    x1="0%"
                    y1="0%"
                    x2="100%"
                    y2="0%"
                >
                    <stop
                        offset="0%"
                        style="
                            stop-color: rgba(6, 182, 212, 0.4);
                            stop-opacity: 1;
                        "
                    />
                    <stop
                        offset="100%"
                        style="
                            stop-color: rgba(139, 92, 246, 0.4);
                            stop-opacity: 1;
                        "
                    />
                </linearGradient>
                <linearGradient
                    id="criticalLink"
                    x1="0%"
                    y1="0%"
                    x2="100%"
                    y2="0%"
                >
                    <stop
                        offset="0%"
                        style="
                            stop-color: rgba(239, 68, 68, 0.6);
                            stop-opacity: 1;
                        "
                    />
                    <stop
                        offset="100%"
                        style="
                            stop-color: rgba(251, 146, 60, 0.6);
                            stop-opacity: 1;
                        "
                    />
                </linearGradient>
            </defs>

            <!-- Lineas de conexion dinamicas -->
            <line
                v-for="link in links"
                :key="`link-${link.source.id}-${link.target.id}`"
                :x1="link.source.x"
                :y1="link.source.y"
                :x2="link.target.x"
                :y2="link.target.y"
                :stroke="getLinkStroke(link)"
                :stroke-width="getLinkWidth(link)"
                class="transition-all duration-300"
                :class="{ 'animate-pulse': link.isCritical }"
            />
        </svg>

        <!-- Capa de Nodos -->
        <div
            v-for="node in nodes"
            :key="node.id"
            class="absolute z-10 transition-all duration-100"
            :style="{
                left: node.x + 'px',
                top: node.y + 'px',
                transform: 'translate(-50%, -50%)',
                zIndex: node.isFocused ? 50 : 10,
            }"
            @click="handleNodeClick(node)"
        >
            <CapabilityNode
                :capability="node"
                :class="{ 'scale-125 shadow-2xl': node.isFocused }"
            />
        </div>

        <!-- Tooltip flotante -->
        <transition name="fade">
            <div
                v-if="focusedNode"
                class="glass-panel-strong absolute z-50 max-w-sm rounded-2xl p-6 shadow-2xl"
                :style="{ left: tooltipX + 'px', top: tooltipY + 'px' }"
            >
                <h3 class="mb-2 text-lg font-bold">{{ focusedNode.name }}</h3>
                <p class="mb-4 text-sm text-white/70">
                    {{ focusedNode.description }}
                </p>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="glass-panel rounded-lg p-2">
                        <div class="text-white/50">Nivel Actual</div>
                        <div class="text-lg font-bold text-cyan-400">
                            N{{ focusedNode.level }}
                        </div>
                    </div>
                    <div class="glass-panel rounded-lg p-2">
                        <div class="text-white/50">Requerido</div>
                        <div class="text-lg font-bold text-purple-400">
                            R{{ focusedNode.required }}
                        </div>
                    </div>
                </div>

                <button
                    @click="openCapabilityDetail(focusedNode)"
                    class="mt-4 w-full rounded-lg bg-gradient-to-r from-cyan-500 to-blue-500 py-2 text-sm font-semibold"
                >
                    Ver Plan de Desarrollo
                </button>
            </div>
        </transition>
    </div>
</template>

<script setup>
import * as d3 from 'd3';
import { computed, onMounted, ref, watch } from 'vue';
import CapabilityNode from './CapabilityNode.vue';

const props = defineProps({
    capabilities: { type: Array, required: true },
    connections: { type: Array, default: () => [] },
});

const emit = defineEmits(['nodeClick']);

const nodes = ref([]);
const links = ref([]);
const focusedNode = ref(null);
const simulation = ref(null);
const containerWidth = ref(0);
const containerHeight = ref(0);

const tooltipX = computed(() => {
    if (!focusedNode.value) return 0;
    return Math.min(focusedNode.value.x + 180, containerWidth.value - 400);
});

const tooltipY = computed(() => {
    if (!focusedNode.value) return 0;
    return Math.max(focusedNode.value.y - 100, 100);
});

const initializeNodes = () => {
    containerWidth.value = window.innerWidth;
    containerHeight.value = window.innerHeight - 73;

    nodes.value = props.capabilities.map((cap) => ({
        ...cap,
        x: cap.x || Math.random() * containerWidth.value,
        y: cap.y || Math.random() * containerHeight.value,
        isFocused: false,
        fx: null,
        fy: null,
    }));
};

const initializeLinks = () => {
    if (props.connections.length > 0) {
        links.value = props.connections.map((c) => ({
            source: nodes.value.find((n) => n.id === c.source_id),
            target: nodes.value.find((n) => n.id === c.target_id),
            isCritical: c.is_critical || false,
        }));
    }
};

const createSimulation = () => {
    simulation.value = d3
        .forceSimulation(nodes.value)
        .force(
            'link',
            d3
                .forceLink(links.value)
                .id((d) => d.id)
                .distance(150),
        )
        .force(
            'charge',
            d3.forceManyBody().strength((d) => -300 - d.importance * 50),
        )
        .force(
            'center',
            d3.forceCenter(containerWidth.value / 2, containerHeight.value / 2),
        )
        .force(
            'collision',
            d3.forceCollide().radius((d) => 60 + d.importance * 15),
        )
        .alphaDecay(0.01)
        .on('tick', () => {
            nodes.value = [...nodes.value];
        });
};

const handleNodeClick = (node) => {
    nodes.value.forEach((n) => (n.isFocused = false));
    node.isFocused = true;
    focusedNode.value = node;

    simulation.value.alphaTarget(0.3).restart();
    node.fx = containerWidth.value / 2;
    node.fy = containerHeight.value / 2;

    setTimeout(() => {
        node.fx = null;
        node.fy = null;
        simulation.value.alphaTarget(0);
    }, 2000);

    emit('nodeClick', node);
};

const getLinkStroke = (link) => {
    const gap = link.target.required - link.target.level;
    return gap > 2 ? 'url(#criticalLink)' : 'url(#healthyLink)';
};

const getLinkWidth = (link) => {
    return link.isCritical ? 3 : 1.5;
};

const openCapabilityDetail = (node) => {
    emit('nodeClick', node);
};

onMounted(() => {
    initializeNodes();
    initializeLinks();
    createSimulation();
});

watch(
    () => props.capabilities,
    () => {
        initializeNodes();
        initializeLinks();
        createSimulation();
    },
    { deep: true },
);
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition:
        opacity 0.3s,
        transform 0.3s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
