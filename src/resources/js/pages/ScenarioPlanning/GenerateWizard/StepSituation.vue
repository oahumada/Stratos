<template>
  <div>
    <h3>Situación Actual</h3>
    <label>Desafíos
      <textarea v-model="store.data.current_challenges"></textarea>
    </label>
    <label>Capacidades existentes
      <textarea v-model="store.data.current_capabilities"></textarea>
    </label>
    <label>Brechas
      <textarea v-model="store.data.current_gaps"></textarea>
    </label>
    <label>Roles formalizados (cantidad)
      <input type="number" v-model.number="store.data.current_roles_count" />
    </label>
    <label>Modelo de competencias
      <input type="checkbox" v-model="store.data.has_formal_competency_model" />
    </label>
  </div>
</template>

<script setup lang="ts">
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore'
const store = useScenarioGenerationStore()
</script>
