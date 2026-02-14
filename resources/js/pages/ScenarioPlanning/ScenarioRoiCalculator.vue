<script setup lang="ts">
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, ref } from 'vue';

defineOptions({ layout: AppLayout });

interface Props {
    scenarioId?: number | string;
}

const props = defineProps<Props>();
const api = useApi();
const { showSuccess, showError } = useNotification();

const loading = ref(false);
const result = ref<any>(null);

const form = ref({
    scenario_id: props.scenarioId || 1,
    talent_nodes_needed: 5,
    acquisition_cost_per_node: 15000,
    reskilling_cost_per_node: 8000,
    reskilling_duration_months: 6,
    internal_talent_pipeline: 3,
    external_market_salary: 120000,
    internal_maintenance_salary: 85000,
});

const calculateRoi = async () => {
    loading.value = true;
    try {
        const response: any = await api.post('/api/strategic-planning/roi-calculator/calculate', form.value);
        result.value = response.data;
        showSuccess('Cálculo de ROI completado');
    } catch (error: any) {
        showError('Error al calcular ROI: ' + error.message);
    } finally {
        loading.value = false;
    }
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
};

const getRoiColor = (index: number) => {
    if (index > 100) return 'success';
    if (index > 50) return 'primary';
    if (index > 0) return 'warning';
    return 'error';
};
</script>

<template>
    <v-container>
        <v-row class="mb-4">
            <v-col cols="12">
                <h2 class="text-h4 font-weight-bold">
                    <v-icon size="large" color="primary" class="mr-2">mdi-calculator-variant</v-icon>
                    Calculadora de ROI Estratégico (Talento)
                </h2>
                <p class="text-subtitle-1 text-medium-emphasis">
                    Compara estrategias de adquisición vs desarrollo de talentos para asegurar la viabilidad del escenario.
                </p>
            </v-col>
        </v-row>

        <v-row>
            <!-- Input Form -->
            <v-col cols="12" md="4">
                <v-card class="pa-4 elevation-2">
                    <v-card-title class="px-0">Parámetros de Simulación</v-card-title>
                    <v-form @submit.prevent="calculateRoi">
                        <v-text-field
                            v-model.number="form.talent_nodes_needed"
                            label="Gaps de Talento a cubrir"
                            type="number"
                            variant="outlined"
                            density="comfortable"
                        />
                        <v-text-field
                            v-model.number="form.acquisition_cost_per_node"
                            label="Costo Reclutamiento (Externo)"
                            type="number"
                            prefix="$"
                            variant="outlined"
                            density="comfortable"
                        />
                        <v-text-field
                            v-model.number="form.reskilling_cost_per_node"
                            label="Costo Capacitación (Interno)"
                            type="number"
                            prefix="$"
                            variant="outlined"
                            density="comfortable"
                        />
                        <v-slider
                            v-model="form.reskilling_duration_months"
                            label="Meses de Reskilling"
                            min="1"
                            max="24"
                            step="1"
                            thumb-label="always"
                            color="primary"
                            class="mt-4"
                        />
                        <v-divider class="my-4"></v-divider>
                        <v-text-field
                            v-model.number="form.external_market_salary"
                            label="Salario Mercado (Anual)"
                            type="number"
                            prefix="$"
                            variant="outlined"
                            density="comfortable"
                        />
                        <v-text-field
                            v-model.number="form.internal_maintenance_salary"
                            label="Salario Actual (Anual)"
                            type="number"
                            prefix="$"
                            variant="outlined"
                            density="comfortable"
                        />
                        
                        <v-btn
                            color="primary"
                            block
                            size="large"
                            @click="calculateRoi"
                            :loading="loading"
                            prepend-icon="mdi-chart-bar"
                        >
                            Calcular Impacto
                        </v-btn>
                    </v-form>
                </v-card>
            </v-col>

            <!-- Results Section -->
            <v-col cols="12" md="8">
                <div v-if="result">
                    <!-- Recommendation Card -->
                    <v-alert
                        type="info"
                        variant="tonal"
                        class="mb-6 border"
                        border="start"
                    >
                        <v-alert-title class="text-h6 font-weight-bold">
                            Recomendación Estratégica: {{ result.recommendation.strategy }}
                        </v-alert-title>
                        <p>{{ result.recommendation.reasoning }}</p>
                    </v-alert>

                    <!-- Comparison Grid -->
                    <v-row>
                        <v-col v-for="(data, strategy) in result.roi_comparison" :key="strategy" cols="12" lg="6">
                            <v-card class="h-100 border rounded-lg">
                                <v-toolbar density="compact" :color="getRoiColor(data.roi_index)" dark>
                                    <v-toolbar-title class="text-uppercase font-weight-bold text-caption">
                                        Estrategia: {{ strategy }}
                                    </v-toolbar-title>
                                    <v-spacer></v-spacer>
                                    <v-chip size="small" class="mr-2" color="white" text-color="black">
                                        ROI: {{ data.roi_index }}%
                                    </v-chip>
                                </v-toolbar>
                                <v-card-text>
                                    <v-list density="compact">
                                        <v-list-item>
                                            <template v-slot:prepend>
                                                <v-icon color="grey">mdi-cash</v-icon>
                                            </template>
                                            <v-list-item-title>Inversión Total</v-list-item-title>
                                            <template v-slot:append>
                                                <span class="font-weight-bold">{{ formatCurrency(data.total_cost) }}</span>
                                            </template>
                                        </v-list-item>
                                        <v-list-item>
                                            <template v-slot:prepend>
                                                <v-icon color="grey">mdi-calendar-clock</v-icon>
                                            </template>
                                            <v-list-item-title>Tiempo de Entrega</v-list-item-title>
                                            <template v-slot:append>
                                                <span>{{ data.time_to_delivery_months }} meses</span>
                                            </template>
                                        </v-list-item>
                                        <v-list-item>
                                            <template v-slot:prepend>
                                                <v-icon color="grey">mdi-shield-alert</v-icon>
                                            </template>
                                            <v-list-item-title>Nivel de Riesgo</v-list-item-title>
                                            <template v-slot:append>
                                                <v-chip size="x-small">{{ data.risk_level }}</v-chip>
                                            </template>
                                        </v-list-item>
                                        <v-divider class="my-2"></v-divider>
                                        <div class="pa-2 bg-grey-lighten-4 rounded italic text-caption">
                                            "{{ data.strategic_value }}"
                                        </div>
                                    </v-list>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                </div>
                <div v-else class="d-flex flex-column align-center justify-center h-100 text-medium-emphasis">
                    <v-icon size="80" color="grey-lighten-2">mdi-calculator-outline</v-icon>
                    <p>Ingresa los parámetros y presiona "Calcular Impacto" para ver la comparativa estratégica.</p>
                </div>
            </v-col>
        </v-row>
    </v-container>
</template>

<style scoped>
.v-card {
    transition: transform 0.2s;
}
.v-card:hover {
    transform: translateY(-4px);
}
</style>
