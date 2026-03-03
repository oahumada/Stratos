<template>
    <div class="impact-analytics animate-in space-y-8 duration-700 fade-in">
        <!-- Impact Summary Card -->
        <StCardGlass
            variant="glass"
            border-accent="indigo"
            class="overflow-hidden p-0!"
            :no-hover="true"
        >
            <div
                class="flex flex-col gap-6 border-b border-white/10 bg-indigo-500/10 px-8 py-5 md:flex-row md:items-center md:justify-between"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/20 shadow-[0_0_20px_rgba(99,102,241,0.3)]"
                    >
                        <v-icon
                            icon="mdi-chart-radar"
                            color="indigo-300"
                            size="24"
                        />
                    </div>
                    <div>
                        <h3
                            class="text-xl font-black tracking-tight text-white"
                        >
                            Neural
                            <span class="text-indigo-400">Impact</span> Analysis
                        </h3>
                        <div
                            class="flex items-center gap-2 text-[10px] font-black tracking-widest text-white/40 uppercase"
                        >
                            <span
                                >Projection Horizon: {{ horizon }} Months</span
                            >
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <StButtonGlass
                        variant="ghost"
                        size="sm"
                        icon="mdi-file-excel"
                        @click="downloadFinancialReport"
                    >
                        CFO Abstract
                    </StButtonGlass>
                    <StButtonGlass
                        variant="ghost"
                        size="sm"
                        icon="mdi-printer"
                        @click="printReport"
                    >
                        Snapshot
                    </StButtonGlass>
                </div>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-12">
                    <!-- Column: Radar Chart & Confidence -->
                    <div class="space-y-10 lg:col-span-7">
                        <div class="flex items-end justify-between">
                            <div>
                                <h4
                                    class="text-sm font-black tracking-[0.2em] text-white/40 uppercase"
                                >
                                    Skill Gap Closure Matrix
                                </h4>
                                <p
                                    class="mt-1 text-xs font-medium text-white/20"
                                >
                                    Benchmarking current vs projected neural
                                    capacity.
                                </p>
                            </div>
                            <div class="w-48 space-y-2">
                                <div
                                    class="flex justify-between text-[9px] font-black tracking-widest text-indigo-400 uppercase"
                                >
                                    <span>Execution Alpha</span>
                                    <span>{{ confidenceFactor }}%</span>
                                </div>
                                <v-slider
                                    v-model="confidenceFactor"
                                    min="30"
                                    max="100"
                                    step="5"
                                    density="compact"
                                    hide-details
                                    color="indigo-accent-2"
                                    @update:model-value="updateChart"
                                    class="confidence-slider"
                                ></v-slider>
                            </div>
                        </div>

                        <div
                            class="chart-wrapper group relative h-[400px] overflow-hidden rounded-3xl border border-white/5 bg-white/[0.02] p-8"
                        >
                            <!-- Background Gradients for Chart -->
                            <div
                                class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(99,102,241,0.05),transparent_70%)]"
                            ></div>

                            <div
                                v-if="loading"
                                class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-black/20 backdrop-blur-sm"
                            >
                                <v-progress-circular
                                    indeterminate
                                    color="indigo-400"
                                    size="48"
                                    width="4"
                                />
                                <span
                                    class="mt-4 text-[10px] font-black tracking-[0.3em] text-indigo-400/60 uppercase"
                                    >Mapping Neurons...</span
                                >
                            </div>
                            <canvas ref="radarChartRef"></canvas>
                        </div>
                    </div>

                    <!-- Column: KPIs & Risk -->
                    <div class="space-y-8 lg:col-span-5">
                        <div>
                            <h4
                                class="mb-6 text-center text-sm font-black tracking-[0.2em] text-white/40 uppercase lg:text-left"
                            >
                                Projected Vector Analytics
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    v-for="kpi in kpis"
                                    :key="kpi.label"
                                    class="group/kpi rounded-2xl border border-white/5 bg-white/[0.03] p-5 transition-all duration-300 hover:border-indigo-500/20 hover:bg-white/[0.07]"
                                >
                                    <div
                                        class="mb-3 flex items-center justify-between"
                                    >
                                        <v-icon
                                            :icon="kpi.icon"
                                            color="indigo-400/50"
                                            size="18"
                                            class="transition-transform group-hover/kpi:scale-110"
                                        />
                                        <v-icon
                                            :color="
                                                kpi.trend === 'up'
                                                    ? 'emerald-400'
                                                    : 'rose-400'
                                            "
                                            size="14"
                                        >
                                            {{
                                                kpi.trend === 'up'
                                                    ? 'mdi-trending-up'
                                                    : 'mdi-trending-down'
                                            }}
                                        </v-icon>
                                    </div>
                                    <div
                                        class="text-xl font-black text-white transition-colors group-hover/kpi:text-indigo-300"
                                    >
                                        {{ kpi.value }}
                                    </div>
                                    <div
                                        class="mt-1 text-[9px] font-black tracking-widest text-white/30 uppercase"
                                    >
                                        {{ kpi.label }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <v-divider class="border-white/5"></v-divider>

                        <!-- TFC Timeline -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h5
                                    class="text-[10px] font-black tracking-widest text-indigo-400 uppercase"
                                >
                                    Velocity to Peak Performance
                                </h5>
                                <span
                                    class="text-[10px] font-bold text-white/40"
                                    >24 WEEK SCALE</span
                                >
                            </div>
                            <div
                                class="relative flex h-2 w-full overflow-hidden rounded-full bg-white/5"
                            >
                                <v-tooltip
                                    v-for="tfc in impactData?.tfc_breakdown ||
                                    []"
                                    :key="tfc.type"
                                    location="top"
                                    offset="12"
                                >
                                    <template #activator="{ props }">
                                        <div
                                            v-bind="props"
                                            class="h-full cursor-help border-r border-black/20 transition-all duration-1000 last:border-0 hover:brightness-125"
                                            :style="{
                                                width:
                                                    (tfc.weeks / 24) * 100 +
                                                    '%',
                                                backgroundColor:
                                                    getStrategyColor(tfc.type),
                                                boxShadow: `inset 0 0 10px rgba(0,0,0,0.2)`,
                                            }"
                                        ></div>
                                    </template>
                                    <StCardGlass
                                        variant="glass"
                                        class="min-w-[140px] border-white/20 p-3! backdrop-blur-xl"
                                    >
                                        <div
                                            class="mb-1 text-[8px] font-black tracking-widest uppercase"
                                            :style="`color: ${getStrategyColor(tfc.type)}`"
                                        >
                                            {{ tfc.type }}
                                        </div>
                                        <div
                                            class="text-xs font-bold text-white"
                                        >
                                            {{ tfc.weeks }} Weeks Readiness
                                        </div>
                                        <div
                                            class="mt-1 text-[9px] text-white/40"
                                        >
                                            {{ tfc.count }} Critical Assets
                                        </div>
                                    </StCardGlass>
                                </v-tooltip>
                            </div>
                        </div>

                        <!-- Risk & Context -->
                        <div
                            class="space-y-6 rounded-3xl border border-white/5 bg-black/40 p-6"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <v-icon
                                        icon="mdi-shield-alert-outline"
                                        color="amber-400"
                                        size="20"
                                    />
                                    <span
                                        class="text-xs font-black tracking-widest text-white/60 uppercase"
                                        >Execution Vulnerability</span
                                    >
                                </div>
                                <StBadgeGlass
                                    :variant="
                                        getRiskBadgeVariant(
                                            impactData?.risk_level,
                                        )
                                    "
                                    size="xs"
                                >
                                    {{
                                        (
                                            impactData?.risk_level || 'LOW'
                                        ).toUpperCase()
                                    }}
                                </StBadgeGlass>
                            </div>

                            <div class="space-y-4">
                                <div
                                    v-if="impactData?.risk_factors?.length"
                                    class="space-y-2"
                                >
                                    <div
                                        v-for="(
                                            factor, idx
                                        ) in impactData.risk_factors"
                                        :key="idx"
                                        class="group/factor flex items-start gap-3"
                                    >
                                        <div
                                            class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-amber-400 transition-transform group-hover/factor:scale-150"
                                        ></div>
                                        <span
                                            class="text-[11px] leading-relaxed font-medium text-white/50 transition-colors group-hover/factor:text-white/80"
                                            >{{ factor }}</span
                                        >
                                    </div>
                                </div>

                                <div
                                    v-if="impactData?.mitigations?.length"
                                    class="space-y-2 border-t border-white/5 pt-4"
                                >
                                    <div
                                        class="mb-2 text-[9px] font-black tracking-widest text-emerald-400 uppercase"
                                    >
                                        Neural Guards
                                    </div>
                                    <div
                                        v-for="(
                                            action, idx
                                        ) in impactData.mitigations"
                                        :key="'m-' + idx"
                                        class="group/mit flex items-start gap-3"
                                    >
                                        <v-icon
                                            icon="mdi-check-decagram"
                                            size="12"
                                            color="emerald-500/50"
                                            class="group-hover/mit:color-emerald-400 mt-1 transition-colors"
                                        />
                                        <span
                                            class="text-[11px] leading-relaxed font-medium text-emerald-100/40 transition-colors group-hover/mit:text-emerald-100/70"
                                            >{{ action }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deep Insight Banner -->
            <div class="border-t border-white/5 bg-indigo-500/5 p-6">
                <div class="flex items-start gap-4">
                    <v-icon
                        icon="mdi-text-box-search-outline"
                        color="indigo-400"
                        size="20"
                        class="mt-0.5"
                    />
                    <div>
                        <div
                            class="mb-1 text-[10px] font-black tracking-[0.2em] text-indigo-300 uppercase"
                        >
                            Strategic Narrative Insight
                        </div>
                        <p
                            class="text-[13px] leading-relaxed font-medium text-white/70 italic"
                        >
                            "{{ summaryText }}"
                        </p>
                    </div>
                </div>
            </div>
        </StCardGlass>
    </div>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
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
            label: 'Gap Mitigation',
            value: adjGap + '%',
            icon: 'mdi-check-decagram-outline',
            trend: 'up',
        },
        {
            label: 'Productivity Index',
            value: impactData.value.productivity_index + '%',
            icon: 'mdi-auto-fix',
            trend: 'up',
        },
        {
            label: 'Time to Full Capacity',
            value: impactData.value.time_to_fill + 'w',
            icon: 'mdi-clock-fast',
            trend: 'down',
        },
        {
            label: 'Estimated ROI',
            value: adjRoi + 'x',
            icon: 'mdi-finance',
            trend: 'up',
        },
    ];
});

const summaryText = computed(() => {
    if (confidenceFactor.value < 60) {
        return "Conservative Execution: Alpha levels below 60% indicate critical vulnerability in knowledge retention. Structural 'Build' reinforcement recommended.";
    }
    return (
        impactData.value?.summary ||
        'Synthesizing strategic impact based on the active modular tactical mix...'
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
            risk_score: 42,
            risk_level: 'Medium',
            risk_factors: [
                'Moderate dependency on external talent acquisitions (35%)',
                'Source data fidelity at acceptable threshold (72%)',
                'IA Agent learning curve estimated at 16 neural cycles',
            ],
            mitigations: [
                'Validate current latency through express technical assessment.',
                'Initiate structural change management protocol (AI Adoption).',
            ],
            tfc_breakdown: [
                { type: 'buy', weeks: 12, count: 2 },
                { type: 'build', weeks: 24, count: 5 },
                { type: 'borrow', weeks: 6, count: 1 },
            ],
            summary:
                "Scenario execution will mitigate 85% of critical architectural gaps. The intensive 'Build' strategy ensures a resilient, high-fidelity learning culture.",
            chart: {
                labels: [
                    'Leadership',
                    'Technical Vision',
                    'Agility',
                    'Communication',
                    'Resilience',
                    'Synthetic Op',
                ],
                actual: [45, 30, 60, 70, 50, 20],
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
        build: '#818cf8',
        buy: '#34d399',
        borrow: '#fbbf24',
        bot: '#a78bfa',
    };
    return colors[type] || '#94a3b8';
};

const getRiskBadgeVariant = (level: string) => {
    switch (level?.toLowerCase()) {
        case 'critical':
            return 'secondary';
        case 'high':
            return 'secondary';
        case 'medium':
            return 'success';
        case 'low':
            return 'primary';
        default:
            return 'glass';
    }
};

const updateChart = () => {
    if (!chartInstance.value || !impactData.value?.chart) return;

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

    if (chartInstance.value) chartInstance.value.destroy();

    const ctx = radarChartRef.value.getContext('2d');
    if (!ctx) return;

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
                    label: 'Current State (As-Is)',
                    data: actual,
                    backgroundColor: 'rgba(244, 63, 94, 0.1)',
                    borderColor: 'rgb(244, 63, 94)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(244, 63, 94)',
                    pointBorderColor: '#000',
                    fill: true,
                },
                {
                    label: 'Projected Target (To-Be)',
                    data: projected,
                    backgroundColor: 'rgba(99, 102, 241, 0.2)',
                    borderColor: 'rgb(99, 102, 241)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(99, 102, 241)',
                    pointBorderColor: '#000',
                    fill: true,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { color: 'rgba(255, 255, 255, 0.05)' },
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    suggestedMin: 0,
                    suggestedMax: 100,
                    ticks: { display: false },
                    pointLabels: {
                        color: 'rgba(255, 255, 255, 0.5)',
                        font: { family: 'Inter', size: 10, weight: 'bold' },
                    },
                },
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        color: 'rgba(255, 255, 255, 0.4)',
                        font: { size: 10, weight: '900' },
                        usePointStyle: true,
                        boxWidth: 6,
                        padding: 20,
                    },
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 11 },
                    padding: 12,
                    cornerRadius: 12,
                    displayColors: true,
                },
            },
        },
    });
};

const printReport = () => globalThis.print();
const downloadFinancialReport = () =>
    window.open(
        `/api/strategic-planning/scenarios/${props.scenarioId}/export-financial`,
        '_blank',
    );

onMounted(fetchData);
watch(() => props.scenarioId, fetchData);
</script>

<style scoped>
.chart-wrapper canvas {
    filter: drop-shadow(0 0 10px rgba(99, 102, 241, 0.1));
}

:deep(.confidence-slider .v-slider-track__background) {
    background-color: rgba(255, 255, 255, 0.05) !important;
}

@media print {
    .impact-analytics {
        background: white !important;
        color: black !important;
    }
    :deep(.v-btn),
    :deep(.v-slider) {
        display: none !important;
    }
}
</style>
