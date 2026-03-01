<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
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

// Dialog state to create a version
const versionName = ref('');
const versionDescription = ref('');
const versionNotes = ref('');
const copySkills = ref(true);
const copyStrategies = ref(true);

// decision_status transition buttons
const decisionTransitions = computed(() => {
    const status = props.scenario.decision_status;
    const buttons: Array<{
        label: string;
        color: string;
        icon: string;
        toStatus: string;
        disabled: boolean;
        variant: 'primary' | 'secondary' | 'glass' | 'ghost' | 'danger';
    }> = [];

    if (status === 'draft') {
        buttons.push({
            label: 'Submit for Approval',
            color: 'primary',
            icon: 'mdi-send',
            toStatus: 'pending_approval',
            disabled: false,
            variant: 'primary',
        });
    }

    if (status === 'pending_approval') {
        buttons.push(
            {
                label: 'Approve',
                color: 'success',
                icon: 'mdi-check-circle',
                toStatus: 'approved',
                disabled: false,
                variant: 'primary',
            },
            {
                label: 'Reject',
                color: 'error',
                icon: 'mdi-close-circle',
                toStatus: 'rejected',
                disabled: false,
                variant: 'danger',
            },
        );
    }

    if (status === 'rejected') {
        buttons.push({
            label: 'Return to Draft',
            color: 'grey',
            icon: 'mdi-undo',
            toStatus: 'draft',
            disabled: false,
            variant: 'ghost',
        });
    }

    return buttons;
});

// Execution buttons
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
        variant: 'primary' | 'secondary' | 'glass' | 'ghost' | 'danger';
    }> = [];

    if (decisionStatus !== 'approved') {
        return []; // Do not show execution buttons if not approved
    }

    if (execStatus === 'planned' || execStatus === 'paused') {
        buttons.push({
            label: 'Start Execution',
            color: 'success',
            icon: 'mdi-play',
            action: 'start',
            disabled: false,
            variant: 'primary',
        });
    }

    if (execStatus === 'in_progress') {
        buttons.push(
            {
                label: 'Pause',
                color: 'warning',
                icon: 'mdi-pause',
                action: 'pause',
                disabled: false,
                variant: 'secondary',
            },
            {
                label: 'Complete',
                color: 'primary',
                icon: 'mdi-check-bold',
                action: 'complete',
                disabled: false,
                variant: 'primary',
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

const canSyncFromParent = computed(() => {
    return (
        props.scenario.parent_id !== null &&
        props.scenario.decision_status !== 'approved'
    );
});

// Transition decision state
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
        showSuccess(`State transitioned to '${targetStatus.value}'`);
        showTransitionDialog.value = false;
        emit('statusChanged');
        emit('refresh');
    } catch (error: any) {
        showError(error?.message || 'Failed to transition state');
    } finally {
        loading.value = false;
    }
};

// Execution actions
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
            `Execution ${action === 'start' ? 'started' : action === 'pause' ? 'paused' : 'completed'}`,
        );
        showExecutionDialog.value = false;
        executionNotes.value = '';
        emit('statusChanged');
        emit('refresh');
    } catch (error: any) {
        showError(error?.message || `Failed to ${action} execution`);
    } finally {
        loading.value = false;
    }
};

const openExecutionDialog = (action: string) => {
    targetStatus.value = action;
    executionNotes.value = '';
    showExecutionDialog.value = true;
};

// Sync from parent
const syncFromParent = async () => {
    loading.value = true;
    try {
        const res = await api.post(
            `/api/strategic-planning/scenarios/${props.scenario.id}/sync-parent`,
        );
        showSuccess(res.message || 'Skills synchronized from parent');
        emit('refresh');
    } catch (error: any) {
        showError(error?.message || 'Failed to sync skills');
    } finally {
        loading.value = false;
    }
};

// Create new version (immutability)
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
        showSuccess(res.message || 'New version created');
        showVersionDialog.value = false;
        emit('refresh');
        // Redirect to new version
        if (res.data?.id) {
            globalThis.location.href = `/strategic-planning/scenarios/${res.data.id}`;
        }
    } catch (error: any) {
        showError(error?.message || 'Failed to create version');
    } finally {
        loading.value = false;
    }
};

// UI States
const decisionStatusBadge = computed(() => {
    const status = props.scenario.decision_status;
    const map: Record<
        string,
        { color: 'glass' | 'primary' | 'secondary'; text: string; icon: string }
    > = {
        draft: {
            color: 'glass',
            text: 'Draft',
            icon: 'mdi-file-document-edit',
        },
        pending_approval: {
            color: 'secondary',
            text: 'Pending Approval',
            icon: 'mdi-clock-alert',
        },
        approved: {
            color: 'primary',
            text: 'Approved',
            icon: 'mdi-check-circle',
        },
        rejected: {
            color: 'secondary',
            text: 'Rejected',
            icon: 'mdi-close-circle',
        },
    };
    return map[status] || map.draft;
});

const executionStatusBadge = computed(() => {
    const status = props.scenario.execution_status;
    const map: Record<
        string,
        {
            color: 'glass' | 'primary' | 'secondary' | 'success';
            text: string;
            icon: string;
        }
    > = {
        planned: {
            color: 'glass',
            text: 'Planned',
            icon: 'mdi-calendar-clock',
        },
        in_progress: {
            color: 'primary',
            text: 'In Progress',
            icon: 'mdi-play-circle',
        },
        paused: {
            color: 'secondary',
            text: 'Paused',
            icon: 'mdi-pause-circle',
        },
        completed: {
            color: 'success',
            text: 'Completed',
            icon: 'mdi-check-circle-outline',
        },
    };
    return map[status] || map.planned;
});
</script>

<template>
    <div class="scenario-actions-panel">
        <StCardGlass variant="glass" class="w-full">
            <div class="flex flex-col gap-8 p-4">
                <!-- Status Badges -->
                <div class="flex flex-wrap items-center gap-4">
                    <StBadgeGlass
                        :variant="decisionStatusBadge.color"
                        size="lg"
                        class="shadow-sm"
                    >
                        <v-icon
                            :icon="decisionStatusBadge.icon"
                            size="18"
                            class="mr-2"
                        />
                        {{ decisionStatusBadge.text }}
                    </StBadgeGlass>

                    <StBadgeGlass
                        v-if="scenario.decision_status === 'approved'"
                        :variant="executionStatusBadge.color"
                        size="lg"
                    >
                        <v-icon
                            :icon="executionStatusBadge.icon"
                            size="18"
                            class="mr-2"
                        />
                        {{ executionStatusBadge.text }}
                    </StBadgeGlass>
                </div>

                <!-- Decision Transition Buttons -->
                <div
                    v-if="decisionTransitions.length > 0"
                    class="flex flex-col gap-3"
                >
                    <h4
                        class="text-xs font-black tracking-widest text-white/50 uppercase"
                    >
                        State Transitions
                    </h4>
                    <div class="flex flex-wrap gap-3">
                        <StButtonGlass
                            v-for="btn in decisionTransitions"
                            :key="btn.toStatus"
                            :variant="btn.variant"
                            :icon="btn.icon"
                            :disabled="btn.disabled"
                            :loading="loading"
                            @click="openTransitionDialog(btn.toStatus)"
                        >
                            {{ btn.label }}
                        </StButtonGlass>
                    </div>
                </div>

                <!-- Execution Buttons -->
                <div
                    v-if="executionActions.length > 0"
                    class="flex flex-col gap-3"
                >
                    <h4
                        class="text-xs font-black tracking-widest text-white/50 uppercase"
                    >
                        Execution Control
                    </h4>
                    <div class="flex flex-wrap gap-3">
                        <StButtonGlass
                            v-for="btn in executionActions"
                            :key="btn.action"
                            :variant="btn.variant"
                            :icon="btn.icon"
                            :disabled="btn.disabled"
                            :loading="loading"
                            @click="openExecutionDialog(btn.action)"
                        >
                            {{ btn.label }}
                        </StButtonGlass>
                    </div>
                </div>

                <!-- Versioning and Hierarchy Buttons -->
                <div class="flex flex-col gap-3 border-t border-white/10 pt-6">
                    <h4
                        class="text-xs font-black tracking-widest text-white/50 uppercase"
                    >
                        Additional Actions
                    </h4>

                    <div class="flex flex-wrap gap-3">
                        <StButtonGlass
                            v-if="canCreateVersion"
                            variant="secondary"
                            icon="mdi-content-copy"
                            :loading="loading"
                            @click="openVersionDialog"
                            class="!border-purple-500/30 hover:!bg-purple-500/10"
                        >
                            Create New Version
                        </StButtonGlass>

                        <StButtonGlass
                            v-if="canSyncFromParent"
                            variant="glass"
                            icon="mdi-sync"
                            :loading="loading"
                            @click="syncFromParent"
                        >
                            Sync Skills from Parent
                        </StButtonGlass>

                        <div
                            v-if="!canEdit"
                            class="flex items-center gap-2 rounded-lg border border-white/5 bg-white/5 px-4 py-2 opacity-60"
                        >
                            <v-icon icon="mdi-lock" size="16" color="white" />
                            <span class="text-xs font-bold text-white"
                                >Approved Scenario (Immutable)</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </StCardGlass>

        <!-- Dialog: Confirm transition -->
        <v-dialog v-model="showTransitionDialog" max-width="500">
            <StCardGlass
                variant="glass"
                border-accent="indigo"
                class="!bg-[#0f172a]/95 backdrop-blur-xl"
            >
                <div
                    class="mb-4 flex items-center gap-3 border-b border-white/10 pb-4"
                >
                    <v-icon color="indigo-400">mdi-state-machine</v-icon>
                    <h3 class="text-lg font-black text-white">
                        Confirm Transition
                    </h3>
                </div>

                <p class="mb-4 text-sm text-white/70">
                    Are you sure you want to transition the state to
                    <span class="font-bold text-white uppercase">{{
                        targetStatus.replace('_', ' ')
                    }}</span
                    >?
                </p>

                <v-textarea
                    v-model="transitionNotes"
                    label="Notes (optional)"
                    rows="3"
                    variant="outlined"
                    density="comfortable"
                    bg-color="rgba(0,0,0,0.2)"
                    class="mb-6"
                    hide-details
                />

                <div class="flex justify-end gap-3">
                    <StButtonGlass
                        variant="ghost"
                        @click="showTransitionDialog = false"
                        >Cancel</StButtonGlass
                    >
                    <StButtonGlass
                        variant="primary"
                        :loading="loading"
                        @click="confirmTransition"
                        >Confirm</StButtonGlass
                    >
                </div>
            </StCardGlass>
        </v-dialog>

        <!-- Dialog: Confirm execution action -->
        <v-dialog v-model="showExecutionDialog" max-width="500">
            <StCardGlass
                variant="glass"
                border-accent="indigo"
                class="!bg-[#0f172a]/95 backdrop-blur-xl"
            >
                <div
                    class="mb-4 flex items-center gap-3 border-b border-white/10 pb-4"
                >
                    <v-icon color="indigo-400">mdi-lightning-bolt</v-icon>
                    <h3 class="text-lg font-black text-white">
                        Confirm
                        {{
                            targetStatus === 'start'
                                ? 'Start'
                                : targetStatus === 'pause'
                                  ? 'Pause'
                                  : 'Completion'
                        }}
                    </h3>
                </div>

                <v-textarea
                    v-model="executionNotes"
                    label="Notes (optional)"
                    rows="3"
                    variant="outlined"
                    density="comfortable"
                    bg-color="rgba(0,0,0,0.2)"
                    class="mb-6"
                    hide-details
                />

                <div class="flex justify-end gap-3">
                    <StButtonGlass
                        variant="ghost"
                        @click="showExecutionDialog = false"
                        >Cancel</StButtonGlass
                    >
                    <StButtonGlass
                        variant="primary"
                        :loading="loading"
                        @click="executeAction(targetStatus)"
                        >Confirm</StButtonGlass
                    >
                </div>
            </StCardGlass>
        </v-dialog>

        <!-- Dialog: Create new version -->
        <v-dialog v-model="showVersionDialog" max-width="600">
            <StCardGlass
                variant="glass"
                border-accent="purple"
                class="!bg-[#0f172a]/95 backdrop-blur-xl"
            >
                <div
                    class="mb-4 flex items-center gap-3 border-b border-white/10 pb-4"
                >
                    <v-icon color="purple-400">mdi-source-branch</v-icon>
                    <h3 class="text-lg font-black text-white">
                        Create New Version (Immutability)
                    </h3>
                </div>

                <p
                    class="mb-6 text-xs leading-relaxed font-medium text-white/50"
                >
                    An editable copy of the current scenario will be created.
                    The approved scenario will remain immutable.
                </p>

                <div class="mb-6 space-y-4">
                    <v-text-field
                        v-model="versionName"
                        label="New version name *"
                        variant="outlined"
                        density="comfortable"
                        bg-color="rgba(0,0,0,0.2)"
                        hide-details
                    />
                    <v-textarea
                        v-model="versionDescription"
                        label="Description"
                        rows="2"
                        variant="outlined"
                        density="comfortable"
                        bg-color="rgba(0,0,0,0.2)"
                        hide-details
                    />
                    <v-textarea
                        v-model="versionNotes"
                        label="Change notes"
                        rows="2"
                        variant="outlined"
                        density="comfortable"
                        bg-color="rgba(0,0,0,0.2)"
                        hide-details
                    />

                    <div
                        class="mt-4 flex flex-col gap-2 rounded-xl border border-white/5 bg-white/2 p-4"
                    >
                        <v-checkbox
                            v-model="copySkills"
                            label="Copy skills from current scenario"
                            density="compact"
                            hide-details
                            color="purple-400"
                        />
                        <v-checkbox
                            v-model="copyStrategies"
                            label="Copy strategies from current scenario"
                            density="compact"
                            hide-details
                            color="purple-400"
                        />
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <StButtonGlass
                        variant="ghost"
                        @click="showVersionDialog = false"
                        >Cancel</StButtonGlass
                    >
                    <StButtonGlass
                        variant="primary"
                        class="!border-purple-500/50 !bg-purple-500/20 hover:!bg-purple-500/30"
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
                        Create Version
                    </StButtonGlass>
                </div>
            </StCardGlass>
        </v-dialog>
    </div>
</template>

<style scoped>
.scenario-actions-panel {
    display: flex;
    flex-direction: column;
}

:deep(.v-field__input) {
    color: white !important;
}
:deep(.v-label) {
    color: rgba(255, 255, 255, 0.6) !important;
}
</style>
