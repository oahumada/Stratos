<script setup lang="ts">
import * as apiHelper from '@/apiHelper';
import StButtonGlass from '@/components/StButtonGlass.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Download, Maximize2, RefreshCcw } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

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

const chartOptions = ref<any>({});

const fetchHeatmapData = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await apiHelper.get<{
            x_axis: string[];
            y_axis: string[];
            data: any[];
            critical_risks?: any[];
        }>('/departments/heatmap');

        // Setup ECharts Options
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
                textStyle: {
                    color: '#fff',
                },
            },
            grid: {
                height: '75%',
                top: '10%',
                left: '10%',
                right: '5%',
                bottom: '15%',
            },
            xAxis: {
                type: 'category',
                data: response.x_axis,
                splitArea: { show: true },
                axisLabel: {
                    color: '#9ca3af',
                    rotate: 45,
                },
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
                    color: [
                        '#1e1b4b',
                        '#4338ca',
                        '#8b5cf6',
                        '#d946ef',
                        '#f43f5e',
                    ], // Dark Blue -> Red (Cold to Hot)
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
                        formatter: function (p: any) {
                            return p.data[2] + '%';
                        },
                    },
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowColor: 'rgba(0, 0, 0, 0.5)',
                        },
                    },
                    markPoint: {
                        symbol: 'pin',
                        symbolSize: 20,
                        itemStyle: {
                            color: '#f43f5e',
                        },
                        data:
                            response.critical_risks?.map((r: any) => ({
                                coord: [r.coord[0], r.coord[1]],
                                value: '!',
                            })) || [],
                    },
                },
            ],
        };
    } catch (e: any) {
        error.value = 'Error al cargar mapa térmico: ' + e.message;
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchHeatmapData();
});
</script>

<template>
    <AppLayout>
        <title>Stratos Map - Radiografía de Competencias</title>
        <Head title="Stratos Map - Radiografía de Competencias" />

        <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
            <header
                class="flex flex-col justify-between gap-4 md:flex-row md:items-center"
            >
                <div>
                    <h1
                        class="mb-2 text-3xl font-bold tracking-tight text-white"
                    >
                        Stratos Map
                    </h1>
                    <p class="text-gray-400">
                        Radiografía Organizacional. Temperatura de Gaps entre
                        Capacidad Requerida vs Capacidad Actual distribuida por
                        Sub-departamentos.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <StButtonGlass
                        variant="ghost"
                        @click="fetchHeatmapData"
                        :disabled="loading"
                    >
                        <RefreshCcw
                            class="mr-2 h-4 w-4"
                            :class="{ 'animate-spin': loading }"
                        />
                        Sincronizar
                    </StButtonGlass>
                    <StButtonGlass variant="primary">
                        <Download class="mr-2 h-4 w-4" />
                        Exportar Matriz
                    </StButtonGlass>
                </div>
            </header>

            <div
                class="bg-surface relative flex min-h-[600px] flex-col rounded-2xl border border-white/5 p-6 shadow-2xl"
            >
                <div class="mb-6 flex items-center justify-between">
                    <h2
                        class="flex items-center gap-2 text-xl font-medium text-white"
                    >
                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                        Temperatura de Competencias Estratégicas
                    </h2>
                    <button
                        class="text-gray-400 transition-colors hover:text-white"
                    >
                        <Maximize2 class="h-5 w-5" />
                    </button>
                </div>

                <div
                    v-if="loading"
                    class="bg-surface/50 absolute inset-0 z-10 flex items-center justify-center rounded-2xl backdrop-blur-sm"
                >
                    <div
                        class="h-8 w-8 animate-spin rounded-full border-4 border-indigo-500/30 border-t-indigo-500"
                    ></div>
                </div>

                <div
                    v-else-if="error"
                    class="flex flex-1 flex-col items-center justify-center py-12 text-center text-red-400"
                >
                    {{ error }}
                </div>

                <div v-else class="h-full min-h-[500px] w-full flex-1">
                    <v-chart
                        class="chart h-full w-full"
                        :option="chartOptions"
                        autoresize
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.chart {
    min-height: 500px;
    height: 100%;
    width: 100%;
}
</style>
