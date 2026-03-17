<script setup lang="ts">
import * as apiHelper from '@/apiHelper';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StratosMap from '@/components/Organization/StratosMap.vue';
import StratosOrgChart from '@/components/Organization/OrgChart/StratosOrgChart.vue';
import { onMounted, ref, watch } from 'vue';
import { Layers, CircleDot, Users, RefreshCcw, Maximize2, Search, X, UserCheck } from 'lucide-vue-next';
import { HeatmapChart } from 'echarts/charts';
import { GridComponent, TitleComponent, TooltipComponent, VisualMapComponent } from 'echarts/components';
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';

// Apache ECharts Imports
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
const currentTab = ref('org-chart'); // 'heatmap' | 'gravitational' | 'cerberos' | 'org-chart'

// Data state
const chartOptions = ref<any>({});
const mapNodes = ref([]);
const mapLinks = ref([]);

// Search state for Cerberos
const searchQuery = ref('');
const searchResults = ref<any[]>([]);
const searchLoading = ref(false);
const selectedPerson = ref<any>(null);

const handleSearch = async () => {
    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        return;
    }
    searchLoading.value = true;
    try {
        const response = await apiHelper.get<any[]>(`/api/stratos-maps/people/search?query=${searchQuery.value}`);
        searchResults.value = response;
    } catch (e) {
        console.error('Error searching people:', e);
    } finally {
        searchLoading.value = false;
    }
};

const selectPerson = (person: any) => {
    selectedPerson.value = person;
    searchQuery.value = person.full_name;
    searchResults.value = [];
    fetchData();
};

const clearSelection = () => {
    selectedPerson.value = null;
    searchQuery.value = '';
    searchResults.value = [];
    fetchData();
};

const fetchData = async () => {
    loading.value = true;
    error.value = null;
    
    // Clear previous data to avoid showing old nodes in new tab
    if (currentTab.value === 'gravitational' || currentTab.value === 'cerberos') {
        mapNodes.value = [];
        mapLinks.value = [];
    }

    console.log(`[StratosMap] Fetching data for tab: ${currentTab.value}`);
    try {
        if (currentTab.value === 'heatmap') {
            await fetchHeatmapData();
        } else if (currentTab.value === 'gravitational') {
            await fetchGravitationalData();
        } else if (currentTab.value === 'cerberos') {
            await fetchCerberosData();
        }
    } catch (e: any) {
        console.error(`[StratosMap] Error fetching ${currentTab.value}:`, e);
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
    console.log('[StratosMap] Gravitational Data:', response);
    mapNodes.value = response.nodes || [];
    mapLinks.value = response.links || [];
};

const fetchCerberosData = async () => {
    const url = selectedPerson.value 
        ? `/api/stratos-maps/cerberos?person_id=${selectedPerson.value.id}`
        : '/api/stratos-maps/cerberos';
        
    const response = await apiHelper.get(url);
    console.log('[StratosMap] Cerberos Data:', response);
    mapNodes.value = response.nodes || [];
    mapLinks.value = response.links || [];
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
        <title>Stratos Map - Inteligencia Organizacional</title>
        <Head title="Stratos Map - Inteligencia Organizacional" />

        <div class="relative min-h-screen bg-[#020617] text-white selection:bg-indigo-500/30 overflow-hidden">
            <!-- Background Elements -->
            <div class="pointer-events-none fixed inset-0 overflow-hidden">
                <div class="absolute -top-[10%] -left-[10%] h-[40%] w-[40%] rounded-full bg-indigo-500/10 blur-[120px]"></div>
                <div class="absolute top-[20%] -right-[10%] h-[35%] w-[35%] rounded-full bg-purple-500/10 blur-[120px]"></div>
                <div class="absolute -bottom-[10%] left-[20%] h-[30%] w-[30%] rounded-full bg-blue-500/10 blur-[120px]"></div>
            </div>

            <div class="relative z-10 mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
                <header class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                    <div>
                        <h1 class="mb-2 text-3xl font-black tracking-tight text-white">
                            Stratos <span class="text-indigo-400">Map</span>
                        </h1>
                        <p class="text-sm font-medium text-white/40">
                            Multidimensional intelligence: talent, competencies, and leadership architecture.
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex bg-white/5 p-1 rounded-xl border border-white/10 backdrop-blur-md">
                            <button 
                                @click="currentTab = 'org-chart'"
                                class="px-6 py-2.5 rounded-lg transition-all text-[11px] font-black tracking-widest uppercase flex items-center gap-2"
                                :class="currentTab === 'org-chart' ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'text-white/40 hover:text-white hover:bg-white/5'"
                            >
                                <Layers class="h-4 w-4" />
                                Stratos Chart
                            </button>
                            <button 
                                @click="currentTab = 'heatmap'"
                                class="px-6 py-2.5 rounded-lg transition-all text-[11px] font-black tracking-widest uppercase flex items-center gap-2"
                                :class="currentTab === 'heatmap' ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'text-white/40 hover:text-white hover:bg-white/5'"
                            >
                                <CircleDot class="h-4 w-4" />
                                Heatmap
                            </button>
                            <button 
                                @click="currentTab = 'gravitational'"
                                class="px-6 py-2.5 rounded-lg transition-all text-[11px] font-black tracking-widest uppercase flex items-center gap-2"
                                :class="currentTab === 'gravitational' ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'text-white/40 hover:text-white hover:bg-white/5'"
                            >
                                <Layers class="h-4 w-4" />
                                Gravitational
                            </button>
                            <button 
                                @click="currentTab = 'cerberos'"
                                class="px-6 py-2.5 rounded-lg transition-all text-[11px] font-black tracking-widest uppercase flex items-center gap-2"
                                :class="currentTab === 'cerberos' ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'text-white/40 hover:text-white hover:bg-white/5'"
                            >
                                <Users class="h-4 w-4" />
                                Cerberos
                            </button>
                        </div>
                    </div>
                </header>

            <div class="bg-surface relative flex min-h-[750px] flex-col rounded-[32px] border border-white/10 p-8 shadow-2xl overflow-hidden backdrop-blur-xl">
                <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex flex-col gap-1">
                        <h2 class="flex items-center gap-3 text-lg font-black tracking-tight text-white uppercase">
                            <span class="h-2 w-2 rounded-full animate-pulse" :class="currentTab === 'heatmap' ? 'bg-rose-500 shadow-[0_0_10px_rgba(244,63,94,0.6)]' : 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.6)]'"></span>
                            {{ 
                                currentTab === 'org-chart' ? 'Hierarchical Architecture (Stratos Chart)' : 
                            currentTab === 'heatmap' ? 'Competency Radiography' : 
                            currentTab === 'gravitational' ? 'Unit Ecosystem (Talent Mass)' : 
                            'Leadership Neural Network (Cerberos)'
                            }}
                        </h2>
                        <span class="text-[10px] font-bold text-white/30 tracking-[0.2em] uppercase ml-5">Neural Engine Active</span>
                    </div>

                    <!-- Cerberos Search Bar -->
                    <div v-if="currentTab === 'cerberos'" class="relative w-full max-w-md">
                        <div class="relative group">
                            <input 
                                v-model="searchQuery"
                                @input="handleSearch"
                                type="text" 
                                placeholder="Search by name or RUT..."
                                class="w-full bg-white/5 border border-white/10 rounded-2xl py-3 pl-12 pr-10 text-sm text-white placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all backdrop-blur-md"
                            />
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center">
                                <RefreshCcw v-if="searchLoading" class="h-4 w-4 text-indigo-400 animate-spin" />
                                <Search v-else class="h-5 w-5 text-white/20 group-focus-within:text-indigo-400 transition-colors" />
                            </div>
                            <button 
                                v-if="searchQuery"
                                @click="clearSelection"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-white/20 hover:text-white"
                            >
                                <X class="h-4 w-4" />
                            </button>
                        </div>

                        <!-- Dropdown Results -->
                        <div v-if="searchResults.length > 0" class="absolute top-full left-0 right-0 mt-2 bg-[#0f172a]/95 border border-white/10 rounded-2xl overflow-hidden backdrop-blur-xl z-50 shadow-2xl">
                            <div 
                                v-for="person in searchResults" 
                                :key="person.id"
                                @click="selectPerson(person)"
                                class="px-5 py-3 hover:bg-white/5 cursor-pointer flex items-center justify-between border-b border-white/5 last:border-0"
                            >
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-white">{{ person.full_name }}</span>
                                    <span class="text-[10px] text-white/40 uppercase tracking-wider">Talent ID: #{{ person.id }}</span>
                                </div>
                                <UserCheck class="h-4 w-4 text-white/20" />
                            </div>
                        </div>
                    </div>

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

                    <!-- Vue Flow Org Chart -->
                    <div v-if="currentTab === 'org-chart'" class="h-[600px] w-full">
                         <StratosOrgChart />
                    </div>

                    <!-- ECharts Heatmap -->
                    <div v-else-if="currentTab === 'heatmap'" class="h-[600px] w-full">
                        <v-chart class="chart h-full w-full" :option="chartOptions" autoresize />
                    </div>

                    <!-- D3 Stratos Map -->
                    <div v-else-if="currentTab === 'gravitational' || currentTab === 'cerberos'" class="h-[600px] w-full">
                        <div v-if="currentTab === 'cerberos' && !selectedPerson && !loading" class="absolute inset-0 flex flex-col items-center justify-center text-center p-8">
                            <div class="h-24 w-24 bg-indigo-500/10 rounded-full flex items-center justify-center mb-6 animate-pulse border border-indigo-500/20">
                                <Search class="h-10 w-10 text-indigo-400" />
                            </div>
                            <h3 class="text-xl font-black mb-2 uppercase tracking-tight">Selecciona un Colaborador</h3>
                            <p class="text-sm text-white/40 max-w-sm">Busca por nombre o RUT para activar la Red Neural y visualizar su ecosistema de evaluación 360°.</p>
                        </div>
                        <StratosMap 
                            v-else
                            :nodes="mapNodes" 
                            :links="mapLinks" 
                            :mode="currentTab === 'gravitational' ? 'gravitational' : 'cerberos'"
                            @node-click="handleNodeClick"
                        />
                    </div>
                </div>

                <!-- Legend / Summary Footer -->
                <footer v-if="!loading && !error" class="mt-8 pt-6 border-t border-white/5 flex flex-wrap gap-8">
                    <div v-if="currentTab === 'org-chart'" class="flex items-center gap-6 text-gray-400 text-xs">
                         <div class="flex items-center gap-2">
                            <div class="h-3 w-3 rounded-full bg-indigo-500 glow-indigo"></div>
                            <span>Interactive Hierarchy</span>
                        </div>
                    </div>
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
    </div>
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

/* Vue Flow Overrides for Dark Mode */
:deep(.vue-flow__edge-path) {
    stroke-dasharray: 5;
    animation: dash 1s linear infinite;
}

@keyframes dash {
    from { stroke-dashoffset: 10; }
    to { stroke-dashoffset: 0; }
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
