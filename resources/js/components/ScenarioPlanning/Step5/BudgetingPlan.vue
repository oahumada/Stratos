<template>
    <div class="budgeting-plan-container">
        <v-row>
            <!-- Panel de Supuestos -->
            <v-col cols="12" md="4">
                <v-card class="mb-4" elevation="2">
                    <v-card-title class="bg-indigo-darken-2 text-white">
                        <v-icon start>mdi-calculator</v-icon>
                        Parámetros Financieros
                    </v-card-title>
                    <v-card-text class="pt-4">
                        <v-form v-model="formValid">
                            <v-text-field
                                v-model.number="assumptions.avg_annual_salary"
                                label="Salario Anual Promedio (USD)"
                                type="number"
                                prefix="$"
                                variant="outlined"
                                density="comfortable"
                                @change="saveAssumptions"
                            ></v-text-field>

                            <v-divider class="my-4"></v-divider>

                            <div class="text-subtitle-2 mb-2">
                                Costos por Estrategia
                            </div>

                            <v-text-field
                                v-model.number="assumptions.buy_hiring_fee_pct"
                                label="Fee Reclutamiento (%)"
                                type="number"
                                suffix="%"
                                variant="outlined"
                                density="compact"
                                hint="Costo de contratación externa sobre salario"
                                persistent-hint
                                @change="saveAssumptions"
                            ></v-text-field>

                            <v-text-field
                                v-model.number="
                                    assumptions.build_training_unit_cost
                                "
                                label="Costo Unitario Formación"
                                type="number"
                                prefix="$"
                                variant="outlined"
                                density="compact"
                                class="mt-4"
                                hint="Costo promedio de upskilling por persona"
                                persistent-hint
                                @change="saveAssumptions"
                            ></v-text-field>

                            <v-text-field
                                v-model.number="assumptions.borrow_premium_pct"
                                label="Premium Contingente (%)"
                                type="number"
                                suffix="%"
                                variant="outlined"
                                density="compact"
                                class="mt-4"
                                hint="Sobrecosto de perfiles temporales/BPO"
                                persistent-hint
                                @change="saveAssumptions"
                            ></v-text-field>

                            <v-text-field
                                v-model.number="assumptions.bot_monthly_cost"
                                label="Costo Mensual Bot/IA"
                                type="number"
                                prefix="$"
                                variant="outlined"
                                density="compact"
                                class="mt-4"
                                hint="Licenciamiento/mantenimiento por agente"
                                persistent-hint
                                @change="saveAssumptions"
                            ></v-text-field>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="indigo"
                            variant="text"
                            :loading="saving"
                            @click="saveAssumptions"
                        >
                            Guardar Supuestos
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>

            <!-- Panel de Resumen de Inversión -->
            <v-col cols="12" md="8">
                <v-card elevation="2">
                    <v-card-title
                        class="d-flex justify-space-between align-center"
                    >
                        <div>
                            <v-icon start color="success"
                                >mdi-cash-multiple</v-icon
                            >
                            Resumen de Inversión Estratégica
                        </div>
                        <v-chip color="success" variant="flat">
                            Total: {{ formatCurrency(totalInvestment) }}
                        </v-chip>
                    </v-card-title>
                    <v-card-text>
                        <v-row class="mt-2">
                            <v-col
                                v-for="stat in strategySummary"
                                :key="stat.type"
                                cols="6"
                                sm="3"
                            >
                                <v-card
                                    variant="tonal"
                                    :color="stat.color"
                                    class="pa-2 text-center"
                                >
                                    <div class="text-caption text-uppercase">
                                        {{ stat.label }}
                                    </div>
                                    <div class="text-h6">
                                        {{ formatCurrency(stat.total) }}
                                    </div>
                                    <div class="text-caption">
                                        {{ stat.count }} acciones
                                    </div>
                                </v-card>
                            </v-col>
                        </v-row>

                        <v-divider class="my-6"></v-divider>

                        <div class="text-h6 mb-4">
                            Desglose de Costos Estimados
                        </div>
                        <v-table density="comfortable">
                            <thead>
                                <tr>
                                    <th scope="col">Estrategia</th>
                                    <th scope="col">Rol / Habilidad</th>
                                    <th scope="col" class="text-right">
                                        Base Cálculo
                                    </th>
                                    <th scope="col" class="text-right">
                                        Costo Estimado
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="item in calculatedStrategies"
                                    :key="item.id"
                                >
                                    <td>
                                        <v-chip
                                            size="x-small"
                                            :color="
                                                getStrategyColor(item.strategy)
                                            "
                                            class="text-uppercase"
                                        >
                                            {{ item.strategy }}
                                        </v-chip>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">
                                            {{ item.role_name || 'General' }}
                                        </div>
                                        <div class="text-caption">
                                            {{ item.skill_name }}
                                        </div>
                                    </td>
                                    <td class="text-caption text-right">
                                        {{ getCalculationBase(item) }}
                                    </td>
                                    <td class="font-weight-bold text-right">
                                        {{
                                            formatCurrency(item.calculated_cost)
                                        }}
                                    </td>
                                </tr>
                                <tr v-if="calculatedStrategies.length === 0">
                                    <td
                                        colspan="4"
                                        class="text-medium-emphasis py-4 text-center"
                                    >
                                        No hay estrategias generadas para
                                        calcular costos.
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-card-text>
                </v-card>

                <!-- Gráfico de Distribución (Conceptual) -->
                <v-card class="mt-4" variant="outlined">
                    <v-card-text class="pa-0">
                        <div class="d-flex" style="height: 12px">
                            <div
                                v-for="stat in strategySummary"
                                :key="'bar-' + stat.type"
                                :style="{
                                    width:
                                        (totalInvestment > 0
                                            ? (stat.total / totalInvestment) *
                                              100
                                            : 0) + '%',
                                    backgroundColor: getHexColor(stat.color),
                                }"
                                v-tooltip="stat.label"
                            ></div>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
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

const notification = useNotification();
const loading = ref(false);
const saving = ref(false);
const formValid = ref(true);
const strategies = ref<any[]>([]);

const assumptions = ref({
    avg_annual_salary: 48000,
    buy_hiring_fee_pct: 15,
    build_training_unit_cost: 1200,
    borrow_premium_pct: 30,
    bot_monthly_cost: 250,
});

const loadData = async () => {
    loading.value = true;
    try {
        // 1. Cargar Escenario para traer assumptions
        const scenarioRes = await axios.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}`,
        );
        const scenario = scenarioRes.data.data || scenarioRes.data;

        if (scenario.assumptions?.financial) {
            assumptions.value = {
                ...assumptions.value,
                ...scenario.assumptions.financial,
            };
        }

        // 2. Cargar Estrategias
        const strategyRes = await axios.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}/strategies`,
        );
        strategies.value = strategyRes.data.data || [];
    } catch (error) {
        console.error('Error loading budget data', error);
        notification.showError('Error al cargar datos financieros');
    } finally {
        loading.value = false;
    }
};

const saveAssumptions = async () => {
    saving.value = true;
    try {
        await axios.patch(
            `/api/strategic-planning/scenarios/${props.scenarioId}`,
            {
                assumptions: {
                    financial: assumptions.value,
                },
            },
        );
        notification.showSuccess('Supuestos actualizados');
    } catch (error) {
        console.error('Error saving assumptions', error);
        notification.showError('Error al guardar supuestos');
    } finally {
        saving.value = false;
    }
};

const calculatedStrategies = computed(() => {
    return strategies.value.map((s) => {
        let cost = 0;
        const monthlySalary = assumptions.value.avg_annual_salary / 12;

        switch (s.strategy) {
            case 'buy':
                cost =
                    assumptions.value.avg_annual_salary *
                    (assumptions.value.buy_hiring_fee_pct / 100);
                break;
            case 'build':
                cost = assumptions.value.build_training_unit_cost;
                break;
            case 'borrow':
                // Supuesto: 6 meses de contratación con el premium
                cost =
                    monthlySalary *
                    6 *
                    (1 + assumptions.value.borrow_premium_pct / 100);
                break;
            case 'bot':
                // Supuesto: Implementación + 12 meses de mantenimiento
                cost = 2000 + assumptions.value.bot_monthly_cost * 12;
                break;
            default:
                cost = s.estimated_cost || 0;
        }

        return {
            ...s,
            calculated_cost: cost,
        };
    });
});

const strategySummary = computed(() => {
    const types = [
        { type: 'build', label: 'Build', color: 'indigo' },
        { type: 'buy', label: 'Buy', color: 'emerald' },
        { type: 'borrow', label: 'Borrow', color: 'amber' },
        { type: 'bot', label: 'Bot', color: 'purple' },
    ];

    return types.map((t) => {
        const filtered = calculatedStrategies.value.filter(
            (s) => s.strategy === t.type,
        );
        return {
            ...t,
            count: filtered.length,
            total: filtered.reduce(
                (acc, curr) => acc + curr.calculated_cost,
                0,
            ),
        };
    });
});

const totalInvestment = computed(() => {
    return calculatedStrategies.value.reduce(
        (acc, curr) => acc + curr.calculated_cost,
        0,
    );
});

const getCalculationBase = (item: any) => {
    switch (item.strategy) {
        case 'buy':
            return `${assumptions.value.buy_hiring_fee_pct}% de Salario Anual`;
        case 'build':
            return `Costo unitario por upskilling`;
        case 'borrow':
            return `6 meses @ ${assumptions.value.borrow_premium_pct}% premium`;
        case 'bot':
            return `Setup + 12 meses mant.`;
        default:
            return 'Costo manual';
    }
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(val);
};

const getStrategyColor = (type: string) => {
    const colors: Record<string, string> = {
        build: 'indigo',
        buy: 'emerald',
        borrow: 'amber',
        bot: 'purple',
    };
    return colors[type] || 'grey';
};

const getHexColor = (color: string) => {
    const hex: Record<string, string> = {
        indigo: '#3f51b5',
        emerald: '#10b981',
        amber: '#ffb300',
        purple: '#9c27b0',
    };
    return hex[color] || '#9e9e9e';
};

onMounted(loadData);
</script>

<style scoped>
.budgeting-plan-container {
    padding: 16px 0;
}
</style>
