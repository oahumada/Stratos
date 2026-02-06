<template>
  <div class="generate-wizard">
    <h2>Generar Escenario (Asistido)</h2>
    <component :is="currentStepComponent" />

    <div class="wizard-controls">
      <button @click="prev" :disabled="store.step === 1">Atrás</button>
      <button v-if="store.step < 5" @click="next">Siguiente</button>
      <button v-else @click="onGenerate" :disabled="store.generating">Generar</button>
    </div>

    <div v-if="store.generationId" class="generation-status">
      <p>ID: {{ store.generationId }} — Estado: {{ store.generationStatus }}</p>
      <button @click="refreshStatus">Actualizar</button>
      <pre v-if="store.generationResult">{{ JSON.stringify(store.generationResult, null, 2) }}</pre>
    </div>

    <div v-if="showPreview" class="preview-modal-backdrop">
      <div class="preview-modal">
        <PreviewConfirm :promptPreview="store.previewPrompt" @confirm="onConfirmGenerate" @edit="onEditPrompt" />
        <button class="close" @click="showPreview = false">Cerrar</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore'
import StepIdentity from './StepIdentity.vue'
import StepSituation from './StepSituation.vue'
import StepIntent from './StepIntent.vue'
import StepResources from './StepResources.vue'
import StepHorizon from './StepHorizon.vue'
import PreviewConfirm from './PreviewConfirm.vue'

const store = useScenarioGenerationStore()

const stepComponents = {
  1: StepIdentity,
  2: StepSituation,
  3: StepIntent,
  4: StepResources,
  5: StepHorizon,
}

const currentStepComponent = computed(() => stepComponents[store.step])

function next() { store.next() }
function prev() { store.prev() }

const showPreview = ref(false)

async function onGenerate() {
  try {
    // First get prompt preview from server
    await store.preview()
    showPreview.value = true
  } catch (e) {
    console.error(e)
  }
}

async function onConfirmGenerate() {
  showPreview.value = false
  try {
    await store.generate()
  } catch (e) {
    console.error(e)
  }
}

function onEditPrompt() {
  // Close preview and allow user to edit wizard fields
  showPreview.value = false
  store.step = 1
}

async function refreshStatus() {
  await store.fetchStatus()
}
</script>

<style scoped>
.generate-wizard { padding: 1rem }
.wizard-controls { margin-top: 1rem }
.wizard-controls button { margin-right: 0.5rem }
.generation-status { margin-top: 1rem; background:#f7f7f7; padding:0.5rem }
</style>
<template>
  <v-container>
    <v-card>
      <v-card-title>Generar Escenario asistido</v-card-title>
      <v-card-text>
        <StepIdentity />
      </v-card-text>
      <v-card-actions>
        <v-btn color="primary" @click="onGenerate" :loading="store.loading">Generar</v-btn>
      </v-card-actions>
    </v-card>
    <div v-if="store.result">
      <h3>Resultado</h3>
      <pre>{{ JSON.stringify(store.result, null, 2) }}</pre>
    </div>
  </v-container>
</template>

<script setup>
import StepIdentity from './StepIdentity.vue';
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore';
const store = useScenarioGenerationStore();

const onGenerate = async () => {
  try {
    await store.generate();
    // poll result
    setTimeout(async () => {
      await store.fetchResult();
    }, 500);
  } catch (e) {
    // error handled in store
  }
};
</script>
