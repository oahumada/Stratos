<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';

interface CalendarEvent {
    id: number;
    title: string;
    description: string | null;
    event_type: string;
    starts_at: string;
    ends_at: string;
    all_day: boolean;
}

const events = ref<CalendarEvent[]>([]);
const loading = ref(true);
const dialog = ref(false);
const syncing = ref(false);

const eventTypes = ['course_deadline', 'compliance_deadline', 'session', 'quiz_deadline'];

const form = ref({
    title: '',
    description: '',
    event_type: 'session',
    starts_at: '',
    ends_at: '',
});

async function fetchEvents() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/lms/calendar');
        events.value = data;
    } finally {
        loading.value = false;
    }
}

async function createEvent() {
    await axios.post('/api/lms/calendar', form.value);
    dialog.value = false;
    form.value = { title: '', description: '', event_type: 'session', starts_at: '', ends_at: '' };
    await fetchEvents();
}

async function deleteEvent(id: number) {
    await axios.delete(`/api/lms/calendar/${id}`);
    await fetchEvents();
}

async function downloadIcal() {
    window.location.href = '/api/lms/calendar/ical';
}

async function syncCompliance() {
    syncing.value = true;
    try {
        await axios.post('/api/lms/calendar/sync-compliance');
        await fetchEvents();
    } finally {
        syncing.value = false;
    }
}

function formatDate(d: string) {
    return new Date(d).toLocaleString();
}

onMounted(fetchEvents);
</script>

<template>
    <v-container>
        <v-row>
            <v-col>
                <div class="d-flex justify-space-between align-center mb-4">
                    <h1 class="text-h4">Calendario LMS</h1>
                    <div class="d-flex ga-2">
                        <v-btn variant="outlined" @click="downloadIcal">
                            <v-icon start>mdi-download</v-icon>
                            iCal
                        </v-btn>
                        <v-btn variant="outlined" :loading="syncing" @click="syncCompliance">
                            <v-icon start>mdi-sync</v-icon>
                            Sync Compliance
                        </v-btn>
                        <v-btn color="primary" @click="dialog = true">
                            <v-icon start>mdi-plus</v-icon>
                            Nuevo Evento
                        </v-btn>
                    </div>
                </div>

                <v-card :loading="loading">
                    <v-list v-if="events.length">
                        <v-list-item v-for="ev in events" :key="ev.id">
                            <template #prepend>
                                <v-icon>mdi-calendar</v-icon>
                            </template>
                            <v-list-item-title>{{ ev.title }}</v-list-item-title>
                            <v-list-item-subtitle>
                                {{ ev.event_type }} · {{ formatDate(ev.starts_at) }} – {{ formatDate(ev.ends_at) }}
                            </v-list-item-subtitle>
                            <template #append>
                                <v-btn size="small" variant="text" color="error" @click="deleteEvent(ev.id)">
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </template>
                        </v-list-item>
                    </v-list>
                    <v-card-text v-else-if="!loading">No hay eventos.</v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-dialog v-model="dialog" max-width="500">
            <v-card title="Crear Evento">
                <v-card-text>
                    <v-text-field v-model="form.title" label="Título" required />
                    <v-textarea v-model="form.description" label="Descripción" rows="2" />
                    <v-select v-model="form.event_type" :items="eventTypes" label="Tipo" />
                    <v-text-field v-model="form.starts_at" label="Inicio" type="datetime-local" />
                    <v-text-field v-model="form.ends_at" label="Fin" type="datetime-local" />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn @click="dialog = false">Cancelar</v-btn>
                    <v-btn color="primary" @click="createEvent">Crear</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>
