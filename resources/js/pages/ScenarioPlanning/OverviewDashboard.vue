<script setup lang="ts">
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import CoverageChart from './Charts/CoverageChart.vue';
import DepartmentGapsChart from './Charts/DepartmentGapsChart.vue';
import HeadcountChart from './Charts/HeadcountChart.vue';
import MatchScoreDistributionChart from './Charts/MatchScoreDistributionChart.vue';
import ReadinessTimelineChart from './Charts/ReadinessTimelineChart.vue';
import SkillGapsChart from './Charts/SkillGapsChart.vue';
import SuccessionRiskChart from './Charts/SuccessionRiskChart.vue';
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
});

const formatNumber = (num: number): string => {
    return new Intl.NumberFormat('en-US').format(num);
};

// Helper functions for chart data aggregation
const countGapsByPriority = (priority: string): number => {
    // This will be populated by data from the store in the next phase
    // For now return mock data that will be replaced by store getters
    const gapPriorities: { [key: string]: number } = {
        critical: 3,
        high: 4,
        medium: 5,
        low: 2,
    };
    return gapPriorities[priority] || 0;
};

const countByReadiness = (level: string): number => {
    // Mock data - will be populated from store
    const readinessCounts: { [key: string]: number } = {
        immediately: 3,
        within_six: 4,
        within_twelve: 2,
        beyond_twelve: 1,
    };
    return readinessCounts[level] || 0;
};

const getAllMatchScores = (): number[] => {
    // Mock data - will be populated from store
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
        // Analytics legacy deshabilitado temporalmente
    } catch (e) {
        void e;
        showError('Failed to load scenario');
    }
};

// Analytics legacy deshabilitado temporalmente hasta migrar al nuevo módulo
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
            // Mapear algunos KPIs a tarjetas existentes cuando sea posible
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

// mark some bindings referenced to avoid unused-var during staged refactor
void page;
void loadAnalytics;

const downloadReport = () => {
    // TODO: Implement report download
    showSuccess('Descarga de reporte aún no implementada');
};

onMounted(() => {
    loadScenario();
    loadCriticalTalents();
});
</script>

<template>
    <div class="overview-dashboard">
        <v-container fluid>
            <v-row class="mb-4">
                <v-col cols="12" md="8">
                    <h2>Escenario: {{ scenarioName }}</h2>
                    <p class="text-subtitle-2">{{ scenarioDescription }}</p>
                </v-col>
                <v-col cols="12" md="4" class="text-right">
                    <v-btn
                        color="primary"
                        @click="runAnalysis"
                        :loading="analyzing"
                        prepend-icon="mdi-refresh"
                        class="mr-2"
                    >
                        Ejecutar Análisis
                    </v-btn>
                    <v-btn
                        color="success"
                        @click="generateStrategies"
                        prepend-icon="mdi-lightbulb-on"
                        class="mr-2"
                    >
                        Generar Estrategias
                    </v-btn>
                    <v-btn
                        color="secondary"
                        @click="downloadReport"
                        prepend-icon="mdi-download"
                    >
                        Exportar
                    </v-btn>
                </v-col>
            </v-row>

            <!-- Navigation Tabs -->
            <v-row class="mb-4">
                <v-col cols="12">
                    <v-tabs v-model="activeTab" bg-color="primary">
                        <v-tab value="overview">Resumen</v-tab>
                        <v-tab value="simulator">
                            <v-icon start>mdi-chart-timeline</v-icon>
                            Simulador de Crecimiento
                        </v-tab>
                        <v-tab value="critical">
                            <v-icon start>mdi-alert-circle</v-icon>
                            Talentos Críticos ({{ criticalTalentsCount }})
                        </v-tab>
                        <v-tab value="forecasts">Proyecciones de Roles</v-tab>
                        <v-tab value="matches">Coincidencias de Talento</v-tab>
                        <v-tab value="gaps">Brechas de Habilidades</v-tab>
                        <v-tab value="succession">Planes de Sucesión</v-tab>
                        <v-tab value="roi">
                            <v-icon start>mdi-calculator</v-icon>
                            Análisis ROI
                        </v-tab>
                        <v-tab value="strategies">
                            <v-icon start>mdi-target</v-icon>
                            Asignar Estrategias
                        </v-tab>
                    </v-tabs>
                </v-col>
            </v-row>

            <!-- Tab Content -->
            <div v-show="activeTab === 'overview'">
                <!-- KPI Cards -->
                <v-row class="mb-4">
                    <v-col cols="12" md="3">
                        <v-card>
                            <v-card-text>
                                <div class="text-overline">Dotación Total</div>
                                <div class="text-h4">
                                    {{
                                        analytics.total_headcount_current || '0'
                                    }}
                                </div>
                                <div class="text-caption">
                                    Actual →
                                    {{
                                        analytics.total_headcount_projected ||
                                        '0'
                                    }}
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="3">
                        <v-card>
                            <v-card-text>
                                <div class="text-overline">
                                    Crecimiento Neto
                                </div>
                                <div class="text-h4">
                                    {{ analytics.net_growth || '0' }}
                                </div>
                                <div class="text-caption">
                                    {{
                                        analytics.net_growth > 0
                                            ? 'Expansión'
                                            : 'Reducción'
                                    }}
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="3">
                        <v-card>
                            <v-card-text>
                                <div class="text-overline">
                                    Cobertura Interna
                                </div>
                                <div class="text-h4">
                                    {{
                                        analytics.internal_coverage_percentage ||
                                        '0'
                                    }}%
                                </div>
                                <div class="text-caption">
                                    Brecha Externa:
                                    {{
                                        analytics.external_gap_percentage ||
                                        '0'
                                    }}%
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="3">
                        <v-card>
                            <v-card-text>
                                <div class="text-overline">
                                    Riesgo de Sucesión
                                </div>
                                <div class="text-h4">
                                    {{
                                        analytics.succession_risk_percentage ||
                                        '0'
                                    }}%
                                </div>
                                <div class="text-caption">
                                    Roles críticos en riesgo
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>

                <!-- Primary Charts Row -->
                <v-row class="mb-4">
                    <v-col cols="12" md="6">
                        <v-card>
                            <v-card-title>Proyección de Dotación</v-card-title>
                            <v-card-text>
                                <HeadcountChart
                                    :currentHeadcount="
                                        analytics.total_headcount_current
                                    "
                                    :projectedHeadcount="
                                        analytics.total_headcount_projected
                                    "
                                />
                            </v-card-text>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="6">
                        <v-card>
                            <v-card-title>Cobertura Interna</v-card-title>
                            <v-card-text>
                                <CoverageChart
                                    :internalCoverage="
                                        analytics.internal_coverage_percentage
                                    "
                                    :externalGap="
                                        analytics.external_gap_percentage
                                    "
                                />
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>

                <!-- Secondary Charts Row -->
                <v-row class="mb-4">
                    <v-col cols="12" md="6">
                        <v-card>
                            <v-card-title
                                >Brechas de Habilidades por
                                Prioridad</v-card-title
                            >
                            <v-card-text>
                                <SkillGapsChart
                                    :criticalGaps="
                                        countGapsByPriority('critical')
                                    "
                                    :highGaps="countGapsByPriority('high')"
                                    :mediumGaps="countGapsByPriority('medium')"
                                    :lowGaps="countGapsByPriority('low')"
                                />
                            </v-card-text>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="6">
                        <v-card>
                            <v-card-title
                                >Evaluación de Riesgo de Sucesión</v-card-title
                            >
                            <v-card-text>
                                <SuccessionRiskChart
                                    :riskPercentage="
                                        analytics.succession_risk_percentage
                                    "
                                />
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>

                <!-- Tertiary Charts Row -->
                <v-row class="mb-4">
                    <v-col cols="12" md="4">
                        <v-card>
                            <v-card-title
                                >Cronograma de Preparación</v-card-title
                            >
                            <v-card-text>
                                <ReadinessTimelineChart
                                    :immediatelyReady="
                                        countByReadiness('immediately')
                                    "
                                    :readyWithinSix="
                                        countByReadiness('within_six')
                                    "
                                    :readyWithinTwelve="
                                        countByReadiness('within_twelve')
                                    "
                                    :beyondTwelve="
                                        countByReadiness('beyond_twelve')
                                    "
                                />
                            </v-card-text>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="4">
                        <v-card>
                            <v-card-title
                                >Distribución de Puntuación</v-card-title
                            >
                            <v-card-text>
                                <MatchScoreDistributionChart
                                    :scores="getAllMatchScores()"
                                />
                            </v-card-text>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="4">
                        <v-card>
                            <v-card-title
                                >Brechas por Departamento</v-card-title
                            >
                            <v-card-text>
                                <DepartmentGapsChart
                                    :departments="getDepartments()"
                                    :gapCounts="getGapCountsByDepartment()"
                                />
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>

                <!-- Risk Summary -->
                <v-row>
                    <v-col cols="12" md="6">
                        <v-card>
                            <v-card-title>Resumen de Riesgos</v-card-title>
                            <v-list>
                                <v-list-item>
                                    <v-list-item-title
                                        >Posiciones de Alto
                                        Riesgo</v-list-item-title
                                    >
                                    <v-list-item-subtitle>{{
                                        analytics.high_risk_positions
                                    }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title
                                        >Posiciones de Riesgo
                                        Medio</v-list-item-title
                                    >
                                    <v-list-item-subtitle>{{
                                        analytics.medium_risk_positions
                                    }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title
                                        >Habilidades Críticas en
                                        Riesgo</v-list-item-title
                                    >
                                    <v-list-item-subtitle>{{
                                        analytics.critical_skills_at_risk
                                    }}</v-list-item-subtitle>
                                </v-list-item>
                            </v-list>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="6">
                        <v-card>
                            <v-card-title>Estimaciones de Costo</v-card-title>
                            <v-list>
                                <v-list-item>
                                    <v-list-item-title
                                        >Costo de
                                        Reclutamiento</v-list-item-title
                                    >
                                    <v-list-item-subtitle
                                        >${{
                                            formatNumber(
                                                analytics.estimated_recruitment_cost,
                                            )
                                        }}</v-list-item-subtitle
                                    >
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title
                                        >Costo de
                                        Capacitación</v-list-item-title
                                    >
                                    <v-list-item-subtitle
                                        >${{
                                            formatNumber(
                                                analytics.estimated_training_cost,
                                            )
                                        }}</v-list-item-subtitle
                                    >
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title
                                        >Cronograma de Contratación
                                        Externa</v-list-item-title
                                    >
                                    <v-list-item-subtitle
                                        >{{
                                            analytics.estimated_external_hiring_months
                                        }}
                                        meses</v-list-item-subtitle
                                    >
                                </v-list-item>
                            </v-list>
                        </v-card>
                    </v-col>
                </v-row>

                <!-- Action Buttons -->
                <v-row class="mt-4">
                    <v-col cols="12">
                        <v-btn
                            color="primary"
                            @click="runAnalysis"
                            :loading="analyzing"
                            prepend-icon="mdi-refresh"
                        >
                            Ejecutar Análisis Completo
                        </v-btn>
                        <v-btn
                            color="secondary"
                            @click="downloadReport"
                            prepend-icon="mdi-download"
                            class="ml-2"
                        >
                            Descargar Reporte
                        </v-btn>
                    </v-col>
                </v-row>
            </div>

            <!-- Growth Simulator Tab -->
            <div v-show="activeTab === 'simulator'">
                <v-card>
                    <v-card-title class="text-h5 bg-primary">
                        <v-icon start>mdi-chart-timeline</v-icon>
                        Simulador de Escenarios de Crecimiento
                    </v-card-title>
                    <v-card-text>
                        <v-row class="mt-4">
                            <v-col cols="12" md="3">
                                <v-text-field
                                    v-model.number="
                                        simulationParams.growth_percentage
                                    "
                                    label="Crecimiento %"
                                    type="number"
                                    suffix="%"
                                    min="0"
                                    max="100"
                                    density="comfortable"
                                    variant="outlined"
                                />
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-select
                                    v-model="simulationParams.horizon_months"
                                    :items="[12, 18, 24, 36]"
                                    label="Horizonte (meses)"
                                    density="comfortable"
                                    variant="outlined"
                                />
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-text-field
                                    v-model.number="
                                        simulationParams.external_hiring_ratio
                                    "
                                    label="Contratación Externa %"
                                    type="number"
                                    suffix="%"
                                    min="0"
                                    max="100"
                                    density="comfortable"
                                    variant="outlined"
                                />
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-btn
                                    color="primary"
                                    size="large"
                                    @click="runSimulation"
                                    block
                                >
                                    <v-icon start>mdi-play</v-icon>
                                    Ejecutar Simulación
                                </v-btn>
                            </v-col>
                        </v-row>

                        <v-divider class="my-4"></v-divider>

                        <!-- Simulation Results -->
                        <v-row v-if="simulationResults" class="mt-4">
                            <v-col cols="12" md="4">
                                <v-card color="blue-lighten-5">
                                    <v-card-title
                                        >Proyección de Dotación</v-card-title
                                    >
                                    <v-card-text>
                                        <v-row>
                                            <v-col cols="6" class="text-center">
                                                <div class="text-h4">
                                                    {{
                                                        simulationResults.current_talent_pool
                                                    }}
                                                </div>
                                                <div class="text-caption">
                                                    Pool de Talento Actual
                                                </div>
                                            </v-col>
                                            <v-col cols="6" class="text-center">
                                                <div
                                                    class="text-h4 text-primary"
                                                >
                                                    {{
                                                        simulationResults.projected_talent_requirement
                                                    }}
                                                </div>
                                                <div class="text-caption">
                                                    Requerimientos Proyectados
                                                </div>
                                            </v-col>
                                        </v-row>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-card color="green-lighten-5">
                                    <v-card-title
                                        >Crecimiento Neto</v-card-title
                                    >
                                    <v-card-text>
                                        <div
                                            class="text-h3 text-success text-center"
                                        >
                                            +{{
                                                simulationResults.net_capacity_gap
                                            }}
                                        </div>
                                        <div class="text-caption text-center">
                                            Brecha de capacidad neta
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-card color="orange-lighten-5">
                                    <v-card-title>Costo Estimado</v-card-title>
                                    <v-card-text>
                                        <div class="text-h4 text-center">
                                            ${{
                                                formatNumber(
                                                    simulationResults
                                                        .estimated_cost
                                                        ?.total || 0,
                                                )
                                            }}
                                        </div>
                                        <div class="text-caption text-center">
                                            Reclutamiento: ${{
                                                formatNumber(
                                                    simulationResults
                                                        .estimated_cost
                                                        ?.recruitment || 0,
                                                )
                                            }}<br />
                                            Capacitación: ${{
                                                formatNumber(
                                                    simulationResults
                                                        .estimated_cost
                                                        ?.training || 0,
                                                )
                                            }}
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>

                        <!-- Department Breakdown -->
                        <v-row v-if="simulationResults">
                            <v-col cols="12">
                                <v-card>
                                    <v-card-title
                                        >Distribución por
                                        Departamento</v-card-title
                                    >
                                    <v-card-text>
                                        <v-table density="comfortable">
                                            <thead>
                                                <tr>
                                                    <th>Departamento</th>
                                                    <th>Actual</th>
                                                    <th>Proyectado</th>
                                                    <th>Brecha</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(
                                                        data, cat
                                                    ) in simulationResults.by_capability_area"
                                                    :key="cat"
                                                >
                                                    <td>{{ cat }}</td>
                                                    <td>{{ data.current }}</td>
                                                    <td>
                                                        <strong>{{
                                                            data.projected
                                                        }}</strong>
                                                    </td>
                                                    <td>
                                                        <v-chip
                                                            :color="
                                                                data.gap > 0
                                                                    ? 'success'
                                                                    : 'error'
                                                            "
                                                            size="small"
                                                        >
                                                            {{
                                                                data.gap > 0
                                                                    ? '+'
                                                                    : ''
                                                            }}{{ data.gap }}
                                                        </v-chip>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </v-table>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>

                        <!-- Skills Needed -->
                        <v-row v-if="simulationResults?.skills_needed">
                            <v-col cols="12">
                                <v-card>
                                    <v-card-title
                                        >Habilidades Más
                                        Demandadas</v-card-title
                                    >
                                    <v-card-text>
                                        <v-table density="comfortable">
                                            <thead>
                                                <tr>
                                                    <th>Habilidad</th>
                                                    <th>Cantidad Requerida</th>
                                                    <th>
                                                        Disponibilidad Interna
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="skill in simulationResults.strategic_skills_needed"
                                                    :key="skill.skill_id"
                                                >
                                                    <td>
                                                        {{ skill.skill_name }}
                                                    </td>
                                                    <td>
                                                        <strong>{{
                                                            skill.count_needed
                                                        }}</strong>
                                                    </td>
                                                    <td>
                                                        <v-progress-linear
                                                            :model-value="
                                                                skill.internal_availability_pct
                                                            "
                                                            :color="
                                                                skill.internal_availability_pct >
                                                                50
                                                                    ? 'success'
                                                                    : 'warning'
                                                            "
                                                            height="20"
                                                        >
                                                            {{
                                                                Math.round(
                                                                    skill.internal_availability_pct,
                                                                )
                                                            }}%
                                                        </v-progress-linear>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </v-table>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>

                        <!-- Critical Risks -->
                        <v-row v-if="simulationResults?.critical_risks?.length">
                            <v-col cols="12">
                                <v-card color="red-lighten-5">
                                    <v-card-title>
                                        <v-icon start color="error"
                                            >mdi-alert</v-icon
                                        >
                                        Riesgos Críticos Identificados
                                    </v-card-title>
                                    <v-card-text>
                                        <v-list>
                                            <v-list-item
                                                v-for="(
                                                    risk, idx
                                                ) in simulationResults.critical_talent_risks"
                                                :key="idx"
                                            >
                                                <v-list-item-title>{{
                                                    risk.role
                                                }}</v-list-item-title>
                                                <v-list-item-subtitle>
                                                    <v-chip
                                                        color="error"
                                                        size="small"
                                                        class="mr-2"
                                                        >{{
                                                            risk.critical_level
                                                        }}</v-chip
                                                    >
                                                    {{ risk.recommendation }}
                                                </v-list-item-subtitle>
                                            </v-list-item>
                                        </v-list>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </div>

            <!-- Critical Talents Tab -->
            <div v-show="activeTab === 'critical'">
                <v-card>
                    <v-card-title class="text-h5 bg-error">
                        <v-icon start>mdi-alert-circle</v-icon>
                        Talentos Críticos & Riesgo de Orquestación
                    </v-card-title>
                    <v-card-text>
                        <v-data-table
                            :headers="criticalTalentsHeaders"
                            :items="criticalTalents"
                            density="comfortable"
                            class="mt-4"
                        >
                            <template #[`item.capability`]="{ item }">
                                <strong>{{ item.capability }}</strong>
                            </template>
                            <template #[`item.risk_status`]="{ item }">
                                <v-chip
                                    :color="getRiskColor(item.risk_status)"
                                    text-color="white"
                                    size="small"
                                >
                                    {{ item.risk_status }}
                                </v-chip>
                            </template>
                            <template
                                #[`item.internal_succession.ready_now`]="{
                                    item,
                                }"
                            >
                                <v-chip
                                    :color="
                                        item.internal_succession.ready_now > 0
                                            ? 'success'
                                            : 'error'
                                    "
                                    size="small"
                                >
                                    {{ item.internal_succession.ready_now }}
                                </v-chip>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </div>

            <!-- Role Forecasts Tab -->
            <div v-show="activeTab === 'forecasts'">
                <RoleForecastsTable :scenarioId="scenarioId" />
            </div>

            <!-- Talent Matches Tab -->
            <div v-show="activeTab === 'matches'">
                <MatchingResults :scenarioId="scenarioId" />
            </div>

            <!-- Skill Gaps Tab -->
            <div v-show="activeTab === 'gaps'">
                <SkillGapsMatrix :scenarioId="scenarioId" />
            </div>

            <!-- Succession Plans Tab -->
            <div v-show="activeTab === 'succession'">
                <SuccessionPlanCard :scenarioId="scenarioId" />
            </div>

            <!-- ROI Calculator Tab -->
            <div v-show="activeTab === 'roi'">
                <ScenarioRoiCalculator :scenarioId="scenarioId" />
            </div>

            <!-- Strategy Assigner Tab -->
            <div v-show="activeTab === 'strategies'">
                <ScenarioStrategyAssigner :scenarioId="scenarioId" />
            </div>
        </v-container>
    </div>
</template>

<style scoped>
.overview-dashboard {
    padding: 20px;
}
</style>
