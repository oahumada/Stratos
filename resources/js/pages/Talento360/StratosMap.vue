<script setup lang="ts">
import * as apiHelper from '@/apiHelper';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StratosMap from '@/components/Organization/StratosMap.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Download, Maximize2, RefreshCcw, Layers, Users, CircleDot } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

// Apache ECharts Imports
import { HeatmapChart } from 'echarts/charts';
import {
    GridComponent,
    TitleComponent,
    TooltipComponent,
    VisualMapComponent,
} from 'echarts/components';
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import VChart from 'vue-echarts';

// Register echarts modules
use([
    TooltipComponent,
    GridComponent,
    VisualMapComponent,
    HeatmapChart,
    CanvasRenderer,
    TitleComponent,
]);

const loading = ref(true);
const error = ref<string | null>(null);
const currentTab = ref('heatmap'); // 'heatmap' | 'gravitational' | 'cerberos'

// Data state
const chartOptions = ref<any>({});
const mapNodes = ref([]);
const mapLinks = ref([]);

const fetchData = async () => {
    loading.value = true;
    error.value = null;
    try {
        if (currentTab.value === 'heatmap') {
            await fetchHeatmapData();
        } else if (currentTab.value === 'gravitational') {
            await fetchGravitationalData();
        } else if (currentTab.value === 'cerberos') {
            await fetchCerberosData();
        }
    } catch (e: any) {
        error.value = 'Error al cargar los datos: ' + e.message;
    } finally {
        loading.value = false;
    }
};

const fetchHeatmapData = async () => {
    const response = await apiHelper.get<{
        x_axis: string[];
        y_axis: string[];
        data: any[];
        critical_risks?: any[];
    }>('/api/departments/heatmap');

    chartOptions.value = {
        tooltip: {
            position: 'top',
            formatter: function (params: any) {
                const depto = response.x_axis[params.data[0]];
                const skill = response.y_axis[params.data[1]];
                const temp = params.data[2];

                const risk = response.critical_risks?.find(
                    (r: any) =>
                        r.coord[0] === params.data[0] &&
                        r.coord[1] === params.data[1],
                );

                let html = `<strong>${depto}</strong><br/>${skill}: <span class="text-emerald-400 font-bold">${temp}%</span> Cobertura`;
                if (risk) {
                    html += `<br/><div class="mt-2 p-2 bg-rose-500/20 border border-rose-500/30 rounded text-rose-300 text-[10px]">⚠️ ${risk.reason}</div>`;
                }
                return html;
            },
            backgroundColor: 'rgba(23, 23, 23, 0.9)',
            borderColor: 'rgba(255, 255, 255, 0.1)',
            textStyle: { color: '#fff' },
        },
        grid: { height: '75%', top: '10%', left: '10%', right: '5%', bottom: '15%' },
        xAxis: {
            type: 'category',
            data: response.x_axis,
            splitArea: { show: true },
            axisLabel: { color: '#9ca3af', rotate: 45 },
            axisLine: { lineStyle: { color: 'rgba(255, 255, 255, 0.1)' } },
        },
        yAxis: {
            type: 'category',
            data: response.y_axis,
            splitArea: { show: true },
            axisLabel: { color: '#9ca3af' },
            axisLine: { lineStyle: { color: 'rgba(255, 255, 255, 0.1)' } },
        },
        visualMap: {
            min: 0,
            max: 100,
            calculable: true,
            orient: 'horizontal',
            left: 'center',
            bottom: '2%',
            inRange: {
                color: ['#1e1b4b', '#4338ca', '#8b5cf6', '#d946ef', '#f43f5e'],
            },
            textStyle: { color: '#9ca3af' },
        },
        series: [
            {
                name: 'Temperatura de Skill',
                type: 'heatmap',
                data: response.data,
                label: {
                    show: true,
                    color: '#ffffff',
                    formatter: function (p: any) { return p.data[2] + '%'; },
                },
                emphasis: {
                    itemStyle: { shadowBlur: 10, shadowColor: 'rgba(0, 0, 0, 0.5)' },
                },
            },
        ],
    };
};

const fetchGravitationalData = async () => {
    const response = await apiHelper.get('/api/stratos-maps/gravitational');
    mapNodes.value = response.nodes;
    mapLinks.value = response.links;
};

const fetchCerberosData = async () => {
    const response = await apiHelper.get('/api/stratos-maps/cerberos');
    mapNodes.value = response.nodes;
    mapLinks.value = response.links;
};

const handleNodeClick = (node: any) => {
    console.log('Node clicked:', node);
};

onMounted(() => {
    fetchData();
});

watch(currentTab, () => {
    fetchData();
});
</script>

<template>
    <AppLayout>
        <title>Stratos Map - Inteligencia Organizacional</title>
        <Head title="Stratos Map - Inteligencia Organizacional" />

        <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
            <header class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                <div>
                    <h1 class="mb-2 text-3xl font-bold tracking-tight text-white">
                        Stratos Map
                    </h1>
                    <p class="text-gray-400">
                        Visualización multidimensional del talento, competencias y liderazgo.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex bg-white/5 p-1 rounded-lg border border-white/10">
                        <button 
                            @click="currentTab = 'heatmap'"
                            class="px-4 py-2 rounded-md transition-all text-sm flex items-center gap-2"
                            :class="currentTab === 'heatmap' ? 'bg-indigo-500 text-white' : 'text-gray-400 hover:text-white'"
                        >
                            <CircleDot class="h-4 w-4" />
                            Matriz Térmica
                        </button>
                        <button 
                            @click="currentTab = 'gravitational'"
                            class="px-4 py-2 rounded-md transition-all text-sm flex items-center gap-2"
                            :class="currentTab === 'gravitational' ? 'bg-indigo-500 text-white' : 'text-gray-400 hover:text-white'"
                        >
                            <Layers class="h-4 w-4" />
                            Nodos Gravitacionales
                        </button>
                        <button 
                            @click="currentTab = 'cerberos'"
                            class="px-4 py-2 rounded-md transition-all text-sm flex items-center gap-2"
                            :class="currentTab === 'cerberos' ? 'bg-indigo-500 text-white' : 'text-gray-400 hover:text-white'"
                        >
                            <Users class="h-4 w-4" />
                            Mapa Cerberos
                        </button>
                    </div>
                </div>
            </header>

            <div class="bg-surface relative flex min-h-[700px] flex-col rounded-3xl border border-white/5 p-8 shadow-2xl overflow-hidden">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="flex items-center gap-2 text-xl font-medium text-white">
                        <span class="h-2 w-2 rounded-full" :class="currentTab === 'heatmap' ? 'bg-rose-500' : 'bg-emerald-500'"></span>
                        {{ 
                            currentTab === 'heatmap' ? 'Radiografía de Competencias' : 
                            currentTab === 'gravitational' ? 'Ecosistema de Unidades (Masa de Talento)' : 
                            'Red Neuronal de Liderazgo (Cerberos)'
                        }}
                    </h2>
                    <div class="flex items-center gap-3">
                        <StButtonGlass variant="ghost" size="sm" @click="fetchData" :disabled="loading">
                            <RefreshCcw class="h-4 w-4" :class="{ 'animate-spin': loading }" />
                        </StButtonGlass>
                        <button class="text-gray-400 hover:text-white transition-colors">
                            <Maximize2 class="h-5 w-5" />
                        </button>
                    </div>
                </div>

                <!-- Visualizers -->
                <div class="flex-1 relative min-h-[500px]">
                    <div v-if="loading" class="absolute inset-0 z-10 flex items-center justify-center bg-surface/50 backdrop-blur-sm rounded-2xl">
                        <div class="h-10 w-10 animate-spin rounded-full border-4 border-indigo-500/30 border-t-indigo-500"></div>
                    </div>

                    <div v-else-if="error" class="flex h-full flex-col items-center justify-center py-12 text-center text-red-400">
                        {{ error }}
                    </div>

                    <!-- ECharts Heatmap -->
                    <div v-if="currentTab === 'heatmap'" class="h-full w-full">
                        <v-chart class="chart h-full w-full min-h-[550px]" :option="chartOptions" autoresize />
                    </div>

                    <!-- D3 Stratos Map -->
                    <div v-else class="h-full w-full min-h-[550px]">
                        <StratosMap 
                            :nodes="mapNodes" 
                            :links="mapLinks" 
                            :mode="currentTab === 'gravitational' ? 'gravitational' : 'cerberos'"
                            @node-click="handleNodeClick"
                        />
                    </div>
                </div>

                <!-- Legend / Summary Footer -->
                <footer v-if="!loading && !error" class="mt-8 pt-6 border-t border-white/5 flex flex-wrap gap-8">
                    <div v-if="currentTab === 'gravitational'" class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <div class="h-3 w-3 rounded-full bg-indigo-500 glow-indigo"></div>
                            <span class="text-xs text-gray-400">Masa = Headcount</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="h-3 w-3 rounded-full bg-purple-500 glow-purple"></div>
                            <span class="text-xs text-gray-400">Brillo = Payroll / Valor Real</span>
                        </div>
                    </div>
                    <div v-if="currentTab === 'cerberos'" class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <div class="h-3 w-3 rounded-full bg-emerald-500 glow-emerald"></div>
                            <span class="text-xs text-gray-400">Nodos = Colaboradores</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="h-3 w-3 rounded-full bg-blue-500 glow-blue"></div>
                            <span class="text-xs text-gray-400">Hi-Po = Mayor Radio de Influencia</span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.chart {
    min-height: 550px;
    height: 100%;
}

.glow-indigo { box-shadow: 0 0 10px #6366f1; }
.glow-purple { box-shadow: 0 0 10px #a855f7; }
.glow-emerald { box-shadow: 0 0 10px #10b981; }
.glow-blue { box-shadow: 0 0 10px #3b82f6; }

:deep(.bg-surface) {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(2, 6, 23, 0.95));
    backdrop-filter: blur(20px);
}
</style>
