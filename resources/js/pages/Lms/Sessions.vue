<script setup lang="ts">
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

interface Session {
    id: number;
    title: string;
    session_type: string;
    location: string | null;
    meeting_url: string | null;
    starts_at: string;
    ends_at: string;
    max_attendees: number | null;
    is_recording_available: boolean;
    course: { id: number; title: string } | null;
    instructor: { id: number; name: string } | null;
    attendances: Array<{
        id: number;
        user_id: number;
        status: string;
        user?: { id: number; name: string; email: string };
    }>;
}

const sessions = ref<Session[]>([]);
const loading = ref(false);
const error = ref('');
const activeTab = ref<'upcoming' | 'past'>('upcoming');
const showAttendanceDialog = ref(false);
const selectedSession = ref<Session | null>(null);
const attendanceData = ref<Array<{ user_id: number; status: string }>>([]);

const statusColors: Record<string, string> = {
    registered: 'blue',
    confirmed: 'teal',
    attended: 'green',
    absent: 'red',
    cancelled: 'grey',
};

const sessionTypeIcons: Record<string, string> = {
    in_person: 'mdi-map-marker',
    virtual: 'mdi-video',
    hybrid: 'mdi-monitor-cellphone',
};

const filteredSessions = computed(() => {
    const now = new Date().toISOString();
    if (activeTab.value === 'upcoming') {
        return sessions.value.filter((s) => s.starts_at > now);
    }
    return sessions.value.filter((s) => s.ends_at <= now);
});

async function fetchSessions() {
    loading.value = true;
    error.value = '';
    try {
        const res = await axios.get('/api/lms/sessions');
        sessions.value = res.data.data ?? res.data;
    } catch {
        error.value = 'Error loading sessions.';
    } finally {
        loading.value = false;
    }
}

async function registerForSession(sessionId: number) {
    try {
        await axios.post(`/api/lms/sessions/${sessionId}/register`);
        await fetchSessions();
    } catch (e: unknown) {
        const msg =
            (e as { response?: { data?: { message?: string } } })?.response
                ?.data?.message ?? 'Registration failed.';
        error.value = msg;
    }
}

async function cancelRegistration(sessionId: number) {
    try {
        await axios.post(`/api/lms/sessions/${sessionId}/cancel-registration`);
        await fetchSessions();
    } catch {
        error.value = 'Failed to cancel registration.';
    }
}

function openAttendanceDialog(session: Session) {
    selectedSession.value = session;
    attendanceData.value = session.attendances
        .filter((a) => a.status !== 'cancelled')
        .map((a) => ({ user_id: a.user_id, status: a.status }));
    showAttendanceDialog.value = true;
}

async function submitAttendance() {
    if (!selectedSession.value) return;
    try {
        await axios.post(
            `/api/lms/sessions/${selectedSession.value.id}/attendance`,
            {
                attendances: attendanceData.value,
            },
        );
        showAttendanceDialog.value = false;
        await fetchSessions();
    } catch {
        error.value = 'Failed to mark attendance.';
    }
}

onMounted(fetchSessions);
</script>

<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <h1 class="text-h4 mb-4">ILT Sessions</h1>

                <v-alert
                    v-if="error"
                    type="error"
                    closable
                    class="mb-4"
                    @click:close="error = ''"
                >
                    {{ error }}
                </v-alert>

                <v-tabs v-model="activeTab" class="mb-4">
                    <v-tab value="upcoming">
                        <v-icon start>mdi-calendar-clock</v-icon>
                        Upcoming
                    </v-tab>
                    <v-tab value="past">
                        <v-icon start>mdi-calendar-check</v-icon>
                        Past
                    </v-tab>
                </v-tabs>

                <v-progress-linear v-if="loading" indeterminate class="mb-4" />

                <v-row>
                    <v-col
                        v-for="session in filteredSessions"
                        :key="session.id"
                        cols="12"
                        md="6"
                        lg="4"
                    >
                        <v-card elevation="2">
                            <v-card-title class="d-flex align-center">
                                <v-icon
                                    :icon="
                                        sessionTypeIcons[
                                            session.session_type
                                        ] || 'mdi-calendar'
                                    "
                                    class="mr-2"
                                />
                                {{ session.title }}
                            </v-card-title>
                            <v-card-subtitle>
                                {{ session.course?.title ?? 'N/A' }} ·
                                {{ session.instructor?.name ?? 'TBD' }}
                            </v-card-subtitle>
                            <v-card-text>
                                <div class="mb-1">
                                    <v-icon size="small">mdi-clock</v-icon>
                                    {{
                                        new Date(
                                            session.starts_at,
                                        ).toLocaleString()
                                    }}
                                    —
                                    {{
                                        new Date(
                                            session.ends_at,
                                        ).toLocaleString()
                                    }}
                                </div>
                                <div v-if="session.location" class="mb-1">
                                    <v-icon size="small">mdi-map-marker</v-icon>
                                    {{ session.location }}
                                </div>
                                <div v-if="session.meeting_url" class="mb-1">
                                    <v-icon size="small">mdi-link</v-icon>
                                    <a
                                        :href="session.meeting_url"
                                        target="_blank"
                                        >Join Meeting</a
                                    >
                                </div>
                                <div class="mb-1">
                                    <v-chip
                                        size="small"
                                        :color="
                                            session.session_type === 'virtual'
                                                ? 'blue'
                                                : session.session_type ===
                                                    'hybrid'
                                                  ? 'purple'
                                                  : 'green'
                                        "
                                    >
                                        {{ session.session_type }}
                                    </v-chip>
                                    <v-chip
                                        v-if="session.max_attendees"
                                        size="small"
                                        class="ml-1"
                                    >
                                        {{
                                            session.attendances.filter(
                                                (a) => a.status !== 'cancelled',
                                            ).length
                                        }}/{{ session.max_attendees }}
                                    </v-chip>
                                </div>
                            </v-card-text>
                            <v-card-actions>
                                <v-btn
                                    color="primary"
                                    size="small"
                                    @click="registerForSession(session.id)"
                                >
                                    Register
                                </v-btn>
                                <v-btn
                                    color="error"
                                    variant="text"
                                    size="small"
                                    @click="cancelRegistration(session.id)"
                                >
                                    Cancel
                                </v-btn>
                                <v-spacer />
                                <v-btn
                                    variant="outlined"
                                    size="small"
                                    @click="openAttendanceDialog(session)"
                                >
                                    <v-icon start>mdi-clipboard-check</v-icon>
                                    Attendance
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>

                <v-alert
                    v-if="!loading && filteredSessions.length === 0"
                    type="info"
                    class="mt-4"
                >
                    No sessions found.
                </v-alert>
            </v-col>
        </v-row>

        <!-- Attendance Dialog -->
        <v-dialog v-model="showAttendanceDialog" max-width="600">
            <v-card>
                <v-card-title
                    >Mark Attendance —
                    {{ selectedSession?.title }}</v-card-title
                >
                <v-card-text>
                    <v-list v-if="selectedSession">
                        <v-list-item
                            v-for="(att, idx) in attendanceData"
                            :key="idx"
                        >
                            <template #prepend>
                                <span>{{
                                    selectedSession.attendances.find(
                                        (a) => a.user_id === att.user_id,
                                    )?.user?.name ?? `User #${att.user_id}`
                                }}</span>
                            </template>
                            <template #append>
                                <v-select
                                    v-model="att.status"
                                    :items="[
                                        'registered',
                                        'confirmed',
                                        'attended',
                                        'absent',
                                    ]"
                                    density="compact"
                                    variant="outlined"
                                    style="min-width: 160px"
                                />
                            </template>
                        </v-list-item>
                    </v-list>
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn @click="showAttendanceDialog = false">Cancel</v-btn>
                    <v-btn color="primary" @click="submitAttendance"
                        >Save</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>
