<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

interface CommunityDetail {
    id: number;
    name: string;
    description: string | null;
    type: string;
    status: string;
    health_score: number | null;
    social_presence: number | null;
    cognitive_presence: number | null;
    teaching_presence: number | null;
    max_members: number | null;
    domain_skills: string[] | null;
    learning_goals: string[] | null;
    image_url: string | null;
    facilitator: { id: number; name: string } | null;
    course: { id: number; title: string } | null;
    members_count: number;
    created_at: string;
}

interface HealthData {
    social_presence: number;
    cognitive_presence: number;
    teaching_presence: number;
    health_score: number;
    status: string;
}

interface Member {
    id: number;
    user_id: number;
    role: string;
    lpp_stage: string;
    contribution_score: number;
    discussions_count: number;
    ugc_count: number;
    joined_at: string;
    last_active_at: string;
    user: { id: number; name: string; email: string };
}

interface Activity {
    id: number;
    activity_type: string;
    title: string | null;
    content: string | null;
    presence_type: string | null;
    engagement_score: number;
    created_at: string;
    user: { id: number; name: string };
}

interface MentorMatch {
    user_id: number;
    user_name: string;
    role: string;
    contribution_score: number;
    match_confidence: number;
}

interface Progression {
    current_role: string;
    next_role: string;
    promoted: boolean;
    metrics: Record<string, number>;
    thresholds: Record<string, number>;
}

const props = defineProps<{ id?: string | number }>();

const communityId = computed(() => {
    if (props.id) return props.id;
    try {
        const route = useRoute();
        return route.params.id;
    } catch {
        const match = window.location.pathname.match(/\/lms\/communities\/(\d+)/);
        return match?.[1] ?? '';
    }
});

const community = ref<CommunityDetail | null>(null);
const health = ref<HealthData | null>(null);
const members = ref<Member[]>([]);
const activities = ref<Activity[]>([]);
const mentors = ref<MentorMatch[]>([]);
const progression = ref<Progression | null>(null);

const loading = ref(false);
const activeTab = ref('members');
const isMember = ref(false);

const roleColor = (role: string) => {
    switch (role) {
        case 'leader': return 'red';
        case 'expert': return 'purple';
        case 'mentor': return 'blue';
        case 'contributor': return 'teal';
        case 'member': return 'green';
        case 'novice': return 'grey';
        default: return 'grey';
    }
};

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

const activityIcon = (type: string) => {
    switch (type) {
        case 'discussion': return 'mdi-forum';
        case 'ugc': return 'mdi-file-document-edit';
        case 'peer_review': return 'mdi-eye-check';
        case 'mentorship': return 'mdi-account-supervisor';
        case 'recognition': return 'mdi-star';
        case 'knowledge_share': return 'mdi-share-variant';
        case 'event': return 'mdi-calendar';
        default: return 'mdi-circle';
    }
};

const formatDate = (date: string) => new Date(date).toLocaleDateString('es', { day: 'numeric', month: 'short', year: 'numeric' });

async function fetchCommunity() {
    loading.value = true;
    try {
        const { data } = await axios.get(`/api/lms/communities/${communityId.value}`);
        community.value = data.community;
        health.value = data.health;
    } catch {
        community.value = null;
    } finally {
        loading.value = false;
    }
}

async function fetchMembers() {
    try {
        const { data } = await axios.get(`/api/lms/communities/${communityId.value}/members`);
        members.value = data.data ?? [];
    } catch {
        members.value = [];
    }
}

async function fetchActivities() {
    try {
        const { data } = await axios.get(`/api/lms/communities/${communityId.value}/activities`);
        activities.value = data.data ?? [];
    } catch {
        activities.value = [];
    }
}

async function fetchMentors() {
    try {
        const { data } = await axios.get(`/api/lms/communities/${communityId.value}/mentors`);
        mentors.value = data.mentors ?? [];
    } catch {
        mentors.value = [];
    }
}

async function fetchProgression() {
    try {
        const { data } = await axios.get(`/api/lms/communities/${communityId.value}/progression`);
        progression.value = data;
        isMember.value = true;
    } catch {
        progression.value = null;
        isMember.value = false;
    }
}

async function joinCommunity() {
    try {
        await axios.post(`/api/lms/communities/${communityId.value}/join`);
        isMember.value = true;
        fetchCommunity();
        fetchMembers();
        fetchProgression();
    } catch {
        // handled silently
    }
}

async function leaveCommunity() {
    try {
        await axios.post(`/api/lms/communities/${communityId.value}/leave`);
        isMember.value = false;
        fetchCommunity();
        fetchMembers();
    } catch {
        // handled silently
    }
}

onMounted(async () => {
    await fetchCommunity();
    fetchMembers();
    fetchActivities();
    fetchProgression();
});
</script>

<template>
    <v-container fluid class="pa-6 bg-grey-lighten-4 fill-height align-start">
        <v-progress-linear v-if="loading" indeterminate class="mb-4" />

        <template v-if="community">
            <!-- Header -->
            <v-row class="mb-4">
                <v-col cols="12">
                    <v-card>
                        <v-row no-gutters>
                            <v-col v-if="community.image_url" cols="12" md="4">
                                <v-img :src="community.image_url" height="200" cover />
                            </v-col>
                            <v-col>
                                <v-card-title class="text-h5 font-weight-bold">
                                    {{ community.name }}
                                </v-card-title>
                                <v-card-text>
                                    <div class="d-flex flex-wrap ga-2 mb-3">
                                        <v-chip :color="typeColor(community.type)" size="small">
                                            {{ community.type }}
                                        </v-chip>
                                        <v-chip
                                            :color="healthColor(health?.health_score ?? null)"
                                            size="small"
                                            variant="tonal"
                                        >
                                            <v-icon start size="14">mdi-heart-pulse</v-icon>
                                            {{ health?.health_score?.toFixed(0) ?? '—' }}%
                                            · {{ healthLabel(health?.health_score ?? null) }}
                                        </v-chip>
                                        <v-chip size="small" variant="outlined">
                                            <v-icon start size="14">mdi-account-group</v-icon>
                                            {{ community.members_count }} miembros
                                        </v-chip>
                                    </div>

                                    <p v-if="community.description" class="text-body-1 mb-3">
                                        {{ community.description }}
                                    </p>

                                    <div v-if="community.facilitator" class="text-body-2 text-grey-darken-1 mb-2">
                                        <v-icon size="16" class="mr-1">mdi-shield-account</v-icon>
                                        Facilitador: {{ community.facilitator.name }}
                                    </div>

                                    <div v-if="community.domain_skills?.length" class="mb-2">
                                        <span class="text-caption">Habilidades:</span>
                                        <v-chip
                                            v-for="skill in community.domain_skills"
                                            :key="skill"
                                            size="x-small"
                                            class="ml-1"
                                        >
                                            {{ skill }}
                                        </v-chip>
                                    </div>

                                    <div class="d-flex ga-2 mt-3">
                                        <v-btn
                                            v-if="!isMember"
                                            color="primary"
                                            @click="joinCommunity"
                                        >
                                            <v-icon start>mdi-login</v-icon>
                                            Unirse
                                        </v-btn>
                                        <v-btn
                                            v-else
                                            color="error"
                                            variant="outlined"
                                            @click="leaveCommunity"
                                        >
                                            <v-icon start>mdi-logout</v-icon>
                                            Salir
                                        </v-btn>
                                        <v-btn
                                            variant="tonal"
                                            :to="`/lms/communities/${communityId}/dashboard`"
                                        >
                                            <v-icon start>mdi-chart-line</v-icon>
                                            Dashboard
                                        </v-btn>
                                    </div>
                                </v-card-text>
                            </v-col>
                        </v-row>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Progression Card (if member) -->
            <v-row v-if="progression" class="mb-4">
                <v-col cols="12">
                    <v-card class="pa-4">
                        <div class="d-flex align-center ga-4">
                            <div>
                                <span class="text-caption">Tu rol actual</span>
                                <v-chip :color="roleColor(progression.current_role)" class="ml-2">
                                    {{ progression.current_role }}
                                </v-chip>
                            </div>
                            <v-icon>mdi-arrow-right</v-icon>
                            <div>
                                <span class="text-caption">Próximo rol</span>
                                <v-chip :color="roleColor(progression.next_role)" variant="outlined" class="ml-2">
                                    {{ progression.next_role }}
                                </v-chip>
                            </div>
                            <v-spacer />
                            <v-alert v-if="progression.promoted" type="success" density="compact" variant="tonal" class="ma-0">
                                ¡Ascendido!
                            </v-alert>
                        </div>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Tabs -->
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-tabs v-model="activeTab" color="primary">
                            <v-tab value="members">
                                <v-icon start>mdi-account-group</v-icon>
                                Miembros
                            </v-tab>
                            <v-tab value="activities" @click="fetchActivities">
                                <v-icon start>mdi-timeline</v-icon>
                                Actividades
                            </v-tab>
                            <v-tab value="mentorship" @click="fetchMentors">
                                <v-icon start>mdi-account-supervisor</v-icon>
                                Mentoría
                            </v-tab>
                        </v-tabs>

                        <v-window v-model="activeTab">
                            <!-- Members Tab -->
                            <v-window-item value="members">
                                <v-table>
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Rol</th>
                                            <th>Etapa LPP</th>
                                            <th>Contribución</th>
                                            <th>Discusiones</th>
                                            <th>Última actividad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="member in members" :key="member.id">
                                            <td>{{ member.user?.name ?? '—' }}</td>
                                            <td>
                                                <v-chip size="x-small" :color="roleColor(member.role)">
                                                    {{ member.role }}
                                                </v-chip>
                                            </td>
                                            <td>
                                                <v-chip size="x-small" variant="outlined">
                                                    {{ member.lpp_stage }}
                                                </v-chip>
                                            </td>
                                            <td>{{ member.contribution_score }}</td>
                                            <td>{{ member.discussions_count }}</td>
                                            <td>{{ member.last_active_at ? formatDate(member.last_active_at) : '—' }}</td>
                                        </tr>
                                    </tbody>
                                </v-table>
                                <v-alert v-if="!members.length" type="info" variant="tonal" class="ma-4">
                                    No hay miembros aún.
                                </v-alert>
                            </v-window-item>

                            <!-- Activities Tab -->
                            <v-window-item value="activities">
                                <v-timeline density="compact" class="pa-4" v-if="activities.length">
                                    <v-timeline-item
                                        v-for="activity in activities"
                                        :key="activity.id"
                                        :icon="activityIcon(activity.activity_type)"
                                        size="small"
                                        dot-color="primary"
                                    >
                                        <div class="d-flex align-center mb-1">
                                            <span class="font-weight-bold text-body-2">
                                                {{ activity.user?.name ?? 'Sistema' }}
                                            </span>
                                            <v-chip size="x-small" class="ml-2" variant="outlined">
                                                {{ activity.activity_type }}
                                            </v-chip>
                                            <v-spacer />
                                            <span class="text-caption text-grey">
                                                {{ formatDate(activity.created_at) }}
                                            </span>
                                        </div>
                                        <p v-if="activity.title" class="text-body-2">{{ activity.title }}</p>
                                        <p v-if="activity.content" class="text-caption text-grey-darken-1">
                                            {{ activity.content }}
                                        </p>
                                    </v-timeline-item>
                                </v-timeline>
                                <v-alert v-else type="info" variant="tonal" class="ma-4">
                                    No hay actividades registradas.
                                </v-alert>
                            </v-window-item>

                            <!-- Mentorship Tab -->
                            <v-window-item value="mentorship">
                                <div class="pa-4">
                                    <h3 class="text-subtitle-1 font-weight-bold mb-3">Mentores Sugeridos</h3>
                                    <v-row v-if="mentors.length">
                                        <v-col
                                            v-for="mentor in mentors"
                                            :key="mentor.user_id"
                                            cols="12" sm="6" md="4"
                                        >
                                            <v-card variant="outlined" class="pa-3">
                                                <div class="d-flex align-center mb-2">
                                                    <v-avatar color="primary" size="40" class="mr-3">
                                                        <span class="text-h6 text-white">
                                                            {{ (mentor.user_name ?? '?')[0] }}
                                                        </span>
                                                    </v-avatar>
                                                    <div>
                                                        <p class="font-weight-bold">{{ mentor.user_name }}</p>
                                                        <v-chip size="x-small" :color="roleColor(mentor.role)">
                                                            {{ mentor.role }}
                                                        </v-chip>
                                                    </div>
                                                </div>
                                                <div class="text-body-2">
                                                    <span>Contribución: {{ mentor.contribution_score }}</span>
                                                    <v-progress-linear
                                                        :model-value="mentor.match_confidence"
                                                        color="primary"
                                                        class="mt-1"
                                                        height="6"
                                                        rounded
                                                    />
                                                    <span class="text-caption">Confianza: {{ mentor.match_confidence }}%</span>
                                                </div>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                    <v-alert v-else type="info" variant="tonal">
                                        No hay mentores disponibles en este momento.
                                    </v-alert>
                                </div>
                            </v-window-item>
                        </v-window>
                    </v-card>
                </v-col>
            </v-row>
        </template>

        <v-alert v-else-if="!loading" type="warning" variant="tonal">
            No se encontró la comunidad.
        </v-alert>
    </v-container>
</template>
