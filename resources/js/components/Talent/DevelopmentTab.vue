<template>
    <div class="development-tab">
        <div v-if="loading" class="d-flex pa-4 justify-center">
            <v-progress-circular
                indeterminate
                color="primary"
            ></v-progress-circular>
        </div>

        <div v-else-if="paths.length === 0" class="pa-4 text-center">
            <v-icon size="64" color="grey lighten-2" class="mb-2"
                >mdi-road-variant</v-icon
            >
            <div class="text-h6 text-grey">
                Sin planes de desarrollo activos
            </div>
            <div class="text-body-2 text-grey">
                Esta persona no tiene asignado ningún plan de crecimiento o
                cierre de brechas.
            </div>
            <v-btn
                color="primary"
                variant="text"
                class="mt-4"
                @click="openCreateDialog"
            >
                Generar Plan
            </v-btn>
        </div>

        <v-expansion-panels v-else v-model="panel" variant="accordion">
            <v-expansion-panel
                v-for="path in paths"
                :key="path.id"
                elevation="0"
                class="mb-2 rounded border"
            >
                <v-expansion-panel-title>
                    <div class="d-flex align-center flex-grow-1">
                        <v-icon
                            :color="getStatusColor(path.status)"
                            class="mr-3"
                        >
                            {{ getStatusIcon(path.status) }}
                        </v-icon>
                        <div>
                            <div class="font-weight-bold">
                                {{ path.action_title }}
                            </div>
                            <div class="text-caption text-grey">
                                Duración:
                                {{ path.estimated_duration_months }} meses
                            </div>
                        </div>
                        <v-spacer></v-spacer>
                        <v-chip
                            size="small"
                            :color="getStatusColor(path.status)"
                            class="mr-2"
                        >
                            {{ path.status }}
                        </v-chip>
                    </div>
                </v-expansion-panel-title>

                <v-expansion-panel-text>
                    <div class="py-2">
                        <div class="text-subtitle-2 mb-3">
                            Acciones de Desarrollo (70-20-10)
                        </div>

                        <v-timeline
                            density="compact"
                            align="start"
                            truncate-line="start"
                        >
                            <v-timeline-item
                                v-for="action in path.actions"
                                :key="action.id"
                                :dot-color="getActionColor(action.type)"
                                size="x-small"
                            >
                                <div class="mb-4">
                                    <div class="font-weight-bold">
                                        {{ action.title }}
                                        <v-chip
                                            size="x-small"
                                            class="ml-2"
                                            variant="outlined"
                                        >
                                            {{ action.strategy }}
                                        </v-chip>
                                    </div>
                                    <div
                                        class="text-body-2 text-grey-darken-1 mb-2"
                                    >
                                        {{ action.description }}
                                    </div>

                                    <!-- Embedded Mentor Card for Mentoring Actions -->
                                    <div v-if="action.mentor" class="mt-3 mb-2">
                                        <MentorCard
                                            :mentor="
                                                formatMentor(action.mentor)
                                            "
                                            @request="openSessionDialog(action)"
                                        />
                                        <div class="d-flex justify-end pr-2">
                                            <v-btn
                                                variant="text"
                                                size="x-small"
                                                color="primary"
                                                prepend-icon="mdi-history"
                                                @click="
                                                    openSessionDialog(action)
                                                "
                                            >
                                                Bitácora de Sesiones
                                            </v-btn>
                                        </div>
                                    </div>
                                    <div
                                        v-else-if="
                                            action.type === 'mentoring' ||
                                            action.type === 'mentorship'
                                        "
                                        class="mt-2"
                                    >
                                        <v-sheet
                                            border
                                            rounded
                                            class="pa-3 bg-grey-lighten-4"
                                        >
                                            <div class="d-flex align-center">
                                                <v-avatar
                                                    color="secondary"
                                                    size="32"
                                                    class="mr-2"
                                                >
                                                    <v-icon
                                                        icon="mdi-account-school"
                                                        size="18"
                                                        color="white"
                                                    ></v-icon>
                                                </v-avatar>
                                                <div>
                                                    <div
                                                        class="text-caption font-weight-bold"
                                                    >
                                                        Buscando Mentor...
                                                    </div>
                                                    <div class="text-caption">
                                                        Pendiente de asignación
                                                    </div>
                                                </div>
                                            </div>
                                        </v-sheet>
                                    </div>

                                    <div class="d-flex align-center mt-1 gap-2">
                                        <v-chip
                                            size="x-small"
                                            prepend-icon="mdi-clock-outline"
                                            class="mr-1"
                                        >
                                            {{ action.estimated_hours }}h
                                        </v-chip>

                                        <v-menu transition="scale-transition">
                                            <template
                                                v-slot:activator="{ props }"
                                            >
                                                <v-chip
                                                    size="x-small"
                                                    :color="
                                                        getActionStatusColor(
                                                            action.status,
                                                        )
                                                    "
                                                    v-bind="props"
                                                    class="cursor-pointer"
                                                    :loading="
                                                        updatingActionId ===
                                                        action.id
                                                    "
                                                >
                                                    {{ action.status }}
                                                    <v-icon
                                                        end
                                                        icon="mdi-chevron-down"
                                                        size="10"
                                                    ></v-icon>
                                                </v-chip>
                                            </template>
                                            <v-list
                                                density="compact"
                                                min-width="120"
                                            >
                                                <v-list-item
                                                    v-for="status in [
                                                        'pending',
                                                        'in_progress',
                                                        'completed',
                                                        'cancelled',
                                                    ]"
                                                    :key="status"
                                                    @click="
                                                        updateActionStatus(
                                                            action.id,
                                                            status,
                                                        )
                                                    "
                                                    :active="
                                                        action.status === status
                                                    "
                                                    :color="
                                                        getActionStatusColor(
                                                            status,
                                                        )
                                                    "
                                                >
                                                    <v-list-item-title
                                                        class="text-caption text-capitalize"
                                                    >
                                                        {{
                                                            status.replace(
                                                                '_',
                                                                ' ',
                                                            )
                                                        }}
                                                    </v-list-item-title>
                                                </v-list-item>
                                            </v-list>
                                        </v-menu>

                                        <v-chip
                                            size="x-small"
                                            color="grey"
                                            variant="tonal"
                                        >
                                            {{ action.type }}
                                        </v-chip>

                                        <v-btn
                                            icon="mdi-paperclip"
                                            size="x-small"
                                            variant="text"
                                            color="grey"
                                            @click="openEvidenceDialog(action)"
                                            title="Gestionar Evidencias"
                                        ></v-btn>

                                        <template v-if="action.lms_course_id">
                                            <v-btn
                                                icon="mdi-play-network-outline"
                                                size="x-small"
                                                variant="text"
                                                color="primary"
                                                @click="launchLms(action.id)"
                                                title="Lanzar Curso LMS"
                                            ></v-btn>
                                            <v-btn
                                                icon="mdi-sync"
                                                size="x-small"
                                                variant="text"
                                                color="success"
                                                :loading="
                                                    syncingLmsId === action.id
                                                "
                                                @click="
                                                    syncLmsProgress(action.id)
                                                "
                                                title="Sincronizar Progreso"
                                            ></v-btn>
                                        </template>
                                    </div>
                                </div>
                            </v-timeline-item>
                        </v-timeline>
                    </div>
                </v-expansion-panel-text>
            </v-expansion-panel>
        </v-expansion-panels>
    </div>

    <CreatePathDialog
        ref="createDialog"
        :person-id="personId"
        :skills="skills"
        @generated="fetchPaths"
    />

    <MentorshipSessionDialog
        ref="sessionDialog"
        :action-id="selectedAction?.id"
        :action-title="selectedAction?.title"
    />

    <EvidenceDialog ref="evidenceDialog" :action-id="selectedAction?.id" />
</template>

<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';
import CreatePathDialog from './CreatePathDialog.vue';
import EvidenceDialog from './EvidenceDialog.vue';
import MentorCard from './MentorCard.vue';
import MentorshipSessionDialog from './MentorshipSessionDialog.vue';

const props = defineProps({
    personId: {
        type: Number,
        required: true,
    },
    skills: {
        type: Array,
        default: () => [],
    },
});

const paths = ref([]);
const loading = ref(false);
const updatingActionId = ref<number | null>(null);
const syncingLmsId = ref<number | null>(null);
const panel = ref(0);
const createDialog = ref<InstanceType<typeof CreatePathDialog> | null>(null);
const sessionDialog = ref<InstanceType<typeof MentorshipSessionDialog> | null>(
    null,
);
const evidenceDialog = ref<InstanceType<typeof EvidenceDialog> | null>(null);
const selectedAction = ref<any>(null);

const fetchPaths = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/development-paths', {
            params: { people_id: props.personId },
        });
        paths.value = response.data.data;
    } catch (e) {
        console.error('Error fetching development paths', e);
    } finally {
        loading.value = false;
    }
};

const updateActionStatus = async (actionId, newStatus) => {
    updatingActionId.value = actionId;
    try {
        await axios.patch(`/api/development-actions/${actionId}/status`, {
            status: newStatus,
        });
        await fetchPaths();
    } catch (e) {
        console.error('Error updating action status', e);
    } finally {
        updatingActionId.value = null;
    }
};

const openCreateDialog = () => {
    createDialog.value?.open();
};

const formatMentor = (mentorData: any) => {
    return {
        id: mentorData.id,
        full_name:
            mentorData.full_name ||
            `${mentorData.first_name} ${mentorData.last_name}`,
        role: mentorData.role?.name || 'Experto',
        match_score: 95, // Mock score
        expertise_level: 5,
        avatar: mentorData.photo_url || null,
    };
};

const openSessionDialog = (action: any) => {
    selectedAction.value = action;
    setTimeout(() => {
        sessionDialog.value?.open();
    }, 50);
};

const openEvidenceDialog = (action: any) => {
    selectedAction.value = action;
    setTimeout(() => {
        evidenceDialog.value?.open();
    }, 50);
};

const launchLms = async (actionId: number) => {
    try {
        const response = await axios.post(
            `/api/development-actions/${actionId}/launch-lms`,
        );
        if (response.data.url) {
            window.open(response.data.url, '_blank');
        }
    } catch (e) {
        console.error('Error launching LMS', e);
    }
};

const syncLmsProgress = async (actionId: number) => {
    syncingLmsId.value = actionId;
    try {
        await axios.post(`/api/development-actions/${actionId}/sync-lms`);
        await fetchPaths();
    } catch (e) {
        console.error('Error syncing LMS progress', e);
    } finally {
        syncingLmsId.value = null;
    }
};

onMounted(() => {
    if (props.personId) {
        fetchPaths();
    }
});

watch(
    () => props.personId,
    (newId) => {
        if (newId) fetchPaths();
    },
);

const getStatusColor = (status) => {
    switch (status) {
        case 'active':
            return 'success';
        case 'draft':
            return 'grey';
        case 'completed':
            return 'blue';
        default:
            return 'primary';
    }
};

const getActionStatusColor = (status) => {
    switch (status) {
        case 'completed':
            return 'success';
        case 'in_progress':
            return 'warning';
        case 'cancelled':
            return 'error';
        default:
            return 'grey';
    }
};

const getStatusIcon = (status) => {
    switch (status) {
        case 'active':
            return 'mdi-play-circle';
        case 'draft':
            return 'mdi-pencil-circle';
        case 'completed':
            return 'mdi-check-circle';
        default:
            return 'mdi-circle-outline';
    }
};

const getActionColor = (type) => {
    switch (type) {
        case 'training':
            return 'info'; // Build (Course)
        case 'mentoring':
            return 'success'; // Borrow (Mentor)
        case 'project':
            return 'warning'; // Apply (Project)
        default:
            return 'grey';
    }
};
</script>

<style scoped>
.development-tab {
    min-height: 200px;
}
</style>
