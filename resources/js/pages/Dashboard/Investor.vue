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
        console.error('Error fetching investor dashboard:', error);
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
        name: 'Readiness',
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
                    total: { show: true, label: 'Skills', color: '#94a3b8' },
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

defineOptions({ layout: AppLayout });
</script>

<template>
    <Head>
        <title>Investor Radar | Stratos Talent ROI</title>
    </Head>

    <div class="investor-container">
        <!-- Dashboard Header -->
        <header class="dashboard-header mb-8">
            <div
                class="d-flex align-center justify-space-between flex-wrap gap-4"
            >
                <div>
                    <h1 class="text-h4 font-weight-black mb-1 text-white">
                        Investor Radar <span class="badge">Live</span>
                    </h1>
                    <p class="text-subtitle-1 text-slate-200">
                        Executive Analysis of Organizational Capital & ROI
                        Projections
                    </p>
                </div>
                <div class="header-actions">
                    <v-btn
                        prepend-icon="mdi-download"
                        variant="tonal"
                        color="white"
                        rounded="lg"
                        >Export Report</v-btn
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

        <!-- KPI Grid -->
        <v-row v-if="!loading && dashboardData" class="mb-8">
            <!-- ROI Card -->
            <v-col cols="12" sm="6" md="3">
                <StCardGlass class="pa-6" :no-hover="false">
                    <div class="card-icon">
                        <v-icon color="emerald-accent-3">mdi-cash-check</v-icon>
                    </div>
                    <div class="card-body">
                        <span class="label">Talent ROI (YTD)</span>
                        <h2 class="value text-emerald-accent-3">
                            {{
                                formatCurrency(
                                    dashboardData.summary.talent_roi_usd,
                                )
                            }}
                        </h2>
                        <span class="subtext"
                            >Estimated Savings in Hiring/Upskilling</span
                        >
                    </div>
                </StCardGlass>
            </v-col>

            <!-- Readiness Card -->
            <v-col cols="12" sm="6" md="3">
                <StCardGlass class="pa-6">
                    <div class="card-icon">
                        <v-icon color="indigo-accent-2">mdi-shield-star</v-icon>
                    </div>
                    <div class="card-body">
                        <span class="label">Org Readiness IQ</span>
                        <h2 class="value">
                            {{ dashboardData.summary.org_readiness }}%
                        </h2>
                        <div class="progress-bar-container mt-2">
                            <v-progress-linear
                                :model-value="
                                    dashboardData.summary.org_readiness
                                "
                                color="indigo-accent-2"
                                height="6"
                                rounded
                            ></v-progress-linear>
                        </div>
                    </div>
                </StCardGlass>
            </v-col>

            <!-- Critical Risk Card -->
            <v-col cols="12" sm="6" md="3">
                <StCardGlass class="pa-6">
                    <div class="card-icon">
                        <v-icon color="rose-accent-2"
                            >mdi-alert-decagram</v-icon
                        >
                    </div>
                    <div class="card-body">
                        <span class="label">Talent Risk Index</span>
                        <h2 class="value">
                            {{ dashboardData.summary.critical_gap_rate }}%
                        </h2>
                        <span class="subtext"
                            >Critical Skill Gaps in Core Roles</span
                        >
                    </div>
                </StCardGlass>
            </v-col>

            <!-- AI Augmentation Card -->
            <v-col cols="12" sm="6" md="3">
                <StCardGlass class="pa-6">
                    <div class="card-icon">
                        <v-icon color="cyan-accent-2"
                            >mdi-robot-industrial</v-icon
                        >
                    </div>
                    <div class="card-body">
                        <span class="label">AI Augmentation</span>
                        <h2 class="value">
                            {{ dashboardData.summary.ai_augmentation_index }}%
                        </h2>
                        <span class="subtext"
                            >Efficiency gain via Synthetic Tasks</span
                        >
                    </div>
                </StCardGlass>
            </v-col>
        </v-row>

        <!-- Loading State -->
        <v-row v-if="loading">
            <v-col v-for="i in 4" :key="i" cols="12" sm="6" md="3">
                <v-skeleton-loader
                    type="card"
                    class="glass-card-skeleton"
                ></v-skeleton-loader>
            </v-col>
        </v-row>

        <!-- Main Charts Section -->
        <v-row v-if="!loading && dashboardData">
            <!-- Left Chart: Dept Readiness -->
            <v-col cols="12" md="8">
                <StCardGlass class="pa-6" :no-hover="true">
                    <div class="d-flex justify-space-between align-center mb-6">
                        <h3 class="text-h6 font-weight-bold text-white">
                            Organizational Readiness by Unit
                        </h3>
                        <div class="st-badge-live bg-indigo-500 text-white">
                            Live Data
                        </div>
                    </div>
                    <VueApexCharts
                        height="350"
                        :options="departmentChartOptions"
                        :series="departmentSeries"
                    />
                </StCardGlass>
            </v-col>

            <!-- Right Chart: Skill Distribution -->
            <v-col cols="12" md="4">
                <StCardGlass class="pa-6" :no-hover="true">
                    <h3 class="text-h6 font-weight-bold mb-6 text-white">
                        Skill Inventory Health
                    </h3>
                    <VueApexCharts
                        height="350"
                        :options="skillDistributionOptions"
                        :series="skillSeries"
                    />
                </StCardGlass>
            </v-col>
        </v-row>

        <!-- Footer / Forecast Section -->
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
                                Q2 Strategic Forecast
                            </h2>
                            <p class="text-body-1 text-slate-200">
                                Based on current upskilling trajectories and AI
                                integration plans, Stratos projects a
                                <strong class="text-emerald-accent-3"
                                    >{{
                                        dashboardData.forecast
                                            .next_quarter_readiness
                                    }}% readiness index</strong
                                >
                                for the next quarter, yielding an additional
                                <strong class="text-emerald-accent-3">{{
                                    formatCurrency(
                                        dashboardData.forecast
                                            .projected_savings_usd,
                                    )
                                }}</strong>
                                in operational efficiency.
                            </p>
                        </v-col>
                        <v-col cols="12" md="5" class="text-right">
                            <v-btn
                                size="large"
                                color="indigo-accent-2"
                                rounded="xl"
                                elevation="10"
                                >Review Strategic Plan</v-btn
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
