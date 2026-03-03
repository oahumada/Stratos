<script setup lang="ts">
import SentinelHealthWidget from '@/components/SentinelHealthWidget.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import AgenticScenarioPlanner from './AgenticScenarioPlanner.vue';
import CoverageChart from './Charts/CoverageChart.vue';
import DepartmentGapsChart from './Charts/DepartmentGapsChart.vue';
import HeadcountChart from './Charts/HeadcountChart.vue';
import CrisisSimulator from './CrisisSimulator.vue';
import MatchingResults from './MatchingResults.vue';
import RoleForecastsTable from './RoleForecastsTable.vue';
import ScenarioRoiCalculator from './ScenarioRoiCalculator.vue';
import ScenarioStrategyAssigner from './ScenarioStrategyAssigner.vue';
import SkillGapsMatrix from './SkillGapsMatrix.vue';
import SuccessionPlanCard from './SuccessionPlanCard.vue';

defineOptions({ layout: AppLayout });

interface Analytics {
    total_headcount_current: number;
    total_headcount_projected: number;
    net_growth: number;
    internal_coverage_percentage: number;
    external_gap_percentage: number;
    total_skills_required: number;
    skills_with_gaps: number;
    critical_skills_at_risk: number;
    critical_roles: number;
    critical_roles_with_successor: number;
    succession_risk_percentage: number;
    estimated_recruitment_cost: number;
    estimated_training_cost: number;
    estimated_external_hiring_months: number;
    high_risk_positions: number;
    medium_risk_positions: number;
    critical_gaps: number;
    high_gaps: number;
    medium_gaps: number;
    low_gaps: number;
    immediately_ready: number;
    ready_within_six: number;
    ready_within_twelve: number;
    beyond_twelve: number;
}

interface Props {
    id: number | string;
}

const props = defineProps<Props>();
const page = usePage();
const api = useApi();
const { showSuccess, showError } = useNotification();

const scenarioId = computed(() => {
    const id = props.id;
    return typeof id === 'string' ? parseInt(id, 10) : id || 0;
});
const scenarioName = ref('');
const scenarioDescription = ref('');
const analyzing = ref(false);
const activeTab = ref('overview');

const analytics = ref<Analytics>({
    total_headcount_current: 0,
    total_headcount_projected: 0,
    net_growth: 0,
    internal_coverage_percentage: 0,
    external_gap_percentage: 0,
    total_skills_required: 0,
    skills_with_gaps: 0,
    critical_skills_at_risk: 0,
    critical_roles: 0,
    critical_roles_with_successor: 0,
    succession_risk_percentage: 0,
    estimated_recruitment_cost: 0,
    estimated_training_cost: 0,
    estimated_external_hiring_months: 0,
    high_risk_positions: 0,
    medium_risk_positions: 0,
    critical_gaps: 0,
    high_gaps: 0,
    medium_gaps: 0,
    low_gaps: 0,
    immediately_ready: 0,
    ready_within_six: 0,
    ready_within_twelve: 0,
    beyond_twelve: 0,
});

const formatNumber = (num: number): string => {
    return new Intl.NumberFormat('en-US').format(num);
};

// Helper functions for chart data aggregation
const _countGapsByPriority = (priority: string): number => {
    const gapPriorities: { [key: string]: number } = {
        critical: 3,
        high: 4,
        medium: 5,
        low: 2,
    };
    return gapPriorities[priority] || 0;
};

const _countByReadiness = (level: string): number => {
    const readinessCounts: { [key: string]: number } = {
        immediately: 3,
        within_six: 4,
        within_twelve: 2,
        beyond_twelve: 1,
    };
    return readinessCounts[level] || 0;
};

const _getAllMatchScores = (): number[] => {
    return [95, 87, 92, 78, 84, 91, 56, 71, 88, 82];
};

const getDepartments = (): string[] => {
    return ['Engineering', 'Sales', 'Marketing', 'HR', 'Finance'];
};

const getGapCountsByDepartment = (): number[] => {
    return [3, 2, 4, 1, 2];
};

const loadScenario = async () => {
    try {
        const response = await api.get(
            `/api/strategic-planning/scenarios/${scenarioId.value}`,
        );
        const scenario = (response as any).data;
        scenarioName.value = scenario.name;
        scenarioDescription.value = scenario.description;
    } catch (e) {
        void e;
        showError('Failed to load scenario');
    }
};

const loadAnalytics = async () => {
    return;
};

// Growth Simulator
const simulationParams = ref({
    growth_percentage: 25,
    horizon_months: 24,
    external_hiring_ratio: 30,
    retention_target: 95,
});

const simulationResults = ref<any>(null);
const criticalTalents = ref<any[]>([]);
const criticalTalentsCount = computed(() => criticalTalents.value.length);

const criticalTalentsHeaders = [
    { title: 'Talent Area / Capability', value: 'capability' },
    { title: 'Role Archetype', value: 'role_archetype' },
    { title: 'Criticality Score', value: 'criticality_score' },
    { title: 'Risk Status', value: 'risk_status' },
    { title: 'Succession (Ready Now)', value: 'internal_succession.ready_now' },
    { title: 'Mitigation Strategy', value: 'mitigation_strategy' },
];

const runSimulation = async () => {
    if (!scenarioId.value || scenarioId.value <= 0) return;
    try {
        const response = await api.post(
            `/api/strategic-planning/scenarios/${scenarioId.value}/simulate-growth`,
            simulationParams.value,
        );
        const result: any = (response as any).data;
        simulationResults.value = result.simulation;
        showSuccess('Simulación ejecutada correctamente');
    } catch (error: any) {
        console.error('Simulation error:', error);
        showError('Error al ejecutar la simulación');
    }
};

const loadCriticalTalents = async () => {
    if (!scenarioId.value || scenarioId.value <= 0) return;
    try {
        const response = await api.get(
            '/api/strategic-planning/critical-talents',
            { scenario_id: scenarioId.value },
        );
        const result: any = (response as any).data;
        criticalTalents.value = Array.isArray(result)
            ? result
            : result.data || [];
    } catch (error: any) {
        console.error('Critical talents error:', error);
        showError('Error al cargar talentos críticos');
    }
};

const getRiskColor = (riskStatus: string): string => {
    switch (riskStatus) {
        case 'HIGH':
            return 'error';
        case 'MEDIUM':
            return 'warning';
        case 'LOW':
            return 'success';
        default:
            return 'grey';
    }
};

const runAnalysis = async () => {
    if (!scenarioId.value || scenarioId.value <= 0) return;
    try {
        analyzing.value = true;
        const res = await api.post(
            `/api/strategic-planning/scenarios/${scenarioId.value}/calculate-gaps`,
            {},
        );
        const result: any = (res as any).data || res;
        if (result && result.summary) {
            analytics.value.total_skills_required =
                result.summary.total_required_skills ||
                analytics.value.total_skills_required;
            analytics.value.skills_with_gaps =
                result.summary.critical_skills_count ??
                analytics.value.skills_with_gaps;
            analytics.value.internal_coverage_percentage = Math.round(
                result.summary.coverage_pct ?? 0,
            );
        }
        showSuccess('Brechas calculadas correctamente');
    } catch (e: any) {
        console.error('calculate-gaps error', e);
        const msg =
            e?.response?.data?.message ||
            e?.message ||
            'Error al calcular brechas';
        showError(msg);
    } finally {
        analyzing.value = false;
    }
};

const generateStrategies = async () => {
    if (!scenarioId.value || scenarioId.value <= 0) return;
    try {
        const res = await api.post(
            `/api/strategic-planning/scenarios/${scenarioId.value}/refresh-suggested-strategies`,
            {},
        );
        const created = (res as any)?.created ?? 0;
        showSuccess(`Estrategias sugeridas actualizadas (${created} nuevas)`);
    } catch (e: any) {
        console.error('refresh-suggested-strategies error', e);
        const msg =
            e?.response?.data?.message ||
            e?.message ||
            'Error al generar estrategias';
        showError(msg);
    }
};

void page;
void loadAnalytics;

const downloadReport = () => {
    showSuccess('Descarga de reporte aún no implementada');
};

onMounted(() => {
    loadScenario();
    loadCriticalTalents();
});
</script>

<template>
    <div class="st-glass-container pa-8">
        <!-- Dashboard Header -->
        <header class="mb-10">
            <div
                class="d-flex align-center justify-space-between flex-wrap gap-6"
            >
                <div class="grow">
                    <div class="d-flex align-center mb-2 gap-3">
                        <v-btn
                            icon
                            variant="text"
                            color="white"
                            @click="
                                $inertia.visit('/strategic-planning/scenarios')
                            "
                            class="rounded-xl border border-white/10 bg-white/5"
                        >
                            <v-icon>mdi-arrow-left</v-icon>
                        </v-btn>
                        <h1
                            class="text-h3 font-weight-black tracking-tighter text-white"
                        >
                            {{ scenarioName || 'Scenario Detail' }}
                            <span class="st-badge-live ml-2">SIMULATION</span>
                        </h1>
                    </div>
                    <p
                        class="text-h6 font-weight-regular max-w-2xl leading-relaxed text-indigo-100/60"
                    >
                        {{
                            scenarioDescription ||
                            'Executive Strategic Overview and Performance Projections'
                        }}
                    </p>
                </div>

                <div class="header-actions d-flex items-center gap-4">
                    <StButtonGlass
                        variant="glass"
                        icon="mdi-refresh"
                        :loading="analyzing"
                        @click="runAnalysis"
                    >
                        Analyze Gaps
                    </StButtonGlass>

                    <StButtonGlass
                        variant="secondary"
                        icon="mdi-lightbulb-on"
                        @click="generateStrategies"
                    >
                        Generate Strategies
                    </StButtonGlass>

                    <StButtonGlass
                        variant="ghost"
                        icon="mdi-download"
                        @click="downloadReport"
                    >
                        Export
                    </StButtonGlass>
                </div>
            </div>
        </header>

        <!-- Executive Navigation -->
        <div class="tab-scroll-container mb-10">
            <v-tabs
                v-model="activeTab"
                bg-color="transparent"
                color="indigo-accent-2"
                align-tabs="start"
                class="glass-tabs"
            >
                <v-tab
                    value="overview"
                    class="text-none font-weight-bold tracking-wide"
                >
                    <v-icon start>mdi-view-dashboard</v-icon>Overview
                </v-tab>
                <v-tab
                    value="simulator"
                    class="text-none font-weight-bold tracking-wide"
                >
                    <v-icon start>mdi-chart-timeline</v-icon>Simulator
                </v-tab>
                <v-tab
                    value="critical"
                    class="text-none font-weight-bold tracking-wide"
                >
                    <v-icon start>mdi-alert-decagram</v-icon>Critical Risk ({{
                        criticalTalentsCount
                    }})
                </v-tab>
                <v-tab
                    value="forecasts"
                    class="text-none font-weight-bold tracking-wide"
                >
                    <v-icon start>mdi-account-clock</v-icon>Forecasts
                </v-tab>
                <v-tab
                    value="matches"
                    class="text-none font-weight-bold tracking-wide"
                >
                    <v-icon start>mdi-transit-connection-variant</v-icon>Matches
                </v-tab>
                <v-tab
                    value="gaps"
                    class="text-none font-weight-bold tracking-wide"
                >
                    <v-icon start>mdi-lightning-bolt</v-icon>Skill Gaps
                </v-tab>
                <v-tab
                    value="succession"
                    class="text-none font-weight-bold tracking-wide"
                >
                    <v-icon start>mdi-sync</v-icon>Succession
                </v-tab>
                <v-tab
                    value="roi"
                    class="text-none font-weight-bold tracking-wide"
                >
                    <v-icon start>mdi-calculator</v-icon>ROI Analysis
                </v-tab>
                <v-tab
                    value="strategies"
                    class="text-none font-weight-bold tracking-wide"
                >
                    <v-icon start>mdi-target</v-icon>Strategies
                </v-tab>
                <v-tab
                    value="crisis"
                    class="text-none font-weight-bold tracking-wide"
                >
                    <v-icon start>mdi-shield-alert</v-icon>Crisis Sim
                </v-tab>
                <v-tab
                    value="agentic"
                    class="text-none font-weight-bold tracking-wide"
                >
                    <v-icon start>mdi-robot-outline</v-icon>Agentic
                </v-tab>
            </v-tabs>
        </div>

        <!-- Main Content Transitions -->
        <div class="tab-content-wrapper overflow-hidden pb-12">
            <transition name="fade-slide" mode="out-in">
                <!-- Tab: Overview -->
                <div v-if="activeTab === 'overview'" :key="'overview'">
                    <!-- KPI Cards -->
                    <v-row class="mb-8">
                        <v-col cols="12" sm="6" md="3">
                            <StCardGlass
                                class="pa-6 relative h-full overflow-hidden border-white/5"
                                :no-hover="false"
                            >
                                <div class="kpi-accent bg-indigo-500"></div>
                                <div
                                    class="text-overline font-weight-black pl-2 tracking-widest text-white/50"
                                >
                                    Current Headcount
                                </div>
                                <div
                                    class="d-flex mt-2 gap-2 pl-2 align-baseline"
                                >
                                    <div
                                        class="text-h3 font-weight-black text-white"
                                    >
                                        {{
                                            formatNumber(
                                                analytics.total_headcount_current,
                                            )
                                        }}
                                    </div>
                                    <div
                                        class="text-caption font-weight-bold text-indigo-300"
                                    >
                                        <v-icon size="14"
                                            >mdi-arrow-right</v-icon
                                        >
                                        {{
                                            formatNumber(
                                                analytics.total_headcount_projected,
                                            )
                                        }}
                                    </div>
                                </div>
                            </StCardGlass>
                        </v-col>

                        <v-col cols="12" sm="6" md="3">
                            <StCardGlass
                                class="pa-6 relative h-full overflow-hidden border-white/5"
                                :no-hover="false"
                            >
                                <div class="kpi-accent bg-emerald-500"></div>
                                <div
                                    class="text-overline font-weight-black pl-2 tracking-widest text-white/50"
                                >
                                    Net Capacity Expansion
                                </div>
                                <div
                                    class="d-flex mt-2 gap-2 pl-2 align-baseline"
                                >
                                    <div
                                        class="text-h3 font-weight-black text-emerald-400"
                                    >
                                        {{ analytics.net_growth > 0 ? '+' : ''
                                        }}{{
                                            formatNumber(analytics.net_growth)
                                        }}
                                    </div>
                                    <div
                                        class="text-caption font-weight-bold ml-2 tracking-tighter text-emerald-200/50 uppercase"
                                    >
                                        FTE IMPACT
                                    </div>
                                </div>
                            </StCardGlass>
                        </v-col>

                        <v-col cols="12" sm="6" md="3">
                            <StCardGlass
                                class="pa-6 relative h-full overflow-hidden border-white/5"
                                :no-hover="false"
                            >
                                <div class="kpi-accent bg-amber-500"></div>
                                <div
                                    class="text-overline font-weight-black pl-2 tracking-widest text-white/50"
                                >
                                    Internal Coverage
                                </div>
                                <div
                                    class="d-flex mt-2 gap-2 pl-2 align-baseline"
                                >
                                    <div
                                        class="text-h3 font-weight-black text-amber-200"
                                    >
                                        {{
                                            analytics.internal_coverage_percentage
                                        }}%
                                    </div>
                                    <div
                                        class="text-caption font-weight-bold ml-2 tracking-tighter text-amber-400/50 uppercase"
                                    >
                                        Gap:
                                        {{ analytics.external_gap_percentage }}%
                                    </div>
                                </div>
                                <v-progress-linear
                                    :model-value="
                                        analytics.internal_coverage_percentage
                                    "
                                    color="amber-400"
                                    height="4"
                                    class="mt-4 rounded-xl"
                                />
                            </StCardGlass>
                        </v-col>

                        <v-col cols="12" sm="6" md="3">
                            <StCardGlass
                                class="pa-6 relative h-full overflow-hidden border-white/5"
                                :no-hover="false"
                            >
                                <div class="kpi-accent bg-rose-500"></div>
                                <div
                                    class="text-overline font-weight-black pl-2 tracking-widest text-white/50"
                                >
                                    Succession Risk
                                </div>
                                <div
                                    class="d-flex mt-2 gap-2 pl-2 align-baseline"
                                >
                                    <div
                                        class="text-h3 font-weight-black text-rose-400"
                                    >
                                        {{
                                            analytics.succession_risk_percentage
                                        }}%
                                    </div>
                                    <div
                                        class="st-badge-live ml-2 border border-rose-500/30 bg-rose-500/20 text-rose-400"
                                    >
                                        CRITICAL
                                    </div>
                                </div>
                            </StCardGlass>
                        </v-col>
                    </v-row>

                    <!-- Chart & Insights Row -->
                    <v-row class="mb-8">
                        <v-col cols="12" md="8">
                            <StCardGlass
                                class="pa-8 h-full border-white/5"
                                :no-hover="true"
                            >
                                <div
                                    class="d-flex align-center justify-space-between mb-8"
                                >
                                    <h3
                                        class="text-h5 font-weight-black tracking-tight text-white"
                                    >
                                        Capacity Evolution Projections
                                    </h3>
                                    <div class="d-flex gap-2">
                                        <v-chip
                                            size="small"
                                            variant="tonal"
                                            color="indigo-accent-2"
                                            label
                                            class="rounded-lg"
                                            >CURRENT</v-chip
                                        >
                                        <v-chip
                                            size="small"
                                            variant="tonal"
                                            color="emerald-accent-2"
                                            label
                                            class="rounded-lg"
                                            >PROJECTED</v-chip
                                        >
                                    </div>
                                </div>
                                <HeadcountChart
                                    :currentHeadcount="
                                        analytics.total_headcount_current
                                    "
                                    :projectedHeadcount="
                                        analytics.total_headcount_projected
                                    "
                                />
                            </StCardGlass>
                        </v-col>

                        <v-col cols="12" md="4">
                            <SentinelHealthWidget />
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12" md="6">
                            <StCardGlass
                                class="pa-8 h-full border-white/5"
                                :no-hover="true"
                            >
                                <h3
                                    class="text-h5 font-weight-black mb-8 tracking-tight text-white uppercase"
                                >
                                    Talent Pool Composition
                                </h3>
                                <CoverageChart
                                    :internalCoverage="
                                        analytics.internal_coverage_percentage
                                    "
                                    :externalGap="
                                        analytics.external_gap_percentage
                                    "
                                />
                            </StCardGlass>
                        </v-col>
                        <v-col cols="12" md="6">
                            <StCardGlass
                                class="pa-8 h-full border-white/5"
                                :no-hover="true"
                            >
                                <h3
                                    class="text-h5 font-weight-black mb-8 tracking-tight text-white uppercase"
                                >
                                    Execution Gaps by Department
                                </h3>
                                <DepartmentGapsChart
                                    :departments="getDepartments()"
                                    :gapCounts="getGapCountsByDepartment()"
                                />
                            </StCardGlass>
                        </v-col>
                    </v-row>
                </div>

                <!-- Tab: Simulator -->
                <div
                    v-else-if="activeTab === 'simulator'"
                    :key="'simulator'"
                    class="tab-content-anim"
                >
                    <StCardGlass class="pa-8 border-white/10" :no-hover="true">
                        <div class="d-flex align-center mb-10 gap-4">
                            <v-avatar
                                color="indigo-700"
                                size="48"
                                class="elevation-10"
                            >
                                <v-icon color="white"
                                    >mdi-chart-timeline</v-icon
                                >
                            </v-avatar>
                            <div>
                                <h2
                                    class="text-h4 font-weight-black tracking-tighter text-white"
                                >
                                    Growth Engine Simulator
                                </h2>
                                <p
                                    class="text-subtitle-1 leading-none text-white/50"
                                >
                                    Simulate scaling impacts on talent readiness
                                    and operational cost.
                                </p>
                            </div>
                        </div>

                        <v-row
                            class="align-center pa-6 mb-10 rounded-2xl border border-white/5 bg-white/5"
                        >
                            <v-col cols="12" md="3">
                                <v-text-field
                                    v-model.number="
                                        simulationParams.growth_percentage
                                    "
                                    label="Org Growth (%)"
                                    type="number"
                                    suffix="%"
                                    variant="outlined"
                                    class="glass-input"
                                    hide-details
                                />
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-select
                                    v-model="simulationParams.horizon_months"
                                    :items="[12, 18, 24, 36]"
                                    label="Horizon (Months)"
                                    variant="outlined"
                                    class="glass-input"
                                    hide-details
                                />
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-text-field
                                    v-model.number="
                                        simulationParams.external_hiring_ratio
                                    "
                                    label="External Hiring Ratio"
                                    type="number"
                                    suffix="%"
                                    variant="outlined"
                                    class="glass-input"
                                    hide-details
                                />
                            </v-col>
                            <v-col cols="12" md="3">
                                <StButtonGlass
                                    block
                                    variant="secondary"
                                    size="lg"
                                    @click="runSimulation"
                                    icon="mdi-play"
                                >
                                    Run Simulation
                                </StButtonGlass>
                            </v-col>
                        </v-row>

                        <div v-if="simulationResults">
                            <v-row class="mb-10">
                                <v-col
                                    cols="12"
                                    md="4"
                                    v-for="(stat, idx) in [
                                        {
                                            label: 'Projected Talent Req',
                                            val: simulationResults.projected_talent_requirement,
                                            color: 'indigo-400',
                                        },
                                        {
                                            label: 'Net Capacity Gap',
                                            val: simulationResults.net_capacity_gap,
                                            color: 'rose-400',
                                        },
                                        {
                                            label: 'Total Estimated OpEx',
                                            val:
                                                '$' +
                                                formatNumber(
                                                    simulationResults
                                                        .estimated_cost
                                                        ?.total || 0,
                                                ),
                                            color: 'emerald-400',
                                        },
                                    ]"
                                    :key="idx"
                                >
                                    <div
                                        class="pa-8 rounded-3xl border border-white/10 bg-white/5 text-center transition-all hover:bg-white/10"
                                    >
                                        <div
                                            class="text-caption font-weight-black mb-2 tracking-widest text-white/30 uppercase"
                                        >
                                            {{ stat.label }}
                                        </div>
                                        <div
                                            class="text-h3 font-weight-black"
                                            :class="'text-' + stat.color"
                                        >
                                            {{ stat.val }}
                                        </div>
                                    </div>
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col cols="12">
                                    <StCardGlass
                                        class="pa-0 overflow-hidden border-white/5"
                                        :no-hover="true"
                                    >
                                        <v-table class="glass-table">
                                            <thead>
                                                <tr>
                                                    <th
                                                        class="text-overline font-weight-black pl-8 text-white/40"
                                                    >
                                                        Strategic Capability
                                                        Area
                                                    </th>
                                                    <th
                                                        class="text-overline font-weight-black text-center text-white/40"
                                                    >
                                                        Current FTE
                                                    </th>
                                                    <th
                                                        class="text-overline font-weight-black text-center text-white/40"
                                                    >
                                                        Projected Req
                                                    </th>
                                                    <th
                                                        class="text-overline font-weight-black pr-8 text-right text-white/40"
                                                    >
                                                        Net Delta
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(
                                                        data, cat
                                                    ) in simulationResults.by_capability_area"
                                                    :key="cat"
                                                >
                                                    <td
                                                        class="font-weight-black pl-8 text-white"
                                                    >
                                                        {{ cat }}
                                                    </td>
                                                    <td
                                                        class="text-center text-white/70"
                                                    >
                                                        {{ data.current }}
                                                    </td>
                                                    <td
                                                        class="font-weight-black text-center text-indigo-300"
                                                    >
                                                        {{ data.projected }}
                                                    </td>
                                                    <td class="pr-8 text-right">
                                                        <v-chip
                                                            :color="
                                                                data.gap >= 0
                                                                    ? 'emerald-500/20'
                                                                    : 'rose-500/20'
                                                            "
                                                            :text-color="
                                                                data.gap >= 0
                                                                    ? 'emerald-400'
                                                                    : 'rose-400'
                                                            "
                                                            size="small"
                                                            variant="flat"
                                                            border
                                                            class="font-weight-bold"
                                                        >
                                                            {{
                                                                data.gap >= 0
                                                                    ? '+'
                                                                    : ''
                                                            }}{{ data.gap }}
                                                        </v-chip>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </v-table>
                                    </StCardGlass>
                                </v-col>
                            </v-row>
                        </div>
                    </StCardGlass>
                </div>

                <!-- Tab: Critical Talents -->
                <div
                    v-else-if="activeTab === 'critical'"
                    :key="'critical'"
                    class="tab-content-anim"
                >
                    <StCardGlass
                        class="pa-0 overflow-hidden border-white/10"
                        :no-hover="true"
                    >
                        <div
                            class="pa-8 border-b border-white/5 bg-rose-900/10"
                        >
                            <h2
                                class="text-h4 font-weight-black d-flex align-center gap-3 tracking-tighter text-rose-300"
                            >
                                <v-icon color="rose-400" size="40"
                                    >mdi-alert-decagram</v-icon
                                >
                                Critical Talent Exposure Map
                            </h2>
                            <p class="text-body-1 mt-2 text-rose-200/50">
                                Identification of bottleneck roles and
                                high-impact succession risks.
                            </p>
                        </div>
                        <v-data-table
                            :headers="criticalTalentsHeaders"
                            :items="criticalTalents"
                            class="glass-table"
                            density="comfortable"
                        >
                            <template #[`item.capability`]="{ item }">
                                <div class="font-weight-black pl-4 text-white">
                                    {{ item.capability }}
                                </div>
                            </template>
                            <template #[`item.risk_status`]="{ item }">
                                <v-chip
                                    :color="
                                        getRiskColor(item.risk_status) ===
                                        'error'
                                            ? 'red-500/20'
                                            : 'orange-500/20'
                                    "
                                    :text-color="
                                        getRiskColor(item.risk_status) ===
                                        'error'
                                            ? 'red-400'
                                            : 'orange-400'
                                    "
                                    variant="flat"
                                    size="small"
                                    border
                                    class="font-weight-black px-4"
                                >
                                    {{ item.risk_status }}
                                </v-chip>
                            </template>
                            <template
                                #[`item.internal_succession.ready_now`]="{
                                    item,
                                }"
                            >
                                <div class="d-flex align-center gap-2">
                                    <v-avatar
                                        :color="
                                            item.internal_succession.ready_now >
                                            0
                                                ? 'emerald-500/20'
                                                : 'rose-500/20'
                                        "
                                        size="28"
                                        class="border border-white/10"
                                    >
                                        <span
                                            class="text-caption font-weight-black"
                                            :class="
                                                item.internal_succession
                                                    .ready_now > 0
                                                    ? 'text-emerald-400'
                                                    : 'text-rose-400'
                                            "
                                        >
                                            {{
                                                item.internal_succession
                                                    .ready_now
                                            }}
                                        </span>
                                    </v-avatar>
                                    <span class="text-body-2 text-white/50"
                                        >Ready Now</span
                                    >
                                </div>
                            </template>
                        </v-data-table>
                    </StCardGlass>
                </div>

                <!-- Shared / Legacy Modular Tabs -->
                <div
                    v-else-if="activeTab === 'forecasts'"
                    :key="'forecasts'"
                    class="tab-content-anim"
                >
                    <RoleForecastsTable :scenarioId="scenarioId" />
                </div>
                <div
                    v-else-if="activeTab === 'matches'"
                    :key="'matches'"
                    class="tab-content-anim"
                >
                    <MatchingResults :scenarioId="scenarioId" />
                </div>
                <div
                    v-else-if="activeTab === 'gaps'"
                    :key="'gaps'"
                    class="tab-content-anim"
                >
                    <SkillGapsMatrix :scenarioId="scenarioId" />
                </div>
                <div
                    v-else-if="activeTab === 'succession'"
                    :key="'succession'"
                    class="tab-content-anim"
                >
                    <SuccessionPlanCard :scenarioId="scenarioId" />
                </div>
                <div
                    v-else-if="activeTab === 'roi'"
                    :key="'roi'"
                    class="tab-content-anim"
                >
                    <ScenarioRoiCalculator :scenarioId="scenarioId" />
                </div>
                <div
                    v-else-if="activeTab === 'strategies'"
                    :key="'strategies'"
                    class="tab-content-anim"
                >
                    <ScenarioStrategyAssigner :scenarioId="scenarioId" />
                </div>

                <div
                    v-else-if="activeTab === 'crisis'"
                    :key="'crisis'"
                    class="tab-content-anim"
                >
                    <CrisisSimulator :scenario-id="scenarioId" />
                </div>

                <div
                    v-else-if="activeTab === 'agentic'"
                    :key="'agentic'"
                    class="tab-content-anim"
                >
                    <AgenticScenarioPlanner :scenario-id="scenarioId" />
                </div>
            </transition>
        </div>
    </div>
</template>

<style scoped>
.st-glass-container {
    background:
        radial-gradient(circle at top left, rgba(30, 30, 50, 0.4), transparent),
        radial-gradient(
            circle at bottom right,
            rgba(20, 10, 40, 0.4),
            transparent
        );
    min-height: 100vh;
}

.tab-scroll-container {
    overflow-x: auto;
    scrollbar-width: none;
}
.tab-scroll-container::-webkit-scrollbar {
    display: none;
}

.kpi-accent {
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    opacity: 0.6;
}

.glass-tabs :deep(.v-tab) {
    height: 64px !important;
    color: rgba(255, 255, 255, 0.7) !important;
    border-bottom: 2px solid transparent !important;
    text-transform: none !important;
    letter-spacing: 0.5px !important;
}

.glass-tabs :deep(.v-tab--selected) {
    color: #ffffff !important;
    background: linear-gradient(
        to top,
        rgba(99, 102, 241, 0.2),
        transparent
    ) !important;
}

.glass-table {
    background: transparent !important;
}

.glass-table :deep(th) {
    background: rgba(255, 255, 255, 0.02) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
    height: 56px !important;
    color: rgba(255, 255, 255, 0.5) !important;
}

.glass-table :deep(td) {
    border-bottom: 1px solid rgba(255, 255, 255, 0.03) !important;
    height: 72px !important;
}

.glass-table :deep(tr:hover) {
    background: rgba(255, 255, 255, 0.02) !important;
}

.tab-content-anim {
    animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.roi-item {
    transition: transform 0.3s ease;
}
.roi-item:hover {
    transform: translateX(4px);
}

.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.3s ease;
}
.fade-slide-enter-from {
    opacity: 0;
    transform: translateX(10px);
}
.fade-slide-leave-to {
    opacity: 0;
    transform: translateX(-10px);
}
</style>
