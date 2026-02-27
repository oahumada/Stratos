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

                    <v-card-text class="pa-6 flex-grow-1">
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
                            >Configurar</v-btn
                        >
                        <v-spacer></v-spacer>
                        <v-btn
                            variant="tonal"
                            size="small"
                            class="text-none px-4"
                            color="primary"
                            @click="handleShowLogs(agent)"
                            >Ver Logs</v-btn
                        >
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
                        Stratos evoluciona hacia una orquestación total: el
                        <strong>Estratega</strong> y el
                        <strong>Simulador</strong> diseñan el futuro orgánico,
                        mientras el <strong>Matchmaker</strong> y el
                        <strong>Sentinel</strong> aseguran la resonancia y
                        transparencia del talento.
                    </p>
                    <div class="bg-opacity-10 pa-3 mb-4 rounded-lg bg-white">
                        <div class="text-caption font-weight-bold mb-1">
                            MISIONES ACTUALES:
                        </div>
                        <div class="text-caption">
                            • Paso 1: Escenario IQ (Simulación Orgánica)
                        </div>
                        <div class="text-caption">
                            • Paso 2: Diseño de Talento (Cubo de Roles)
                        </div>
                        <div class="text-caption">
                            • Paso 3: Selección por Resonancia ADN
                        </div>
                        <div class="text-caption">
                            • Paso 4: Audit Trail & Integridad (Sentinel)
                        </div>
                    </div>
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

        <AgentConfigurationModal
            :visible="showConfigModal"
            :agent="selectedAgent"
            :loading="savingConfig"
            @close="showConfigModal = false"
            @save="handleSaveConfig"
        />

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

const agents = ref<Agent[]>([]);
const loading = ref(false);
const showConfigModal = ref(false);
const selectedAgent = ref<Agent | null>(null);
const savingConfig = ref(false);
const showSuccessSnackbar = ref(false);
const successMessage = ref('');

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

const handleShowLogs = (agent: Agent) => {
    successMessage.value = `Mostrando logs recientes para ${agent.name} (Simulado)`;
    showSuccessSnackbar.value = true;
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

<style scoped>
.italic {
    font-style: italic;
}
</style>
