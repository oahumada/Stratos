<template>
    <Head title="Stratos Identity Analytics" />

    <div class="min-h-screen bg-[#0a0a0c] text-white">
        <!-- Sidebar and Nav would be here, assuming AppLayout handles it -->
        <div class="pa-6 max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-cyan-500/30 bg-cyan-500/10 text-cyan-400">
                            <PhChartPieSlice :size="32" weight="duotone" />
                        </div>
                        <h1 class="text-4xl font-black tracking-tighter text-white">
                            {{ t('culture.dashboard.title') || 'Stratos Identity Analytics' }}
                        </h1>
                    </div>
                    <p class="mt-2 text-lg font-medium text-white/40">
                        {{ t('culture.dashboard.subtitle') || 'Diagnóstico profundo de la salud y alineación cultural.' }}
                    </p>
                </div>

                <div class="flex gap-4">
                    <v-btn
                        variant="flat"
                        color="cyan-accent-4"
                        prepend-icon="mdi-radar"
                        :loading="scanning"
                        @click="runScan"
                        class="font-bold rounded-xl shadow-xl shadow-cyan-900/20"
                        style="color: white !important;"
                    >
                        {{ t('culture.dashboard.run_scan') || 'Ejecutar Escaneo' }}
                    </v-btn>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <v-row>
                <!-- Key Metrics Row -->
                <v-col cols="12" md="4">
                    <v-card class="dashboard-card pa-6 h-full">
                        <div class="d-flex align-center justify-between mb-6">
                            <div class="text-overline text-white/50">CULTURAL HEALTH</div>
                            <v-icon icon="mdi-shield-check" color="green-accent-3"></v-icon>
                        </div>
                        <div class="text-center py-4">
                            <v-progress-circular
                                :model-value="healthScore"
                                :size="180"
                                :width="15"
                                :color="healthColor"
                            >
                                <div class="text-h2 font-black">{{ healthScore }}</div>
                            </v-progress-circular>
                            <div class="mt-4 text-h6 font-bold" :class="`text-${healthColor}`">
                                {{ healthStatusLabel }}
                            </div>
                        </div>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card class="dashboard-card pa-6 h-full">
                        <div class="d-flex align-center justify-between mb-6">
                            <div class="text-overline text-white/50">IDENTITY FRICTION</div>
                            <v-icon icon="mdi-vector-difference" color="red-accent-2"></v-icon>
                        </div>
                        <div class="text-center py-4">
                            <v-progress-circular
                                :model-value="frictionScore"
                                :size="180"
                                :width="15"
                                :color="frictionColor"
                                rotate="-90"
                            >
                                <div class="text-h2 font-black">{{ frictionScore }}%</div>
                            </v-progress-circular>
                            <div class="mt-4 text-h6 font-bold" :class="`text-${frictionColor}`">
                                {{ frictionLabel }}
                            </div>
                        </div>
                        <p class="text-caption text-white/40 text-center mt-2 px-4">
                            Brecha entre la cultura declarada en el Blueprint y la cultura vivida detectada.
                        </p>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card class="dashboard-card pa-6 h-full">
                        <div class="d-flex align-center justify-between mb-4">
                            <div class="text-overline text-white/50">AI SENTINEL DIAGNOSIS</div>
                            <v-chip size="x-small" color="cyan" variant="flat">LATEST INTELLIGENCE</v-chip>
                        </div>
                        <div v-if="aiAnalysis" class="diagnosis-content">
                            <p class="text-body-1 italic text-cyan-100/90 mb-4 bg-cyan-500/5 pa-4 rounded-xl border border-cyan-500/10">
                                "{{ aiAnalysis.diagnosis }}"
                            </p>
                            <div class="mb-4">
                                <div class="text-caption font-bold text-amber-300 uppercase mb-2">Acciones Críticas:</div>
                                <div v-for="(action, i) in aiAnalysis.ceo_actions" :key="i" class="d-flex gap-2 mb-2 text-body-2 text-white/70">
                                    <v-icon icon="mdi-flash" size="14" color="amber-accent-2" class="mt-1"></v-icon>
                                    {{ action }}
                                </div>
                            </div>
                            <div class="pa-3 rounded-xl bg-red-500/10 border border-red-500/20">
                                <span class="text-caption font-bold text-red-300 uppercase">Nodo Crítico:</span>
                                <div class="text-body-2 font-bold">{{ aiAnalysis.critical_node }}</div>
                            </div>
                        </div>
                        <div v-else class="text-center py-10 opacity-20">
                            <v-icon icon="mdi-robot-vacuum" size="64"></v-icon>
                            <p>Esperando escaneo...</p>
                        </div>
                    </v-card>
                </v-col>

                <!-- Second Row: Radar and Silos -->
                <v-col cols="12" md="7">
                    <v-card class="dashboard-card pa-6 h-full">
                        <div class="d-flex align-center justify-between mb-6">
                            <div>
                                <div class="text-overline text-white/50">COMPETING VALUES FRAMEWORK</div>
                                <div class="text-h6 font-bold text-white">Radar de ADN Organizacional</div>
                            </div>
                            <v-icon icon="mdi-radar" color="indigo-accent-2"></v-icon>
                        </div>
                        
                        <div class="radar-container">
                            <apexchart
                                type="radar"
                                height="400"
                                :options="radarOptions"
                                :series="radarSeries"
                            ></apexchart>
                        </div>

                        <v-row class="mt-4 px-4 text-center">
                            <v-col cols="3">
                                <div class="text-caption text-indigo-300 font-bold">CLAN</div>
                                <div class="text-body-2 text-white/40">Colaborar</div>
                            </v-col>
                            <v-col cols="3">
                                <div class="text-caption text-amber-300 font-bold">ADHOCRACIA</div>
                                <div class="text-body-2 text-white/40">Crear</div>
                            </v-col>
                            <v-col cols="3">
                                <div class="text-caption text-red-300 font-bold">MERCADO</div>
                                <div class="text-body-2 text-white/40">Competir</div>
                            </v-col>
                            <v-col cols="3">
                                <div class="text-caption text-green-300 font-bold">JERARQUÍA</div>
                                <div class="text-body-2 text-white/40">Controlar</div>
                            </v-col>
                        </v-row>
                    </v-card>
                </v-col>

                <v-col cols="12" md="5">
                    <v-card class="dashboard-card pa-6 h-full">
                        <div class="d-flex align-center justify-between mb-6">
                            <div>
                                <div class="text-overline text-white/50">CULTURAL SILOS</div>
                                <div class="text-h6 font-bold text-white">Heatmap de Fragmentación</div>
                            </div>
                            <v-icon icon="mdi-google-circles-extended" color="pink-accent-2"></v-icon>
                        </div>

                        <div v-if="departmentSilos.length > 0" class="silo-list">
                            <div 
                                v-for="silo in departmentSilos" 
                                :key="silo.name"
                                class="pa-4 mb-3 rounded-2xl bg-white/5 border border-white/5 d-flex align-center justify-between"
                            >
                                <div>
                                    <div class="text-body-1 font-bold">{{ silo.name }}</div>
                                    <div class="text-caption text-white/40">Cultura Dominante: {{ silo.dominant }}</div>
                                </div>
                                <div class="text-right">
                                    <v-chip :color="silo.friction > 50 ? 'red' : 'amber'" size="small" variant="tonal">
                                        {{ silo.friction }}% Fricción
                                    </v-chip>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-10 opacity-20">
                            <v-icon icon="mdi-map-search" size="64"></v-icon>
                            <p>Analizando silos organizacionales...</p>
                        </div>
                    </v-card>
                </v-col>
            </v-row>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { PhChartPieSlice, PhRadar } from '@phosphor-icons/vue';
import { computed, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import StButtonGlass from '@/components/StButtonGlass.vue';

const { t } = useI18n();

const loading = ref(false);
const scanning = ref(false);

// State
const healthScore = ref(72);
const frictionScore = ref(24);
const aiAnalysis = ref<any>(null);
const departmentSilos = ref<any[]>([]);

const radarSeries = ref([
    {
        name: 'Declarada (Blueprint)',
        data: [80, 70, 60, 50]
    },
    {
        name: 'Vivida (Realidad)',
        data: [65, 45, 75, 80]
    }
]);

const radarOptions = computed(() => ({
    chart: {
        toolbar: { show: false },
        dropShadow: { enabled: true, blur: 8, left: 1, top: 1, opacity: 0.2 }
    },
    colors: ['#00E5FF', '#FF00E5'],
    stroke: { width: 3 },
    fill: { opacity: 0.2 },
    markers: { size: 4 },
    xaxis: {
        categories: ['Clan', 'Adhocracia', 'Mercado', 'Jerarquía'],
        labels: {
            style: {
                colors: ['#ffffff', '#ffffff', '#ffffff', '#ffffff'],
                fontSize: '12px',
                fontWeight: 600
            }
        }
    },
    yaxis: { show: false, min: 0, max: 100 },
    legend: {
        position: 'bottom',
        labels: { colors: '#ffffff' }
    },
    grid: { show: false }
}));

const healthColor = computed(() => {
    if (healthScore.value >= 75) return 'cyan-accent-2';
    if (healthScore.value >= 50) return 'amber-accent-2';
    return 'red-accent-2';
});

const healthStatusLabel = computed(() => {
    if (healthScore.value >= 75) return 'Óptima';
    if (healthScore.value >= 50) return 'Estable';
    return 'Riesgo Crítico';
});

const frictionColor = computed(() => {
    if (frictionScore.value < 20) return 'green-accent-3';
    if (frictionScore.value < 40) return 'amber-accent-2';
    return 'red-accent-2';
});

const frictionLabel = computed(() => {
    if (frictionScore.value < 20) return 'Baja';
    if (frictionScore.value < 40) return 'Moderada';
    return 'Elevada';
});

const runScan = async () => {
    scanning.value = true;
    try {
        const { data } = await axios.get('/api/pulse/health-scan');
        const d = data.data;
        
        healthScore.value = d.health_score || healthScore.value;
        frictionScore.value = d.friction_score || frictionScore.value;
        aiAnalysis.value = d.ai_analysis;
        departmentSilos.value = d.department_silos || [];
        
        if (d.cvf_radar) {
            radarSeries.value[1].data = [
                d.cvf_radar.clan,
                d.cvf_radar.adhocracy,
                d.cvf_radar.market,
                d.cvf_radar.hierarchy
            ];
        }
    } catch (e) {
        console.error('Scan failed', e);
    } finally {
        scanning.value = false;
    }
};

onMounted(() => {
    runScan();
});
</script>

<style scoped>
.dashboard-card {
    background: rgba(30, 41, 59, 0.4) !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
    border-radius: 24px !important;
    backdrop-filter: blur(12px);
}

.radar-container {
    background: radial-gradient(circle, rgba(99, 102, 241, 0.05) 0%, rgba(0, 0, 0, 0) 70%);
}

.diagnosis-content {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

:deep(.apexcharts-legend-text) {
    color: rgba(255, 255, 255, 0.7) !important;
}
</style>
