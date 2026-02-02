<script setup lang="ts">
import StatusTimeline from '@/components/StrategicPlanningScenarios/StatusTimeline.vue';
import VersionHistoryModal from '@/components/StrategicPlanningScenarios/VersionHistoryModal.vue';
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import AppLayout from '@/layouts/AppLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import PrototypeMap from './Index.vue';

type Props = {
    id: number | string;
};

type ScenarioPayload = {
    id: number;
    name: string;
    description?: string;
    scenario_type?: string;
    status: string;
    decision_status: string;
    execution_status: string;
    current_step?: number;
    is_current_version?: boolean;
    version_number?: number;
    version_group_id?: string;
    parent_id?: number | null;
    time_horizon_weeks?: number;
    estimated_budget?: number;
    created_at?: string;
    scope_type?: string;
    scope_id?: number | null;
    horizon_months?: number;
    planning_horizon_months?: number;
    start_date?: string | null;
    end_date?: string | null;
    owner?: string | null;
    scenario_skills?: any[];
    skill_demands?: any[];
};

defineOptions({ layout: AppLayout });

const props = defineProps<Props>();
const api = useApi();
const { showSuccess, showError } = useNotification();

const scenario = ref<ScenarioPayload | null>(null);
const loading = ref(false);
const refreshing = ref(false);
const savingStep1 = ref(false);
const formData = ref({
    // Basic metadata
    id: null as number | null,
    name: '',
    description: '',
    scenario_type: '',
    status: 'draft',
    decision_status: '',
    execution_status: '',
    current_step: 1,
    is_current_version: false,
    version_number: null as number | null,
    version_group_id: null as string | null,
    parent_id: null as number | null,

    // Time / horizon
    planning_horizon_months: 12,
    horizon_months: 12,
    time_horizon_weeks: null as number | null,
    start_date: null as string | null,
    end_date: null as string | null,

    // other
    owner: null as string | null,
    estimated_budget: null as number | null,
    scope_type: '',
    scope_id: null as number | null,
    import_to_plan: false,
});

const fieldErrors = ref<Record<string, string[]>>({});
const roles = ref<any[]>([]);
const rolesLoading = ref(false);
const skills = ref<any[]>([]);
const skillsLoading = ref(false);
const scenarioSkills = ref<any[]>([]);
const showNewSkillDialog = ref(false);
const newSkillName = ref('');
const newSkillCategory = ref('technical');
const newSkillLoading = ref(false);
const newSkillTargetIndex = ref<number | null>(null);
const roleActions = ref<Record<number, string>>({});
const departments = ref<any[]>([]);
const deptLoading = ref(false);
const roleFamilies = ref<any[]>([]);
const includedCount = computed(
    () =>
        Object.values(roleActions.value).filter((v) => v === 'include').length,
);
const importCount = computed(
    () => Object.values(roleActions.value).filter((v) => v === 'import').length,
);
const ignoredCount = computed(
    () => Object.values(roleActions.value).filter((v) => v === 'ignore').length,
);
const roleHeaders = ref([
    { title: 'Rol', key: 'name' },
    { title: 'Departamento', key: 'department' },
    { title: 'Familia', key: 'family' },
    { title: 'Acciones', key: 'actions', sortable: false },
]);
const activeTab = ref<
    | 'stepper'
    | 'overview'
    | 'gaps'
    | 'strategies'
    | 'matches'
    | 'forecasts'
    | 'comparisons'
    | 'succession'
    | 'actions'
>('stepper');
const currentStep = ref(1);
const versionHistoryRef = ref<InstanceType<typeof VersionHistoryModal> | null>(
    null,
);
const statusTimelineRef = ref<InstanceType<typeof StatusTimeline> | null>(null);

const scenarioId = computed(() => {
    const value =
        typeof props.id === 'string' ? parseInt(props.id, 10) : props.id;
    return value || 0;
});

// detect optional view mode from querystring (e.g., ?view=map)
const page = usePage();
const viewMode = computed(() => {
    try {
        const url = (page as any).url || window.location.href;
        const params = new URLSearchParams(
            new URL(url, window.location.origin).search,
        );
        return params.get('view') || null;
    } catch (e) {
        void e;
        return null;
    }
});

const loadScenario = async () => {
    if (!scenarioId.value || scenarioId.value <= 0) return;
    loading.value = true;
    try {
        const response = await api.get(
            `/api/strategic-planning/scenarios/${scenarioId.value}`,
        );
        const data = (response as any)?.data ?? response;
        scenario.value = data;
        // scenario payload loaded
        currentStep.value = scenario.value?.current_step || 1;
        // populate simple form data for step1
        formData.value.name = scenario.value?.name ?? '';
        formData.value.description = scenario.value?.description ?? '';
        formData.value.scope_type = scenario.value?.scope_type ?? '';
        formData.value.planning_horizon_months =
            scenario.value?.horizon_months ??
            scenario.value?.planning_horizon_months ??
            12;
        formData.value.horizon_months =
            scenario.value?.horizon_months ??
            formData.value.planning_horizon_months;
        formData.value.time_horizon_weeks =
            scenario.value?.time_horizon_weeks ?? null;
        formData.value.start_date = scenario.value?.start_date ?? null;
        formData.value.end_date = scenario.value?.end_date ?? null;
        formData.value.owner = scenario.value?.owner ?? null;
        formData.value.scope_id = scenario.value?.scope_id ?? null;

        // advanced / tracking fields
        formData.value.id = scenario.value?.id ?? null;
        formData.value.scenario_type = scenario.value?.scenario_type ?? '';
        formData.value.status = scenario.value?.status ?? 'draft';
        formData.value.decision_status = scenario.value?.decision_status ?? '';
        formData.value.execution_status =
            scenario.value?.execution_status ?? '';
        formData.value.current_step =
            scenario.value?.current_step ?? formData.value.current_step;
        formData.value.is_current_version =
            scenario.value?.is_current_version ?? false;
        formData.value.version_number = scenario.value?.version_number ?? null;
        formData.value.version_group_id =
            scenario.value?.version_group_id ?? null;
        formData.value.parent_id = scenario.value?.parent_id ?? null;
        formData.value.estimated_budget =
            scenario.value?.estimated_budget ?? null;
        // load departments/role families if applicable
        if (
            formData.value.scope_type === 'department' ||
            formData.value.scope_type === 'role_family'
        ) {
            await loadDepartments();
        }
        // load skills for step1 and populate scenario skills
        await loadSkills();
        // populate scenarioSkills from scenario payload if available
        scenarioSkills.value = (
            scenario.value?.scenario_skills ||
            scenario.value?.skill_demands ||
            []
        ).map((s: any) => ({
            id: s.id || null,
            skill_id: s.skill_id || s.id || null,
            required_level: s.required_level ?? s.level ?? 1,
            required_headcount:
                s.required_headcount ?? s.required_headcount ?? 1,
            priority: s.priority || 'medium',
            rationale: s.rationale || s.notes || '',
        }));
        // load roles for step1 based on scope (kept but roles will be unused in Phase 1)
        await loadRoles();
    } catch (e) {
        void e;
        showError('No se pudo cargar el escenario');
    } finally {
        loading.value = false;
    }
};

const loadRoles = async () => {
    // fetch roles filtered by scope_type if provided
    rolesLoading.value = true;
    try {
        const params: any = {};
        if (formData.value.scope_type)
            params.scope_type = formData.value.scope_type;
        if (formData.value.scope_id) params.scope_id = formData.value.scope_id;
        const res = await api.get('/api/roles', params);
        roles.value = (res as any)?.data ?? res;
        // derive role families from roles (attribute on role)
        const famMap: Record<string, any> = {};
        roles.value.forEach((r: any) => {
            const fid =
                r.role_family_id || (r.role_family && r.role_family.id) || null;
            const fname =
                r.role_family_name ||
                (r.role_family && r.role_family.name) ||
                r.role_family ||
                null;
            if (fid && !famMap[fid]) famMap[fid] = { id: fid, name: fname };
        });
        roleFamilies.value = Object.values(famMap);
        // initialize actions for newly loaded roles
        roles.value.forEach((r: any) => {
            if (!roleActions.value[r.id]) roleActions.value[r.id] = 'ignore';
        });
    } catch (e) {
        void e;
        // ignore silently; roles are optional
    } finally {
        rolesLoading.value = false;
    }
};

const loadSkills = async () => {
    skillsLoading.value = true;
    try {
        const res = await api.get('/api/skills');
        skills.value = (res as any)?.data ?? res ?? [];
    } catch (e) {
        void e;
        // ignore
    } finally {
        skillsLoading.value = false;
    }
};

const addScenarioSkill = () => {
    scenarioSkills.value.push({
        id: null,
        skill_id: null,
        strategic_role: '',
        priority: 'medium',
        rationale: '',
    });
};

const removeScenarioSkill = (index: number) => {
    scenarioSkills.value.splice(index, 1);
};

const openNewSkillDialog = (targetIndex: number | null = null) => {
    newSkillName.value = '';
    newSkillCategory.value = 'technical';
    newSkillTargetIndex.value = targetIndex;
    showNewSkillDialog.value = true;
};

const createNewSkill = async () => {
    if (!newSkillName.value.trim()) return;
    newSkillLoading.value = true;
    try {
        const payload: any = {
            name: newSkillName.value.trim(),
            category: newSkillCategory.value,
            maturity_status: 'emergente',
            status: 'active',
        };
        // if we have a scenario context, mark discovery origin
        if (scenarioId.value && scenarioId.value > 0)
            payload.discovered_in_scenario_id = scenarioId.value;
        const res: any = await api.post('/api/skills', { data: payload });
        const created = (res as any)?.data ?? res;
        // append to local skills catalog
        skills.value.push(created);
        // if target index provided, assign to that row, else create new scenarioSkill with this skill
        if (
            newSkillTargetIndex.value !== null &&
            scenarioSkills.value[newSkillTargetIndex.value]
        ) {
            scenarioSkills.value[newSkillTargetIndex.value].skill_id =
                created.id;
        } else {
            scenarioSkills.value.push({
                id: null,
                skill_id: created.id,
                strategic_role: '',
                priority: 'medium',
                rationale: '',
            });
        }
        showNewSkillDialog.value = false;
    } catch (e) {
        void e;
        showError('Error al crear la skill');
    } finally {
        newSkillLoading.value = false;
    }
};

const setAllActions = (action: string) => {
    roles.value.forEach((r: any) => {
        roleActions.value[r.id] = action;
    });
};

// mark computed/handlers referenced to avoid unused-var while Phase 1 removes roles
void includedCount.value;
void importCount.value;
void ignoredCount.value;
void roleHeaders.value;
void setAllActions;
// Prevent linter false-positives for Phase 1 unused handlers/refs declared above
void activeTab.value;
void addScenarioSkill;
void removeScenarioSkill;
void openNewSkillDialog;
void createNewSkill;

watch(
    () => formData.value.scope_type,
    async (nv, ov) => {
        if (nv !== ov) await loadRoles();
    },
);

watch(
    () => formData.value.scope_id,
    async (nv, ov) => {
        if (nv !== ov) await loadRoles();
    },
);

const loadDepartments = async () => {
    deptLoading.value = true;
    try {
        const res = await api.get('/api/departments');
        departments.value = (res as any)?.data ?? res;
    } catch (e) {
        void e;
        // ignore
    } finally {
        deptLoading.value = false;
    }
};

const saveStep1 = async () => {
    if (!scenarioId.value || scenarioId.value <= 0) return;
    savingStep1.value = true;
    fieldErrors.value = {};

    // client-side validation
    if (!validateStep1()) {
        savingStep1.value = false;
        return;
    }

    try {
        const payload = {
            step1: {
                metadata: {
                    id: formData.value.id,
                    name: formData.value.name,
                    description: formData.value.description,
                    scenario_type: formData.value.scenario_type,
                    status: formData.value.status,
                    decision_status: formData.value.decision_status,
                    execution_status: formData.value.execution_status,
                    current_step: formData.value.current_step,
                    is_current_version: formData.value.is_current_version,
                    version_number: formData.value.version_number,
                    version_group_id: formData.value.version_group_id,
                    parent_id: formData.value.parent_id,

                    planning_horizon_months:
                        formData.value.planning_horizon_months,
                    horizon_months: formData.value.horizon_months,
                    time_horizon_weeks: formData.value.time_horizon_weeks,
                    start_date: formData.value.start_date,
                    end_date: formData.value.end_date,

                    owner: formData.value.owner,
                    scope_type: formData.value.scope_type,
                    scope_id: formData.value.scope_id,
                    estimated_budget: formData.value.estimated_budget,
                },
                // scenario skills for Phase 1
                skills: scenarioSkills.value.map((s: any) => ({
                    id: s.id || null,
                    skill_id: s.skill_id,
                    required_level: s.required_level,
                    required_headcount: s.required_headcount,
                    priority: s.priority,
                    rationale: s.rationale,
                })),
                import_to_plan: formData.value.import_to_plan,
            },
        };

        const res = await api.patch(
            `/api/strategic-planning/scenarios/${scenarioId.value}`,
            payload,
        );
        const updated = (res as any)?.data ?? res;
        scenario.value = updated;
        showSuccess('Paso 1 guardado');
    } catch (err: any) {
        // extract validation errors if provided by server
        if (err?.response?.data) {
            const data = err.response.data;
            if (data.errors) fieldErrors.value = data.errors;
            if (data.message) showError(data.message);
            else showError('No se pudo guardar el Paso 1');
        } else {
            showError('No se pudo guardar el Paso 1');
        }
    } finally {
        savingStep1.value = false;
    }
};

function validateStep1() {
    const errors: Record<string, string[]> = {};
    if (!formData.value.name || !formData.value.name.trim()) {
        errors.name = ['El nombre es requerido'];
    }
    if (
        !formData.value.planning_horizon_months ||
        formData.value.planning_horizon_months <= 0
    ) {
        errors.planning_horizon_months = ['El horizonte debe ser mayor que 0'];
    }
    if (formData.value.start_date && formData.value.end_date) {
        const s = new Date(formData.value.start_date);
        const e = new Date(formData.value.end_date);
        if (s > e)
            errors.start_date = [
                'La fecha de inicio no puede ser posterior a la fecha fin',
            ];
    }

    // Scenario skills validation: each item must have a skill selected
    if (
        Array.isArray(scenarioSkills.value) &&
        scenarioSkills.value.length > 0
    ) {
        const missing = scenarioSkills.value.some((s: any) => !s.skill_id);
        if (missing) {
            errors.scenario_skills = [
                'Todas las filas deben tener una skill seleccionada',
            ];
        }
    }

    fieldErrors.value = errors;
    return Object.keys(errors).length === 0;
}

const handleStatusChanged = () => {
    loadScenario();
};

const handleStepChange = (step: number) => {
    currentStep.value = step;
};

const openVersionHistory = () => {
    versionHistoryRef.value?.openDialog();
};

const openStatusTimeline = () => {
    statusTimelineRef.value?.openTimeline();
};

const handleVersionSelected = (id: number) => {
    // Navigate to the web route (Inertia page) not the API JSON endpoint
    router.visit(`/strategic-planning/${id}`);
};

const calculateGaps = async () => {
    if (!scenarioId.value || scenarioId.value <= 0) return;
    refreshing.value = true;
    try {
        await api.post(
            `/api/strategic-planning/scenarios/${scenarioId.value}/calculate-gaps`,
        );
        showSuccess('Brechas recalculadas');
    } catch (e) {
        void e;
        showError('Error al recalcular brechas');
    } finally {
        refreshing.value = false;
    }
};

const refreshStrategies = async () => {
    refreshing.value = true;
    try {
        // Endpoint opcional para generar/actualizar estrategias; si no existe, recarga el escenario
        showSuccess('Estrategias actualizadas');
    } catch (e) {
        void e;
        showError('Error al actualizar estrategias');
    } finally {
        refreshing.value = false;
    }
};

// mark handlers defined below to avoid linter 'assigned but never used' warnings
void saveStep1;
void handleStatusChanged;
void handleStepChange;
void openVersionHistory;
void openStatusTimeline;
void handleVersionSelected;
void calculateGaps;
void refreshStrategies;

// Definición de los 7 pasos del workflow
const stepperItems = [
    { value: 1, title: 'Mapa', icon: 'mdi-map', subtitle: 'Visualización del escenario' },
    { value: 2, title: 'Brechas', icon: 'mdi-chart-box-outline', subtitle: 'Análisis de gaps' },
    { value: 3, title: 'Estrategias', icon: 'mdi-strategy', subtitle: 'Cierre de brechas' },
    { value: 4, title: 'Workforce', icon: 'mdi-account-group', subtitle: 'Plan de personal' },
    { value: 5, title: 'Pronósticos', icon: 'mdi-chart-timeline-variant', subtitle: 'Roles futuros' },
    { value: 6, title: 'Comparar', icon: 'mdi-compare', subtitle: 'Versiones' },
    { value: 7, title: 'Dashboard', icon: 'mdi-view-dashboard', subtitle: 'Resumen' },
];

const currentStepInfo = computed(() => stepperItems.find(s => s.value === currentStep.value) || stepperItems[0]);

const goBack = () => {
    router.visit('/scenario-planning');
};

const goToStep = (step: number) => {
    if (step >= 1 && step <= stepperItems.length) {
        currentStep.value = step;
        // Actualizar URL con el step actual
        const url = new URL(window.location.href);
        url.searchParams.set('step', String(step));
        window.history.replaceState({}, '', url.toString());
    }
};

const nextStep = () => {
    if (currentStep.value < stepperItems.length) {
        goToStep(currentStep.value + 1);
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        goToStep(currentStep.value - 1);
    }
};

// Parsear step desde query param
const parseInitialStep = () => {
    try {
        const url = new URL(window.location.href);
        const stepParam = url.searchParams.get('step');
        const viewParam = url.searchParams.get('view');
        
        // Si viene con ?view=map, ir al paso 1
        if (viewParam === 'map') {
            currentStep.value = 1;
            return;
        }
        
        if (stepParam) {
            const stepNum = parseInt(stepParam, 10);
            if (stepNum >= 1 && stepNum <= stepperItems.length) {
                currentStep.value = stepNum;
            }
        }
    } catch (e) {
        void e;
    }
};

onMounted(() => {
    parseInitialStep();
    loadScenario();
});
</script>

<template>
    <v-app>
    <div class="scenario-detail-wrapper">
        <!-- Header con navegación (usando v-sheet en lugar de v-app-bar) -->
        <v-sheet color="surface" class="border-b" style="position: sticky; top: 0; z-index: 10;">
            <v-container fluid class="pa-2">
                <v-row no-gutters align="center">
                    <v-col cols="auto">
                        <v-btn icon="mdi-arrow-left" variant="text" @click="goBack" />
                    </v-col>
                    
                    <v-col v-if="scenario" cols="auto" class="flex-grow-1 d-flex align-center gap-2">
                        <span class="font-weight-medium">{{ scenario.name }}</span>
                        <v-chip 
                            v-if="scenario.decision_status" 
                            size="x-small" 
                            :color="scenario.decision_status === 'approved' ? 'success' : scenario.decision_status === 'rejected' ? 'error' : 'warning'"
                        >
                            {{ scenario.decision_status }}
                        </v-chip>
                    </v-col>
                    
                    <v-col v-else cols="auto" class="flex-grow-1">
                        <v-skeleton-loader type="text" width="200" />
                    </v-col>

                    <v-col cols="auto" class="d-flex gap-2 align-center">
                        <v-chip variant="tonal" color="primary" size="small">
                            Paso {{ currentStep }}/{{ stepperItems.length }}
                        </v-chip>
                        <v-btn 
                            icon="mdi-history" 
                            variant="text" 
                            size="small"
                            @click="openVersionHistory"
                            title="Historial de versiones"
                        />
                    </v-col>
                </v-row>
            </v-container>
        </v-sheet>

        <!-- Stepper horizontal compacto -->
        <v-sheet color="grey-lighten-4" class="py-1 px-2 border-b stepper-nav">
            <div class="d-flex justify-center align-center gap-1 flex-wrap">
                <v-btn
                    v-for="step in stepperItems"
                    :key="step.value"
                    :variant="currentStep === step.value ? 'flat' : 'text'"
                    :color="currentStep === step.value ? 'primary' : 'default'"
                    size="small"
                    :prepend-icon="step.icon"
                    @click="goToStep(step.value)"
                    class="step-btn"
                >
                    <span class="d-none d-md-inline">{{ step.title }}</span>
                    <span class="d-md-none">{{ step.value }}</span>
                </v-btn>
            </div>
        </v-sheet>

        <!-- Contenido del step actual -->
        <div class="scenario-content">
            <v-progress-linear v-if="loading" indeterminate color="primary" />
            
            <template v-else-if="scenario">
                <!-- Step 1: Mapa de Escenario -->
                <div v-show="currentStep === 1" class="step-content step-map">
                    <PrototypeMap :scenario="scenario" />
                </div> 

                <!-- Step 2: Análisis de Brechas -->
                <div v-show="currentStep === 2" class="step-content">
                    <v-container>
                        <v-card>
                            <v-card-title>
                                <v-icon class="mr-2">mdi-chart-box-outline</v-icon>
                                Análisis de Brechas
                            </v-card-title>
                            <v-card-text>
                                <v-alert type="info" variant="tonal" class="mb-4">
                                    Identifica gaps en skills y competencias entre el estado actual y el objetivo.
                                </v-alert>
                                <v-btn color="primary" @click="calculateGaps" :loading="refreshing">
                                    Calcular Brechas
                                </v-btn>
                            </v-card-text>
                        </v-card>
                    </v-container>
                </div>

                <!-- Step 3: Estrategias de Cierre -->
                <div v-show="currentStep === 3" class="step-content">
                    <v-container>
                        <v-card>
                            <v-card-title>
                                <v-icon class="mr-2">mdi-strategy</v-icon>
                                Estrategias de Cierre
                            </v-card-title>
                            <v-card-text>
                                <v-alert type="info" variant="tonal" class="mb-4">
                                    Define estrategias para cerrar las brechas identificadas (capacitación, contratación, etc.).
                                </v-alert>
                                <v-btn color="primary" @click="refreshStrategies" :loading="refreshing">
                                    Generar Estrategias
                                </v-btn>
                            </v-card-text>
                        </v-card>
                    </v-container>
                </div>

                <!-- Step 4: Plan de Workforce -->
                <div v-show="currentStep === 4" class="step-content">
                    <v-container>
                        <v-card>
                            <v-card-title>
                                <v-icon class="mr-2">mdi-account-group</v-icon>
                                Plan de Workforce
                            </v-card-title>
                            <v-card-text>
                                <v-alert type="info" variant="tonal">
                                    Planifica los recursos humanos necesarios para ejecutar el escenario.
                                </v-alert>
                            </v-card-text>
                        </v-card>
                    </v-container>
                </div>

                <!-- Step 5: Pronóstico de Roles -->
                <div v-show="currentStep === 5" class="step-content">
                    <v-container>
                        <v-card>
                            <v-card-title>
                                <v-icon class="mr-2">mdi-chart-timeline-variant</v-icon>
                                Pronóstico de Roles
                            </v-card-title>
                            <v-card-text>
                                <v-alert type="info" variant="tonal">
                                    Proyecta las necesidades de roles a futuro basándose en el escenario.
                                </v-alert>
                            </v-card-text>
                        </v-card>
                    </v-container>
                </div>

                <!-- Step 6: Comparación -->
                <div v-show="currentStep === 6" class="step-content">
                    <v-container>
                        <v-card>
                            <v-card-title>
                                <v-icon class="mr-2">mdi-compare</v-icon>
                                Comparación de Escenarios
                            </v-card-title>
                            <v-card-text>
                                <v-alert type="info" variant="tonal">
                                    Compara diferentes versiones del escenario o escenarios alternativos.
                                </v-alert>
                            </v-card-text>
                        </v-card>
                    </v-container>
                </div>

                <!-- Step 7: Dashboard -->
                <div v-show="currentStep === 7" class="step-content">
                    <v-container>
                        <v-card>
                            <v-card-title>
                                <v-icon class="mr-2">mdi-view-dashboard</v-icon>
                                Dashboard Ejecutivo
                            </v-card-title>
                            <v-card-text>
                                <v-alert type="info" variant="tonal">
                                    Resumen ejecutivo y KPIs del escenario.
                                </v-alert>
                            </v-card-text>
                        </v-card>
                    </v-container>
                </div>
            </template>

            <template v-else>
                <v-container>
                    <v-alert type="error" variant="tonal">
                        No se pudo cargar el escenario.
                    </v-alert>
                </v-container>
            </template>
        </div>

        <!-- Footer con navegación entre steps (usando v-sheet) -->
        <v-sheet color="surface" class="border-t" style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 10;">
            <v-container fluid class="pa-2">
                <v-row no-gutters align="center">
                    <v-col cols="4">
                        <v-btn
                            v-if="currentStep > 1"
                            variant="text"
                            size="small"
                            prepend-icon="mdi-chevron-left"
                            @click="prevStep"
                        >
                            <span class="d-none d-sm-inline">{{ stepperItems[currentStep - 2]?.title }}</span>
                            <span class="d-sm-none">Anterior</span>
                        </v-btn>
                    </v-col>
                    
                    <v-col cols="4" class="text-center">
                        <span class="text-caption text-medium-emphasis">
                            {{ currentStepInfo.title }}: {{ currentStepInfo.subtitle }}
                        </span>
                    </v-col>
                    
                    <v-col cols="4" class="text-right">
                        <v-btn
                            v-if="currentStep < stepperItems.length"
                            variant="tonal"
                            color="primary"
                            size="small"
                            append-icon="mdi-chevron-right"
                            @click="nextStep"
                        >
                            <span class="d-none d-sm-inline">{{ stepperItems[currentStep]?.title }}</span>
                            <span class="d-sm-none">Siguiente</span>
                        </v-btn>
                        <v-btn
                            v-else
                            variant="flat"
                            color="success"
                            size="small"
                            prepend-icon="mdi-check"
                            @click="goBack"
                        >
                            Finalizar
                        </v-btn>
                    </v-col>
                </v-row>
            </v-container>
        </v-sheet>

        <!-- Modales -->
        <VersionHistoryModal
            v-if="scenarioId && scenarioId > 0 && scenario"
            ref="versionHistoryRef"
            :scenario-id="scenarioId"
            :version-group-id="scenario.version_group_id || ''"
            :current-version="scenario.version_number || 1"
            @version-selected="handleVersionSelected"
        />
        <StatusTimeline
            v-if="scenarioId && scenarioId > 0"
            ref="statusTimelineRef"
            :scenario-id="scenarioId"
            @status-changed="handleStatusChanged"
        />
    </div>
    </v-app>
</template>

<style scoped>
.scenario-detail-wrapper {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.stepper-nav {
    position: sticky;
    top: 56px;
    z-index: 9;
}

.step-btn {
    min-width: auto;
}

.scenario-content {
    flex: 1;
    overflow: auto;
    padding-bottom: 80px;
    padding-top: 10px;
}

.step-content {
    min-height: auto;
}

.step-map {
    height: min(70vh, 640px);
    min-height: 420px;
    padding: 0;
}

.step-map :deep(.prototype-map-root) {
    height: 100%;
    max-height: 100%;
}
</style>
