<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useNotification } from '@kyvg/vue3-notification';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { useTheme as useVuetifyTheme } from 'vuetify';

defineOptions({ layout: AppLayout });

const vuetifyTheme = useVuetifyTheme();

const headerGradient = computed(() => {
    const theme = vuetifyTheme.global.current.value;
    return `linear-gradient(135deg, ${theme.colors.primary} 0%, ${theme.colors.secondary} 100%)`;
});

const { notify } = useNotification();

interface DevelopmentStep {
    order: number;
    action_type: string;
    skill_name: string;
    description: string;
    estimated_duration_days: number;
}

interface DevelopmentPath {
    id: number;
    people_id: number;
    people_name: string;
    current_role_id?: number;
    current_role_name?: string;
    target_role_id: number;
    target_role_name: string;
    steps: DevelopmentStep[];
    created_at: string;
}

// State
const paths = ref<DevelopmentPath[]>([]);
const loading = ref(false);
const expandedPath = ref<number | null>(null);
const deleteDialogOpen = ref(false);
const pathToDelete = ref<DevelopmentPath | null>(null);
const deleting = ref(false);

// Load development paths
const loadPaths = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/development-paths');
        paths.value = response.data.data || response.data;
        if (paths.value.length > 0) {
            expandedPath.value = paths.value[0].id;
        }
    } catch (err) {
        console.error('Failed to load development paths', err);
        notify({
            type: 'error',
            text: 'Error loading development paths',
        });
    } finally {
        loading.value = false;
    }
};

// Get action icon
const getActionIcon = (actionType: string): string => {
    const iconMap: Record<string, string> = {
        course: 'mdi-book-open-variant',
        mentorship: 'mdi-human-greeting-variant',
        project: 'mdi-folder-multiple',
        certification: 'mdi-certificate',
        workshop: 'mdi-presentation',
        reading: 'mdi-book',
        practice: 'mdi-code-braces',
    };
    return iconMap[actionType] || 'mdi-checkbox-marked-circle';
};

// Get action color
const getActionColor = (actionType: string): string => {
    const colorMap: Record<string, string> = {
        course: 'indigo',
        mentorship: 'teal',
        project: 'success',
        certification: 'warning',
        workshop: 'success',
        reading: 'accent',
        practice: 'error',
    };
    return colorMap[actionType] || 'grey';
};

// Calculate total duration
const calculateTotalDuration = (steps: DevelopmentStep[]): number => {
    return steps.reduce(
        (total, step) => total + (step.estimated_duration_days || 0),
        0,
    );
};

// Check if path is towards current role
const isCurrentRole = (path: DevelopmentPath): boolean => {
    return path.current_role_id === path.target_role_id;
};

const openDeleteDialog = (path: DevelopmentPath) => {
    pathToDelete.value = path;
    deleteDialogOpen.value = true;
};

const confirmDelete = async () => {
    if (!pathToDelete.value) return;

    deleting.value = true;
    try {
        await axios.delete(`/api/development-paths/${pathToDelete.value.id}`);
        notify({
            type: 'success',
            text: 'Ruta de aprendizaje eliminada correctamente',
        });
        deleteDialogOpen.value = false;
        pathToDelete.value = null;
        await loadPaths();
    } catch (err: any) {
        console.error('Failed to delete path', err);
        notify({
            type: 'error',
            text: err.response?.data?.message || 'Error al eliminar la ruta',
        });
    } finally {
        deleting.value = false;
    }
};

onMounted(() => {
    loadPaths();
});
</script>

<template>
    <div class="pa-4">
        <!-- Header -->
        <div
            class="d-flex justify-space-between align-center mb-4"
            :style="{ background: headerGradient }"
            style="padding: 1.5rem; border-radius: 8px"
        >
            <div>
                <h1 class="text-h4 font-weight-bold mb-2" style="color: white">
                    Learning Paths
                </h1>
                <p
                    class="text-subtitle-2"
                    style="color: rgba(255, 255, 255, 0.85)"
                >
                    Rutas de desarrollo personalizadas para el crecimiento de
                    competencias
                </p>
            </div>
        </div>

        <!-- Description Section -->
        <v-card class="mb-6" elevation="0" variant="outlined">
            <v-card-text class="pa-6">
                <div class="d-flex align-start gap-4">
                    <v-icon size="48" color="primary" class="mt-1"
                        >mdi-map-marker-path</v-icon
                    >
                    <div class="flex-grow-1">
                        <h2 class="text-h6 font-weight-bold mb-3">
                            ¿Qué es un Learning Path?
                        </h2>
                        <p class="text-body-2 mb-3">
                            Un <strong>Learning Path</strong> es una ruta de
                            aprendizaje personalizada y estructurada que se
                            genera automáticamente basándose en el análisis de
                            brechas de competencias entre el perfil actual de
                            una persona y los requisitos de un rol objetivo.
                        </p>
                        <p class="text-body-2 mb-3">
                            Cada ruta contiene una secuencia organizada de pasos
                            de desarrollo que guían el crecimiento profesional
                            de manera progresiva y medible, desde el nivel
                            actual hasta alcanzar las competencias requeridas.
                        </p>

                        <div class="mt-4">
                            <h3 class="text-subtitle-1 font-weight-bold mb-2">
                                Características principales:
                            </h3>
                            <v-row dense>
                                <v-col cols="12" md="6">
                                    <div class="d-flex align-start mb-2 gap-2">
                                        <v-icon size="small" color="success"
                                            >mdi-check-circle</v-icon
                                        >
                                        <span class="text-body-2"
                                            ><strong>Personalizada:</strong>
                                            Adaptada al nivel actual de cada
                                            persona</span
                                        >
                                    </div>
                                    <div class="d-flex align-start mb-2 gap-2">
                                        <v-icon size="small" color="success"
                                            >mdi-check-circle</v-icon
                                        >
                                        <span class="text-body-2"
                                            ><strong>Priorizada:</strong> Skills
                                            críticas y brechas grandes
                                            primero</span
                                        >
                                    </div>
                                    <div class="d-flex align-start mb-2 gap-2">
                                        <v-icon size="small" color="success"
                                            >mdi-check-circle</v-icon
                                        >
                                        <span class="text-body-2"
                                            ><strong>Estructurada:</strong>
                                            Pasos secuenciales con duración
                                            estimada</span
                                        >
                                    </div>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <div class="d-flex align-start mb-2 gap-2">
                                        <v-icon size="small" color="success"
                                            >mdi-check-circle</v-icon
                                        >
                                        <span class="text-body-2"
                                            ><strong>Diversa:</strong> Múltiples
                                            tipos de actividades de
                                            aprendizaje</span
                                        >
                                    </div>
                                    <div class="d-flex align-start mb-2 gap-2">
                                        <v-icon size="small" color="success"
                                            >mdi-check-circle</v-icon
                                        >
                                        <span class="text-body-2"
                                            ><strong>Medible:</strong> Duración
                                            total y progreso por paso</span
                                        >
                                    </div>
                                    <div class="d-flex align-start mb-2 gap-2">
                                        <v-icon size="small" color="success"
                                            >mdi-check-circle</v-icon
                                        >
                                        <span class="text-body-2"
                                            ><strong>Certificable:</strong>
                                            Incluye certificaciones para skills
                                            críticas</span
                                        >
                                    </div>
                                </v-col>
                            </v-row>
                        </div>

                        <v-alert
                            type="info"
                            variant="tonal"
                            class="mt-4"
                            density="compact"
                        >
                            <template #prepend>
                                <v-icon>mdi-information</v-icon>
                            </template>
                            <strong>Cómo generar una ruta:</strong> Realiza un
                            análisis de brechas (Gap Analysis) y haz clic en
                            "Generar ruta de aprendizaje"
                        </v-alert>

                        <div class="mt-4 border-t pt-4">
                            <h3 class="text-subtitle-1 font-weight-bold mb-2">
                                ¿Cómo se determinan las acciones?
                            </h3>
                            <p class="text-body-2 mb-3">
                                Las acciones de aprendizaje se generan
                                automáticamente basándose en el
                                <strong>tamaño de la brecha</strong> (diferencia
                                entre nivel requerido y nivel actual),
                                independientemente del nivel absoluto en que se
                                encuentre la persona:
                            </p>
                            <v-table
                                class="text-body-2"
                                style="background: transparent"
                            >
                                <thead>
                                    <tr style="background-color: transparent">
                                        <th class="text-left">
                                            Tamaño de Brecha
                                        </th>
                                        <th class="text-left">
                                            Acciones Asignadas
                                        </th>
                                        <th class="text-left">
                                            Duración Estimada
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>1 nivel</strong></td>
                                        <td>📖 Lectura y estudio</td>
                                        <td>15-20 días</td>
                                    </tr>
                                    <tr>
                                        <td><strong>2 niveles</strong></td>
                                        <td>📚 Curso + Práctica</td>
                                        <td>40-50 días</td>
                                    </tr>
                                    <tr>
                                        <td><strong>3 niveles</strong></td>
                                        <td>🎓 Curso + Mentoría + Proyecto</td>
                                        <td>75-90 días</td>
                                    </tr>
                                    <tr>
                                        <td><strong>4+ niveles</strong></td>
                                        <td>
                                            🏆 Curso + Mentoría + Workshop +
                                            Proyecto
                                        </td>
                                        <td>100-120 días</td>
                                    </tr>
                                </tbody>
                            </v-table>
                            <p class="text-caption text-medium-emphasis mt-2">
                                💡 Para skills críticas se agrega certificación
                                (15 días adicionales)
                            </p>
                        </div>
                    </div>
                </div>
            </v-card-text>
        </v-card>

        <!-- Loading State -->
        <v-card v-if="loading" class="mb-6" elevation="0" variant="outlined">
            <v-card-text class="py-12 text-center">
                <v-progress-circular indeterminate color="primary" size="48" />
                <p class="text-medium-emphasis mt-4">
                    Cargando rutas de aprendizaje...
                </p>
            </v-card-text>
        </v-card>

        <!-- Paths List -->
        <div v-if="!loading">
            <v-card
                v-if="paths.length === 0"
                elevation="0"
                variant="outlined"
                class="py-16"
            >
                <v-card-text class="text-center">
                    <v-icon size="80" class="text-medium-emphasis mb-6"
                        >mdi-map-marker-path</v-icon
                    >
                    <div class="text-h6 mb-2">No hay rutas de aprendizaje</div>
                    <div class="text-body-2 text-medium-emphasis">
                        Las rutas de desarrollo se crean desde el módulo de
                        análisis de brechas (Gap Analysis)
                    </div>
                </v-card-text>
            </v-card>

            <v-card
                v-for="path in paths"
                :key="path.id"
                class="mb-4"
                elevation="0"
                variant="outlined"
            >
                <!-- Header -->
                <v-card-title
                    @click="
                        expandedPath = expandedPath === path.id ? null : path.id
                    "
                    style="cursor: pointer"
                    class="pa-6"
                >
                    <div
                        class="d-flex align-center justify-space-between w-100"
                    >
                        <div class="flex-grow-1">
                            <div class="text-h6 font-weight-bold">
                                {{ path.people_name }}
                                <span
                                    v-if="path.current_role_name"
                                    class="text-body-2 text-medium-emphasis font-weight-regular"
                                >
                                    ({{ path.current_role_name }})
                                </span>
                            </div>
                            <div class="d-flex align-center mt-1 gap-2">
                                <div
                                    class="text-body-2 text-medium-emphasis d-flex align-center"
                                >
                                    <v-icon size="small" class="mr-1"
                                        >mdi-arrow-right</v-icon
                                    >
                                    {{ path.target_role_name }}
                                </div>
                                <v-chip
                                    v-if="isCurrentRole(path)"
                                    size="x-small"
                                    color="info"
                                    variant="flat"
                                    prepend-icon="mdi-account-tie"
                                >
                                    Rol actual
                                </v-chip>
                                <v-chip
                                    v-else
                                    size="x-small"
                                    color="purple"
                                    variant="flat"
                                    prepend-icon="mdi-target"
                                >
                                    Rol objetivo
                                </v-chip>
                            </div>
                        </div>
                        <div class="mr-4 text-right">
                            <v-chip
                                size="small"
                                color="primary"
                                variant="tonal"
                                class="mb-1"
                            >
                                <v-icon start size="small"
                                    >mdi-clock-outline</v-icon
                                >
                                {{ calculateTotalDuration(path.steps) }} días
                            </v-chip>
                            <div class="text-caption text-medium-emphasis">
                                {{ path.steps.length }} pasos
                            </div>
                        </div>
                        <v-btn
                            icon
                            variant="text"
                            size="small"
                            @click.stop="openDeleteDialog(path)"
                            color="error"
                            title="Eliminar ruta"
                        >
                            <v-icon>mdi-delete-outline</v-icon>
                        </v-btn>
                        <v-btn icon variant="text" size="small">
                            <v-icon>
                                {{
                                    expandedPath === path.id
                                        ? 'mdi-chevron-up'
                                        : 'mdi-chevron-down'
                                }}
                            </v-icon>
                        </v-btn>
                    </div>
                </v-card-title>

                <!-- Timeline of Steps -->
                <v-expand-transition>
                    <div v-if="expandedPath === path.id">
                        <v-divider />
                        <v-card-text class="pa-6">
                            <div class="ml-2">
                                <v-timeline
                                    side="end"
                                    density="compact"
                                    line-inset="12"
                                >
                                    <v-timeline-item
                                        v-for="(step, index) in path.steps"
                                        :key="index"
                                        :dot-color="
                                            getActionColor(step.action_type)
                                        "
                                        size="small"
                                    >
                                        <template #icon>
                                            <v-icon
                                                size="small"
                                                :icon="
                                                    getActionIcon(
                                                        step.action_type,
                                                    )
                                                "
                                            />
                                        </template>

                                        <v-card
                                            elevation="0"
                                            variant="tonal"
                                            :color="
                                                getActionColor(step.action_type)
                                            "
                                        >
                                            <v-card-text class="pa-4">
                                                <div
                                                    class="d-flex align-center justify-space-between mb-2"
                                                >
                                                    <div
                                                        class="text-subtitle-1 font-weight-bold"
                                                    >
                                                        {{ step.skill_name }}
                                                    </div>
                                                    <v-chip
                                                        size="small"
                                                        variant="flat"
                                                        :color="
                                                            getActionColor(
                                                                step.action_type,
                                                            )
                                                        "
                                                    >
                                                        {{ step.action_type }}
                                                    </v-chip>
                                                </div>
                                                <p
                                                    class="text-body-2 text-medium-emphasis mb-3"
                                                >
                                                    {{ step.description }}
                                                </p>
                                                <div class="d-flex gap-4">
                                                    <v-chip
                                                        size="x-small"
                                                        variant="outlined"
                                                    >
                                                        <v-icon
                                                            start
                                                            size="x-small"
                                                            >mdi-clock-outline</v-icon
                                                        >
                                                        {{
                                                            step.estimated_duration_days
                                                        }}
                                                        días
                                                    </v-chip>
                                                    <v-chip
                                                        size="x-small"
                                                        variant="outlined"
                                                    >
                                                        <v-icon
                                                            start
                                                            size="x-small"
                                                            >mdi-order-numeric-ascending</v-icon
                                                        >
                                                        Paso {{ step.order }}
                                                    </v-chip>
                                                </div>
                                            </v-card-text>
                                        </v-card>
                                    </v-timeline-item>
                                </v-timeline>
                            </div>
                        </v-card-text>
                    </div>
                </v-expand-transition>
            </v-card>
        </div>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialogOpen" max-width="500px">
            <v-card>
                <v-card-title class="d-flex align-center text-error gap-2">
                    <v-icon>mdi-alert-circle-outline</v-icon>
                    Eliminar ruta de aprendizaje
                </v-card-title>
                <v-divider />
                <v-card-text class="py-6">
                    <p class="text-body-2 mb-3">
                        ¿Está seguro de que desea eliminar la ruta de
                        aprendizaje de
                        <strong>{{ pathToDelete?.people_name }}</strong
                        >?
                    </p>
                    <p class="text-body-2 text-medium-emphasis">
                        Esta acción no se puede deshacer. La ruta será marcada
                        como eliminada en el sistema.
                    </p>
                </v-card-text>
                <v-divider />
                <v-card-actions class="pa-4">
                    <v-spacer />
                    <v-btn variant="outlined" @click="deleteDialogOpen = false">
                        Cancelar
                    </v-btn>
                    <v-btn
                        color="error"
                        variant="flat"
                        @click="confirmDelete"
                        :loading="deleting"
                    >
                        <v-icon start>mdi-delete</v-icon>
                        Eliminar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<style scoped>
/* Removed custom styles - using Vuetify utilities */
</style>
