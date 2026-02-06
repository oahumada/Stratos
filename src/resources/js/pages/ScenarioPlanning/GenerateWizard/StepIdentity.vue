<template>
  <div>
    <h3>Identidad Organizacional</h3>
    <label>Nombre
      <input v-model="store.data.company_name" />
    </label>
    <label>Industria
      <input v-model="store.data.industry" />
    </label>
    <label>Sub-industria
      <input v-model="store.data.sub_industry" />
    </label>
    <label>Tamaño
      <input type="number" v-model.number="store.data.company_size" />
    </label>
    <label>Alcance
      <input v-model="store.data.geographic_scope" />
    </label>
    <label>Ciclo organizacional
      <input v-model="store.data.organizational_cycle" />
    </label>
    <div class="auto-generate">
      <label>
        <input type="checkbox" v-model="store.data.auto_generate" /> Generación automática (IA)
      </label>
      <p class="legend">Activar para que la IA genere un escenario sugerido con los datos ingresados. Podrás revisar y editar el resultado antes de aceptarlo.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore'
const store = useScenarioGenerationStore()

</script>

<style scoped>
.auto-generate { margin-top: 0.75rem }
.legend { font-size: 0.9rem; color: #546; margin: 0.25rem 0 0 }
</style>
</script>
<template>
  <div>
    <v-text-field v-model="local.company_name" label="Nombre de la organización" />
    <v-text-field v-model="local.industry" label="Industria" />
    <v-text-field v-model="local.sub_industry" label="Sub-industria" />
    <v-text-field v-model="local.company_size" type="number" label="Tamaño (personas)" />
  </div>
</template>

<script setup>
import { reactive, toRefs, watch } from 'vue';
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore';

const store = useScenarioGenerationStore();
const props = defineProps({});

const local = reactive({
  company_name: store.data.company_name || '',
  industry: store.data.industry || '',
  sub_industry: store.data.sub_industry || '',
  company_size: store.data.company_size || null,
});

watch(local, (nv) => {
  Object.keys(nv).forEach(k => store.setField(k, nv[k]));
}, { deep: true });
</script>
