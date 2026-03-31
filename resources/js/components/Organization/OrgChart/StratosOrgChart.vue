<script setup lang="ts">
import * as apiHelper from '@/apiHelper';
import DepartmentNodeComponent from '@/components/Organization/OrgChart/DepartmentNode.vue';
import { Background } from '@vue-flow/background';
import { Controls } from '@vue-flow/controls';
import { VueFlow, useVueFlow, type NodeTypesObject } from '@vue-flow/core';
import * as dagre from 'dagre';
import { onMounted, ref } from 'vue';

// Vue Flow Styles
import '@vue-flow/core/dist/style.css';
import '@vue-flow/core/dist/theme-default.css';

const { fitView } = useVueFlow();

const loading = ref(true);
const error = ref<string | null>(null);
const elements = ref<any[]>([]);

const nodeTypes: NodeTypesObject = {
    department: DepartmentNodeComponent,
};

const fetchTree = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await apiHelper.get<any[]>('/api/departments/tree');
        const { nodes, edges } = transformData(response);
        elements.value = layoutNodes(nodes, edges);
    } catch (e: any) {
        error.value = 'Error al cargar el organigrama: ' + e.message;
    } finally {
        loading.value = false;
    }
};

const transformData = (data: any[]) => {
    const nodes: any[] = [];
    const edges: any[] = [];

    const traverse = (item: any) => {
        nodes.push({
            id: item.id.toString(),
            type: 'department',
            data: {
                label: item.name,
                description: item.description,
                managerName: item.manager
                    ? `${item.manager.first_name} ${item.manager.last_name}`
                    : null,
                headcount: item.headcount || 0,
            },
            position: { x: 0, y: 0 },
        });

        if (item.parent_id) {
            edges.push({
                id: `e${item.parent_id}-${item.id}`,
                source: item.parent_id.toString(),
                target: item.id.toString(),
                animated: true,
                style: { stroke: 'rgba(99, 102, 241, 0.4)', strokeWidth: 2 },
            });
        }

        if (item.children && item.children.length > 0) {
            item.children.forEach(traverse);
        }
    };

    data.forEach(traverse);
    return { nodes, edges };
};

const layoutNodes = (nodes: any[], edges: any[]) => {
    const dagreGraph = new dagre.graphlib.Graph();
    dagreGraph.setDefaultEdgeLabel(() => ({}));
    dagreGraph.setGraph({ rankdir: 'TB', nodesep: 100, ranksep: 100 });

    nodes.forEach((node) => {
        dagreGraph.setNode(node.id, { width: 250, height: 150 });
    });

    edges.forEach((edge) => {
        dagreGraph.setEdge(edge.source, edge.target);
    });

    dagre.layout(dagreGraph);

    nodes.forEach((node) => {
        const nodeWithPosition = dagreGraph.node(node.id);
        node.position = {
            x: nodeWithPosition.x - 125,
            y: nodeWithPosition.y - 75,
        };
    });

    return [...nodes, ...edges];
};

const handlePaneReady = () => {
    setTimeout(() => {
        fitView();
    }, 100);
};

onMounted(() => {
    fetchTree();
});

defineExpose({ refresh: fetchTree });
</script>

<template>
    <div
        class="relative h-[600px] w-full overflow-hidden rounded-2xl border border-white/5 bg-slate-950/20"
    >
        <div
            v-if="loading"
            class="absolute inset-0 z-20 flex items-center justify-center bg-slate-950/50 backdrop-blur-sm"
        >
            <div
                class="h-10 w-10 animate-spin rounded-full border-4 border-indigo-500/30 border-t-indigo-500"
            ></div>
        </div>

        <div
            v-if="error"
            class="flex h-full items-center justify-center p-4 text-center text-red-400"
        >
            {{ error }}
        </div>

        <VueFlow
            v-if="!loading && elements.length > 0"
            v-model="elements"
            :node-types="nodeTypes"
            @pane-ready="handlePaneReady"
            :default-viewport="{ zoom: 0.8 }"
            :min-zoom="0.1"
            :max-zoom="4"
            class="h-full w-full"
        >
            <Background pattern-color="rgba(255,255,255,0.05)" :gap="20" />
            <Controls />
        </VueFlow>

        <div
            v-else-if="!loading && elements.length === 0"
            class="flex h-full items-center justify-center text-gray-500"
        >
            No se encontraron unidades en la jerarquía.
        </div>
    </div>
</template>

<style scoped>
:deep(.vue-flow__edge-path) {
    stroke-dasharray: 5;
    animation: dash 1s linear infinite;
}

@keyframes dash {
    from {
        stroke-dashoffset: 10;
    }
    to {
        stroke-dashoffset: 0;
    }
}

:deep(.vue-flow__controls) {
    background: rgba(15, 23, 42, 0.8);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 4px;
}
:deep(.vue-flow__controls-button) {
    fill: #9ca3af;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}
:deep(.vue-flow__controls-button:hover) {
    background: rgba(255, 255, 255, 0.05);
    fill: white;
}
</style>
