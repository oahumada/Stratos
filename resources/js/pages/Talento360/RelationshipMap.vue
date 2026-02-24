<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Config,
    FilterConfig,
    ItemForm,
    TableConfig,
} from '@/types/form-schema';
import { ref } from 'vue';
import FormSchema from '../form-template/FormSchema.vue';
import configJson from './relationship-form/config.json';
import filtersJson from './relationship-form/filters.json';
import itemFormJson from './relationship-form/itemForm.json';
import tableConfigJson from './relationship-form/tableConfig.json';

defineOptions({ layout: AppLayout });

// Load configs from JSON files
const config: Config = configJson as any;
const tableConfig: TableConfig = tableConfigJson as unknown as TableConfig;
const itemForm: ItemForm = itemFormJson as unknown as ItemForm;
const filters: FilterConfig[] = filtersJson as unknown as FilterConfig[];

const formRef = ref(null);
</script>

<template>
    <div class="relationship-map-container">
        <FormSchema
            ref="formRef"
            :config="config"
            :table-config="tableConfig"
            :item-form="itemForm"
            :filters="filters"
        />
    </div>
</template>

<style scoped>
.relationship-map-container {
    padding: 0;
}
</style>
