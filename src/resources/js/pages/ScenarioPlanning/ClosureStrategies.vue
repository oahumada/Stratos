<script setup lang="ts">
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import { onMounted, ref, watch } from 'vue';

interface Strategy {
    id: number;
    skill_id?: number;
    skill_name?: string;
    strategy: string;
    strategy_name?: string;
    description?: string;
    estimated_cost?: number;
    estimated_time_weeks?: number;
    success_probability?: number;
    risk_level?: string;
    status?: string;
    action_items?: string[];
}

const props = defineProps<{ scenarioId: number }>();
const emit = defineEmits<{ (e: 'refreshed'): void }>();

const api = useApi();
const { showSuccess, showError } = useNotification();

const loading = ref(false);
const refreshing = ref(false);
const strategies = ref<Strategy[]>([]);
const statusFilter = ref<string | null>(null);
const strategyFilter = ref<string | null>(null);

const statusOptions = [
    'proposed',
    'approved',
    'in_progress',
    'completed',
    'cancelled',
];
const strategyOptions = ['build', 'buy', 'borrow', 'bot', 'bind', 'bridge'];

/**
 * Fetches the closure strategies for a given scenario
 * @returns {Promise<void>} resolves when the strategies are fetched
 */
const fetchStrategies = async () => {
    if (!props.scenarioId) return;
    loading.value = true;
    try {
        const res: any = await api.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}`,
        );
        const raw = res?.data || res;
        strategies.value = raw?.closure_strategies || [];
    } catch (error) {
        showError('No se pudieron cargar las estrategias: ' + error);
    } finally {
        loading.value = false;
    }
};

/**
 * Refreshes the suggested closure strategies for a given scenario
 * This will trigger a call to the backend to refresh the strategies,
 * and then fetch the updated strategies.
 * @emits {refreshed} when the strategies are refreshed
 */
const refreshSuggested = async () => {
    refreshing.value = true;
    try {
        await api.post(
            `/api/strategic-planning/scenarios/${props.scenarioId}/refresh-suggested-strategies`,
        );
        await fetchStrategies();
        showSuccess('Estrategias sugeridas actualizadas');
        emit('refreshed');
    } catch (error) {
        showError('Error al refrescar estrategias: ' + error);
    } finally {
        refreshing.value = false;
    }
};

const filteredStrategies = () => {
    return strategies.value.filter((s) => {
        const matchStatus = statusFilter.value
            ? s.status === statusFilter.value
            : true;
        const matchStrategy = strategyFilter.value
            ? s.strategy === strategyFilter.value
            : true;
        return matchStatus && matchStrategy;
    });
};

const formatMoney = (value?: number) => {
    if (value === undefined || value === null) return '—';
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(value);
};

/**
 * Formats a probability value as a percentage string.
 * If the value is undefined or null, returns '—'.
 * @param {number} [value] - The probability value to format.
 * @returns {string} - The formatted probability value as a percentage string.
 */
const formatProb = (value?: number) => {
    if (value === undefined || value === null) return '—';
    return `${Math.round(value * 100)}%`;
};

watch(() => props.scenarioId, fetchStrategies, { immediate: true });

onMounted(() => {
    fetchStrategies();
});
</script>

<template>
    <div class="closure-strategies">
        <v-row class="mb-4">
            <v-col cols="12" md="8">
                <h3 class="mb-1">Estrategias de cierre de brechas</h3>
                <p class="text-medium-emphasis mb-0">
                    Revisa y filtra las estrategias sugeridas (6Bs)
                </p>
            </v-col>
            <v-col cols="12" md="4" class="text-right">
                <v-btn
                    color="primary"
                    :loading="refreshing"
                    @click="refreshSuggested"
                    prepend-icon="mdi-refresh"
                >
                    Refrescar sugerencias
                </v-btn>
            </v-col>
        </v-row>

        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="4">
                        <v-select
                            v-model="statusFilter"
                            :items="statusOptions"
                            label="Estado"
                            clearable
                        />
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-select
                            v-model="strategyFilter"
                            :items="strategyOptions"
                            label="Estrategia"
                            clearable
                        />
                    </v-col>
                </v-row>
            </v-card-text>

            <v-data-table
                :items="filteredStrategies()"
                :headers="[
                    { title: 'Skill', key: 'skill_name' },
                    { title: 'Estrategia', key: 'strategy' },
                    { title: 'Costo', key: 'estimated_cost' },
                    { title: 'Tiempo (sem)', key: 'estimated_time_weeks' },
                    { title: 'Éxito', key: 'success_probability' },
                    { title: 'Riesgo', key: 'risk_level' },
                    { title: 'Estado', key: 'status' },
                ]"
                :loading="loading"
                item-key="id"
                class="elevation-0"
            >
                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template #item.skill_name="{ item }">
                    <div class="d-flex align-center">
                        <v-icon size="small" class="mr-2"
                            >mdi-lightbulb-on-outline</v-icon
                        >
                        <div>
                            <div class="font-weight-medium">
                                {{ item.skill_name || 'Skill' }}
                            </div>
                            <div class="text-caption text-medium-emphasis">
                                {{ item.strategy_name || 'Estrategia' }}
                            </div>
                        </div>
                    </div>
                </template>

                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template #item.strategy="{ item }">
                    <v-chip
                        size="small"
                        color="primary"
                        variant="tonal"
                        class="text-uppercase"
                        >{{ item.strategy }}</v-chip
                    >
                </template>

                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template #item.estimated_cost="{ item }">{{
                    formatMoney(item.estimated_cost)
                }}</template>
                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template #item.estimated_time_weeks="{ item }">{{
                    item.estimated_time_weeks || '—'
                }}</template>
                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template #item.success_probability="{ item }">{{
                    formatProb(item.success_probability)
                }}</template>
                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template #item.risk_level="{ item }">
                    <v-chip
                        size="small"
                        :color="
                            item.risk_level === 'high'
                                ? 'error'
                                : item.risk_level === 'medium'
                                  ? 'warning'
                                  : 'success'
                        "
                        variant="flat"
                    >
                        {{ item.risk_level || '—' }}
                    </v-chip>
                </template>
                <!-- eslint-disable-next-line vue/valid-v-slot -->
                <template #item.status="{ item }">
                    <v-chip
                        size="small"
                        :color="
                            item.status === 'approved'
                                ? 'success'
                                : item.status === 'in_progress'
                                  ? 'info'
                                  : item.status === 'completed'
                                    ? 'primary'
                                    : 'warning'
                        "
                        variant="flat"
                    >
                        {{ item.status || 'proposed' }}
                    </v-chip>
                </template>

                <template #no-data>
                    <div class="pa-6 text-medium-emphasis text-center">
                        No hay estrategias para este escenario.
                    </div>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>
