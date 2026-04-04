<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

interface CatalogCourse {
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
}

interface CategoryItem {
    category: string;
    count: number;
}

interface TagItem {
    tag: string;
    count: number;
}

const courses = ref<CatalogCourse[]>([]);
const featuredCourses = ref<CatalogCourse[]>([]);
const categories = ref<CategoryItem[]>([]);
const tags = ref<TagItem[]>([]);
const loading = ref(false);
const totalPages = ref(1);
const currentPage = ref(1);

const searchQuery = ref('');
const selectedCategory = ref<string | null>(null);
const selectedLevel = ref<string | null>(null);
const selectedTags = ref<string[]>([]);
const selectedEnrollmentType = ref<string | null>(null);
const sortBy = ref('newest');

const levels = ['beginner', 'intermediate', 'advanced'];
const enrollmentTypes = [
    { title: 'Abierto', value: 'open' },
    { title: 'Aprobación', value: 'approval' },
    { title: 'Invitación', value: 'invite' },
];
const sortOptions = [
    { title: 'Más nuevos', value: 'newest' },
    { title: 'Más populares', value: 'popularity' },
    { title: 'Mejor valorados', value: 'rating' },
    { title: 'A-Z', value: 'title' },
];

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

async function fetchCourses() {
    loading.value = true;
    try {
        const params: Record<string, unknown> = {
            sort: sortBy.value,
            page: currentPage.value,
        };
        if (searchQuery.value) params.search = searchQuery.value;
        if (selectedCategory.value) params.category = selectedCategory.value;
        if (selectedLevel.value) params.level = selectedLevel.value;
        if (selectedTags.value.length) params.tags = selectedTags.value;
        if (selectedEnrollmentType.value) params.enrollment_type = selectedEnrollmentType.value;

        const { data } = await axios.get('/api/lms/catalog', { params });
        courses.value = data.data ?? [];
        totalPages.value = data.last_page ?? 1;
    } catch {
        courses.value = [];
    } finally {
        loading.value = false;
    }
}

async function fetchFeatured() {
    try {
        const { data } = await axios.get('/api/lms/catalog', {
            params: { featured: true, per_page: 4 },
        });
        featuredCourses.value = data.data ?? [];
    } catch {
        featuredCourses.value = [];
    }
}

async function fetchCategories() {
    try {
        const { data } = await axios.get('/api/lms/catalog/categories');
        categories.value = data.categories ?? [];
    } catch {
        categories.value = [];
    }
}

async function fetchTags() {
    try {
        const { data } = await axios.get('/api/lms/catalog/tags');
        tags.value = data.tags ?? [];
    } catch {
        tags.value = [];
    }
}

function goToDetail(courseId: number) {
    router.visit(`/lms/catalog/${courseId}`);
}

function clearFilters() {
    searchQuery.value = '';
    selectedCategory.value = null;
    selectedLevel.value = null;
    selectedTags.value = [];
    selectedEnrollmentType.value = null;
    sortBy.value = 'newest';
    currentPage.value = 1;
}

watch([searchQuery, selectedCategory, selectedLevel, selectedTags, selectedEnrollmentType, sortBy], () => {
    currentPage.value = 1;
    fetchCourses();
});

watch(currentPage, () => fetchCourses());

onMounted(() => {
    fetchCourses();
    fetchFeatured();
    fetchCategories();
    fetchTags();
});
</script>

<template>
    <v-container fluid class="pa-6 bg-grey-lighten-4 fill-height align-start">
        <v-row>
            <v-col cols="12">
                <div class="d-flex align-center mb-6">
                    <div>
                        <h1 class="text-h4 font-weight-bold mb-1">Catálogo de Cursos</h1>
                        <p class="text-subtitle-1 text-grey-darken-1">
                            Explora y descubre cursos disponibles
                        </p>
                    </div>
                </div>
            </v-col>
        </v-row>

        <!-- Featured Banner -->
        <v-row v-if="featuredCourses.length" class="mb-4">
            <v-col cols="12">
                <h2 class="text-h6 font-weight-bold mb-3">
                    <v-icon start>mdi-star</v-icon>
                    Cursos Destacados
                </h2>
            </v-col>
            <v-col
                v-for="course in featuredCourses"
                :key="'feat-' + course.id"
                cols="12" sm="6" md="3"
            >
                <v-card
                    class="cursor-pointer h-100"
                    elevation="3"
                    color="primary"
                    variant="tonal"
                    @click="goToDetail(course.id)"
                >
                    <v-card-title class="text-subtitle-1 font-weight-bold">{{ course.title }}</v-card-title>
                    <v-card-text>
                        <v-chip size="x-small" :color="levelColor(course.level)" class="mr-1">{{ course.level }}</v-chip>
                        <v-chip size="x-small" v-if="course.category">{{ course.category }}</v-chip>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-row>
            <!-- Sidebar Filters -->
            <v-col cols="12" md="3">
                <v-card class="pa-4">
                    <h3 class="text-subtitle-1 font-weight-bold mb-3">Filtros</h3>

                    <v-text-field
                        v-model="searchQuery"
                        label="Buscar cursos..."
                        prepend-inner-icon="mdi-magnify"
                        clearable
                        density="compact"
                        variant="outlined"
                        class="mb-3"
                    />

                    <v-select
                        v-model="selectedCategory"
                        :items="categories.map(c => c.category)"
                        label="Categoría"
                        clearable
                        density="compact"
                        variant="outlined"
                        class="mb-3"
                    />

                    <v-select
                        v-model="selectedLevel"
                        :items="levels"
                        label="Nivel"
                        clearable
                        density="compact"
                        variant="outlined"
                        class="mb-3"
                    />

                    <v-select
                        v-model="selectedEnrollmentType"
                        :items="enrollmentTypes"
                        item-title="title"
                        item-value="value"
                        label="Tipo de inscripción"
                        clearable
                        density="compact"
                        variant="outlined"
                        class="mb-3"
                    />

                    <div v-if="tags.length" class="mb-3">
                        <p class="text-caption mb-1">Etiquetas</p>
                        <v-chip-group v-model="selectedTags" multiple column>
                            <v-chip
                                v-for="t in tags.slice(0, 15)"
                                :key="t.tag"
                                :value="t.tag"
                                size="small"
                                filter
                            >
                                {{ t.tag }} ({{ t.count }})
                            </v-chip>
                        </v-chip-group>
                    </div>

                    <v-btn variant="text" size="small" @click="clearFilters">Limpiar filtros</v-btn>
                </v-card>
            </v-col>

            <!-- Course Grid -->
            <v-col cols="12" md="9">
                <div class="d-flex align-center mb-4">
                    <v-select
                        v-model="sortBy"
                        :items="sortOptions"
                        item-title="title"
                        item-value="value"
                        label="Ordenar por"
                        density="compact"
                        variant="outlined"
                        style="max-width: 220px"
                        hide-details
                    />
                </div>

                <v-progress-linear v-if="loading" indeterminate class="mb-4" />

                <v-row v-if="courses.length">
                    <v-col
                        v-for="course in courses"
                        :key="course.id"
                        cols="12" sm="6" lg="4"
                    >
                        <v-card class="h-100 cursor-pointer" hover @click="goToDetail(course.id)">
                            <v-img
                                height="140"
                                cover
                                class="bg-grey-lighten-2 d-flex align-center justify-center"
                            >
                                <v-icon size="48" color="grey-lighten-1">mdi-school</v-icon>
                            </v-img>
                            <v-card-title class="text-subtitle-1 font-weight-bold pb-1">{{ course.title }}</v-card-title>
                            <v-card-text>
                                <div class="d-flex flex-wrap ga-1 mb-2">
                                    <v-chip size="x-small" v-if="course.category">{{ course.category }}</v-chip>
                                    <v-chip size="x-small" :color="levelColor(course.level)">{{ course.level }}</v-chip>
                                </div>
                                <div class="d-flex align-center text-body-2 text-grey-darken-1 mb-1">
                                    <v-icon size="16" class="mr-1">mdi-clock-outline</v-icon>
                                    {{ formatDuration(course.estimated_duration_minutes) }}
                                    <v-spacer />
                                    <v-icon size="16" class="mr-1">mdi-account-group</v-icon>
                                    {{ course.enrollments_count }}
                                </div>
                                <div class="d-flex align-center">
                                    <v-rating
                                        :model-value="course.ratings_avg_rating ?? 0"
                                        density="compact"
                                        size="16"
                                        readonly
                                        half-increments
                                        color="amber"
                                    />
                                    <span class="text-caption ml-1">({{ course.review_count }})</span>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>

                <v-alert v-else-if="!loading" type="info" variant="tonal" class="mt-4">
                    No se encontraron cursos con los filtros seleccionados.
                </v-alert>

                <div v-if="totalPages > 1" class="d-flex justify-center mt-6">
                    <v-pagination v-model="currentPage" :length="totalPages" />
                </div>
            </v-col>
        </v-row>
    </v-container>
</template>
