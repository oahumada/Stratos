<template>
  <div>
    <h3>Horizonte Temporal</h3>
    <label>Plazo
      <input v-model="store.data.time_horizon" />
    </label>
    <label>Urgencia
      <input v-model="store.data.urgency_level" />
    </label>
    <label>Hitos
      <textarea v-model="store.data.milestones"></textarea>
    </label>
    <label>Organization ID (opcional)
      <input v-model.number="store.data.organization_id" />
    </label>
  </div>
</template>

<script setup lang="ts">
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore'
const store = useScenarioGenerationStore()
</script>
