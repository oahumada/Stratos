<script setup lang="ts">
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import { computed, ref } from 'vue';

interface Props {
    scenario: {
        id: number;
        decision_status: string;
        execution_status: string;
        current_step?: number;
        is_current_version?: boolean;
        parent_id?: number | null;
    };
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'refresh'): void;
    (e: 'statusChanged'): void;
}>();

const api = useApi();
const { showSuccess, showError } = useNotification();

const loading = ref(false);
const showTransitionDialog = ref(false);
const showExecutionDialog = ref(false);
const showVersionDialog = ref(false);
const transitionNotes = ref('');
const executionNotes = ref('');
const targetStatus = ref('');

// Dialog state para crear versión
const versionName = ref('');
const versionDescription = ref('');
const versionNotes = ref('');
const copySkills = ref(true);
const copyStrategies = ref(true);

// Botones de transición de decision_status
const decisionTransitions = computed(() => {
    const status = props.scenario.decision_status;
    const buttons: Array<{
        label: string;
        color: string;
        icon: string;
        toStatus: string;
        disabled: boolean;
    }> = [];

    if (status === 'draft') {
        buttons.push({
            label: 'Enviar a Aprobación',
            color: 'primary',
            icon: 'mdi-send',
            toStatus: 'pending_approval',
            disabled: false,
        });
    }

    if (status === 'pending_approval') {
        buttons.push(
            {
                label: 'Aprobar',
                color: 'success',
                icon: 'mdi-check-circle',
                toStatus: 'approved',
                disabled: false,
            },
            {
                label: 'Rechazar',
                color: 'error',
                icon: 'mdi-close-circle',
                toStatus: 'rejected',
                disabled: false,
            },
        );
    }

    if (status === 'rejected') {
        buttons.push({
            label: 'Volver a Borrador',
            color: 'grey',
            icon: 'mdi-undo',
            toStatus: 'draft',
            disabled: false,
        });
    }

    return buttons;
});

// Botones de ejecución
const executionActions = computed(() => {
    const execStatus = props.scenario.execution_status;
    const decisionStatus = props.scenario.decision_status;
    const buttons: Array<{
        label: string;
        color: string;
        icon: string;
        action: string;
        disabled: boolean;
        tooltip?: string;
    }> = [];

    if (decisionStatus !== 'approved') {
        return []; // No mostrar botones de ejecución si no está aprobado
    }

    if (execStatus === 'planned' || execStatus === 'paused') {
        buttons.push({
            label: 'Iniciar Ejecución',
            color: 'success',
            icon: 'mdi-play',
            action: 'start',
            disabled: false,
        });
    }

    if (execStatus === 'in_progress') {
        buttons.push(
            {
                label: 'Pausar',
                color: 'warning',
                icon: 'mdi-pause',
                action: 'pause',
                disabled: false,
            },
            {
                label: 'Completar',
                color: 'primary',
                icon: 'mdi-check-bold',
                action: 'complete',
                disabled: false,
            },
        );
    }

    return buttons;
});

const canCreateVersion = computed(() => {
    return (
        props.scenario.decision_status === 'approved' &&
        props.scenario.is_current_version
    );
});

const canEdit = computed(() => {
    return props.scenario.decision_status !== 'approved';
});

// `canDelete` removed — not used in template

const canSyncFromParent = computed(() => {
    return (
        props.scenario.parent_id !== null &&
        props.scenario.decision_status !== 'approved'
    );
});

// Transicionar estado de decisión
const openTransitionDialog = (toStatus: string) => {
    targetStatus.value = toStatus;
    transitionNotes.value = '';
    showTransitionDialog.value = true;
};

const confirmTransition = async () => {
    loading.value = true;
    try {
        await api.post(
            `/api/strategic-planning/scenarios/${props.scenario.id}/decision-status`,
            {
                to_status: targetStatus.value,
                notes: transitionNotes.value || null,
            },
        );
        showSuccess(`Estado transicionado a '${targetStatus.value}'`);
        showTransitionDialog.value = false;
        emit('statusChanged');
        emit('refresh');
    } catch (error: any) {
        showError(error?.message || 'Error al transicionar estado');
    } finally {
        loading.value = false;
    }
};

// Acciones de ejecución
const executeAction = async (action: string) => {
    loading.value = true;
    try {
        await api.post(
            `/api/strategic-planning/scenarios/${props.scenario.id}/execution/${action}`,
            {
                notes: executionNotes.value || null,
            },
        );
        showSuccess(
            `Ejecución ${action === 'start' ? 'iniciada' : action === 'pause' ? 'pausada' : 'completada'}`,
        );
        showExecutionDialog.value = false;
        executionNotes.value = '';
        emit('statusChanged');
        emit('refresh');
    } catch (error: any) {
        showError(error?.message || `Error al ${action} ejecución`);
    } finally {
        loading.value = false;
    }
};

const openExecutionDialog = (action: string) => {
    targetStatus.value = action;
    executionNotes.value = '';
    showExecutionDialog.value = true;
};

// Sincronizar desde padre
const syncFromParent = async () => {
    loading.value = true;
    try {
        const res = await api.post(
            `/api/strategic-planning/scenarios/${props.scenario.id}/sync-parent`,
        );
        showSuccess(res.message || 'Skills sincronizadas desde el padre');
        emit('refresh');
    } catch (error: any) {
        showError(error?.message || 'Error al sincronizar skills');
    } finally {
        loading.value = false;
    }
};

// Crear nueva versión (inmutabilidad)
const openVersionDialog = () => {
    showVersionDialog.value = true;
};

const createNewVersion = async (payload: any) => {
    loading.value = true;
    try {
        const res = await api.post(
            `/api/strategic-planning/scenarios/${props.scenario.id}/versions`,
            payload,
        );
        showSuccess(res.message || 'Nueva versión creada');
        showVersionDialog.value = false;
        emit('refresh');
        // Redirigir a la nueva versión
        if (res.data?.id) {
            window.location.href = `/strategic-planning/scenarios/${res.data.id}`;
        }
    } catch (error: any) {
        showError(error?.message || 'Error al crear versión');
    } finally {
        loading.value = false;
    }
};

// Estados para UI
const decisionStatusBadge = computed(() => {
    const status = props.scenario.decision_status;
    const map: Record<string, { color: string; text: string; icon: string }> = {
        draft: {
            color: 'grey',
            text: 'Borrador',
            icon: 'mdi-file-document-edit',
        },
        pending_approval: {
            color: 'warning',
            text: 'Pendiente Aprobación',
            icon: 'mdi-clock-alert',
        },
        approved: {
            color: 'success',
            text: 'Aprobado',
            icon: 'mdi-check-circle',
        },
        rejected: {
            color: 'error',
            text: 'Rechazado',
            icon: 'mdi-close-circle',
        },
    };
    return map[status] || map.draft;
});

const executionStatusBadge = computed(() => {
    const status = props.scenario.execution_status;
    const map: Record<string, { color: string; text: string; icon: string }> = {
        planned: {
            color: 'grey-lighten-1',
            text: 'Planificado',
            icon: 'mdi-calendar-clock',
        },
        in_progress: {
            color: 'primary',
            text: 'En Ejecución',
            icon: 'mdi-play-circle',
        },
        paused: { color: 'warning', text: 'Pausado', icon: 'mdi-pause-circle' },
        completed: {
            color: 'success',
            text: 'Completado',
            icon: 'mdi-check-circle-outline',
        },
    };
    return map[status] || map.planned;
});
</script>

<template>
    <div class="scenario-actions-panel">
        <!-- Badges de estado -->
        <v-row class="mb-4">
            <v-col cols="12" md="6">
                <v-chip
                    :color="decisionStatusBadge.color"
                    :prepend-icon="decisionStatusBadge.icon"
                    variant="flat"
                    size="large"
                    class="mr-2"
                >
                    {{ decisionStatusBadge.text }}
                </v-chip>

                <v-chip
                    v-if="scenario.decision_status === 'approved'"
                    :color="executionStatusBadge.color"
                    :prepend-icon="executionStatusBadge.icon"
                    variant="tonal"
                    size="large"
                >
                    {{ executionStatusBadge.text }}
                </v-chip>
            </v-col>
        </v-row>

        <!-- Botones de transición de decisión -->
        <v-row v-if="decisionTransitions.length > 0">
            <v-col cols="12">
                <div class="text-subtitle-2 text-medium-emphasis mb-2">
                    Transiciones de Estado
                </div>
                <v-btn
                    v-for="btn in decisionTransitions"
                    :key="btn.toStatus"
                    :color="btn.color"
                    :prepend-icon="btn.icon"
                    :disabled="btn.disabled"
                    :loading="loading"
                    variant="elevated"
                    class="mr-2 mb-2"
                    @click="openTransitionDialog(btn.toStatus)"
                >
                    {{ btn.label }}
                </v-btn>
            </v-col>
        </v-row>

        <!-- Botones de ejecución -->
        <v-row v-if="executionActions.length > 0">
            <v-col cols="12">
                <div class="text-subtitle-2 text-medium-emphasis mb-2">
                    Control de Ejecución
                </div>
                <v-btn
                    v-for="btn in executionActions"
                    :key="btn.action"
                    :color="btn.color"
                    :prepend-icon="btn.icon"
                    :disabled="btn.disabled"
                    :loading="loading"
                    variant="elevated"
                    class="mr-2 mb-2"
                    @click="openExecutionDialog(btn.action)"
                >
                    {{ btn.label }}
                </v-btn>
            </v-col>
        </v-row>

        <!-- Botones de versionamiento y jerarquía -->
        <v-row>
            <v-col cols="12">
                <v-divider class="my-4" />
                <div class="text-subtitle-2 text-medium-emphasis mb-2">
                    Acciones Adicionales
                </div>

                <v-btn
                    v-if="canCreateVersion"
                    color="purple"
                    prepend-icon="mdi-content-copy"
                    variant="outlined"
                    class="mr-2 mb-2"
                    :loading="loading"
                    @click="openVersionDialog"
                >
                    Crear Nueva Versión
                </v-btn>

                <v-btn
                    v-if="canSyncFromParent"
                    color="info"
                    prepend-icon="mdi-sync"
                    variant="outlined"
                    class="mr-2 mb-2"
                    :loading="loading"
                    @click="syncFromParent"
                >
                    Sincronizar Skills desde Padre
                </v-btn>

                <v-btn
                    v-if="!canEdit"
                    color="grey"
                    prepend-icon="mdi-lock"
                    variant="text"
                    disabled
                    class="mr-2 mb-2"
                >
                    Escenario Aprobado (Inmutable)
                </v-btn>
            </v-col>
        </v-row>

        <!-- Dialog: Confirmar transición -->
        <v-dialog v-model="showTransitionDialog" max-width="500">
            <v-card>
                <v-card-title>Confirmar Transición</v-card-title>
                <v-card-text>
                    <p>
                        ¿Confirmas transicionar el estado a
                        <strong>{{ targetStatus }}</strong
                        >?
                    </p>
                    <v-textarea
                        v-model="transitionNotes"
                        label="Notas (opcional)"
                        rows="3"
                        variant="outlined"
                        class="mt-2"
                    />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn variant="text" @click="showTransitionDialog = false"
                        >Cancelar</v-btn
                    >
                    <v-btn
                        color="primary"
                        :loading="loading"
                        @click="confirmTransition"
                        >Confirmar</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Dialog: Confirmar acción de ejecución -->
        <v-dialog v-model="showExecutionDialog" max-width="500">
            <v-card>
                <v-card-title
                    >Confirmar
                    {{
                        targetStatus === 'start'
                            ? 'Inicio'
                            : targetStatus === 'pause'
                              ? 'Pausa'
                              : 'Finalización'
                    }}</v-card-title
                >
                <v-card-text>
                    <v-textarea
                        v-model="executionNotes"
                        label="Notas (opcional)"
                        rows="3"
                        variant="outlined"
                    />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn variant="text" @click="showExecutionDialog = false"
                        >Cancelar</v-btn
                    >
                    <v-btn
                        color="primary"
                        :loading="loading"
                        @click="executeAction(targetStatus)"
                        >Confirmar</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Dialog: Crear nueva versión -->
        <v-dialog v-model="showVersionDialog" max-width="600">
            <v-card>
                <v-card-title>Crear Nueva Versión (Inmutabilidad)</v-card-title>
                <v-card-text>
                    <p class="text-body-2 text-medium-emphasis mb-4">
                        Se creará una copia editable del escenario actual. El
                        escenario aprobado quedará inmutable.
                    </p>
                    <v-text-field
                        v-model="versionName"
                        label="Nombre de la nueva versión *"
                        variant="outlined"
                        required
                        class="mb-2"
                    />
                    <v-textarea
                        v-model="versionDescription"
                        label="Descripción"
                        rows="2"
                        variant="outlined"
                        class="mb-2"
                    />
                    <v-textarea
                        v-model="versionNotes"
                        label="Notas sobre cambios"
                        rows="2"
                        variant="outlined"
                        class="mb-2"
                    />
                    <v-checkbox
                        v-model="copySkills"
                        label="Copiar skills del escenario actual"
                        density="compact"
                    />
                    <v-checkbox
                        v-model="copyStrategies"
                        label="Copiar estrategias del escenario actual"
                        density="compact"
                    />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn variant="text" @click="showVersionDialog = false"
                        >Cancelar</v-btn
                    >
                    <v-btn
                        color="purple"
                        :loading="loading"
                        :disabled="!versionName"
                        @click="
                            createNewVersion({
                                name: versionName,
                                description: versionDescription,
                                notes: versionNotes,
                                copy_skills: copySkills,
                                copy_strategies: copyStrategies,
                            })
                        "
                    >
                        Crear Versión
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<style scoped>
.scenario-actions-panel {
    padding: 16px;
    background: rgba(var(--v-theme-surface), 0.5);
    border-radius: 8px;
}
</style>
