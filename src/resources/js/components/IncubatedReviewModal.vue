<template>
    <v-dialog v-model="open" max-width="800">
        <v-card>
            <v-card-title>
                <div>
                    <div class="text-h6">Revisión: {{ item?.name }}</div>
                    <div class="text-caption">{{ item?.description }}</div>
                </div>
            </v-card-title>
            <v-card-text>
                <div v-if="!item">No hay datos.</div>
                <div v-else>
                    <div class="mb-3">
                        <strong>Competencias</strong>
                        <ul>
                            <li
                                v-for="c in item.competencies || []"
                                :key="c.id"
                            >
                                {{ c.name }}
                            </li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <strong>Metadatos</strong>
                        <pre>{{ JSON.stringify(item, null, 2) }}</pre>
                    </div>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn text @click="$emit('close')">Cerrar</v-btn>
                <v-btn color="primary" @click="gotoEdit">Editar</v-btn>
                <v-btn color="success" @click="promote" :loading="promoting"
                    >Promover</v-btn
                >
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup lang="ts">
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    item: { type: Object, default: null },
    scenarioId: { type: [Number, String], default: null },
});
const emit = defineEmits(['update:modelValue', 'close', 'promoted']);

const api = useApi();
const { showSuccess, showError } = useNotification();

const promoting = ref(false);
const open = ref(!!props.modelValue);

watch(
    () => props.modelValue,
    (v) => {
        open.value = !!v;
    },
);

watch(open, (v) => {
    emit('update:modelValue', v);
});

function gotoEdit() {
    if (!props.item?.id) return;
    window.location.href = `/capabilities/${props.item.id}`;
}

async function promote() {
    if (!props.scenarioId || !props.item?.id) return;
    promoting.value = true;
    try {
        const res: any = await api.post(
            `/api/strategic-planning/scenarios/${props.scenarioId}/capabilities/${props.item.id}/promote`,
        );
        showSuccess('Capability promovida');
        emit('promoted', res?.data ?? null);
        emit('close');
        open.value = false;
    } catch (e: any) {
        console.error(e);
        showError('Error promoviendo capability');
    } finally {
        promoting.value = false;
    }
}
</script>
