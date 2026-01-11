<script setup lang="ts">
import { ref, onMounted } from 'vue';
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

// Helper to get level name (kept for future use)
const _getLevelName = (level: number) => {
  const levelDef = skillLevels.value.find(l => l.level === level);
  return levelDef ? levelDef.name : `Nivel ${level}`;
};

// Helper to get level display label (kept for future use)
const _getLevelDisplay = (level: number) => {
  const levelDef = skillLevels.value.find(l => l.level === level);
  return levelDef ? levelDef.display_label : `Nivel ${level}`;
};

// reference to avoid unused-var during iterative refactor
void _getLevelName
void _getLevelDisplay

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

// Skill levels descriptions
const skillLevelDescriptions = [
  {
    level: 1,
    name: 'Básico',
    description: 'Conocimiento fundamental. Puede realizar tareas simples bajo supervisión. Requiere soporte para decisiones complejas.'
  },
  {
    level: 2,
    name: 'Intermedio',
    description: 'Competencia establecida. Puede trabajar de forma independiente en tareas estándar. Requiere orientación en situaciones nuevas.'
  },
  {
    level: 3,
    name: 'Avanzado',
    description: 'Dominio demostrado. Resuelve problemas complejos de forma autónoma. Puede asesorar a otros en la habilidad.'
  },
  {
    level: 4,
    name: 'Experto',
    description: 'Dominio excepcional. Líder en la habilidad. Desarrolla mejores prácticas y mentoriza equipos.'
  },
  {
    level: 5,
    name: 'Maestría',
    description: 'Expertise excepcional. Referente organizacional. Impulsa innovación y establece estándares de excelencia.'
  }
];
</script>

<template>
  <div class="mb-6">
    <v-card class="mb-6" variant="elevated">
      <v-card-title class="text-h6 text-primary">
        <v-icon start>mdi-lightbulb-outline</v-icon>
        Módulo de Habilidades (Skills)
      </v-card-title>
      <v-card-text>
        <p class="text-body-2 mb-4">
          <strong>Objetivo del Módulo:</strong> Este módulo permite gestionar y catalogar las habilidades (skills) de la organización. 
          Las habilidades pueden ser técnicas o blandas, y se relacionan con los roles disponibles y las personas que las poseen. 
          Cada habilidad tiene un nivel asociado que indica el grado de dominio requerido.
        </p>
        
        <div>
          <p class="text-body-2 font-weight-bold mb-3">Niveles de Habilidades (1-5):</p>
          <v-row dense>
            <v-col v-for="level in skillLevelDescriptions" :key="level.level" cols="12" md="6" lg="4" xl="3">
              <v-card variant="outlined" class="h-100">
                <v-card-title class="text-subtitle-2">
                  <v-chip :color="
                    level.level === 1 ? 'blue' :
                    level.level === 2 ? 'cyan' :
                    level.level === 3 ? 'green' :
                    level.level === 4 ? 'orange' :
                    'red'
                  " text-color="white" size="small" class="mr-2">
                    Nivel {{ level.level }}
                  </v-chip>
                  {{ level.name }}
                </v-card-title>
                <v-card-text class="text-caption">
                  {{ level.description }}
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </div>
      </v-card-text>
    </v-card>
  </div>

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
                <v-list-item-subtitle class="text-body-2 mt-2 d-flex align-center gap-2">
                  <span class="font-weight-medium">Nivel requerido:</span>
                  <SkillLevelChip 
                    :level="roleSkill.required_level" 
                    :skill-levels="skillLevels"
                    color="primary"
                    size="large"
                    
                  />
                  <v-chip 
                    v-if="roleSkill.is_critical" 
                    size="small" 
                    color="error" 
                    text-color="white"
                    class="ml-auto"
                  >
                    <v-icon start size="small">mdi-alert</v-icon>
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
