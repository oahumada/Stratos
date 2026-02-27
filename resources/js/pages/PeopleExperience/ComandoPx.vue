<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { usePermissions } from '../../composables/usePermissions';
import AppLayout from '../../layouts/AppLayout.vue';

const { can } = usePermissions();

interface PxCampaign {
    id: number;
    name: string;
    mode: string;
    status: string;
    starts_at: string | null;
    created_at: string;
}

const campaigns = ref<PxCampaign[]>([]);
const loading = ref(false);
const wizardDialog = ref(false);
const saving = ref(false);

// Wizard State
const step = ref(1);
const newCampaign = ref({
    name: 'Campaña Clima & Estrés',
    mode: 'agent_autonomous',
    scope: { type: 'randomized_sample', target_pct: 20 },
    topics: ['clima', 'stress', 'happiness'],
});

const modeOptions = [
    { title: 'IA Autónoma (Aleatorio Dirigido)', value: 'agent_autonomous' },
    { title: 'Recurrente Programado', value: 'recurring' },
    { title: 'Envío Masivo (Fecha Única)', value: 'specific_date' },
];

const topicOptions = [
    { title: 'Clima Laboral', value: 'clima' },
    { title: 'Niveles de Estrés', value: 'stress' },
    { title: 'Burnout & Salud Ocupacional', value: 'burnout' },
    { title: 'Felicidad y Engagement', value: 'happiness' },
    { title: 'Safety (Seguridad Física)', value: 'health' },
];

const scopeOptions = [
    { title: 'Muestra Aleatoria (Recomendado IA)', value: 'randomized_sample' },
    { title: 'Toda la Empresa', value: 'all' },
    { title: 'Por Departamento / Área', value: 'department' },
];

const loadCampaigns = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/px-campaigns');
        campaigns.value = data.data;
    } catch (e) {
        console.error('Failed to load px campaigns', e);
    } finally {
        loading.value = false;
    }
};

const saveCampaign = async () => {
    saving.value = true;
    try {
        await axios.post('/api/px-campaigns', {
            ...newCampaign.value,
            schedule_config: { frequency: 'monthly' }, // Mock de parametrización de agenda
        });
        wizardDialog.value = false;
        step.value = 1;
        loadCampaigns();
    } catch (e) {
        console.error('Failed to save px campaign', e);
    } finally {
        saving.value = false;
    }
};

const getStatusColor = (status: string) => {
    const map: Record<string, string> = {
        draft: 'grey',
        scheduled: 'info',
        active: 'success',
        paused: 'warning',
        completed: 'indigo',
    };
    return map[status] || 'grey';
};

onMounted(() => {
    loadCampaigns();
});
</script>

<template>
    <AppLayout>
        <v-container fluid class="pa-6">
            <div class="d-flex align-center justify-space-between mb-6">
                <div>
                    <h1 class="text-h4 font-weight-bold mb-2 text-white">
                        Comando People Experience (PX)
                    </h1>
                    <p class="text-grey-lighten-1">
                        Orquestación activa de clima, salud laboral y prevención
                        de burnout.
                    </p>
                </div>

                <v-btn
                    v-if="can('assessments.manage')"
                    prepend-icon="mdi-brain"
                    color="success"
                    size="large"
                    @click="wizardDialog = true"
                >
                    Nueva Campaña PX
                </v-btn>
            </div>

            <v-row>
                <v-col cols="12">
                    <v-card class="bg-surface-dark border-auth" elevation="0">
                        <v-card-title class="pa-4 d-flex align-center">
                            <v-icon
                                icon="mdi-heart-pulse"
                                color="success"
                                class="mr-2"
                            ></v-icon>
                            Campañas de Medición Activas
                        </v-card-title>
                        <v-divider></v-divider>

                        <v-data-table
                            :headers="[
                                { title: 'Campaña', key: 'name' },
                                { title: 'Despliegue', key: 'mode' },
                                { title: 'Estado', key: 'status' },
                                { title: 'F. Inicio', key: 'starts_at' },
                                {
                                    title: 'Acciones',
                                    key: 'actions',
                                    sortable: false,
                                    align: 'end',
                                },
                            ]"
                            :items="campaigns"
                            :loading="loading"
                            class="bg-transparent"
                            theme="dark"
                        >
                            <template #[`item.status`]="{ item }">
                                <v-chip
                                    :color="getStatusColor(item.status)"
                                    size="small"
                                    class="text-uppercase font-weight-bold"
                                >
                                    {{ item.status }}
                                </v-chip>
                            </template>

                            <template #[`item.mode`]="{ item }">
                                {{
                                    modeOptions.find(
                                        (o) => o.value === item.mode,
                                    )?.title || item.mode
                                }}
                            </template>

                            <template #[`item.actions`]>
                                <v-btn
                                    icon="mdi-pencil"
                                    variant="text"
                                    size="small"
                                    color="grey-lighten-1"
                                ></v-btn>
                                <v-btn
                                    icon="mdi-chart-box-outline"
                                    variant="text"
                                    size="small"
                                    color="success"
                                ></v-btn>
                            </template>

                            <template #no-data>
                                <div class="pa-8 text-grey text-center">
                                    <v-icon
                                        icon="mdi-shield-check-outline"
                                        size="48"
                                        class="text-grey-darken-2 mb-4"
                                    ></v-icon>
                                    <p>
                                        No hay campañas de clima/salud
                                        configuradas aún.
                                    </p>
                                </div>
                            </template>
                        </v-data-table>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- Wizard PX -->
        <v-dialog v-model="wizardDialog" max-width="900" persistent>
            <v-card class="bg-surface-dark bg-blur border-auth">
                <v-toolbar color="transparent" class="px-2">
                    <v-toolbar-title class="font-weight-bold text-white">
                        <v-icon
                            icon="mdi-brain"
                            color="success"
                            class="mr-2"
                        ></v-icon>
                        Diseñar Campaña PX
                    </v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn
                        icon="mdi-close"
                        variant="text"
                        @click="wizardDialog = false"
                    ></v-btn>
                </v-toolbar>

                <v-card-text class="pa-0">
                    <v-stepper v-model="step" class="bg-transparent" alt-labels>
                        <template #default>
                            <v-stepper-header class="elevation-0">
                                <v-stepper-item
                                    title="Objetivo & Tópicos"
                                    value="1"
                                    :complete="step > 1"
                                    color="success"
                                ></v-stepper-item>
                                <v-divider></v-divider>
                                <v-stepper-item
                                    title="Alcance & Distribución"
                                    value="2"
                                    :complete="step > 2"
                                    color="success"
                                ></v-stepper-item>
                                <v-divider></v-divider>
                                <v-stepper-item
                                    title="Confirmación"
                                    value="3"
                                    color="success"
                                ></v-stepper-item>
                            </v-stepper-header>

                            <v-stepper-window>
                                <!-- Paso 1 -->
                                <v-stepper-window-item value="1">
                                    <v-card
                                        class="elevation-0 pa-4 bg-transparent"
                                    >
                                        <v-text-field
                                            v-model="newCampaign.name"
                                            label="Denominación de la Campaña"
                                            variant="outlined"
                                            color="success"
                                            placeholder="Ej. Termómetro de Burnout Q3"
                                            class="mb-4"
                                        ></v-text-field>

                                        <h3
                                            class="text-subtitle-1 mt-4 mb-3 text-white"
                                        >
                                            Tópicos Clínicos/Organizacionales a
                                            medir
                                        </h3>
                                        <v-select
                                            v-model="newCampaign.topics"
                                            :items="topicOptions"
                                            multiple
                                            chips
                                            variant="outlined"
                                            color="success"
                                        ></v-select>
                                    </v-card>
                                    <div class="d-flex pa-4 justify-end">
                                        <v-btn color="success" @click="step = 2"
                                            >Siguiente
                                            <v-icon
                                                icon="mdi-chevron-right"
                                            ></v-icon
                                        ></v-btn>
                                    </div>
                                </v-stepper-window-item>

                                <!-- Paso 2 -->
                                <v-stepper-window-item value="2">
                                    <v-card
                                        class="elevation-0 pa-4 bg-transparent"
                                    >
                                        <h3
                                            class="text-subtitle-1 mb-3 text-white"
                                        >
                                            Alcance Poblacional
                                        </h3>
                                        <v-select
                                            v-model="newCampaign.scope.type"
                                            :items="scopeOptions"
                                            variant="outlined"
                                            color="success"
                                        ></v-select>

                                        <v-slider
                                            v-if="
                                                newCampaign.scope.type ===
                                                'randomized_sample'
                                            "
                                            v-model="
                                                newCampaign.scope.target_pct
                                            "
                                            class="mt-4"
                                            label="Tráfico Aleatorio %"
                                            min="5"
                                            max="100"
                                            step="5"
                                            thumb-label
                                            color="info"
                                        ></v-slider>

                                        <h3
                                            class="text-subtitle-1 mt-6 mb-3 text-white"
                                        >
                                            Motor de Distribución
                                        </h3>
                                        <v-radio-group
                                            v-model="newCampaign.mode"
                                            color="success"
                                        >
                                            <v-radio
                                                v-for="opt in modeOptions"
                                                :key="opt.value"
                                                :label="opt.title"
                                                :value="opt.value"
                                            ></v-radio>
                                        </v-radio-group>

                                        <v-alert
                                            v-if="
                                                newCampaign.mode ===
                                                'agent_autonomous'
                                            "
                                            type="info"
                                            variant="tonal"
                                            class="mt-2"
                                        >
                                            La IA decidirá los momentos óptimos
                                            para despachar micro-encuestas a los
                                            usuarios en base al tráfico y carga
                                            de trabajo para no saturarlos,
                                            maximizando la tasa de respuesta.
                                        </v-alert>
                                    </v-card>
                                    <div
                                        class="d-flex justify-space-between pa-4"
                                    >
                                        <v-btn variant="text" @click="step = 1"
                                            >Atrás</v-btn
                                        >
                                        <v-btn color="success" @click="step = 3"
                                            >Revisar</v-btn
                                        >
                                    </div>
                                </v-stepper-window-item>

                                <!-- Paso 3 -->
                                <v-stepper-window-item value="3">
                                    <v-card
                                        class="elevation-0 pa-6 bg-transparent text-center"
                                    >
                                        <v-icon
                                            icon="mdi-brain"
                                            size="64"
                                            color="success"
                                            class="mb-4"
                                        ></v-icon>
                                        <h2 class="text-h5 mb-2 text-white">
                                            Comando Listo
                                        </h2>
                                        <p class="text-grey mb-6">
                                            Cerbero y Sentinel quedarán listos
                                            para comenzar la prospección de
                                            datos tan pronto como actives la
                                            campaña.
                                        </p>

                                        <v-list
                                            class="bg-surface mb-6 rounded text-left"
                                            density="compact"
                                        >
                                            <v-list-item
                                                title="Tópicos a monitorear"
                                                :subtitle="
                                                    newCampaign.topics.join(
                                                        ', ',
                                                    )
                                                "
                                            ></v-list-item>
                                            <v-list-item
                                                title="Distribución"
                                                :subtitle="
                                                    newCampaign.mode ===
                                                    'agent_autonomous'
                                                        ? 'Optimizada por la Inteligencia Artificial'
                                                        : 'Manual/Calendario'
                                                "
                                            ></v-list-item>
                                            <v-list-item
                                                v-if="
                                                    newCampaign.scope.type ===
                                                    'randomized_sample'
                                                "
                                                title="Población Constante"
                                                :subtitle="`Muestra aleatoria del ${newCampaign.scope.target_pct}%`"
                                            ></v-list-item>
                                        </v-list>
                                    </v-card>

                                    <div
                                        class="d-flex justify-space-between pa-4"
                                    >
                                        <v-btn variant="text" @click="step = 2"
                                            >Atrás</v-btn
                                        >
                                        <v-btn
                                            color="success"
                                            :loading="saving"
                                            prepend-icon="mdi-check"
                                            @click="saveCampaign"
                                        >
                                            Establecer Campaña
                                        </v-btn>
                                    </div>
                                </v-stepper-window-item>
                            </v-stepper-window>
                        </template>
                    </v-stepper>
                </v-card-text>
            </v-card>
        </v-dialog>
    </AppLayout>
</template>

<style scoped>
.bg-surface-dark {
    background-color: #1e1e24 !important;
}
.bg-blur {
    backdrop-filter: blur(10px);
    background-color: rgba(30, 30, 36, 0.95) !important;
}
.border-auth {
    border: 1px solid rgba(255, 255, 255, 0.05);
}
</style>
