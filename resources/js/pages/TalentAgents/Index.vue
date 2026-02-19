<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <v-container
            fluid
            class="pa-6 bg-grey-lighten-4 fill-height align-start"
        >
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
                            <v-avatar
                                size="64"
                                class="elevation-2 border-xl mb-4"
                                color="white"
                            >
                                <v-icon
                                    :icon="getAgentIcon(agent.name)"
                                    :color="getAgentColor(agent.name)"
                                    size="32"
                                ></v-icon>
                            </v-avatar>
                            <div class="text-h6 font-weight-bold">
                                {{ agent.name }}
                            </div>
                            <div class="text-subtitle-2 opacity-80">
                                {{ agent.role_description }}
                            </div>

                            <v-chip
                                size="x-small"
                                color="white"
                                variant="flat"
                                class="position-absolute"
                                style="top: 24px; right: 24px"
                            >
                                <v-icon
                                    start
                                    icon="mdi-circle"
                                    size="8"
                                    color="success"
                                ></v-icon>
                                ACTIVO
                            </v-chip>
                        </div>

                        <v-card-text class="pa-6 flex-grow-1">
                            <div class="text-body-2 font-weight-bold mb-2">
                                Persona:
                            </div>
                            <p
                                class="text-caption text-grey-darken-1 mb-4 italic"
                            >
                                "{{ agent.persona }}"
                            </p>

                            <div class="text-body-2 font-weight-bold mb-2">
                                Áreas de Expertise:
                            </div>
                            <div class="d-flex mb-4 flex-wrap gap-1">
                                <v-chip
                                    v-for="area in agent.expertise_areas"
                                    :key="area"
                                    size="x-small"
                                    variant="tonal"
                                    class="mr-1 mb-1"
                                >
                                    {{ formatExpertise(area) }}
                                </v-chip>
                            </div>

                            <div
                                class="bg-grey-lighten-4 pa-3 mt-auto rounded-lg"
                            >
                                <div
                                    class="d-flex justify-space-between text-caption mb-1"
                                >
                                    <span class="text-grey font-weight-medium"
                                        >Modelo:</span
                                    >
                                    <span class="font-weight-bold">{{
                                        agent.model
                                    }}</span>
                                </div>
                                <div
                                    class="d-flex justify-space-between text-caption"
                                >
                                    <span class="text-grey font-weight-medium"
                                        >Proveedor:</span
                                    >
                                    <span
                                        class="font-weight-bold text-uppercase"
                                        >{{ agent.provider }}</span
                                    >
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
                                >Configurar</v-btn
                            >
                            <v-spacer></v-spacer>
                            <v-btn
                                variant="tonal"
                                size="small"
                                class="text-none px-4"
                                color="primary"
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
                        <div
                            class="d-flex justify-space-between text-caption mt-2"
                        >
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
                            Visión Multi-Agente
                        </h3>
                        <p class="text-body-2 mb-4 opacity-80">
                            Stratos orquestará pronto flujos donde el
                            <strong>Estratega</strong> diseña el plan y el
                            <strong>Coach</strong> lo ejecuta, trabajando en
                            paralelo.
                        </p>
                        <v-btn
                            color="white"
                            variant="flat"
                            block
                            class="text-none mt-auto"
                            size="small"
                        >
                            Explorar LangGraph Vision
                        </v-btn>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Talent Agents', href: '/talent-agents' },
];

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
