<template>
  <div>
    <h3>Recursos y Restricciones</h3>
    <label>Nivel de presupuesto
      <input v-model="store.data.budget_level" />
    </label>
    <label>Disponibilidad de talento
      <input v-model="store.data.talent_availability" />
    </label>
    <label>Capacidad de capacitación
      <input v-model="store.data.training_capacity" />
    </label>
    <label>Madurez tecnológica
      <input v-model="store.data.technology_maturity" />
    </label>
    <label>Restricciones críticas
      <textarea v-model="store.data.critical_constraints"></textarea>
    </label>
  </div>
</template>

<script setup lang="ts">
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore'
const store = useScenarioGenerationStore()
</script>
