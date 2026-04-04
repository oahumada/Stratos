<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';

interface CohortMember {
    id: number;
    user_id: number;
    role: string;
    joined_at: string;
    user?: { id: number; name: string; email: string };
}

interface Cohort {
    id: number;
    name: string;
    description: string | null;
    course_id: number | null;
    starts_at: string | null;
    ends_at: string | null;
    max_members: number | null;
    is_active: boolean;
    members_count: number;
    facilitator?: { id: number; name: string } | null;
    course?: { id: number; title: string } | null;
    members?: CohortMember[];
}

interface CohortProgress {
    cohort_id: number;
    total_members: number;
    completed: number;
    completion_rate: number;
}

const cohorts = ref<Cohort[]>([]);
const loading = ref(true);
const createDialog = ref(false);
const detailDialog = ref(false);
const selectedCohort = ref<Cohort | null>(null);
const selectedProgress = ref<CohortProgress | null>(null);

const form = ref({
    name: '',
    description: '',
    max_members: null as number | null,
});

async function fetchCohorts() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/lms/cohorts');
        cohorts.value = Array.isArray(data) ? data : data.data || [];
    } finally {
        loading.value = false;
    }
}

async function createCohort() {
    await axios.post('/api/lms/cohorts', form.value);
    createDialog.value = false;
    form.value = { name: '', description: '', max_members: null };
    await fetchCohorts();
}

async function showDetail(cohort: Cohort) {
    const { data } = await axios.get(`/api/lms/cohorts/${cohort.id}`);
    selectedCohort.value = data.cohort;
    selectedProgress.value = data.progress;
    detailDialog.value = true;
}

async function fetchProgress(cohortId: number) {
    const { data } = await axios.get(`/api/lms/cohorts/${cohortId}/progress`);
    selectedProgress.value = data;
}

onMounted(fetchCohorts);
</script>

<template>
    <v-container>
        <v-row class="mb-4" align="center">
            <v-col>
                <h1 class="text-h4">Cohortes</h1>
            </v-col>
            <v-col cols="auto">
                <v-btn color="primary" @click="createDialog = true">
                    <v-icon start>mdi-plus</v-icon> Crear Cohorte
                </v-btn>
            </v-col>
        </v-row>

        <v-progress-linear v-if="loading" indeterminate />

        <v-row>
            <v-col v-for="cohort in cohorts" :key="cohort.id" cols="12" md="6" lg="4">
                <v-card variant="outlined" @click="showDetail(cohort)" class="cursor-pointer">
                    <v-card-title>{{ cohort.name }}</v-card-title>
                    <v-card-subtitle>
                        {{ cohort.course?.title || 'General' }}
                        <v-chip v-if="cohort.is_active" color="green" size="x-small" class="ml-2">Activo</v-chip>
                    </v-card-subtitle>
                    <v-card-text>
                        <p v-if="cohort.description">{{ cohort.description }}</p>
                        <div class="d-flex align-center mt-2">
                            <v-icon size="small" class="mr-1">mdi-account-group</v-icon>
                            {{ cohort.members_count }}
                            <span v-if="cohort.max_members"> / {{ cohort.max_members }}</span>
                        </div>
                        <div v-if="cohort.facilitator" class="mt-1">
                            <v-icon size="small" class="mr-1">mdi-account-star</v-icon>
                            {{ cohort.facilitator.name }}
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-alert v-if="!loading && cohorts.length === 0" type="info">
            No hay cohortes creadas.
        </v-alert>

        <!-- Create Dialog -->
        <v-dialog v-model="createDialog" max-width="500">
            <v-card>
                <v-card-title>Crear Cohorte</v-card-title>
                <v-card-text>
                    <v-text-field v-model="form.name" label="Nombre" class="mb-3" />
                    <v-textarea v-model="form.description" label="Descripción" rows="3" class="mb-3" />
                    <v-text-field v-model.number="form.max_members" label="Máximo de miembros" type="number" />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn @click="createDialog = false">Cancelar</v-btn>
                    <v-btn color="primary" @click="createCohort">Crear</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Detail Dialog -->
        <v-dialog v-model="detailDialog" max-width="700">
            <v-card v-if="selectedCohort">
                <v-card-title>{{ selectedCohort.name }}</v-card-title>
                <v-card-text>
                    <p v-if="selectedCohort.description" class="mb-4">{{ selectedCohort.description }}</p>

                    <div v-if="selectedProgress" class="mb-4">
                        <h3 class="text-h6 mb-2">Progreso</h3>
                        <v-progress-linear
                            :model-value="selectedProgress.completion_rate"
                            color="primary"
                            height="20"
                            rounded
                        >
                            <template #default>{{ selectedProgress.completion_rate }}%</template>
                        </v-progress-linear>
                        <p class="mt-1 text-body-2">
                            {{ selectedProgress.completed }} / {{ selectedProgress.total_members }} completados
                        </p>
                    </div>

                    <h3 class="text-h6 mb-2">Miembros</h3>
                    <v-list density="compact">
                        <v-list-item
                            v-for="member in selectedCohort.members"
                            :key="member.id"
                        >
                            <v-list-item-title>{{ member.user?.name || `User #${member.user_id}` }}</v-list-item-title>
                            <template #append>
                                <v-chip size="x-small">{{ member.role }}</v-chip>
                            </template>
                        </v-list-item>
                    </v-list>
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn @click="detailDialog = false">Cerrar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>
