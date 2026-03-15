<script setup lang="ts">
import StCardGlass from '@/components/StCardGlass.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import {
    ChevronRight,
    LayoutGrid,
    Rocket,
    ShieldAlert,
    Star,
    TrendingUp,
    Users,
} from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

interface Trait {
    trait_name: string;
    average_score: number;
}

interface Assessment {
    id: number;
    person_name: string;
    type: string;
    potential: number;
    completed_at: string;
}

const metrics = ref({
    potential_index: 0,
    high_potential_count: 0,
    total_assessed: 0,
    trait_distribution: [] as Trait[],
    latest_assessments: [] as Assessment[],
});

const loading = ref(true);

const loadMetrics = async () => {
    loading.value = true;
    try {
        const response = await axios.get(
            '/api/strategic-planning/assessments/metrics',
        );
        metrics.value = response.data;
    } catch (e) {
        console.error('Error loading Talento 360 metrics', e);
    } finally {
        loading.value = false;
    }
};

onMounted(loadMetrics);

// Radar Chart Data
const chartOptions = computed(() => ({
    chart: {
        id: 'traits-radar',
        toolbar: { show: false },
    },
    xaxis: {
        categories: metrics.value.trait_distribution.map((t) => t.trait_name),
    },
    yaxis: {
        show: false,
        min: 0,
        max: 1,
    },
    colors: ['#3b82f6'],
    fill: {
        opacity: 0.4,
        type: 'gradient',
        gradient: {
            shade: 'dark',
            type: 'vertical',
            gradientToColors: ['#8b5cf6'],
            stops: [0, 100],
        },
    },
    stroke: {
        show: true,
        width: 3,
        colors: ['#3b82f6'],
        dashArray: 0,
    },
    markers: {
        size: 5,
        strokeColors: '#3b82f6',
        strokeWidth: 2,
        hover: { size: 8 },
    },
    theme: {
        mode: 'dark',
    },
    grid: {
        borderColor: 'rgba(255,255,255,0.05)',
    },
}));

const chartSeries = computed(() => [
    {
        name: 'Promedio Organizacional',
        data: metrics.value.trait_distribution.map((t) =>
            Number.parseFloat(t.average_score.toString()),
        ),
    },
]);

// DNA Cloning Feature
const dnaDialog = ref(false);
const dnaLoading = ref(false);
const dnaResult = ref<{
    success_persona: string;
    dominant_gene: string;
    search_profile: string;
} | null>(null);
const selectedPerson = ref<{ id: number; name: string } | null>(null);

const openDnaExtractor = () => {
    dnaDialog.value = true;
    dnaResult.value = null;
};

const extractDNA = async (personId: number, personName: string) => {
    selectedPerson.value = { id: personId, name: personName };
    dnaLoading.value = true;
    dnaResult.value = null;
    try {
        const response = await axios.post(
            `/api/talent/dna-extract/${personId}`,
        );
        dnaResult.value = response.data.data;
    } catch (error) {
        console.error('Error extrayendo DNA:', error);
    } finally {
        dnaLoading.value = false;
    }
};
</script>

<template>
    <div class="mx-auto min-h-screen max-w-7xl space-y-8 p-8">
        <!-- Header Section -->
        <header
            class="flex flex-col justify-between gap-6 md:flex-row md:items-end"
        >
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <div class="rounded-2xl bg-primary/20 p-2.5 text-primary">
                        <Users :size="28" />
                    </div>
                    <div>
                        <h1
                            class="text-3xl font-black tracking-tight text-white"
                        >
                            Talento 360° Dashboard
                        </h1>
                        <p class="font-medium text-slate-400">
                            Análisis predictivo y visualización de potencial
                            humano.
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <button
                    @click="router.visit('/talento360/relationships')"
                    class="flex items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-sm font-bold text-white transition-all hover:bg-white/10"
                >
                    <LayoutGrid :size="18" />
                    Mapa Cerbero
                </button>
                <button
                    @click="router.visit('/talento360/war-room')"
                    class="flex items-center gap-2 rounded-2xl bg-linear-to-r from-primary to-indigo-600 px-6 py-3 text-sm font-black text-white shadow-xl shadow-primary/20 transition-all hover:scale-105 active:scale-95"
                >
                    <Rocket :size="18" />
                    MOBILITY WAR ROOM
                </button>
            </div>
        </header>

        <!-- Loading State -->
        <div
            v-if="loading"
            class="flex h-96 flex-col items-center justify-center gap-4"
        >
            <div
                class="h-16 w-16 animate-spin rounded-full border-4 border-primary/20 border-t-primary"
            ></div>
            <p class="animate-pulse font-bold text-slate-500">
                Sincronizando red neuronal de talento...
            </p>
        </div>

        <template v-else>
            <!-- Strategic Metrics Grid -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <StCardGlass
                    class="group relative overflow-hidden border-white/5 p-8"
                >
                    <div
                        class="absolute -right-4 -bottom-4 text-primary/5 transition-colors group-hover:text-primary/10"
                    >
                        <TrendingUp :size="120" />
                    </div>
                    <p
                        class="mb-4 text-[10px] font-black tracking-[0.2em] text-slate-500 uppercase"
                    >
                        Índice de Potencial
                    </p>
                    <div class="flex flex-col">
                        <span
                            class="text-4xl leading-none font-black text-white"
                            >{{ metrics.potential_index }}%</span
                        >
                        <span class="mt-2 text-xs font-medium text-slate-400"
                            >Promedio organizacional</span
                        >
                    </div>
                </StCardGlass>

                <StCardGlass
                    class="group relative overflow-hidden border-emerald-500/10 p-8"
                >
                    <div
                        class="absolute -right-4 -bottom-4 text-emerald-500/5 transition-colors group-hover:text-emerald-500/10"
                    >
                        <Star :size="120" />
                    </div>
                    <p
                        class="mb-4 text-[10px] font-black tracking-[0.2em] text-slate-500 uppercase"
                    >
                        High Potentials
                    </p>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-4">
                            <span
                                class="text-4xl leading-none font-black text-emerald-400"
                                >{{ metrics.high_potential_count }}</span
                            >
                            <button
                                @click="openDnaExtractor"
                                class="rounded-full border border-emerald-500/30 bg-emerald-500/20 px-3 py-1 text-[10px] font-bold text-emerald-400 uppercase transition-all hover:bg-emerald-500/30"
                            >
                                DNA Extractor
                            </button>
                        </div>
                        <span class="mt-2 text-xs font-medium text-slate-400"
                            >Candidatos a liderazgo estratégico</span
                        >
                    </div>
                </StCardGlass>

                <StCardGlass
                    class="group relative overflow-hidden border-indigo-500/10 p-8"
                >
                    <div
                        class="absolute -right-4 -bottom-4 text-indigo-500/5 transition-colors group-hover:text-indigo-500/10"
                    >
                        <Users :size="120" />
                    </div>
                    <p
                        class="mb-4 text-[10px] font-black tracking-[0.2em] text-slate-500 uppercase"
                    >
                        Total Evaluados
                    </p>
                    <div class="flex flex-col">
                        <span
                            class="text-4xl leading-none font-black text-indigo-400"
                            >{{ metrics.total_assessed }}</span
                        >
                        <span class="mt-2 text-xs font-medium text-slate-400"
                            >Cobertura actual del 85%</span
                        >
                    </div>
                </StCardGlass>

                <StCardGlass
                    class="group relative overflow-hidden border-amber-500/10 p-8"
                >
                    <div
                        class="absolute -right-4 -bottom-4 text-amber-500/5 transition-colors group-hover:text-amber-500/10"
                    >
                        <ShieldAlert :size="120" />
                    </div>
                    <p
                        class="mb-4 text-[10px] font-black tracking-[0.2em] text-slate-500 uppercase"
                    >
                        Riesgo de Fuga
                    </p>
                    <div class="flex flex-col">
                        <span
                            class="text-4xl leading-none font-black text-amber-500"
                            >12%</span
                        >
                        <span class="mt-2 text-xs font-medium text-slate-400"
                            >Basado en alineación cultural</span
                        >
                    </div>
                </StCardGlass>
            </div>

            <!-- Main Analysis Area -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                <!-- Radar Analysis -->
                <StCardGlass class="border-white/5 p-8 lg:col-span-7">
                    <div class="mb-8 flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-white">
                                Mapa de Rasgos Organizacionales
                            </h3>
                            <p class="text-sm text-slate-400">
                                Distribución de competencias blandas detectadas.
                            </p>
                        </div>
                    </div>
                    <div class="chart-container">
                        <VueApexCharts
                            type="radar"
                            height="450"
                            width="100%"
                            :options="chartOptions"
                            :series="chartSeries"
                        />
                    </div>
                </StCardGlass>

                <!-- Recent Activity -->
                <StCardGlass
                    class="flex flex-col border-white/5 p-8 lg:col-span-5"
                >
                    <div class="mb-8 flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-white">
                                Actividad Reciente
                            </h3>
                            <p class="text-sm text-slate-400">
                                Últimas evaluaciones finalizadas.
                            </p>
                        </div>
                    </div>

                    <div class="flex-1 space-y-4">
                        <button
                            v-for="assessment in metrics.latest_assessments"
                            :key="assessment.id"
                            @click="
                                router.visit(
                                    '/talento360/results/' + assessment.id,
                                )
                            "
                            class="group flex w-full items-center justify-between rounded-2xl border border-white/5 bg-white/5 p-4 transition-all hover:border-white/10 hover:bg-white/10"
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 font-black text-primary transition-transform group-hover:scale-110"
                                >
                                    {{
                                        assessment.person_name
                                            ? assessment.person_name.charAt(0)
                                            : '?'
                                    }}
                                </div>
                                <div class="text-left">
                                    <h4 class="text-sm font-bold text-white">
                                        {{ assessment.person_name }}
                                    </h4>
                                    <p
                                        class="text-[10px] font-medium tracking-wider text-slate-500 uppercase"
                                    >
                                        {{ assessment.type }} •
                                        {{ assessment.completed_at }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div
                                    class="rounded-lg px-3 py-1 text-xs font-black"
                                    :class="
                                        assessment.potential >= 0.8
                                            ? 'bg-emerald-500/20 text-emerald-400'
                                            : 'bg-amber-500/20 text-amber-400'
                                    "
                                >
                                    {{
                                        (assessment.potential * 100).toFixed(0)
                                    }}%
                                </div>
                                <ChevronRight
                                    :size="16"
                                    class="text-slate-600"
                                />
                            </div>
                        </button>

                        <div
                            v-if="!metrics.latest_assessments.length"
                            class="flex h-64 flex-col items-center justify-center text-slate-600 italic"
                        >
                            No hay evaluaciones recientes.
                        </div>
                    </div>

                    <button
                        @click="router.visit('/people')"
                        class="mt-8 w-full rounded-xl border border-white/5 py-4 text-sm font-bold text-slate-400 transition-all hover:bg-white/5"
                    >
                        Ver Repositorio de Talento Completo
                    </button>
                </StCardGlass>
            </div>
        </template>

        <!-- DNA Cloning Dialog -->
        <v-dialog v-model="dnaDialog" max-width="600" persistent>
            <v-card class="rounded-xl" color="grey-darken-4">
                <v-card-title class="d-flex align-center pa-5">
                    <v-icon
                        icon="mdi-dna"
                        color="deep-purple-accent-2"
                        class="mr-2"
                    ></v-icon>
                    DNA Cloning: Extraer Blueprint de Éxito
                    <v-spacer></v-spacer>
                    <v-btn
                        icon="mdi-close"
                        variant="text"
                        size="small"
                        @click="dnaDialog = false"
                    ></v-btn>
                </v-card-title>

                <v-card-text class="pa-5">
                    <p class="text-body-2 text-grey-lighten-1 mb-4">
                        Selecciona un High-Performer para extraer su DNA de
                        éxito. El Matchmaker de Resonancia analizará su
                        combinación de skills, DISC y estilo cultural para crear
                        un perfil cloneable.
                    </p>

                    <!-- Person Selection -->
                    <div v-if="!dnaLoading && !dnaResult">
                        <v-list bg-color="transparent">
                            <v-list-item
                                v-for="assessment in metrics.latest_assessments.filter(
                                    (a) => a.potential >= 0.7,
                                )"
                                :key="'dna-' + assessment.id"
                                class="mb-2 rounded-lg border border-white/5"
                                @click="
                                    extractDNA(
                                        assessment.id,
                                        assessment.person_name,
                                    )
                                "
                            >
                                <template v-slot:prepend>
                                    <v-avatar
                                        color="deep-purple-darken-3"
                                        size="36"
                                    >
                                        {{
                                            assessment.person_name?.charAt(0) ||
                                            '?'
                                        }}
                                    </v-avatar>
                                </template>
                                <v-list-item-title class="font-weight-bold">{{
                                    assessment.person_name
                                }}</v-list-item-title>
                                <v-list-item-subtitle
                                    >Potencial:
                                    {{
                                        (assessment.potential * 100).toFixed(0)
                                    }}%</v-list-item-subtitle
                                >
                                <template v-slot:append>
                                    <v-icon
                                        icon="mdi-chevron-right"
                                        size="20"
                                    ></v-icon>
                                </template>
                            </v-list-item>
                        </v-list>
                        <p
                            v-if="
                                !metrics.latest_assessments.filter(
                                    (a) => a.potential >= 0.7,
                                ).length
                            "
                            class="text-grey py-6 text-center"
                        >
                            No hay High-Performers evaluados todavía.
                        </p>
                    </div>

                    <!-- Loading -->
                    <div v-if="dnaLoading" class="py-8 text-center">
                        <v-progress-circular
                            indeterminate
                            color="deep-purple-accent-2"
                            size="64"
                        ></v-progress-circular>
                        <p class="text-grey-lighten-1 mt-4">
                            Matchmaker de Resonancia analizando DNA de
                            <strong>{{ selectedPerson?.name }}</strong
                            >...
                        </p>
                    </div>

                    <!-- Results -->
                    <div v-if="dnaResult && !dnaLoading">
                        <v-alert
                            type="success"
                            variant="tonal"
                            class="mb-4"
                            density="compact"
                        >
                            DNA de
                            <strong>{{ selectedPerson?.name }}</strong> extraído
                            exitosamente.
                        </v-alert>

                        <v-card
                            color="grey-darken-3"
                            class="pa-4 mb-3 rounded-lg"
                        >
                            <div
                                class="text-caption text-deep-purple-accent-2 font-weight-bold mb-1"
                            >
                                PERSONA DE ÉXITO
                            </div>
                            <p class="text-body-2 text-white">
                                {{ dnaResult.success_persona }}
                            </p>
                        </v-card>

                        <v-card
                            color="grey-darken-3"
                            class="pa-4 mb-3 rounded-lg"
                        >
                            <div
                                class="text-caption text-amber-accent-2 font-weight-bold mb-1"
                            >
                                GEN DOMINANTE
                            </div>
                            <p class="text-body-2 text-white">
                                {{ dnaResult.dominant_gene }}
                            </p>
                        </v-card>

                        <v-card color="grey-darken-3" class="pa-4 rounded-lg">
                            <div
                                class="text-caption text-cyan-accent-2 font-weight-bold mb-1"
                            >
                                PERFIL DE BÚSQUEDA
                            </div>
                            <p class="text-body-2 text-white">
                                {{ dnaResult.search_profile }}
                            </p>
                        </v-card>
                    </div>
                </v-card-text>

                <v-card-actions class="pa-5 pt-0">
                    <v-spacer></v-spacer>
                    <v-btn
                        v-if="dnaResult"
                        variant="tonal"
                        color="deep-purple-accent-2"
                        @click="dnaResult = null"
                    >
                        Extraer Otro
                    </v-btn>
                    <v-btn variant="text" @click="dnaDialog = false"
                        >Cerrar</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<style scoped>
.chart-container {
    min-height: 450px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
