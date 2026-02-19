<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import FormSchema from '../../form-template/FormSchema.vue';

// Import JSON configs
import configJson from './config.json';
import filtersJson from './filters.json';
import itemFormJson from './itemForm.json';
import tableConfigJson from './tableConfig.json';

defineOptions({ layout: AppLayout });

const config = configJson;
const tableConfig = tableConfigJson;
const itemForm = itemFormJson;
const filters = filtersJson;
</script>

<template>
    <v-card class="mb-6" variant="elevated">
        <v-card-title class="text-h6 text-primary">
            <v-icon start>mdi-chart-line-variant</v-icon>
            Scales & BARS Definitions
        </v-card-title>
        <v-card-text>
            Define los niveles de competencia (1-5) con descripciones
            conductuales específicas (Behaviorally Anchored Rating Scales).
        </v-card-text>
    </v-card>

    <FormSchema
        :config="config"
        :table-config="tableConfig"
        :item-form="itemForm"
        :filters="filters"
    />
</template>
