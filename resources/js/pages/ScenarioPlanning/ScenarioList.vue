<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import AppLayout from '@/layouts/AppLayout.vue';
import { useStrategicPlanningScenariosStore } from '@/stores/scenarioPlanningScenariosStore';
import { router } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import GenerateWizard from './GenerateWizard/GenerateWizard.vue';
import ScenarioCreateModal from './ScenarioCreateModal.vue';

defineOptions({ layout: AppLayout });

type ScenarioStatus = 'draft' | 'active' | 'completed' | 'archived';

type ScenarioListItem = {
    id: number;
    name: string;
    description: string;
    scenario_type: string;
    status: ScenarioStatus;
    decision_status?: 'draft' | 'pending_approval' | 'approved' | 'rejected';
    execution_status?: 'planned' | 'in_progress' | 'paused' | 'completed';
    is_current_version?: boolean;
    version_number?: number;
    parent_id?: number | null;
    time_horizon_weeks?: number;
    fiscal_year?: number;
    created_at: string;
};

const api = useApi();
const { showSuccess, showError } = useNotification();
const store = useStrategicPlanningScenariosStore();

const scenarios = ref<ScenarioListItem[]>([]);
const loading = ref(false);
const showCreateFromTemplate = ref(false);
const showGenerateWizard = ref(false);
const selectedScenarioIds = ref<number[]>([]);

const filters = ref<{ status: ScenarioStatus | null; type: string | null }>({
    status: null,
    type: null,
});

const statusOptions: ScenarioStatus[] = [
    'draft',
    'active',
    'completed',
    'archived',
];
const typeOptions = [
    { value: 'transformation', title: 'Transformación' },
    { value: 'growth', title: 'Crecimiento' },
    { value: 'automation', title: 'Automatización' },
    { value: 'merger', title: 'Fusión' },
    { value: 'custom', title: 'Personalizado' },
];

const tableHeaders = [
    { title: 'Nombre', key: 'name' },
    { title: 'Tipo', key: 'scenario_type' },
    { title: 'Estado (Decisión)', key: 'decision_status' },
    { title: 'Estado (Ejecución)', key: 'execution_status' },
    { title: 'Versión', key: 'version_number' },
    { title: 'Horizonte (semanas)', key: 'time_horizon_weeks' },
    { title: 'Acciones', key: 'actions', sortable: false },
];

const filteredScenarios = computed(() => {
    return scenarios.value.filter((s) => {
        const matchStatus = filters.value.status
            ? s.status === filters.value.status
            : true;
        const matchType = filters.value.type
            ? s.scenario_type === filters.value.type
            : true;
        return matchStatus && matchType;
    });
});

const statusColor = (status: ScenarioStatus) => {
    const map: Record<ScenarioStatus, 'warning' | 'success' | 'glass'> = {
        draft: 'warning',
        active: 'success',
        completed: 'glass',
        archived: 'glass',
    };
    return map[status] || 'glass';
};

// mark as referenced to avoid unused-var during refactor
void statusColor;

const decisionStatusColor = (
    status?: string,
): 'primary' | 'secondary' | 'error' | 'warning' | 'success' | 'glass' => {
    const map: Record<
        string,
        'primary' | 'secondary' | 'error' | 'warning' | 'success' | 'glass'
    > = {
        draft: 'glass',
        pending_approval: 'warning',
        approved: 'success',
        rejected: 'error',
    };
    return map[status || 'draft'] || 'glass';
};

const executionStatusColor = (
    status?: string,
): 'primary' | 'secondary' | 'error' | 'warning' | 'success' | 'glass' => {
    const map: Record<
        string,
        'primary' | 'secondary' | 'error' | 'warning' | 'success' | 'glass'
    > = {
        planned: 'glass',
        in_progress: 'primary',
        paused: 'warning',
        completed: 'success',
    };
    return map[status || 'planned'] || 'glass';
};

const decisionStatusText = (status?: string) => {
    const map: Record<string, string> = {
        draft: 'Borrador',
        pending_approval: 'Pendiente',
        approved: 'Aprobado',
        rejected: 'Rechazado',
    };
    return map[status || 'draft'];
};

const executionStatusText = (status?: string) => {
    const map: Record<string, string> = {
        planned: 'Planificado',
        in_progress: 'En ejecución',
        paused: 'Pausado',
        completed: 'Completado',
    };
    return map[status || 'planned'];
};

const loadScenarios = async () => {
    loading.value = true;
    try {
        const res: any = await api.get('/api/strategic-planning/scenarios', {
            params: {
                status: filters.value.status,
                type: filters.value.type,
            },
        });
        const data = Array.isArray(res)
            ? res
            : Array.isArray(res?.data?.items)
              ? res.data.items
              : Array.isArray(res?.items)
                ? res.items
                : Array.isArray(res?.data)
                  ? res.data
                  : [];
        scenarios.value = data;
        store.scenarios = data;
    } catch (e) {
        void e;
        showError('No se pudieron cargar los escenarios');
    } finally {
        loading.value = false;
    }
};

const goToDetail = (scenario: ScenarioListItem) => {
    router.visit(`/strategic-planning/${scenario.id}?view=map`);
};

const deleteScenario = async (scenario: ScenarioListItem) => {
    const ok = window.confirm(
        `¿Eliminar escenario "${scenario.name}"? Esta acción no se puede deshacer.`,
    );
    if (!ok) return;

    try {
        await api.delete(`/api/strategic-planning/scenarios/${scenario.id}`);
        showSuccess('Escenario eliminado');
        // reload list
        await loadScenarios();
    } catch (err: any) {
        showError(
            err?.response?.data?.message || 'Error al eliminar el escenario',
        );
    }
};

const openCreateFromTemplate = () => {
    showCreateFromTemplate.value = true;
};

onMounted(() => {
    loadScenarios();
});
</script>

<template>
    <div class="scenario-list-page">
        <v-container fluid>
            <v-row class="align-center mb-4">
                <v-col cols="12" md="6">
                    <h2 class="text-h4 font-weight-bold mb-2 text-white">
                        Planificación Estratégica de Escenarios
                    </h2>
                    <p class="mb-0 text-white/70">
                        Administra, filtra y navega escenarios con metodología 7
                        pasos
                    </p>
                </v-col>
            </v-row>

            <StCardGlass class="mb-6">
                <div class="pa-2">
                    <v-row>
                        <v-col cols="12" md="3">
                            <v-select
                                v-model="filters.status"
                                :items="statusOptions"
                                label="Estado (Legacy)"
                                clearable
                            />
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-select
                                v-model="filters.type"
                                :items="typeOptions"
                                item-title="title"
                                item-value="value"
                                label="Tipo de escenario"
                                clearable
                            />
                        </v-col>
                        <v-col cols="12" md="2" class="text-right">
                            <StButtonGlass
                                variant="ghost"
                                @click="loadScenarios"
                                icon="mdi-refresh"
                            >
                                Refrescar
                            </StButtonGlass>
                        </v-col>
                        <v-col
                            cols="12"
                            md="4"
                            class="d-flex justify-end gap-2"
                        >
                            <StButtonGlass
                                variant="glass"
                                icon="mdi-robot-excited-outline"
                                @click="showGenerateWizard = true"
                            >
                                Generar con IA
                            </StButtonGlass>
                            <StButtonGlass
                                variant="primary"
                                icon="mdi-plus"
                                @click="openCreateFromTemplate"
                            >
                                Nuevo escenario
                            </StButtonGlass>
                        </v-col>
                    </v-row>
                    <v-alert
                        type="info"
                        variant="tonal"
                        density="compact"
                        class="mt-2"
                    >
                        <strong>Estados duales:</strong> Los escenarios ahora
                        tienen estado de decisión
                        (borrador/pendiente/aprobado/rechazado) y estado de
                        ejecución (planificado/en progreso/pausado/completado).
                    </v-alert>
                </div>

                <v-data-table
                    :headers="tableHeaders"
                    :items="filteredScenarios"
                    :loading="loading"
                    item-key="id"
                    show-select
                    v-model:selected="selectedScenarioIds"
                    class="elevation-0 bg-transparent text-white"
                >
                    <template #[`item.decision_status`]="{ item }">
                        <StBadgeGlass
                            :variant="decisionStatusColor(item.decision_status)"
                        >
                            {{ decisionStatusText(item.decision_status) }}
                        </StBadgeGlass>
                    </template>

                    <template #[`item.execution_status`]="{ item }">
                        <StBadgeGlass
                            v-if="item.decision_status === 'approved'"
                            :variant="
                                executionStatusColor(item.execution_status)
                            "
                        >
                            {{ executionStatusText(item.execution_status) }}
                        </StBadgeGlass>
                        <StBadgeGlass v-else variant="glass">
                            N/A
                        </StBadgeGlass>
                    </template>

                    <template #[`item.version_number`]="{ item }">
                        <div v-if="item.version_number" class="text-caption">
                            <v-icon
                                icon="mdi-history"
                                size="x-small"
                                class="mr-1"
                            />
                            v{{ item.version_number }}
                            <StBadgeGlass
                                v-if="item.is_current_version"
                                variant="primary"
                                class="ml-1"
                            >
                                Actual
                            </StBadgeGlass>
                        </div>
                        <span v-else class="text-white/50">—</span>
                    </template>

                    <template #[`item.actions`]="{ item }">
                        <div class="d-flex gap-1">
                            <v-tooltip text="Ver detalle">
                                <template #activator="{ props }">
                                    <StButtonGlass
                                        v-bind="props"
                                        size="sm"
                                        variant="ghost"
                                        icon="mdi-eye"
                                        @click="goToDetail(item)"
                                    />
                                </template>
                            </v-tooltip>

                            <v-tooltip text="Borrar">
                                <template #activator="{ props }">
                                    <StButtonGlass
                                        v-bind="props"
                                        size="sm"
                                        variant="ghost"
                                        icon="mdi-delete"
                                        class="text-error border-none!"
                                        @click="deleteScenario(item)"
                                    />
                                </template>
                            </v-tooltip>

                            <v-tooltip
                                v-if="item.parent_id"
                                text="Escenario hijo"
                            >
                                <template #activator="{ props }">
                                    <v-icon
                                        v-bind="props"
                                        icon="mdi-file-tree"
                                        size="small"
                                        color="info"
                                    />
                                </template>
                            </v-tooltip>

                            <v-menu>
                                <template #activator="{ props }">
                                    <StButtonGlass
                                        v-bind="props"
                                        size="sm"
                                        variant="ghost"
                                        icon="mdi-dots-vertical"
                                    />
                                </template>

                                <v-list density="compact">
                                    <v-list-item
                                        v-if="
                                            item.decision_status ===
                                                'approved' &&
                                            item.is_current_version
                                        "
                                        prepend-icon="mdi-content-copy"
                                        title="Crear nueva versión"
                                        @click="goToDetail(item)"
                                    />
                                    <v-list-item
                                        v-if="
                                            item.decision_status !== 'approved'
                                        "
                                        prepend-icon="mdi-pencil"
                                        title="Editar"
                                        @click="goToDetail(item)"
                                    />
                                    <v-list-item
                                        v-if="item.parent_id"
                                        prepend-icon="mdi-sync"
                                        title="Sincronizar desde padre"
                                        @click="goToDetail(item)"
                                    />
                                    <v-divider />
                                    <v-list-item
                                        prepend-icon="mdi-history"
                                        title="Ver historial de versiones"
                                        @click="goToDetail(item)"
                                    />
                                </v-list>
                            </v-menu>
                        </div>
                    </template>

                    <template #no-data>
                        <div class="pa-6 text-center text-white/50">
                            No hay escenarios todavía. Crea uno desde plantilla.
                        </div>
                    </template>
                </v-data-table>
            </StCardGlass>
        </v-container>

        <v-dialog v-model="showCreateFromTemplate" max-width="900px">
            <ScenarioCreateModal
                @created="loadScenarios"
                @close="showCreateFromTemplate = false"
            />
        </v-dialog>

        <!-- Generate Wizard Dialog (AI-assisted scenario generation) -->
        <v-dialog
            v-model="showGenerateWizard"
            max-width="1100px"
            persistent
            scrollable
        >
            <v-card>
                <v-card-title
                    class="d-flex align-center justify-space-between pa-4"
                >
                    <div class="d-flex align-center gap-2">
                        <v-icon color="deep-purple" class="mr-2"
                            >mdi-robot-excited-outline</v-icon
                        >
                        <span>Asistente de Generación de Escenarios (IA)</span>
                    </div>
                    <v-btn
                        icon
                        variant="text"
                        @click="showGenerateWizard = false"
                    >
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider />
                <v-card-text class="pa-0">
                    <GenerateWizard
                        @scenario-created="
                            () => {
                                showGenerateWizard = false;
                                loadScenarios();
                            }
                        "
                    />
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>
