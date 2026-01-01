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
  catalogKey?: string;
}

// Load configs from JSON files
const config: Config = configJson as Config;
const tableConfig: TableConfig = tableConfigJson as TableConfig;
const itemForm: ItemForm = itemFormJson as ItemForm;
const filters: FilterConfig[] = filtersJson as FilterConfig[];

const formatDate = (value: any): string => {
  if (!value) return '';
  try {
    const date = new Date(value);
    if (!isNaN(date.getTime())) {
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = date.getFullYear();
      return `${day}/${month}/${year}`;
    }
  } catch (e) {
    return value as string;
  }
  return value as string;
};
</script>

<template>
  <FormSchema
    :config="config"
    :table-config="tableConfig"
    :item-form="itemForm"
    :filters="filters"
    :enable-row-detail="true"
  >
    <template #detail="{ item, tab, setTab, sync, close }">
      <v-card flat border class="pa-3">
        <div class="text-h6 font-weight-bold">{{ item.first_name }} {{ item.last_name }}</div>
        <div class="text-body-2 text-secondary">{{ item.email }}</div>
        <div class="text-body-2">Depto: {{ item.department?.name || item.department_id }}</div>
        <div class="text-body-2">Rol: {{ item.role?.name || item.role_id }}</div>
        <div class="text-body-2">Hired: {{ formatDate(item.hire_date) }}</div>
      </v-card>

      <div class="d-flex align-center justify-space-between mt-4">
        <v-tabs :model-value="tab" density="compact" color="primary" @update:modelValue="setTab">
          <v-tab value="active">Skills Activas</v-tab>
          <v-tab value="history">Historial</v-tab>
        </v-tabs>
        <div class="d-flex align-center gap-2">
          <v-btn size="small" color="primary" variant="tonal" prepend-icon="mdi-sync" @click="sync">
            Sincronizar con rol
          </v-btn>
          <v-btn size="small" variant="text" icon="mdi-close" @click="close" />
        </div>
      </div>

      <v-window :model-value="tab" class="mt-3">
        <v-window-item value="active">
          <v-card flat border class="pa-3" v-if="item.skills?.length">
            <div class="text-subtitle-2 mb-2">Skills activas</div>
            <div class="d-flex flex-wrap gap-2">
              <v-chip
                v-for="skill in item.skills"
                :key="skill.id"
                size="small"
                class="text-capitalize"
                :color="skill.pivot?.current_level >= skill.pivot?.required_level ? 'success' : 'warning'"
                variant="tonal"
              >
                {{ skill.name }} ({{ skill.pivot?.current_level }}/{{ skill.pivot?.required_level }})
              </v-chip>
            </div>
          </v-card>
          <v-alert v-else type="info" density="comfortable" variant="tonal">
            Sin skills activas en el pivote `people_role_skills`.
          </v-alert>
        </v-window-item>

        <v-window-item value="history">
          <v-alert type="info" density="comfortable" variant="tonal">
            Historial no cargado (requiere endpoint de `people_role_skills` inactivos).
          </v-alert>
        </v-window-item>
      </v-window>
    </template>
  </FormSchema>
</template>

<style scoped>
/* Custom styles */
</style>
