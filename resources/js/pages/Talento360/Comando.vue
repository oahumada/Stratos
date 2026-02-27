<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import moment from 'moment';
import 'moment/locale/es';
import { computed, onMounted, ref } from 'vue';
import { usePermissions } from '../../composables/usePermissions';
import AppLayout from '../../layouts/AppLayout.vue';

moment.locale('es');

const { can } = usePermissions();

interface AssessmentCycle {
    id: number;
    name: string;
    description: string | null;
    mode: 'specific_date' | 'quarterly' | 'annual' | 'continuous';
    status: 'draft' | 'scheduled' | 'active' | 'completed' | 'cancelled';
    schedule_config: Record<string, any>;
    scope: { type: string; ids: number[] };
    evaluators: {
        self: boolean;
        manager: boolean;
        peers: number;
        reports: boolean;
        ai: boolean;
    };
    instruments: string[];
    starts_at: string | null;
    ends_at: string | null;
    created_at: string;
}

const cycles = ref<AssessmentCycle[]>([]);
const loading = ref(false);
const wizardDialog = ref(false);
const saving = ref(false);
const step = ref(1);

// Stats
const stats = computed(() => {
    return {
        active: cycles.value.filter((c) => c.status === 'active').length,
        total_participants: 1240, // Mock for now
        avg_completion: 68, // Mock
        pending_evals: 450, // Mock
    };
});

const newCycle = ref<
    Partial<AssessmentCycle> & { notifications?: Record<string, boolean> }
>({
    name: '',
    description: '',
    mode: 'quarterly',
    scope: { type: 'all', ids: [] },
    evaluators: {
        self: true,
        manager: true,
        peers: 2,
        reports: true,
        ai: true,
    },
    instruments: ['bars', 'pulse'],
    schedule_config: { quarter: 1, year: 2026 },
    notifications: { email: true, in_app: true },
    status: 'draft',
    starts_at: null,
    ends_at: null,
});

const modeOptions = [
    {
        title: 'Fecha Específica',
        value: 'specific_date',
        icon: 'mdi-calendar-star',
        desc: 'Lanzamiento único en una fecha puntual.',
    },
    {
        title: 'Trimestral',
        value: 'quarterly',
        icon: 'mdi-calendar-range',
        desc: 'Ciclos de medición cada 3 meses.',
    },
    {
        title: 'Anual (Aniversario)',
        value: 'annual',
        icon: 'mdi-cake-variant',
        desc: 'Se dispara en la fecha de ingreso de cada colaborador.',
    },
    {
        title: 'Continuo Aleatorio',
        value: 'continuous',
        icon: 'mdi-infinity',
        desc: 'La IA decide cuándo medir para no saturar.',
    },
];

const instrumentOptions = [
    {
        title: 'Evaluación BARS (Competencias)',
        value: 'bars',
        icon: 'mdi-chart-bar',
    },
    {
        title: 'Encuesta Pulse (Clima)',
        value: 'pulse',
        icon: 'mdi-heart-pulse',
    },
    { title: 'Perfil Psicométrico (DISC)', value: 'disc', icon: 'mdi-brain' },
    {
        title: 'Entrevista de IA (Cerbero)',
        value: 'interview',
        icon: 'mdi-robot',
    },
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
        await axios.post('/api/assessment-cycles', newCycle.value);
        wizardDialog.value = false;
        resetWizard();
        loadCycles();
    } catch (e) {
        console.error('Failed to save cycle', e);
    } finally {
        saving.value = false;
    }
};

const activateCycle = async (id: number) => {
    loading.value = true;
    try {
        await axios.put(`/api/assessment-cycles/${id}`, { status: 'active' });
        loadCycles();
    } catch (e) {
        console.error('Failed to activate cycle', e);
    } finally {
        loading.value = false;
    }
};

const resetWizard = () => {
    step.value = 1;
    newCycle.value = {
        name: '',
        description: '',
        mode: 'quarterly',
        scope: { type: 'all', ids: [] },
        evaluators: {
            self: true,
            manager: true,
            peers: 2,
            reports: true,
            ai: true,
        },
        instruments: ['bars', 'pulse'],
        schedule_config: { quarter: 1, year: 2026 },
        notifications: { email: true, in_app: true },
        status: 'draft',
    };
};

const previewLoading = ref(false);
const previewData = ref({
    participants: 0,
    impacted_areas: 0,
});

watch(step, (newStep) => {
    if (newStep === 4) {
        previewLoading.value = true;
        setTimeout(() => {
            const scopeType = newCycle.value.scope?.type;
            if (scopeType === 'all') {
                previewData.value = { participants: 184, impacted_areas: 6 };
            } else if (scopeType === 'scenario') {
                previewData.value = { participants: 42, impacted_areas: 2 };
            } else if (scopeType === 'department') {
                previewData.value = { participants: 18, impacted_areas: 1 };
            } else {
                previewData.value = { participants: 8, impacted_areas: 1 };
            }
            previewLoading.value = false;
        }, 1500);
    }
});

const formatDate = (date: string | null) => {
    if (!date) return 'Sin definir';
    return moment(date).format('DD MMM YYYY');
};

const getStatusColor = (status: string) => {
    const map: Record<string, string> = {
        draft: 'grey-lighten-1',
        scheduled: 'cyan-accent-3',
        active: 'green-accent-3',
        completed: 'indigo-accent-2',
        cancelled: 'red-accent-2',
    };
    return map[status] || 'grey';
};

onMounted(() => {
    loadCycles();
});
</script>

<template>
    <AppLayout>
        <v-container fluid class="pa-0 pa-md-6 min-vh-100">
            <!-- Header Section -->
            <div
                class="d-flex flex-column flex-md-row align-start align-md-center justify-space-between px-md-0 mb-8 px-4"
            >
                <div>
                    <h1
                        class="text-h3 font-weight-black mb-2 tracking-tight text-white"
                    >
                        Unidad de Comando
                        <span class="text-primary">Cerbero</span>
                    </h1>
                    <p class="text-h6 font-weight-regular text-grey-lighten-1">
                        Orquestación inteligente de ciclos de assessment y
                        talento 360.
                    </p>
                </div>

                <v-btn
                    v-if="can('assessments.manage')"
                    prepend-icon="mdi-rocket-launch"
                    variant="flat"
                    color="primary"
                    height="56"
                    class="elevation-xl mt-md-0 mt-4 rounded-xl px-8"
                    @click="wizardDialog = true"
                >
                    Configurar Ciclo
                </v-btn>
            </div>

            <!-- Stats Dashboard Row -->
            <v-row class="mb-8">
                <v-col cols="12" sm="6" md="3">
                    <v-card
                        class="glass-card shadow-premium border-auth"
                        height="120"
                    >
                        <v-card-text class="d-flex align-center h-100">
                            <v-avatar
                                color="primary-lighten-4"
                                size="56"
                                class="mr-4 rounded-xl"
                            >
                                <v-icon
                                    icon="mdi-sync"
                                    color="primary"
                                    size="32"
                                ></v-icon>
                            </v-avatar>
                            <div>
                                <div
                                    class="text-h4 font-weight-black text-white"
                                >
                                    {{ stats.active }}
                                </div>
                                <div
                                    class="text-caption text-grey-lighten-1 text-uppercase font-weight-bold"
                                >
                                    Ciclos Activos
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <v-card
                        class="glass-card shadow-premium border-auth"
                        height="120"
                    >
                        <v-card-text class="d-flex align-center h-100">
                            <v-avatar
                                color="success-lighten-4"
                                size="56"
                                class="mr-4 rounded-xl"
                            >
                                <v-icon
                                    icon="mdi-account-group"
                                    color="success"
                                    size="32"
                                ></v-icon>
                            </v-avatar>
                            <div>
                                <div
                                    class="text-h4 font-weight-black text-white"
                                >
                                    {{ stats.total_participants }}
                                </div>
                                <div
                                    class="text-caption text-grey-lighten-1 text-uppercase font-weight-bold"
                                >
                                    Colaboradores
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <v-card
                        class="glass-card shadow-premium border-auth"
                        height="120"
                    >
                        <v-card-text class="d-flex align-center h-100">
                            <v-avatar
                                color="info-lighten-4"
                                size="56"
                                class="mr-4 rounded-xl"
                            >
                                <v-icon
                                    icon="mdi-chart-donut"
                                    color="info"
                                    size="32"
                                ></v-icon>
                            </v-avatar>
                            <div class="flex-grow-1">
                                <div
                                    class="d-flex align-end justify-space-between"
                                >
                                    <div
                                        class="text-h4 font-weight-black text-white"
                                    >
                                        {{ stats.avg_completion }}%
                                    </div>
                                </div>
                                <div
                                    class="text-caption text-grey-lighten-1 text-uppercase font-weight-bold"
                                >
                                    Completitud Avg.
                                </div>
                                <v-progress-linear
                                    :model-value="stats.avg_completion"
                                    color="info"
                                    height="4"
                                    rounded
                                    class="mt-1"
                                ></v-progress-linear>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                    <v-card
                        class="glass-card shadow-premium border-auth"
                        height="120"
                    >
                        <v-card-text class="d-flex align-center h-100">
                            <v-avatar
                                color="warning-lighten-4"
                                size="56"
                                class="mr-4 rounded-xl"
                            >
                                <v-icon
                                    icon="mdi-clock-alert-outline"
                                    color="warning"
                                    size="32"
                                ></v-icon>
                            </v-avatar>
                            <div>
                                <div
                                    class="text-h4 font-weight-black text-white"
                                >
                                    {{ stats.pending_evals }}
                                </div>
                                <div
                                    class="text-caption text-grey-lighten-1 text-uppercase font-weight-bold"
                                >
                                    Evals. Pendientes
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Main Listing Row -->
            <v-row>
                <v-col cols="12">
                    <v-card
                        class="glass-card shadow-premium border-auth overflow-hidden"
                        elevation="0"
                    >
                        <v-toolbar color="transparent" class="px-4 py-2">
                            <v-toolbar-title
                                class="text-h6 font-weight-bold text-white"
                            >
                                <v-icon
                                    icon="mdi-history"
                                    color="primary"
                                    class="mr-2"
                                ></v-icon>
                                Historial de Orquestaciones
                            </v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-text-field
                                placeholder="Buscar ciclo..."
                                prepend-inner-icon="mdi-magnify"
                                variant="solo"
                                hide-details
                                density="compact"
                                class="max-width-300 mr-4"
                                rounded="lg"
                            ></v-text-field>
                            <v-btn
                                icon="mdi-filter-variant"
                                variant="text"
                                color="grey"
                            ></v-btn>
                        </v-toolbar>

                        <v-divider color="white" class="opacity-10"></v-divider>

                        <v-data-table
                            :headers="[
                                {
                                    title: 'Identificación del Ciclo',
                                    key: 'name',
                                },
                                { title: 'Modalidad', key: 'mode' },
                                {
                                    title: 'Progreso',
                                    key: 'progress',
                                    sortable: false,
                                },
                                { title: 'Estado', key: 'status' },
                                { title: 'Lanzamiento', key: 'starts_at' },
                                {
                                    title: '',
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
                            <template #[`item.name`]="{ item }">
                                <div class="py-4">
                                    <div
                                        class="text-body-1 font-weight-bold text-white"
                                    >
                                        {{ item.name }}
                                    </div>
                                    <div
                                        class="text-caption text-grey-lighten-1"
                                    >
                                        {{
                                            item.description ||
                                            'Sin descripción'
                                        }}
                                    </div>
                                </div>
                            </template>

                            <template #[`item.mode`]="{ item }">
                                <v-chip
                                    size="small"
                                    variant="outlined"
                                    color="primary-lighten-1"
                                    class="text-caption"
                                >
                                    <v-icon
                                        start
                                        :icon="
                                            modeOptions.find(
                                                (o) => o.value === item.mode,
                                            )?.icon || 'mdi-calendar'
                                        "
                                        size="14"
                                    ></v-icon>
                                    {{
                                        modeOptions.find(
                                            (o) => o.value === item.mode,
                                        )?.title || item.mode
                                    }}
                                </v-chip>
                            </template>

                            <template #[`item.progress`]="{ item }">
                                <div
                                    class="d-flex align-center"
                                    style="min-width: 150px"
                                >
                                    <v-progress-linear
                                        :model-value="item.completion_rate || 0"
                                        color="success"
                                        height="6"
                                        rounded
                                        class="mr-3"
                                    ></v-progress-linear>
                                    <span
                                        class="text-caption font-weight-bold text-white"
                                    >
                                        {{ item.completion_rate || 0 }}%
                                    </span>
                                </div>
                            </template>

                            <template #[`item.status`]="{ item }">
                                <v-chip
                                    :color="getStatusColor(item.status)"
                                    size="small"
                                    variant="flat"
                                    class="text-uppercase font-weight-black text-black"
                                >
                                    {{ item.status }}
                                </v-chip>
                            </template>

                            <template #[`item.starts_at`]="{ item }">
                                <div class="text-caption">
                                    <v-icon
                                        icon="mdi-calendar"
                                        size="14"
                                        class="mr-1"
                                    ></v-icon>
                                    {{ formatDate(item.starts_at) }}
                                </div>
                            </template>

                            <template #[`item.actions`]="{ item }">
                                <v-btn
                                    v-if="
                                        item.status === 'draft' ||
                                        item.status === 'scheduled'
                                    "
                                    icon="mdi-rocket"
                                    variant="tonal"
                                    size="small"
                                    color="success"
                                    class="mr-2"
                                    @click="
                                        activateCycle(
                                            (item as AssessmentCycle).id,
                                        )
                                    "
                                    title="Activar Ciclo Oficialmente"
                                ></v-btn>
                                <v-btn
                                    v-if="item.status === 'active'"
                                    icon="mdi-chart-box-outline"
                                    variant="tonal"
                                    size="small"
                                    color="primary"
                                    class="mr-2"
                                    title="Ver Seguimiento Dashboard"
                                    @click="
                                        router.visit('/talento360/dashboard')
                                    "
                                ></v-btn>
                                <v-btn
                                    icon="mdi-dots-vertical"
                                    variant="text"
                                    size="small"
                                    color="grey-lighten-1"
                                ></v-btn>
                            </template>

                            <template #no-data>
                                <div class="pa-12 text-center">
                                    <v-btn
                                        variant="text"
                                        color="primary"
                                        prepend-icon="mdi-plus"
                                        @click="wizardDialog = true"
                                        >Lanzar primer ciclo</v-btn
                                    >
                                </div>
                            </template>
                        </v-data-table>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- Premium Wizard -->
        <v-dialog
            v-model="wizardDialog"
            max-width="1000"
            persistent
            transition="dialog-bottom-transition"
        >
            <v-card class="glass-card border-auth overflow-hidden" height="700">
                <v-row class="ma-0 h-100">
                    <!-- Sidebar Navigation -->
                    <v-col
                        cols="3"
                        class="bg-primary-darken-4 pa-6 d-flex flex-column border-right-auth"
                    >
                        <div class="mb-8">
                            <v-icon
                                icon="mdi-rocket-launch"
                                color="primary"
                                size="32"
                                class="mb-2"
                            ></v-icon>
                            <h3
                                class="text-h5 font-weight-bold mb-1 text-white"
                            >
                                Configuración
                            </h3>
                            <p class="text-caption text-grey-lighten-1">
                                Define el ADN de tu ciclo de evaluación.
                            </p>
                        </div>

                        <div class="flex-grow-1">
                            <v-list
                                bg-color="transparent"
                                density="compact"
                                nav
                                class="pa-0 v-list-wizard"
                            >
                                <v-list-item
                                    :active="step === 1"
                                    prepend-icon="mdi-numeric-1-circle"
                                    title="Identidad & Tipo"
                                    class="mb-2 rounded-lg"
                                    @click="step = 1"
                                ></v-list-item>
                                <v-list-item
                                    :active="step === 2"
                                    :disabled="step < 2"
                                    prepend-icon="mdi-numeric-2-circle"
                                    title="Población & Alcance"
                                    class="mb-2 rounded-lg"
                                    @click="step = 2"
                                ></v-list-item>
                                <v-list-item
                                    :active="step === 3"
                                    :disabled="step < 3"
                                    prepend-icon="mdi-numeric-3-circle"
                                    title="Instrumentos & Red"
                                    class="mb-2 rounded-lg"
                                    @click="step = 3"
                                ></v-list-item>
                                <v-list-item
                                    :active="step === 4"
                                    :disabled="step < 4"
                                    prepend-icon="mdi-numeric-4-circle"
                                    title="Confirmación"
                                    class="mb-2 rounded-lg"
                                    @click="step = 4"
                                ></v-list-item>
                            </v-list>
                        </div>

                        <v-btn
                            block
                            variant="tonal"
                            color="white"
                            class="rounded-lg"
                            @click="wizardDialog = false"
                        >
                            Cancelar
                        </v-btn>
                    </v-col>

                    <!-- Content Area -->
                    <v-col
                        cols="9"
                        class="pa-0 d-flex flex-column bg-surface-dark h-100"
                    >
                        <v-card-text
                            class="pa-8 flex-grow-1 overflow-y-auto pb-0"
                        >
                            <!-- Step 1: Identity -->
                            <v-window v-model="step" class="h-100">
                                <v-window-item :value="1">
                                    <div class="mb-6">
                                        <div
                                            class="text-overline font-weight-black mb-1 text-primary"
                                        >
                                            PASO 1
                                        </div>
                                        <h2
                                            class="text-h4 font-weight-bold mb-2 text-white"
                                        >
                                            Identidad del Ciclo
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            Nombra este ciclo y selecciona cómo
                                            la IA debe disparar las
                                            evaluaciones.
                                        </p>
                                    </div>

                                    <v-text-field
                                        v-model="newCycle.name"
                                        label="Nombre del Ciclo"
                                        placeholder="Ej. Evaluación de Potencial Q1 2026"
                                        variant="filled"
                                        color="primary"
                                        bg-color="rgba(255,255,255,0.05)"
                                        class="mb-4 rounded-lg"
                                    ></v-text-field>

                                    <v-textarea
                                        v-model="newCycle.description"
                                        label="Descripción / Objetivo"
                                        placeholder="Breve explicación del propósito de esta medición..."
                                        variant="filled"
                                        color="primary"
                                        bg-color="rgba(255,255,255,0.05)"
                                        rows="3"
                                        class="mb-6 rounded-lg"
                                    ></v-textarea>

                                    <h3
                                        class="text-subtitle-1 font-weight-bold mb-4 text-white"
                                    >
                                        Modalidad de Disparo
                                    </h3>
                                    <v-row>
                                        <v-col
                                            cols="6"
                                            v-for="mode in modeOptions"
                                            :key="mode.value"
                                        >
                                            <v-card
                                                @click="
                                                    newCycle.mode =
                                                        mode.value as any
                                                "
                                                :class="[
                                                    'pa-4 border-auth cursor-pointer rounded-xl transition-all',
                                                    newCycle.mode === mode.value
                                                        ? 'bg-primary-darken-3 elevation-xl border-primary'
                                                        : 'bg-white-opacity-5',
                                                ]"
                                                flat
                                            >
                                                <div
                                                    class="d-flex align-center mb-2"
                                                >
                                                    <v-avatar
                                                        :color="
                                                            newCycle.mode ===
                                                            mode.value
                                                                ? 'primary'
                                                                : 'grey-darken-3'
                                                        "
                                                        size="40"
                                                        class="mr-3"
                                                    >
                                                        <v-icon
                                                            :icon="mode.icon"
                                                            size="20"
                                                        ></v-icon>
                                                    </v-avatar>
                                                    <div
                                                        class="text-subtitle-1 font-weight-bold text-white"
                                                    >
                                                        {{ mode.title }}
                                                    </div>
                                                </div>
                                                <div
                                                    class="text-caption text-grey-lighten-1"
                                                >
                                                    {{ mode.desc }}
                                                </div>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- Step 2: Scope -->
                                <v-window-item :value="2">
                                    <div class="mb-6">
                                        <div
                                            class="text-overline font-weight-black mb-1 text-primary"
                                        >
                                            PASO 2
                                        </div>
                                        <h2
                                            class="text-h4 font-weight-bold mb-2 text-white"
                                        >
                                            Población & Alcance
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            ¿A quiénes queremos evaluar en esta
                                            ocasión?
                                        </p>
                                    </div>

                                    <v-select
                                        v-model="newCycle.scope!.type"
                                        label="Segmentación de Población"
                                        :items="[
                                            {
                                                title: 'Toda la Organización',
                                                value: 'all',
                                            },
                                            {
                                                title: 'Por Departamento',
                                                value: 'department',
                                            },
                                            {
                                                title: 'Por Escenario Estratégico',
                                                value: 'scenario',
                                            },
                                            {
                                                title: 'Solo High Potentials (HiPo)',
                                                value: 'hipo',
                                            },
                                        ]"
                                        variant="filled"
                                        bg-color="rgba(255,255,255,0.05)"
                                        class="mb-6 rounded-lg"
                                    ></v-select>

                                    <v-alert
                                        v-if="
                                            newCycle.scope!.type === 'scenario'
                                        "
                                        type="info"
                                        variant="tonal"
                                        border="start"
                                        icon="mdi-brain"
                                        class="mb-6 rounded-lg"
                                    >
                                        Esta opción sincroniza el ciclo con los
                                        escenarios de Scenario IQ, evaluando
                                        solo a los roles impactados.
                                    </v-alert>

                                    <h3
                                        class="text-subtitle-1 font-weight-bold mb-4 text-white"
                                    >
                                        Cronograma Proyectado
                                    </h3>
                                    <v-row>
                                        <v-col cols="6">
                                            <v-text-field
                                                v-model="newCycle.starts_at"
                                                label="Fecha de Inicio"
                                                type="date"
                                                variant="filled"
                                                bg-color="rgba(255,255,255,0.05)"
                                                class="rounded-lg"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="6">
                                            <v-text-field
                                                v-model="newCycle.ends_at"
                                                label="Fecha de Cierre"
                                                type="date"
                                                variant="filled"
                                                bg-color="rgba(255,255,255,0.05)"
                                                class="rounded-lg"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- Step 3: Instruments & Red -->
                                <v-window-item :value="3">
                                    <div class="mb-6">
                                        <div
                                            class="text-overline font-weight-black mb-1 text-primary"
                                        >
                                            PASO 3
                                        </div>
                                        <h2
                                            class="text-h4 font-weight-bold mb-2 text-white"
                                        >
                                            Instrumentos & Red 360
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            Configura las herramientas de
                                            medición y quiénes participarán como
                                            evaluadores.
                                        </p>
                                    </div>

                                    <h3
                                        class="text-subtitle-1 font-weight-bold mb-3 text-white"
                                    >
                                        Modelos de Medición
                                    </h3>
                                    <v-row class="mb-6">
                                        <v-col
                                            cols="6"
                                            v-for="inst in instrumentOptions"
                                            :key="inst.value"
                                        >
                                            <v-checkbox
                                                v-model="newCycle.instruments"
                                                :value="inst.value"
                                                :label="inst.title"
                                                color="primary"
                                                hide-details
                                                class="pa-2 bg-white-opacity-5 border-auth rounded-lg"
                                            >
                                                <template #label>
                                                    <div
                                                        class="d-flex align-center"
                                                    >
                                                        <v-icon
                                                            :icon="inst.icon"
                                                            color="primary"
                                                            class="mr-2"
                                                            size="18"
                                                        ></v-icon>
                                                        <span
                                                            class="text-white"
                                                            >{{
                                                                inst.title
                                                            }}</span
                                                        >
                                                    </div>
                                                </template>
                                            </v-checkbox>
                                        </v-col>
                                    </v-row>

                                    <h3
                                        class="text-subtitle-1 font-weight-bold mb-3 text-white"
                                    >
                                        Red de Feedback (Vistas)
                                    </h3>
                                    <v-card
                                        class="bg-white-opacity-5 border-auth pa-4 rounded-xl"
                                    >
                                        <v-row>
                                            <v-col cols="6" md="4">
                                                <v-switch
                                                    v-model="
                                                        newCycle.evaluators!
                                                            .self
                                                    "
                                                    label="Autoevaluación"
                                                    color="primary"
                                                    density="compact"
                                                    hide-details
                                                ></v-switch>
                                            </v-col>
                                            <v-col cols="6" md="4">
                                                <v-switch
                                                    v-model="
                                                        newCycle.evaluators!
                                                            .manager
                                                    "
                                                    label="Jefe Directo"
                                                    color="primary"
                                                    density="compact"
                                                    hide-details
                                                ></v-switch>
                                            </v-col>
                                            <v-col cols="6" md="4">
                                                <v-switch
                                                    v-model="
                                                        newCycle.evaluators!
                                                            .reports
                                                    "
                                                    label="Reportes Directos"
                                                    color="primary"
                                                    density="compact"
                                                    hide-details
                                                ></v-switch>
                                            </v-col>
                                            <v-col cols="6" md="4">
                                                <v-switch
                                                    v-model="
                                                        newCycle.evaluators!.ai
                                                    "
                                                    label="IA Cerbero"
                                                    color="primary"
                                                    density="compact"
                                                    hide-details
                                                ></v-switch>
                                            </v-col>
                                        </v-row>
                                        <v-divider
                                            class="my-4 opacity-10"
                                        ></v-divider>
                                        <div
                                            class="d-flex align-center justify-space-between"
                                        >
                                            <div class="text-body-2 text-white">
                                                Nº de Pares aleatorios por
                                                persona
                                            </div>
                                            <div style="width: 200px">
                                                <v-slider
                                                    v-model="
                                                        newCycle.evaluators!
                                                            .peers
                                                    "
                                                    min="0"
                                                    max="8"
                                                    step="1"
                                                    thumb-label
                                                    hide-details
                                                    color="primary"
                                                ></v-slider>
                                            </div>
                                        </div>
                                    </v-card>
                                </v-window-item>

                                <!-- Step 4: Confirmation -->
                                <v-window-item :value="4">
                                    <div class="mb-8 text-center">
                                        <v-avatar
                                            color="primary"
                                            size="80"
                                            class="elevation-xl mb-4"
                                        >
                                            <v-icon
                                                icon="mdi-check-all"
                                                size="48"
                                                color="white"
                                            ></v-icon>
                                        </v-avatar>
                                        <h2
                                            class="text-h4 font-weight-bold mb-2 text-white"
                                        >
                                            Resumen de Lanzamiento
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            Revisa la configuración final antes
                                            de registrar el ciclo en el sistema.
                                        </p>
                                    </div>

                                    <v-card
                                        class="bg-white-opacity-5 border-auth mb-6 overflow-hidden rounded-xl"
                                    >
                                        <v-list
                                            bg-color="transparent"
                                            class="pa-0"
                                        >
                                            <v-list-item class="px-6 py-3">
                                                <template #prepend
                                                    ><v-icon
                                                        icon="mdi-tag-outline"
                                                        color="primary"
                                                    ></v-icon
                                                ></template>
                                                <v-list-item-title
                                                    class="font-weight-bold text-white"
                                                    >{{
                                                        newCycle.name
                                                    }}</v-list-item-title
                                                >
                                                <v-list-item-subtitle
                                                    class="text-grey"
                                                    >Nombre del
                                                    Ciclo</v-list-item-subtitle
                                                >
                                            </v-list-item>
                                            <v-divider
                                                class="opacity-10"
                                            ></v-divider>
                                            <v-row class="ma-0">
                                                <v-col
                                                    cols="6"
                                                    class="pa-0 border-right-auth"
                                                >
                                                    <v-list-item
                                                        class="px-6 py-3"
                                                    >
                                                        <v-list-item-title
                                                            class="font-weight-bold text-white"
                                                            >{{
                                                                modeOptions.find(
                                                                    (o) =>
                                                                        o.value ===
                                                                        newCycle.mode,
                                                                )?.title
                                                            }}</v-list-item-title
                                                        >
                                                        <v-list-item-subtitle
                                                            class="text-grey"
                                                            >Modalidad</v-list-item-subtitle
                                                        >
                                                    </v-list-item>
                                                </v-col>
                                                <v-col cols="6" class="pa-0">
                                                    <v-list-item
                                                        class="px-6 py-3"
                                                    >
                                                        <v-list-item-title
                                                            class="font-weight-bold text-white"
                                                            >{{
                                                                newCycle
                                                                    .instruments
                                                                    ?.length
                                                            }}
                                                            Seleccionados</v-list-item-title
                                                        >
                                                        <v-list-item-subtitle
                                                            class="text-grey"
                                                            >Instrumentos</v-list-item-subtitle
                                                        >
                                                    </v-list-item>
                                                </v-col>
                                            </v-row>
                                            <v-divider
                                                class="opacity-10"
                                            ></v-divider>
                                            <v-list-item class="px-6 py-3">
                                                <template #prepend
                                                    ><v-icon
                                                        icon="mdi-robot-outline"
                                                        :color="
                                                            newCycle.evaluators
                                                                ?.ai
                                                                ? 'success'
                                                                : 'grey'
                                                        "
                                                    ></v-icon
                                                ></template>
                                                <v-list-item-title
                                                    :class="
                                                        newCycle.evaluators?.ai
                                                            ? 'text-success font-weight-bold'
                                                            : 'text-grey'
                                                    "
                                                >
                                                    {{
                                                        newCycle.evaluators?.ai
                                                            ? 'Inteligencia Artificial Activada'
                                                            : 'IA Desactivada'
                                                    }}
                                                </v-list-item-title>
                                                <v-list-item-subtitle
                                                    class="text-grey"
                                                    >Motor
                                                    Cerbero</v-list-item-subtitle
                                                >
                                            </v-list-item>
                                        </v-list>
                                    </v-card>

                                    <!-- Preview Section Start -->
                                    <h3
                                        class="text-subtitle-1 font-weight-bold mb-4 text-white"
                                    >
                                        Preview de Alcance Estimado
                                    </h3>

                                    <v-card
                                        class="bg-primary-darken-3 border-auth pa-6 mb-8 rounded-xl text-center"
                                        flat
                                    >
                                        <div v-if="previewLoading" class="py-4">
                                            <v-progress-circular
                                                indeterminate
                                                color="primary"
                                                class="mb-3"
                                            ></v-progress-circular>
                                            <div
                                                class="text-caption text-grey-lighten-1"
                                            >
                                                Cerbero está calculando el
                                                universo de participantes...
                                            </div>
                                        </div>
                                        <v-row v-else>
                                            <v-col
                                                cols="6"
                                                class="border-right-auth"
                                            >
                                                <div
                                                    class="text-h3 font-weight-black mb-1 text-white"
                                                >
                                                    {{
                                                        previewData.participants
                                                    }}
                                                </div>
                                                <div
                                                    class="text-caption text-indigo-lighten-2 text-uppercase font-weight-bold"
                                                >
                                                    Participantes Target
                                                </div>
                                            </v-col>
                                            <v-col cols="6">
                                                <div
                                                    class="text-h3 font-weight-black mb-1 text-white"
                                                >
                                                    {{
                                                        previewData.impacted_areas
                                                    }}
                                                </div>
                                                <div
                                                    class="text-caption text-indigo-lighten-2 text-uppercase font-weight-bold"
                                                >
                                                    Áreas / Redes 360
                                                </div>
                                            </v-col>
                                        </v-row>
                                    </v-card>
                                    <!-- Preview Section End -->

                                    <v-alert
                                        type="warning"
                                        variant="tonal"
                                        border="start"
                                        class="mb-6 rounded-xl"
                                    >
                                        El ciclo se guardará como
                                        <strong>Borrador</strong>. Podrás editar
                                        el alcance detallado de personas antes
                                        de lanzarlo oficialmente.
                                    </v-alert>
                                </v-window-item>
                            </v-window>
                        </v-card-text>

                        <!-- Footer Actions -->
                        <v-divider color="white" class="opacity-10"></v-divider>
                        <div
                            class="pa-8 d-flex justify-space-between align-center"
                        >
                            <v-btn
                                v-if="step > 1"
                                variant="text"
                                prepend-icon="mdi-chevron-left"
                                color="grey-lighten-1"
                                @click="step--"
                                >Regresar</v-btn
                            >
                            <div v-else></div>

                            <v-btn
                                v-if="step < 4"
                                color="primary"
                                height="48"
                                class="rounded-lg px-8"
                                append-icon="mdi-chevron-right"
                                @click="step++"
                                >Continuar</v-btn
                            >

                            <v-btn
                                v-else
                                color="success"
                                height="48"
                                class="rounded-lg px-8"
                                :loading="saving"
                                prepend-icon="mdi-check-circle"
                                @click="saveCycle"
                                >Lanzar Configuración</v-btn
                            >
                        </div>
                    </v-col>
                </v-row>
            </v-card>
        </v-dialog>
    </AppLayout>
</template>

<style scoped>
.glass-card {
    background: rgba(30, 30, 36, 0.8) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
}

.border-auth {
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.border-right-auth {
    border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.shadow-premium {
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2) !important;
}

.tracking-tight {
    letter-spacing: -0.02em;
}

.v-list-wizard .v-list-item--active {
    background: rgba(var(--v-theme-primary), 0.15) !important;
    color: rgb(var(--v-theme-primary)) !important;
}

.bg-white-opacity-5 {
    background-color: rgba(255, 255, 255, 0.03) !important;
}

.transition-all {
    transition: all 0.3s ease;
}

.max-width-300 {
    max-width: 300px;
}

.bg-primary-darken-4 {
    background-color: #0d1117 !important;
}

.bg-surface-dark {
    background-color: #161b22 !important;
}
</style>
