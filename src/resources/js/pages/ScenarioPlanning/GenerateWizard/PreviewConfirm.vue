<template>
    <div class="preview-confirm">
        <div class="preview-content">
            <h3>Confirmar consulta a la IA</h3>
            <p>Se generó el prompt. Revisa y autoriza la llamada al LLM.</p>
            <pre class="prompt">{{ promptPreview }}</pre>
            <div class="actions">
                <label
                    style="
                        display: flex;
                        align-items: center;
                        gap: 8px;
                        margin-bottom: 8px;
                    "
                >
                    <input type="checkbox" v-model="importAfterAccept" />
                    <span
                        >Importar entidades incubadas automáticamente al
                        aceptar</span
                    >
                </label>
                <button @click="$emit('edit')">Editar</button>
                <button @click="$emit('confirm', importAfterAccept)">
                    Autorizar llamada LLM
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { defineProps, ref } from 'vue';

const props = defineProps({ promptPreview: { type: String, required: true } });
const importAfterAccept = ref(false);
</script>

<style scoped>
.preview-confirm {
    background: white;
    padding: 1rem;
    border-radius: 6px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
}
.prompt {
    background: #f4f4f4;
    padding: 0.5rem;
    max-height: 300px;
    overflow: auto;
}
.actions {
    margin-top: 0.5rem;
}
.actions button {
    margin-right: 0.5rem;
}
</style>
