<template>
    <v-dialog v-model="dialog" max-width="700">
        <v-card>
            <v-card-title class="pa-4 d-flex align-center">
                <v-icon color="primary" class="mr-2">mdi-account-group</v-icon>
                Sesiones de Mentoría
                <v-spacer></v-spacer>
                <v-btn
                    icon="mdi-close"
                    variant="text"
                    @click="dialog = false"
                ></v-btn>
            </v-card-title>

            <v-card-text class="pa-0">
                <v-tabs v-model="activeTab" grow border-b>
                    <v-tab value="list">Historial</v-tab>
                    <v-tab value="add">Nueva Sesión</v-tab>
                </v-tabs>

                <v-window v-model="activeTab" class="pa-4">
                    <!-- Session List -->
                    <v-window-item value="list">
                        <div v-if="loading" class="d-flex pa-4 justify-center">
                            <v-progress-circular
                                indeterminate
                                color="primary"
                            ></v-progress-circular>
                        </div>
                        <div
                            v-else-if="sessions.length === 0"
                            class="pa-8 text-center"
                        >
                            <v-icon
                                size="48"
                                color="grey-lighten-2"
                                class="mb-2"
                                >mdi-comment-outline</v-icon
                            >
                            <div class="text-subtitle-1 text-grey">
                                No hay sesiones registradas
                            </div>
                            <v-btn
                                color="primary"
                                variant="text"
                                size="small"
                                @click="activeTab = 'add'"
                                class="mt-2"
                            >
                                Registrar la primera
                            </v-btn>
                        </div>
                        <v-timeline v-else density="compact" align="start">
                            <v-timeline-item
                                v-for="session in sessions"
                                :key="session.id"
                                dot-color="primary"
                                size="x-small"
                            >
                                <div
                                    class="pa-3 bg-grey-lighten-5 mb-4 rounded border"
                                >
                                    <div
                                        class="d-flex justify-space-between mb-1 align-baseline"
                                    >
                                        <div class="font-weight-bold">
                                            {{
                                                formatDate(session.session_date)
                                            }}
                                        </div>
                                        <v-chip
                                            size="x-small"
                                            :color="
                                                getStatusColor(session.status)
                                            "
                                            class="text-capitalize"
                                        >
                                            {{ session.status }}
                                        </v-chip>
                                    </div>
                                    <div class="text-body-2 mb-2 italic">
                                        "{{ session.summary }}"
                                    </div>
                                    <div
                                        v-if="session.next_steps"
                                        class="text-caption"
                                    >
                                        <span class="font-weight-bold"
                                            >Siguientes pasos:</span
                                        >
                                        {{ session.next_steps }}
                                    </div>
                                    <div class="text-caption text-grey mt-1">
                                        Duración:
                                        {{ session.duration_minutes }} min
                                    </div>
                                </div>
                            </v-timeline-item>
                        </v-timeline>
                    </v-window-item>

                    <!-- Add Session Form -->
                    <v-window-item value="add">
                        <v-form ref="form" v-model="valid">
                            <v-row>
                                <v-col cols="12" sm="6">
                                    <v-text-field
                                        v-model="newSession.session_date"
                                        label="Fecha de la Sesión"
                                        type="date"
                                        variant="outlined"
                                        density="compact"
                                        :rules="[
                                            (v) =>
                                                !!v || 'La fecha es requerida',
                                        ]"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <v-text-field
                                        v-model="newSession.duration_minutes"
                                        label="Duración (minutos)"
                                        type="number"
                                        variant="outlined"
                                        density="compact"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea
                                        v-model="newSession.summary"
                                        label="Resumen de la Sesión"
                                        placeholder="¿De qué hablaron? ¿Qué se cubrió?"
                                        variant="outlined"
                                        counter
                                        rows="3"
                                        :rules="[
                                            (v) =>
                                                !!v ||
                                                'El resumen es requerido',
                                        ]"
                                    ></v-textarea>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea
                                        v-model="newSession.next_steps"
                                        label="Compromisos / Siguientes Pasos"
                                        placeholder="Acciones para la próxima reunión"
                                        variant="outlined"
                                        rows="2"
                                    ></v-textarea>
                                </v-col>
                            </v-row>
                            <div class="d-flex mt-2 justify-end">
                                <v-btn
                                    color="primary"
                                    :loading="saving"
                                    :disabled="!valid"
                                    @click="saveSession"
                                >
                                    Guardar Sesión
                                </v-btn>
                            </div>
                        </v-form>
                    </v-window-item>
                </v-window>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script setup lang="ts">
import axios from 'axios';
import { ref } from 'vue';

const props = defineProps({
    actionId: {
        type: Number,
        required: true,
    },
    actionTitle: {
        type: String,
        default: 'Sesión de Mentoría',
    },
});

const dialog = ref(false);
const activeTab = ref('list');
const sessions = ref([]);
const loading = ref(false);
const saving = ref(false);
const valid = ref(false);

const newSession = ref({
    session_date: new Date().toISOString().substr(0, 10),
    summary: '',
    next_steps: '',
    duration_minutes: 60,
    status: 'completed',
});

const open = () => {
    dialog.value = true;
    fetchSessions();
};

const fetchSessions = async () => {
    if (!props.actionId) return;
    loading.value = true;
    try {
        const response = await axios.get('/api/mentorship-sessions', {
            params: { development_action_id: props.actionId },
        });
        sessions.value = response.data.data;
    } catch (e) {
        console.error('Error fetching sessions', e);
    } finally {
        loading.value = false;
    }
};

const saveSession = async () => {
    saving.value = true;
    try {
        await axios.post('/api/mentorship-sessions', {
            ...newSession.value,
            development_action_id: props.actionId,
        });

        // Reset form and go to list
        newSession.value = {
            session_date: new Date().toISOString().substr(0, 10),
            summary: '',
            next_steps: '',
            duration_minutes: 60,
            status: 'completed',
        };
        activeTab.value = 'list';
        await fetchSessions();
    } catch (e) {
        console.error('Error saving session', e);
    } finally {
        saving.value = false;
    }
};

const formatDate = (dateStr: string) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString();
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'completed':
            return 'success';
        case 'scheduled':
            return 'info';
        case 'cancelled':
            return 'error';
        default:
            return 'grey';
    }
};

defineExpose({ open });
</script>
