<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {
    PhLock,
    PhLockOpen,
    PhPath,
    PhPlus,
    PhUsers,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

defineOptions({ layout: AppLayout });

interface PathItem {
    id: number;
    course: {
        id: number;
        title: string;
        level: string;
        estimated_duration_minutes: number;
    };
    order: number;
    is_required: boolean;
    unlock_after_item_id: number | null;
}

interface LearningPath {
    id: number;
    title: string;
    description: string | null;
    level: string | null;
    estimated_duration_minutes: number;
    is_mandatory: boolean;
    is_active: boolean;
    items_count?: number;
    enrollments_count?: number;
    items?: PathItem[];
}

interface PathEnrollment {
    id: number;
    status: string;
    progress_percentage: number;
    started_at: string | null;
    completed_at: string | null;
}

const paths = ref<LearningPath[]>([]);
const selectedPath = ref<LearningPath | null>(null);
const enrollment = ref<PathEnrollment | null>(null);
const unlockedItems = ref<number[]>([]);
const loading = ref(true);
const enrolling = ref(false);
const error = ref('');
const showCreateDialog = ref(false);

// Create form
const newTitle = ref('');
const newDescription = ref('');
const newLevel = ref('intermediate');

const levels = [
    { value: 'beginner', title: 'Principiante' },
    { value: 'intermediate', title: 'Intermedio' },
    { value: 'advanced', title: 'Avanzado' },
];

async function loadPaths() {
    try {
        loading.value = true;
        const { data } = await axios.get('/api/lms/learning-paths');
        paths.value = data.data ?? data;
    } catch (e: any) {
        error.value = e.response?.data?.message ?? 'Error cargando rutas';
    } finally {
        loading.value = false;
    }
}

async function selectPath(path: LearningPath) {
    try {
        loading.value = true;
        const { data } = await axios.get(`/api/lms/learning-paths/${path.id}`);
        selectedPath.value = data.data ?? data;
        await loadProgress(path.id);
    } catch (e: any) {
        error.value = e.response?.data?.message ?? 'Error cargando ruta';
    } finally {
        loading.value = false;
    }
}

async function loadProgress(pathId: number) {
    try {
        const { data } = await axios.get(
            `/api/lms/learning-paths/${pathId}/progress`,
        );
        enrollment.value = data.enrollment ?? null;
        unlockedItems.value = (data.items ?? [])
            .filter((i: any) => i.is_unlocked)
            .map((i: any) => i.item_id);
    } catch {
        enrollment.value = null;
        unlockedItems.value = [];
    }
}

async function enrollInPath() {
    if (!selectedPath.value) return;
    try {
        enrolling.value = true;
        error.value = '';
        const { data } = await axios.post(
            `/api/lms/learning-paths/${selectedPath.value.id}/enroll`,
        );
        enrollment.value = data.data ?? data;
        await loadProgress(selectedPath.value.id);
    } catch (e: any) {
        error.value = e.response?.data?.message ?? 'Error al inscribirse';
    } finally {
        enrolling.value = false;
    }
}

async function createPath() {
    try {
        loading.value = true;
        error.value = '';
        await axios.post('/api/lms/learning-paths', {
            title: newTitle.value,
            description: newDescription.value,
            level: newLevel.value,
            items: [],
        });
        showCreateDialog.value = false;
        newTitle.value = '';
        newDescription.value = '';
        await loadPaths();
    } catch (e: any) {
        error.value = e.response?.data?.message ?? 'Error al crear ruta';
    } finally {
        loading.value = false;
    }
}

const progressColor = computed(() => {
    if (!enrollment.value) return 'grey';
    if (enrollment.value.progress_percentage >= 100) return 'success';
    if (enrollment.value.progress_percentage >= 50) return 'primary';
    return 'warning';
});

function formatDuration(minutes: number): string {
    if (minutes < 60) return `${minutes} min`;
    const h = Math.floor(minutes / 60);
    const m = minutes % 60;
    return m > 0 ? `${h}h ${m}min` : `${h}h`;
}

onMounted(loadPaths);
</script>

<template>
    <v-container class="py-6" max-width="1200">
        <div class="d-flex justify-space-between align-center mb-6">
            <h1 class="text-h4">
                <PhPath :size="32" class="mr-2" />
                Rutas de Aprendizaje
            </h1>
            <v-btn color="primary" @click="showCreateDialog = true">
                <PhPlus :size="18" class="mr-1" /> Nueva Ruta
            </v-btn>
        </div>

        <v-alert
            v-if="error"
            type="error"
            class="mb-4"
            closable
            @click:close="error = ''"
            >{{ error }}</v-alert
        >

        <v-row>
            <!-- Path list -->
            <v-col cols="12" :md="selectedPath ? 5 : 12">
                <v-skeleton-loader
                    v-if="loading && !paths.length"
                    type="card, card, card"
                />
                <v-card
                    v-for="path in paths"
                    :key="path.id"
                    class="mb-3 cursor-pointer"
                    :variant="
                        selectedPath?.id === path.id ? 'tonal' : 'elevated'
                    "
                    @click="selectPath(path)"
                >
                    <v-card-title>{{ path.title }}</v-card-title>
                    <v-card-subtitle>
                        <v-chip size="x-small" class="mr-1">{{
                            path.level ?? 'General'
                        }}</v-chip>
                        <v-chip size="x-small" class="mr-1"
                            >{{ path.items_count ?? 0 }} cursos</v-chip
                        >
                        <v-chip
                            size="x-small"
                            v-if="path.is_mandatory"
                            color="warning"
                            >Obligatoria</v-chip
                        >
                    </v-card-subtitle>
                    <v-card-text
                        v-if="path.description"
                        class="text-truncate"
                        >{{ path.description }}</v-card-text
                    >
                    <v-card-actions>
                        <span class="text-caption">{{
                            formatDuration(path.estimated_duration_minutes)
                        }}</span>
                        <v-spacer />
                        <v-chip v-if="path.enrollments_count" size="x-small">
                            <PhUsers :size="12" class="mr-1" />
                            {{ path.enrollments_count }}
                        </v-chip>
                    </v-card-actions>
                </v-card>
                <v-card
                    v-if="!loading && !paths.length"
                    variant="outlined"
                    class="pa-8 text-center"
                >
                    <p class="text-grey mb-4">
                        No hay rutas de aprendizaje aún
                    </p>
                    <v-btn color="primary" @click="showCreateDialog = true"
                        >Crear primera ruta</v-btn
                    >
                </v-card>
            </v-col>

            <!-- Path detail -->
            <v-col v-if="selectedPath" cols="12" md="7">
                <v-card class="mb-4">
                    <v-card-title class="text-h5">{{
                        selectedPath.title
                    }}</v-card-title>
                    <v-card-text>
                        <p v-if="selectedPath.description" class="mb-4">
                            {{ selectedPath.description }}
                        </p>

                        <!-- Enrollment status -->
                        <v-card
                            v-if="enrollment"
                            variant="tonal"
                            class="pa-3 mb-4"
                        >
                            <div
                                class="d-flex justify-space-between align-center mb-2"
                            >
                                <span class="font-weight-bold"
                                    >Tu progreso</span
                                >
                                <v-chip
                                    :color="
                                        enrollment.status === 'completed'
                                            ? 'success'
                                            : 'primary'
                                    "
                                    size="small"
                                >
                                    {{
                                        enrollment.status === 'completed'
                                            ? 'Completada'
                                            : 'En progreso'
                                    }}
                                </v-chip>
                            </div>
                            <v-progress-linear
                                :model-value="enrollment.progress_percentage"
                                :color="progressColor"
                                height="8"
                                rounded
                            />
                            <span class="text-caption"
                                >{{
                                    Math.round(enrollment.progress_percentage)
                                }}%</span
                            >
                        </v-card>
                        <v-btn
                            v-else
                            color="primary"
                            block
                            class="mb-4"
                            :loading="enrolling"
                            @click="enrollInPath"
                        >
                            Inscribirme en esta ruta
                        </v-btn>

                        <!-- Items / courses -->
                        <h3 class="mb-3">Cursos en esta ruta</h3>
                        <v-timeline density="compact" side="end">
                            <v-timeline-item
                                v-for="item in selectedPath.items"
                                :key="item.id"
                                :dot-color="
                                    unlockedItems.includes(item.id)
                                        ? 'success'
                                        : 'grey'
                                "
                                size="small"
                            >
                                <v-card
                                    :variant="
                                        unlockedItems.includes(item.id)
                                            ? 'elevated'
                                            : 'outlined'
                                    "
                                    :disabled="
                                        !unlockedItems.includes(item.id) &&
                                        !!enrollment
                                    "
                                >
                                    <v-card-title class="text-subtitle-1">
                                        <component
                                            :is="
                                                unlockedItems.includes(item.id)
                                                    ? PhLockOpen
                                                    : PhLock
                                            "
                                            :size="16"
                                            class="mr-1"
                                        />
                                        {{ item.course.title }}
                                    </v-card-title>
                                    <v-card-subtitle>
                                        {{ item.course.level }} ·
                                        {{
                                            formatDuration(
                                                item.course
                                                    .estimated_duration_minutes,
                                            )
                                        }}
                                        <v-chip
                                            v-if="item.is_required"
                                            size="x-small"
                                            color="warning"
                                            class="ml-1"
                                            >Requerido</v-chip
                                        >
                                    </v-card-subtitle>
                                </v-card>
                            </v-timeline-item>
                        </v-timeline>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Create dialog -->
        <v-dialog v-model="showCreateDialog" max-width="600">
            <v-card>
                <v-card-title>Nueva Ruta de Aprendizaje</v-card-title>
                <v-card-text>
                    <v-text-field
                        v-model="newTitle"
                        label="Título"
                        variant="outlined"
                        class="mb-3"
                    />
                    <v-textarea
                        v-model="newDescription"
                        label="Descripción"
                        variant="outlined"
                        rows="3"
                        class="mb-3"
                    />
                    <v-select
                        v-model="newLevel"
                        :items="levels"
                        item-value="value"
                        item-title="title"
                        label="Nivel"
                        variant="outlined"
                    />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn variant="text" @click="showCreateDialog = false"
                        >Cancelar</v-btn
                    >
                    <v-btn
                        color="primary"
                        :disabled="!newTitle"
                        @click="createPath"
                        >Crear</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>
