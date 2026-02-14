<script setup lang="ts">
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import { computed, ref } from 'vue';

interface Props {
    scenarioId: number;
    versionGroupId: string;
    currentVersion: number;
}

interface Version {
    id: number;
    version_number: number;
    name: string;
    description?: string;
    decision_status: string;
    execution_status: string;
    is_current_version: boolean;
    created_at: string;
    owner?: {
        name: string;
    };
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'versionSelected', versionId: number): void;
}>();

const api = useApi();
const { showSuccess, showError } = useNotification();

const showDialog = ref(false);
const loading = ref(false);
const versions = ref<Version[]>([]);
const selectedVersions = ref<number[]>([]);
const showComparison = ref(false);

const openDialog = () => {
    showDialog.value = true;
    loadVersions();
};

const loadVersions = async () => {
    loading.value = true;
    try {
        const res = await api.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}/versions`,
        );
        versions.value = res.data?.versions || [];
    } catch (e) {
        void e;
        showError('Error al cargar versiones');
    } finally {
        loading.value = false;
    }
};

const sortedVersions = computed(() => {
    return [...versions.value].sort(
        (a, b) => b.version_number - a.version_number,
    );
});

const getVersionBadgeColor = (status: string): string => {
    const map: Record<string, string> = {
        draft: 'grey',
        pending_approval: 'warning',
        approved: 'success',
        rejected: 'error',
    };
    return map[status] || 'grey';
};

const getExecutionBadgeColor = (status: string): string => {
    const map: Record<string, string> = {
        planned: 'grey-lighten-1',
        in_progress: 'primary',
        paused: 'warning',
        completed: 'success',
    };
    return map[status] || 'grey';
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const selectVersion = (versionId: number) => {
    emit('versionSelected', versionId);
    showDialog.value = false;
};

const toggleCompare = (versionId: number) => {
    const index = selectedVersions.value.indexOf(versionId);
    if (index > -1) {
        selectedVersions.value.splice(index, 1);
    } else {
        if (selectedVersions.value.length < 2) {
            selectedVersions.value.push(versionId);
        }
    }
};

const compareVersions = () => {
    if (selectedVersions.value.length === 2) {
        showComparison.value = true;
        // Aquí se puede implementar lógica de comparación detallada
        showSuccess('Comparando versiones...');
    }
};

const canCompare = computed(() => selectedVersions.value.length === 2);

defineExpose({ openDialog });
</script>

<template>
    <v-dialog v-model="showDialog" max-width="900" scrollable>
        <v-card>
            <v-card-title class="d-flex align-center">
                <v-icon icon="mdi-history" class="mr-2" />
                Historial de Versiones
                <v-spacer />
                <v-chip
                    color="primary"
                    variant="flat"
                    size="small"
                    prepend-icon="mdi-information"
                >
                    Total: {{ versions.length }} versiones
                </v-chip>
            </v-card-title>

            <v-divider />

            <v-card-text style="max-height: 600px">
                <v-alert
                    v-if="versions.length === 0 && !loading"
                    type="info"
                    variant="tonal"
                    class="mb-4"
                >
                    No hay versiones anteriores. Esta es la primera versión del
                    escenario.
                </v-alert>

                <v-alert
                    v-if="selectedVersions.length > 0"
                    type="warning"
                    variant="outlined"
                    dismissible
                    class="mb-4"
                >
                    <div class="d-flex align-center">
                        <span
                            >{{ selectedVersions.length }} versiones
                            seleccionadas para comparar</span
                        >
                        <v-spacer />
                        <v-btn
                            v-if="canCompare"
                            color="primary"
                            size="small"
                            variant="elevated"
                            prepend-icon="mdi-compare"
                            @click="compareVersions"
                        >
                            Comparar
                        </v-btn>
                    </div>
                </v-alert>

                <v-timeline side="end" density="compact" line-inset="8">
                    <v-timeline-item
                        v-for="version in sortedVersions"
                        :key="version.id"
                        :dot-color="
                            version.is_current_version ? 'primary' : 'grey'
                        "
                        size="small"
                    >
                        <template #icon>
                            <v-icon
                                :icon="
                                    version.is_current_version
                                        ? 'mdi-star'
                                        : 'mdi-circle'
                                "
                                size="small"
                            />
                        </template>

                        <v-card
                            :elevation="version.is_current_version ? 4 : 1"
                            :class="{
                                'border-primary': version.is_current_version,
                            }"
                            class="mb-3"
                        >
                            <v-card-title class="d-flex align-center">
                                <div class="d-flex flex-column flex-grow-1">
                                    <div class="d-flex align-center">
                                        <v-chip
                                            :color="
                                                version.is_current_version
                                                    ? 'primary'
                                                    : 'grey'
                                            "
                                            size="small"
                                            variant="flat"
                                            class="mr-2"
                                        >
                                            v{{ version.version_number }}
                                        </v-chip>
                                        <span class="text-h6">{{
                                            version.name
                                        }}</span>
                                    </div>
                                    <div
                                        class="text-caption text-medium-emphasis mt-1"
                                    >
                                        Creado:
                                        {{ formatDate(version.created_at) }}
                                        <span v-if="version.owner">
                                            · {{ version.owner.name }}</span
                                        >
                                    </div>
                                </div>

                                <v-checkbox
                                    :model-value="
                                        selectedVersions.includes(version.id)
                                    "
                                    :disabled="
                                        !canCompare &&
                                        !selectedVersions.includes(version.id)
                                    "
                                    density="compact"
                                    hide-details
                                    @update:model-value="
                                        toggleCompare(version.id)
                                    "
                                />
                            </v-card-title>

                            <v-card-text v-if="version.description">
                                <p class="text-body-2 mb-2">
                                    {{ version.description }}
                                </p>
                            </v-card-text>

                            <v-card-text>
                                <div class="d-flex flex-wrap gap-2">
                                    <v-chip
                                        :color="
                                            getVersionBadgeColor(
                                                version.decision_status,
                                            )
                                        "
                                        size="small"
                                        variant="tonal"
                                    >
                                        {{ version.decision_status }}
                                    </v-chip>
                                    <v-chip
                                        :color="
                                            getExecutionBadgeColor(
                                                version.execution_status,
                                            )
                                        "
                                        size="small"
                                        variant="outlined"
                                    >
                                        {{ version.execution_status }}
                                    </v-chip>
                                    <v-chip
                                        v-if="version.is_current_version"
                                        color="primary"
                                        size="small"
                                        prepend-icon="mdi-star"
                                    >
                                        Actual
                                    </v-chip>
                                </div>
                            </v-card-text>

                            <v-card-actions>
                                <v-btn
                                    v-if="version.id !== scenarioId"
                                    color="primary"
                                    variant="text"
                                    size="small"
                                    prepend-icon="mdi-open-in-new"
                                    @click="selectVersion(version.id)"
                                >
                                    Ver esta versión
                                </v-btn>
                                <v-btn
                                    v-else
                                    color="grey"
                                    variant="text"
                                    size="small"
                                    disabled
                                >
                                    Versión actual
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-timeline-item>
                </v-timeline>

                <v-progress-linear
                    v-if="loading"
                    indeterminate
                    color="primary"
                    class="mt-4"
                />
            </v-card-text>

            <v-divider />

            <v-card-actions>
                <v-spacer />
                <v-btn variant="text" @click="showDialog = false">Cerrar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style scoped>
.border-primary {
    border: 2px solid rgb(var(--v-theme-primary));
}
</style>
