<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import FormSchema from '../form-template/FormSchema.vue';
import SkillLevelChip from '@/components/SkillLevelChip.vue';

// Import JSON configs
import configJson from './roles-form/config.json';
import tableConfigJson from './roles-form/tableConfig.json';
import itemFormJson from './roles-form/itemForm.json';
import filtersJson from './roles-form/filters.json';

defineOptions({ layout: AppLayout });

// Detail tab state
const detailTab = ref('info');

// Skill levels for chips
const skillLevels = ref<any[]>([]);

onMounted(async () => {
  try {
    const response = await axios.get('/api/catalogs', {
      params: { catalogs: ['skill_levels'] }
    });
    skillLevels.value = response.data.skill_levels || [];
  } catch (error) {
    console.error('Error loading skill levels:', error);
  }
});

// Helpers to map relations safely
const getRoleSkills = (item: any) => {
  if (!item?.skills) return [];

  return item.skills.map((skill: any) => ({
    id: skill.id,
    name: skill.name,
    category: skill.category,
    required_level: skill.pivot?.required_level || 0,
    is_critical: skill.pivot?.is_critical || false,
  }));
};

const getRolePeople = (item: any) => {
  return Array.isArray(item?.people) ? item.people : [];
};

const getPersonName = (person: any) => {
  if (!person) return 'Sin nombre';
  const fullName = `${person.first_name || ''} ${person.last_name || ''}`.trim();
  return fullName || person.name || 'Sin nombre';
};

const getPersonDepartment = (person: any) => {
  return person?.department?.name || person?.department_full_name || person?.department_name || null;
};

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
    enable-row-detail
  >
    <template #detail="{ item }">
      <v-tabs v-model="detailTab">
        <v-tab value="info">
          <v-icon start>mdi-information</v-icon>
          Información
        </v-tab>
        <v-tab value="skills">
          <v-icon start>mdi-star-circle</v-icon>
          Skills ({{ item.skills_count || item.skills?.length || 0 }})
        </v-tab>
        <v-tab value="people">
          <v-icon start>mdi-account-group</v-icon>
          Personas ({{ item.People_count || item.people_count || item.people?.length || 0 }})
        </v-tab>
      </v-tabs>

      <v-window v-model="detailTab" class="mt-4">
        <!-- Info Tab -->
        <v-window-item value="info">
          <v-card flat border class="pa-3">
            <div class="text-subtitle-2 mb-3">Información del Rol</div>
            <v-list density="compact">
              <v-list-item>
                <v-list-item-title class="text-body-2">
                  <strong>Nombre:</strong> {{ item.name }}
                </v-list-item-title>
              </v-list-item>
              <v-list-item v-if="item.description">
                <v-list-item-title class="text-body-2">
                  <strong>Descripción:</strong> {{ item.description }}
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-card>
        </v-window-item>

        <!-- Skills Tab -->
        <v-window-item value="skills">
          <v-card flat border class="pa-3">
            <div class="text-subtitle-2 mb-3">Skills requeridas</div>
            <div v-if="getRoleSkills(item).length === 0" class="text-center text-secondary py-4">
              No hay skills asignadas
            </div>
            <v-list v-else density="compact">
              <v-list-item
                v-for="skill in getRoleSkills(item)"
                :key="skill.id"
                class="mb-2"
                border
              >
                <template #prepend>
                  <v-avatar color="primary" size="32">
                    <v-icon size="small">mdi-star-circle</v-icon>
                  </v-avatar>
                </template>
                <v-list-item-title class="text-body-2 font-weight-medium">
                  {{ skill.name }}
                </v-list-item-title>
                <v-list-item-subtitle class="text-caption">
                  Nivel requerido:
                  <SkillLevelChip
                    :level="skill.required_level"
                    :skill-levels="skillLevels"
                    color="black"
                    class="ml-1"
                    size="small"
                  />
                  <v-chip
                    v-if="skill.is_critical"
                    size="small"
                    color="error"
                    class="ml-2"
                  >
                    Crítica
                  </v-chip>
                </v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card>
        </v-window-item>

        <!-- People Tab -->
        <v-window-item value="people">
          <v-card flat border class="pa-3">
            <div class="text-subtitle-2 mb-3">Personas en este rol</div>
            <div v-if="getRolePeople(item).length === 0" class="text-center text-secondary py-4">
              No hay personas asignadas
            </div>
            <v-list v-else density="compact">
              <v-list-item
                v-for="person in getRolePeople(item)"
                :key="person.id"
                class="mb-2"
                border
              >
                <template #prepend>
                  <v-avatar color="secondary" size="32">
                    <v-icon size="small">mdi-account</v-icon>
                  </v-avatar>
                </template>
                <v-list-item-title class="text-body-2 font-weight-medium">
                  {{ getPersonName(person) }}
                </v-list-item-title>
                <v-list-item-subtitle class="text-caption">
                  {{ person.email ? `(${person.email})` : '(Sin correo)' }}
                  <span v-if="getPersonDepartment(person)"> · {{ getPersonDepartment(person) }}</span>
                </v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card>
        </v-window-item>
      </v-window>
    </template>
  </FormSchema>
</template>

<style scoped>
/* Custom styles */
</style>
