<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps<{
    sessionId: string | number;
}>();

interface Profile {
    id: number;
    trait_name: string;
    score: number;
    rationale: string;
}

interface Session {
    id: number;
    person: {
        id: number;
        first_name: string;
        last_name: string;
        photo_url: string;
        role_name: string;
    };
    metadata: {
        overall_potential?: number;
        summary_report?: string;
        blind_spots?: string[];
        ai_reasoning_flow?: string[];
    };
    psychometric_profiles: Profile[];
    completed_at: string;
}

const session = ref<Session | null>(null);
const loading = ref(true);
const activeTrait = ref<Profile | null>(null);

const loadResults = async () => {
    loading.value = true;
    try {
        const response = await axios.get(
            `/api/strategic-planning/assessments/sessions/${props.sessionId}`,
        );
        session.value = response.data;
        if (session.value?.psychometric_profiles.length) {
            activeTrait.value = session.value.psychometric_profiles[0];
        }
    } catch (e) {
        console.error('Error loading assessment results', e);
    } finally {
        loading.value = false;
    }
};

onMounted(loadResults);

const radarChartOptions = computed(() => ({
    chart: {
        id: 'results-radar',
        toolbar: { show: false },
        dropShadow: {
            enabled: true,
            blur: 8,
            left: 1,
            top: 1,
            opacity: 0.2,
        },
    },
    xaxis: {
        categories:
            session.value?.psychometric_profiles.map((p) => p.trait_name) || [],
        labels: {
            style: {
                colors: new Array(
                    session.value?.psychometric_profiles.length || 0,
                ).fill('#94a3b8'),
                fontSize: '12px',
                fontFamily: 'Inter, sans-serif',
            },
        },
    },
    yaxis: {
        show: false,
        min: 0,
        max: 1,
    },
    colors: ['#3b82f6'],
    fill: {
        opacity: 0.3,
        type: 'gradient',
        gradient: {
            shade: 'dark',
            type: 'vertical',
            shadeIntensity: 0.5,
            gradientToColors: ['#8b5cf6'],
            inverseColors: true,
            opacityFrom: 0.7,
            opacityTo: 0.3,
        },
    },
    stroke: {
        show: true,
        width: 3,
        colors: ['#3b82f6'],
    },
    markers: {
        size: 5,
        hover: { size: 8 },
    },
}));

const radarChartSeries = computed(() => [
    {
        name: 'Puntaje',
        data: session.value?.psychometric_profiles.map((p) => p.score) || [],
    },
]);

const getPotentialLabel = (score: number) => {
    if (score >= 0.8) return 'Alto Potencial (HiPo)';
    if (score >= 0.5) return 'Desempeño Sólido';
    return 'En Desarrollo';
};

const getPotentialColor = (score: number) => {
    if (score >= 0.8) return '#10b981';
    if (score >= 0.5) return '#f59e0b';
    return '#ef4444';
};
</script>

<template>
    <div class="results-page">
        <Head title="Análisis de Potencial IA - Stratos" />

        <v-container
            v-if="loading"
            class="fill-height d-flex align-center justify-center"
        >
            <div class="text-center">
                <v-progress-circular
                    indeterminate
                    color="primary"
                    size="64"
                    width="6"
                ></v-progress-circular>
                <p class="text-h6 font-weight-light mt-4 text-slate-400">
                    Consultando al Córtex Digital...
                </p>
            </div>
        </v-container>

        <v-container v-else-if="session" class="pa-6">
            <!-- Header Section -->
            <v-row class="mb-4">
                <v-col cols="12">
                    <v-btn
                        variant="text"
                        color="blue-lighten-4"
                        prepend-icon="mdi-arrow-left"
                        to="/talento360"
                        class="mb-4"
                    >
                        Volver al Dashboard
                    </v-btn>
                </v-col>
            </v-row>
            <v-row class="align-center mb-8">
                <v-col cols="12" md="8">
                    <div class="d-flex align-center">
                        <v-avatar
                            size="96"
                            class="elevation-12 profile-avatar mr-6"
                        >
                            <v-img
                                :src="session.person.photo_url"
                                cover
                            ></v-img>
                        </v-avatar>
                        <div>
                            <div
                                class="text-overline font-weight-bold primary--text mb-1"
                            >
                                INFORME DE INTELIGENCIA ESTRATÉGICA
                            </div>
                            <h1
                                class="text-h3 font-weight-bold white--text mb-1"
                            >
                                {{ session.person.first_name }}
                                {{ session.person.last_name }}
                            </h1>
                            <p class="text-h6 mb-0 text-slate-400">
                                {{ session.person.role_name }} •
                                <span class="text-primary">{{
                                    getPotentialLabel(
                                        session.metadata.overall_potential || 0,
                                    )
                                }}</span>
                            </p>
                        </div>
                    </div>
                </v-col>
                <v-col cols="12" md="4" class="text-md-right">
                    <div class="potential-badge pa-4 d-inline-block rounded-xl">
                        <div class="text-caption text-uppercase opacity-70">
                            Índice de Potencial IA
                        </div>
                        <div
                            class="text-h2 font-weight-black"
                            :style="{
                                color: getPotentialColor(
                                    session.metadata.overall_potential || 0,
                                ),
                            }"
                        >
                            {{
                                (
                                    (session.metadata.overall_potential || 0) *
                                    100
                                ).toFixed(0)
                            }}%
                        </div>
                    </div>
                </v-col>
            </v-row>

            <v-row>
                <!-- Main Intelligence Display -->
                <v-col cols="12" md="7">
                    <v-card class="glass-card pa-6 mb-6">
                        <div class="d-flex align-center mb-6">
                            <v-icon color="primary" class="mr-3"
                                >mdi-brain</v-icon
                            >
                            <h2 class="text-h5 font-weight-bold">
                                Razonamiento del Agente
                            </h2>
                        </div>

                        <p
                            class="text-body-1 mb-8 leading-relaxed text-slate-300"
                        >
                            {{ session.metadata.summary_report }}
                        </p>

                        <div class="traits-explorer mt-8">
                            <h3 class="text-overline mb-4 text-slate-400">
                                Explorador de Rasgos
                            </h3>
                            <v-row>
                                <v-col
                                    v-for="trait in session.psychometric_profiles"
                                    :key="trait.id"
                                    cols="12"
                                    sm="6"
                                >
                                    <div
                                        class="trait-item pa-4 cursor-pointer rounded-lg transition-all"
                                        :class="{
                                            'active-trait':
                                                activeTrait?.id === trait.id,
                                        }"
                                        @click="activeTrait = trait"
                                    >
                                        <div
                                            class="d-flex justify-space-between align-center mb-2"
                                        >
                                            <span
                                                class="font-weight-medium text-white"
                                                >{{ trait.trait_name }}</span
                                            >
                                            <span
                                                class="font-weight-bold text-primary"
                                                >{{
                                                    (trait.score * 100).toFixed(
                                                        0,
                                                    )
                                                }}%</span
                                            >
                                        </div>
                                        <v-progress-linear
                                            :model-value="trait.score * 100"
                                            color="primary"
                                            height="6"
                                            rounded
                                        ></v-progress-linear>
                                    </div>
                                </v-col>
                            </v-row>
                        </div>
                    </v-card>

                    <!-- The "AI Thinking" Detail (Explicability) -->
                    <v-card
                        v-if="activeTrait"
                        class="glass-card pa-6 reasoning-detail animate-fade-in mb-6"
                        key="reasoning"
                    >
                        <div class="d-flex align-center mb-4">
                            <v-avatar color="primary" size="32" class="mr-3">
                                <v-icon size="18" color="white"
                                    >mdi-eye-check</v-icon
                                >
                            </v-avatar>
                            <h3 class="text-h6 font-weight-bold">
                                Evidencia para "{{ activeTrait.trait_name }}"
                            </h3>
                        </div>
                        <div
                            class="rationale-content pa-4 rounded-xl border-l-4 border-primary bg-slate-900"
                        >
                            <p class="text-body-2 mb-0 text-slate-200 italic">
                                "{{ activeTrait.rationale }}"
                            </p>
                        </div>
                    </v-card>

                    <!-- AI Reasoning Flow Stepper -->
                    <v-card
                        v-if="session.metadata.ai_reasoning_flow?.length"
                        class="glass-card pa-6"
                    >
                        <div class="d-flex align-center mb-6">
                            <v-icon color="purple" class="mr-3"
                                >mdi-transit-connection-variant</v-icon
                            >
                            <h3 class="text-h6 font-weight-bold">
                                Flujo de Razonamiento Cognitivo
                            </h3>
                        </div>
                        <div class="reasoning-stepper">
                            <div
                                v-for="(step, i) in session.metadata
                                    .ai_reasoning_flow"
                                :key="i"
                                class="stepper-item d-flex pb-6"
                            >
                                <div
                                    class="stepper-line-container d-flex flex-column align-center mr-4"
                                >
                                    <div class="stepper-dot"></div>
                                    <div
                                        v-if="
                                            i <
                                            session.metadata.ai_reasoning_flow
                                                .length -
                                                1
                                        "
                                        class="stepper-line"
                                    ></div>
                                </div>
                                <div class="stepper-content pt-0">
                                    <div class="text-body-2 text-slate-300">
                                        {{ step }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </v-card>
                </v-col>

                <!-- Visualization & Blind Spots -->
                <v-col cols="12" md="5">
                    <v-card class="glass-card pa-6 mb-6">
                        <v-card-title class="mb-4 px-0 pt-0"
                            >Arquitectura Cognitiva</v-card-title
                        >
                        <div class="radar-container d-flex justify-center">
                            <VueApexCharts
                                type="radar"
                                height="380"
                                width="100%"
                                :options="radarChartOptions"
                                :series="radarChartSeries"
                            ></VueApexCharts>
                        </div>
                    </v-card>

                    <v-card
                        v-if="session.metadata.blind_spots?.length"
                        class="glass-card pa-6 border-warning-glow"
                    >
                        <div class="d-flex align-center mb-4">
                            <v-icon color="warning" class="mr-2"
                                >mdi-alert-octagram</v-icon
                            >
                            <h3 class="text-h6 font-weight-bold text-warning">
                                Puntos Ciegos Detectados
                            </h3>
                        </div>
                        <v-list bg-color="transparent" class="pa-0">
                            <v-list-item
                                v-for="(spot, i) in session.metadata
                                    .blind_spots"
                                :key="i"
                                class="mb-2 px-0"
                            >
                                <template v-slot:prepend>
                                    <v-icon
                                        color="warning"
                                        size="18"
                                        class="mr-2"
                                        >mdi-chevron-right</v-icon
                                    >
                                </template>
                                <v-list-item-title
                                    class="text-body-2 white--text whitespace-normal"
                                >
                                    {{ spot }}
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<style scoped>
.results-page {
    background: radial-gradient(circle at top right, #1e293b 0%, #0f172a 100%);
    min-height: 100vh;
    color: #f8fafc;
    font-family: 'Outfit', sans-serif;
}

.glass-card {
    background: rgba(30, 41, 59, 0.7);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 24px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4);
}

.potential-badge {
    background: rgba(15, 23, 42, 0.5);
    border: 2px solid rgba(255, 255, 255, 0.05);
    min-width: 200px;
}

.profile-avatar {
    border: 4px solid rgba(59, 130, 246, 0.3);
}

.trait-item {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid transparent;
}

.trait-item:hover {
    background: rgba(255, 255, 255, 0.05);
    transform: translateX(4px);
}

.active-trait {
    background: rgba(59, 130, 246, 0.1);
    border-color: rgba(59, 130, 246, 0.3);
}

.border-warning-glow {
    border: 1px solid rgba(245, 158, 11, 0.3);
    box-shadow: 0 0 20px rgba(245, 158, 11, 0.05);
}

/* Stepper Styles */
.stepper-item {
    position: relative;
}

.stepper-dot {
    width: 12px;
    height: 12px;
    background: #8b5cf6;
    border-radius: 50%;
    box-shadow: 0 0 10px rgba(139, 92, 246, 0.5);
    z-index: 2;
}

.stepper-line {
    width: 2px;
    flex-grow: 1;
    background: rgba(139, 92, 246, 0.2);
    margin-top: 4px;
    margin-bottom: 4px;
}

.stepper-line-container {
    width: 12px;
}

.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
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

.whitespace-normal {
    white-space: normal;
}
</style>
