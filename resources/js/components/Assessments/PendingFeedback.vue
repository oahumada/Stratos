<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref } from 'vue';

const pendingRequests = ref<any[]>([]);
const loading = ref(true);
const dialog = ref(false);
const selectedRequest = ref<any>(null);
const answers = ref<any[]>([]);
const submitting = ref(false);

const loadRequests = async () => {
    loading.value = true;
    try {
        const response = await axios.get(
            '/api/strategic-planning/assessments/feedback/pending',
        );
        pendingRequests.value = response.data;
    } catch (e) {
        console.error('Error loading pending requests', e);
    } finally {
        loading.value = false;
    }
};

const openFeedbackForm = (request: any) => {
    selectedRequest.value = request;
    answers.value = [
        {
            question:
                '¿Cuál es el mayor impacto positivo de este integrante en el equipo?',
            answer: '',
        },
        {
            question:
                '¿En qué situación lo has visto bajo presión y cómo reaccionó?',
            answer: '',
        },
        {
            question:
                '¿Qué es lo único que debería empezar a hacer diferente para crecer?',
            answer: '',
        },
    ];
    dialog.value = true;
};

const submitFeedback = async () => {
    submitting.value = true;
    try {
        await axios.post(
            '/api/strategic-planning/assessments/feedback/submit',
            {
                request_id: selectedRequest.value.id,
                answers: answers.value,
            },
        );
        dialog.value = false;
        await loadRequests();
    } catch (e) {
        console.error('Error submitting feedback', e);
    } finally {
        submitting.value = false;
    }
};

onMounted(loadRequests);
</script>

<template>
    <div v-if="pendingRequests.length > 0" class="mb-6">
        <v-alert
            color="indigo-lighten-5"
            border="start"
            border-color="indigo"
            elevation="1"
        >
            <template #prepend>
                <v-icon color="indigo" size="large">mdi-account-heart</v-icon>
            </template>

            <div class="d-flex align-center justify-space-between w-100">
                <div>
                    <div class="text-subtitle-1 font-weight-bold indigo--text">
                        Feedback 360 Pendiente
                    </div>
                    <div class="text-body-2 text-secondary">
                        Tienes {{ pendingRequests.length }} solicitudes de
                        feedback pendientes por completar.
                    </div>
                </div>
                <div
                    class="d-flex pa-1 gap-2 overflow-x-auto"
                    style="max-width: 60%"
                >
                    <v-chip
                        v-for="req in pendingRequests"
                        :key="req.id"
                        closable
                        @click="openFeedbackForm(req)"
                        class="ma-1"
                        color="indigo"
                        variant="flat"
                    >
                        {{ req.subject.full_name }}
                    </v-chip>
                </div>
            </div>
        </v-alert>

        <!-- Feedback Form Dialog -->
        <v-dialog v-model="dialog" max-width="600" persistent>
            <v-card v-if="selectedRequest">
                <v-card-title class="bg-indigo py-4 text-white">
                    Feedback para {{ selectedRequest.subject.full_name }}
                    <div class="text-caption text-indigo-lighten-4">
                        Relación: {{ selectedRequest.relationship }}
                    </div>
                </v-card-title>

                <v-card-text class="pa-6">
                    <div v-for="(q, i) in answers" :key="i" class="mb-4">
                        <div class="text-subtitle-2 indigo--text mb-2">
                            {{ q.question }}
                        </div>
                        <v-textarea
                            v-model="q.answer"
                            variant="outlined"
                            density="compact"
                            rows="3"
                            placeholder="Escribe tu observación aquí..."
                            hide-details
                        ></v-textarea>
                    </div>
                </v-card-text>

                <v-card-actions class="pa-4 pt-0">
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="dialog = false">Cerrar</v-btn>
                    <v-btn
                        color="indigo"
                        variant="flat"
                        :loading="submitting"
                        @click="submitFeedback"
                    >
                        Enviar Feedback
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>
