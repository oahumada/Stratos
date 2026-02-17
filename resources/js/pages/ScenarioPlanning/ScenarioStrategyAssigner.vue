<script setup lang="ts">
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import AppLayout from '@/layouts/AppLayout.vue';
import { onMounted, ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps<{ scenarioId: number | string }>();
const api = useApi();
const { showSuccess, showError } = useNotification();

const step = ref(1);
const loading = ref(false);
const gaps = ref<any[]>([]);
const portfolio = ref<any>(null);

const selectedGap = ref<any>(null);
const assignmentForm = ref({
    gap_id: '',
    strategy_type: 'build',
    rationale: '',
    appointed_owner_id: 1,
    budget_allocation: 0,
});

const loadGaps = async () => {
    loading.value = true;
    try {
        const res: any = await api.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}/gaps-for-assignment`,
        );
        gaps.value = res.data;
    } catch (e: any) {
        showError('Error al cargar gaps: ' + e.message);
    } finally {
        loading.value = false;
    }
};

const selectGap = (gap: any) => {
    selectedGap.value = gap;
    assignmentForm.value.gap_id = gap.id;
    assignmentForm.value.rationale = `Se recomienda estrategia basada en ${gap.recommended_strategy}`;
    step.value = 2;
};

const submitAssignment = async () => {
    loading.value = true;
    try {
        await api.post(
            '/api/strategic-planning/strategies/assign',
            assignmentForm.value,
        );
        showSuccess('Estrategia asignada con éxito');
        await loadPortfolio();
        step.value = 3;
    } catch (e: any) {
        showError('Error al asignar estrategia: ' + e.message);
    } finally {
        loading.value = false;
    }
};

const loadPortfolio = async () => {
    try {
        const res: any = await api.get(
            `/api/strategic-planning/strategies/portfolio/${props.scenarioId}`,
        );
        portfolio.value = res.data;
    } catch (e: any) {
        console.error(e);
    }
};

const finalizeScenario = async () => {
    loading.value = true;
    try {
        const res: any = await api.post(
            `/api/strategic-planning/scenarios/${props.scenarioId}/finalize`,
            {},
        );
        showSuccess(
            'Escenario finalizado correctamente. Avanzando a Budgeting.',
        );
        // Refresh portfolio and gaps to reflect finalized state
        await loadPortfolio();
        await loadGaps();
    } catch (e: any) {
        showError('Error al finalizar escenario: ' + (e.message || e));
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadGaps();
    loadPortfolio();
});

const getCriticalityColor = (lvl: string) => {
    return lvl === 'critical' ? 'error' : 'warning';
};
</script>

<template>
    <v-container>
        <v-row class="mb-4">
            <v-col cols="12">
                <h2 class="text-h4 font-weight-bold">
                    <v-icon size="large" color="primary" class="mr-2"
                        >mdi-target-variant</v-icon
                    >
                    Asignador de Estrategias de Talento (4B)
                </h2>
                <p class="text-subtitle-1 text-medium-emphasis">
                    Transforma los gaps detectados en iniciativas estratégicas:
                    Build, Buy, Borrow o Bot.
                </p>
            </v-col>
        </v-row>

        <v-stepper v-model="step" class="elevation-2 rounded-lg">
            <v-stepper-header>
                <v-stepper-item
                    :complete="step > 1"
                    title="Identificar Gaps"
                    value="1"
                ></v-stepper-item>
                <v-divider></v-divider>
                <v-stepper-item
                    :complete="step > 2"
                    title="Asignar Estrategia"
                    value="2"
                ></v-stepper-item>
                <v-divider></v-divider>
                <v-stepper-item
                    title="Revisar Portafolio"
                    value="3"
                ></v-stepper-item>
            </v-stepper-header>

            <v-stepper-window>
                <!-- Step 1: Gap Identification -->
                <v-stepper-window-item value="1">
                    <v-card variant="flat">
                        <v-card-title
                            >Brechas de Capacidad Identificadas</v-card-title
                        >
                        <v-card-text>
                            <v-row
                                v-if="loading"
                                justify="center"
                                class="pa-12"
                            >
                                <v-progress-circular
                                    indeterminate
                                    color="primary"
                                ></v-progress-circular>
                            </v-row>
                            <v-row v-else>
                                <v-col
                                    v-for="gap in gaps"
                                    :key="gap.id"
                                    cols="12"
                                    md="6"
                                >
                                    <v-card border flat class="pa-3 hover-card">
                                        <div
                                            class="d-flex justify-space-between align-start"
                                        >
                                            <div>
                                                <div class="text-overline">
                                                    {{ gap.capability }}
                                                </div>
                                                <div class="text-h6">
                                                    {{ gap.role_archetype }}
                                                </div>
                                            </div>
                                            <v-chip
                                                :color="
                                                    getCriticalityColor(
                                                        gap.criticality,
                                                    )
                                                "
                                                size="small"
                                                class="text-uppercase"
                                            >
                                                {{ gap.criticality }}
                                            </v-chip>
                                        </div>
                                        <div class="text-body-2 my-2">
                                            {{ gap.description }}
                                        </div>
                                        <v-divider class="my-2"></v-divider>
                                        <div
                                            class="d-flex justify-space-between align-center"
                                        >
                                            <span class="text-caption"
                                                >Requerimiento:
                                                <strong>{{
                                                    gap.talent_requirement_count
                                                }}</strong>
                                                talentos</span
                                            >
                                            <v-btn
                                                color="primary"
                                                variant="tonal"
                                                size="small"
                                                @click="selectGap(gap)"
                                            >
                                                Configurar Estrategia
                                            </v-btn>
                                        </div>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-stepper-window-item>

                <!-- Step 2: Strategy Assignment -->
                <v-stepper-window-item value="2">
                    <v-card variant="flat" v-if="selectedGap">
                        <v-card-title
                            >Configurar Estrategia para
                            {{ selectedGap.role_archetype }}</v-card-title
                        >
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-radio-group
                                        v-model="assignmentForm.strategy_type"
                                        label="Seleccionar Modalidad 4B"
                                    >
                                        <v-radio value="build">
                                            <template v-slot:label>
                                                <div>
                                                    <strong>BUILD</strong>
                                                    (Upskilling / Desarrollo
                                                    Interno)
                                                </div>
                                            </template>
                                        </v-radio>
                                        <v-radio value="buy">
                                            <template v-slot:label>
                                                <div>
                                                    <strong>BUY</strong>
                                                    (Reclutamiento de Expertos)
                                                </div>
                                            </template>
                                        </v-radio>
                                        <v-radio value="borrow">
                                            <template v-slot:label>
                                                <div>
                                                    <strong>BORROW</strong>
                                                    (Contingent Talent /
                                                    Consultoría)
                                                </div>
                                            </template>
                                        </v-radio>
                                        <v-radio value="bot">
                                            <template v-slot:label>
                                                <div>
                                                    <strong>BOT</strong>
                                                    (Automatización / IA /
                                                    Agentes)
                                                </div>
                                            </template>
                                        </v-radio>
                                    </v-radio-group>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-textarea
                                        v-model="assignmentForm.rationale"
                                        label="Justificación Estratégica"
                                        rows="4"
                                        variant="outlined"
                                        placeholder="Explica por qué esta es la mejor opción para la viabilidad del escenario..."
                                    ></v-textarea>
                                    <v-text-field
                                        v-model.number="
                                            assignmentForm.budget_allocation
                                        "
                                        label="Presupuesto Asignado"
                                        type="number"
                                        prefix="$"
                                        variant="outlined"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn variant="text" @click="step = 1"
                                >Volver</v-btn
                            >
                            <v-btn
                                color="primary"
                                :loading="loading"
                                @click="submitAssignment"
                                >Confirmar Estrategia</v-btn
                            >
                        </v-card-actions>
                    </v-card>
                </v-stepper-window-item>

                <!-- Step 3: Portfolio Review -->
                <v-stepper-window-item value="3">
                    <v-card variant="flat" v-if="portfolio">
                        <v-card-title
                            >Portafolio de Iniciativas Consolidado</v-card-title
                        >
                        <v-card-text>
                            <v-row>
                                <v-col
                                    v-for="(
                                        metrics, strategy
                                    ) in portfolio.portfolio"
                                    :key="strategy"
                                    cols="12"
                                    md="3"
                                >
                                    <v-card class="pa-4 border text-center">
                                        <div
                                            class="text-overline font-weight-bold text-primary"
                                        >
                                            {{ strategy }}
                                        </div>
                                        <div class="text-h3">
                                            {{ metrics.count }}
                                        </div>
                                        <div class="text-caption">
                                            {{ metrics.status }}
                                        </div>
                                        <v-divider class="my-2"></v-divider>
                                        <div class="text-caption">
                                            Inv:
                                            <strong
                                                >${{
                                                    metrics.investment.toLocaleString()
                                                }}</strong
                                            >
                                        </div>
                                    </v-card>
                                </v-col>
                            </v-row>

                            <v-row class="mt-8">
                                <v-col cols="12" md="6">
                                    <v-card color="teal-darken-3" theme="dark">
                                        <v-card-text>
                                            <div class="text-h6">
                                                Impacto en Viabilidad
                                            </div>
                                            <div
                                                class="d-flex align-center mt-4"
                                            >
                                                <div class="text-h2">
                                                    {{
                                                        portfolio
                                                            .summary_metrics
                                                            .capability_coverage_delta
                                                    }}
                                                </div>
                                                <div class="ml-4">
                                                    Aumento proyectado en
                                                    cobertura de capacidades
                                                    clave.
                                                </div>
                                            </div>
                                        </v-card-text>
                                    </v-card>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-card
                                        color="indigo-darken-3"
                                        theme="dark"
                                    >
                                        <v-card-text>
                                            <div class="text-h6">
                                                Reducción de Riesgo
                                            </div>
                                            <div
                                                class="d-flex align-center mt-4"
                                            >
                                                <div class="text-h2">
                                                    {{
                                                        Math.round(
                                                            portfolio
                                                                .summary_metrics
                                                                .risk_reduction_index *
                                                                100,
                                                        )
                                                    }}%
                                                </div>
                                                <div class="ml-4">
                                                    Índice de mitigación de
                                                    brechas críticas.
                                                </div>
                                            </div>
                                        </v-card-text>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn
                                color="primary"
                                variant="outlined"
                                pill
                                @click="step = 1"
                                >Asignar Otro Gap</v-btn
                            >
                            <v-spacer></v-spacer>
                            <v-btn
                                color="success"
                                :loading="loading"
                                prepend-icon="mdi-check-all"
                                @click="finalizeScenario"
                                >Finalizar Plan Estratégico</v-btn
                            >
                        </v-card-actions>
                    </v-card>
                </v-stepper-window-item>
            </v-stepper-window>
        </v-stepper>
    </v-container>
</template>

<style scoped>
.hover-card:hover {
    border-color: rgb(var(--v-theme-primary)) !important;
    background-color: rgba(var(--v-theme-primary), 0.02);
    cursor: pointer;
}
</style>
