<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';

interface UserContent {
    id: number;
    title: string;
    description: string | null;
    content_type: string;
    content_body: string | null;
    content_url: string | null;
    status: string;
    views_count: number;
    likes_count: number;
    tags: string[] | null;
    author?: { id: number; name: string };
    course?: { id: number; title: string } | null;
}

const published = ref<UserContent[]>([]);
const pending = ref<UserContent[]>([]);
const loading = ref(true);
const tab = ref('browse');
const createDialog = ref(false);

const form = ref({
    title: '',
    description: '',
    content_type: 'article',
    content_body: '',
    content_url: '',
});

const contentTypes = [
    { title: 'Artículo', value: 'article' },
    { title: 'Video', value: 'video_link' },
    { title: 'Recurso', value: 'resource' },
    { title: 'Consejo', value: 'tip' },
    { title: 'Tutorial', value: 'tutorial' },
];

async function fetchPublished() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/lms/ugc');
        published.value = data.data || data;
    } finally {
        loading.value = false;
    }
}

async function fetchPending() {
    try {
        const { data } = await axios.get('/api/lms/ugc/pending');
        pending.value = Array.isArray(data) ? data : data.data || [];
    } catch {
        // User may not have manage permission
    }
}

async function createContent() {
    const { data } = await axios.post('/api/lms/ugc', form.value);
    // Auto-submit for review
    await axios.post(`/api/lms/ugc/${data.id}/submit`);
    createDialog.value = false;
    form.value = { title: '', description: '', content_type: 'article', content_body: '', content_url: '' };
    await fetchPublished();
    await fetchPending();
}

async function approve(id: number) {
    await axios.post(`/api/lms/ugc/${id}/approve`);
    await fetchPending();
    await fetchPublished();
}

async function reject(id: number) {
    await axios.post(`/api/lms/ugc/${id}/reject`, { reason: 'No cumple los criterios.' });
    await fetchPending();
}

async function like(id: number) {
    await axios.post(`/api/lms/ugc/${id}/like`);
    await fetchPublished();
}

onMounted(async () => {
    await Promise.all([fetchPublished(), fetchPending()]);
});
</script>

<template>
    <v-container>
        <v-row class="mb-4" align="center">
            <v-col>
                <h1 class="text-h4">Comunidad</h1>
            </v-col>
            <v-col cols="auto">
                <v-btn color="primary" @click="createDialog = true">
                    <v-icon start>mdi-plus</v-icon> Crear Contenido
                </v-btn>
            </v-col>
        </v-row>

        <v-tabs v-model="tab" class="mb-4">
            <v-tab value="browse">Publicados</v-tab>
            <v-tab value="moderation">Moderación ({{ pending.length }})</v-tab>
        </v-tabs>

        <v-progress-linear v-if="loading" indeterminate />

        <div v-if="tab === 'browse'">
            <v-row>
                <v-col v-for="content in published" :key="content.id" cols="12" md="6" lg="4">
                    <v-card variant="outlined">
                        <v-card-title>{{ content.title }}</v-card-title>
                        <v-card-subtitle>
                            {{ content.author?.name }} · {{ content.content_type }}
                        </v-card-subtitle>
                        <v-card-text>
                            <p v-if="content.description">{{ content.description }}</p>
                            <div class="d-flex align-center mt-2">
                                <v-icon size="small" class="mr-1">mdi-eye</v-icon>
                                {{ content.views_count }}
                                <v-icon size="small" class="ml-3 mr-1">mdi-thumb-up</v-icon>
                                {{ content.likes_count }}
                            </div>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn size="small" @click="like(content.id)">
                                <v-icon start>mdi-thumb-up-outline</v-icon> Me gusta
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </div>

        <div v-if="tab === 'moderation'">
            <v-card v-for="content in pending" :key="content.id" class="mb-3" variant="outlined">
                <v-card-title>{{ content.title }}</v-card-title>
                <v-card-subtitle>{{ content.author?.name }} · {{ content.content_type }}</v-card-subtitle>
                <v-card-text>
                    <p v-if="content.description">{{ content.description }}</p>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="success" @click="approve(content.id)">Aprobar</v-btn>
                    <v-btn color="error" @click="reject(content.id)">Rechazar</v-btn>
                </v-card-actions>
            </v-card>
            <v-alert v-if="pending.length === 0" type="info">Sin contenido pendiente de moderación.</v-alert>
        </div>

        <!-- Create Dialog -->
        <v-dialog v-model="createDialog" max-width="600">
            <v-card>
                <v-card-title>Crear Contenido</v-card-title>
                <v-card-text>
                    <v-text-field v-model="form.title" label="Título" class="mb-3" />
                    <v-textarea v-model="form.description" label="Descripción" rows="2" class="mb-3" />
                    <v-select v-model="form.content_type" :items="contentTypes" label="Tipo" class="mb-3" />
                    <v-textarea v-model="form.content_body" label="Contenido" rows="4" class="mb-3" />
                    <v-text-field v-model="form.content_url" label="URL (opcional)" />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn @click="createDialog = false">Cancelar</v-btn>
                    <v-btn color="primary" @click="createContent">Publicar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>
