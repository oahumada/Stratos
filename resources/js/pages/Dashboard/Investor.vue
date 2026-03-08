<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

// Types for data consistency
interface DashboardData {
    summary: {
        headcount: number;
        active_scenarios: number;
        org_readiness: number;
        talent_roi_usd: number;
        critical_gap_rate: number;
        ai_augmentation_index: number;
        culture_health_score: number;
        avg_turnover_risk: number;
        stratos_iq: {
            score: number;
            components: Record<string, number>;
            weights: Record<string, number>;
        };
    };
    ceo_view: {
        stratos_iq: {
            score: number;
            components: Record<string, number>;
            weights: Record<string, number>;
        };
        top_kpis: Array<{
            key: string;
            label: string;
            value: number;
            unit: string;
            status: 'green' | 'yellow' | 'red';
            driver: string;
            action: string;
        }>;
    };
    charts: {
        skill_levels: Array<{ current_level: number; count: number }>;
        department_readiness: Array<{
            name: string;
            readiness: string | number;
        }>;
    };
    forecast: {
        next_quarter_readiness: number;
        projected_savings_usd: number;
    };
    timestamp: string;
}

const loading = ref(true);
const dashboardData = ref<DashboardData | null>(null);

const fetchDashboardData = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/investor/dashboard');
        dashboardData.value = response.data.data;
    } catch (error) {
        console.error('Error al obtener dashboard inversor:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchDashboardData();
});

// Chart Configurations
const departmentChartOptions = computed(() => ({
    chart: {
        type: 'bar' as const,
        toolbar: { show: false },
        background: 'transparent',
    },
    plotOptions: {
        bar: {
            borderRadius: 6,
            horizontal: true,
            distributed: true,
            barHeight: '60%',
        },
    },
    colors: ['#6366f1', '#8b5cf6', '#d946ef', '#ec4899', '#f43f5e'],
    dataLabels: { enabled: true, formatter: (val: number) => `${val}%` },
    xaxis: {
        categories:
            dashboardData.value?.charts.department_readiness.map(
                (d) => d.name,
            ) || [],
        labels: { style: { colors: '#94a3b8' } },
    },
    yaxis: { labels: { style: { colors: '#94a3b8' } } },
    grid: { show: false },
    theme: { mode: 'dark' as const },
}));

const departmentSeries = computed(() => [
    {
        name: 'Preparación',
        data:
            dashboardData.value?.charts.department_readiness.map((d) =>
                Math.round(Number(d.readiness)),
            ) || [],
    },
]);

const skillDistributionOptions = computed(() => ({
    chart: { type: 'donut' as const, background: 'transparent' },
    labels: [
        'Nivel 1 (Básico)',
        'Nivel 2',
        'Nivel 3 (Competente)',
        'Nivel 4',
        'Nivel 5 (Experto)',
    ],
    colors: ['#f43f5e', '#fbbf24', '#10b981', '#3b82f6', '#8b5cf6'],
    stroke: { show: false },
    legend: { position: 'bottom' as const, labels: { colors: '#94a3b8' } },
    dataLabels: { enabled: false },
    plotOptions: {
        pie: {
            donut: {
                size: '75%',
                labels: {
                    show: true,
                    name: { show: true, color: '#94a3b8' },
                    value: {
                        show: true,
                        color: '#ffffff',
                        fontSize: '24px',
                        fontWeight: 700,
                    },
                    total: {
                        show: true,
                        label: 'Habilidades',
                        color: '#94a3b8',
                    },
                },
            },
        },
    },
    theme: { mode: 'dark' as const },
}));

const skillSeries = computed(() => {
    const counts = [0, 0, 0, 0, 0];
    dashboardData.value?.charts.skill_levels.forEach((sl) => {
        if (sl.current_level >= 1 && sl.current_level <= 5) {
            counts[sl.current_level - 1] = sl.count;
        }
    });
    return counts;
});

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(val);
};

const kpiIconMap: Record<string, string> = {
    stratos_iq: 'mdi-brain',
    org_readiness: 'mdi-shield-star',
    critical_gap_rate: 'mdi-alert-decagram',
    talent_roi_usd: 'mdi-cash-check',
    avg_turnover_risk: 'mdi-account-arrow-right',
};

const getKpiIcon = (key: string) => kpiIconMap[key] || 'mdi-chart-box';

const getKpiColor = (status: 'green' | 'yellow' | 'red') => {
    if (status === 'green') {
        return 'emerald-accent-3';
    }

    if (status === 'yellow') {
        return 'amber-accent-3';
    }

    return 'rose-accent-2';
};

const getKpiHexColor = (status: 'green' | 'yellow' | 'red') => {
    if (status === 'green') {
        return '#34d399';
    }

    if (status === 'yellow') {
        return '#fbbf24';
    }

    return '#fb7185';
};

const formatKpiValue = (value: number, unit: string) => {
    if (unit === 'usd') {
        return formatCurrency(value);
    }

    if (unit === '%' || unit === '/100') {
        return `${Math.round(value)}${unit === '%' ? '%' : ''}`;
    }

    return `${value}`;
};

const kpiThresholdMap: Record<string, string> = {
    stratos_iq: 'Verde ≥ 70 · Amarillo 60-69 · Rojo < 60',
    org_readiness: 'Verde ≥ 75% · Amarillo 65-74% · Rojo < 65%',
    critical_gap_rate: 'Verde ≤ 15% · Amarillo 16-25% · Rojo > 25%',
    talent_roi_usd: 'Verde ≥ $160k · Amarillo $150k-$159k · Rojo < $150k',
    avg_turnover_risk: 'Verde ≤ 35 · Amarillo 36-45 · Rojo > 45',
};

const getKpiThreshold = (key: string) => {
    return kpiThresholdMap[key] || 'Umbral no definido';
};

defineOptions({ layout: AppLayout });
</script>

<template>
    <Head>
        <title>Radar Inversor | ROI de Talento Stratos</title>
    </Head>

    <div class="investor-container">
        <!-- Encabezado del Dashboard -->
        <header class="dashboard-header mb-8">
            <div
                class="d-flex align-center justify-space-between flex-wrap gap-4"
            >
                <div>
                    <h1 class="text-h4 font-weight-black mb-1 text-white">
                        Radar Inversor <span class="badge">En vivo</span>
                    </h1>
                    <p class="text-subtitle-1 text-slate-200">
                        Análisis ejecutivo de capital organizacional y
                        proyecciones de ROI
                    </p>
                </div>
                <div class="header-actions">
                    <v-btn
                        prepend-icon="mdi-download"
                        variant="tonal"
                        color="white"
                        rounded="lg"
                        class="header-btn"
                        >Exportar reporte</v-btn
                    >
                    <v-btn
                        icon="mdi-refresh"
                        variant="text"
                        color="slate-400"
                        @click="fetchDashboardData"
                        :loading="loading"
                    ></v-btn>
                </div>
            </div>
        </header>

        <v-row v-if="!loading && dashboardData" class="mb-4">
            <v-col cols="12">
                <StCardGlass class="pa-4" :no-hover="true">
                    <div class="d-flex align-center flex-wrap gap-3">
                        <span class="legend-label">Semáforo:</span>
                        <v-chip
                            size="small"
                            color="emerald-accent-3"
                            variant="flat"
                            class="legend-chip"
                            >Verde: en objetivo</v-chip
                        >
                        <v-chip
                            size="small"
                            color="amber-accent-3"
                            variant="flat"
                            class="legend-chip"
                            >Amarillo: atención</v-chip
                        >
                        <v-chip
                            size="small"
                            color="rose-accent-2"
                            variant="flat"
                            class="legend-chip"
                            >Rojo: intervención</v-chip
                        >
                    </div>
                </StCardGlass>
            </v-col>
        </v-row>

        <!-- KPI Grid (CEO 10s View) -->
        <v-row v-if="!loading && dashboardData" class="mb-8">
            <v-col
                v-for="kpi in dashboardData.ceo_view.top_kpis"
                :key="kpi.key"
                cols="12"
                sm="6"
                md="4"
                lg="2"
            >
                <StCardGlass class="pa-6" :no-hover="false">
                    <div class="card-icon">
                        <v-icon :color="getKpiColor(kpi.status)">
                            {{ getKpiIcon(kpi.key) }}
                        </v-icon>
                    </div>
                    <div class="card-body">
                        <span class="label">{{ kpi.label }}</span>
                        <h2
                            class="value"
                            :style="{ color: getKpiHexColor(kpi.status) }"
                        >
                            {{ formatKpiValue(kpi.value, kpi.unit) }}
                        </h2>
                        <div class="kpi-threshold mb-1">
                            {{ getKpiThreshold(kpi.key) }}
                        </div>
                        <span class="subtext">{{ kpi.driver }}</span>
                        <div class="kpi-action mt-2">{{ kpi.action }}</div>
                    </div>
                </StCardGlass>
            </v-col>
        </v-row>

        <!-- Loading State -->
        <v-row v-if="loading">
            <v-col v-for="i in 5" :key="i" cols="12" sm="6" md="4" lg="2">
                <v-skeleton-loader
                    type="card"
                    class="glass-card-skeleton"
                ></v-skeleton-loader>
            </v-col>
        </v-row>

        <!-- Main Charts Section -->
        <v-row v-if="!loading && dashboardData">
            <!-- Gráfico Izquierdo: Preparación por Área -->
            <v-col cols="12" md="8">
                <StCardGlass class="pa-6" :no-hover="true">
                    <div class="d-flex justify-space-between align-center mb-6">
                        <h3 class="text-h6 font-weight-bold text-white">
                            Preparación Organizacional por Unidad
                        </h3>
                        <div class="st-badge-live bg-indigo-500 text-white">
                            Datos en vivo
                        </div>
                    </div>
                    <VueApexCharts
                        height="350"
                        :options="departmentChartOptions"
                        :series="departmentSeries"
                    />
                </StCardGlass>
            </v-col>

            <!-- Gráfico Derecho: Distribución de Habilidades -->
            <v-col cols="12" md="4">
                <StCardGlass class="pa-6" :no-hover="true">
                    <h3 class="text-h6 font-weight-bold mb-6 text-white">
                        Salud del Inventario de Habilidades
                    </h3>
                    <VueApexCharts
                        height="350"
                        :options="skillDistributionOptions"
                        :series="skillSeries"
                    />
                </StCardGlass>
            </v-col>
        </v-row>

        <!-- Footer / Sección de Proyección -->
        <v-row v-if="!loading && dashboardData" class="mt-8">
            <v-col cols="12">
                <StCardGlass
                    class="pa-8 border-indigo-500/20 bg-indigo-900/10"
                    :no-hover="true"
                >
                    <v-row align="center">
                        <v-col cols="12" md="7">
                            <h2
                                class="text-h5 font-weight-bold mb-2 text-white"
                            >
                                Proyección Estratégica T2
                            </h2>
                            <p class="text-body-1 text-slate-200">
                                Basado en las trayectorias actuales de
                                upskilling y planes de integración de IA,
                                Stratos proyecta un
                                <strong class="text-emerald-accent-3"
                                    >{{
                                        dashboardData.forecast
                                            .next_quarter_readiness
                                    }}% de índice de preparación</strong
                                >
                                para el próximo trimestre, generando un
                                adicional de
                                <strong class="text-emerald-accent-3">{{
                                    formatCurrency(
                                        dashboardData.forecast
                                            .projected_savings_usd,
                                    )
                                }}</strong>
                                en eficiencia operativa.
                            </p>
                        </v-col>
                        <v-col cols="12" md="5" class="text-right">
                            <v-btn
                                size="large"
                                color="indigo-accent-2"
                                rounded="xl"
                                elevation="10"
                                >Revisar Plan Estratégico</v-btn
                            >
                        </v-col>
                    </v-row>
                </StCardGlass>
            </v-col>
        </v-row>
    </div>
</template>

<style scoped>
.investor-container {
    padding: 1rem;
}

.card-icon {
    width: 3rem;
    height: 3rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
}

.card-body .label {
    display: block;
    color: #94a3b8;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    margin-bottom: 0.5rem;
}

.card-body .value {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.25rem;
}

.card-body .subtext {
    font-size: 0.75rem;
    color: #64748b;
}

.kpi-threshold {
    font-size: 0.7rem;
    color: #94a3b8;
}

.kpi-action {
    font-size: 0.75rem;
    color: #cbd5e1;
}

.legend-label {
    font-size: 0.8rem;
    color: #94a3b8;
    font-weight: 600;
}

.header-btn {
    color: #e2e8f0 !important;
}

:deep(.legend-chip .v-chip__content) {
    color: #ffffff !important;
    font-weight: 600;
}

.glass-card-skeleton {
    background: rgba(30, 41, 59, 0.3) !important;
    border-radius: 1.5rem !important;
}

@media (max-width: 960px) {
    .investor-container {
        padding: 1rem;
    }
}
</style>
