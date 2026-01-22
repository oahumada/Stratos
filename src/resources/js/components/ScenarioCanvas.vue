<template>
    <div ref="container" :class="['w-full', 'h-full', containerClass]">
        <svg
            ref="svg"
            :width="width"
            :height="height"
            class="h-full w-full"
        ></svg>
    </div>
</template>

<script setup lang="ts">
import * as d3 from 'd3';
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';

interface NodeDatum {
    id: string | number;
    group?: number;
    fx?: number | null;
    fy?: number | null;
}

interface LinkDatum {
    source: string | number;
    target: string | number;
    value?: number;
}

const props = defineProps<{
    width?: number;
    height?: number;
    nodes?: NodeDatum[];
    links?: LinkDatum[];
    containerClass?: string;
}>();

const emit = defineEmits(['select']);

const width = props.width ?? 1200;
const height = props.height ?? 700;
const nodes = props.nodes ?? [
    { id: 'A', group: 1 },
    { id: 'B', group: 1 },
    { id: 'C', group: 2 },
    { id: 'D', group: 2 },
    { id: 'E', group: 3 },
];
const links = props.links ?? [
    { source: 'A', target: 'B' },
    { source: 'A', target: 'C' },
    { source: 'B', target: 'D' },
    { source: 'C', target: 'E' },
];

const svg = ref<SVGSVGElement | null>(null);
const container = ref<HTMLDivElement | null>(null);
let simulation: d3.Simulation<any, undefined> | null = null;

function create() {
    if (!svg.value) return;

    const s = d3.select(svg.value);
    s.selectAll('*').remove();

    // defs: glow filter + radial gradients
    const defs = s.append('defs');

    const glow = defs.append('filter').attr('id', 'glow');
    glow.append('feGaussianBlur')
        .attr('stdDeviation', 6)
        .attr('result', 'coloredBlur');
    const feMerge = glow.append('feMerge');
    feMerge.append('feMergeNode').attr('in', 'coloredBlur');
    feMerge.append('feMergeNode').attr('in', 'SourceGraphic');

    const color = d3.scaleOrdinal<string>(d3.schemeTableau10);

    const g = s.attr('viewBox', `0 0 ${width} ${height}`).append('g');

    const link = g
        .append('g')
        .attr('stroke', 'rgba(255,255,255,0.12)')
        .attr('stroke-width', 1.5)
        .selectAll('line')
        .data(links)
        .join('line');

    const node = g.append('g').selectAll('g').data(nodes).join('g');

    // circle + outer glow
    node
        .append('g')
        .attr('class', 'node-group')
        .call((sel) =>
            sel
                .append('circle')
                .attr('r', 12)
                .attr('fill', (d: any) => color((d as any).group))
                .attr('stroke', 'rgba(255,255,255,0.6)')
                .attr('stroke-width', 1),
        );

    // soft halo
    node.append('circle')
        .attr('r', 24)
        .attr('fill', 'none')
        .attr('class', 'halo')
        .style('filter', 'url(#glow)')
        .style('stroke', (d: any) => color((d as any).group))
        .style('stroke-width', 4)
        .style('opacity', 0.45);

    // labels
    node.append('text')
        .text((d: any) => d.id)
        .attr('x', 16)
        .attr('y', 4)
        .attr('fill', 'rgba(255,255,255,0.85)')
        .attr('font-size', 12);

    // zoom + pan
    s.call(
        d3
            .zoom<SVGSVGElement, unknown>()
            .scaleExtent([0.5, 2])
            .on('zoom', (event) => {
                g.attr('transform', event.transform.toString());
            }),
    );

    // simulation
    simulation = d3
        .forceSimulation(nodes as any)
        .force(
            'link',
            d3
                .forceLink(links as any)
                .id((d: any) => d.id)
                .distance(120)
                .strength(0.4),
        )
        .force('charge', d3.forceManyBody().strength(-220))
        .force('center', d3.forceCenter(width / 2, height / 2))
        .on('tick', () => {
            link.attr('x1', (d: any) => (d.source as any).x)
                .attr('y1', (d: any) => (d.source as any).y)
                .attr('x2', (d: any) => (d.target as any).x)
                .attr('y2', (d: any) => (d.target as any).y);

            node.attr('transform', (d: any) => `translate(${d.x},${d.y})`);
        });

    // drag behavior
    const drag = d3
        .drag<SVGElement, any>()
        .on('start', (event, d) => {
            if (!event.active && simulation)
                simulation.alphaTarget(0.3).restart();
            d.fx = d.x;
            d.fy = d.y;
        })
        .on('drag', (event, d) => {
            d.fx = event.x;
            d.fy = event.y;
        })
        .on('end', (event, d) => {
            if (simulation) simulation.alphaTarget(0);
            d.fx = null;
            d.fy = null;
        });

    node.call(drag as any);

    // click selection
    node.on('click', (event: any, d: any) => {
        emit('select', d);
    });
}

onMounted(() => {
    create();
});

onBeforeUnmount(() => {
    if (simulation) {
        simulation.stop();
        simulation = null;
    }
});

watch(
    () => [props.nodes, props.links],
    () => {
        // re-create when data changes
        create();
    },
    { deep: true },
);
</script>

<style scoped>
.node-group > circle {
    transition: r 0.12s ease;
}
.node-group:hover > circle {
    r: 16px;
}
svg {
    background: linear-gradient(
        180deg,
        rgba(6, 10, 20, 0.6),
        rgba(12, 16, 28, 0.9)
    );
    border-radius: 12px;
    overflow: visible;
}
</style>
