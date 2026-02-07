<template>
  <div>
    <v-text-field v-model="local.company_name" label="Nombre de la organización" />
    <v-text-field v-model="local.industry" label="Industria" />
    <v-text-field v-model="local.sub_industry" label="Sub-industria" />
    <v-text-field v-model="local.company_size" type="number" label="Tamaño (personas)" />
    <div class="auto-generate">
      <v-checkbox v-model="local.auto_generate" label="Generación automática (IA)" />
      <p class="legend">Activar para que la IA genere un escenario sugerido con los datos ingresados. Podrás revisar y editar el resultado antes de aceptarlo.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue';
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore';

const store = useScenarioGenerationStore();

const local = reactive({
  company_name: store.data.company_name || '',
  industry: store.data.industry || '',
  sub_industry: store.data.sub_industry || '',
  company_size: store.data.company_size || null,
  auto_generate: store.data.auto_generate || false,
});

watch(local, (nv) => {
  Object.keys(nv).forEach((k) => store.setField(k, (nv as any)[k]));
}, { deep: true });
</script>

<style scoped>
.auto-generate { margin-top: 0.75rem }
.legend { font-size: 0.9rem; color: #546; margin: 0.25rem 0 0 }
</style>
