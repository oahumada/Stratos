<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import axios from 'axios';
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
    colors: ['#2196F3'],
    fill: {
        opacity: 0.4,
        colors: ['#2196F3'],
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['#1976D2'],
        dashArray: 0,
    },
    markers: {
        size: 4,
        hover: { size: 7 },
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

const getPotentialColor = (score: number) => {
    if (score >= 0.8) return 'success';
    if (score >= 0.5) return 'warning';
    return 'error';
};

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
    <v-container fluid class="pa-6">
        <v-row>
            <v-col cols="12">
                <div class="d-flex align-center justify-space-between mb-4">
                    <div>
                        <h1 class="text-h4 font-weight-bold primary--text">
                            Talento 360 Dashboard
                        </h1>
                        <p class="text-subtitle-1 text-secondary">
                            Análisis inteligente de potencial y rasgos
                            organizacionales
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <v-btn
                            color="secondary"
                            variant="outlined"
                            prepend-icon="mdi-transit-connection-variant"
                            @click="router.visit('/talento360/relationships')"
                        >
                            Gestionar Mapa Cerbero
                        </v-btn>
                        <v-btn
                            color="primary"
                            prepend-icon="mdi-account-search"
                            @click="router.visit('/people')"
                        >
                            Evaluar Colaboradores
                        </v-btn>
                    </div>
                </div>
            </v-col>
        </v-row>

        <v-row v-if="loading">
            <v-col cols="12" class="py-10 text-center">
                <v-progress-circular
                    indeterminate
                    color="primary"
                    size="64"
                ></v-progress-circular>
            </v-col>
        </v-row>

        <template v-else>
            <!-- Summary Widgets -->
            <v-row>
                <v-col cols="12" sm="6" md="3">
                    <v-card border flat class="pa-4 bg-blue-lighten-5">
                        <div class="text-overline mb-1">
                            Índice de Potencial
                        </div>
                        <div class="d-flex align-end">
                            <div class="text-h3 font-weight-bold">
                                {{ metrics.potential_index }}%
                            </div>
                            <v-icon color="primary" class="mb-2 ml-2"
                                >mdi-trending-up</v-icon
                            >
                        </div>
                        <div class="text-caption text-secondary">
                            Promedio organizacional
                        </div>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <v-card border flat class="pa-4 bg-green-lighten-5">
                        <div class="text-overline mb-1">High Potentials</div>
                        <div class="d-flex align-end">
                            <div class="text-h3 font-weight-bold text-success">
                                {{ metrics.high_potential_count }}
                            </div>
                            <v-icon color="success" class="mb-2 ml-2"
                                >mdi-star</v-icon
                            >
                        </div>
                        <div
                            class="d-flex align-center justify-space-between mt-1"
                        >
                            <div class="text-caption text-secondary">
                                Candidatos a liderazgo
                            </div>
                            <v-btn
                                size="x-small"
                                variant="tonal"
                                color="deep-purple"
                                prepend-icon="mdi-dna"
                                @click="openDnaExtractor"
                            >
                                DNA
                            </v-btn>
                        </div>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <v-card border flat class="pa-4 bg-purple-lighten-5">
                        <div class="text-overline mb-1">Total Evaluados</div>
                        <div class="d-flex align-end">
                            <div class="text-h3 font-weight-bold">
                                {{ metrics.total_assessed }}
                            </div>
                            <v-icon color="purple" class="mb-2 ml-2"
                                >mdi-account-check</v-icon
                            >
                        </div>
                        <div class="text-caption text-secondary">
                            Cobertura de evaluación
                        </div>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <v-card border flat class="pa-4 bg-orange-lighten-5">
                        <div class="text-overline mb-1">Riesgo de Fuga</div>
                        <div class="d-flex align-end">
                            <div class="text-h3 font-weight-bold text-warning">
                                12%
                            </div>
                            <v-icon color="warning" class="mb-2 ml-2"
                                >mdi-alert</v-icon
                            >
                        </div>
                        <div class="text-caption text-secondary">
                            Basado en alineación (MOCK)
                        </div>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Charts and Activity -->
            <v-row class="mt-4">
                <v-col cols="12" md="7">
                    <v-card border flat class="pa-4 fill-height">
                        <v-card-title class="px-0 pt-0"
                            >Mapa de Rasgos Organizacionales</v-card-title
                        >
                        <v-card-subtitle class="px-0"
                            >Promedio de competencias blandas
                            detectadas</v-card-subtitle
                        >

                        <div class="chart-container py-4">
                            <VueApexCharts
                                type="radar"
                                height="350"
                                :options="chartOptions"
                                :series="chartSeries"
                            ></VueApexCharts>
                        </div>
                    </v-card>
                </v-col>

                <v-col cols="12" md="5">
                    <v-card border flat class="pa-4 fill-height">
                        <v-card-title class="px-0 pt-0"
                            >Actividad Reciente</v-card-title
                        >
                        <v-list class="px-0">
                            <v-list-item
                                v-for="assessment in metrics.latest_assessments"
                                :key="assessment.id"
                                class="border-bottom-light px-0"
                                link
                                @click="
                                    router.visit(
                                        '/talento360/results/' + assessment.id,
                                    )
                                "
                            >
                                <template v-slot:prepend>
                                    <v-avatar color="grey-lighten-3">
                                        {{
                                            assessment.person_name
                                                ? assessment.person_name.charAt(
                                                      0,
                                                  )
                                                : '?'
                                        }}
                                    </v-avatar>
                                </template>
                                <v-list-item-title class="font-weight-bold">{{
                                    assessment.person_name
                                }}</v-list-item-title>
                                <v-list-item-subtitle
                                    >{{ assessment.type }} •
                                    {{
                                        assessment.completed_at
                                    }}</v-list-item-subtitle
                                >
                                <template v-slot:append>
                                    <v-chip
                                        :color="
                                            getPotentialColor(
                                                assessment.potential,
                                            )
                                        "
                                        size="small"
                                    >
                                        {{
                                            (
                                                assessment.potential * 100
                                            ).toFixed(0)
                                        }}%
                                    </v-chip>
                                </template>
                            </v-list-item>
                            <v-list-item
                                v-if="!metrics.latest_assessments.length"
                                class="py-10 text-center text-secondary"
                            >
                                Sin evaluaciones recientes.
                            </v-list-item>
                        </v-list>
                        <v-divider class="my-2"></v-divider>
                        <v-btn
                            variant="text"
                            block
                            color="primary"
                            class="mt-2"
                            @click="router.visit('/people')"
                        >
                            Ver todo el talento
                        </v-btn>
                    </v-card>
                </v-col>
            </v-row>
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
    </v-container>
</template>

<style scoped>
.chart-container {
    min-height: 350px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.border-bottom-light {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}
</style>
