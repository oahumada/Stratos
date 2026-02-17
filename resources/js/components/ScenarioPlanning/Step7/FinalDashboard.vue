<template>
    <div class="final-dashboard">
        <v-container fluid>
            <v-row>
                <!-- KPIs Principales -->
                <v-col
                    cols="12"
                    md="4"
                    v-for="kpi in mainKpis"
                    :key="kpi.label"
                >
                    <v-card elevation="2" class="h-100">
                        <v-card-text class="d-flex align-center">
                            <v-avatar :color="kpi.color" size="48" class="mr-4">
                                <v-icon :icon="kpi.icon" color="white"></v-icon>
                            </v-avatar>
                            <div>
                                <div class="text-caption text-medium-emphasis">
                                    {{ kpi.label }}
                                </div>
                                <div class="text-h5 font-weight-bold">
                                    {{ kpi.value }}
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <v-row class="mt-4">
                <!-- Mix de Inversión y Synthetization -->
                <v-col cols="12" md="6">
                    <v-card
                        elevation="2"
                        title="Distribución de Inversión (4B)"
                    >
                        <v-card-text>
                            <div v-if="summary.total_investment > 0">
                                <v-list density="compact">
                                    <v-list-item
                                        v-for="item in summary.investment"
                                        :key="item.strategy_type"
                                    >
                                        <template v-slot:prepend>
                                            <v-icon
                                                :icon="
                                                    getStrategyIcon(
                                                        item.strategy_type,
                                                    )
                                                "
                                                :color="
                                                    getStrategyColor(
                                                        item.strategy_type,
                                                    )
                                                "
                                            ></v-icon>
                                        </template>
                                        <v-list-item-title
                                            class="text-capitalize"
                                            >{{
                                                item.strategy_type
                                            }}</v-list-item-title
                                        >
                                        <v-list-item-subtitle>{{
                                            formatCurrency(item.total_cost)
                                        }}</v-list-item-subtitle>
                                        <template v-slot:append>
                                            <div
                                                class="text-caption font-weight-bold"
                                            >
                                                {{
                                                    Math.round(
                                                        (item.total_cost /
                                                            summary.total_investment) *
                                                            100,
                                                    )
                                                }}%
                                            </div>
                                        </template>
                                    </v-list-item>
                                </v-list>
                                <v-divider class="my-2"></v-divider>
                                <div
                                    class="d-flex justify-space-between bg-grey-lighten-4 rounded px-4 py-2"
                                >
                                    <span class="font-weight-bold"
                                        >Total Inversión Estimada</span
                                    >
                                    <span class="text-h6 text-primary">{{
                                        formatCurrency(summary.total_investment)
                                    }}</span>
                                </div>
                            </div>
                            <v-alert v-else type="warning" variant="tonal"
                                >No hay estrategias de inversión
                                definidas.</v-alert
                            >
                        </v-card-text>
                    </v-card>

                    <v-card
                        elevation="2"
                        class="mt-4"
                        title="Mix Humano vs Sintético"
                    >
                        <v-card-text>
                            <div class="d-flex align-center mb-2">
                                <v-icon
                                    icon="mdi-account-outline"
                                    class="mr-2"
                                ></v-icon>
                                <span>Humano</span>
                                <v-spacer></v-spacer>
                                <span
                                    >{{
                                        100 - summary.synthetization_index
                                    }}%</span
                                >
                            </div>
                            <v-progress-linear
                                :model-value="summary.synthetization_index"
                                height="20"
                                rounded
                                color="purple"
                                background-color="blue"
                            >
                                <template v-slot:default="{ value }">
                                    <div
                                        class="text-caption font-weight-bold text-white"
                                    >
                                        Synthetization Index: {{ value }}%
                                    </div>
                                </template>
                            </v-progress-linear>
                            <div class="d-flex align-center mt-2">
                                <v-icon
                                    icon="mdi-robot-outline"
                                    class="mr-2"
                                    color="purple"
                                ></v-icon>
                                <span class="text-purple">Sintético (Bot)</span>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Gaps Críticos y Riesgos -->
                <v-col cols="12" md="6">
                    <v-card elevation="2" title="Gaps Críticos Identificados">
                        <v-card-text>
                            <v-table density="compact">
                                <thead>
                                    <tr>
                                        <th
                                            scope="col"
                                            class="font-weight-bold text-left"
                                        >
                                            Habilidad
                                        </th>
                                        <th
                                            scope="col"
                                            class="font-weight-bold text-center"
                                        >
                                            Prioridad
                                        </th>
                                        <th
                                            scope="col"
                                            class="font-weight-bold text-right"
                                        >
                                            Gap (FTE)
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="gap in summary.critical_gaps"
                                        :key="gap.skill"
                                    >
                                        <td>{{ gap.skill }}</td>
                                        <td class="text-center">
                                            <v-chip
                                                :color="
                                                    getPriorityColor(
                                                        gap.priority,
                                                    )
                                                "
                                                size="x-small"
                                                variant="flat"
                                            >
                                                {{ gap.priority }}
                                            </v-chip>
                                        </td>
                                        <td class="font-weight-bold text-right">
                                            {{ gap.gap }}
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </v-card-text>
                    </v-card>

                    <v-card
                        elevation="2"
                        class="mt-4"
                        :color="getRiskBgColor(summary.risk_level)"
                    >
                        <v-card-text class="d-flex align-center">
                            <v-icon
                                icon="mdi-alert-decagram"
                                size="32"
                                class="mr-4"
                            ></v-icon>
                            <div>
                                <div class="text-subtitle-1 font-weight-bold">
                                    Evaluación de Riesgo de Ejecución
                                </div>
                                <div class="text-h6">
                                    {{ summary.risk_level }} Risk
                                </div>
                                <div class="text-caption">
                                    Basado en Scenario IQ y Brecha de FTEs
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>

                    <!-- Acciones Finales -->
                    <div class="d-flex flex-column mt-6 gap-2">
                        <v-btn
                            block
                            color="success"
                            size="large"
                            prepend-icon="mdi-check-circle"
                            @click="approveScenario"
                        >
                            Aprobar y Finalizar Escenario
                        </v-btn>
                        <v-row>
                            <v-col cols="6">
                                <v-btn
                                    block
                                    variant="outlined"
                                    prepend-icon="mdi-file-pdf-box"
                                    >Exportar Reporte</v-btn
                                >
                            </v-col>
                            <v-col cols="6">
                                <v-btn
                                    block
                                    variant="outlined"
                                    prepend-icon="mdi-share-variant"
                                    >Compartir con Comité</v-btn
                                >
                            </v-col>
                        </v-row>
                    </div>
                </v-col>
            </v-row>
        </v-container>

        <!-- Diálogo de Aprobación -->
        <v-dialog v-model="approveDialog" max-width="400">
            <v-card>
                <v-card-title>Confirmar Aprobación</v-card-title>
                <v-card-text>
                    Al aprobar este escenario, se marcará como la versión
                    definitiva para su ejecución. ¿Deseas continuar?
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="approveDialog = false"
                        >Cancelar</v-btn
                    >
                    <v-btn
                        color="success"
                        variant="flat"
                        :loading="approving"
                        @click="confirmApproval"
                        >Confirmar</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup lang="ts">
import { useNotification } from '@/composables/useNotification';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    scenarioId: {
        type: [Number, String],
        required: true,
    },
});

const emit = defineEmits(['approved']);

const notification = useNotification();
const loading = ref(true);
const approving = ref(false);
const approveDialog = ref(false);
const summary = ref<any>({
    iq: 0,
    total_investment: 0,
    investment: [],
    fte: { required: 0, current: 0, gap: 0 },
    critical_gaps: [],
    synthetization_index: 0,
    risk_level: 'Medium',
});

const mainKpis = computed(() => [
    {
        label: 'Scenario IQ',
        value: `${summary.value.iq}%`,
        icon: 'mdi-brain',
        color: getIqColor(summary.value.iq),
    },
    {
        label: 'Inversión 4B',
        value: formatCurrency(summary.value.total_investment),
        icon: 'mdi-cash-multiple',
        color: 'success',
    },
    {
        label: 'Brecha de Talento',
        value: `${summary.value.fte.gap} FTEs`,
        icon: 'mdi-account-alert',
        color: 'orange',
    },
]);

const fetchSummary = async () => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}/summary`,
        );
        summary.value = res.data.data;
    } catch (error) {
        console.error('Error fetching summary:', error);
        notification.showError('Error al cargar el resumen ejecutivo');
    } finally {
        loading.value = false;
    }
};

const approveScenario = () => {
    approveDialog.value = true;
};

const confirmApproval = async () => {
    approving.value = true;
    try {
        await axios.post(
            `/api/strategic-planning/scenarios/${props.scenarioId}/finalize`,
        );
        notification.showSuccess('Escenario aprobado y finalizado con éxito');
        approveDialog.value = false;
        emit('approved');
    } catch (error) {
        console.error('Error in scenario approval:', error);
        notification.showError('Error al finalizar el escenario');
    } finally {
        approving.value = false;
    }
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(val);
};

const getIqColor = (iq: number) => {
    if (iq < 40) return 'error';
    if (iq < 70) return 'warning';
    return 'info';
};

const getStrategyColor = (type: string) => {
    const map: any = {
        build: 'blue',
        buy: 'green',
        borrow: 'orange',
        bot: 'purple',
    };
    return map[type] || 'grey';
};

const getStrategyIcon = (type: string) => {
    const map: any = {
        build: 'mdi-trending-up',
        buy: 'mdi-account-plus',
        borrow: 'mdi-account-switch',
        bot: 'mdi-robot',
    };
    return map[type] || 'mdi-help-circle';
};

const getPriorityColor = (p: string) => {
    const map: any = {
        critical: 'error',
        high: 'orange',
        medium: 'blue',
        low: 'grey',
    };
    return map[p] || 'grey';
};

const getRiskBgColor = (level: string) => {
    const map: any = {
        High: 'error-lighten-4',
        Medium: 'warning-lighten-4',
        Low: 'success-lighten-4',
    };
    return map[level] || 'grey-lighten-4';
};

onMounted(fetchSummary);
</script>

<style scoped>
.gap-2 {
    gap: 8px;
}
</style>
