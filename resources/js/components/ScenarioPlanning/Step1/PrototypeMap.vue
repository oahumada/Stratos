<script setup lang="ts">
import * as d3 from 'd3';
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import dagre from 'dagre';

const props = defineProps<{
    scenario: any;
}>();

const emit = defineEmits(['node-click', 'edit-node']);

const container = ref<HTMLDivElement | null>(null);
const svg = ref<SVGSVGElement | null>(null);
let simulation: d3.Simulation<any, undefined> | null = null;

const allNodes = ref<any[]>([]);
const allLinks = ref<any[]>([]);
const nodes = ref<any[]>([]);
const links = ref<any[]>([]);
const width = ref(900);
const height = ref(600);

const prepareData = () => {
    if (!props.scenario) return;

    const rootId = 'scenario-' + props.scenario.id;
    const root = {
        id: rootId,
        name: props.scenario.name,
        type: 'scenario',
        group: 0,
        expanded: true,
        importance: 5,
        x: width.value / 2,
        y: height.value / 2,
    };

    const newNodes: any[] = [root];
    const newLinks: any[] = [];

    if (Array.isArray(props.scenario.capabilities)) {
        props.scenario.capabilities.forEach((cap: any) => {
            const capId = 'cap-' + cap.id;
            const capNode = {
                ...cap,
                id: capId,
                parentId: rootId,
                type: 'capability',
                group: 1,
                expanded: false,
                importance: cap.importance || 3,
            };
            newNodes.push(capNode);
            newLinks.push({
                source: rootId,
                target: capId,
                value: 1,
            });

            if (Array.isArray(cap.competencies)) {
                cap.competencies.forEach((comp: any) => {
                    const compId = 'comp-' + comp.id;
                    const compNode = {
                        ...comp,
                        id: compId,
                        parentId: capId,
                        type: 'competency',
                        group: 2,
                        expanded: false,
                        importance: 1,
                    };
                    newNodes.push(compNode);
                    newLinks.push({
                        source: capId,
                        target: compId,
                        value: 0.8,
                    });

                    if (Array.isArray(comp.skills)) {
                        comp.skills.forEach((skill: any) => {
                            const skillId =
                                'skill-' + (skill.id || Math.random());
                            const skillNode = {
                                ...skill,
                                id: skillId,
                                parentId: compId,
                                type: 'skill',
                                group: 3,
                                expanded: false,
                                importance: 0.5,
                            };
                            newNodes.push(skillNode);
                            newLinks.push({
                                source: compId,
                                target: skillId,
                                value: 0.5,
                            });
                        });
                    }
                });
            }
        });
    }

    allNodes.value = newNodes;
    allLinks.value = newLinks;
    updateVisibleData();
};

const updateVisibleData = () => {
    if (allNodes.value.length === 0) return;

    const visibleNodeIds = new Set<string>();
    // Definimos la raíz explícitamente por tipo
    const root = allNodes.value.find((n) => n.type === 'scenario');
    if (!root) return;

    const stack = [root];

    while (stack.length > 0) {
        const current = stack.pop();
        if (!current) continue;

        visibleNodeIds.add(current.id);

        // Si el nodo está expandido, sus hijos directos son elegibles para ser visibles
        if (current.expanded) {
            allNodes.value.forEach((node) => {
                if (node.parentId === current.id) {
                    if (!visibleNodeIds.has(node.id)) {
                        // Inherit position if not set to avoid (0,0) jump
                        if (node.x === undefined) node.x = current.x;
                        if (node.y === undefined) node.y = current.y;
                        stack.push(node);
                    }
                }
            });
        }
    }

    nodes.value = allNodes.value.filter((n) => visibleNodeIds.has(n.id));
    links.value = allLinks.value.filter((l) => {
        const sourceId = typeof l.source === 'object' ? l.source.id : l.source;
        const targetId = typeof l.target === 'object' ? l.target.id : l.target;
        return visibleNodeIds.has(sourceId) && visibleNodeIds.has(targetId);
    });

    // --- Aplicar Dagre para Layout Inicial ---
    const dagreGraph = new dagre.graphlib.Graph();
    dagreGraph.setDefaultEdgeLabel(() => ({}));
    dagreGraph.setGraph({ rankdir: 'LR', nodesep: 40, ranksep: 100 });

    nodes.value.forEach(node => {
        dagreGraph.setNode(node.id, { width: 40, height: 40 });
    });

    links.value.forEach(link => {
        const s = typeof link.source === 'object' ? link.source.id : link.source;
        const t = typeof link.target === 'object' ? link.target.id : link.target;
        dagreGraph.setEdge(s, t);
    });

    dagre.layout(dagreGraph);

    nodes.value.forEach(node => {
        const dNode = dagreGraph.node(node.id);
        if (dNode && node.x === undefined) {
            node.x = dNode.x + width.value / 4;
            node.y = dNode.y + height.value / 4;
        }
    });
    // ------------------------------------------

    if (simulation) {
        simulation.nodes(nodes.value);
        const linkForce: any = simulation.force('link');
        if (linkForce) linkForce.links(links.value);
        simulation.alpha(0.3).restart();
    }
    render();
};

const toggleNode = (event: any, d: any) => {
    if (!d) return;
    event.stopPropagation();

    // Toggle state directly on the object held by D3 and find it in allNodes
    d.expanded = !d.expanded;
    const nodeToToggle = allNodes.value.find((n) => n.id === d.id);
    if (nodeToToggle) nodeToToggle.expanded = d.expanded;

    // Si cerramos un nodo, colapsamos recursivamente todos sus descendientes
    if (nodeToToggle && !nodeToToggle.expanded) {
        const recursiveCollapse = (parentId: string) => {
            allNodes.value.forEach((node) => {
                if (node.parentId === parentId) {
                    node.expanded = false;
                    recursiveCollapse(node.id);
                }
            });
        };
        recursiveCollapse(nodeToToggle.id);
    }

    updateVisibleData();
};

const render = () => {
    if (!svg.value) return;
    const s = d3.select(svg.value);
    const g = s.select('g');

    // Update Links
    const link = g
        .select('.links')
        .selectAll('line')
        .data(
            links.value,
            (d: any) => `${d.source.id || d.source}-${d.target.id || d.target}`,
        )
        .join(
            (enter) =>
                enter
                    .append('line')
                    .attr('stroke', 'rgba(255,255,255,0.05)')
                    .attr('stroke-width', (d: any) => (d.value === 1 ? 2 : 1))
                    .attr('stroke-dasharray', (d: any) =>
                        d.value <= 0.5 ? '2,2' : 'none',
                    )
                    .call((enter) =>
                        enter
                            .transition()
                            .duration(500)
                            .attr('stroke', 'rgba(255,255,255,0.15)'),
                    ),
            (update) => update,
            (exit) =>
                exit
                    .transition()
                    .duration(300)
                    .attr('stroke-width', 0)
                    .remove(),
        );

    // Update Nodes
    const node = g
        .select('.nodes')
        .selectAll('g.node-group')
        .data(nodes.value, (d: any) => d.id)
        .join(
            (enter) => {
                const nodeGroup = enter
                    .append('g')
                    .attr('class', 'node-group')
                    .attr('cursor', 'pointer');

                nodeGroup
                    .append('circle')
                    .attr('r', 0)
                    .attr('fill', (d: any) =>
                        d.type === 'scenario'
                            ? 'url(#scenarioGrad)'
                            : d.type === 'capability'
                              ? 'url(#capGrad)'
                              : d.type === 'competency'
                                ? 'rgba(139, 92, 246, 0.2)'
                                : 'rgba(34, 211, 238, 0.1)',
                    )
                    .attr('stroke', (d: any) =>
                        d.type === 'competency'
                            ? '#a78bfa80'
                            : d.type === 'skill'
                              ? '#22d3ee50'
                              : 'rgba(255,255,255,0.2)',
                    )
                    .attr('stroke-width', 1.5)
                    .style('filter', 'url(#glow)')
                    .transition()
                    .duration(500)
                    .attr('r', (d: any) =>
                        d.type === 'scenario'
                            ? 35
                            : d.type === 'capability'
                              ? 22
                              : d.type === 'competency'
                                ? 12
                                : 6,
                    );

                const textElement = nodeGroup
                    .append('text')
                    .attr('x', (d: any) =>
                        d.type === 'scenario'
                            ? 45
                            : d.type === 'capability'
                              ? 30
                              : 18,
                    )
                    .attr('y', 0)
                    .attr('fill', 'rgba(255,255,255,0.7)')
                    .attr('font-size', (d: any) =>
                        d.type === 'scenario'
                            ? '14px'
                            : d.type === 'capability'
                              ? '11px'
                              : '9px',
                    )
                    .style('opacity', 0);

                textElement.each(function(d: any) {
                    const el = d3.select(this);
                    const name = d.name || "";
                    const words = name.split(/\s+/);
                    const limit = 25; // Umbral de caracteres
                    
                    if (name.length > limit && words.length > 1) {
                        // Intentar dividir en dos líneas
                        let line1 = "";
                        let i = 0;
                        while (i < words.length && (line1 + words[i]).length <= limit) {
                            line1 += (line1 ? " " : "") + words[i];
                            i++;
                        }
                        const line2 = words.slice(i).join(" ");
                        
                        el.append('tspan')
                            .text(line1)
                            .attr('x', el.attr('x'))
                            .attr('dy', '-0.2em');
                            
                        el.append('tspan')
                            .text(line2.length > 30 ? line2.substring(0, 27) + "..." : line2)
                            .attr('x', el.attr('x'))
                            .attr('dy', '1.2em');
                    } else {
                        el.append('tspan')
                            .text(name)
                            .attr('x', el.attr('x'))
                            .attr('dy', '0.35em');
                    }
                });

                textElement
                    .transition()
                    .duration(700)
                    .style('opacity', 1);

                return nodeGroup;
            },
            (update) => {
                update
                    .select('circle')
                    .attr('stroke', (d: any) =>
                        d.expanded ? '#fff' : 'rgba(255,255,255,0.2)',
                    );
                return update;
            },
            (exit) =>
                exit.transition().duration(300).style('opacity', 0).remove(),
        );

    // Reinforce listeners on the merged selection
    node.on('click', (event, d) => {
        toggleNode(event, d);
    })
        .on('contextmenu', (event, d) => {
            event.preventDefault();
            event.stopPropagation();
            emit('edit-node', d);
        })
        .call(
            d3
                .drag<any, any>()
                .on('start', dragstarted)
                .on('drag', dragged)
                .on('end', dragended),
        );

    if (simulation) {
        simulation.on('tick', () => {
            link.attr('x1', (d: any) => d.source.x)
                .attr('y1', (d: any) => d.source.y)
                .attr('x2', (d: any) => d.target.x)
                .attr('y2', (d: any) => d.target.y);

            node.attr('transform', (d: any) => `translate(${d.x},${d.y})`);
        });
    }
};

const createSimulation = () => {
    if (!svg.value) return;

    if (simulation) simulation.stop();

    const s = d3.select(svg.value);
    s.selectAll('*').remove();

    const defs = s.append('defs');

    // Scenario Gradient
    const scenarioGrad = defs
        .append('radialGradient')
        .attr('id', 'scenarioGrad')
        .attr('cx', '30%')
        .attr('cy', '30%')
        .attr('r', '70%');
    scenarioGrad
        .append('stop')
        .attr('offset', '0%')
        .attr('stop-color', '#818cf8');
    scenarioGrad
        .append('stop')
        .attr('offset', '100%')
        .attr('stop-color', '#4f46e5');

    // Capability Gradient
    const capGrad = defs
        .append('radialGradient')
        .attr('id', 'capGrad')
        .attr('cx', '30%')
        .attr('cy', '30%')
        .attr('r', '70%');
    capGrad.append('stop').attr('offset', '0%').attr('stop-color', '#22d3ee');
    capGrad.append('stop').attr('offset', '100%').attr('stop-color', '#0891b2');

    // Glow Filter
    const filter = defs
        .append('filter')
        .attr('id', 'glow')
        .attr('x', '-50%')
        .attr('y', '-50%')
        .attr('width', '200%')
        .attr('height', '200%');
    filter
        .append('feGaussianBlur')
        .attr('stdDeviation', '3')
        .attr('result', 'coloredBlur');
    const feMerge = filter.append('feMerge');
    feMerge.append('feMergeNode').attr('in', 'coloredBlur');
    feMerge.append('feMergeNode').attr('in', 'SourceGraphic');

    const g = s.append('g');

    const zoom = d3
        .zoom<SVGSVGElement, unknown>()
        .scaleExtent([0.1, 4])
        .on('zoom', (event) => g.attr('transform', event.transform));

    s.call(zoom);

    g.append('g').attr('class', 'links');
    g.append('g').attr('class', 'nodes');

    simulation = d3
        .forceSimulation(nodes.value)
        .force(
            'link',
            d3
                .forceLink(links.value)
                .id((d: any) => d.id)
                .distance((d: any) =>
                    d.value === 1 ? 160 : d.value === 0.8 ? 100 : 60,
                ),
        )
        .force(
            'charge',
            d3
                .forceManyBody()
                .strength((d: any) =>
                    d.type === 'scenario'
                        ? -1000
                        : d.type === 'capability'
                          ? -500
                          : -200,
                ),
        )
        .force('center', d3.forceCenter(width.value / 2, height.value / 2))
        .force(
            'collision',
            d3
                .forceCollide()
                .radius((d: any) =>
                    d.type === 'scenario'
                        ? 60
                        : d.type === 'capability'
                          ? 40
                          : 25,
                ),
        )
        .on('tick', () => {
            // Tick assigned in render() for dynamic updates
        });

    render();
};

function dragstarted(event: any) {
    if (!event.active) simulation?.alphaTarget(0.3).restart();
    event.subject.fx = event.subject.x;
    event.subject.fy = event.subject.y;
}

function dragged(event: any) {
    event.subject.fx = event.x;
    event.subject.fy = event.y;
}

function dragended(event: any) {
    if (!event.active) simulation?.alphaTarget(0);
    event.subject.fx = null;
    event.subject.fy = null;
}

const updateSize = () => {
    if (container.value) {
        width.value = container.value.clientWidth;
        height.value = container.value.clientHeight || 600;
        if (simulation) {
            simulation.force(
                'center',
                d3.forceCenter(width.value / 2, height.value / 2),
            );
            simulation.alpha(0.3).restart();
        }
    }
};

onMounted(() => {
    updateSize();
    prepareData();
    createSimulation();
    window.addEventListener('resize', updateSize);
});

onBeforeUnmount(() => {
    if (simulation) simulation.stop();
    window.removeEventListener('resize', updateSize);
});

watch(
    () => props.scenario,
    () => {
        prepareData();
        createSimulation();
    },
    { deep: true },
);
</script>

<template>
    <div ref="container" class="prototype-map-root relative h-full w-full">
        <svg
            ref="svg"
            class="h-full w-full cursor-grab active:cursor-grabbing"
            :viewBox="`0 0 ${width} ${height}`"
        ></svg>

        <!-- Legend / Info -->
        <div class="pointer-events-none absolute bottom-6 left-6 space-y-2">
            <div class="flex items-center gap-2">
                <div
                    class="h-2 w-2 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.6)]"
                ></div>
                <span
                    class="text-[10px] font-black tracking-widest text-white/40 uppercase"
                    >Architecture Core</span
                >
            </div>
            <div class="flex items-center gap-2">
                <div
                    class="h-2 w-2 rounded-full bg-violet-500 shadow-[0_0_8px_rgba(139,92,246,0.6)]"
                ></div>
                <span
                    class="text-[10px] font-black tracking-widest text-white/40 uppercase"
                    >Competency</span
                >
            </div>
            <div class="flex items-center gap-2">
                <div
                    class="h-2 w-2 rounded-full border border-cyan-400 bg-cyan-400/50 shadow-[0_0_8px_rgba(34,211,238,0.4)]"
                ></div>
                <span
                    class="text-[10px] font-black tracking-widest text-white/40 uppercase"
                    >Skill</span
                >
            </div>
        </div>
    </div>
</template>

<style scoped>
.prototype-map-root {
    background: radial-gradient(
        circle at center,
        rgba(15, 23, 42, 0.4) 0%,
        rgba(2, 6, 23, 0.8) 100%
    );
    border-radius: 24px;
    overflow: hidden;
}

svg {
    user-select: none;
}

text {
    font-family: 'Inter', sans-serif;
    pointer-events: none;
}
</style>
