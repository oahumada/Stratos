<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import FormSchema from '@/components/FormSchema.vue';
import config from '@/components/Alergia/config.json';
import tableConfig from '@/components/Alergia/tableConfig.json';
import itemForm from '@/components/Alergia/itemForm.json';
import { useDataStore } from '@/store.js';

defineOptions({ layout: AppLayout });
const store = useDataStore();

// Manejar el caso cuando pacienteSelected es un array o está vacío
const pacienteId = store.getPacienteSelected.id;

</script>

<template>
  <v-container fluid>
    <v-sheet>
      <FormSchema :paciente-id="pacienteId" :config="config" :table-config="tableConfig" :item-form="itemForm" />
    </v-sheet>
  </v-container>
</template>

<style scoped>
/* Custom styles */
</style>
