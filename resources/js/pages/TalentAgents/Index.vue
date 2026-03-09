<script setup lang="ts">
import AgentConfigurationModal from '@/components/TalentAgents/AgentConfigurationModal.vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';

interface Agent {
    id: number;
    name: string;
    role_description: string;
    persona: string;
    expertise_areas: string[];
    model: string;
    provider: string;
    is_active: boolean;
}

interface AgentLog {
    timestamp: string;
    type: 'THINK' | 'ACTION' | 'RESULT' | 'ERROR';
    event: string;
    details: string;
}

const agents = ref<Agent[]>([]);
const loading = ref(false);
const showConfigModal = ref(false);
const selectedAgent = ref<Agent | null>(null);
const savingConfig = ref(false);
const showSuccessSnackbar = ref(false);
const successMessage = ref('');

// Logs State
const showLogsModal = ref(false);
const loadingLogs = ref(false);
const agentLogs = ref<AgentLog[]>([]);

const fetchAgents = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/agents');
        agents.value = response.data.data;
    } catch (e) {
        console.error('Error fetching agents', e);
    } finally {
        loading.value = false;
    }
};

const openConfig = (agent: Agent) => {
    selectedAgent.value = agent;
    showConfigModal.value = true;
};

const handleSaveConfig = async (agentData: any) => {
    savingConfig.value = true;
    try {
        await axios.put(`/api/agents/${agentData.id}`, agentData);
        await fetchAgents();
        showConfigModal.value = false;
        successMessage.value = 'Configuración guardada correctamente';
        showSuccessSnackbar.value = true;
    } catch (e) {
        console.error('Error saving agent config', e);
    } finally {
        savingConfig.value = false;
    }
};

const handleShowLogs = async (agent: Agent) => {
    selectedAgent.value = agent;
    showLogsModal.value = true;
    loadingLogs.value = true;

    // Simular fetch de logs reales (en el futuro esto vendría de /api/agents/{id}/logs)
    setTimeout(() => {
        agentLogs.value = [
            {
                timestamp: new Date().toLocaleTimeString(),
                type: 'THINK',
                event: 'Análisis de Brechas Estratégicas',
                details: `Iniciando escaneo de competencias para el escenario actual. Detectando desalineación del 15% en roles técnicos.`,
            },
            {
                timestamp: new Date().toLocaleTimeString(),
                type: 'ACTION',
                event: 'Consulta a Base de Conocimiento',
                details:
                    'Buscando patrones de éxito en el sector tecnológico para el rol de Cloud Architect.',
            },
            {
                timestamp: new Date().toLocaleTimeString(),
                type: 'RESULT',
                event: 'Generación de Propuesta',
                details:
                    'Propuesta de enriquecimiento para 3 roles activos enviada al orquestador.',
            },
            {
                timestamp: new Date().toLocaleTimeString(),
                type: 'THINK',
                event: 'Consistencia de Catálogo',
                details:
                    'Verificando que las nuevas competencias no dupliquen conceptos existentes en el diccionario de la organización.',
            },
        ];
        loadingLogs.value = false;
    }, 800);
};

const getLogColor = (type: string) => {
    switch (type) {
        case 'THINK':
            return 'indigo';
        case 'ACTION':
            return 'orange';
        case 'RESULT':
            return 'success';
        case 'ERROR':
            return 'error';
        default:
            return 'grey';
    }
};

const getAgentHeaderClass = (agent: any) => {
    if (agent.name.includes('Estratega'))
        return 'bg-blue-lighten-4 text-blue-darken-4';
    if (agent.name.includes('Navegador'))
        return 'bg-green-lighten-4 text-green-darken-4';
    if (agent.name.includes('Coach'))
        return 'bg-purple-lighten-4 text-purple-darken-4';
    if (agent.name.includes('Orquestador'))
        return 'bg-amber-lighten-4 text-amber-darken-4';
    if (agent.name.includes('Selector'))
        return 'bg-cyan-lighten-4 text-cyan-darken-4';
    if (agent.name.includes('Arquitecto'))
        return 'bg-teal-lighten-4 text-teal-darken-4';
    if (agent.name.includes('Curador'))
        return 'bg-indigo-lighten-4 text-indigo-darken-4';
    if (agent.name.includes('Diseñador'))
        return 'bg-blue-grey-lighten-4 text-blue-grey-darken-4';
    if (agent.name.includes('Simulador'))
        return 'bg-deep-orange-lighten-4 text-deep-orange-darken-4';
    if (agent.name.includes('Sentinel'))
        return 'bg-red-lighten-4 text-red-darken-4';
    if (agent.name.includes('Matchmaker'))
        return 'bg-pink-lighten-4 text-pink-darken-4';
    return 'bg-grey-lighten-3';
};

const getAgentIcon = (name: string) => {
    if (name.includes('Estratega')) return 'mdi-strategy';
    if (name.includes('Navegador')) return 'mdi-compass-rose';
    if (name.includes('Coach')) return 'mdi-school-outline';
    if (name.includes('Orquestador')) return 'mdi-account-search-outline';
    if (name.includes('Selector')) return 'mdi-briefcase-account-outline';
    if (name.includes('Arquitecto')) return 'mdi-school';
    if (name.includes('Curador')) return 'mdi-book-open-page-variant-outline';
    if (name.includes('Diseñador')) return 'mdi-vector-arrange-below';
    if (name.includes('Simulador')) return 'mdi-molecule';
    if (name.includes('Sentinel')) return 'mdi-shield-eye-outline';
    if (name.includes('Matchmaker')) return 'mdi-heart-flash';
    return 'mdi-robot';
};

const getAgentColor = (name: string) => {
    if (name.includes('Estratega')) return 'blue-darken-2';
    if (name.includes('Navegador')) return 'green-darken-2';
    if (name.includes('Coach')) return 'purple-darken-2';
    if (name.includes('Orquestador')) return 'amber-darken-3';
    if (name.includes('Selector')) return 'cyan-darken-3';
    if (name.includes('Arquitecto')) return 'teal-darken-3';
    if (name.includes('Curador')) return 'indigo-darken-3';
    if (name.includes('Diseñador')) return 'blue-grey-darken-3';
    if (name.includes('Simulador')) return 'deep-orange-darken-4';
    if (name.includes('Sentinel')) return 'red-darken-4';
    if (name.includes('Matchmaker')) return 'pink-darken-4';
    return 'grey-darken-2';
};

const formatExpertise = (area: string) => {
    return area
        .split('_')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

onMounted(fetchAgents);
</script>

<template>
    <v-container fluid class="pa-6 bg-grey-lighten-4 fill-height align-start">
        <v-row>
            <v-col cols="12">
                <div class="d-flex align-center mb-6">
                    <div>
                        <h1 class="text-h4 font-weight-bold mb-1">
                            Talent Agents Hub
                        </h1>
                        <p class="text-subtitle-1 text-grey-darken-1">
                            Gestión de la fuerza laboral digital y agentes
                            especializados.
                        </p>
                    </div>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="primary"
                        prepend-icon="mdi-robot-outline"
                        class="text-none"
                        disabled
                    >
                        Entrenar Nuevo Agente
                    </v-btn>
                </div>
            </v-col>

            <!-- Featured System Agents -->
            <v-col v-for="agent in agents" :key="agent.id" cols="12" md="4">
                <v-card
                    class="fill-height d-flex flex-column overflow-hidden rounded-xl"
                    elevation="1"
                >
                    <div
                        :class="['pa-6', getAgentHeaderClass(agent)]"
                        style="position: relative"
                    >
                        <div class="d-flex align-center mb-4">
                            <v-avatar
                                :color="getAgentColor(agent.name)"
                                size="56"
                                class="elevation-3 mr-4 rounded-xl"
                            >
                                <v-icon
                                    size="32"
                                    color="white"
                                    :icon="getAgentIcon(agent.name)"
                                ></v-icon>
                            </v-avatar>
                            <div>
                                <h3 class="text-h6 font-weight-bold">
                                    {{ agent.name }}
                                </h3>
                                <v-chip
                                    size="x-small"
                                    color="success"
                                    variant="flat"
                                    class="font-weight-bold"
                                >
                                    <v-icon
                                        start
                                        icon="mdi-circle"
                                        size="8"
                                    ></v-icon>
                                    ACTIVO
                                </v-chip>
                            </div>
                            <v-spacer></v-spacer>
                            <v-btn
                                icon="mdi-dots-vertical"
                                variant="text"
                                size="small"
                            ></v-btn>
                        </div>
                        <div
                            class="text-body-2 font-weight-medium line-clamp-2"
                            style="height: 40px"
                        >
                            {{ agent.role_description }}
                        </div>
                    </div>

                    <v-card-text class="pa-6 grow">
                        <div class="mb-4">
                            <div class="text-caption text-grey mb-1">
                                CORE EXPERTISE
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <v-chip
                                    v-for="area in agent.expertise_areas"
                                    :key="area"
                                    size="x-small"
                                    variant="tonal"
                                    class="mt-1 mr-1"
                                >
                                    {{ formatExpertise(area) }}
                                </v-chip>
                            </div>
                        </div>

                        <div class="d-flex align-center mt-auto">
                            <div class="mr-4">
                                <div class="text-caption text-grey">
                                    BRAIN ENGINE
                                </div>
                                <div class="text-body-2 font-weight-bold">
                                    {{ agent.model }}
                                </div>
                            </div>
                            <div>
                                <div class="text-caption text-grey">
                                    PROVIDER
                                </div>
                                <span class="font-weight-bold text-uppercase">{{
                                    agent.provider
                                }}</span>
                            </div>
                        </div>
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions class="pa-4 bg-grey-lighten-5">
                        <v-btn
                            variant="text"
                            size="small"
                            class="text-none"
                            color="primary"
                            @click="openConfig(agent)"
                        >
                            Configurar
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn
                            variant="tonal"
                            size="small"
                            class="text-none px-4"
                            color="primary"
                            @click="handleShowLogs(agent)"
                        >
                            Ver Logs
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>

            <!-- AI System Metrics -->
            <v-col cols="12" md="8">
                <v-card class="pa-6 rounded-xl" elevation="1">
                    <h3 class="text-h6 font-weight-bold mb-4">
                        Uso de API & Performance
                    </h3>
                    <v-row>
                        <v-col cols="4">
                            <div class="text-caption text-grey mb-1">
                                Tokens Consumidos (Hoy)
                            </div>
                            <div class="text-h6">124.5k</div>
                        </v-col>
                        <v-col cols="4">
                            <div class="text-caption text-grey mb-1">
                                Latencia Promedio
                            </div>
                            <div class="text-h6 text-success">1.2s</div>
                        </v-col>
                        <v-col cols="4">
                            <div class="text-caption text-grey mb-1">
                                Éxito en Tareas
                            </div>
                            <div class="text-h6 text-primary">98.2%</div>
                        </v-col>
                    </v-row>
                    <v-divider class="my-4"></v-divider>
                    <v-progress-linear
                        model-value="65"
                        color="primary"
                        height="10"
                        rounded
                        class="mt-2"
                    ></v-progress-linear>
                    <div class="d-flex justify-space-between text-caption mt-2">
                        <span>Cuota Mensual Utilizada</span>
                        <span class="font-weight-bold"
                            >65% ($42.30 / $100.00)</span
                        >
                    </div>
                </v-card>
            </v-col>

            <v-col cols="12" md="4">
                <v-card
                    class="pa-6 fill-height rounded-xl bg-primary text-white"
                    elevation="2"
                >
                    <v-icon
                        icon="mdi-head-snowflake-outline"
                        size="48"
                        class="mb-4"
                    ></v-icon>
                    <h3 class="text-h6 font-weight-bold mb-2">
                        Orquestación Multi-Agente
                    </h3>
                    <p class="text-body-2 mb-4 opacity-80">
                        Los agentes consumen tokens de reflexión antes de emitir
                        cualquier propuesta estratégica para asegurar la
                        coherencia sistémica.
                    </p>
                    <v-btn
                        color="white"
                        variant="flat"
                        block
                        class="text-none mt-auto"
                        size="small"
                        to="/docs/technical/multi_agent_talent_design"
                    >
                        Leer Documentación Técnica
                    </v-btn>
                </v-card>
            </v-col>
        </v-row>

        <!-- Components Modals -->
        <AgentConfigurationModal
            v-if="showConfigModal"
            :visible="showConfigModal"
            :agent="selectedAgent"
            :loading="savingConfig"
            @close="showConfigModal = false"
            @save="handleSaveConfig"
        />

        <v-dialog v-model="showLogsModal" max-width="800px" scrollable>
            <v-card class="overflow-hidden rounded-xl">
                <v-toolbar color="grey-darken-4" theme="dark" flat>
                    <v-icon start class="ml-4">mdi-console-line</v-icon>
                    <v-toolbar-title class="font-weight-bold">
                        Logs de Pensamiento: {{ selectedAgent?.name }}
                    </v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="showLogsModal = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-toolbar>

                <v-card-text class="pa-0 bg-grey-darken-4 text-grey-lighten-2">
                    <div
                        class="pa-6 text-body-2 custom-logs-container font-mono"
                    >
                        <div
                            v-if="loadingLogs"
                            class="d-flex flex-column align-center justify-center py-12"
                        >
                            <v-progress-circular
                                indeterminate
                                color="primary"
                                class="mb-4"
                            ></v-progress-circular>
                            <span>Sincronizando con el motor neural...</span>
                        </div>
                        <div
                            v-else-if="agentLogs.length === 0"
                            class="py-12 text-center"
                        >
                            <v-icon size="48" color="grey-darken-2" class="mb-4"
                                >mdi-database-off</v-icon
                            >
                            <p>No hay logs recientes para este agente.</p>
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="(log, i) in agentLogs"
                                :key="i"
                                class="mb-6 border-l-2 border-primary pl-4"
                            >
                                <div
                                    class="d-flex align-center mb-1"
                                    style="gap: 8px"
                                >
                                    <span
                                        class="font-weight-bold text-caption text-primary"
                                        >[{{ log.timestamp }}]</span
                                    >
                                    <v-chip
                                        size="x-small"
                                        :color="getLogColor(log.type)"
                                        variant="flat"
                                        class="font-weight-bold"
                                    >
                                        {{ log.type }}
                                    </v-chip>
                                </div>
                                <div class="font-weight-bold mb-2 text-white">
                                    {{ log.event }}
                                </div>
                                <div
                                    class="text-grey-lighten-1 italic opacity-80"
                                >
                                    {{ log.details }}
                                </div>
                            </div>
                        </div>
                    </div>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-snackbar
            v-model="showSuccessSnackbar"
            color="success"
            rounded="pill"
            elevation="24"
        >
            {{ successMessage }}
            <template #actions>
                <v-btn variant="text" @click="showSuccessSnackbar = false"
                    >Cerrar</v-btn
                >
            </template>
        </v-snackbar>
    </v-container>
</template>

<style scoped>
.custom-logs-container {
    max-height: 500px;
    overflow-y: auto;
}
.space-y-4 > * + * {
    margin-top: 1rem;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
