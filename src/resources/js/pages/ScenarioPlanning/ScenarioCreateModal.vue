<script setup lang="ts">
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const api = useApi();
const { showSuccess, showError } = useNotification();

const name = ref('');
const description = ref('');
const decisionStatus = ref<
    'draft' | 'pending_approval' | 'approved' | 'rejected'
>('draft');
const executionStatus = ref<'planned' | 'in_progress' | 'paused' | 'completed'>(
    'planned',
);
const versionNumber = ref<number | null>(1);
const timeHorizonWeeks = ref<number | null>(12);
const saving = ref(false);

const emit = defineEmits(['created', 'close']);

const decisionOptions = [
    { value: 'draft', title: 'Borrador' },
    { value: 'pending_approval', title: 'Pendiente' },
    { value: 'approved', title: 'Aprobado' },
    { value: 'rejected', title: 'Rechazado' },
];

const executionOptions = [
    { value: 'planned', title: 'Planificado' },
    { value: 'in_progress', title: 'En ejecución' },
    { value: 'paused', title: 'Pausado' },
    { value: 'completed', title: 'Completado' },
];

const createScenario = async () => {
    if (!name.value.trim()) {
        showError('El nombre del escenario es obligatorio');
        return;
    }

    saving.value = true;
    try {
        const payload = {
            name: name.value,
            description: description.value,
            decision_status: decisionStatus.value,
            execution_status: executionStatus.value,
            version_number: versionNumber.value,
            time_horizon_weeks: timeHorizonWeeks.value,
        };
        const res: any = await api.post(
            '/api/strategic-planning/scenarios',
            payload,
        );
        const created = res?.data ?? res;
        showSuccess('Escenario creado');
        emit('created', created);
        emit('close');
        // navigate to detail if id present
        const id = created?.id || created?.data?.id;
        if (id) router.visit(`/strategic-planning/${id}`);
    } catch (err: any) {
        showError(err?.response?.data?.message || 'Error al crear escenario');
    } finally {
        saving.value = false;
    }
};

const close = () => emit('close');
</script>

<template>
    <v-card>
        <v-card-title>Crear nuevo escenario</v-card-title>
        <v-card-text>
            <v-row>
                <v-col cols="12">
                    <v-text-field v-model="name" label="Nombre" required />
                </v-col>

                <v-col cols="12">
                    <v-textarea
                        v-model="description"
                        label="Descripción"
                        rows="3"
                    />
                </v-col>

                <v-col cols="12" md="6">
                    <v-select
                        v-model="decisionStatus"
                        :items="decisionOptions"
                        item-title="title"
                        item-value="value"
                        label="Estado (Decisión)"
                    />
                </v-col>

                <v-col cols="12" md="6">
                    <v-select
                        v-model="executionStatus"
                        :items="executionOptions"
                        item-title="title"
                        item-value="value"
                        label="Estado (Ejecución)"
                    />
                </v-col>

                <v-col cols="12" md="6">
                    <v-text-field
                        v-model.number="versionNumber"
                        label="Versión"
                        type="number"
                    />
                </v-col>

                <v-col cols="12" md="6">
                    <v-text-field
                        v-model.number="timeHorizonWeeks"
                        label="Horizonte (semanas)"
                        type="number"
                    />
                </v-col>
            </v-row>
        </v-card-text>
        <v-card-actions>
            <v-spacer />
            <v-btn text @click="close">Cancelar</v-btn>
            <v-btn color="primary" :loading="saving" @click="createScenario"
                >Crear</v-btn
            >
        </v-card-actions>
    </v-card>
</template>

<style scoped></style>
