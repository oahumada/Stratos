<script setup lang="ts">
import axios from 'axios';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { computed, onMounted, ref } from 'vue';
import { usePermissions } from '../../composables/usePermissions';
import AppLayout from '../../layouts/AppLayout.vue';

const { can } = usePermissions();

interface PxCampaign {
    id: number;
    name: string;
    description: string | null;
    mode: 'agent_autonomous' | 'recurring' | 'specific_date';
    status: 'draft' | 'scheduled' | 'active' | 'paused' | 'completed';
    topics: string[];
    scope: { type: string; target_pct?: number; ids?: number[] };
    schedule_config: Record<string, any>;
    starts_at: string | null;
    ends_at: string | null;
    created_at: string;
}

const campaigns = ref<PxCampaign[]>([]);
const loading = ref(false);
const wizardDialog = ref(false);
const saving = ref(false);
const step = ref(1);

// Stats
const stats = computed(() => {
    return {
        active: campaigns.value.filter((c) => c.status === 'active').length,
        avg_engagement: 84, // Mock
        sentiment_index: 4.2, // Mock 1-5
        burnout_risk: 'Bajo', // Mock
    };
});

const newCampaign = ref<Partial<PxCampaign>>({
    name: '',
    description: '',
    mode: 'agent_autonomous',
    scope: { type: 'randomized_sample', target_pct: 20 },
    topics: ['clima', 'stress'],
    schedule_config: { frequency: 'monthly' },
    status: 'draft',
    starts_at: null,
    ends_at: null,
});

const modeOptions = [
    {
        title: 'IA Autónoma',
        value: 'agent_autonomous',
        icon: 'mdi-robot-confused',
        desc: 'Sentinel decide el mejor momento para preguntar.',
    },
    {
        title: 'Recurrente',
        value: 'recurring',
        icon: 'mdi-calendar-repeat',
        desc: 'Mediciones periódicas programadas.',
    },
    {
        title: 'Pulso Específico',
        value: 'specific_date',
        icon: 'mdi-target',
        desc: 'Envío masivo en una fecha y hora exacta.',
    },
];

const topicOptions = [
    {
        title: 'Clima Laboral',
        value: 'clima',
        icon: 'mdi-weather-partly-cloudy',
    },
    { title: 'Estrés & Burnout', value: 'stress', icon: 'mdi-fire-alert' },
    {
        title: 'Felicidad (eNPS)',
        value: 'happiness',
        icon: 'mdi-emoticon-happy-outline',
    },
    {
        title: 'Salud Ocupacional',
        value: 'health',
        icon: 'mdi-hospital-box-outline',
    },
    {
        title: 'Liderazgo & Apoyo',
        value: 'leadership',
        icon: 'mdi-account-tie',
    },
];

const scopeOptions = [
    { title: 'Muestra Aleatoria (IA)', value: 'randomized_sample' },
    { title: 'Población Total', value: 'all' },
    { title: 'Departamentos Críticos', value: 'department' },
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
        await axios.post('/api/px-campaigns', newCampaign.value);
        wizardDialog.value = false;
        resetWizard();
        loadCampaigns();
    } catch (e) {
        console.error('Failed to save px campaign', e);
    } finally {
        saving.value = false;
    }
};

const resetWizard = () => {
    step.value = 1;
    newCampaign.value = {
        name: '',
        description: '',
        mode: 'agent_autonomous',
        scope: { type: 'randomized_sample', target_pct: 20 },
        topics: ['clima', 'stress'],
        schedule_config: { frequency: 'monthly' },
        status: 'draft',
    };
};

const formatDate = (date: string | null) => {
    if (!date) return 'IA Decide';
    return format(new Date(date), 'dd MMM yyyy', { locale: es });
};

const getStatusColor = (status: string) => {
    const map: Record<string, string> = {
        draft: 'grey-lighten-1',
        scheduled: 'cyan-accent-3',
        active: 'success',
        paused: 'warning-accent-2',
        completed: 'indigo-accent-2',
    };
    return map[status] || 'grey';
};

onMounted(() => {
    loadCampaigns();
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
                        Estrategia
                        <span class="text-success">People Experience</span>
                    </h1>
                    <p class="text-h6 font-weight-regular text-grey-lighten-1">
                        Monitoreo preventivo de salud organizacional y
                        engagement.
                    </p>
                </div>

                <v-btn
                    v-if="can('assessments.manage')"
                    prepend-icon="mdi-brain"
                    variant="flat"
                    color="success"
                    height="56"
                    class="elevation-xl mt-md-0 mt-4 rounded-xl px-8"
                    @click="wizardDialog = true"
                >
                    Lanzar Campaña
                </v-btn>
            </div>

            <!-- Dashboard Stats Row -->
            <v-row class="mb-8">
                <v-col
                    cols="12"
                    sm="6"
                    md="3"
                    v-for="(val, key) in stats"
                    :key="key"
                >
                    <v-card
                        class="glass-card shadow-premium border-auth"
                        height="120"
                    >
                        <v-card-text class="d-flex align-center h-100">
                            <v-avatar
                                :color="
                                    key === 'burnout_risk'
                                        ? val === 'Bajo'
                                            ? 'success'
                                            : 'error'
                                        : 'success-lighten-4'
                                "
                                size="56"
                                class="mr-4 rounded-xl"
                            >
                                <v-icon
                                    :icon="
                                        key === 'active'
                                            ? 'mdi-pulse'
                                            : key === 'avg_engagement'
                                              ? 'mdi-heart'
                                              : key === 'sentiment_index'
                                                ? 'mdi-face-smile'
                                                : 'mdi-shield-alert'
                                    "
                                    :color="
                                        key === 'burnout_risk'
                                            ? 'white'
                                            : 'success'
                                    "
                                    size="32"
                                ></v-icon>
                            </v-avatar>
                            <div>
                                <div
                                    class="text-h4 font-weight-black text-white"
                                >
                                    {{ val
                                    }}{{ key === 'avg_engagement' ? '%' : '' }}
                                </div>
                                <div
                                    class="text-caption text-grey-lighten-1 text-uppercase font-weight-bold"
                                >
                                    {{ key.replace('_', ' ') }}
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
                                    color="success"
                                    class="mr-2"
                                ></v-icon>
                                Campañas Vigentes & Historial
                            </v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-text-field
                                placeholder="Buscar campaña..."
                                prepend-inner-icon="mdi-magnify"
                                variant="solo"
                                hide-details
                                density="compact"
                                class="max-width-300 mr-4"
                                rounded="lg"
                            ></v-text-field>
                        </v-toolbar>

                        <v-divider color="white" class="opacity-10"></v-divider>

                        <v-data-table
                            :headers="[
                                { title: 'Nombre de Campaña', key: 'name' },
                                { title: 'Tópicos', key: 'topics' },
                                { title: 'Despliegue', key: 'mode' },
                                {
                                    title: 'Impacto IA',
                                    key: 'impact',
                                    sortable: false,
                                },
                                { title: 'Estado', key: 'status' },
                                {
                                    title: '',
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
                                            'Prospección activa'
                                        }}
                                    </div>
                                </div>
                            </template>

                            <template #[`item.topics`]="{ item }">
                                <div class="d-flex flex-wrap gap-1">
                                    <v-chip
                                        v-for="t in item.topics"
                                        :key="t"
                                        size="x-small"
                                        color="success"
                                        variant="tonal"
                                        class="mr-1 mb-1"
                                    >
                                        {{
                                            topicOptions.find(
                                                (o) => o.value === t,
                                            )?.title || t
                                        }}
                                    </v-chip>
                                </div>
                            </template>

                            <template #[`item.mode`]="{ item }">
                                <v-chip
                                    size="small"
                                    variant="outlined"
                                    color="success-lighten-1"
                                >
                                    <v-icon
                                        start
                                        :icon="
                                            modeOptions.find(
                                                (o) => o.value === item.mode,
                                            )?.icon || 'mdi-broadcast'
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

                            <template #[`item.impact`]="{ item }">
                                <div
                                    class="d-flex align-center"
                                    style="min-width: 120px"
                                >
                                    <v-progress-linear
                                        :model-value="
                                            item.status === 'active' ? 75 : 0
                                        "
                                        color="success"
                                        height="6"
                                        rounded
                                        class="mr-3"
                                    ></v-progress-linear>
                                    <span class="text-caption text-white">{{
                                        item.status === 'active' ? '75%' : '0%'
                                    }}</span>
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

                            <template #[`item.actions`]="{ item }">
                                <v-btn
                                    icon="mdi-dots-vertical"
                                    variant="text"
                                    size="small"
                                    color="grey-lighten-1"
                                ></v-btn>
                                <v-btn
                                    v-if="item.status === 'active'"
                                    icon="mdi-chart-line"
                                    variant="tonal"
                                    size="small"
                                    color="success"
                                    class="ml-2"
                                ></v-btn>
                            </template>

                            <template #no-data>
                                <div class="pa-12 text-center">
                                    <v-icon
                                        icon="mdi-shield-check-outline"
                                        size="64"
                                        color="grey-darken-3"
                                        class="mb-4"
                                    ></v-icon>
                                    <p class="text-grey mb-4">
                                        No hay campañas configuradas para People
                                        Experience.
                                    </p>
                                    <v-btn
                                        variant="flat"
                                        color="success"
                                        prepend-icon="mdi-plus"
                                        @click="wizardDialog = true"
                                        >Lanzar Primera Campaña</v-btn
                                    >
                                </div>
                            </template>
                        </v-data-table>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- Premium Wizard PX -->
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
                        class="bg-success-darken-4 pa-6 d-flex flex-column border-right-auth"
                    >
                        <div class="mb-8">
                            <v-icon
                                icon="mdi-heart-pulse"
                                color="success"
                                size="32"
                                class="mb-2"
                            ></v-icon>
                            <h3
                                class="text-h5 font-weight-bold mb-1 text-white"
                            >
                                Diseño PX
                            </h3>
                            <p class="text-caption text-grey-lighten-1">
                                Configura la escucha activa de la organización.
                            </p>
                        </div>

                        <div class="flex-grow-1">
                            <v-list
                                bg-color="transparent"
                                density="compact"
                                nav
                                class="pa-0 v-list-wizard success-theme"
                            >
                                <v-list-item
                                    :active="step === 1"
                                    prepend-icon="mdi-numeric-1-circle"
                                    title="Tópicos & Objetivo"
                                    class="mb-2 rounded-lg"
                                    @click="step = 1"
                                ></v-list-item>
                                <v-list-item
                                    :active="step === 2"
                                    :disabled="step < 2"
                                    prepend-icon="mdi-numeric-2-circle"
                                    title="Frecuencia & Modo"
                                    class="mb-2 rounded-lg"
                                    @click="step = 2"
                                ></v-list-item>
                                <v-list-item
                                    :active="step === 3"
                                    :disabled="step < 3"
                                    prepend-icon="mdi-numeric-3-circle"
                                    title="Alcance (Scope)"
                                    class="mb-2 rounded-lg"
                                    @click="step = 3"
                                ></v-list-item>
                                <v-list-item
                                    :active="step === 4"
                                    :disabled="step < 4"
                                    prepend-icon="mdi-numeric-4-circle"
                                    title="Validación"
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
                            Cerrar
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
                            <!-- Step 1: Topics -->
                            <v-window v-model="step" class="h-100">
                                <v-window-item :value="1">
                                    <div class="mb-6">
                                        <div
                                            class="text-overline text-success font-weight-black mb-1"
                                        >
                                            PASO 1
                                        </div>
                                        <h2
                                            class="text-h4 font-weight-bold mb-2 text-white"
                                        >
                                            Foco de Medición
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            ¿Qué dimensiones de la experiencia
                                            humana queremos monitorear?
                                        </p>
                                    </div>

                                    <v-text-field
                                        v-model="newCampaign.name"
                                        label="Nombre de la Campaña"
                                        placeholder="Ej. Termómetro de Burnout 2026"
                                        variant="filled"
                                        color="success"
                                        bg-color="rgba(255,255,255,0.05)"
                                        class="mb-6 rounded-lg"
                                    ></v-text-field>

                                    <h3
                                        class="text-subtitle-1 font-weight-bold mb-4 text-white"
                                    >
                                        Dimensiones a Evaluar
                                    </h3>
                                    <v-row>
                                        <v-col
                                            cols="6"
                                            v-for="topic in topicOptions"
                                            :key="topic.value"
                                        >
                                            <v-card
                                                @click="
                                                    newCampaign.topics?.includes(
                                                        topic.value,
                                                    )
                                                        ? (newCampaign.topics =
                                                              newCampaign.topics.filter(
                                                                  (t) =>
                                                                      t !==
                                                                      topic.value,
                                                              ))
                                                        : newCampaign.topics?.push(
                                                              topic.value,
                                                          )
                                                "
                                                :class="[
                                                    'pa-4 border-auth cursor-pointer rounded-xl transition-all',
                                                    newCampaign.topics?.includes(
                                                        topic.value,
                                                    )
                                                        ? 'bg-success-darken-3 border-success elevation-xl'
                                                        : 'bg-white-opacity-5',
                                                ]"
                                                flat
                                            >
                                                <div
                                                    class="d-flex align-center"
                                                >
                                                    <v-avatar
                                                        :color="
                                                            newCampaign.topics?.includes(
                                                                topic.value,
                                                            )
                                                                ? 'success'
                                                                : 'grey-darken-3'
                                                        "
                                                        size="40"
                                                        class="mr-3"
                                                    >
                                                        <v-icon
                                                            :icon="topic.icon"
                                                            size="20"
                                                        ></v-icon>
                                                    </v-avatar>
                                                    <div
                                                        class="text-subtitle-1 font-weight-bold text-white"
                                                    >
                                                        {{ topic.title }}
                                                    </div>
                                                </div>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- Step 2: Mode & Frequency -->
                                <v-window-item :value="2">
                                    <div class="mb-6">
                                        <div
                                            class="text-overline text-success font-weight-black mb-1"
                                        >
                                            PASO 2
                                        </div>
                                        <h2
                                            class="text-h4 font-weight-bold mb-2 text-white"
                                        >
                                            Motor de Distribución
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            Define el nivel de autonomía de la
                                            IA para capturar el sentimiento
                                            organizacional.
                                        </p>
                                    </div>

                                    <v-row class="mb-6">
                                        <v-col
                                            cols="12"
                                            v-for="mode in modeOptions"
                                            :key="mode.value"
                                        >
                                            <v-card
                                                @click="
                                                    newCampaign.mode =
                                                        mode.value as any
                                                "
                                                :class="[
                                                    'pa-4 border-auth cursor-pointer rounded-xl transition-all',
                                                    newCampaign.mode ===
                                                    mode.value
                                                        ? 'bg-success-darken-3 border-success elevation-xl'
                                                        : 'bg-white-opacity-5',
                                                ]"
                                                flat
                                            >
                                                <div
                                                    class="d-flex align-center"
                                                >
                                                    <v-avatar
                                                        :color="
                                                            newCampaign.mode ===
                                                            mode.value
                                                                ? 'success'
                                                                : 'grey-darken-3'
                                                        "
                                                        size="48"
                                                        class="mr-4"
                                                    >
                                                        <v-icon
                                                            :icon="mode.icon"
                                                            size="24"
                                                        ></v-icon>
                                                    </v-avatar>
                                                    <div class="flex-grow-1">
                                                        <div
                                                            class="text-h6 font-weight-bold text-white"
                                                        >
                                                            {{ mode.title }}
                                                        </div>
                                                        <div
                                                            class="text-caption text-grey-lighten-1"
                                                        >
                                                            {{ mode.desc }}
                                                        </div>
                                                    </div>
                                                    <v-icon
                                                        v-if="
                                                            newCampaign.mode ===
                                                            mode.value
                                                        "
                                                        icon="mdi-check-circle"
                                                        color="success"
                                                    ></v-icon>
                                                </div>
                                            </v-card>
                                        </v-col>
                                    </v-row>

                                    <v-alert
                                        v-if="
                                            newCampaign.mode ===
                                            'agent_autonomous'
                                        "
                                        type="info"
                                        variant="tonal"
                                        class="border-success rounded-xl opacity-90"
                                    >
                                        <strong
                                            >Inteligencia Sentinel
                                            Activa:</strong
                                        >
                                        La IA analizará la carga de trabajo de
                                        cada usuario y lanzará las
                                        micro-encuestas solo cuando detecte una
                                        ventana de baja fricción emocional.
                                    </v-alert>
                                </v-window-item>

                                <!-- Step 3: Scope -->
                                <v-window-item :value="3">
                                    <div class="mb-6">
                                        <div
                                            class="text-overline text-success font-weight-black mb-1"
                                        >
                                            PASO 3
                                        </div>
                                        <h2
                                            class="text-h4 font-weight-bold mb-2 text-white"
                                        >
                                            Alcance de Prospección
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            Configura a qué porcentaje de la
                                            población debe impactar esta
                                            campaña.
                                        </p>
                                    </div>

                                    <v-select
                                        v-model="newCampaign.scope!.type"
                                        label="Segmentación de Muestra"
                                        :items="scopeOptions"
                                        variant="filled"
                                        bg-color="rgba(255,255,255,0.05)"
                                        class="mb-6 rounded-lg"
                                    ></v-select>

                                    <div
                                        class="bg-white-opacity-5 border-auth pa-8 rounded-xl text-center"
                                        v-if="
                                            newCampaign.scope!.type ===
                                            'randomized_sample'
                                        "
                                    >
                                        <div
                                            class="text-h2 font-weight-black mb-2 text-white"
                                        >
                                            {{ newCampaign.scope!.target_pct }}%
                                        </div>
                                        <div
                                            class="text-subtitle-1 text-grey mb-6"
                                        >
                                            Tráfico aleatorio diario por usuario
                                        </div>
                                        <v-slider
                                            v-model="
                                                newCampaign.scope!.target_pct
                                            "
                                            min="5"
                                            max="100"
                                            step="5"
                                            color="success"
                                            thumb-label
                                            class="mx-4"
                                        ></v-slider>
                                        <p
                                            class="text-caption text-grey-lighten-1"
                                        >
                                            Una muestra del
                                            {{ newCampaign.scope!.target_pct }}%
                                            garantiza anonimato y reduce la
                                            fatiga de encuesta.
                                        </p>
                                    </div>
                                </v-window-item>

                                <!-- Step 4: Confirmation -->
                                <v-window-item :value="4">
                                    <div class="mb-8 text-center">
                                        <v-avatar
                                            color="success"
                                            size="80"
                                            class="elevation-xl mb-4"
                                        >
                                            <v-icon
                                                icon="mdi-shield-check"
                                                size="48"
                                                color="white"
                                            ></v-icon>
                                        </v-avatar>
                                        <h2
                                            class="text-h4 font-weight-bold mb-2 text-white"
                                        >
                                            Campaña Lista
                                        </h2>
                                        <p
                                            class="text-body-1 text-grey-lighten-1"
                                        >
                                            Revisa el ADN de la campaña antes
                                            del despliegue en Sentinel.
                                        </p>
                                    </div>

                                    <v-card
                                        class="bg-white-opacity-5 border-auth mb-6 overflow-hidden rounded-xl"
                                    >
                                        <v-list
                                            bg-color="transparent"
                                            class="pa-0"
                                        >
                                            <v-list-item class="px-6 py-4">
                                                <template #prepend
                                                    ><v-icon
                                                        icon="mdi-tag-text-outline"
                                                        color="success"
                                                    ></v-icon
                                                ></template>
                                                <v-list-item-title
                                                    class="font-weight-bold text-white"
                                                    >{{
                                                        newCampaign.name
                                                    }}</v-list-item-title
                                                >
                                                <v-list-item-subtitle
                                                    class="text-grey"
                                                    >Nombre de la
                                                    Campaña</v-list-item-subtitle
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
                                                        class="px-6 py-4"
                                                    >
                                                        <v-list-item-title
                                                            class="font-weight-bold text-white"
                                                            >{{
                                                                modeOptions.find(
                                                                    (o) =>
                                                                        o.value ===
                                                                        newCampaign.mode,
                                                                )?.title
                                                            }}</v-list-item-title
                                                        >
                                                        <v-list-item-subtitle
                                                            class="text-grey"
                                                            >Distribución</v-list-item-subtitle
                                                        >
                                                    </v-list-item>
                                                </v-col>
                                                <v-col cols="6" class="pa-0">
                                                    <v-list-item
                                                        class="px-6 py-4"
                                                    >
                                                        <v-list-item-title
                                                            class="font-weight-bold text-white"
                                                            >{{
                                                                newCampaign
                                                                    .topics
                                                                    ?.length
                                                            }}
                                                            Dimensiones</v-list-item-title
                                                        >
                                                        <v-list-item-subtitle
                                                            class="text-grey"
                                                            >Métricas</v-list-item-subtitle
                                                        >
                                                    </v-list-item>
                                                </v-col>
                                            </v-row>
                                        </v-list>
                                    </v-card>

                                    <v-alert
                                        type="success"
                                        variant="tonal"
                                        border="start"
                                        class="rounded-xl"
                                        icon="mdi-robot"
                                    >
                                        Sentinel monitoreará el tráfico y
                                        enviará notificaciones preventivas si
                                        detecta riesgos de burnout en los
                                        departamentos seleccionados.
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
                                color="success"
                                height="48"
                                class="rounded-lg px-8"
                                append-icon="mdi-chevron-right"
                                @click="step++"
                                >Siguiente Paso</v-btn
                            >

                            <v-btn
                                v-else
                                color="success"
                                height="48"
                                class="shadow-premium rounded-lg px-8"
                                :loading="saving"
                                prepend-icon="mdi-check-decagram"
                                @click="saveCampaign"
                                >Establecer Campaña</v-btn
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
    background: rgba(30, 36, 30, 0.8) !important;
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
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3) !important;
}

.tracking-tight {
    letter-spacing: -0.02em;
}

.v-list-wizard.success-theme .v-list-item--active {
    background: rgba(var(--v-theme-success), 0.15) !important;
    color: rgb(var(--v-theme-success)) !important;
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

.bg-success-darken-4 {
    background-color: #0d1a11 !important;
}

.bg-surface-dark {
    background-color: #161c18 !important;
}

.gap-1 {
    gap: 4px;
}
</style>
