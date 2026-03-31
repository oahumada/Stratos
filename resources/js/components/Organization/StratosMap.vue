<script setup lang="ts">
import { useElementSize } from '@vueuse/core';
import * as d3 from 'd3';
import { onMounted, onUnmounted, ref, watch } from 'vue';

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

const props = withDefaults(
    defineProps<{
        nodes: Node[];
        links: Link[];
        mode: 'gravitational' | 'cerberos';
        loading?: boolean;
    }>(),
    {
        loading: false,
    },
);

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
        person: ['#10b981', '#3b82f6'], // Emerald to Blue
    },
};

const initGraph = () => {
    console.log(
        '[D3 Map] initGraph called. nodes length:',
        props.nodes.length,
        'svgRef:',
        !!svgRef.value,
    );
    if (!svgRef.value || props.nodes.length === 0) return;

    // Limpiar previo
    d3.select(svgRef.value).selectAll('*').remove();

    if (width.value === 0 || height.value === 0) {
        console.warn('[D3 Map] Container size is 0, waiting for next tick...');
        setTimeout(initGraph, 100);
        return;
    }

    svg = d3.select(svgRef.value);
    g = svg.append('g');

    // Configurar Zoom
    const zoom = d3
        .zoom<SVGSVGElement, unknown>()
        .scaleExtent([0.1, 4])
        .on('zoom', (event) => {
            g?.attr('transform', event.transform);
        });

    svg.call(zoom);

    // Definir Filtros y Gradientes
    const defs = svg.append('defs');

    // Sombras y Luces
    const glow = defs
        .append('filter')
        .attr('id', 'node-glow')
        .attr('x', '-50%')
        .attr('y', '-50%')
        .attr('width', '200%')
        .attr('height', '200%');
    glow.append('feGaussianBlur')
        .attr('stdDeviation', 8)
        .attr('result', 'coloredBlur');
    const feMerge = glow.append('feMerge');
    feMerge.append('feMergeNode').attr('in', 'coloredBlur');
    feMerge.append('feMergeNode').attr('in', 'SourceGraphic');

    // Gradiente dinámico
    const gradient = defs
        .append('linearGradient')
        .attr('id', 'node-gradient')
        .attr('x1', '0%')
        .attr('y1', '0%')
        .attr('x2', '100%')
        .attr('y2', '100%');

    gradient
        .append('stop')
        .attr('offset', '0%')
        .attr(
            'stop-color',
            props.mode === 'gravitational'
                ? config.nodeColors.department[0]
                : config.nodeColors.person[0],
        );
    gradient
        .append('stop')
        .attr('offset', '100%')
        .attr(
            'stop-color',
            props.mode === 'gravitational'
                ? config.nodeColors.department[1]
                : config.nodeColors.person[1],
        );

    // Crear Escalas con defensas
    const validNodes = props.nodes.map((n) => ({
        ...n,
        mass:
            typeof n.mass === 'number' && !isNaN(n.mass)
                ? Math.max(n.mass, 1)
                : 1,
    }));

    if (validNodes.length === 0) {
        console.warn('[D3 Map] No valid nodes to render');
        return;
    }

    const massExtent = d3.extent(validNodes, (d) => d.mass) as [number, number];
    const minMass = massExtent[0] ?? 1;
    const maxMass = massExtent[1] ?? 1;

    const radiusScale = d3
        .scaleSqrt()
        .domain([0, Math.max(maxMass, 5)])
        .range([config.minRadius, config.maxRadius]);

    // Simulación de Fuerza
    // Clonamos los datos para evitar que D3 ensucie los props reactivos de Vue
    const nodesData = JSON.parse(JSON.stringify(validNodes));
    const linksData = JSON.parse(JSON.stringify(props.links || []));

    // Layout Inicial (Específico para Cerberos)
    nodesData.forEach((node: any) => {
        const centerX = width.value / 2 || 400;
        const centerY = height.value / 2 || 300;
        const dist = 220;

        // Si es Cerberos, aplicamos layout direccional
        if (props.mode === 'cerberos') {
            const rel = linksData.find(
                (l: any) => l.source.id === node.id || l.target.id === node.id,
            );

            // Lógica de "Orbita Evaluativa"
            if (node.id === props.nodes[0]?.id) {
                // Asumimos el primero como foco si no hay ID (se puede mejorar)
                node.fx = centerX;
                node.fy = centerY;
                node.isFocus = true;
            } else {
                // Posicionar según relación con el foco (Simplificado)
                const linkToFocus = linksData.find(
                    (l: any) =>
                        (l.source.id === node.id &&
                            l.target.id === props.nodes[0]?.id) ||
                        (l.target.id === node.id &&
                            l.source.id === props.nodes[0]?.id),
                );

                if (linkToFocus) {
                    if (
                        linkToFocus.type === 'supervisor' &&
                        linkToFocus.target.id === props.nodes[0]?.id
                    ) {
                        // Es el JEFE (Arriba)
                        node.x = centerX;
                        node.y = centerY - dist;
                    } else if (
                        linkToFocus.type === 'supervisor' &&
                        linkToFocus.source.id === props.nodes[0]?.id
                    ) {
                        // Es un COLABORADOR (Abajo)
                        node.x = centerX;
                        node.y = centerY + dist;
                    } else {
                        // Es un PAR (Lados)
                        const index = nodesData
                            .filter((n: any) => n.id !== props.nodes[0]?.id)
                            .indexOf(node);
                        node.x = centerX + (index % 2 === 0 ? dist : -dist);
                        node.y = centerY + (Math.random() - 0.5) * 50;
                    }
                }
            }
        } else {
            // Layout normal para Gravitational (Dagre)
            node.x = (width.value / 2 || 300) + (Math.random() - 0.5) * 200;
            node.y = (height.value / 2 || 300) + (Math.random() - 0.5) * 200;
        }
    });

    const chargeStrength = props.mode === 'cerberos' ? -2500 : -1000;
    const linkDistance = props.mode === 'cerberos' ? 250 : 180;

    console.log(
        `[D3 Map] Initializing Engine. Nodes: ${nodesData.length}, Links: ${linksData.length}`,
    );

    simulation = d3
        .forceSimulation<Node>(nodesData)
        .force(
            'link',
            d3
                .forceLink<Node, Link>(linksData)
                .id((d) => d.id)
                .distance((d: any) =>
                    d.type === 'supervisor' ? linkDistance : linkDistance * 0.8,
                )
                .strength(0.5),
        )
        .force('charge', d3.forceManyBody().strength(chargeStrength))
        .force(
            'center',
            d3.forceCenter(width.value / 2 || 400, height.value / 2 || 300),
        )
        .force(
            'collision',
            d3.forceCollide().radius((d: any) => radiusScale(d.mass || 1) + 50),
        )
        .alphaDecay(0.05);

    simulation.alpha(1).restart();

    // Marcadores para flechas
    defs.append('marker')
        .attr('id', 'arrowhead')
        .attr('viewBox', '0 -5 10 10')
        .attr('refX', 45) // Ajustado para alejarse más del centro (radio promedio)
        .attr('refY', 0)
        .attr('markerWidth', 7)
        .attr('markerHeight', 7)
        .attr('orient', 'auto')
        .append('path')
        .attr('d', 'M0,-5L10,0L0,5')
        .attr('fill', 'rgba(16, 185, 129, 0.6)'); // Un poco más sólido

    // Dibujar Enlaces
    const link = g
        .append('g')
        .attr('class', 'links')
        .selectAll('line')
        .data(linksData)
        .enter()
        .append('line')
        .attr('stroke', (d: any) => {
            if (d.type === 'supervisor') return 'rgba(16, 185, 129, 0.4)'; // Más opaco
            if (d.type === 'peer') return 'rgba(59, 130, 246, 0.3)';
            return 'rgba(255,255,255,0.2)';
        })
        .attr('stroke-width', (d: any) => (d.type === 'supervisor' ? 2 : 1))
        .attr('stroke-dasharray', (d: any) =>
            d.type === 'supervisor' ? 'none' : '5,5',
        )
        .attr('marker-end', (d: any) =>
            d.type === 'supervisor' ? 'url(#arrowhead)' : null,
        );

    // Dibujar Nodos
    const node = g
        .append('g')
        .attr('class', 'nodes')
        .selectAll('g')
        .data(nodesData)
        .enter()
        .append('g')
        .attr('cursor', 'pointer')
        .on('click', (event, d) => emit('node-click', d))
        .call(
            d3
                .drag<SVGGElement, any>()
                .on('start', dragstarted)
                .on('drag', dragged)
                .on('end', dragended) as any,
        );

    // Fondo del nodo (Glass effect)
    node.append('circle')
        .attr('r', (d) => radiusScale(d.mass))
        .attr('fill', 'rgba(255, 255, 255, 0.03)')
        .attr('stroke', 'rgba(255, 255, 255, 0.1)')
        .attr('stroke-width', 1)
        .attr('backdrop-filter', 'blur(10px)');

    // Círculo central (Masa)
    node.append('circle')
        .attr('r', (d) => radiusScale(d.mass) * 0.7)
        .attr('fill', 'url(#node-gradient)')
        .attr('opacity', (d) => ((d as any).isFocus ? 1 : 0.8))
        .attr('stroke', (d) => ((d as any).isFocus ? 'white' : 'none'))
        .attr('stroke-width', 2)
        .style('filter', 'url(#node-glow)');

    // Icono central
    node.append('text')
        .attr('text-anchor', 'middle')
        .attr('dy', '0.35em')
        .attr('font-size', (d) => radiusScale(d.mass) * 0.8 + 'px') // MUCHO MÁS GRANDE
        .attr('fill', 'white')
        .attr('opacity', 0.9)
        .style('pointer-events', 'none')
        .text(props.mode === 'gravitational' ? '🏢' : '👤');

    // Label del nodo con multi-línea
    const labels = node
        .append('text')
        .attr('dy', (d) => radiusScale(d.mass) + 20)
        .attr('text-anchor', 'middle')
        .attr('fill', 'white')
        .attr('font-size', '11px')
        .attr('font-weight', '500')
        .attr(
            'style',
            'text-shadow: 0 2px 4px rgba(0,0,0,0.8); pointer-events: none;',
        );

    labels.each(function (d) {
        const el = d3.select(this);
        const name = d.name || 'N/A';
        const words = name.split(/\s+/);
        if (words.length > 2 && name.length > 20) {
            const mid = Math.ceil(words.length / 2);
            el.append('tspan')
                .text(words.slice(0, mid).join(' '))
                .attr('x', 0)
                .attr('dy', '1.2em');
            el.append('tspan')
                .text(words.slice(mid).join(' '))
                .attr('x', 0)
                .attr('dy', '1.2em');
        } else {
            el.append('tspan').text(name).attr('x', 0).attr('dy', '1.2em');
        }
    });

    // Update positions
    simulation.on('tick', () => {
        link.attr('x1', (d) => (d.source as Node).x!)
            .attr('y1', (d) => (d.source as Node).y!)
            .attr('x2', (d) => (d.target as Node).x!)
            .attr('y2', (d) => (d.target as Node).y!);

        node.attr('transform', (d) => `translate(${d.x},${d.y})`);
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
watch([width, height, props.nodes, props.links, () => props.mode], () => {
    initGraph();
});

onMounted(() => {
    console.log('[D3 Map] Component Mounted. Nodes:', props.nodes.length);
    setTimeout(initGraph, 300);
});

onUnmounted(() => {
    simulation?.stop();
});
</script>

<template>
    <div
        ref="container"
        class="stratos-map-container relative h-full w-full overflow-hidden"
    >
        <!-- Overlay Loading -->
        <div
            v-if="loading"
            class="absolute inset-0 z-10 flex items-center justify-center bg-black/20 backdrop-blur-sm"
        >
            <div
                class="h-12 w-12 animate-spin rounded-full border-4 border-indigo-500/30 border-t-indigo-500"
            ></div>
        </div>

        <svg ref="svgRef" class="h-full w-full"></svg>

        <!-- UI Controls (Opcional) -->
        <div class="absolute right-6 bottom-6 flex flex-col gap-2">
            <div
                class="glass-control rounded-lg border border-white/10 bg-black/40 p-2 backdrop-blur-md"
            >
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
