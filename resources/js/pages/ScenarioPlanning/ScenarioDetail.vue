<script setup lang="ts">
import IncubatedReviewModal from '@/components/IncubatedReviewModal.vue';
import ChangeSetModal from '@/components/ScenarioPlanning/ChangeSetModal.vue';
import StatusTimeline from '@/components/ScenarioPlanning/StatusTimeline.vue';
import IncubatedCubeReview from '@/components/ScenarioPlanning/Step2/IncubatedCubeReview.vue';
import RoleCompetencyMatrix from '@/components/ScenarioPlanning/Step2/RoleCompetencyMatrix.vue';
import VersionHistoryModal from '@/components/ScenarioPlanning/VersionHistoryModal.vue';
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import AppLayout from '@/layouts/AppLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import GenerateWizard from './GenerateWizard/GenerateWizard.vue';
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
    created_by?: number | null;
    scope_type?: string;
    scope_id?: number | null;
    horizon_months?: number;
    planning_horizon_months?: number;
    start_date?: string | null;
    end_date?: string | null;
    owner?: string | null;
    scenario_skills?: any[];
    skill_demands?: any[];
    source_generation_id?: number | null;
    accepted_prompt?: string | null;
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
const scenarioSkills = ref<any[]>([]);
const roleActions = ref<Record<number, string>>({});
const departments = ref<any[]>([]);
const deptLoading = ref(false);
const roleFamilies = ref<any[]>([]);
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const includedCount = computed(
    () =>
        Object.values(roleActions.value).filter((v) => v === 'include').length,
);
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const importCount = computed(
    () => Object.values(roleActions.value).filter((v) => v === 'import').length,
);
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const ignoredCount = computed(
    () => Object.values(roleActions.value).filter((v) => v === 'ignore').length,
);
// eslint-disable-next-line @typescript-eslint/no-unused-vars
const roleHeaders = ref([
    { title: 'Rol', key: 'name' },
    { title: 'Departamento', key: 'department' },
    { title: 'Familia', key: 'family' },
    { title: 'Acciones', key: 'actions', sortable: false },
]);
// eslint-disable-next-line @typescript-eslint/no-unused-vars
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
const showChangeSet = ref(false);
const changeSetId = ref<number | null>(null);
const creatingChangeSet = ref(false);
const statusTimelineRef = ref<InstanceType<typeof StatusTimeline> | null>(null);
const showGenerateWizard = ref(false);
const showIncubatedReview = ref(false);
const selectedIncubated = ref<any | null>(null);
const showAcceptedPromptDialog = ref(false);

const scenarioId = computed(() => {
    const value =
        typeof props.id === 'string' ? Number.parseInt(props.id, 10) : props.id;
    return Number.isNaN(value) ? 0 : value;
});

// Compute whether the current user can view the accepted prompt (defensive UI-side check)
const page = usePage();
const canViewAcceptedPrompt = computed(() => {
    try {
        const user = (page as any).props?.auth?.user ?? null;
        if (!user) return false;
        const role = user.role ?? null;
        if (['admin', 'approver', 'owner', 'superadmin'].includes(role))
            return true;
        // allow if user is creator/owner of the scenario (frontend best-effort)
        return scenario.value
            ? (scenario.value.created_by ?? null) === user.id
            : false;
    } catch {
        return false;
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
        // load capability tree (incubated entities)
        await loadCapabilityTree();
        // load import audit for associated generation (if any)
        if (scenario.value?.source_generation_id) await loadImportAudit();
    } catch {
        showError('No se pudo cargar el escenario');
    } finally {
        loading.value = false;
    }
};

const incubatedTree = ref<any[]>([]);
const loadingTree = ref(false);

const importAudit = ref<any[]>([]);
const loadingImportAudit = ref(false);

const loadCapabilityTree = async () => {
    if (!scenarioId.value || scenarioId.value <= 0) return;
    loadingTree.value = true;
    try {
        const res: any = await api.get(
            `/api/strategic-planning/scenarios/${scenarioId.value}/capability-tree`,
        );
        incubatedTree.value = (res as any)?.data ?? res ?? [];
    } catch (e) {
        console.error('Error loading capability tree', e);
        incubatedTree.value = [];
    } finally {
        loadingTree.value = false;
    }
};

const openIncubatedReview = (cap: any) => {
    selectedIncubated.value = cap;
    showIncubatedReview.value = true;
};

const onPromoted = async () => {
    // refresh tree after single promote
    await loadCapabilityTree();
    // also refresh the scenario to update counts
    await loadScenario();
};

const openAcceptedPromptDialog = async () => {
    if (!scenario.value || !scenario.value.source_generation_id) return;
    showAcceptedPromptDialog.value = true;
    try {
        await api.post(
            `/api/strategic-planning/scenarios/generate/${scenario.value.source_generation_id}/record-view`,
        );
    } catch (e) {
        // ignore errors; non-blocking
        console.error('record view failed', e);
    }
};

const loadImportAudit = async () => {
    if (!scenario.value || !scenario.value.source_generation_id) return;
    loadingImportAudit.value = true;
    try {
        const res: any = await api.get(
            `/api/strategic-planning/scenarios/generate/${scenario.value.source_generation_id}`,
        );
        const data = (res as any)?.data ?? res;
        importAudit.value = data?.metadata?.import_audit ?? [];
    } catch (e) {
        console.error('Error loading import audit', e);
        importAudit.value = [];
    } finally {
        loadingImportAudit.value = false;
    }
};

const goToCompetency = (id: number) => {
    router.visit(`/competencies/${id}`);
};

const promoteIncubated = async () => {
    if (!scenarioId.value) return;
    try {
        await api.post(
            `/api/strategic-planning/scenarios/${scenarioId.value}/approve`,
        );
        showSuccess('Entidades incubadas promovidas');
        await loadScenario();
    } catch (e) {
        console.error(e);
        showError('Error promoviendo entidades');
    }
};

const simulateLLM = async () => {
    if (!scenarioId.value) return;
    loading.value = true;
    try {
        await api.post(
            `/api/strategic-planning/scenarios/${scenarioId.value}/simulate-import`,
        );
        showSuccess(
            'Simulación de respuesta LLM completada. Estructura generada.',
        );
        // Refresh full state
        await loadScenario();
        // Force refresh of child components if needed
    } catch (e: any) {
        console.error(e);
        showError(
            'Error al simular importación: ' +
                (e.response?.data?.message || e.message),
        );
    } finally {
        loading.value = false;
    }
};

const refreshStrategies = async () => {
    refreshing.value = true;
    try {
        // En el futuro esto regenerará las estrategias basadas en brechas
        await new Promise((resolve) => setTimeout(resolve, 1000));
        showSuccess('Estrategias de cierre actualizadas');
    } catch (e) {
        console.error(e);
        showError('Error al actualizar las estrategias');
    } finally {
        refreshing.value = false;
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
    } catch {
        // ignore silently; roles are optional
    } finally {
        rolesLoading.value = false;
    }
};

// eslint-disable-next-line @typescript-eslint/no-unused-vars
const setAllActions = (action: string) => {
    roles.value.forEach((r: any) => {
        roleActions.value[r.id] = action;
    });
};

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
    } catch {
        // ignore
    } finally {
        deptLoading.value = false;
    }
};

// eslint-disable-next-line @typescript-eslint/no-unused-vars
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

const openVersionHistory = () => {
    versionHistoryRef.value?.openDialog();
};

const closeChangeSetModal = () => {
    showChangeSet.value = false;
    changeSetId.value = null;
};

const openChangeSetModal = async () => {
    if (!scenarioId.value || scenarioId.value <= 0) return;
    creatingChangeSet.value = true;
    try {
        const res: any = await api.post(
            `/api/strategic-planning/scenarios/${scenarioId.value}/change-sets`,
            {},
        );
        const cs = (res as any)?.data ?? res;
        // accommodate both { data: cs } and direct cs responses
        changeSetId.value = cs?.id ?? (cs?.data && cs.data.id) ?? null;
        if (changeSetId.value) showChangeSet.value = true;
        else showError('No se pudo obtener el ChangeSet');
    } catch (e) {
        console.error(e);
        const friendly =
            (e as any)?.friendlyMessage ||
            (e as any)?.message ||
            'No se pudo generar el ChangeSet';
        showError(friendly);
    } finally {
        creatingChangeSet.value = false;
    }
};

const handleVersionSelected = (id: number) => {
    // Navigate to the web route (Inertia page) not the API JSON endpoint
    router.visit(`/strategic-planning/${id}`);
};

// Definición de los 7 pasos del workflow
const stepperItems = [
    {
        value: 1,
        title: 'Mapa',
        icon: 'mdi-map',
        subtitle: 'Visualización del escenario',
    },
    {
        value: 2,
        title: 'Mapeo',
        icon: 'mdi-table',
        subtitle: 'Roles ↔ Competencias',
    },
    {
        value: 3,
        title: 'Estrategias',
        icon: 'mdi-strategy',
        subtitle: 'Cierre de brechas',
    },
    {
        value: 4,
        title: 'Escenarios',
        icon: 'mdi-account-group',
        subtitle: 'Plan de personal',
    },
    {
        value: 5,
        title: 'Pronósticos',
        icon: 'mdi-chart-timeline-variant',
        subtitle: 'Roles futuros',
    },
    { value: 6, title: 'Comparar', icon: 'mdi-compare', subtitle: 'Versiones' },
    {
        value: 7,
        title: 'Dashboard',
        icon: 'mdi-view-dashboard',
        subtitle: 'Resumen',
    },
];

const currentStepInfo = computed(
    () =>
        stepperItems.find((s) => s.value === currentStep.value) ||
        stepperItems[0],
);

const goBack = () => {
    router.visit('/scenario-planning');
};

const goToStep = (step: number) => {
    if (step >= 1 && step <= stepperItems.length) {
        currentStep.value = step;
        // Actualizar URL con el step actual
        const url = new URL(globalThis.location.href);
        url.searchParams.set('step', String(step));
        globalThis.history.replaceState({}, '', url.toString());
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
        const url = new URL(globalThis.location.href);
        const stepParam = url.searchParams.get('step');
        const viewParam = url.searchParams.get('view');

        // Si viene con ?view=map, ir al paso 1
        if (viewParam === 'map') {
            currentStep.value = 1;
            return;
        }

        if (stepParam) {
            const stepNum = Number.parseInt(stepParam, 10);
            if (stepNum >= 1 && stepNum <= stepperItems.length) {
                currentStep.value = stepNum;
            }
        }
    } catch {}
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
            <v-sheet
                color="surface"
                class="border-b"
                style="position: sticky; top: 0; z-index: 10"
            >
                <v-container fluid class="pa-2">
                    <v-row no-gutters align="center">
                        <v-col cols="auto">
                            <v-btn
                                icon="mdi-arrow-left"
                                variant="text"
                                @click="goBack"
                            />
                        </v-col>

                        <v-col
                            v-if="scenario"
                            cols="auto"
                            class="d-flex align-center flex-grow-1 gap-2"
                        >
                            <span class="font-weight-medium">{{
                                scenario.name
                            }}</span>
                            <v-btn
                                v-if="
                                    canViewAcceptedPrompt &&
                                    scenario.source_generation_id &&
                                    scenario.accepted_prompt
                                "
                                small
                                variant="text"
                                @click="openAcceptedPromptDialog"
                                >Ver prompt aceptado</v-btn
                            >
                            <v-chip
                                v-if="scenario.decision_status"
                                size="x-small"
                                :color="
                                    scenario.decision_status === 'approved'
                                        ? 'success'
                                        : scenario.decision_status ===
                                            'rejected'
                                          ? 'error'
                                          : 'warning'
                                "
                            >
                                {{ scenario.decision_status }}
                            </v-chip>
                        </v-col>

                        <v-col v-else cols="auto" class="flex-grow-1">
                            <v-skeleton-loader type="text" width="200" />
                        </v-col>

                        <v-col cols="auto" class="d-flex align-center gap-2">
                            <v-chip
                                variant="tonal"
                                color="primary"
                                size="small"
                            >
                                Paso {{ currentStep }}/{{ stepperItems.length }}
                            </v-chip>
                            <v-btn
                                icon="mdi-history"
                                variant="text"
                                size="small"
                                @click="openVersionHistory"
                                title="Historial de versiones"
                            />
                            <v-btn
                                icon="mdi-source-branch"
                                variant="text"
                                size="small"
                                :loading="creatingChangeSet"
                                @click="openChangeSetModal"
                                title="ChangeSet"
                            />
                            <v-btn
                                icon="mdi-robot"
                                variant="text"
                                size="small"
                                data-test="generate-wizard-button"
                                @click="showGenerateWizard = true"
                                title="Generar escenario"
                            />
                        </v-col>
                    </v-row>
                </v-container>
            </v-sheet>

            <!-- Stepper horizontal compacto -->
            <v-sheet
                color="grey-lighten-4"
                class="stepper-nav border-b px-2 py-1"
            >
                <div class="d-flex align-center flex-wrap justify-center gap-1">
                    <v-btn
                        v-for="step in stepperItems"
                        :key="step.value"
                        :variant="currentStep === step.value ? 'flat' : 'text'"
                        :color="
                            currentStep === step.value ? 'primary' : 'default'
                        "
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
                <v-progress-linear
                    v-if="loading"
                    indeterminate
                    color="primary"
                />

                <template v-else-if="scenario">
                    <!-- Step 1: Mapa de Escenario -->
                    <div
                        v-show="currentStep === 1"
                        class="step-content step-map"
                    >
                        <div
                            class="mb-4 rounded border-l-4 border-blue-500 bg-blue-50 p-4"
                        >
                            <div class="d-flex align-start gap-3">
                                <v-icon
                                    icon="mdi-information"
                                    class="mt-1 text-blue-600"
                                />
                                <div>
                                    <h3
                                        class="font-weight-semibold mb-1 text-blue-800"
                                    >
                                        ¿En qué consiste el Paso 1?
                                    </h3>
                                    <p class="text-body-2 mb-0 text-blue-700">
                                        Diseñar el escenario definiendo
                                        capacidades estratégicas y competencias
                                        (existentes o nuevas) que darán base al
                                        mapa del escenario.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <PrototypeMap :scenario="scenario" />
                        <div class="mt-4">
                            <v-card class="pa-3">
                                <v-card-title>
                                    <v-icon class="mr-2">mdi-seed</v-icon>
                                    Entidades incubadas en este escenario
                                    <v-spacer />
                                    <v-btn
                                        small
                                        variant="text"
                                        @click="promoteIncubated"
                                        title="Promover incubadas"
                                        >Promover todas</v-btn
                                    >
                                </v-card-title>
                                <v-card-text>
                                    <div v-if="loadingTree">
                                        <v-progress-linear
                                            indeterminate
                                            color="primary"
                                        />
                                    </div>
                                    <div v-else>
                                        <div v-if="incubatedTree.length === 0">
                                            No se encontraron entidades
                                            incubadas.
                                        </div>
                                        <div v-else>
                                            <div
                                                v-for="cap in incubatedTree"
                                                :key="cap.id"
                                                class="mb-3"
                                            >
                                                <div
                                                    class="d-flex align-center justify-space-between"
                                                >
                                                    <div>
                                                        <strong>{{
                                                            cap.name
                                                        }}</strong>
                                                        <div
                                                            class="text-sm text-gray-600"
                                                        >
                                                            {{
                                                                cap.description
                                                            }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <v-btn
                                                            size="x-small"
                                                            variant="text"
                                                            @click="
                                                                openIncubatedReview(
                                                                    cap,
                                                                )
                                                            "
                                                            >Revisar</v-btn
                                                        >
                                                    </div>
                                                </div>
                                                <div class="mt-2 ml-4">
                                                    <div
                                                        v-for="comp in cap.competencies"
                                                        :key="comp.id"
                                                        class="mb-1"
                                                    >
                                                        <div
                                                            class="d-flex align-center gap-2"
                                                        >
                                                            <span
                                                                class="text-sm"
                                                                >•
                                                                {{
                                                                    comp.name
                                                                }}</span
                                                            >
                                                            <v-btn
                                                                size="x-small"
                                                                variant="text"
                                                                @click="
                                                                    goToCompetency(
                                                                        comp.id,
                                                                    )
                                                                "
                                                                >Ver/Editar</v-btn
                                                            >
                                                        </div>
                                                        <div
                                                            class="ml-4 text-sm text-gray-700"
                                                        >
                                                            <span
                                                                v-for="s in comp.skills"
                                                                :key="s.id"
                                                                class="mr-3"
                                                                >{{ s.name }}
                                                                <small
                                                                    v-if="
                                                                        s.is_incubating
                                                                    "
                                                                    class="text-yellow-700"
                                                                    >(incubation)</small
                                                                ></span
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </v-card-text>

                                <v-card-text>
                                    <div
                                        v-if="
                                            scenario &&
                                            scenario.source_generation_id
                                        "
                                    >
                                        <h4 class="mb-2">
                                            Registro de importación
                                        </h4>
                                        <div v-if="loadingImportAudit">
                                            <v-progress-linear
                                                indeterminate
                                                color="primary"
                                            />
                                        </div>
                                        <div v-else>
                                            <div
                                                v-if="importAudit.length === 0"
                                            >
                                                No hay registros de importación.
                                            </div>
                                            <div v-else>
                                                <div
                                                    v-for="(
                                                        entry, idx
                                                    ) in importAudit"
                                                    :key="idx"
                                                    class="mb-3"
                                                >
                                                    <div
                                                        class="text-sm text-gray-700"
                                                    >
                                                        <strong>{{
                                                            entry.attempted_at ||
                                                            entry.attempted ||
                                                            '—'
                                                        }}</strong>
                                                        —
                                                        <em>{{
                                                            entry.attempted_by ||
                                                            'Sistema'
                                                        }}</em>
                                                    </div>
                                                    <div>
                                                        Resultado:
                                                        <strong>{{
                                                            entry.result
                                                        }}</strong>
                                                    </div>
                                                    <div
                                                        v-if="entry.error"
                                                        class="mt-1"
                                                    >
                                                        <div
                                                            class="text-sm text-red-700"
                                                        >
                                                            Error:
                                                        </div>
                                                        <pre class="text-sm">{{
                                                            JSON.stringify(
                                                                entry.error,
                                                                null,
                                                                2,
                                                            )
                                                        }}</pre>
                                                    </div>
                                                    <div
                                                        v-if="entry.report"
                                                        class="mt-1"
                                                    >
                                                        <div
                                                            class="text-sm text-green-700"
                                                        >
                                                            Reporte:
                                                        </div>
                                                        <pre class="text-sm">{{
                                                            JSON.stringify(
                                                                entry.report,
                                                                null,
                                                                2,
                                                            )
                                                        }}</pre>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </v-card-text>
                            </v-card>
                        </div>
                    </div>

                    <!-- Step 2: Mapeo Roles-Competencias -->
                    <div v-if="currentStep === 2" class="step-content">
                        <div v-if="scenarioId > 0">
                            <!-- Puente de Transición / Fase de Conciliación -->
                            <div
                                v-if="
                                    [
                                        'draft',
                                        'incubating',
                                        'incubated',
                                    ].includes(scenario?.status)
                                "
                                class="mb-8 overflow-hidden rounded-xl border border-indigo-100 bg-gradient-to-br from-white to-indigo-50/30 shadow-sm"
                            >
                                <div
                                    class="d-flex align-center justify-space-between bg-indigo-50/50 px-6 py-4"
                                >
                                    <div>
                                        <h3
                                            class="text-h6 font-weight-bold mb-0 text-indigo-900"
                                        >
                                            Fase de Conciliación Estratégica
                                        </h3>
                                        <div
                                            class="text-caption text-indigo-700"
                                        >
                                            Puente de Transición: Valida el
                                            diseño incubado antes de la
                                            ingeniería de detalle.
                                        </div>
                                    </div>
                                    <v-btn
                                        size="small"
                                        variant="flat"
                                        color="indigo-darken-2"
                                        @click="simulateLLM"
                                        prepend-icon="mdi-auto-fix"
                                        class="text-none"
                                    >
                                        Simular IA (Full Concept)
                                    </v-btn>
                                </div>

                                <div class="pa-6">
                                    <IncubatedCubeReview
                                        :scenario-id="Number(scenarioId)"
                                        :key="`incubated-${scenarioId}-${scenario?.updated_at || ''}`"
                                        @approved="loadScenario"
                                    />
                                </div>

                                <v-divider class="my-8" />
                                <h3
                                    class="text-h6 font-weight-bold mb-4 text-slate-800"
                                >
                                    Matriz de Ingeniería de Roles (Pivote Final)
                                </h3>
                            </div>

                            <!-- Estado: Diseño Consolidado -->
                            <div
                                v-else
                                class="mb-8 flex items-center justify-between rounded-xl border border-emerald-100 bg-emerald-50/30 p-6"
                            >
                                <div class="flex items-center gap-4">
                                    <div
                                        class="rounded-full bg-emerald-500 p-2"
                                    >
                                        <v-icon color="white"
                                            >mdi-check-all</v-icon
                                        >
                                    </div>
                                    <div>
                                        <h4
                                            class="text-lg font-bold text-emerald-900"
                                        >
                                            Diseño Consolidado
                                        </h4>
                                        <p class="text-sm text-emerald-700">
                                            La estructura ha sido aprobada.
                                            Ahora puedes precisar las brechas en
                                            la matriz inferior.
                                        </p>
                                    </div>
                                </div>
                                <v-btn
                                    variant="text"
                                    color="emerald-darken-2"
                                    size="small"
                                    prepend-icon="mdi-history"
                                    @click="scenario.status = 'incubating'"
                                >
                                    Reabrir Laboratorio
                                </v-btn>
                            </div>

                            <RoleCompetencyMatrix
                                :scenario-id="scenarioId"
                                :key="`rcm-${scenarioId}-${currentStep}-${scenario?.updated_at || ''}`"
                            />
                        </div>
                        <div v-else class="flex justify-center py-8">
                            <v-progress-circular
                                indeterminate
                                color="primary"
                            ></v-progress-circular>
                            <span class="ml-2">Cargando escenario...</span>
                        </div>
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
                                    <v-alert
                                        type="info"
                                        variant="tonal"
                                        class="mb-4"
                                    >
                                        Define estrategias para cerrar las
                                        brechas identificadas (capacitación,
                                        contratación, etc.).
                                    </v-alert>
                                    <v-btn
                                        color="primary"
                                        @click="refreshStrategies"
                                        :loading="refreshing"
                                    >
                                        Generar Estrategias
                                    </v-btn>
                                </v-card-text>
                            </v-card>
                        </v-container>
                    </div>

                    <!-- Step 4: Plan de Personal -->
                    <div v-show="currentStep === 4" class="step-content">
                        <v-container>
                            <v-card>
                                <v-card-title>
                                    <v-icon class="mr-2"
                                        >mdi-account-group</v-icon
                                    >
                                    Plan de Personal
                                </v-card-title>
                                <v-card-text>
                                    <v-alert type="info" variant="tonal">
                                        Planifica los recursos humanos
                                        necesarios para ejecutar el escenario.
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
                                    <v-icon class="mr-2"
                                        >mdi-chart-timeline-variant</v-icon
                                    >
                                    Pronóstico de Roles
                                </v-card-title>
                                <v-card-text>
                                    <v-alert type="info" variant="tonal">
                                        Proyecta las necesidades de roles a
                                        futuro basándose en el escenario.
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
                                        Compara diferentes versiones del
                                        escenario o escenarios alternativos.
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
                                    <v-icon class="mr-2"
                                        >mdi-view-dashboard</v-icon
                                    >
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
            <v-sheet
                color="surface"
                class="border-t"
                style="
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    z-index: 10;
                "
            >
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
                                <span class="d-none d-sm-inline">{{
                                    stepperItems[currentStep - 2]?.title
                                }}</span>
                                <span class="d-sm-none">Anterior</span>
                            </v-btn>
                        </v-col>

                        <v-col cols="4" class="text-center">
                            <span class="text-caption text-medium-emphasis">
                                {{ currentStepInfo.title }}:
                                {{ currentStepInfo.subtitle }}
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
                                <span class="d-none d-sm-inline">{{
                                    stepperItems[currentStep]?.title
                                }}</span>
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
            <v-dialog v-model="showChangeSet" max-width="900" scrollable>
                <v-card>
                    <v-card-text>
                        <ChangeSetModal
                            v-if="changeSetId"
                            :id="changeSetId"
                            title="ChangeSet"
                            @close="closeChangeSetModal"
                        />
                    </v-card-text>
                </v-card>
            </v-dialog>
            <v-dialog v-model="showGenerateWizard" max-width="1000">
                <v-card>
                    <v-card-text>
                        <GenerateWizard />
                    </v-card-text>
                </v-card>
            </v-dialog>
            <IncubatedReviewModal
                v-model:modal="showIncubatedReview"
                :item="selectedIncubated"
                :scenario-id="scenarioId"
                @close="showIncubatedReview = false"
                @promoted="onPromoted"
            />

            <v-dialog v-model="showAcceptedPromptDialog" max-width="800">
                <v-card>
                    <v-card-title>Prompt aceptado</v-card-title>
                    <v-card-text>
                        <pre v-if="scenario">{{
                            scenario.accepted_prompt
                        }}</pre>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn text @click="showAcceptedPromptDialog = false"
                            >Cerrar</v-btn
                        >
                    </v-card-actions>
                </v-card>
            </v-dialog>
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
