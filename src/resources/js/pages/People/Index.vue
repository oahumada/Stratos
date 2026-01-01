<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import FormSchema from '../form-template/FormSchema.vue';

// Import JSON configs
import configJson from './people-form/config.json';
import tableConfigJson from './people-form/tableConfig.json';
import itemFormJson from './people-form/itemForm.json';
import filtersJson from './people-form/filters.json';

defineOptions({ layout: AppLayout });

interface FormField {
  key: string;
  label: string;
  type: 'text' | 'email' | 'number' | 'password' | 'select' | 'checkbox' | 'textarea' | 'date' | 'time' | 'switch';
  rules?: ((v: any) => boolean | string)[];
  placeholder?: string;
  items?: any[];
}

interface TableHeader {
  text: string;
  value: string;
  type?: 'date' | 'text' | 'number';
  sortable?: boolean;
  filterable?: boolean;
}

interface Config {
  endpoints: {
    index: string;
    apiUrl: string;
  };
  titulo: string;
  descripcion?: string;
  permisos?: {
    crear: boolean;
    editar: boolean;
    eliminar: boolean;
  };
}

interface TableConfig {
  headers: TableHeader[];
  options?: Record<string, any>;
}

interface ItemForm {
  fields: FormField[];
  catalogs?: string[];
  layout?: string;
}

interface FilterConfig {
  field: string;
  type: 'text' | 'select' | 'date';
  label: string;
  items?: any[];
  placeholder?: string;
}

// Load configs from JSON files
const config: Config = configJson as Config;
const tableConfig: TableConfig = tableConfigJson as TableConfig;
const itemForm: ItemForm = itemFormJson as ItemForm;
const filters: FilterConfig[] = filtersJson as FilterConfig[];
</script>

<template>
  <FormSchema
    :config="config"
    :table-config="tableConfig"
    :item-form="itemForm"
    :filters="filters"
  />
</template>

<style scoped>
/* Custom styles */
</style>
