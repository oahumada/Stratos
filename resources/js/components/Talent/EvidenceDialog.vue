<template>
    <v-dialog v-model="dialog" max-width="600">
        <v-card>
            <v-card-title class="pa-4 d-flex align-center">
                <v-icon color="primary" class="mr-2">mdi-attachment</v-icon>
                Evidencias de Progreso
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
                    <v-tab value="upload">Subir Evidencia</v-tab>
                </v-tabs>

                <v-window v-model="activeTab" class="pa-4">
                    <!-- Evidence List -->
                    <v-window-item value="list">
                        <div v-if="loading" class="d-flex pa-4 justify-center">
                            <v-progress-circular
                                indeterminate
                                color="primary"
                            ></v-progress-circular>
                        </div>
                        <div
                            v-else-if="evidences.length === 0"
                            class="pa-8 text-center"
                        >
                            <v-icon
                                size="48"
                                color="grey-lighten-2"
                                class="mb-2"
                                >mdi-file-question-outline</v-icon
                            >
                            <div class="text-subtitle-1 text-grey">
                                Aún no hay evidencias
                            </div>
                            <v-btn
                                color="primary"
                                variant="text"
                                size="small"
                                @click="activeTab = 'upload'"
                                class="mt-2"
                            >
                                Subir primera evidencia
                            </v-btn>
                        </div>
                        <div v-else>
                            <v-list lines="two">
                                <v-list-item
                                    v-for="evidence in evidences"
                                    :key="evidence.id"
                                    :title="evidence.title"
                                    :subtitle="evidence.description"
                                >
                                    <template v-slot:prepend>
                                        <v-avatar color="blue-lighten-5">
                                            <v-icon
                                                color="blue"
                                                :icon="getIcon(evidence.type)"
                                            ></v-icon>
                                        </v-avatar>
                                    </template>
                                    <template v-slot:append>
                                        <v-btn
                                            v-if="evidence.file_path"
                                            icon="mdi-download"
                                            variant="text"
                                            size="small"
                                            :href="
                                                '/storage/' + evidence.file_path
                                            "
                                            target="_blank"
                                        ></v-btn>
                                        <v-btn
                                            v-if="evidence.external_url"
                                            icon="mdi-open-in-new"
                                            variant="text"
                                            size="small"
                                            :href="evidence.external_url"
                                            target="_blank"
                                        ></v-btn>
                                        <v-btn
                                            icon="mdi-delete-outline"
                                            variant="text"
                                            size="small"
                                            color="error"
                                            @click="deleteEvidence(evidence.id)"
                                        ></v-btn>
                                    </template>
                                </v-list-item>
                            </v-list>
                        </div>
                    </v-window-item>

                    <!-- Upload Form -->
                    <v-window-item value="upload">
                        <v-form ref="form" v-model="valid">
                            <v-text-field
                                v-model="newEvidence.title"
                                label="Título de la evidencia"
                                variant="outlined"
                                density="compact"
                                :rules="[
                                    (v) => !!v || 'El título es requerido',
                                ]"
                                class="mb-2"
                            ></v-text-field>

                            <v-textarea
                                v-model="newEvidence.description"
                                label="Descripción resumida"
                                variant="outlined"
                                density="compact"
                                rows="2"
                                class="mb-2"
                            ></v-textarea>

                            <v-select
                                v-model="newEvidence.type"
                                :items="[
                                    {
                                        title: 'Archivo/Documento',
                                        value: 'file',
                                    },
                                    { title: 'Enlace Externo', value: 'link' },
                                    { title: 'Nota de texto', value: 'text' },
                                ]"
                                label="Tipo de Evidencia"
                                variant="outlined"
                                density="compact"
                                class="mb-2"
                            ></v-select>

                            <v-file-input
                                v-if="newEvidence.type === 'file'"
                                v-model="newEvidence.file"
                                label="Seleccionar archivo"
                                variant="outlined"
                                density="compact"
                                prepend-icon="mdi-paperclip"
                                show-size
                                :rules="[
                                    (v) => !!v || 'El archivo es requerido',
                                ]"
                            ></v-file-input>

                            <v-text-field
                                v-if="newEvidence.type === 'link'"
                                v-model="newEvidence.external_url"
                                label="URL externa"
                                variant="outlined"
                                density="compact"
                                placeholder="https://..."
                                :rules="[(v) => !!v || 'La URL es requerida']"
                            ></v-text-field>

                            <div class="d-flex mt-4 justify-end">
                                <v-btn
                                    color="primary"
                                    :loading="saving"
                                    :disabled="!valid"
                                    @click="saveEvidence"
                                >
                                    Guardar Evidencia
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
});

const dialog = ref(false);
const activeTab = ref('list');
const evidences = ref([]);
const loading = ref(false);
const saving = ref(false);
const valid = ref(false);

const newEvidence = ref({
    title: '',
    description: '',
    type: 'file',
    file: null,
    external_url: '',
});

const open = () => {
    dialog.value = true;
    fetchEvidences();
};

const fetchEvidences = async () => {
    if (!props.actionId) return;
    loading.value = true;
    try {
        const response = await axios.get('/api/evidences', {
            params: { development_action_id: props.actionId },
        });
        evidences.value = response.data.data;
    } catch (e) {
        console.error('Error fetching evidences', e);
    } finally {
        loading.value = false;
    }
};

const saveEvidence = async () => {
    saving.value = true;
    try {
        const formData = new FormData();
        formData.append('development_action_id', props.actionId.toString());
        formData.append('title', newEvidence.value.title);
        formData.append('description', newEvidence.value.description);
        formData.append('type', newEvidence.value.type);

        if (newEvidence.value.type === 'file' && newEvidence.value.file) {
            formData.append('file', newEvidence.value.file);
        }
        if (
            newEvidence.value.type === 'link' &&
            newEvidence.value.external_url
        ) {
            formData.append('external_url', newEvidence.value.external_url);
        }

        await axios.post('/api/evidences', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });

        // Reset form
        newEvidence.value = {
            title: '',
            description: '',
            type: 'file',
            file: null,
            external_url: '',
        };
        activeTab.value = 'list';
        await fetchEvidences();
    } catch (e) {
        console.error('Error saving evidence', e);
    } finally {
        saving.value = false;
    }
};

const deleteEvidence = async (id: number) => {
    if (!confirm('¿Seguro que deseas eliminar esta evidencia?')) return;
    try {
        await axios.delete(`/api/evidences/${id}`);
        await fetchEvidences();
    } catch (e) {
        console.error('Error deleting evidence', e);
    }
};

const getIcon = (type: string) => {
    switch (type) {
        case 'file':
            return 'mdi-file-document-outline';
        case 'link':
            return 'mdi-link-variant';
        case 'text':
            return 'mdi-text-box-outline';
        default:
            return 'mdi-paperclip';
    }
};

defineExpose({ open });
</script>
