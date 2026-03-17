<script setup lang="ts">
import * as apiHelper from '@/apiHelper';
import DepartmentNodeComponent from '@/components/Organization/OrgChart/DepartmentNode.vue';
import { Head } from '@inertiajs/vue3';
import { Layers, MousePointer2, RefreshCcw } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { VueFlow, useVueFlow, type NodeTypesObject } from '@vue-flow/core';
import { Background } from '@vue-flow/background';
import { Controls } from '@vue-flow/controls';

import * as dagre from 'dagre';

// Vue Flow Styles
import '@vue-flow/core/dist/style.css';
import '@vue-flow/core/dist/theme-default.css';

interface DepartmentNode {
    id: number;
    name: string;
    description: string | null;
    parent_id: number | null;
    headcount?: number;
    payroll_total?: number;
    manager: {
        id: number;
        first_name: string;
        last_name: string;
    } | null;
    children: DepartmentNode[];
}

const { fitView } = useVueFlow();

const loading = ref(true);
const error = ref<string | null>(null);
const elements = ref<any[]>([]);


// Custom node type registration
const nodeTypes: NodeTypesObject = {
    department: DepartmentNodeComponent,
};

const fetchTree = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await apiHelper.get<DepartmentNode[]>('/api/departments/tree');
        const { nodes, edges } = transformData(response);
        elements.value = layoutNodes(nodes, edges);
    } catch (e: any) {
        error.value = 'Error al cargar el organigrama: ' + e.message;
    } finally {
        loading.value = false;
    }
};

const transformData = (data: DepartmentNode[]) => {
    const nodes: any[] = [];
    const edges: any[] = [];

    const traverse = (item: DepartmentNode) => {
        nodes.push({
            id: item.id.toString(),
            type: 'department',
            data: {
                label: item.name,
                description: item.description,
                managerName: item.manager ? `${item.manager.first_name} ${item.manager.last_name}` : null,
                headcount: item.headcount || 0
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

onMounted(() => {
    fetchTree();
});

const handlePaneReady = () => {
    setTimeout(() => {
        fitView();
    }, 100);
};
</script>

<template>
        <Head title="Stratos - Organigrama Interactivo" />

        <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
            <header class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                <div>
                    <h1 class="mb-2 text-3xl font-bold tracking-tight text-white">
                        Organigrama Interactivo
                    </h1>
                    <p class="text-gray-400">
                        Visualiza y gestiona la estructura jerárquica de tu organización.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button 
                        @click="fetchTree"
                        class="flex items-center gap-2 px-4 py-2 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 transition-all text-white text-sm"
                    >
                        <RefreshCcw class="h-4 w-4" :class="{ 'animate-spin': loading }" />
                        Sincronizar
                    </button>
                    <button class="flex items-center gap-2 px-4 py-2 bg-indigo-500 hover:bg-indigo-600 rounded-lg transition-all text-white text-sm shadow-lg shadow-indigo-500/20">
                        <Layers class="h-4 w-4" />
                        Añadir Unidad
                    </button>
                </div>
            </header>

            <div class="bg-chart-container relative h-[700px] w-full rounded-3xl border border-white/5 overflow-hidden shadow-2xl">
                <div v-if="loading" class="absolute inset-0 z-20 flex items-center justify-center bg-slate-950/50 backdrop-blur-md">
                    <div class="flex flex-col items-center gap-4">
                        <div class="h-12 w-12 animate-spin rounded-full border-4 border-indigo-500/30 border-t-indigo-500"></div>
                        <span class="text-indigo-400 font-medium animate-pulse">Calculando Jerarquía...</span>
                    </div>
                </div>

                <div v-if="error" class="absolute inset-0 z-20 flex items-center justify-center bg-slate-950/80 p-6 text-center">
                    <div class="max-w-md space-y-4">
                        <div class="text-rose-500 text-lg font-semibold">Error de Carga</div>
                        <p class="text-gray-400">{{ error }}</p>
                        <button @click="fetchTree" class="px-6 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-white transition-all">Reintentar</button>
                    </div>
                </div>

                <VueFlow
                    v-if="!loading && elements.length > 0"
                    v-model="elements"
                    :node-types="nodeTypes"
                    @pane-ready="handlePaneReady"
                    :default-viewport="{ zoom: 0.8 }"
                    :min-zoom="0.2"
                    :max-zoom="4"
                >
                    <Background pattern-color="rgba(255,255,255,0.05)" :gap="20" />
                    <Controls />
                    
                    <template #node-department="props">
                        <DepartmentNodeComponent v-bind="props" />
                    </template>
                </VueFlow>

                <div v-else-if="!loading && elements.length === 0" class="flex h-full items-center justify-center flex-col gap-4">
                    <MousePointer2 class="h-12 w-12 text-gray-600" />
                    <p class="text-gray-500">No se encontraron unidades organizativas.</p>
                </div>
            </div>

            <!-- Ayuda / Legend -->
            <footer class="flex items-center justify-between text-xs text-gray-500 px-2">
                <div class="flex gap-6">
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-indigo-500"></span> Flujo de Reporte</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Líder Asignado</span>
                </div>
                <div>Usa el Scroll para Zoom · Arrastra para Navegar</div>
            </footer>
        </div>
</template>

<style scoped>
.bg-chart-container {
    background: radial-gradient(circle at top right, rgba(30, 41, 59, 0.7), rgba(2, 6, 23, 1));
}

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
