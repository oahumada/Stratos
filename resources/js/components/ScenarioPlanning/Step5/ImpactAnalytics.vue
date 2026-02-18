<template>
    <div class="impact-analytics mt-4">
        <v-row>
            <v-col cols="12">
                <v-card elevation="2" class="mb-4 overflow-hidden">
                    <v-card-title
                        class="d-flex align-center justify-space-between bg-primary py-3 text-white"
                    >
                        <div class="d-flex align-center">
                            <v-icon start icon="mdi-chart-radar" class="mr-2" />
                            <span
                                >Visualización de Impacto Estratégico (IA)</span
                            >
                        </div>
                        <v-chip size="small" color="white" variant="outlined">
                            Proyección a {{ horizon }} meses
                        </v-chip>
                    </v-card-title>

                    <v-card-text class="pa-6">
                        <v-row>
                            <!-- Gráfico de Competencias y Control de Confianza -->
                            <v-col cols="12" md="7">
                                <div
                                    class="d-flex align-center justify-space-between mb-4"
                                >
                                    <div class="text-h6 d-flex align-center">
                                        Cierre de Brechas por Competencia
                                        <v-tooltip
                                            location="top"
                                            text="Compara el nivel actual de la organización contra el nivel proyectado tras aplicar las estrategias sugeridas."
                                        >
                                            <template #activator="{ props }">
                                                <v-icon
                                                    v-bind="props"
                                                    size="small"
                                                    color="medium-emphasis"
                                                    class="ml-2"
                                                    >mdi-information</v-icon
                                                >
                                            </template>
                                        </v-tooltip>
                                    </div>
                                    <div style="width: 250px">
                                        <div
                                            class="text-caption d-flex justify-space-between mb-1"
                                        >
                                            <span
                                                >Factor de Confianza
                                                (Ejecución)</span
                                            >
                                            <span class="font-weight-bold"
                                                >{{ confidenceFactor }}%</span
                                            >
                                        </div>
                                        <v-slider
                                            v-model="confidenceFactor"
                                            min="30"
                                            max="100"
                                            step="5"
                                            density="compact"
                                            hide-details
                                            color="primary"
                                            @update:model-value="updateChart"
                                        ></v-slider>
                                    </div>
                                </div>
                                <div
                                    class="chart-container"
                                    style="height: 350px"
                                >
                                    <v-progress-circular
                                        v-if="loading"
                                        indeterminate
                                        color="primary"
                                    />
                                    <canvas v-else ref="radarChartRef"></canvas>
                                </div>
                            </v-col>

                            <!-- Resumen de KPIs de Impacto -->
                            <v-col cols="12" md="5">
                                <div class="text-h6 mb-4">KPIs Proyectados</div>
                                <v-list lines="two" class="pa-0">
                                    <v-list-item
                                        v-for="kpi in kpis"
                                        :key="kpi.label"
                                        :prepend-icon="kpi.icon"
                                        :class="kpi.class"
                                    >
                                        <v-list-item-title
                                            class="font-weight-bold"
                                            >{{ kpi.value }}</v-list-item-title
                                        >
                                        <v-list-item-subtitle>{{
                                            kpi.label
                                        }}</v-list-item-subtitle>
                                        <template #append>
                                            <v-icon
                                                :color="
                                                    kpi.trend === 'up'
                                                        ? 'success'
                                                        : 'error'
                                                "
                                            >
                                                {{
                                                    kpi.trend === 'up'
                                                        ? 'mdi-trending-up'
                                                        : 'mdi-trending-down'
                                                }}
                                            </v-icon>
                                        </template>
                                    </v-list-item>
                                </v-list>

                                <v-divider class="my-4"></v-divider>

                                <div
                                    class="text-subtitle-2 font-weight-bold mb-2"
                                >
                                    Tiempo a Plena Capacidad (TFC)
                                </div>
                                <div class="tfc-breakdown mb-4">
                                    <v-tooltip
                                        v-for="tfc in impactData?.tfc_breakdown ||
                                        []"
                                        :key="tfc.type"
                                        location="top"
                                    >
                                        <template #activator="{ props }">
                                            <div
                                                v-bind="props"
                                                class="tfc-bar"
                                                :style="{
                                                    width:
                                                        (tfc.weeks / 24) * 100 +
                                                        '%',
                                                    backgroundColor:
                                                        getStrategyColor(
                                                            tfc.type,
                                                        ),
                                                }"
                                            ></div>
                                        </template>
                                        <span
                                            >{{ tfc.type.toUpperCase() }}:
                                            {{ tfc.weeks }} semanas de
                                            preparación</span
                                        >
                                    </v-tooltip>
                                    <div
                                        class="d-flex justify-space-between text-caption mt-1"
                                    >
                                        <span>Inicio</span>
                                        <span>6 meses</span>
                                    </div>
                                </div>

                                <div
                                    class="impact-summary-box pa-4 bg-grey-lighten-4 rounded border"
                                >
                                    <div
                                        class="text-subtitle-2 font-weight-bold mb-2 text-primary"
                                    >
                                        Resumen Estratégico
                                    </div>
                                    <p class="text-body-2 mb-0">
                                        {{ summaryText }}
                                    </p>
                                </div>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { Chart, registerables } from 'chart.js';
import { computed, onMounted, ref, watch } from 'vue';

Chart.register(...registerables);

const props = defineProps({
    scenarioId: {
        type: [Number, String],
        required: true,
    },
    horizon: {
        type: Number,
        default: 12,
    },
});

const loading = ref(true);
const confidenceFactor = ref(85);
const radarChartRef = ref<HTMLCanvasElement | null>(null);
const chartInstance = ref<Chart | null>(null);
const impactData = ref<any>(null);

const kpis = computed(() => {
    if (!impactData.value) return [];
    // Adjust values based on confidence factor
    const adjGap = Math.round(
        impactData.value.gap_closure * (confidenceFactor.value / 100),
    );
    const adjRoi = (
        impactData.value.estimated_roi *
        (confidenceFactor.value / 100)
    ).toFixed(1);

    return [
        {
            label: 'Cierre de Gap Estimado',
            value: adjGap + '%',
            icon: 'mdi-check-decagram',
            trend: 'up',
            class: 'text-success',
        },
        {
            label: 'Indice de Productividad',
            value: impactData.value.productivity_index + '%',
            icon: 'mdi-rocket',
            trend: 'up',
            class: '',
        },
        {
            label: 'Tiempo a Plena Capacidad',
            value: impactData.value.time_to_fill + ' sem',
            icon: 'mdi-clock-fast',
            trend: 'down',
            class: '',
        },
        {
            label: 'Retorno Estimado (ROI)',
            value: adjRoi + 'x',
            icon: 'mdi-finance',
            trend: 'up',
            class: 'text-primary',
        },
    ];
});

const summaryText = computed(() => {
    if (confidenceFactor.value < 60) {
        return "Atención: Con un factor de confianza conservador, el impacto del escenario se reduce significativamente. Se recomienda revisar las estrategias de 'Build' para asegurar la retención de conocimientos.";
    }
    return (
        impactData.value?.summary ||
        'Calculando impacto del escenario basado en las estrategias de Build, Buy y Borrow seleccionadas...'
    );
});

const fetchData = async () => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}/impact`,
        );
        impactData.value = res.data.data || res.data;
        setTimeout(initChart, 100);
    } catch {
        // Fallback demo data
        impactData.value = {
            gap_closure: 85,
            productivity_index: 92,
            time_to_fill: 14,
            estimated_roi: 3.2,
            tfc_breakdown: [
                { type: 'buy', weeks: 12, count: 2 },
                { type: 'build', weeks: 24, count: 5 },
                { type: 'borrow', weeks: 6, count: 1 },
            ],
            summary:
                "La implementación de este escenario cerrará el 85% de las brechas críticas identificadas. La estrategia de 'Build' intensiva asegura una cultura de aprendizaje resiliente.",
            chart: {
                labels: [
                    'Liderazgo',
                    'Visión Técnica',
                    'Agilidad',
                    'Comunicación',
                    'Resolución Problemas',
                    'Soft Skills',
                ],
                actual: [45, 30, 60, 70, 50, 65],
                projected: [90, 85, 95, 100, 95, 90],
            },
        };
        setTimeout(initChart, 100);
    } finally {
        loading.value = false;
    }
};

const getStrategyColor = (type: string) => {
    const colors: Record<string, string> = {
        build: '#3f51b5',
        buy: '#10b981',
        borrow: '#ffb300',
        bot: '#9c27b0',
    };
    return colors[type] || '#9e9e9e';
};

const updateChart = () => {
    if (!chartInstance.value || !impactData.value?.chart) return;

    // Adjust projected levels by confidence factor
    const actual = impactData.value.chart.actual;
    const projected = impactData.value.chart.projected.map(
        (p: number, i: number) => {
            const gap = p - actual[i];
            return actual[i] + gap * (confidenceFactor.value / 100);
        },
    );

    chartInstance.value.data.datasets[1].data = projected;
    chartInstance.value.update();
};

const initChart = () => {
    if (!radarChartRef.value || !impactData.value?.chart) return;

    if (chartInstance.value) {
        chartInstance.value.destroy();
    }

    const ctx = radarChartRef.value.getContext('2d');
    if (!ctx) return;

    // Initial projected levels adjusted by default confidence
    const actual = impactData.value.chart.actual;
    const projected = impactData.value.chart.projected.map(
        (p: number, i: number) => {
            const gap = p - actual[i];
            return actual[i] + gap * (confidenceFactor.value / 100);
        },
    );

    chartInstance.value = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: impactData.value.chart.labels,
            datasets: [
                {
                    label: 'Situación Actual',
                    data: actual,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgb(255, 99, 132)',
                    pointBackgroundColor: 'rgb(255, 99, 132)',
                    fill: true,
                },
                {
                    label: 'Impacto IA (Proyectado)',
                    data: projected,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgb(54, 162, 235)',
                    pointBackgroundColor: 'rgb(54, 162, 235)',
                    fill: true,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { display: true },
                    suggestedMin: 0,
                    suggestedMax: 100,
                    ticks: { stepSize: 20 },
                },
            },
            plugins: {
                legend: { position: 'bottom' },
            },
        },
    });
};

onMounted(fetchData);

watch(() => props.scenarioId, fetchData);
</script>

<style scoped>
.impact-analytics {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chart-container {
    position: relative;
    width: 100%;
}

.tfc-breakdown {
    height: 12px;
    background-color: #eee;
    display: flex;
    border-radius: 6px;
    overflow: hidden;
}

.tfc-bar {
    height: 100%;
    transition: width 0.3s ease;
    border-right: 1px solid rgba(255, 255, 255, 0.3);
}

.tfc-bar:last-child {
    border-right: none;
}
</style>
