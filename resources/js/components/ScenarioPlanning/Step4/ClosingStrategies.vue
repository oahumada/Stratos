<template>
    <div class="closing-strategies">
        <v-card class="elevation-1 mb-6">
            <v-card-title class="d-flex align-center px-6 py-4">
                <v-icon color="primary" class="mr-3">mdi-strategy</v-icon>
                <span class="text-h6 font-weight-bold"
                    >Estrategias de Cierre de Brechas</span
                >
                <v-spacer></v-spacer>
                <v-btn
                    color="primary"
                    variant="flat"
                    @click="generateStrategies"
                    :loading="loading"
                    prepend-icon="mdi-auto-fix"
                >
                    Generar con IA
                </v-btn>
            </v-card-title>
            <v-card-text class="px-6 pb-6">
                <p class="text-body-2 text-medium-emphasis mb-4">
                    Basado en las brechas identificadas en el Paso 3, el sistema
                    propone estrategias de tipo
                    <strong>Build</strong> (capacitación),
                    <strong>Buy</strong> (contratación),
                    <strong>Borrow</strong> (externos) o
                    <strong>Bot</strong> (automatización).
                </p>

                <v-alert
                    v-if="strategies.length === 0 && !loading"
                    type="info"
                    variant="tonal"
                    icon="mdi-information"
                >
                    No se han generado estrategias para este escenario aún. Haga
                    clic en el botón superior para iniciar el análisis.
                </v-alert>

                <v-row v-else>
                    <v-col
                        v-for="strategy in strategies"
                        :key="strategy.id"
                        cols="12"
                        md="6"
                    >
                        <v-card variant="outlined" class="strategy-card h-100">
                            <v-card-item>
                                <template v-slot:prepend>
                                    <v-avatar
                                        :color="
                                            getStrategyColor(strategy.strategy)
                                        "
                                        size="40"
                                    >
                                        <v-icon color="white">{{
                                            getStrategyIcon(strategy.strategy)
                                        }}</v-icon>
                                    </v-avatar>
                                </template>
                                <v-card-title>{{
                                    strategy.strategy_name
                                }}</v-card-title>
                                <v-card-subtitle>{{
                                    strategy.skill_name ||
                                    'Habilidad del Escenario'
                                }}</v-card-subtitle>
                            </v-card-item>

                            <v-card-text>
                                <div class="text-body-2 mb-3">
                                    {{ strategy.description }}
                                </div>

                                <!-- Mix de Talento (Blueprint) -->
                                <div v-if="strategy.blueprint" class="mb-4">
                                    <div
                                        class="d-flex justify-space-between text-caption mb-1"
                                    >
                                        <span class="font-weight-bold"
                                            >Mix de Talento ({{
                                                strategy.role_name
                                            }})</span
                                        >
                                        <span
                                            >{{
                                                strategy.blueprint
                                                    .human_leverage
                                            }}% H /
                                            {{
                                                strategy.blueprint
                                                    .synthetic_leverage
                                            }}% S</span
                                        >
                                    </div>
                                    <v-progress-linear
                                        height="8"
                                        rounded
                                        :model-value="
                                            strategy.blueprint.human_leverage
                                        "
                                        color="indigo-darken-1"
                                        bg-color="purple-lighten-2"
                                        bg-opacity="1"
                                    >
                                    </v-progress-linear>
                                </div>

                                <div class="d-flex flex-wrap gap-2">
                                    <v-chip size="x-small" label>
                                        <v-icon start size="12"
                                            >mdi-currency-usd</v-icon
                                        >
                                        Est. Costo:
                                        {{
                                            formatCurrency(
                                                strategy.estimated_cost,
                                            )
                                        }}
                                    </v-chip>
                                    <v-chip size="x-small" label>
                                        <v-icon start size="12"
                                            >mdi-clock-outline</v-icon
                                        >
                                        {{ strategy.estimated_time_weeks }}
                                        semanas
                                    </v-chip>
                                    <v-chip
                                        size="x-small"
                                        label
                                        :color="
                                            getRiskColor(strategy.risk_level)
                                        "
                                    >
                                        Riesgo: {{ strategy.risk_level }}
                                    </v-chip>
                                </div>
                            </v-card-text>

                            <v-divider></v-divider>
                            <v-card-actions>
                                <v-btn
                                    variant="text"
                                    size="small"
                                    :color="getStrategyColor(strategy.strategy)"
                                >
                                    Ver Detalle
                                </v-btn>
                                <v-spacer></v-spacer>
                                <v-chip
                                    size="x-small"
                                    :color="getStatusColor(strategy.status)"
                                >
                                    {{ strategy.status }}
                                </v-chip>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>

                <!-- Planes de Sucesión -->
                <div v-if="successionPlans.length > 0" class="mt-8">
                    <v-divider class="mb-6"></v-divider>
                    <h3
                        class="text-h6 font-weight-bold d-flex align-center mb-4"
                    >
                        <v-icon color="amber-darken-2" class="mr-2"
                            >mdi-account-switch</v-icon
                        >
                        Planes de Sucesión (Roles Críticos)
                    </h3>
                    <v-row>
                        <v-col
                            v-for="plan in successionPlans"
                            :key="plan.role_name"
                            cols="12"
                        >
                            <v-card
                                variant="tonal"
                                border
                                color="amber-lighten-5"
                            >
                                <v-card-item>
                                    <v-card-title class="text-amber-darken-4">{{
                                        plan.role_name
                                    }}</v-card-title>
                                    <v-card-subtitle
                                        >Continuidad
                                        Estratégica</v-card-subtitle
                                    >
                                </v-card-item>
                                <v-card-text>
                                    <v-table
                                        density="compact"
                                        class="bg-transparent"
                                    >
                                        <thead>
                                            <tr>
                                                <th scope="col">Prioridad</th>
                                                <th scope="col">Sucesor</th>
                                                <th scope="col">
                                                    Disponibilidad
                                                </th>
                                                <th scope="col">Readiness</th>
                                                <th scope="col">
                                                    Timeline Est.
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="plan.primary_successor">
                                                <td>
                                                    <v-chip
                                                        size="x-small"
                                                        color="success"
                                                        >Primario</v-chip
                                                    >
                                                </td>
                                                <td>
                                                    {{
                                                        plan.primary_successor
                                                            .name
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        plan.primary_successor
                                                            .availability
                                                    }}
                                                </td>
                                                <td>
                                                    <v-progress-linear
                                                        :model-value="
                                                            plan
                                                                .primary_successor
                                                                .readiness_percentage
                                                        "
                                                        color="success"
                                                        height="10"
                                                        rounded
                                                    >
                                                        <template
                                                            v-slot:default="{
                                                                value,
                                                            }"
                                                        >
                                                            <span
                                                                class="text-caption"
                                                                >{{
                                                                    Math.ceil(
                                                                        value,
                                                                    )
                                                                }}%</span
                                                            >
                                                        </template>
                                                    </v-progress-linear>
                                                </td>
                                                <td>
                                                    {{
                                                        plan.primary_successor
                                                            .timeline
                                                    }}
                                                </td>
                                            </tr>
                                            <tr v-if="plan.secondary_successor">
                                                <td>
                                                    <v-chip
                                                        size="x-small"
                                                        color="info"
                                                        >Secundario</v-chip
                                                    >
                                                </td>
                                                <td>
                                                    {{
                                                        plan.secondary_successor
                                                            .name
                                                    }}
                                                </td>
                                                <td>
                                                    {{
                                                        plan.secondary_successor
                                                            .availability
                                                    }}
                                                </td>
                                                <td>
                                                    <v-progress-linear
                                                        :model-value="
                                                            plan
                                                                .secondary_successor
                                                                .readiness_percentage
                                                        "
                                                        color="info"
                                                        height="10"
                                                        rounded
                                                    >
                                                        <template
                                                            v-slot:default="{
                                                                value,
                                                            }"
                                                        >
                                                            <span
                                                                class="text-caption"
                                                                >{{
                                                                    Math.ceil(
                                                                        value,
                                                                    )
                                                                }}%</span
                                                            >
                                                        </template>
                                                    </v-progress-linear>
                                                </td>
                                                <td>
                                                    {{
                                                        plan.secondary_successor
                                                            .timeline
                                                    }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </v-table>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>

<script setup lang="ts">
import { useNotification } from '@/composables/useNotification';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const props = defineProps({
    scenarioId: {
        type: [Number, String],
        required: true,
    },
});

const strategies = ref<any[]>([]);
const successionPlans = ref<any[]>([]);
const loading = ref(false);
const { showSuccess, showError } = useNotification();

const loadStrategies = async () => {
    loading.value = true;
    try {
        const [stratRes, succRes] = await Promise.all([
            axios.get(
                `/api/strategic-planning/scenarios/${props.scenarioId}/strategies`,
            ),
            axios.get(
                `/api/scenarios/${props.scenarioId}/step2/succession-plans`,
            ),
        ]);
        strategies.value = stratRes.data.data || [];
        successionPlans.value = succRes.data.data || [];
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const generateStrategies = async () => {
    loading.value = true;
    try {
        const response = await axios.post(
            `/api/strategic-planning/scenarios/${props.scenarioId}/refresh-suggested-strategies`,
        );
        if (response.data.success) {
            showSuccess('Estrategias generadas exitosamente');
            await loadStrategies();
        }
    } catch (e) {
        console.error(e);
        showError('Error al generar las estrategias');
    } finally {
        loading.value = false;
    }
};

const getStrategyColor = (type: string | null) => {
    switch (type?.toLowerCase()) {
        case 'build':
            return 'indigo';
        case 'buy':
            return 'emerald';
        case 'borrow':
            return 'orange';
        case 'bot':
            return 'purple';
        default:
            return 'grey';
    }
};

const getStrategyIcon = (type: string | null) => {
    switch (type?.toLowerCase()) {
        case 'build':
            return 'mdi-school';
        case 'buy':
            return 'mdi-account-plus';
        case 'borrow':
            return 'mdi-handshake';
        case 'bot':
            return 'mdi-robot';
        default:
            return 'mdi-help-circle';
    }
};

const getStatusColor = (status: string | null) => {
    switch (status?.toLowerCase()) {
        case 'proposed':
            return 'amber';
        case 'approved':
            return 'success';
        case 'rejected':
            return 'error';
        default:
            return 'grey';
    }
};

const getRiskColor = (risk: string | null) => {
    switch (risk?.toLowerCase()) {
        case 'low':
            return 'success';
        case 'medium':
            return 'warning';
        case 'high':
            return 'error';
        default:
            return 'grey';
    }
};

const formatCurrency = (value: number | string | null) => {
    if (!value) return '$0';
    const num = typeof value === 'string' ? Number.parseFloat(value) : value;
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(num);
};

onMounted(() => {
    loadStrategies();
});
</script>

<style scoped>
.strategy-card {
    transition:
        transform 0.2s,
        box-shadow 0.2s;
}
.strategy-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.gap-2 {
    gap: 8px;
}
</style>
