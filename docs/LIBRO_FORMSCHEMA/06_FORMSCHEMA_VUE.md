# Capítulo 6: FormSchema.vue - Renderizador Dinámico

**Duración de lectura:** 25 minutos  
**Nivel:** Intermedio  
**Conceptos clave:** Composition API, Renderizado dinámico, Component composition

---

## Introducción: El Lienzo Inteligente

Mientras que `FormSchemaController` es dinámico en **backend**, `FormSchema.vue` es dinámico en **frontend**.

Un único componente Vue renderiza **tablas y formularios** para **cualquier modelo**.

```vue
<!-- Mismo componente, resultados diferentes -->

<!-- Renderiza tabla de Personas -->
<FormSchema 
  :config="personConfig"
  @save="savePerson"
/>

<!-- Renderiza tabla de Certificaciones -->
<FormSchema 
  :config="certificationConfig"
  @save="saveCertification"
/>
```

---

## 1. Estructura Completa

### FormSchema.vue - Arquitectura

```vue
<template>
  <div class="form-schema-container">
    
    <!-- SECCIÓN 1: Encabezado y búsqueda -->
    <div class="header-section">
      <h1>{{ config.title }}</h1>
      <div class="toolbar">
        <v-btn 
          color="primary" 
          @click="openCreateDialog"
        >
          New {{ config.singularName }}
        </v-btn>
      </div>
    </div>
    
    <!-- SECCIÓN 2: Búsqueda y filtros -->
    <div class="search-section">
      <FormData
        v-model="searchQuery"
        :schema="config.filters"
        @search="performSearch"
      />
    </div>
    
    <!-- SECCIÓN 3: Tabla de datos -->
    <div class="table-section">
      <v-data-table
        :headers="tableHeaders"
        :items="items"
        :loading="loading"
        :pagination="pagination"
        @update:page="page = $event"
        class="elevation-1"
      >
        <!-- Column templates dinámicos -->
        <template 
          v-for="col in config.tableConfig.columns"
          :key="col.key"
          #[`item.${col.key}`]="{ item }"
        >
          <!-- Renderizar según tipo de columna -->
          <span v-if="col.type === 'text'">{{ item[col.key] }}</span>
          <span v-else-if="col.type === 'date'">
            {{ formatDate(item[col.key]) }}
          </span>
          <v-chip 
            v-else-if="col.type === 'status'"
            :color="getStatusColor(item[col.key])"
          >
            {{ item[col.key] }}
          </v-chip>
          <!-- ... más tipos ... -->
        </template>
        
        <!-- Acciones (editar, eliminar) -->
        <template #item.actions="{ item }">
          <v-btn 
            icon="mdi-pencil" 
            size="small"
            @click="editItem(item)"
          />
          <v-btn 
            icon="mdi-delete" 
            size="small"
            color="red"
            @click="deleteItem(item)"
          />
        </template>
      </v-data-table>
    </div>
    
    <!-- SECCIÓN 4: Dialog para crear/editar -->
    <v-dialog 
      v-model="showDialog" 
      max-width="600px"
      persistent
    >
      <v-card>
        <v-card-title>
          {{ editingItem?.id ? 'Edit' : 'Create' }} {{ config.singularName }}
        </v-card-title>
        
        <v-card-text>
          <!-- Formulario dinámico -->
          <FormData
            :key="editingItem?.id"
            :initial-data="editingItem"
            :schema="config.itemForm"
            @submit="saveItem"
            @cancel="showDialog = false"
          />
        </v-card-text>
      </v-card>
    </v-dialog>
    
    <!-- Notificaciones -->
    <Notifications />
    
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { notify } from '@kyvg/vue3-notification';
import axios from 'axios';
import FormData from '@/components/FormData.vue';
import Notifications from '@/components/Notifications.vue';

/**
 * PROPS: Configuración del modelo
 * 
 * config: {
 *   apiEndpoint: '/api/person',
 *   title: 'Personas',
 *   singularName: 'Persona',
 *   filters: { ... },
 *   tableConfig: { columns: [...] },
 *   itemForm: { fields: [...] }
 * }
 */
interface Props {
  config: {
    apiEndpoint: string;
    title: string;
    singularName: string;
    pluralName: string;
    filters: object;
    tableConfig: {
      columns: Array<{
        key: string;
        label: string;
        type: string;
        width?: string;
      }>;
    };
    itemForm: {
      fields: Array<any>;
    };
  };
}

const props = defineProps<Props>();

// ==================== ESTADO ====================

// Datos de tabla
const items = ref<any[]>([]);
const loading = ref(false);
const page = ref(1);
const pageSize = ref(15);

// Estado de búsqueda
const searchQuery = ref('');
const filters = ref({});

// Estado de diálogo
const showDialog = ref(false);
const editingItem = ref<any | null>(null);

// Paginación
const pagination = computed(() => ({
  page: page.value,
  pageSize: pageSize.value,
}));

// ==================== COMPUTED ====================

/**
 * Generar headers de tabla dinámicamente
 * desde config.tableConfig
 */
const tableHeaders = computed(() => {
  return [
    ...props.config.tableConfig.columns.map(col => ({
      title: col.label,
      key: col.key,
      width: col.width || '100px',
    })),
    {
      title: 'Acciones',
      key: 'actions',
      sortable: false,
      width: '100px',
    },
  ];
});

/**
 * Endpoint dinámico según config
 */
const apiEndpoint = computed(() => props.config.apiEndpoint);

// ==================== MÉTODOS ====================

/**
 * Cargar datos de tabla
 */
async function loadItems() {
  try {
    loading.value = true;
    
    const response = await axios.get(apiEndpoint.value, {
      params: {
        page: page.value,
        per_page: pageSize.value,
      },
    });
    
    items.value = response.data.data;
    
  } catch (error) {
    notify({
      group: 'default',
      title: 'Error',
      text: 'Failed to load items',
      type: 'error',
    });
  } finally {
    loading.value = false;
  }
}

/**
 * Realizar búsqueda avanzada
 */
async function performSearch() {
  try {
    loading.value = true;
    page.value = 1; // Reset a página 1
    
    const response = await axios.post(
      `${apiEndpoint.value}/search`,
      {
        query: searchQuery.value,
        filters: filters.value,
        page: page.value,
      }
    );
    
    items.value = response.data.data;
    
  } catch (error) {
    notify({
      group: 'default',
      title: 'Error',
      text: 'Search failed',
      type: 'error',
    });
  } finally {
    loading.value = false;
  }
}

/**
 * Abrir diálogo para crear
 */
function openCreateDialog() {
  editingItem.value = null;
  showDialog.value = true;
}

/**
 * Abrir diálogo para editar
 */
function editItem(item: any) {
  editingItem.value = { ...item };
  showDialog.value = true;
}

/**
 * Guardar (crear o actualizar)
 */
async function saveItem(data: any) {
  try {
    loading.value = true;
    
    if (editingItem.value?.id) {
      // UPDATE
      await axios.put(
        `${apiEndpoint.value}/${editingItem.value.id}`,
        data
      );
      
      notify({
        group: 'default',
        title: 'Success',
        text: `${props.config.singularName} updated`,
        type: 'success',
      });
      
    } else {
      // CREATE
      await axios.post(apiEndpoint.value, data);
      
      notify({
        group: 'default',
        title: 'Success',
        text: `${props.config.singularName} created`,
        type: 'success',
      });
    }
    
    showDialog.value = false;
    editingItem.value = null;
    await loadItems();
    
  } catch (error: any) {
    notify({
      group: 'default',
      title: 'Error',
      text: error.response?.data?.message || 'Save failed',
      type: 'error',
    });
  } finally {
    loading.value = false;
  }
}

/**
 * Eliminar registro
 */
async function deleteItem(item: any) {
  if (!confirm(`Delete ${item.name}?`)) return;
  
  try {
    loading.value = true;
    
    await axios.delete(`${apiEndpoint.value}/${item.id}`);
    
    notify({
      group: 'default',
      title: 'Success',
      text: `${props.config.singularName} deleted`,
      type: 'success',
    });
    
    await loadItems();
    
  } catch (error) {
    notify({
      group: 'default',
      title: 'Error',
      text: 'Delete failed',
      type: 'error',
    });
  } finally {
    loading.value = false;
  }
}

// ==================== UTILIDADES ====================

/**
 * Formatear fecha para display
 */
function formatDate(dateString: string): string {
  if (!dateString) return '-';
  return new Date(dateString).toLocaleDateString('es-ES');
}

/**
 * Color de chip según status
 */
function getStatusColor(status: string): string {
  return {
    'active': 'green',
    'inactive': 'grey',
    'pending': 'orange',
    'completed': 'blue',
  }[status] || 'grey';
}

// ==================== LIFECYCLE ====================

/**
 * Cargar datos al montar componente
 */
onMounted(() => {
  loadItems();
});

</script>

<style scoped lang="scss">
.form-schema-container {
  padding: 24px;
  
  .header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    
    h1 {
      font-size: 28px;
      font-weight: 500;
    }
  }
  
  .search-section {
    margin-bottom: 24px;
    padding: 16px;
    background-color: #f5f5f5;
    border-radius: 4px;
  }
  
  .table-section {
    margin-bottom: 24px;
  }
}
</style>
```

---

## 2. Subcomponente: FormData.vue

### Renderizador de Campos

```vue
<template>
  <v-form @submit.prevent="submitForm" class="form-data">
    
    <!-- Loop dinámico sobre campos -->
    <div 
      v-for="field in schema.fields"
      :key="field.key"
      class="form-field"
    >
      
      <!-- TEXT FIELD -->
      <v-text-field
        v-if="field.type === 'text'"
        v-model="formData[field.key]"
        :label="field.label"
        :hint="field.hint"
        :rules="field.rules || []"
        :required="field.required"
      />
      
      <!-- EMAIL FIELD -->
      <v-text-field
        v-else-if="field.type === 'email'"
        v-model="formData[field.key]"
        :label="field.label"
        type="email"
        :rules="field.rules || []"
      />
      
      <!-- NUMBER FIELD -->
      <v-text-field
        v-else-if="field.type === 'number'"
        v-model.number="formData[field.key]"
        :label="field.label"
        type="number"
      />
      
      <!-- TEXTAREA -->
      <v-textarea
        v-else-if="field.type === 'textarea'"
        v-model="formData[field.key]"
        :label="field.label"
        rows="4"
      />
      
      <!-- DATE FIELD -->
      <v-date-field
        v-else-if="field.type === 'date'"
        v-model="formData[field.key]"
        :label="field.label"
      />
      
      <!-- SELECT (Dropdown) -->
      <v-select
        v-else-if="field.type === 'select'"
        v-model="formData[field.key]"
        :items="getSelectOptions(field.key)"
        :label="field.label"
        :multiple="field.multiple || false"
      />
      
      <!-- MULTISELECT (Para relaciones many-to-many) -->
      <v-select
        v-else-if="field.type === 'multiselect'"
        v-model="formData[field.key]"
        :items="getSelectOptions(field.key)"
        :label="field.label"
        multiple
        chips
      />
      
      <!-- CHECKBOX -->
      <v-checkbox
        v-else-if="field.type === 'checkbox'"
        v-model="formData[field.key]"
        :label="field.label"
      />
      
      <!-- RADIO GROUP -->
      <v-radio-group
        v-else-if="field.type === 'radio'"
        v-model="formData[field.key]"
        :label="field.label"
      >
        <v-radio
          v-for="option in field.options"
          :key="option.value"
          :value="option.value"
          :label="option.label"
        />
      </v-radio-group>
      
    </div>
    
    <!-- Botones de acción -->
    <div class="form-actions">
      <v-btn type="submit" color="primary">
        Save
      </v-btn>
      <v-btn @click="$emit('cancel')" text>
        Cancel
      </v-btn>
    </div>
    
  </v-form>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import axios from 'axios';

interface Props {
  schema: {
    fields: Array<any>;
  };
  initialData?: Record<string, any>;
}

const props = withDefaults(defineProps<Props>(), {
  initialData: () => ({}),
});

const emit = defineEmits<{
  submit: [data: Record<string, any>];
  cancel: [];
}>();

// Datos del formulario (reactivo)
const formData = ref<Record<string, any>>({});

// Opciones en caché para selects
const selectOptions = ref<Record<string, any[]>>({});

/**
 * Inicializar formData con initialData
 */
watch(
  () => props.initialData,
  (newData) => {
    if (newData) {
      formData.value = { ...newData };
    }
  },
  { deep: true, immediate: true }
);

/**
 * Obtener opciones para dropdown
 * Soporta:
 *   1. field.options (array hardcoded)
 *   2. field.apiEndpoint (cargar desde API)
 *   3. field.catalog (referencia a catálogo)
 */
async function getSelectOptions(fieldKey: string): Promise<any[]> {
  const field = props.schema.fields.find(f => f.key === fieldKey);
  if (!field) return [];
  
  // Si ya está en caché, retornar
  if (selectOptions.value[fieldKey]) {
    return selectOptions.value[fieldKey];
  }
  
  // Si tiene opciones directas
  if (field.options) {
    selectOptions.value[fieldKey] = field.options;
    return field.options;
  }
  
  // Si tiene endpoint
  if (field.apiEndpoint) {
    try {
      const response = await axios.get(field.apiEndpoint);
      const options = response.data.data || response.data;
      selectOptions.value[fieldKey] = options;
      return options;
    } catch (error) {
      console.error(`Failed to load options for ${fieldKey}`, error);
      return [];
    }
  }
  
  return [];
}

/**
 * Submit del formulario
 */
function submitForm() {
  emit('submit', formData.value);
}

</script>

<style scoped lang="scss">
.form-data {
  .form-field {
    margin-bottom: 16px;
  }
  
  .form-actions {
    display: flex;
    gap: 8px;
    margin-top: 24px;
  }
}
</style>
```

---

## 3. Integración en Página Index

### Uso en Person/Index.vue

```vue
<template>
  <AppLayout title="Personas">
    <FormSchema :config="config" />
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import FormSchema from '@/components/FormSchema.vue';

// Importar configuración específica del modelo
import configJson from './person-form/config.json';

// Ref reactiva con la configuración
const config = reactive({
  ...configJson,
  apiEndpoint: '/api/person', // Override endpoint
});

</script>
```

---

## 4. Archivo de Configuración: config.json

### Estructura

```json
{
  "apiEndpoint": "/api/person",
  "title": "Personas",
  "singularName": "Persona",
  "pluralName": "Personas",
  
  "tableConfig": {
    "columns": [
      {
        "key": "id",
        "label": "ID",
        "type": "number",
        "width": "80px"
      },
      {
        "key": "name",
        "label": "Nombre",
        "type": "text",
        "width": "200px"
      },
      {
        "key": "email",
        "label": "Email",
        "type": "email",
        "width": "250px"
      },
      {
        "key": "status",
        "label": "Estado",
        "type": "status",
        "width": "120px"
      },
      {
        "key": "created_at",
        "label": "Creado",
        "type": "date",
        "width": "150px"
      }
    ]
  },
  
  "itemForm": {
    "fields": [
      {
        "key": "name",
        "label": "Nombre Completo",
        "type": "text",
        "required": true,
        "rules": ["required"]
      },
      {
        "key": "email",
        "label": "Email",
        "type": "email",
        "required": true,
        "rules": ["required", "email"]
      },
      {
        "key": "department_id",
        "label": "Departamento",
        "type": "select",
        "apiEndpoint": "/api/departments",
        "required": true
      },
      {
        "key": "skills",
        "label": "Habilidades",
        "type": "multiselect",
        "apiEndpoint": "/api/skills"
      }
    ]
  },
  
  "filters": {
    "fields": [
      {
        "key": "search",
        "label": "Buscar...",
        "type": "text",
        "placeholder": "Nombre, email, etc."
      },
      {
        "key": "status",
        "label": "Estado",
        "type": "select",
        "options": [
          { "value": "active", "label": "Activo" },
          { "value": "inactive", "label": "Inactivo" }
        ]
      }
    ]
  }
}
```

---

## 5. Ventajas del Renderizado Dinámico

| Beneficio | Descripción |
|-----------|-------------|
| **Reutilización** | Un componente para múltiples modelos |
| **Mantenimiento** | Cambio en tabla/formulario = un lugar |
| **Configuración** | Cambios sin tocar Vue code |
| **Tipo-seguro** | Configuración JSON validable |
| **Rendimiento** | Componentes compilados en tiempo de build |

---

## Conclusión: Componentes como Lienzos

`FormSchema.vue` no es un componente más. Es una **plataforma** que transforma JSON en interfaces de usuario completas.

---

**Próximo capítulo:** [07_JSON_DRIVEN_CONFIG.md](07_JSON_DRIVEN_CONFIG.md)

¿Por qué JSON en lugar de código Vue?
