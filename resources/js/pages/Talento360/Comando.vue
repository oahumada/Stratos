<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { usePermissions } from '../../composables/usePermissions';
import AppLayout from '../../layouts/AppLayout.vue';

const { can } = usePermissions();

interface AssessmentCycle {
    id: number;
    name: string;
    mode: string;
    status: string;
    starts_at: string | null;
    created_at: string;
}

const cycles = ref<AssessmentCycle[]>([]);
const loading = ref(false);
const wizardDialog = ref(false);
const saving = ref(false);

// Wizard State
const step = ref(1);
const newCycle = ref({
    name: 'Evaluación Estratégica Q1',
    mode: 'quarterly',
    scope: { type: 'all', ids: [] },
    evaluators: {
        self: true,
        manager: true,
        peers: 2,
        reports: false,
        ai: true,
    },
    instruments: ['bars', 'pulse'],
    notifications: { email: true, dashboard: true },
});

const modeOptions = [
    { title: 'Fecha Específica', value: 'specific_date' },
    { title: 'Trimestral', value: 'quarterly' },
    { title: 'Anual (Aniversario)', value: 'annual' },
    { title: 'Continuo Aleatorio', value: 'continuous' },
];

const instrumentOptions = [
    { title: 'Evaluación BARS (Competencias)', value: 'bars' },
    { title: 'Encuesta Pulse (Clima)', value: 'pulse' },
    { title: 'Perfil Psicométrico (DISC)', value: 'disc' },
    { title: 'Entrevista de IA (Cerbero)', value: 'interview' },
];

const loadCycles = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/assessment-cycles');
        cycles.value = data.data;
    } catch (e) {
        console.error('Failed to load assessment cycles', e);
    } finally {
        loading.value = false;
    }
};

const saveCycle = async () => {
    saving.value = true;
    try {
        await axios.post('/api/assessment-cycles', {
            ...newCycle.value,
            schedule_config: { quarter: 1, year: 2026 }, // Mock payload param
        });
        wizardDialog.value = false;
        step.value = 1;
        loadCycles();
    } catch (e) {
        console.error('Failed to save cycle', e);
    } finally {
        saving.value = false;
    }
};

const getStatusColor = (status: string) => {
    const map: Record<string, string> = {
        draft: 'grey',
        scheduled: 'info',
        active: 'success',
        completed: 'indigo',
        cancelled: 'error',
    };
    return map[status] || 'grey';
};

onMounted(() => {
    loadCycles();
});
</script>

<template>
    <AppLayout>
        <v-container fluid class="pa-6">
            <div class="d-flex align-center justify-space-between mb-6">
                <div>
                    <h1 class="text-h4 font-weight-bold mb-2 text-white">
                        Comando 360
                    </h1>
                    <p class="text-grey-lighten-1">
                        Orquestación de ciclos de evaluación y medición de
                        talento.
                    </p>
                </div>

                <v-btn
                    v-if="can('assessments.manage')"
                    prepend-icon="mdi-rocket-launch"
                    color="primary"
                    size="large"
                    @click="wizardDialog = true"
                >
                    Nuevo Ciclo de Evaluación
                </v-btn>
            </div>

            <v-row>
                <v-col cols="12">
                    <v-card class="bg-surface-dark border-auth" elevation="0">
                        <v-card-title class="pa-4 d-flex align-center">
                            <v-icon
                                icon="mdi-calendar-sync"
                                color="primary"
                                class="mr-2"
                            ></v-icon>
                            Ciclos Activos & Programados
                        </v-card-title>
                        <v-divider></v-divider>

                        <v-data-table
                            :headers="[
                                { title: 'Nombre del Ciclo', key: 'name' },
                                { title: 'Modalidad', key: 'mode' },
                                { title: 'Estado', key: 'status' },
                                {
                                    title: 'F. Inicio (proyectada)',
                                    key: 'starts_at',
                                },
                                {
                                    title: 'Acciones',
                                    key: 'actions',
                                    sortable: false,
                                    align: 'end',
                                },
                            ]"
                            :items="cycles"
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

                            <template #[`item.actions`]="{ item }">
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
                                    color="primary"
                                ></v-btn>
                            </template>

                            <template #no-data>
                                <div class="pa-8 text-grey text-center">
                                    <v-icon
                                        icon="mdi-inbox-outline"
                                        size="48"
                                        class="text-grey-darken-2 mb-4"
                                    ></v-icon>
                                    <p>No hay ciclos configurados aún.</p>
                                </div>
                            </template>
                        </v-data-table>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- Wizard de Creación de Ciclo -->
        <v-dialog v-model="wizardDialog" max-width="900" persistent>
            <v-card class="bg-surface-dark bg-blur border-auth">
                <v-toolbar color="transparent" class="px-2">
                    <v-toolbar-title class="font-weight-bold text-white">
                        <v-icon
                            icon="mdi-rocket-launch"
                            color="primary"
                            class="mr-2"
                        ></v-icon>
                        Configurar Ciclo
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
                                    title="Contexto & Modalidad"
                                    value="1"
                                    :complete="step > 1"
                                    color="primary"
                                ></v-stepper-item>
                                <v-divider></v-divider>
                                <v-stepper-item
                                    title="Instrumentos & Evaluadores"
                                    value="2"
                                    :complete="step > 2"
                                    color="primary"
                                ></v-stepper-item>
                                <v-divider></v-divider>
                                <v-stepper-item
                                    title="Confirmación"
                                    value="3"
                                    color="primary"
                                ></v-stepper-item>
                            </v-stepper-header>

                            <v-stepper-window>
                                <!-- Paso 1 -->
                                <v-stepper-window-item value="1">
                                    <v-card
                                        class="elevation-0 pa-4 bg-transparent"
                                    >
                                        <v-text-field
                                            v-model="newCycle.name"
                                            label="Nombre del Ciclo"
                                            variant="outlined"
                                            color="primary"
                                            placeholder="Ej. Evaluación Q1 2026"
                                            class="mb-4"
                                        ></v-text-field>

                                        <h3
                                            class="text-subtitle-1 mb-3 text-white"
                                        >
                                            Modalidad del Ciclo
                                        </h3>
                                        <v-radio-group
                                            v-model="newCycle.mode"
                                            color="primary"
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
                                                newCycle.mode === 'continuous'
                                            "
                                            type="info"
                                            variant="tonal"
                                            class="mt-2"
                                        >
                                            El modo Continuo instruye a la IA
                                            para despachar pulse surveys y
                                            entrevistas de forma natural y
                                            aleatoria a lo largo del año.
                                        </v-alert>
                                    </v-card>
                                    <div class="d-flex pa-4 justify-end">
                                        <v-btn color="primary" @click="step = 2"
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
                                            Instrumentos a Desplegar
                                        </h3>
                                        <v-select
                                            v-model="newCycle.instruments"
                                            :items="instrumentOptions"
                                            multiple
                                            chips
                                            variant="outlined"
                                            color="primary"
                                        ></v-select>

                                        <h3
                                            class="text-subtitle-1 mt-4 mb-3 text-white"
                                        >
                                            Red de Evaluadores (360)
                                        </h3>
                                        <v-row>
                                            <v-col cols="6"
                                                ><v-switch
                                                    v-model="
                                                        newCycle.evaluators.self
                                                    "
                                                    label="Autoevaluación"
                                                    color="primary"
                                                    density="compact"
                                                    hide-details
                                                ></v-switch
                                            ></v-col>
                                            <v-col cols="6"
                                                ><v-switch
                                                    v-model="
                                                        newCycle.evaluators
                                                            .manager
                                                    "
                                                    label="Jefe Directo"
                                                    color="primary"
                                                    density="compact"
                                                    hide-details
                                                ></v-switch
                                            ></v-col>
                                            <v-col cols="6"
                                                ><v-switch
                                                    v-model="
                                                        newCycle.evaluators
                                                            .reports
                                                    "
                                                    label="Reportes Directos"
                                                    color="primary"
                                                    density="compact"
                                                    hide-details
                                                ></v-switch
                                            ></v-col>
                                            <v-col cols="6"
                                                ><v-switch
                                                    v-model="
                                                        newCycle.evaluators.ai
                                                    "
                                                    label="IA Cerbero (Entrevistadora)"
                                                    color="primary"
                                                    density="compact"
                                                    hide-details
                                                ></v-switch
                                            ></v-col>
                                        </v-row>
                                        <v-slider
                                            v-model="newCycle.evaluators.peers"
                                            class="mt-4"
                                            label="Nº de Pares al azar"
                                            min="0"
                                            max="5"
                                            step="1"
                                            thumb-label
                                            color="info"
                                        ></v-slider>
                                    </v-card>
                                    <div
                                        class="d-flex justify-space-between pa-4"
                                    >
                                        <v-btn variant="text" @click="step = 1"
                                            >Atrás</v-btn
                                        >
                                        <v-btn color="primary" @click="step = 3"
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
                                            icon="mdi-rocket-launch-outline"
                                            size="64"
                                            color="primary"
                                            class="mb-4"
                                        ></v-icon>
                                        <h2 class="text-h5 mb-2 text-white">
                                            Listo para lanzar orquestación
                                        </h2>
                                        <p class="text-grey mb-6">
                                            El ciclo se registrará como
                                            <strong>Draft</strong> y podrás
                                            revisar el alcance por persona antes
                                            de iniciarlo oficialmente.
                                        </p>

                                        <v-list
                                            class="bg-surface mb-6 rounded text-left"
                                            density="compact"
                                        >
                                            <v-list-item
                                                title="Nombre"
                                                :subtitle="newCycle.name"
                                            ></v-list-item>
                                            <v-list-item
                                                title="Instrumentos"
                                                :subtitle="
                                                    newCycle.instruments
                                                        .length +
                                                    ' seleccionados'
                                                "
                                            ></v-list-item>
                                            <v-list-item
                                                title="IA Activada"
                                                :subtitle="
                                                    newCycle.evaluators.ai
                                                        ? 'Sí (Cerbero)'
                                                        : 'No'
                                                "
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
                                            @click="saveCycle"
                                        >
                                            Guardar Ciclo
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
