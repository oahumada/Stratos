<template>
        <v-container
            fluid
            class="pa-6 bg-grey-lighten-4 fill-height align-start"
        >
            <v-row>
                <v-col cols="12">
                    <div class="d-flex align-center mb-6">
                        <div>
                            <h1 class="text-h4 font-weight-bold mb-1">
                                People Experience
                            </h1>
                            <p class="text-subtitle-1 text-grey-darken-1">
                                Monitoreo dinámico del bienestar y compromiso
                                organizacional.
                            </p>
                        </div>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primary"
                            prepend-icon="mdi-plus"
                            class="text-none"
                        >
                            Nueva Encuesta Pulse
                        </v-btn>
                    </div>
                </v-col>

                <!-- KPI Cards -->
                <v-col cols="12" md="3">
                    <v-card class="pa-4 rounded-xl" elevation="1">
                        <div class="d-flex align-center mb-2">
                            <v-avatar color="blue-lighten-5" class="mr-3">
                                <v-icon
                                    color="blue"
                                    icon="mdi-heart-pulse"
                                ></v-icon>
                            </v-avatar>
                            <span
                                class="text-subtitle-2 text-grey-darken-1 font-weight-medium"
                                >Score de Bienestar</span
                            >
                        </div>
                        <div class="text-h4 font-weight-bold">78%</div>
                        <div
                            class="text-caption text-success d-flex align-center mt-1"
                        >
                            <v-icon
                                icon="mdi-arrow-up"
                                size="x-small"
                                class="mr-1"
                            ></v-icon>
                            +4% vs mes anterior
                        </div>
                    </v-card>
                </v-col>

                <v-col cols="12" md="3">
                    <v-card class="pa-4 rounded-xl" elevation="1">
                        <div class="d-flex align-center mb-2">
                            <v-avatar color="green-lighten-5" class="mr-3">
                                <v-icon
                                    color="green"
                                    icon="mdi-account-star"
                                ></v-icon>
                            </v-avatar>
                            <span
                                class="text-subtitle-2 text-grey-darken-1 font-weight-medium"
                                >Engagement (eNPS)</span
                            >
                        </div>
                        <div class="text-h4 font-weight-bold">42</div>
                        <div
                            class="text-caption text-success d-flex align-center mt-1"
                        >
                            <v-icon
                                icon="mdi-arrow-up"
                                size="x-small"
                                class="mr-1"
                            ></v-icon>
                            Top 10% industria
                        </div>
                    </v-card>
                </v-col>

                <v-col cols="12" md="3">
                    <v-card class="pa-4 rounded-xl" elevation="1">
                        <div class="d-flex align-center mb-2">
                            <v-avatar color="orange-lighten-5" class="mr-3">
                                <v-icon
                                    color="orange"
                                    icon="mdi-comment-check"
                                ></v-icon>
                            </v-avatar>
                            <span
                                class="text-subtitle-2 text-grey-darken-1 font-weight-medium"
                                >Tasa de Respuesta</span
                            >
                        </div>
                        <div class="text-h4 font-weight-bold">85%</div>
                        <div class="text-caption text-grey mt-1">
                            Promedio histórico (Pulse)
                        </div>
                    </v-card>
                </v-col>

                <v-col cols="12" md="3">
                    <v-card class="pa-4 rounded-xl" elevation="1">
                        <div class="d-flex align-center mb-2">
                            <v-avatar color="purple-lighten-5" class="mr-3">
                                <v-icon
                                    color="purple"
                                    icon="mdi-lightning-bolt"
                                ></v-icon>
                            </v-avatar>
                            <span
                                class="text-subtitle-2 text-grey-darken-1 font-weight-medium"
                                >Acciones Abiertas</span
                            >
                        </div>
                        <div class="text-h4 font-weight-bold">12</div>
                        <div class="text-caption text-warning mt-1">
                            4 críticas por resolver
                        </div>
                    </v-card>
                </v-col>

                <!-- Sentiment Chart -->
                <v-col cols="12" md="8">
                    <v-card class="pa-6 fill-height rounded-xl" elevation="1">
                        <div class="d-flex align-center mb-6">
                            <h3 class="text-h6 font-weight-bold">
                                Tendencia de Sentimiento
                            </h3>
                            <v-spacer></v-spacer>
                            <v-select
                                :items="['Últimos 3 meses', '6 meses', 'Año']"
                                density="compact"
                                variant="outlined"
                                hide-details
                                style="max-width: 180px"
                                class="text-caption"
                            ></v-select>
                        </div>
                        <div
                            style="height: 300px"
                            class="d-flex align-end justify-space-between px-4 pb-4"
                        >
                            <!-- Simple custom bars for sentiment trending -->
                            <div
                                v-for="(val, i) in [65, 68, 72, 70, 75, 78]"
                                :key="i"
                                class="d-flex flex-column align-center"
                                style="width: 12%"
                            >
                                <div
                                    class="bg-blue-lighten-1 rounded-t-lg transition-all"
                                    :style="{
                                        height: val * 2.5 + 'px',
                                        width: '32px',
                                    }"
                                ></div>
                                <span class="text-caption text-grey mt-2"
                                    >Mes {{ i + 1 }}</span
                                >
                            </div>
                        </div>
                    </v-card>
                </v-col>

                <!-- Top Insights -->
                <v-col cols="12" md="4">
                    <v-card class="pa-6 fill-height rounded-xl" elevation="1">
                        <h3 class="text-h6 font-weight-bold mb-4">
                            Insights de IA
                        </h3>
                        <v-list density="compact" class="bg-transparent">
                            <v-list-item class="align-start mb-4 px-0">
                                <template v-slot:prepend>
                                    <v-icon
                                        color="success"
                                        icon="mdi-auto-fix"
                                        size="small"
                                        class="mt-1 mr-2"
                                    ></v-icon>
                                </template>
                                <div>
                                    <div class="text-body-2 font-weight-bold">
                                        Equilibrio Vida-Trabajo alto
                                    </div>
                                    <div class="text-caption text-grey">
                                        El departamento de Tecnología reporta un
                                        aumento en satisfacción tras la nueva
                                        política de 'No-Meetings Wednesday'.
                                    </div>
                                </div>
                            </v-list-item>

                            <v-list-item class="align-start mb-4 px-0">
                                <template v-slot:prepend>
                                    <v-icon
                                        color="warning"
                                        icon="mdi-alert-circle-outline"
                                        size="small"
                                        class="mt-1 mr-2"
                                    ></v-icon>
                                </template>
                                <div>
                                    <div class="text-body-2 font-weight-bold">
                                        Riesgo de Burnout
                                    </div>
                                    <div class="text-caption text-grey">
                                        El área de Operaciones muestra fatiga
                                        acumulada por cierres de proyecto.
                                        Sugerencia: Revisar distribución de
                                        cargas.
                                    </div>
                                </div>
                            </v-list-item>

                            <v-list-item class="align-start px-0">
                                <template v-slot:prepend>
                                    <v-icon
                                        color="primary"
                                        icon="mdi-star-outline"
                                        size="small"
                                        class="mt-1 mr-2"
                                    ></v-icon>
                                </template>
                                <div>
                                    <div class="text-body-2 font-weight-bold">
                                        Oportunidad de Desarrollo
                                    </div>
                                    <div class="text-caption text-grey">
                                        Un 60% de los colaboradores pide
                                        mentorías técnicas. Vincular con el
                                        módulo de Learning Paths.
                                    </div>
                                </div>
                            </v-list-item>
                        </v-list>
                        <v-btn
                            variant="tonal"
                            block
                            color="primary"
                            class="text-none mt-4"
                            >Expandir Análisis</v-btn
                        >
                    </v-card>
                </v-col>

                <!-- Pulse Surveys List -->
                <v-col cols="12">
                    <v-card
                        class="pa-0 rounded-xl"
                        elevation="1 overflow-hidden"
                    >
                        <v-card-title class="pa-6 border-b">
                            <span class="text-h6 font-weight-bold"
                                >Encuestas Pulse Recientes</span
                            >
                        </v-card-title>
                        <v-data-table
                            :headers="headers"
                            :items="surveys"
                            class="bg-transparent"
                            hide-default-footer
                        >
                            <template #[`item.type`]="{ item }">
                                <v-chip
                                    size="x-small"
                                    :color="getTypeColor(item.type)"
                                    variant="tonal"
                                >
                                    {{ item.type.toUpperCase() }}
                                </v-chip>
                            </template>
                            <template #[`item.sentiment_score`]="{ item }">
                                <div class="d-flex align-center">
                                    <v-progress-linear
                                        :model-value="item.sentiment_score"
                                        color="blue"
                                        height="6"
                                        rounded
                                        class="mr-3"
                                        style="width: 100px"
                                    ></v-progress-linear>
                                    <span class="text-caption font-weight-bold"
                                        >{{ item.sentiment_score }}%</span
                                    >
                                </div>
                            </template>
                            <template #[`item.actions`]="{ item }">
                                <v-btn
                                    icon="mdi-eye-outline"
                                    variant="text"
                                    size="small"
                                    color="grey"
                                    @click="viewResults(item)"
                                ></v-btn>
                                <v-btn
                                    icon="mdi-share-variant"
                                    variant="text"
                                    size="small"
                                    color="grey"
                                ></v-btn>
                            </template>
                        </v-data-table>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
</template>

<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref } from 'vue';

interface Survey {
    id: number;
    title: string;
    type: string;
    responses_count: number;
    sentiment_score: number;
    created_at: string;
}

const surveys = ref<Survey[]>([]);
const loading = ref(false);

const headers = [
    { title: 'Encuesta', key: 'title', align: 'start' as const },
    { title: 'Tipo', key: 'type', align: 'center' as const },
    { title: 'Respuestas', key: 'responses_count', align: 'center' as const },
    { title: 'Sentimiento', key: 'sentiment_score', align: 'start' as const },
    { title: 'Fecha', key: 'created_at', align: 'end' as const },
    { title: '', key: 'actions', sortable: false, align: 'end' as const },
];

const fetchSurveys = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/pulse-surveys');
        // Mocking some scores for the UI display
        surveys.value = response.data.data.map((s: any) => ({
            ...s,
            sentiment_score: Math.floor(Math.random() * (90 - 40 + 1)) + 40,
            created_at: new Date(s.created_at).toLocaleDateString(),
        }));
    } catch (e) {
        console.error('Error fetching pulse surveys', e);
    } finally {
        loading.value = false;
    }
};

const getTypeColor = (type: string) => {
    switch (type) {
        case 'engagement':
            return 'primary';
        case 'climate':
            return 'success';
        case 'enps':
            return 'purple';
        default:
            return 'grey';
    }
};

const viewResults = (survey: Survey) => {
    console.log('Viewing results for', survey.id);
};

onMounted(fetchSurveys);
</script>

<style scoped>
.transition-all {
    transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.transition-all:hover {
    filter: brightness(1.1);
    transform: scaleX(1.1);
}
</style>
