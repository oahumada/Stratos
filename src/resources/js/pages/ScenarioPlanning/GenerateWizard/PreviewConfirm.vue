<template>
    <v-card class="preview-confirm">
        <v-card-text>
            <div class="preview-header">
                <h3 class="title">Confirmar consulta a la IA</h3>
                <p class="subtitle">
                    Se generó el prompt. Revisa y autoriza la llamada al LLM.
                </p>
            </div>

            <v-sheet class="prompt-sheet pa-3" elevation="0">
                <pre class="prompt">{{ promptPreview }}</pre>
            </v-sheet>

            <v-row class="mt-3" align="center">
                <v-col cols="12" md="8">
                    <v-checkbox
                        v-model="importAfterAccept"
                        label="Importar entidades incubadas automáticamente al aceptar"
                    />
                </v-col>
                <v-col cols="12" md="4" class="d-flex justify-end">
                    <v-btn text color="primary" @click="$emit('edit')"
                        >Editar</v-btn
                    >
                    <v-btn
                        color="primary"
                        class="ml-2"
                        :loading="loading"
                        :disabled="loading"
                        @click="$emit('confirm', importAfterAccept)"
                        >Autorizar llamada LLM</v-btn
                    >
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>
</template>

<script setup lang="ts">
import { defineProps, ref } from 'vue';

const props = defineProps({
    promptPreview: { type: String, required: true },
    loading: { type: Boolean, default: false },
});
const importAfterAccept = ref(false);
</script>

<style scoped>
.preview-confirm {
    border-radius: 8px;
}
.preview-header .title {
    margin: 0;
    font-size: 1.1rem;
}
.preview-header .subtitle {
    margin: 0.25rem 0 0;
    color: #666;
}
.prompt-sheet {
    background: #f7f7f9;
    border-radius: 4px;
}
.prompt {
    margin: 0;
    white-space: pre-wrap;
    word-break: break-word;
    max-height: 360px;
    overflow: auto;
}
</style>
