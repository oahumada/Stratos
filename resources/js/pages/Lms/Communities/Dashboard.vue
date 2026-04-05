<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

interface HealthData {
    social_presence: number;
    cognitive_presence: number;
    teaching_presence: number;
    health_score: number;
    status: string;
    details: {
        member_count: number;
        active_members_30d: number;
        total_activities_30d: number;
        discussions_30d: number;
        ugc_30d: number;
        peer_reviews_30d: number;
        mentorships_30d: number;
        events_30d: number;
    };
}

interface Member {
    id: number;
    role: string;
    contribution_score: number;
    user: { id: number; name: string };
}

interface CommunityBasic {
    id: number;
    name: string;
    type: string;
}

const props = defineProps<{ id?: string | number }>();

const communityId = computed(() => {
    if (props.id) return props.id;
    try {
        const route = useRoute();
        return route.params.id;
    } catch {
        const match = window.location.pathname.match(/\/lms\/communities\/(\d+)\/dashboard/);
        return match?.[1] ?? '';
    }
});

const community = ref<CommunityBasic | null>(null);
const health = ref<HealthData | null>(null);
const members = ref<Member[]>([]);
const loading = ref(false);

const statusColor = (status: string) => {
    switch (status) {
        case 'thriving': return 'blue';
        case 'healthy': return 'green';
        case 'at_risk': return 'amber';
        case 'critical': return 'red';
        default: return 'grey';
    }
};

const statusLabel = (status: string) => {
    switch (status) {
        case 'thriving': return 'Excelente';
        case 'healthy': return 'Saludable';
        case 'at_risk': return 'En riesgo';
        case 'critical': return 'Crítico';
        default: return status;
    }
};

const roleDistribution = computed(() => {
    const dist: Record<string, number> = {};
    for (const m of members.value) {
        dist[m.role] = (dist[m.role] || 0) + 1;
    }
    return Object.entries(dist).map(([role, count]) => ({ role, count }));
});

const topContributors = computed(() => {
    return [...members.value]
        .sort((a, b) => b.contribution_score - a.contribution_score)
        .slice(0, 10);
});

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

const presenceGauges = computed(() => {
    if (!health.value) return [];
    return [
        { label: 'Presencia Social', value: health.value.social_presence, color: 'blue', icon: 'mdi-account-group' },
        { label: 'Presencia Cognitiva', value: health.value.cognitive_presence, color: 'purple', icon: 'mdi-brain' },
        { label: 'Presencia Docente', value: health.value.teaching_presence, color: 'teal', icon: 'mdi-school' },
    ];
});

async function fetchAll() {
    loading.value = true;
    try {
        const [communityRes, healthRes, membersRes] = await Promise.all([
            axios.get(`/api/lms/communities/${communityId.value}`),
            axios.get(`/api/lms/communities/${communityId.value}/health`),
            axios.get(`/api/lms/communities/${communityId.value}/members`, { params: { per_page: 100 } }),
        ]);
        community.value = communityRes.data.community;
        health.value = healthRes.data;
        members.value = membersRes.data.data ?? [];
    } catch {
        community.value = null;
        health.value = null;
    } finally {
        loading.value = false;
    }
}

onMounted(() => fetchAll());
</script>

<template>
    <v-container fluid class="pa-6 bg-grey-lighten-4 fill-height align-start">
        <v-progress-linear v-if="loading" indeterminate class="mb-4" />

        <template v-if="community && health">
            <v-row class="mb-4">
                <v-col cols="12">
                    <div class="d-flex align-center mb-2">
                        <v-btn icon="mdi-arrow-left" variant="text" :to="`/lms/communities/${communityId}`" />
                        <h1 class="text-h5 font-weight-bold ml-2">
                            Dashboard: {{ community.name }}
                        </h1>
                    </div>
                </v-col>
            </v-row>

            <!-- Overall Health -->
            <v-row class="mb-4">
                <v-col cols="12" md="4">
                    <v-card class="pa-4 text-center">
                        <p class="text-caption mb-2">Salud General</p>
                        <div class="d-flex justify-center align-center mb-2">
                            <v-progress-circular
                                :model-value="health.health_score"
                                :size="120"
                                :width="12"
                                :color="statusColor(health.status)"
                            >
                                <span class="text-h4 font-weight-bold">{{ health.health_score.toFixed(0) }}</span>
                            </v-progress-circular>
                        </div>
                        <v-chip :color="statusColor(health.status)" size="small">
                            {{ statusLabel(health.status) }}
                        </v-chip>
                    </v-card>
                </v-col>

                <!-- Presence Gauges -->
                <v-col
                    v-for="gauge in presenceGauges"
                    :key="gauge.label"
                    cols="12" md="2.66"
                >
                    <v-card class="pa-4 text-center h-100">
                        <v-icon :color="gauge.color" size="24" class="mb-2">{{ gauge.icon }}</v-icon>
                        <p class="text-caption mb-2">{{ gauge.label }}</p>
                        <v-progress-circular
                            :model-value="gauge.value"
                            :size="80"
                            :width="8"
                            :color="gauge.color"
                        >
                            <span class="text-h6 font-weight-bold">{{ gauge.value.toFixed(0) }}</span>
                        </v-progress-circular>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Details & Distribution -->
            <v-row class="mb-4">
                <!-- Activity Stats -->
                <v-col cols="12" md="6">
                    <v-card class="pa-4">
                        <h3 class="text-subtitle-1 font-weight-bold mb-3">
                            <v-icon start>mdi-chart-bar</v-icon>
                            Actividad (últimos 30 días)
                        </h3>
                        <v-table density="compact">
                            <tbody>
                                <tr>
                                    <td>Miembros totales</td>
                                    <td class="text-right font-weight-bold">{{ health.details.member_count }}</td>
                                </tr>
                                <tr>
                                    <td>Miembros activos</td>
                                    <td class="text-right font-weight-bold">{{ health.details.active_members_30d }}</td>
                                </tr>
                                <tr>
                                    <td>Total actividades</td>
                                    <td class="text-right font-weight-bold">{{ health.details.total_activities_30d }}</td>
                                </tr>
                                <tr>
                                    <td>Discusiones</td>
                                    <td class="text-right font-weight-bold">{{ health.details.discussions_30d }}</td>
                                </tr>
                                <tr>
                                    <td>Contenido generado</td>
                                    <td class="text-right font-weight-bold">{{ health.details.ugc_30d }}</td>
                                </tr>
                                <tr>
                                    <td>Revisiones por pares</td>
                                    <td class="text-right font-weight-bold">{{ health.details.peer_reviews_30d }}</td>
                                </tr>
                                <tr>
                                    <td>Mentorías</td>
                                    <td class="text-right font-weight-bold">{{ health.details.mentorships_30d }}</td>
                                </tr>
                                <tr>
                                    <td>Eventos</td>
                                    <td class="text-right font-weight-bold">{{ health.details.events_30d }}</td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-card>
                </v-col>

                <!-- Role Distribution -->
                <v-col cols="12" md="6">
                    <v-card class="pa-4">
                        <h3 class="text-subtitle-1 font-weight-bold mb-3">
                            <v-icon start>mdi-account-multiple</v-icon>
                            Distribución por Rol
                        </h3>
                        <div v-for="entry in roleDistribution" :key="entry.role" class="mb-2">
                            <div class="d-flex align-center mb-1">
                                <v-chip size="x-small" :color="roleColor(entry.role)" class="mr-2">
                                    {{ entry.role }}
                                </v-chip>
                                <v-spacer />
                                <span class="text-body-2 font-weight-bold">{{ entry.count }}</span>
                            </div>
                            <v-progress-linear
                                :model-value="members.length ? (entry.count / members.length) * 100 : 0"
                                :color="roleColor(entry.role)"
                                height="8"
                                rounded
                            />
                        </div>
                        <v-alert v-if="!roleDistribution.length" type="info" variant="tonal" density="compact">
                            Sin miembros aún.
                        </v-alert>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Top Contributors -->
            <v-row>
                <v-col cols="12">
                    <v-card class="pa-4">
                        <h3 class="text-subtitle-1 font-weight-bold mb-3">
                            <v-icon start>mdi-trophy</v-icon>
                            Top Contribuidores
                        </h3>
                        <v-table density="compact">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Rol</th>
                                    <th class="text-right">Puntuación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(member, i) in topContributors" :key="member.id">
                                    <td>{{ i + 1 }}</td>
                                    <td>{{ member.user?.name ?? '—' }}</td>
                                    <td>
                                        <v-chip size="x-small" :color="roleColor(member.role)">
                                            {{ member.role }}
                                        </v-chip>
                                    </td>
                                    <td class="text-right font-weight-bold">{{ member.contribution_score }}</td>
                                </tr>
                            </tbody>
                        </v-table>
                        <v-alert v-if="!topContributors.length" type="info" variant="tonal" density="compact" class="mt-2">
                            Sin contribuidores aún.
                        </v-alert>
                    </v-card>
                </v-col>
            </v-row>
        </template>

        <v-alert v-else-if="!loading" type="warning" variant="tonal">
            No se pudo cargar el dashboard.
        </v-alert>
    </v-container>
</template>
