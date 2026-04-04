<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref } from 'vue';

interface Webhook {
    id: number;
    url: string;
    secret: string;
    events: string[];
    is_active: boolean;
    failure_count: number;
    last_triggered_at: string | null;
}

const webhooks = ref<Webhook[]>([]);
const loading = ref(true);
const dialog = ref(false);
const testing = ref<number | null>(null);

const availableEvents = [
    'enrollment.completed',
    'course.completed',
    'quiz.submitted',
    'compliance.overdue',
    'certificate.issued',
];

const form = ref({
    url: '',
    secret: '',
    events: [] as string[],
});

async function fetchWebhooks() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/lms/webhooks');
        webhooks.value = data;
    } finally {
        loading.value = false;
    }
}

async function createWebhook() {
    await axios.post('/api/lms/webhooks', form.value);
    dialog.value = false;
    form.value = { url: '', secret: '', events: [] };
    await fetchWebhooks();
}

async function deleteWebhook(id: number) {
    await axios.delete(`/api/lms/webhooks/${id}`);
    await fetchWebhooks();
}

async function testWebhook(id: number) {
    testing.value = id;
    try {
        await axios.post(`/api/lms/webhooks/${id}/test`);
    } finally {
        testing.value = null;
    }
}

onMounted(fetchWebhooks);
</script>

<template>
    <v-container>
        <v-row>
            <v-col>
                <div class="d-flex justify-space-between align-center mb-4">
                    <h1 class="text-h4">Webhooks</h1>
                    <v-btn color="primary" @click="dialog = true">
                        <v-icon start>mdi-plus</v-icon>
                        Nuevo Webhook
                    </v-btn>
                </div>

                <v-card :loading="loading">
                    <v-list v-if="webhooks.length">
                        <v-list-item v-for="wh in webhooks" :key="wh.id">
                            <template #prepend>
                                <v-icon
                                    :color="wh.is_active ? 'green' : 'grey'"
                                >
                                    {{
                                        wh.is_active
                                            ? 'mdi-check-circle'
                                            : 'mdi-close-circle'
                                    }}
                                </v-icon>
                            </template>
                            <v-list-item-title>{{ wh.url }}</v-list-item-title>
                            <v-list-item-subtitle>
                                Eventos: {{ wh.events.join(', ') }} · Fallos:
                                {{ wh.failure_count }}
                            </v-list-item-subtitle>
                            <template #append>
                                <v-btn
                                    size="small"
                                    variant="text"
                                    :loading="testing === wh.id"
                                    @click="testWebhook(wh.id)"
                                >
                                    Test
                                </v-btn>
                                <v-btn
                                    size="small"
                                    variant="text"
                                    color="error"
                                    @click="deleteWebhook(wh.id)"
                                >
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </template>
                        </v-list-item>
                    </v-list>
                    <v-card-text v-else-if="!loading"
                        >No hay webhooks configurados.</v-card-text
                    >
                </v-card>
            </v-col>
        </v-row>

        <v-dialog v-model="dialog" max-width="500">
            <v-card title="Crear Webhook">
                <v-card-text>
                    <v-text-field v-model="form.url" label="URL" required />
                    <v-text-field
                        v-model="form.secret"
                        label="Secret"
                        type="password"
                        required
                    />
                    <v-select
                        v-model="form.events"
                        :items="availableEvents"
                        label="Eventos"
                        multiple
                        chips
                    />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn @click="dialog = false">Cancelar</v-btn>
                    <v-btn color="primary" @click="createWebhook">Crear</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>
