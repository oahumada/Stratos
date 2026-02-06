<template>
  <div>
    <h3>Intención Estratégica</h3>
    <label>Objetivo principal
      <textarea v-model="store.data.strategic_goal"></textarea>
    </label>
    <label>Mercados objetivo
      <input v-model="store.data.target_markets" />
    </label>
    <label>Crecimiento esperado
      <input v-model="store.data.expected_growth" />
    </label>
    <label>Tipo de transformación (coma-separado)
      <input v-model="transformation" @input="onTransformationChange" />
    </label>
    <label>Iniciativas clave
      <textarea v-model="store.data.key_initiatives"></textarea>
    </label>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore'
const store = useScenarioGenerationStore()

const transformation = ref((store.data.transformation_type || []).join(','))
function onTransformationChange() {
  store.data.transformation_type = transformation.value.split(',').map(s => s.trim()).filter(Boolean)
}
</script>
