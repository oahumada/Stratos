<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

interface Community {
    id: number;
    name: string;
    description: string | null;
    type: string;
    status: string;
    health_score: number | null;
    max_members: number | null;
    members_count: number;
    image_url: string | null;
    facilitator: { id: number; name: string } | null;
    created_at: string;
}

interface CommunityForm {
    name: string;
    description: string;
    type: string;
    domain_skills: string[];
    learning_goals: string[];
    max_members: number | null;
    image_url: string;
}

const communities = ref<Community[]>([]);
const loading = ref(false);
const totalPages = ref(1);
const currentPage = ref(1);

const searchQuery = ref('');
const selectedType = ref<string | null>(null);
const selectedStatus = ref<string | null>(null);

const showCreateDialog = ref(false);
const creating = ref(false);
const newSkill = ref('');
const newGoal = ref('');
const form = ref<CommunityForm>({
    name: '',
    description: '',
    type: 'practice',
    domain_skills: [],
    learning_goals: [],
    max_members: null,
    image_url: '',
});

const typeOptions = [
    { title: 'Práctica', value: 'practice' },
    { title: 'Investigación', value: 'inquiry' },
    { title: 'Profesional', value: 'professional' },
    { title: 'Interés', value: 'interest' },
];

const statusOptions = [
    { title: 'Activa', value: 'active' },
    { title: 'Archivada', value: 'archived' },
];

const typeColor = (type: string) => {
    switch (type) {
        case 'practice': return 'primary';
        case 'inquiry': return 'purple';
        case 'professional': return 'teal';
        case 'interest': return 'orange';
        default: return 'grey';
    }
};

const healthColor = (score: number | null) => {
    if (score === null) return 'grey';
    if (score < 25) return 'red';
    if (score < 50) return 'amber';
    if (score < 75) return 'green';
    return 'blue';
};

const healthLabel = (score: number | null) => {
    if (score === null) return 'Sin datos';
    if (score < 25) return 'Crítico';
    if (score < 50) return 'En riesgo';
    if (score < 75) return 'Saludable';
    return 'Excelente';
};

async function fetchCommunities() {
    loading.value = true;
    try {
        const params: Record<string, unknown> = { page: currentPage.value };
        if (searchQuery.value) params.search = searchQuery.value;
        if (selectedType.value) params.type = selectedType.value;
        if (selectedStatus.value) params.status = selectedStatus.value;

        const { data } = await axios.get('/api/lms/communities', { params });
        communities.value = data.data ?? [];
        totalPages.value = data.last_page ?? 1;
    } catch {
        communities.value = [];
    } finally {
        loading.value = false;
    }
}

async function createCommunity() {
    creating.value = true;
    try {
        const payload: Record<string, unknown> = {
            name: form.value.name,
            description: form.value.description || null,
            type: form.value.type,
        };
        if (form.value.domain_skills.length) payload.domain_skills = form.value.domain_skills;
        if (form.value.learning_goals.length) payload.learning_goals = form.value.learning_goals;
        if (form.value.max_members) payload.max_members = form.value.max_members;
        if (form.value.image_url) payload.image_url = form.value.image_url;

        const { data } = await axios.post('/api/lms/communities', payload);
        showCreateDialog.value = false;
        resetForm();
        router.visit(`/lms/communities/${data.community.id}`);
    } catch {
        // errors handled by form validation
    } finally {
        creating.value = false;
    }
}

async function joinCommunity(id: number) {
    try {
        await axios.post(`/api/lms/communities/${id}/join`);
        fetchCommunities();
    } catch {
        // handled silently
    }
}

function goToDetail(id: number) {
    router.visit(`/lms/communities/${id}`);
}

function addSkill() {
    if (newSkill.value.trim() && !form.value.domain_skills.includes(newSkill.value.trim())) {
        form.value.domain_skills.push(newSkill.value.trim());
        newSkill.value = '';
    }
}

function removeSkill(index: number) {
    form.value.domain_skills.splice(index, 1);
}

function addGoal() {
    if (newGoal.value.trim() && !form.value.learning_goals.includes(newGoal.value.trim())) {
        form.value.learning_goals.push(newGoal.value.trim());
        newGoal.value = '';
    }
}

function removeGoal(index: number) {
    form.value.learning_goals.splice(index, 1);
}

function resetForm() {
    form.value = { name: '', description: '', type: 'practice', domain_skills: [], learning_goals: [], max_members: null, image_url: '' };
}

function clearFilters() {
    searchQuery.value = '';
    selectedType.value = null;
    selectedStatus.value = null;
    currentPage.value = 1;
}

const hasActiveFilters = computed(() => !!searchQuery.value || !!selectedType.value || !!selectedStatus.value);

watch([searchQuery, selectedType, selectedStatus], () => {
    currentPage.value = 1;
    fetchCommunities();
});

watch(currentPage, () => fetchCommunities());

onMounted(() => fetchCommunities());
</script>

<template>
    <v-container fluid class="pa-6 bg-grey-lighten-4 fill-height align-start">
        <v-row>
            <v-col cols="12">
                <div class="d-flex align-center mb-6">
                    <div>
                        <h1 class="text-h4 font-weight-bold mb-1">Comunidades de Aprendizaje</h1>
                        <p class="text-subtitle-1 text-grey-darken-1">
                            Conecta, colabora y crece con tus comunidades
                        </p>
                    </div>
                    <v-spacer />
                    <v-btn color="primary" prepend-icon="mdi-plus" @click="showCreateDialog = true">
                        Crear Comunidad
                    </v-btn>
                </div>
            </v-col>
        </v-row>

        <!-- Filters -->
        <v-row class="mb-4">
            <v-col cols="12" sm="4">
                <v-text-field
                    v-model="searchQuery"
                    label="Buscar comunidades..."
                    prepend-inner-icon="mdi-magnify"
                    clearable
                    density="compact"
                    variant="outlined"
                    hide-details
                />
            </v-col>
            <v-col cols="12" sm="3">
                <v-select
                    v-model="selectedType"
                    :items="typeOptions"
                    item-title="title"
                    item-value="value"
                    label="Tipo"
                    clearable
                    density="compact"
                    variant="outlined"
                    hide-details
                />
            </v-col>
            <v-col cols="12" sm="3">
                <v-select
                    v-model="selectedStatus"
                    :items="statusOptions"
                    item-title="title"
                    item-value="value"
                    label="Estado"
                    clearable
                    density="compact"
                    variant="outlined"
                    hide-details
                />
            </v-col>
            <v-col cols="12" sm="2" class="d-flex align-center">
                <v-btn v-if="hasActiveFilters" variant="text" size="small" @click="clearFilters">
                    Limpiar filtros
                </v-btn>
            </v-col>
        </v-row>

        <v-progress-linear v-if="loading" indeterminate class="mb-4" />

        <!-- Community Cards -->
        <v-row v-if="communities.length">
            <v-col
                v-for="community in communities"
                :key="community.id"
                cols="12" sm="6" lg="4"
            >
                <v-card class="h-100 cursor-pointer" hover @click="goToDetail(community.id)">
                    <v-img
                        v-if="community.image_url"
                        :src="community.image_url"
                        height="140"
                        cover
                    />
                    <div v-else class="bg-grey-lighten-2 d-flex align-center justify-center" style="height: 140px">
                        <v-icon size="48" color="grey-lighten-1">mdi-account-group</v-icon>
                    </div>

                    <v-card-title class="text-subtitle-1 font-weight-bold pb-1">
                        {{ community.name }}
                    </v-card-title>

                    <v-card-text>
                        <div class="d-flex flex-wrap ga-1 mb-2">
                            <v-chip size="x-small" :color="typeColor(community.type)">
                                {{ community.type }}
                            </v-chip>
                            <v-chip
                                size="x-small"
                                :color="healthColor(community.health_score)"
                                variant="tonal"
                            >
                                <v-icon start size="12">mdi-heart-pulse</v-icon>
                                {{ healthLabel(community.health_score) }}
                            </v-chip>
                        </div>

                        <p v-if="community.description" class="text-body-2 text-grey-darken-1 mb-2 text-truncate">
                            {{ community.description }}
                        </p>

                        <div class="d-flex align-center text-body-2 text-grey-darken-1">
                            <v-icon size="16" class="mr-1">mdi-account-group</v-icon>
                            {{ community.members_count }} miembros
                            <v-spacer />
                            <span v-if="community.facilitator" class="text-caption">
                                <v-icon size="14" class="mr-1">mdi-shield-account</v-icon>
                                {{ community.facilitator.name }}
                            </span>
                        </div>
                    </v-card-text>

                    <v-card-actions>
                        <v-btn
                            size="small"
                            color="primary"
                            variant="tonal"
                            @click.stop="joinCommunity(community.id)"
                        >
                            <v-icon start>mdi-login</v-icon>
                            Unirse
                        </v-btn>
                        <v-spacer />
                        <v-btn
                            size="small"
                            variant="text"
                            @click.stop="goToDetail(community.id)"
                        >
                            Ver más
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>

        <v-alert v-else-if="!loading" type="info" variant="tonal" class="mt-4">
            No se encontraron comunidades con los filtros seleccionados.
        </v-alert>

        <div v-if="totalPages > 1" class="d-flex justify-center mt-6">
            <v-pagination v-model="currentPage" :length="totalPages" />
        </div>

        <!-- Create Community Dialog -->
        <v-dialog v-model="showCreateDialog" max-width="600">
            <v-card>
                <v-card-title class="text-h6">Crear Nueva Comunidad</v-card-title>
                <v-card-text>
                    <v-text-field
                        v-model="form.name"
                        label="Nombre *"
                        variant="outlined"
                        density="compact"
                        class="mb-3"
                    />
                    <v-textarea
                        v-model="form.description"
                        label="Descripción"
                        variant="outlined"
                        density="compact"
                        rows="3"
                        class="mb-3"
                    />
                    <v-select
                        v-model="form.type"
                        :items="typeOptions"
                        item-title="title"
                        item-value="value"
                        label="Tipo"
                        variant="outlined"
                        density="compact"
                        class="mb-3"
                    />
                    <v-text-field
                        v-model.number="form.max_members"
                        label="Máximo de miembros (opcional)"
                        type="number"
                        variant="outlined"
                        density="compact"
                        class="mb-3"
                    />

                    <!-- Domain Skills -->
                    <div class="mb-3">
                        <p class="text-caption mb-1">Habilidades del dominio</p>
                        <div class="d-flex ga-2 mb-1">
                            <v-text-field
                                v-model="newSkill"
                                density="compact"
                                variant="outlined"
                                placeholder="Agregar habilidad..."
                                hide-details
                                @keyup.enter="addSkill"
                            />
                            <v-btn icon="mdi-plus" size="small" variant="tonal" @click="addSkill" />
                        </div>
                        <v-chip-group>
                            <v-chip
                                v-for="(skill, i) in form.domain_skills"
                                :key="skill"
                                closable
                                size="small"
                                @click:close="removeSkill(i)"
                            >
                                {{ skill }}
                            </v-chip>
                        </v-chip-group>
                    </div>

                    <!-- Learning Goals -->
                    <div class="mb-3">
                        <p class="text-caption mb-1">Metas de aprendizaje</p>
                        <div class="d-flex ga-2 mb-1">
                            <v-text-field
                                v-model="newGoal"
                                density="compact"
                                variant="outlined"
                                placeholder="Agregar meta..."
                                hide-details
                                @keyup.enter="addGoal"
                            />
                            <v-btn icon="mdi-plus" size="small" variant="tonal" @click="addGoal" />
                        </div>
                        <v-chip-group>
                            <v-chip
                                v-for="(goal, i) in form.learning_goals"
                                :key="goal"
                                closable
                                size="small"
                                @click:close="removeGoal(i)"
                            >
                                {{ goal }}
                            </v-chip>
                        </v-chip-group>
                    </div>

                    <v-text-field
                        v-model="form.image_url"
                        label="URL de imagen (opcional)"
                        variant="outlined"
                        density="compact"
                    />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn variant="text" @click="showCreateDialog = false">Cancelar</v-btn>
                    <v-btn
                        color="primary"
                        :loading="creating"
                        :disabled="!form.name"
                        @click="createCommunity"
                    >
                        Crear
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>
