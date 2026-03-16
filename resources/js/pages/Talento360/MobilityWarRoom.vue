<script setup lang="ts">
import GamificationDashboard from '@/components/Lms/GamificationDashboard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';

defineOptions({ layout: AppLayout });

interface Metric {
    fit_score: number;
    friction_score: number;
    legacy_risk: number;
    success_probability: number;
}

interface FinancialImpact {
    currency: string;
    target_monthly_salary: number;
    recruitment_avoidance_cost: number;
    internal_transition_cost: number;
    roi_amount: number;
    roi_percentage: number;
}

interface SuccessionCandidate {
    id: number;
    name: string;
    current_role: string;
    fit_score: number;
}

interface DominoEffect {
    vacant_role: { id: number; name: string };
    succession_candidates: SuccessionCandidate[];
    impact_description: string;
}

interface SimulationResult {
    person?: {
        id: number;
        name: string;
        current_role: string;
        current_department: string;
    };
    target_role?: {
        id: number;
        name: string;
    };
    metrics?: Metric;
    financial_impact?: FinancialImpact;
    domino_effect?: DominoEffect;
    insights?: string[];
    // Mass fields
    individual_results?: any[];
    aggregated_metrics?: {
        avg_fit_score: number;
        avg_friction_score: number;
        avg_legacy_risk: number;
        success_probability: number;
        total_moved: number;
        total_roi_projected: number;
        total_recruitment_savings: number;
    };
    group_insights?: string[];
    skill_gaps?: Array<{
        skill_id: number;
        name: string;
        current_level: number;
        required_level: number;
        is_critical: boolean;
    }>;
    suggested_courses?: Array<{
        course_id: string | number;
        title: string;
        provider: string;
        cost: number;
        skill?: string;
    }>;
    succession_chain?: {
        nodes: Array<{
            id: string;
            label: string;
            type: 'person' | 'vacancy';
            level: number;
            fit?: number;
        }>;
    };
}

interface DeptImpact {
    department_id: number;
    department_name: string;
    health_score: number;
    critical_roles_count: number;
    potential_mobility_out: number;
    friction_index: number;
    risk_level: string;
}

const loading = ref(false);
const catalogsLoading = ref(true);
const impactLoading = ref(false);
const people = ref<any[]>([]);
const roles = ref<any[]>([]);
const selectedPersonIds = ref<number[]>([]);
const selectedRoleId = ref<number | null>(null);
const result = ref<SimulationResult | null>(null);
const orgImpact = ref<DeptImpact[]>([]);
const savedScenarioId = ref<number | null>(null);

const loadCatalogs = async () => {
    try {
        const res = await axios.get('/api/catalogs', {
            params: { endpoints: ['people', 'roles'] },
        });
        people.value = res.data.people || [];
        roles.value = res.data.roles || [];
    } catch (e) {
        console.error('Error loading catalogs', e);
    } finally {
        catalogsLoading.value = false;
    }
};

const loadOrgImpact = async () => {
    impactLoading.value = true;
    try {
        const res = await axios.get(
            '/api/strategic-planning/mobility/organization-impact',
        );
        orgImpact.value = res.data.data;
    } catch (e) {
        console.error('Error loading org impact', e);
    } finally {
        impactLoading.value = false;
    }
};

const runSimulation = async (movements = null) => {
    if (
        !movements &&
        (selectedPersonIds.value.length === 0 || !selectedRoleId.value)
    )
        return;
    saveStatus.value = '';
    savedScenarioId.value = null;
    loading.value = true;
    result.value = null;
    try {
        let payload = {};
        if (movements) {
            payload = { movements };
        } else {
            payload =
                selectedPersonIds.value.length === 1
                    ? {
                          person_id: selectedPersonIds.value[0],
                          target_role_id: selectedRoleId.value,
                      }
                    : {
                          person_ids: selectedPersonIds.value,
                          target_role_id: selectedRoleId.value,
                      };
        }

        const res = await axios.post(
            '/api/strategic-planning/mobility/simulate',
            payload,
        );
        result.value = res.data.data;
        loadOrgImpact();
    } catch (e) {
        console.error('Simulation error', e);
    } finally {
        loading.value = false;
    }
};

const saveStatus = ref('');
const savingScenario = ref(false);
const materializing = ref(false);
const activeTab = ref(0);
const executionTracking = ref<any[]>([]);
const loadingTracking = ref(false);

const selectedTrackingId = ref<number | null>(null);
const trackingDetail = ref<any>(null);
const loadingDetail = ref(false);
const showTrackingDialog = ref(false);

const loadExecutionDetail = async (id: number) => {
    selectedTrackingId.value = id;
    loadingDetail.value = true;
    showTrackingDialog.value = true;
    try {
        const res = await axios.get(`/api/strategic-planning/mobility/execution/${id}`);
        trackingDetail.value = res.data;
    } catch (e) {
        console.error('Error loading tracking detail', e);
    } finally {
        loadingDetail.value = false;
    }
};

const launchLms = async (actionId: number) => {
    try {
        const res = await axios.post(`/api/strategic-planning/mobility/execution/launch/${actionId}`);
        if (res.data.success && res.data.launch_url) {
            window.open(res.data.launch_url, '_blank');
            // Refresh detail to see status change
            loadExecutionDetail(selectedTrackingId.value!);
        }
    } catch (e) {
        console.error('Error launching LMS', e);
    }
};

const syncLmsProgress = async (actionId: number) => {
    try {
        const res = await axios.post(`/api/strategic-planning/mobility/execution/sync/${actionId}`);
        if (res.data.success) {
            loadExecutionDetail(selectedTrackingId.value!);
        }
    } catch (e) {
        console.error('Error syncing progress', e);
    }
};

const loadExecutionTracking = async () => {
    loadingTracking.value = true;
    try {
        const res = await axios.get(
            '/api/strategic-planning/mobility/execution-status',
        );
        executionTracking.value = res.data;
    } catch (e) {
        console.error('Execution tracking error', e);
    } finally {
        loadingTracking.value = false;
    }
};

const aiObjective = ref('');
const aiLoading = ref(false);
const showAiDialog = ref(false);
const aiSuggestions = ref<any>(null);

const getAiSuggestions = async () => {
    if (!aiObjective.value || aiObjective.value.length < 10) return;
    aiLoading.value = true;
    try {
        const response = await axios.post(
            '/api/strategic-planning/mobility/ai-suggestions',
            {
                objective: aiObjective.value,
            },
        );
        if (response.data.success) {
            aiSuggestions.value = response.data;
            showAiDialog.value = true;
        } else {
            saveStatus.value =
                response.data.message || 'Error al obtener sugerencias.';
        }
    } catch (e) {
        console.error(e);
        saveStatus.value = 'Error al conectar con el Advisor de IA';
    } finally {
        aiLoading.value = false;
    }
};

const applyAiSuggestion = (proposal: any) => {
    selectedPersonIds.value = [proposal.person_id];
    selectedRoleId.value = proposal.target_role_id;
    showAiDialog.value = false;
    runSimulation();
};

const applyAllAiSuggestions = () => {
    if (!aiSuggestions.value || !aiSuggestions.value.proposals.length) return;

    const movements = aiSuggestions.value.proposals.map((p: any) => ({
        person_id: p.person_id,
        target_role_id: p.target_role_id,
        rationale: p.rationale,
        suggested_courses: p.suggested_courses,
    }));

    // Sincronizamos la UI por si el usuario quiere editar después
    selectedPersonIds.value = movements.map((m) => m.person_id);
    selectedRoleId.value = movements[0].target_role_id; // Seteamos el primero en el selector por UI

    showAiDialog.value = false;
    runSimulation(movements);
};

watch(activeTab, (newVal) => {
    if (newVal === 1) {
        loadExecutionTracking();
    }
});

const saveAsScenario = async () => {
    if (!result.value || !selectedRoleId.value) return;

    savingScenario.value = true;
    saveStatus.value = '';

    try {
        const scenarioName = `Simulación Movilidad: ${selectedPersonIds.value.length} talentos a ${roles.value.find((r) => r.id === selectedRoleId.value)?.name || 'Nuevo Rol'}`;

        const res = await axios.post(
            '/api/strategic-planning/mobility/save-scenario',
            {
                name: scenarioName,
                target_role_id: selectedRoleId.value,
                simulation_data: result.value,
            },
        );

        savedScenarioId.value = res.data.data.id;
        saveStatus.value =
            'Escenario guardado correctamente. Ahora puede materializarlo.';
        setTimeout(() => (saveStatus.value = ''), 5000);
    } catch (e) {
        console.error('Error saving scenario', e);
        saveStatus.value = 'Error al guardar el escenario.';
    } finally {
        savingScenario.value = false;
    }
};

const materializePlan = async () => {
    if (!savedScenarioId.value) return;

    materializing.value = true;
    saveStatus.value = '';

    try {
        await axios.post(
            `/api/strategic-planning/mobility/scenarios/${savedScenarioId.value}/materialize`,
        );
        saveStatus.value =
            'Plan materializado: Se ha generado un ChangeSet en el módulo de Planificación.';
    } catch (e) {
        console.error('Materialization error', e);
        saveStatus.value = 'Error al materializar el plan.';
    } finally {
        materializing.value = false;
    }
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(val);
};

const getMetricColor = (val: number, inverse = false) => {
    if (inverse) val = 1 - val;
    if (val > 0.7) return '#10b981';
    if (val > 0.4) return '#f59e0b';
    return '#ef4444';
};

const getRiskColor = (level: string) => {
    switch (level) {
        case 'CRÍTICO':
            return '#ef4444';
        case 'ADVERTENCIA':
            return '#f59e0b';
        default:
            return '#10b981';
    }
};

onMounted(() => {
    loadCatalogs();
    loadOrgImpact();
});
</script>

<template>
    <div class="mobility-war-room">
        <Head title="Mobility War-Room | Stratos" />

        <v-container fluid class="pa-6">
            <v-row>
                <v-col cols="12">
                    <div class="d-flex align-center justify-space-between mb-6">
                        <div class="d-flex align-center">
                            <v-avatar
                                color="primary"
                                size="48"
                                class="elevation-4 mr-4"
                            >
                                <v-icon color="white"
                                    >mdi-transit-connection-variant</v-icon
                                >
                            </v-avatar>
                            <div>
                                <h1
                                    class="text-h4 font-weight-black text-white"
                                >
                                    Monitor de Movilidad
                                </h1>
                                <p class="text-subtitle-1 text-slate-400">
                                    Diseño y Seguimiento Estratégico
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-center">
                            <v-btn
                                variant="text"
                                color="primary"
                                @click="loadOrgImpact"
                                :loading="impactLoading"
                                class="rounded-lg"
                            >
                                <v-icon class="mr-2">mdi-refresh</v-icon>
                                Actualizar Salud Orgánica
                            </v-btn>
                        </div>
                    </div>
                    <v-alert
                        v-if="saveStatus"
                        :type="
                            saveStatus.includes('Error') ? 'error' : 'success'
                        "
                        variant="tonal"
                        closable
                        class="animate-fade-in mb-4"
                    >
                        {{ saveStatus }}
                    </v-alert>
                </v-col>
            </v-row>

            <v-tabs
                v-model="activeTab"
                bg-color="transparent"
                color="primary"
                grow
                class="mb-6 border-b border-slate-700"
            >
                <v-tab :value="0">
                    <v-icon left class="mr-2">mdi-chart-line</v-icon>
                    Simulación y Diseño
                </v-tab>
                <v-tab :value="1">
                    <v-icon left class="mr-2">mdi-list-status</v-icon>
                    Seguimiento de Ejecución
                </v-tab>
                <v-tab :value="2">
                    <v-icon left class="mr-2">mdi-trophy-variant</v-icon>
                    Tablero de Gamificación
                </v-tab>
            </v-tabs>

            <v-window v-model="activeTab" class="bg-transparent">
                <!-- Tab 1: Simulation -->
                <v-window-item :value="0">
                    <v-row class="mb-4">
                        <v-col cols="12" class="d-flex justify-end">
                            <v-btn
                                v-if="result && !savedScenarioId"
                                color="success"
                                variant="elevated"
                                prepend-icon="mdi-content-save-outline"
                                :loading="savingScenario"
                                @click="saveAsScenario"
                                class="mr-3 rounded-lg"
                            >
                                Guardar Escenario
                            </v-btn>
                            <v-btn
                                v-if="savedScenarioId"
                                color="secondary"
                                variant="elevated"
                                prepend-icon="mdi-flash"
                                :loading="materializing"
                                @click="materializePlan"
                                class="rounded-lg"
                            >
                                Materializar Plan
                            </v-btn>
                        </v-col>
                    </v-row>
                    <v-row>
                        <!-- Configuration & Heatmap -->
                        <v-col cols="12" md="4">
                            <v-card class="glass-card pa-6 mb-6">
                                <div class="text-overline mb-4 text-primary">
                                    Escenario de Transformación
                                </div>

                                <v-autocomplete
                                    v-model="selectedPersonIds"
                                    :items="people"
                                    item-title="name"
                                    item-value="id"
                                    label="Talentos a Mover"
                                    placeholder="Seleccione colaboradores..."
                                    variant="outlined"
                                    class="mb-4"
                                    multiple
                                    chips
                                    closable-chips
                                    :loading="catalogsLoading"
                                    prepend-inner-icon="mdi-account-multiple"
                                ></v-autocomplete>

                                <v-autocomplete
                                    v-model="selectedRoleId"
                                    :items="roles"
                                    item-title="name"
                                    item-value="id"
                                    label="Posición de Destino"
                                    placeholder="Rol estratégico..."
                                    variant="outlined"
                                    class="mb-6"
                                    :loading="catalogsLoading"
                                    prepend-inner-icon="mdi-target"
                                ></v-autocomplete>

                                <v-btn
                                    block
                                    color="primary"
                                    size="large"
                                    :loading="loading"
                                    :disabled="
                                        selectedPersonIds.length === 0 ||
                                        !selectedRoleId
                                    "
                                    @click="runSimulation"
                                    class="elevation-8 rounded-lg"
                                >
                                    Ejecutar Proyección Estratégica
                                </v-btn>
                            </v-card>

                            <!-- AI Advisor Card -->
                            <v-card
                                class="glass-card pa-6 border-indigo-glow mb-6 border-2"
                            >
                                <div class="d-flex align-center mb-4">
                                    <v-avatar
                                        color="indigo-darken-4"
                                        size="32"
                                        class="mr-3"
                                    >
                                        <v-icon
                                            color="indigo-lighten-2"
                                            size="20"
                                            >mdi-auto-fix</v-icon
                                        >
                                    </v-avatar>
                                    <div
                                        class="text-overline text-indigo-lighten-2 font-weight-black"
                                    >
                                        Stratos AI Advisor
                                    </div>
                                </div>

                                <p
                                    class="text-caption mb-4 text-indigo-100 opacity-70"
                                >
                                    Describa un objetivo estratégico y deje que
                                    la IA diseñe el combo de movimientos óptimo.
                                </p>

                                <v-textarea
                                    v-model="aiObjective"
                                    label="Objetivo de Transformación"
                                    placeholder="Describa qué quiere lograr..."
                                    variant="solo-filled"
                                    rows="3"
                                    auto-grow
                                    class="mb-4 text-white"
                                    bg-color="rgba(30, 41, 59, 0.6)"
                                    hide-details
                                ></v-textarea>

                                <v-btn
                                    block
                                    color="indigo-accent-2"
                                    variant="elevated"
                                    @click="getAiSuggestions"
                                    :loading="aiLoading"
                                    :disabled="
                                        !aiObjective || aiObjective.length < 10
                                    "
                                    prepend-icon="mdi-sparkles"
                                    class="font-weight-bold rounded-lg"
                                >
                                    Generar Estrategia IA
                                </v-btn>
                            </v-card>

                            <!-- Org Heatmap -->
                            <v-card class="glass-card pa-6 overflow-hidden">
                                <div class="text-overline mb-4 text-slate-400">
                                    Estado Sistémico Corporativo
                                </div>
                                <v-list
                                    v-if="!impactLoading"
                                    bg-color="transparent"
                                    class="pa-0"
                                >
                                    <v-list-item
                                        v-for="dept in orgImpact"
                                        :key="dept.department_id"
                                        class="mb-2 px-0"
                                    >
                                        <div
                                            class="d-flex justify-space-between align-center"
                                        >
                                            <div
                                                class="text-truncate mr-2"
                                                style="max-width: 150px"
                                            >
                                                <div
                                                    class="text-body-2 font-weight-bold"
                                                >
                                                    {{ dept.department_name }}
                                                </div>
                                                <div
                                                    class="text-caption text-slate-500"
                                                >
                                                    Salud:
                                                    {{
                                                        (
                                                            dept.health_score *
                                                            100
                                                        ).toFixed(0)
                                                    }}%
                                                </div>
                                            </div>
                                            <v-chip
                                                size="x-small"
                                                :color="
                                                    getRiskColor(
                                                        dept.risk_level,
                                                    )
                                                "
                                                class="font-weight-black"
                                            >
                                                {{ dept.risk_level }}
                                            </v-chip>
                                        </div>
                                        <v-progress-linear
                                            :model-value="
                                                dept.health_score * 100
                                            "
                                            :color="
                                                getRiskColor(dept.risk_level)
                                            "
                                            height="4"
                                            class="mt-1"
                                        ></v-progress-linear>
                                    </v-list-item>
                                </v-list>
                                <div v-else class="pa-4 text-center">
                                    <v-progress-circular
                                        indeterminate
                                        size="24"
                                        color="primary"
                                    ></v-progress-circular>
                                </div>
                            </v-card>
                        </v-col>

                        <!-- Visualization Panel -->
                        <v-col cols="12" md="8">
                            <div v-if="loading" class="pa-12 text-center">
                                <v-progress-circular
                                    indeterminate
                                    color="primary"
                                    size="64"
                                ></v-progress-circular>
                                <p
                                    class="text-h6 mt-4 animate-pulse text-slate-400"
                                >
                                    Modelando Reacciones en Cadena...
                                </p>
                            </div>

                            <div v-else-if="result" class="animate-fade-in">
                                <!-- Financial ROI Highlights -->
                                <div class="mb-6">
                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <v-card
                                                class="glass-card pa-6 border-success-glow"
                                            >
                                                <div
                                                    class="text-overline text-success"
                                                >
                                                    Ahorro en Reclutamiento
                                                </div>
                                                <div
                                                    class="text-h3 font-weight-black text-white"
                                                >
                                                    {{
                                                        formatCurrency(
                                                            result
                                                                .aggregated_metrics
                                                                ?.total_recruitment_savings ||
                                                                result
                                                                    .financial_impact
                                                                    ?.recruitment_avoidance_cost ||
                                                                0,
                                                        )
                                                    }}
                                                </div>
                                            </v-card>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-card
                                                class="glass-card pa-6 border-primary-glow"
                                            >
                                                <div
                                                    class="text-overline text-primary"
                                                >
                                                    ROI de Movilidad
                                                </div>
                                                <div
                                                    class="text-h3 font-weight-black text-white"
                                                >
                                                    {{
                                                        formatCurrency(
                                                            result
                                                                .aggregated_metrics
                                                                ?.total_roi_projected ||
                                                                result
                                                                    .financial_impact
                                                                    ?.roi_amount ||
                                                                0,
                                                        )
                                                    }}
                                                </div>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </div>

                                <!-- Domino Effect Section -->
                                <v-row v-if="result.domino_effect" class="mb-6">
                                    <v-col cols="12">
                                        <v-card
                                            class="glass-card pa-6 border-amber-glow"
                                        >
                                            <div
                                                class="d-flex align-center mb-4"
                                            >
                                                <v-icon
                                                    color="amber"
                                                    class="mr-3"
                                                    >mdi-domino-mask</v-icon
                                                >
                                                <h3
                                                    class="text-h6 font-weight-bold"
                                                >
                                                    Efecto Dominó: Sucesión
                                                    Interna
                                                </h3>
                                            </div>
                                            <p
                                                class="text-body-2 mb-4 text-slate-300"
                                            >
                                                {{
                                                    result.domino_effect
                                                        .impact_description
                                                }}
                                            </p>

                                            <div
                                                v-if="
                                                    result.domino_effect
                                                        .succession_candidates
                                                        .length > 0
                                                "
                                            >
                                                <div
                                                    class="text-overline mb-2 text-slate-400"
                                                >
                                                    Candidatos Sugeridos para:
                                                    {{
                                                        result.domino_effect
                                                            .vacant_role.name
                                                    }}
                                                </div>
                                                <v-row>
                                                    <v-col
                                                        v-for="cand in result
                                                            .domino_effect
                                                            .succession_candidates"
                                                        :key="cand.id"
                                                        cols="12"
                                                        md="4"
                                                    >
                                                        <v-card
                                                            variant="outlined"
                                                            class="pa-4 rounded-xl border-slate-700 bg-slate-900/50"
                                                        >
                                                            <div
                                                                class="text-body-1 font-weight-bold text-white"
                                                            >
                                                                {{ cand.name }}
                                                            </div>
                                                            <div
                                                                class="text-caption mb-2 text-slate-400"
                                                            >
                                                                {{
                                                                    cand.current_role
                                                                }}
                                                            </div>
                                                            <div
                                                                class="d-flex align-center"
                                                            >
                                                                <v-progress-linear
                                                                    :model-value="
                                                                        cand.fit_score *
                                                                        100
                                                                    "
                                                                    :color="
                                                                        getMetricColor(
                                                                            cand.fit_score,
                                                                        )
                                                                    "
                                                                    height="6"
                                                                    class="mr-2"
                                                                ></v-progress-linear>
                                                                <span
                                                                    class="text-caption font-weight-black"
                                                                    >{{
                                                                        (
                                                                            cand.fit_score *
                                                                            100
                                                                        ).toFixed(
                                                                            0,
                                                                        )
                                                                    }}%</span
                                                                >
                                                            </div>
                                                        </v-card>
                                                    </v-col>
                                                </v-row>
                                            </div>
                                        </v-card>
                                    </v-col>
                                </v-row>

                                <!-- Skill Gaps & Training Section -->
                                <v-row
                                    v-if="
                                        result.skill_gaps &&
                                        result.skill_gaps.length > 0
                                    "
                                    class="mb-6"
                                >
                                    <v-col cols="12">
                                        <v-card
                                            class="glass-card pa-6 border-indigo-glow"
                                        >
                                            <div
                                                class="d-flex align-center mb-4"
                                            >
                                                <v-icon
                                                    color="indigo-lighten-2"
                                                    class="mr-3"
                                                    >mdi-school</v-icon
                                                >
                                                <h3
                                                    class="text-h6 font-weight-bold"
                                                >
                                                    Brechas de Skill y Plan de
                                                    Upskilling
                                                </h3>
                                            </div>
                                            <p
                                                class="text-body-2 mb-4 text-slate-300"
                                            >
                                                Se han identificado habilidades
                                                que requieren desarrollo para
                                                asegurar el éxito en la nueva
                                                posición.
                                                <strong
                                                    >Al materializar este plan,
                                                    se creará automáticamente un
                                                    programa de
                                                    capacitación.</strong
                                                >
                                            </p>

                                            <v-row>
                                                <v-col
                                                    v-for="gap in result.skill_gaps"
                                                    :key="gap.skill_id"
                                                    cols="12"
                                                    md="4"
                                                >
                                                    <div
                                                        class="pa-3 rounded-lg border border-slate-700 bg-slate-800/50"
                                                    >
                                                        <div
                                                            class="d-flex justify-space-between align-center mb-2"
                                                        >
                                                            <span
                                                                class="text-caption font-weight-bold truncate"
                                                                style="
                                                                    max-width: 70%;
                                                                "
                                                                >{{
                                                                    gap.name
                                                                }}</span
                                                            >
                                                            <v-chip
                                                                v-if="
                                                                    gap.is_critical
                                                                "
                                                                size="x-small"
                                                                color="error"
                                                                variant="flat"
                                                                >Crítica</v-chip
                                                            >
                                                        </div>
                                                        <div
                                                            class="d-flex align-center"
                                                        >
                                                            <span
                                                                class="text-caption mr-2"
                                                                >{{
                                                                    gap.current_level
                                                                }}</span
                                                            >
                                                            <v-progress-linear
                                                                :model-value="
                                                                    (gap.current_level /
                                                                        gap.required_level) *
                                                                    100
                                                                "
                                                                color="indigo-lighten-2"
                                                                height="4"
                                                                class="rounded-pill"
                                                            ></v-progress-linear>
                                                            <span
                                                                class="text-caption font-weight-black ml-2 text-primary"
                                                                >{{
                                                                    gap.required_level
                                                                }}</span
                                                            >
                                                        </div>
                                                    </div>
                                                </v-col>
                                            </v-row>
                                        </v-card>
                                    </v-col>
                                </v-row>

                                <!-- Recommended Training (LMS Integration) -->
                                <v-row
                                    v-if="
                                        result.suggested_courses &&
                                        result.suggested_courses.length > 0
                                    "
                                    class="mb-6"
                                >
                                    <v-col cols="12">
                                        <v-card
                                            class="glass-card pa-6 border-emerald-glow"
                                        >
                                            <div
                                                class="d-flex align-center mb-4"
                                            >
                                                <v-icon
                                                    color="emerald-lighten-2"
                                                    class="mr-3"
                                                    >mdi-library-video</v-icon
                                                >
                                                <h3
                                                    class="text-h6 font-weight-bold"
                                                >
                                                    Plan de Desarrollo
                                                    Estratégico (LMS)
                                                </h3>
                                                <v-spacer></v-spacer>
                                                <v-chip
                                                    size="small"
                                                    color="emerald"
                                                    variant="tonal"
                                                    >Ruta de Upskilling
                                                    Optimizada</v-chip
                                                >
                                            </div>
                                            <p
                                                class="text-body-2 mb-4 text-slate-300"
                                            >
                                                Cursos recomendados para mitigar
                                                los riesgos de adaptación y
                                                acelerar el ROI de este
                                                movimiento.
                                            </p>
                                            <v-row>
                                                <v-col
                                                    v-for="course in result.suggested_courses"
                                                    :key="course.course_id"
                                                    cols="12"
                                                    md="4"
                                                >
                                                    <div
                                                        class="pa-4 rounded-xl border border-emerald-500/20 bg-emerald-500/5 hover-scale"
                                                    >
                                                        <div
                                                            class="text-subtitle-2 font-weight-black text-white truncate-2-lines mb-2"
                                                            style="height: 48px"
                                                        >
                                                            {{ course.title }}
                                                        </div>
                                                        <div
                                                            class="d-flex justify-space-between align-center"
                                                        >
                                                            <div>
                                                                <div
                                                                    class="text-tiny text-emerald-300 uppercase font-weight-bold"
                                                                >
                                                                    {{
                                                                        course.provider
                                                                    }}
                                                                </div>
                                                                <div
                                                                    v-if="
                                                                        course.skill
                                                                    "
                                                                    class="text-tiny text-slate-400"
                                                                >
                                                                    Skill:
                                                                    {{
                                                                        course.skill
                                                                    }}
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="text-right"
                                                            >
                                                                <div
                                                                    class="text-caption font-weight-black text-white"
                                                                >
                                                                    {{
                                                                        formatCurrency(
                                                                            course.cost,
                                                                        )
                                                                    }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </v-col>
                                            </v-row>
                                        </v-card>
                                    </v-col>
                                </v-row>

                                <!-- Succession Chain Visual Flow -->
                                <v-row
                                    v-if="
                                        result.succession_chain &&
                                        result.succession_chain.nodes.length > 1
                                    "
                                    class="mb-6"
                                >
                                    <v-col cols="12">
                                        <v-card
                                            class="glass-card pa-6 border-blue-glow"
                                        >
                                            <div
                                                class="d-flex align-center mb-6"
                                            >
                                                <v-icon
                                                    color="blue-lighten-2"
                                                    class="mr-3"
                                                    >mdi-graph-outline</v-icon
                                                >
                                                <h3
                                                    class="text-h6 font-weight-bold"
                                                >
                                                    Mapa de Sucesión en Cascada
                                                </h3>
                                                <v-spacer></v-spacer>
                                                <v-chip
                                                    size="small"
                                                    color="blue"
                                                    variant="tonal"
                                                    class="text-caption"
                                                >
                                                    Profundidad de Impacto:
                                                    {{
                                                        Math.max(
                                                            ...result.succession_chain.nodes.map(
                                                                (n) => n.level,
                                                            ),
                                                        ) + 1
                                                    }}
                                                    niveles
                                                </v-chip>
                                            </div>

                                            <div
                                                class="succession-flow-container py-4"
                                            >
                                                <div
                                                    class="d-flex align-center flex-nowrap gap-4 overflow-x-auto pb-4"
                                                >
                                                    <template
                                                        v-for="(
                                                            node, idx
                                                        ) in result
                                                            .succession_chain
                                                            .nodes"
                                                        :key="node.id"
                                                    >
                                                        <!-- Link Arrow -->
                                                        <v-icon
                                                            v-if="idx > 0"
                                                            color="grey-darken-1"
                                                            class="mx-2"
                                                            >mdi-chevron-right</v-icon
                                                        >

                                                        <!-- Node Card -->
                                                        <div
                                                            class="flow-node pa-4 d-flex flex-column align-center hover-scale justify-center rounded-xl border-2 text-center transition-all"
                                                            :class="[
                                                                node.type ===
                                                                'vacancy'
                                                                    ? 'border-amber-500/30 bg-amber-500/5'
                                                                    : 'border-blue-500/30 bg-blue-500/5',
                                                                'min-w-160',
                                                            ]"
                                                        >
                                                            <v-avatar
                                                                :color="
                                                                    node.type ===
                                                                    'vacancy'
                                                                        ? 'amber-lighten-4'
                                                                        : 'blue-lighten-4'
                                                                "
                                                                size="40"
                                                                class="mb-2"
                                                            >
                                                                <v-icon
                                                                    :color="
                                                                        node.type ===
                                                                        'vacancy'
                                                                            ? 'amber-darken-2'
                                                                            : 'blue-darken-2'
                                                                    "
                                                                >
                                                                    {{
                                                                        node.type ===
                                                                        'vacancy'
                                                                            ? 'mdi-briefcase-variant-outline'
                                                                            : 'mdi-account-check'
                                                                    }}
                                                                </v-icon>
                                                            </v-avatar>

                                                            <div
                                                                class="text-caption font-weight-black line-height-tight text-white"
                                                            >
                                                                {{ node.label }}
                                                            </div>
                                                            <div
                                                                class="text-tiny mt-1 tracking-widest text-slate-400 uppercase"
                                                            >
                                                                {{
                                                                    node.type ===
                                                                    'vacancy'
                                                                        ? 'Vacante'
                                                                        : 'Cubre Posición'
                                                                }}
                                                            </div>

                                                            <v-chip
                                                                v-if="node.fit"
                                                                size="x-small"
                                                                :color="
                                                                    getMetricColor(
                                                                        node.fit,
                                                                    )
                                                                "
                                                                class="mt-2"
                                                                variant="flat"
                                                            >
                                                                {{
                                                                    (
                                                                        node.fit *
                                                                        100
                                                                    ).toFixed(
                                                                        0,
                                                                    )
                                                                }}% Fit
                                                            </v-chip>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                            <p
                                                class="text-caption mt-2 text-slate-500 italic"
                                            >
                                                * Este mapa visualiza la
                                                reacción en cadena optimizada
                                                por Stratos para minimizar la
                                                fricción operativa.
                                            </p>
                                        </v-card>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <!-- Main Viability -->
                                    <v-col cols="12" md="6">
                                        <v-card
                                            class="glass-card pa-6 h-100 text-center"
                                        >
                                            <div class="text-overline mb-2">
                                                Viabilidad de Adaptación
                                            </div>
                                            <div
                                                class="success-circle-container mb-4"
                                            >
                                                <div
                                                    class="success-value"
                                                    :style="{
                                                        color: getMetricColor(
                                                            result.metrics
                                                                ?.success_probability ||
                                                                result
                                                                    .aggregated_metrics
                                                                    ?.success_probability ||
                                                                0,
                                                        ),
                                                    }"
                                                >
                                                    {{
                                                        (
                                                            (result.metrics
                                                                ?.success_probability ||
                                                                result
                                                                    .aggregated_metrics
                                                                    ?.success_probability ||
                                                                0) * 100
                                                        ).toFixed(0)
                                                    }}%
                                                </div>
                                            </div>
                                            <v-progress-linear
                                                :model-value="
                                                    (result.metrics
                                                        ?.success_probability ||
                                                        result
                                                            .aggregated_metrics
                                                            ?.success_probability ||
                                                        0) * 100
                                                "
                                                :color="
                                                    getMetricColor(
                                                        result.metrics
                                                            ?.success_probability ||
                                                            result
                                                                .aggregated_metrics
                                                                ?.success_probability ||
                                                            0,
                                                    )
                                                "
                                                height="12"
                                                rounded
                                            ></v-progress-linear>
                                        </v-card>
                                    </v-col>

                                    <!-- Insights -->
                                    <v-col cols="12" md="6">
                                        <v-card class="glass-card pa-6 h-100">
                                            <div
                                                class="d-flex align-center mb-4"
                                            >
                                                <v-icon
                                                    color="primary"
                                                    class="mr-3"
                                                    >mdi-molecule</v-icon
                                                >
                                                <h3
                                                    class="text-h6 font-weight-bold"
                                                >
                                                    Intelligence Insights
                                                </h3>
                                            </div>
                                            <v-list bg-color="transparent">
                                                <v-list-item
                                                    v-for="(
                                                        insight, i
                                                    ) in result.insights ||
                                                    result.group_insights"
                                                    :key="i"
                                                    class="px-0"
                                                >
                                                    <template v-slot:prepend>
                                                        <v-icon
                                                            color="primary"
                                                            size="small"
                                                            class="mr-2"
                                                            >mdi-check-decagram-outline</v-icon
                                                        >
                                                    </template>
                                                    <v-list-item-title
                                                        class="text-body-1 text-slate-200"
                                                        style="
                                                            white-space: normal;
                                                        "
                                                        >{{
                                                            insight
                                                        }}</v-list-item-title
                                                    >
                                                </v-list-item>
                                            </v-list>
                                        </v-card>
                                    </v-col>
                                </v-row>

                                <!-- Move-Set Breakdown -->
                                <v-row class="mt-4">
                                    <v-col cols="12">
                                        <v-card class="glass-card pa-6">
                                            <div class="text-overline mb-4">
                                                Move-Set Analysis
                                            </div>
                                            <v-table
                                                theme="dark"
                                                class="bg-transparent"
                                            >
                                                <thead>
                                                    <tr>
                                                        <th
                                                            scope="col"
                                                            class="text-left"
                                                        >
                                                            Colaborador
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="text-center"
                                                        >
                                                            Fit Score
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="text-center"
                                                        >
                                                            Costo Transición
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="text-center"
                                                        >
                                                            ROI Individual
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template
                                                        v-if="
                                                            result.individual_results
                                                        "
                                                    >
                                                        <tr
                                                            v-for="ind in result.individual_results"
                                                            :key="ind.person.id"
                                                        >
                                                            <td>
                                                                {{
                                                                    ind.person
                                                                        .name
                                                                }}
                                                            </td>
                                                            <td
                                                                class="text-center"
                                                                :style="{
                                                                    color: getMetricColor(
                                                                        ind
                                                                            .metrics
                                                                            .fit_score,
                                                                    ),
                                                                }"
                                                            >
                                                                {{
                                                                    (
                                                                        ind
                                                                            .metrics
                                                                            .fit_score *
                                                                        100
                                                                    ).toFixed(
                                                                        0,
                                                                    )
                                                                }}%
                                                            </td>
                                                            <td
                                                                class="text-red-lighten-2 text-center"
                                                            >
                                                                {{
                                                                    formatCurrency(
                                                                        ind
                                                                            .financial_impact
                                                                            .internal_transition_cost,
                                                                    )
                                                                }}
                                                            </td>
                                                            <td
                                                                class="text-green-lighten-2 font-weight-bold text-center"
                                                            >
                                                                {{
                                                                    formatCurrency(
                                                                        ind
                                                                            .financial_impact
                                                                            .roi_amount,
                                                                    )
                                                                }}
                                                            </td>
                                                        </tr>
                                                    </template>
                                                    <template
                                                        v-else-if="
                                                            result.person
                                                        "
                                                    >
                                                        <tr>
                                                            <td>
                                                                {{
                                                                    result
                                                                        .person
                                                                        .name
                                                                }}
                                                            </td>
                                                            <td
                                                                class="text-center"
                                                                :style="{
                                                                    color: getMetricColor(
                                                                        result
                                                                            .metrics
                                                                            ?.fit_score ||
                                                                            0,
                                                                    ),
                                                                }"
                                                            >
                                                                {{
                                                                    (
                                                                        (result
                                                                            .metrics
                                                                            ?.fit_score ||
                                                                            0) *
                                                                        100
                                                                    ).toFixed(
                                                                        0,
                                                                    )
                                                                }}%
                                                            </td>
                                                            <td
                                                                class="text-red-lighten-2 text-center"
                                                            >
                                                                {{
                                                                    formatCurrency(
                                                                        result
                                                                            .financial_impact
                                                                            ?.internal_transition_cost ||
                                                                            0,
                                                                    )
                                                                }}
                                                            </td>
                                                            <td
                                                                class="text-green-lighten-2 font-weight-bold text-center"
                                                            >
                                                                {{
                                                                    formatCurrency(
                                                                        result
                                                                            .financial_impact
                                                                            ?.roi_amount ||
                                                                            0,
                                                                    )
                                                                }}
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </v-table>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </div>

                            <div v-else class="pa-12 text-center">
                                <v-icon
                                    size="120"
                                    color="slate-800"
                                    class="mb-6"
                                    >mdi-hexagon-multiple</v-icon
                                >
                                <h2
                                    class="text-h5 font-weight-light text-slate-500"
                                >
                                    Inicie una simulación para proyectar la
                                    reacción en cadena
                                </h2>
                            </div>
                        </v-col>
                    </v-row>
                </v-window-item>

                <!-- Tab 2: Execution Status -->
                <v-window-item :value="1">
                    <div v-if="loadingTracking" class="pa-12 text-center">
                        <v-progress-circular
                            indeterminate
                            color="primary"
                            size="64"
                        ></v-progress-circular>
                        <p class="mt-4 text-slate-400">
                            Sincronizando estados de ejecución...
                        </p>
                    </div>

                    <div
                        v-else-if="executionTracking.length === 0"
                        class="pa-12 text-center"
                    >
                        <v-icon size="64" color="slate-600" class="mb-4"
                            >mdi-clipboard-text-search-outline</v-icon
                        >
                        <p class="text-h6 text-slate-500">
                            No se han materializado planes aún.
                        </p>
                        <p class="text-body-2 text-slate-600">
                            Diseñe un escenario en la pestaña de simulación para
                            comenzar.
                        </p>
                    </div>

                    <v-row v-else>
                        <v-col
                            v-for="track in executionTracking"
                            :key="track.id"
                            cols="12"
                        >
                            <v-card
                                class="glass-card pa-6 mb-4 border-slate-700"
                            >
                                <v-row align="center">
                                    <v-col cols="12" md="4">
                                        <div class="d-flex align-center mb-2">
                                            <v-chip
                                                size="small"
                                                :color="
                                                    track.status === 'applied'
                                                        ? 'success'
                                                        : 'amber'
                                                "
                                                class="font-weight-black mr-3"
                                            >
                                                {{ track.status.toUpperCase() }}
                                            </v-chip>
                                            <span
                                                class="text-caption text-slate-500"
                                                >{{ track.created_at }}</span
                                            >
                                        </div>
                                        <h3
                                            class="text-h6 font-weight-bold text-white"
                                        >
                                            {{ track.title }}
                                        </h3>
                                        <p class="text-caption text-indigo-300">
                                            Escenario: {{ track.scenario_name }}
                                        </p>
                                    </v-col>

                                    <v-col cols="12" md="3">
                                        <div
                                            class="text-overline mb-2 text-slate-400"
                                        >
                                            Progreso de Upskilling
                                        </div>
                                        <div
                                            v-if="
                                                track.development_progress &&
                                                track.development_progress
                                                    .length > 0
                                            "
                                        >
                                            <div
                                                v-for="(
                                                    dev, idx
                                                ) in track.development_progress"
                                                :key="idx"
                                                class="mb-3"
                                            >
                                                <div
                                                    class="d-flex justify-space-between text-caption mb-1"
                                                >
                                                    <span
                                                        class="text-truncate mr-2"
                                                        style="max-width: 120px"
                                                        >{{
                                                            dev.person_name
                                                        }}</span
                                                    >
                                                    <span
                                                        class="font-weight-bold"
                                                        >{{
                                                            dev.progress
                                                        }}%</span
                                                    >
                                                </div>
                                                <v-progress-linear
                                                    :model-value="dev.progress"
                                                    color="indigo-lighten-2"
                                                    height="6"
                                                    class="rounded-pill"
                                                ></v-progress-linear>
                                            </div>
                                        </div>
                                        <div
                                            v-else
                                            class="text-caption text-slate-600 italic"
                                        >
                                            Nivel Óptimo (Sin Gaps)
                                        </div>
                                    </v-col>

                                    <v-col cols="12" md="2">
                                        <div
                                            class="text-overline mb-2 text-slate-400"
                                        >
                                            Impacto Financiero
                                        </div>
                                        <div
                                            class="text-h6 font-weight-black text-emerald-400"
                                        >
                                            {{
                                                formatCurrency(
                                                    track.projected_roi,
                                                )
                                            }}
                                        </div>
                                        <div class="text-tiny text-slate-500">
                                            ROI Proyectado
                                        </div>
                                    </v-col>

                                    <v-col cols="12" md="3" class="text-right">
                                        <v-btn
                                            variant="tonal"
                                            color="primary"
                                            prepend-icon="mdi-eye-outline"
                                            class="font-weight-bold rounded-lg"
                                            @click="loadExecutionDetail(track.id)"
                                        >
                                            Ver Detalles
                                        </v-btn>
                                    </v-col>
                                </v-row>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-window-item>

                <!-- Tab 3: Gamification Dashboard -->
                <v-window-item :value="2">
                    <div class="pa-12">
                        <div class="mb-10 text-center">
                            <h2
                                class="text-h3 font-weight-black mb-3 bg-linear-to-r from-primary to-blue-400 bg-clip-text text-transparent italic"
                            >
                                Cultura de Aprendizaje & Gamificación
                            </h2>
                            <p class="text-h6 font-weight-light text-slate-400">
                                Visualice el compromiso de talento y los niveles
                                de expertise de la organización en tiempo real.
                            </p>
                        </div>
                        <GamificationDashboard />
                    </div>
                </v-window-item>
            </v-window>
        </v-container>

        <!-- AI Suggestions Dialog -->
        <v-dialog v-model="showAiDialog" max-width="800px" persistent>
            <v-card class="glass-card border-indigo-glow pa-6 overflow-hidden">
                <div class="d-flex justify-space-between align-center mb-6">
                    <div>
                        <h2 class="text-h5 font-weight-black text-white">
                            Estrategia de Movilidad Propuesta
                        </h2>
                        <p class="text-caption text-indigo-300">
                            Basado en el objetivo: "{{ aiObjective }}"
                        </p>
                    </div>
                    <v-btn
                        icon="mdi-close"
                        variant="text"
                        @click="showAiDialog = false"
                    ></v-btn>
                </div>

                <div
                    v-if="aiSuggestions"
                    class="mb-6 overflow-y-auto"
                    style="max-height: 60vh"
                >
                    <v-alert
                        type="info"
                        variant="tonal"
                        class="mb-6 border-indigo-800"
                        icon="mdi-brain"
                    >
                        <div class="text-body-2">
                            {{ aiSuggestions.strategic_rationale }}
                        </div>
                        <div class="font-weight-black mt-2">
                            ROI Estimado:
                            {{
                                formatCurrency(
                                    aiSuggestions.global_roi_estimate,
                                )
                            }}
                        </div>
                    </v-alert>

                    <h3 class="text-overline mb-4 text-slate-400">
                        Movimientos Recomendados
                    </h3>

                    <div
                        v-for="(prop, idx) in aiSuggestions.proposals"
                        :key="idx"
                        class="pa-4 hover-card mb-4 rounded-lg border border-slate-700 bg-slate-900"
                    >
                        <v-row align="center">
                            <v-col cols="12" md="8">
                                <div class="d-flex align-center mb-2">
                                    <span
                                        class="text-h6 font-weight-bold text-white"
                                        >{{ prop.person_name }}</span
                                    >
                                    <v-icon
                                        size="small"
                                        class="mx-3 text-indigo-400"
                                        >mdi-arrow-right-bold</v-icon
                                    >
                                    <span class="text-h6 text-indigo-200">{{
                                        prop.target_role_name
                                    }}</span>
                                </div>
                                <p class="text-body-2 text-slate-300 italic">
                                    "{{ prop.rationale }}"
                                </p>
                                <div class="mt-2">
                                    <v-chip
                                        v-for="skill in prop.upskilling_priority"
                                        :key="skill"
                                        size="x-small"
                                        color="amber"
                                        class="mr-1"
                                    >
                                        Gap: {{ skill }}
                                    </v-chip>
                                </div>
                            </v-col>
                            <v-col cols="12" md="4" class="text-right">
                                <v-btn
                                    color="primary"
                                    variant="tonal"
                                    size="small"
                                    @click="applyAiSuggestion(prop)"
                                >
                                    Proyectar este
                                </v-btn>
                            </v-col>
                        </v-row>
                    </div>
                </div>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="indigo-accent-2"
                        variant="elevated"
                        block
                        size="large"
                        prepend-icon="mdi-flash"
                        @click="applyAllAiSuggestions"
                    >
                        Aplicar Toda la Estrategia a Simulación
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Execution Detail Dialog -->
        <v-dialog v-model="showTrackingDialog" max-width="900px">
            <v-card class="glass-card pa-6 overflow-hidden">
                <div v-if="loadingDetail" class="pa-12 text-center">
                    <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                </div>
                <div v-else-if="trackingDetail" class="animate-fade-in">
                    <div class="d-flex justify-space-between align-center mb-6">
                        <div>
                            <h2 class="text-h5 font-weight-black text-white">Detalle de Ejecución</h2>
                            <p class="text-caption text-slate-400">{{ trackingDetail.changeset.title }}</p>
                        </div>
                        <v-btn icon="mdi-close" variant="text" @click="showTrackingDialog = false"></v-btn>
                    </div>

                    <v-row class="mb-6">
                        <v-col cols="12" md="4" v-for="path in trackingDetail.development_paths" :key="path.id">
                            <v-card class="bg-slate-900 border border-slate-700 pa-4 rounded-xl">
                                <div class="d-flex align-center mb-4">
                                    <v-avatar color="primary" size="40" class="mr-3">
                                        <span class="text-subtitle-2 font-weight-black">{{ path.people?.first_name?.[0] }}{{ path.people?.last_name?.[0] }}</span>
                                    </v-avatar>
                                    <div>
                                        <div class="text-body-1 font-weight-bold text-white">{{ path.people?.full_name }}</div>
                                        <div class="text-caption text-slate-500">{{ path.action_title }}</div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-space-between text-caption mb-1">
                                        <span>Progreso Plan</span>
                                        <span class="font-weight-bold">{{ 
                                            path.actions.length > 0 
                                            ? Math.round((path.actions.filter(a => a.status === 'completed').length / path.actions.length) * 100) 
                                            : 0 
                                        }}%</span>
                                    </div>
                                    <v-progress-linear 
                                        :model-value="(path.actions.filter(a => a.status === 'completed').length / path.actions.length) * 100" 
                                        color="primary" 
                                        height="8" 
                                        rounded
                                    ></v-progress-linear>
                                </div>

                                <div class="text-overline mb-2 text-slate-500">Acciones / Cursos LMS</div>
                                <div class="actions-list" style="max-height: 250px; overflow-y: auto;">
                                    <div v-for="action in path.actions" :key="action.id" class="mb-2 pa-2 rounded-lg bg-slate-800 border border-slate-700">
                                        <div class="d-flex justify-space-between align-center">
                                            <div class="text-caption font-weight-bold text-white text-truncate mr-2" style="max-width: 150px;">
                                                {{ action.title }}
                                            </div>
                                            <v-chip size="x-small" :color="action.status === 'completed' ? 'success' : (action.status === 'in_progress' ? 'amber' : 'slate-400')">
                                                {{ action.status }}
                                            </v-chip>
                                        </div>
                                        <div class="d-flex mt-2" v-if="action.type === 'lms_course'">
                                            <v-btn 
                                                v-if="action.status !== 'completed'"
                                                size="x-small" 
                                                color="primary" 
                                                variant="flat" 
                                                block 
                                                prepend-icon="mdi-play"
                                                @click="launchLms(action.id)"
                                            >
                                                Lanzar Curso
                                            </v-btn>
                                            <v-btn 
                                                v-if="action.status === 'in_progress'"
                                                size="x-small" 
                                                color="success" 
                                                variant="tonal" 
                                                block 
                                                class="mt-1"
                                                prepend-icon="mdi-refresh"
                                                @click="syncLmsProgress(action.id)"
                                            >
                                                Sincronizar
                                            </v-btn>
                                        </div>
                                    </div>
                                </div>
                            </v-card>
                        </v-col>
                    </v-row>
                </div>
            </v-card>
        </v-dialog>
    </div>
</template>

<style scoped>
.mobility-war-room {
    background: radial-gradient(circle at top right, #1e293b 0%, #020617 100%);
    min-height: 100vh;
    color: #f8fafc;
    font-family: 'Outfit', sans-serif;
}

.glass-card {
    background: rgba(30, 41, 59, 0.4);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 28px;
    box-shadow: 0 16px 32px -12px rgba(0, 0, 0, 0.6);
}

.border-primary-glow {
    border: 1px solid rgba(59, 130, 246, 0.4);
    box-shadow: 0 0 40px rgba(59, 130, 246, 0.15);
}

.border-success-glow {
    border: 1px solid rgba(16, 185, 129, 0.4);
    box-shadow: 0 0 40px rgba(16, 185, 129, 0.15);
}

.border-amber-glow {
    border: 1px solid rgba(245, 158, 11, 0.4);
    box-shadow: 0 0 40px rgba(245, 158, 11, 0.1);
}

.success-circle-container {
    height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    background: radial-gradient(
        circle,
        rgba(59, 130, 246, 0.1) 0%,
        transparent 70%
    );
}

.success-value {
    font-size: 4.5rem;
    font-weight: 900;
    text-shadow: 0 0 30px rgba(0, 0, 0, 0.8);
}

.animate-fade-in {
    animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-pulse {
    animation: pulse 2.5s infinite;
}

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.4;
    }
}

:deep(.v-table) {
    background: transparent !important;
}

:deep(.v-table .v-table__wrapper) {
    background: transparent !important;
    transform: scale(1.02);
}

.succession-flow-container {
    mask-image: linear-gradient(to right, black 85%, transparent 100%);
}

.min-w-160 {
    min-width: 160px;
}

.text-tiny {
    font-size: 0.65rem;
}

.line-height-tight {
    line-height: 1.1;
}

.gap-4 {
    gap: 16px;
}
</style>
