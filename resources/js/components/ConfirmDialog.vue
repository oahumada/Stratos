<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    modelValue: boolean;
    title?: string;
    message?: string;
    confirmText?: string;
    cancelText?: string;
    confirmColor?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
    (e: 'confirm'): void;
    (e: 'cancel'): void;
}>();

const dialogModel = computed({
    get: () => props.modelValue,
    set: (value: boolean) => emit('update:modelValue', value),
});

const handleConfirm = () => {
    emit('confirm');
    dialogModel.value = false;
};

const handleCancel = () => {
    emit('cancel');
    dialogModel.value = false;
};
</script>

<template>
    <v-dialog v-model="dialogModel" max-width="500" persistent>
        <v-card>
            <v-card-title class="text-h5">
                {{ title || '¿Confirmar acción?' }}
            </v-card-title>

            <v-card-text>
                {{ message || '¿Está seguro que desea realizar esta acción?' }}
            </v-card-text>

            <v-card-actions>
                <v-spacer />
                <v-btn variant="text" @click="handleCancel">
                    {{ cancelText || 'Cancelar' }}
                </v-btn>
                <v-btn
                    :color="confirmColor || 'error'"
                    variant="elevated"
                    @click="handleConfirm"
                >
                    {{ confirmText || 'Confirmar' }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
