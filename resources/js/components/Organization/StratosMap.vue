<script setup lang="ts">
import { ref, onMounted, watch, onUnmounted } from 'vue';
import * as d3 from 'd3';
import { useElementSize } from '@vueuse/core';

interface Node extends d3.SimulationNodeDatum {
    id: string | number;
    name: string;
    type: 'department' | 'person';
    mass: number; // headcount or influence
    value: number; // payroll or skills level
    parentId?: string | number;
    color?: string;
}

interface Link extends d3.SimulationLinkDatum<Node> {
    source: string | number | Node;
    target: string | number | Node;
    value: number;
}

const props = withDefaults(defineProps<{
    nodes: Node[];
    links: Link[];
    mode: 'gravitational' | 'cerberos';
    loading?: boolean;
}>(), {
    loading: false
});

const emit = defineEmits(['node-click']);

const container = ref<HTMLElement | null>(null);
const { width, height } = useElementSize(container);
const svgRef = ref<SVGSVGElement | null>(null);

let simulation: d3.Simulation<Node, Link> | null = null;
let svg: d3.Selection<SVGSVGElement, unknown, null, undefined> | null = null;
let g: d3.Selection<SVGGElement, unknown, null, undefined> | null = null;

// Configuración de visualización
const config = {
    minRadius: 20,
    maxRadius: 80,
    nodeColors: {
        department: ['#6366f1', '#a855f7'], // Indigo to Purple
        person: ['#10b981', '#3b82f6'],     // Emerald to Blue
    }
};

const initGraph = () => {
    if (!svgRef.value || props.nodes.length === 0) return;

    // Limpiar previo
    d3.select(svgRef.value).selectAll("*").remove();

    svg = d3.select(svgRef.value);
    g = svg.append("g");

    // Configurar Zoom
    const zoom = d3.zoom<SVGSVGElement, unknown>()
        .scaleExtent([0.1, 4])
        .on("zoom", (event) => {
            g?.attr("transform", event.transform);
        });

    svg.call(zoom);

    // Definir filtros Glass y Gradientes
    const defs = svg.append("defs");

    // Filtro de brillo/desenfoque
    const filter = defs.append("filter")
        .attr("id", "node-glow")
        .attr("x", "-50%")
        .attr("y", "-50%")
        .attr("width", "200%")
        .attr("height", "200%");

    filter.append("feGaussianBlur")
        .attr("stdDeviation", "8")
        .attr("result", "blur");

    filter.append("feComposite")
        .attr("in", "SourceGraphic")
        .attr("in2", "blur")
        .attr("operator", "over");

    // Gradientes para nodos
    const gradient = defs.append("linearGradient")
        .attr("id", "node-gradient")
        .attr("x1", "0%").attr("y1", "0%")
        .attr("x2", "100%").attr("y2", "100%");

    gradient.append("stop")
        .attr("offset", "0%")
        .attr("stop-color", config.nodeColors.department[0]);

    gradient.append("stop")
        .attr("offset", "100%")
        .attr("stop-color", config.nodeColors.department[1]);

    // Crear Escalas
    const massExtent = d3.extent(props.nodes, d => d.mass) as [number, number];
    const radiusScale = d3.scaleSqrt()
        .domain(massExtent[0] === massExtent[1] ? [0, massExtent[0]] : massExtent)
        .range([config.minRadius, config.maxRadius]);

    // Simulación de Fuerza
    simulation = d3.forceSimulation<Node>(props.nodes)
        .force("link", d3.forceLink<Node, Link>(props.links).id(d => d.id).distance(150))
        .force("charge", d3.forceManyBody().strength(-300))
        .force("center", d3.forceCenter(width.value / 2, height.value / 2))
        .force("collision", d3.forceCollide().radius(d => radiusScale(d.mass) + 10));

    // Dibujar Enlaces (Líneas de fuerza)
    const link = g.append("g")
        .attr("class", "links")
        .selectAll("line")
        .data(props.links)
        .enter().append("line")
        .attr("stroke", "rgba(255,255,255,0.05)")
        .attr("stroke-width", 1.5)
        .attr("stroke-dasharray", "4,4");

    // Dibujar Nodos
    const node = g.append("g")
        .attr("class", "nodes")
        .selectAll("g")
        .data(props.nodes)
        .enter().append("g")
        .attr("cursor", "pointer")
        .on("click", (event, d) => emit('node-click', d))
        .call(d3.drag<SVGGElement, Node>()
            .on("start", dragstarted)
            .on("drag", dragged)
            .on("end", dragended) as any);

    // Fondo del nodo (Glass effect)
    node.append("circle")
        .attr("r", d => radiusScale(d.mass))
        .attr("fill", "rgba(255, 255, 255, 0.03)")
        .attr("stroke", "rgba(255, 255, 255, 0.1)")
        .attr("stroke-width", 1)
        .attr("backdrop-filter", "blur(10px)");

    // Círculo central (Masa)
    node.append("circle")
        .attr("r", d => radiusScale(d.mass) * 0.7)
        .attr("fill", "url(#node-gradient)")
        .attr("opacity", 0.6)
        .style("filter", "url(#node-glow)");

    // Label del nodo
    node.append("text")
        .text(d => d.name)
        .attr("dy", d => radiusScale(d.mass) + 20)
        .attr("text-anchor", "middle")
        .attr("fill", "white")
        .attr("font-size", "12px")
        .attr("font-weight", "500")
        .attr("style", "text-shadow: 0 2px 4px rgba(0,0,0,0.5)");

    // Update positions
    simulation.on("tick", () => {
        link
            .attr("x1", d => (d.source as Node).x!)
            .attr("y1", d => (d.source as Node).y!)
            .attr("x2", d => (d.target as Node).x!)
            .attr("y2", d => (d.target as Node).y!);

        node
            .attr("transform", d => `translate(${d.x},${d.y})`);
    });
};

// Drag handlers
function dragstarted(event: any, d: any) {
    if (!event.active) simulation?.alphaTarget(0.3).restart();
    d.fx = d.x;
    d.fy = d.y;
}

function dragged(event: any, d: any) {
    d.fx = event.x;
    d.fy = event.y;
}

function dragended(event: any, d: any) {
    if (!event.active) simulation?.alphaTarget(0);
    d.fx = null;
    d.fy = null;
}

// Watchers
watch([width, height, props.nodes, props.links], () => {
    initGraph();
});

onMounted(() => {
    setTimeout(initGraph, 100);
});

onUnmounted(() => {
    simulation?.stop();
});
</script>

<template>
    <div ref="container" class="stratos-map-container relative w-full h-full overflow-hidden">
        <!-- Overlay Loading -->
        <div v-if="loading" class="absolute inset-0 z-10 flex items-center justify-center bg-black/20 backdrop-blur-sm">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-500/30 border-t-indigo-500"></div>
        </div>

        <svg ref="svgRef" class="w-full h-full"></svg>

        <!-- UI Controls (Opcional) -->
        <div class="absolute bottom-6 right-6 flex flex-col gap-2">
            <div class="glass-control p-2 rounded-lg border border-white/10 bg-black/40 backdrop-blur-md">
                <slot name="controls"></slot>
            </div>
        </div>
    </div>
</template>

<style scoped>
.stratos-map-container {
    background: radial-gradient(circle at center, #0f172a 0%, #020617 100%);
    border-radius: 24px;
}

.glass-control {
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
}

:deep(line) {
    transition: stroke 0.3s ease;
}

:deep(.nodes g:hover circle:first-child) {
    stroke: rgba(255, 255, 255, 0.4);
    fill: rgba(255, 255, 255, 0.08);
}
</style>
