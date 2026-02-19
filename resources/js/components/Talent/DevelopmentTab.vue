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
                                    <div
                                        v-if="
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
                                                        Acción de Mentoría
                                                    </div>
                                                    <div class="text-caption">
                                                        Conecta con expertos
                                                        internos
                                                    </div>
                                                </div>
                                                <v-spacer></v-spacer>
                                                <v-btn
                                                    size="small"
                                                    variant="text"
                                                    color="primary"
                                                    >Ver Detalles</v-btn
                                                >
                                            </div>
                                        </v-sheet>
                                    </div>

                                    <div class="mt-1">
                                        <v-chip
                                            size="x-small"
                                            prepend-icon="mdi-clock-outline"
                                            class="mr-1"
                                        >
                                            {{ action.estimated_hours }}h
                                        </v-chip>
                                        <v-chip size="x-small" color="grey">
                                            {{ action.type }}
                                        </v-chip>
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
</template>

<script setup>
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';
import CreatePathDialog from './CreatePathDialog.vue';

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
const panel = ref(0);
const createDialog = ref(null);

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

const openCreateDialog = () => {
    createDialog.value?.open();
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
