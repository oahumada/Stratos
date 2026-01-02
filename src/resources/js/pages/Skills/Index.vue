<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import FormSchema from '../form-template/FormSchema.vue';
import SkillLevelChip from '@/components/SkillLevelChip.vue';
import axios from 'axios';

// Import JSON configs
import configJson from './skills-form/config.json';
import tableConfigJson from './skills-form/tableConfig.json';
import itemFormJson from './skills-form/itemForm.json';
import filtersJson from './skills-form/filters.json';

defineOptions({ layout: AppLayout });

// State for detail tabs
const detailTab = ref('info');

// State for skill levels
const skillLevels = ref<any[]>([]);

// Load skill levels on mount
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

// Helper to get level name
const getLevelName = (level: number) => {
  const levelDef = skillLevels.value.find(l => l.level === level);
  return levelDef ? levelDef.name : `Nivel ${level}`;
};

// Helper to get level display label (e.g., "1 - Básico")
const getLevelDisplay = (level: number) => {
  const levelDef = skillLevels.value.find(l => l.level === level);
  return levelDef ? levelDef.display_label : `Nivel ${level}`;
};

// Computed properties para roles y people basados en el item que viene con las relaciones
const getSkillRoles = (item: any) => {
  if (!item.roles) return [];
  
  return item.roles.map((role: any) => ({
    id: role.id,
    role: { name: role.name, level: role.level },
    required_level: role.pivot?.required_level || 0,
    is_critical: role.pivot?.is_critical || false
  }));
};

const getSkillPeople = (item: any) => {
  return item.people_role_skills || [];
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
  >
    <template #detail="{ item }">
      <v-tabs v-model="detailTab">
        <v-tab value="info">
          <v-icon start>mdi-information</v-icon>
          Información
        </v-tab>
        <v-tab value="roles">
          <v-icon start>mdi-account-tie</v-icon>
          Roles ({{ item.roles_count || 0 }})
        </v-tab>
        <v-tab value="people">
          <v-icon start>mdi-account-group</v-icon>
          Personas ({{ item.people_count || 0 }})
        </v-tab>
      </v-tabs>

      <v-window v-model="detailTab" class="mt-4">
        <!-- Info Tab -->
        <v-window-item value="info">
          <v-card flat border class="pa-3">
            <div class="text-subtitle-2 mb-3">Información de la Skill</div>
            <v-list density="compact">
              <v-list-item>
                <v-list-item-title class="text-body-2">
                  <strong>Nombre:</strong> {{ item.name }}
                </v-list-item-title>
              </v-list-item>
              <v-list-item>
                <v-list-item-title class="text-body-2">
                  <strong>Categoría:</strong> 
                  <v-chip size="small" class="ml-2" :color="
                    item.category === 'technical' ? 'primary' :
                    item.category === 'soft' ? 'success' :
                    'warning'
                  ">
                    {{ item.category }}
                  </v-chip>
                </v-list-item-title>
              </v-list-item>
              <v-list-item v-if="item.description">
                <v-list-item-title class="text-body-2">
                  <strong>Descripción:</strong> {{ item.description }}
                </v-list-item-title>
              </v-list-item>
              <v-list-item>
                <v-list-item-title class="text-body-2">
                  <strong>Crítica:</strong> 
                  <v-chip size="small" class="ml-2" :color="item.is_critical ? 'error' : 'default'">
                    {{ item.is_critical ? 'Sí' : 'No' }}
                  </v-chip>
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-card>
        </v-window-item>

        <!-- Roles Tab -->
        <v-window-item value="roles">
          <v-card flat border class="pa-3">
            <div class="text-subtitle-2 mb-3">Roles que requieren esta skill</div>
            <div v-if="getSkillRoles(item).length === 0" class="text-center text-secondary py-4">
              No hay roles asignados
            </div>
            <v-list v-else density="compact">
              <v-list-item
                v-for="roleSkill in getSkillRoles(item)"
                :key="roleSkill.id"
                class="mb-2"
                border
              >
                <template #prepend>
                  <v-avatar color="primary" size="32">
                    <v-icon size="small">mdi-account-tie</v-icon>
                  </v-avatar>
                </template>
                <v-list-item-title class="text-body-2 font-weight-medium">
                  {{ roleSkill.role?.name || 'N/A' }}
                </v-list-item-title>
                <v-list-item-subtitle class="text-caption">
                  Nivel requerido: 
                  <SkillLevelChip 
                    :level="roleSkill.required_level" 
                    :skill-levels="skillLevels"
                    color="primary"
                    class="ml-1"
                  />
                  <v-chip 
                    v-if="roleSkill.is_critical" 
                    size="x-small" 
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
            <div class="text-subtitle-2 mb-3">Empleados con esta skill</div>
            <div v-if="getSkillPeople(item).length === 0" class="text-center text-secondary py-4">
              No hay empleados con esta skill
            </div>
            <v-list v-else density="compact">
              <v-list-item
                v-for="peopleSkill in getSkillPeople(item)"
                :key="peopleSkill.id"
                class="mb-2"
                border
              >
                <template #prepend>
                  <v-avatar color="success" size="32">
                    <v-icon size="small">mdi-account</v-icon>
                  </v-avatar>
                </template>
                <v-list-item-title class="text-body-2 font-weight-medium">
                  {{ peopleSkill.person ? `${peopleSkill.person.first_name} ${peopleSkill.person.last_name}` : 'N/A' }}
                </v-list-item-title>
                <v-list-item-subtitle class="text-caption">
                  <span class="font-weight-medium">Actual:</span> 
                  <SkillLevelChip 
                    :level="peopleSkill.current_level" 
                    :skill-levels="skillLevels"
                    :color="peopleSkill.current_level >= peopleSkill.required_level ? 'success' : 'warning'"
                    class="ml-1"
                  />
                  <span class="mx-2">•</span>
                  <span class="font-weight-medium">Requerido:</span> 
                  <SkillLevelChip 
                    :level="peopleSkill.required_level" 
                    :skill-levels="skillLevels"
                    color="primary"
                    class="ml-1"
                  />
                  <v-chip 
                    v-if="peopleSkill.current_level >= peopleSkill.required_level" 
                    size="x-small" 
                    color="success" 
                    class="ml-2"
                  >
                    <v-icon start size="x-small">mdi-check</v-icon>
                    Cumple
                  </v-chip>
                  <v-chip 
                    v-else 
                    size="x-small" 
                    color="warning" 
                    class="ml-2"
                  >
                    <v-icon start size="x-small">mdi-alert</v-icon>
                    Gap: {{ peopleSkill.required_level - peopleSkill.current_level }} nivel(es)
                  </v-chip>
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
