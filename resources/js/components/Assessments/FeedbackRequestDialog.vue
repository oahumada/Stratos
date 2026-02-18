<script setup lang="ts">
import axios from 'axios';
import { onMounted, ref } from 'vue';

const props = defineProps<{
    subjectId: number;
}>();

const emit = defineEmits(['requested']);

const dialog = ref(false);
const loading = ref(false);
const people = ref<any[]>([]);
const selectedEvaluator = ref<number | null>(null);
const relationship = ref('peer');

const relationships = [
    { title: 'Par (Peer)', value: 'peer' },
    { title: 'Jefe Directo (Supervisor)', value: 'supervisor' },
    { title: 'Subordinado (Direct Report)', value: 'subordinate' },
    { title: 'Otro', value: 'other' },
];

const loadPeople = async () => {
    try {
        const response = await axios.post('/api/form-schema/search/People', {
            data: { filters: {} },
        });
        people.value = response.data.data.filter(
            (p: any) => p.id !== props.subjectId,
        );
    } catch (e) {
        console.error('Error loading people', e);
    }
};

const submitRequest = async () => {
    if (!selectedEvaluator.value) return;

    loading.value = true;
    try {
        await axios.post(
            '/api/strategic-planning/assessments/feedback/request',
            {
                subject_id: props.subjectId,
                evaluator_id: selectedEvaluator.value,
                relationship: relationship.value,
            },
        );
        dialog.value = false;
        emit('requested');
    } catch (e) {
        console.error('Error requesting feedback', e);
    } finally {
        loading.value = false;
    }
};

onMounted(loadPeople);
</script>

<template>
    <v-dialog v-model="dialog" max-width="500">
        <template v-slot:activator="{ props }">
            <v-btn
                v-bind="props"
                variant="tonal"
                color="secondary"
                size="small"
                prepend-icon="mdi-account-voice"
            >
                Solicitar Feedback 360
            </v-btn>
        </template>

        <v-card title="Solicitar Feedback de Terceros">
            <v-card-text>
                <p class="text-body-2 mb-4">
                    Selecciona a un colaborador para que brinde feedback
                    cualitativo sobre el desempeño y potencial de este
                    integrante.
                </p>

                <v-autocomplete
                    v-model="selectedEvaluator"
                    :items="people"
                    item-title="full_name"
                    item-value="id"
                    label="Evaluador"
                    placeholder="Busca por nombre..."
                    variant="outlined"
                    density="comfortable"
                ></v-autocomplete>

                <v-select
                    v-model="relationship"
                    :items="relationships"
                    label="Relación con el sujeto"
                    variant="outlined"
                    density="comfortable"
                ></v-select>
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn variant="text" @click="dialog = false">Cancelar</v-btn>
                <v-btn
                    color="primary"
                    :loading="loading"
                    :disabled="!selectedEvaluator"
                    @click="submitRequest"
                >
                    Enviar Solicitud
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
