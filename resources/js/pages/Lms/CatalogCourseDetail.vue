<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';

interface Module {
    id: number;
    title: string;
    order: number;
    lessons_count: number;
}

interface Review {
    id: number;
    rating: number;
    review: string | null;
    created_at: string;
    user: { id: number; name: string } | null;
}

interface CourseDetail {
    id: number;
    title: string;
    description: string | null;
    category: string | null;
    level: string | null;
    estimated_duration_minutes: number;
    enrollment_type: string;
    featured: boolean;
    tags: string[] | null;
    enrollments_count: number;
    ratings_avg_rating: number | null;
    review_count: number;
    modules: Module[];
}

const props = defineProps<{ courseId: number }>();

const course = ref<CourseDetail | null>(null);
const reviews = ref<Review[]>([]);
const modulesCount = ref(0);
const lessonsCount = ref(0);
const loading = ref(true);
const enrolling = ref(false);
const enrolled = ref(false);
const ratingDialog = ref(false);
const userRating = ref(5);
const userReview = ref('');
const submittingRating = ref(false);

const levelColor = (level: string | null) => {
    switch (level) {
        case 'beginner': return 'success';
        case 'intermediate': return 'warning';
        case 'advanced': return 'error';
        default: return 'grey';
    }
};

const formatDuration = (minutes: number) => {
    if (minutes < 60) return `${minutes} min`;
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return mins > 0 ? `${hours}h ${mins}m` : `${hours}h`;
};

async function fetchDetail() {
    loading.value = true;
    try {
        const { data } = await axios.get(`/api/lms/catalog/${props.courseId}`);
        course.value = data.course;
        reviews.value = data.reviews ?? [];
        modulesCount.value = data.modules_count ?? 0;
        lessonsCount.value = data.lessons_count ?? 0;
    } catch {
        course.value = null;
    } finally {
        loading.value = false;
    }
}

async function handleEnroll() {
    if (!course.value) return;
    enrolling.value = true;
    try {
        await axios.post(`/api/lms/catalog/${course.value.id}/enroll`);
        enrolled.value = true;
    } catch {
        // error handled silently
    } finally {
        enrolling.value = false;
    }
}

async function submitRating() {
    if (!course.value) return;
    submittingRating.value = true;
    try {
        await axios.post(`/api/lms/catalog/${course.value.id}/rate`, {
            rating: userRating.value,
            review: userReview.value || null,
        });
        ratingDialog.value = false;
        fetchDetail();
    } catch {
        // error handled silently
    } finally {
        submittingRating.value = false;
    }
}

onMounted(fetchDetail);
</script>

<template>
    <v-container fluid class="pa-6 bg-grey-lighten-4 fill-height align-start">
        <v-progress-linear v-if="loading" indeterminate />

        <template v-if="course">
            <!-- Hero Section -->
            <v-card class="mb-6 pa-6" elevation="2">
                <v-row>
                    <v-col cols="12" md="8">
                        <h1 class="text-h4 font-weight-bold mb-2">{{ course.title }}</h1>
                        <p v-if="course.description" class="text-body-1 text-grey-darken-1 mb-4">
                            {{ course.description }}
                        </p>
                        <div class="d-flex flex-wrap ga-2 mb-3">
                            <v-chip v-if="course.category">{{ course.category }}</v-chip>
                            <v-chip :color="levelColor(course.level)">{{ course.level }}</v-chip>
                            <v-chip variant="outlined">
                                <v-icon start size="16">mdi-clock-outline</v-icon>
                                {{ formatDuration(course.estimated_duration_minutes) }}
                            </v-chip>
                            <v-chip variant="outlined">
                                <v-icon start size="16">mdi-account-group</v-icon>
                                {{ course.enrollments_count }} inscritos
                            </v-chip>
                        </div>
                        <div v-if="course.tags?.length" class="d-flex flex-wrap ga-1">
                            <v-chip v-for="tag in course.tags" :key="tag" size="small" variant="tonal">
                                {{ tag }}
                            </v-chip>
                        </div>
                    </v-col>
                    <v-col cols="12" md="4" class="d-flex flex-column align-center justify-center">
                        <div class="text-center mb-4">
                            <v-rating
                                :model-value="course.ratings_avg_rating ?? 0"
                                density="compact"
                                size="24"
                                readonly
                                half-increments
                                color="amber"
                            />
                            <p class="text-caption">
                                {{ (course.ratings_avg_rating ?? 0).toFixed(1) }} / 5
                                ({{ course.review_count }} reseñas)
                            </p>
                        </div>
                        <v-btn
                            v-if="course.enrollment_type === 'open' && !enrolled"
                            color="primary"
                            size="large"
                            :loading="enrolling"
                            block
                            @click="handleEnroll"
                        >
                            Inscribirme
                        </v-btn>
                        <v-alert v-if="enrolled" type="success" variant="tonal" density="compact" class="mt-2">
                            ¡Inscripción exitosa!
                        </v-alert>
                        <v-btn
                            variant="outlined"
                            class="mt-3"
                            block
                            @click="ratingDialog = true"
                        >
                            Calificar
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card>

            <!-- Content: Modules -->
            <v-row>
                <v-col cols="12" md="8">
                    <v-card class="mb-6">
                        <v-card-title class="text-h6">
                            Contenido del curso
                            <span class="text-caption ml-2">
                                ({{ modulesCount }} módulos · {{ lessonsCount }} lecciones)
                            </span>
                        </v-card-title>
                        <v-expansion-panels variant="accordion">
                            <v-expansion-panel
                                v-for="mod in course.modules"
                                :key="mod.id"
                            >
                                <v-expansion-panel-title>
                                    {{ mod.title }}
                                    <template #actions>
                                        <v-chip size="x-small" variant="tonal">
                                            {{ mod.lessons_count }} lecciones
                                        </v-chip>
                                    </template>
                                </v-expansion-panel-title>
                                <v-expansion-panel-text>
                                    <p class="text-body-2 text-grey">
                                        {{ mod.lessons_count }} lecciones en este módulo
                                    </p>
                                </v-expansion-panel-text>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-card>
                </v-col>

                <!-- Reviews -->
                <v-col cols="12" md="4">
                    <v-card>
                        <v-card-title class="text-h6">Reseñas</v-card-title>
                        <v-card-text v-if="!reviews.length">
                            <p class="text-body-2 text-grey">Sin reseñas aún.</p>
                        </v-card-text>
                        <v-list v-else>
                            <v-list-item
                                v-for="rev in reviews"
                                :key="rev.id"
                            >
                                <template #prepend>
                                    <v-avatar size="32" color="primary" variant="tonal">
                                        <span class="text-caption">{{ rev.user?.name?.charAt(0) ?? '?' }}</span>
                                    </v-avatar>
                                </template>
                                <v-list-item-title class="text-body-2 font-weight-bold">
                                    {{ rev.user?.name ?? 'Usuario' }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    <v-rating
                                        :model-value="rev.rating"
                                        density="compact"
                                        size="12"
                                        readonly
                                        color="amber"
                                    />
                                    <p v-if="rev.review" class="text-body-2 mt-1">{{ rev.review }}</p>
                                </v-list-item-subtitle>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-col>
            </v-row>
        </template>

        <!-- Rating Dialog -->
        <v-dialog v-model="ratingDialog" max-width="450">
            <v-card>
                <v-card-title>Calificar curso</v-card-title>
                <v-card-text>
                    <div class="text-center mb-4">
                        <v-rating
                            v-model="userRating"
                            color="amber"
                            size="32"
                        />
                    </div>
                    <v-textarea
                        v-model="userReview"
                        label="Reseña (opcional)"
                        rows="3"
                        variant="outlined"
                    />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn variant="text" @click="ratingDialog = false">Cancelar</v-btn>
                    <v-btn color="primary" :loading="submittingRating" @click="submitRating">
                        Enviar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>
