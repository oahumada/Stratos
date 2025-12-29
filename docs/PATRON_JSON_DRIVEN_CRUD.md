# 🎯 Patrón JSON-Driven CRUD para FormSchema

**Fecha**: 28 Diciembre 2025  
**Estado**: ✅ Implementado y Documentado  
**Aplicable a**: Todos los módulos CRUD con búsqueda y filtrado

---

## 📋 Resumen Ejecutivo

Este patrón permite crear formularios CRUD completos (Create, Read, Update, Delete) con búsqueda y filtrado usando:

- **Componente reutilizable único** (`FormSchema.vue`)
- **Archivos JSON de configuración** (sin código Vue)
- **Un archivo Index.vue mínimo** que importa JSONs

**Beneficio**: Agregar nuevo módulo CRUD en **15 minutos** sin duplicar código Vue.

---

## 🏗️ Estructura del Patrón

```
/resources/js/pages/[Module]/
├── Index.vue                    ← Punto de entrada mínimo
└── [module]-form/
    ├── config.json             ← Endpoints y permisos
    ├── tableConfig.json        ← Estructura de tabla
    ├── itemForm.json           ← Campos del formulario
    └── filters.json            ← Filtros de búsqueda
```

### Ejemplo: Person Module

```
/resources/js/pages/Person/
├── Index.vue (121 líneas)
└── Person-form/
    ├── config.json
    ├── tableConfig.json
    ├── itemForm.json
    └── filters.json
```

---

## 📄 Definición de Archivos JSON

### 1. config.json - Configuración Global

```json
{
  "endpoints": {
    "index": "/api/Person",
    "apiUrl": "/api/Person"
  },
  "titulo": "Person Management",
  "descripcion": "Manage employees and their skills",
  "permisos": {
    "crear": true,
    "editar": true,
    "eliminar": true
  }
}
```

**Propiedades:**

- `endpoints.index`: Endpoint GET para listar registros
- `endpoints.apiUrl`: Endpoint base para CRUD (create, update, delete)
- `titulo`: Encabezado principal de la página
- `descripcion`: Subtítulo descriptivo
- `permisos`: Booleanos para mostrar/ocultar botones de acciones

---

### 2. tableConfig.json - Estructura de la Tabla

```json
{
  "headers": [
    {
      "text": "Name",
      "value": "name",
      "sortable": true
    },
    {
      "text": "Email",
      "value": "email",
      "sortable": true
    },
    {
      "text": "Department",
      "value": "department",
      "sortable": true
    },
    {
      "text": "Role",
      "value": "role",
      "sortable": false
    },
    {
      "text": "Hired",
      "value": "hired_at",
      "type": "date",
      "sortable": true
    },
    {
      "text": "Actions",
      "value": "actions",
      "sortable": false
    }
  ],
  "options": {
    "dense": false,
    "itemsPerPage": 10,
    "showSelect": false
  }
}
```

**Propiedades por header:**

- `text`: Etiqueta visible en la columna
- `value`: Nombre del campo en el objeto datos
- `type`: Opcional - `date`, `number`, etc. (FormSchema renderiza según tipo)
- `sortable`: Si permite ordenar por esta columna
- `options`: Configuración global de la tabla (Vuetify v-data-table)

---

### 3. itemForm.json - Campos del Formulario

```json
{
  "fields": [
    {
      "key": "name",
      "label": "Full Name",
      "type": "text",
      "placeholder": "Enter full name",
      "rules": ["required", "min:3"]
    },
    {
      "key": "email",
      "label": "Email",
      "type": "email",
      "placeholder": "Enter email address",
      "rules": ["required", "email"]
    },
    {
      "key": "department",
      "label": "Department",
      "type": "text",
      "placeholder": "Enter department",
      "rules": ["required"]
    },
    {
      "key": "role_id",
      "label": "Role",
      "type": "select",
      "placeholder": "Select a role",
      "rules": []
    },
    {
      "key": "hired_at",
      "label": "Hired Date",
      "type": "date",
      "rules": []
    }
  ],
  "catalogs": ["role"]
}
```

**Propiedades por field:**

- `key`: Nombre del campo en el modelo
- `label`: Etiqueta en el formulario
- `type`: Tipo de control - `text`, `email`, `number`, `password`, `textarea`, `select`, `date`, `time`, `checkbox`, `switch`
- `placeholder`: Texto de ayuda en el input
- `rules`: Validaciones aplicadas (ver validación en Index.vue)
- `catalogs`: Array de catálogos a cargar (ej: `["role", "department"]`)

---

### 4. filters.json - Filtros de Búsqueda

```json
[
  {
    "field": "department",
    "type": "select",
    "label": "Department",
    "placeholder": "Select department"
  },
  {
    "field": "role_id",
    "type": "select",
    "label": "Role",
    "placeholder": "Select role"
  }
]
```

**Propiedades:**

- `field`: Campo del modelo a filtrar
- `type`: `text` (búsqueda libre), `select` (dropdown), `date` (date picker)
- `label`: Etiqueta del filtro
- `placeholder`: Texto en el control
- `items`: Se populan dinámicamente desde Index.vue (para selects)

---

## 📝 Index.vue - Punto de Entrada

Estructura mínima que importa JSONs:

```typescript
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import FormSchema from '../form-template/FormSchema.vue';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

// Import JSON configs
import configJson from './Person-form/config.json';
import tableConfigJson from './Person-form/tableConfig.json';
import itemFormJson from './Person-form/itemForm.json';
import filtersJson from './Person-form/filters.json';

defineOptions({ layout: AppLayout });

// Interfaces TypeScript (copiadas de FormSchema.vue)
interface FormField { /* ... */ }
interface TableHeader { /* ... */ }
interface Config { /* ... */ }
interface TableConfig { /* ... */ }
interface ItemForm { /* ... */ }
interface FilterConfig { /* ... */ }

// State
const roles = ref<Role[]>([]);

// Load JSON configs
const config: Config = configJson as Config;
const tableConfig: TableConfig = tableConfigJson as TableConfig;
const itemForm: ItemForm = itemFormJson as ItemForm;
const filtersBase: FilterConfig[] = filtersJson as FilterConfig[];

// Populate dynamic items en filters (ej: cargar roles para role_id filter)
const filters = computed<FilterConfig[]>(() => {
  return filtersBase.map(filter => {
    if (filter.field === 'role_id') {
      return {
        ...filter,
        items: roles.value.map(r => ({ id: r.id, name: r.name })),
      };
    }
    return filter;
  });
});

// API calls needed for dynamic data
const loadRoles = async () => {
  try {
    const response = await axios.get('/api/roles');
    roles.value = response.data.data || response.data;
  } catch (err) {
    console.error('Failed to load roles', err);
  }
};

onMounted(() => {
  loadRoles();
});
</script>

<template>
  <FormSchema
    :config="config"
    :table-config="tableConfig"
    :item-form="itemForm"
    :filters="filters"
  />
</template>
```

**Pasos:**

1. Importar 4 JSONs
2. Definir interfaces TypeScript
3. Cargar catálogos dinámicos (roles, departamentos, etc.)
4. Pasar props a FormSchema.vue

---

## 🚀 Cómo Agregar Nuevo Módulo CRUD

### Paso 1: Crear Estructura (1 min)

```bash
mkdir -p src/resources/js/pages/[Module]/[module]-form
touch src/resources/js/pages/[Module]/Index.vue
touch src/resources/js/pages/[Module]/[module]-form/{config,tableConfig,itemForm,filters}.json
```

### Paso 2: Definir Configs (3 min)

Copiar template de `Person-form/` y adaptar:

**config.json**

```json
{
  "endpoints": {
    "index": "/api/[plural-module]",
    "apiUrl": "/api/[plural-module]"
  },
  "titulo": "[Module] Management",
  "descripcion": "Manage [modules]",
  "permisos": { "crear": true, "editar": true, "eliminar": true }
}
```

**tableConfig.json** - Listar columnas que mostrar en tabla

**itemForm.json** - Listar campos del formulario

**filters.json** - Definir qué filtros mostrar

### Paso 3: Copiar Index.vue Template (5 min)

Copiar Person/Index.vue y cambiar:

```typescript
import configJson from "./[module]-form/config.json";
import tableConfigJson from "./[module]-form/tableConfig.json";
import itemFormJson from "./[module]-form/itemForm.json";
import filtersJson from "./[module]-form/filters.json";
```

### Paso 4: Cargar Catálogos Dinámicos (2 min)

Si necesitas populate selects:

```typescript
const [catalogs] = ref<Record[]>([]);

const filters = computed<FilterConfig[]>(() => {
  return filtersBase.map(filter => {
    if (filter.field === '[catalog_id]') {
      return {
        ...filter,
        items: [catalogs].value.map(r => ({ id: r.id, name: r.name })),
      };
    }
    return filter;
  });
});

const load[Catalogs] = async () => {
  const response = await axios.get('/api/[catalogs]');
  [catalogs].value = response.data.data || response.data;
};

onMounted(() => load[Catalogs]());
```

### Paso 5: Agregar Ruta (1 min)

En `routes/web.php`:

```php
Route::get('/[plural-module]', function () {
    return Inertia::render('[Module]/Index');
})->middleware('auth:sanctum');
```

### Paso 6: Agregar Navlink (1 min)

En `AppSidebar.vue`:

```vue
<Link href="/[plural-module]" class="nav-link">
  [Module]
</Link>
```

**TOTAL: ~15 minutos** sin escribir código Vue complejo.

---

## 🔄 FormSchema.vue - El Componente Central

Responsabilidades:

- 📊 Renderizar tabla con datos
- 🔍 Búsqueda por texto libre
- 🎯 Filtrado por criterios
- ➕ Dialog crear nuevo
- ✏️ Dialog editar
- 🗑️ Confirmación eliminar
- 💾 Llamadas CRUD a API

**Props esperados:**

```typescript
interface Props {
  config: Config; // Endpoints, permisos, títulos
  tableConfig: TableConfig; // Estructura de columnas
  itemForm: ItemForm; // Campos del formulario
  filters: FilterConfig[]; // Filtros disponibles
}
```

**Ubicación**: `/resources/js/pages/form-template/FormSchema.vue`

---

## 🎨 Tipos de Campos Soportados

FormData.vue renderiza automáticamente según `type`:

| Type       | Control                      | Ejemplo                          |
| ---------- | ---------------------------- | -------------------------------- |
| `text`     | v-text-field                 | Nombres, textos cortos           |
| `email`    | v-text-field (type=email)    | Correos                          |
| `number`   | v-text-field (type=number)   | IDs, cantidades                  |
| `password` | v-text-field (type=password) | Contraseñas                      |
| `textarea` | v-textarea                   | Descripciones largas             |
| `select`   | v-select                     | Dropdowns (roles, departamentos) |
| `date`     | v-text-field (type=date)     | Fechas                           |
| `time`     | v-text-field (type=time)     | Horas                            |
| `checkbox` | v-checkbox                   | Booleanos                        |
| `switch`   | v-switch                     | Toggles                          |

---

## ✅ Checklist para Nuevo Módulo

- [ ] JSONs creados y válidos (sin syntax errors)
- [ ] Index.vue importa 4 JSONs
- [ ] Endpoint `/api/[plural-module]` funciona (GET)
- [ ] Endpoint `/api/[plural-module]` soporta POST (create)
- [ ] Endpoint `/api/[plural-module]/{id}` soporta PUT (update)
- [ ] Endpoint `/api/[plural-module]/{id}` soporta DELETE
- [ ] Ruta `/[plural-module]` agregada en web.php
- [ ] NavLink agregado en AppSidebar.vue
- [ ] Catálogos cargados dinámicamente en onMounted
- [ ] Filtros poblados correctamente en computed

---

## 🛡️ Ventajas de Este Patrón

1. **DRY (Don't Repeat Yourself)**: Un solo FormSchema.vue reutilizable
2. **Config-Driven**: Cambiar comportamiento sin recompilar código
3. **Type-Safe**: TypeScript interfaces en Index.vue
4. **Escalable**: Agregar módulos en 15 min
5. **Testeable**: JSONs separados facilitan mocking
6. **Mantenible**: Cambios centralizados en FormSchema.vue
7. **Consistente**: Todos los CRUD usan mismo look & feel

---

## 🔧 Próximos Pasos

### Inmediatos (HOY)

- [ ] Aplicar a Roles/Index.vue
- [ ] Aplicar a Skills/Index.vue

### Day 6

- [ ] Aplicar a 5 módulos restantes (GapAnalysis, DevelopmentPaths, etc.)

### Day 7+

- [ ] Custom templates para columnas especiales (chips, badges)
- [ ] Validación personalizada avanzada
- [ ] Exportar a CSV/Excel desde tabla

---

## 📚 Referencias

- **FormSchema.vue**: `/resources/js/pages/form-template/FormSchema.vue`
- **FormData.vue**: `/resources/js/pages/form-template/FormData.vue`
- **Ejemplo implementado**: `/resources/js/pages/Person/`
- **API spec**: `/docs/dia5_api_endpoints.md`
