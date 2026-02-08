<template>
    <div class="generate-wizard">
        <h2>Generar Escenario (Asistido)</h2>
        <component :is="currentStepComponent" />

        <div class="wizard-controls">
            <button @click="prev" :disabled="store.step === 1">Atrás</button>
            <button v-if="store.step < 5" @click="next">Siguiente</button>
            <button v-else @click="onGenerate" :disabled="store.generating">
                Generar
            </button>
        </div>

        <div v-if="store.generationId" class="generation-status">
            <p>
                ID: {{ store.generationId }} — Estado:
                {{ store.generationStatus }}
            </p>
            <div style="margin-top: 8px">
                <button
                    v-if="store.generationStatus === 'complete'"
                    @click="acceptGeneration"
                    :disabled="accepting"
                >
                    Aceptar y crear escenario
                </button>
            </div>
            <button @click="refreshStatus">Actualizar</button>
            <pre v-if="store.generationResult">{{
                JSON.stringify(store.generationResult, null, 2)
            }}</pre>
        </div>

        <div v-if="showPreview" class="preview-modal-backdrop">
            <div class="preview-modal">
                <PreviewConfirm
                    :promptPreview="store.previewPrompt"
                    @confirm="onConfirmGenerate"
                    @edit="onEditPrompt"
                />
                <button class="close" @click="showPreview = false">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore';
import { computed, ref } from 'vue';
import PreviewConfirm from './PreviewConfirm.vue';
import StepHorizon from './StepHorizon.vue';
import StepIdentity from './StepIdentity.vue';
import StepIntent from './StepIntent.vue';
import StepResources from './StepResources.vue';
import StepSituation from './StepSituation.vue';

const store = useScenarioGenerationStore();

const stepComponents = {
    1: StepIdentity,
    2: StepSituation,
    3: StepIntent,
    4: StepResources,
    5: StepHorizon,
};

const currentStepComponent = computed(() => stepComponents[store.step]);

function next() {
    store.next();
}
function prev() {
    store.prev();
}

const showPreview = ref(false);

async function onGenerate() {
    try {
        // First get prompt preview from server
        await store.preview();
        showPreview.value = true;
    } catch (e) {
        console.error(e);
    }
}

async function onConfirmGenerate(importAfter = false) {
    showPreview.value = false;
    try {
        // persist operator choice to the generation store so accept flow can include it
        store.importAfterAccept = !!importAfter;
        await store.generate();
    } catch (e) {
        console.error(e);
    }
}

function onEditPrompt() {
    // Close preview and allow user to edit wizard fields
    showPreview.value = false;
    store.step = 1;
}

async function refreshStatus() {
    await store.fetchStatus();
}

const accepting = ref(false);
async function acceptGeneration() {
    if (!store.generationId) return;
    accepting.value = true;
    try {
        const res = await store.accept(store.generationId);
        // if backend returns scenario id, navigate to it
        const sid =
            res?.data?.scenario_id ||
            res?.data?.id ||
            (res?.data && res.data.id);
        if (sid) {
            // navigate to scenario detail
            window.location.href = `/scenario-planning/${sid}`;
            return;
        }
        // fallback: close dialog by reloading page
        window.location.reload();
    } catch (e) {
        console.error('Accept failed', e);
        alert(
            'Error al aceptar generación: ' +
                (e?.response?.data?.message || e.message || e),
        );
    } finally {
        accepting.value = false;
    }
}
</script>

<style scoped>
.generate-wizard {
    padding: 1rem;
}
.wizard-controls {
    margin-top: 1rem;
}
.wizard-controls button {
    margin-right: 0.5rem;
}
.generation-status {
    margin-top: 1rem;
    background: #f7f7f7;
    padding: 0.5rem;
}
</style>
