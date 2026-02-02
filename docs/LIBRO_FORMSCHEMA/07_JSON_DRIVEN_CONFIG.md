# Capítulo 7: JSON-Driven Configuration - Configuración como Primera Clase

**Duración de lectura:** 20 minutos  
**Nivel:** Intermedio-Avanzado  
**Conceptos clave:** Declarativo vs Imperativo, Validación de schema, Extensibilidad

---

## Introducción: Código vs Configuración

FormSchema Pattern separa **lógica de presentación** de **configuración de datos**.

```
Pregunta tradicional: "¿Dónde está la lógica?"
Respuesta: En componentes Vue (.vue files)

Pregunta FormSchema: "¿Dónde está la CONFIGURACIÓN?"
Respuesta: En archivos JSON separados
```

### Comparación

```vue
<!-- IMPERATIVO (Tradicional) -->
<template>
  <v-data-table
    :headers="[
      { title: 'ID', key: 'id' },
      { title: 'Nombre', key: 'name' },
      { title: 'Email', key: 'email' },
      { title: 'Creado', key: 'created_at' },
    ]"
    :items="items"
  />
</v-data-table>

<script setup>
// Cambiar tabla → Editar .vue file
</script>
```

vs

```json
// DECLARATIVO (FormSchema)
{
  "tableConfig": {
    "columns": [
      { "key": "id", "label": "ID" },
      { "key": "name", "label": "Nombre" },
      { "key": "email", "label": "Email" },
      { "key": "created_at", "label": "Creado" }
    ]
  }
}

// Cambiar tabla → Editar config.json
```

---

## 1. Ventajas del Enfoque JSON-Driven

### Ventaja 1: Cambios sin Recompilación

```
IMPERATIVO:
  1. Editar .vue file
  2. npm run build
  3. Deploy
  4. Esperar a que se recompile
  
DECLARATIVO (JSON):
  1. Editar config.json
  2. Browser refresh
  ✅ Instant feedback
```

### Ventaja 2: No-Code Configuration

```
Peopleas sin programación pueden cambiar:
  - Nombres de columnas en tabla
  - Campos en formularios
  - Validaciones
  - Filtros
  
Solo necesitan editar JSON (o interfaz en admin)
```

### Ventaja 3: Replicabilidad

```
Crear nuevo CRUD es:
  1. Copiar folder people-form/ → certifications-form/
  2. Editar JSON files (config.json, tableConfig.json, etc.)
  3. Cambiar 4 imports en Index.vue
  4. Registrar en form-schema-complete.php
  
Sin tocar componentes Vue ✅
```

### Ventaja 4: Versionado y Auditoría

```
config.json es archivo de texto
├─ Versionar en Git
├─ Ver cambios históricos (git log)
├─ Revertir cambios fácilmente
└─ Auditar quién cambió qué

Si fuera hardcoded en .vue:
  "¿Quién cambió el orden de columnas?"
  "¿Cuándo se agregó validación?"
  Imposible saberlo
```

### Ventaja 5: Herencia y Mixins

```json
// base-config.json (Configuración base para todos)
{
  "defaultPageSize": 15,
  "defaultSort": "created_at",
  "defaultOrder": "desc"
}

// people-config.json (Extiende base)
{
  "$extends": "base-config.json",
  "title": "Peopleas",
  "filters": { ... }
}
```

---

## 2. Estructura de Archivos de Configuración

### Ubicación Estándar

```
resources/js/pages/
├── People/
│   ├── Index.vue           (Componente, importa config)
│   ├── Show.vue            (Detalle, importa config)
│   └── people-form/        (Carpeta de configuración)
│       ├── config.json          (Configuración principal)
│       ├── tableConfig.json     (Definición de tabla)
│       ├── itemForm.json        (Definición de formulario)
│       └── filters.json         (Definición de filtros)
│
├── Certification/
│   ├── Index.vue
│   ├── Show.vue
│   └── certifications-form/
│       ├── config.json
│       ├── tableConfig.json
│       ├── itemForm.json
│       └── filters.json
```

### Archivos Específicos

#### config.json - Orquestador Central

```json
{
  "model": "People",
  "apiEndpoint": "/api/people",
  "title": "Peopleas",
  "singularName": "Peoplea",
  "pluralName": "Peopleas",
  
  "features": {
    "search": true,
    "create": true,
    "edit": true,
    "delete": true,
    "export": true,
    "import": false
  },
  
  "permissions": {
    "canCreate": "user.can('create', 'People')",
    "canEdit": "user.can('edit', 'People')",
    "canDelete": "user.can('delete', 'People')"
  },
  
  "metadata": {
    "version": "1.0",
    "author": "Omar",
    "lastUpdated": "2025-12-31",
    "description": "Gestión de peopleas en sistema Strato"
  }
}
```

#### tableConfig.json - Definición de Tabla

```json
{
  "columns": [
    {
      "key": "id",
      "label": "ID",
      "type": "number",
      "width": "80px",
      "sortable": true,
      "filterable": false,
      "hidden": false
    },
    {
      "key": "name",
      "label": "Nombre Completo",
      "type": "text",
      "width": "250px",
      "sortable": true,
      "filterable": true,
      "searchable": true
    },
    {
      "key": "email",
      "label": "Email",
      "type": "email",
      "width": "250px",
      "sortable": true,
      "filterable": true
    },
    {
      "key": "department.name",
      "label": "Departamento",
      "type": "relation",
      "relationModel": "Department",
      "relationField": "name",
      "width": "150px",
      "sortable": false,
      "filterable": true
    },
    {
      "key": "created_at",
      "label": "Creado",
      "type": "date",
      "width": "150px",
      "sortable": true,
      "filterable": false
    },
    {
      "key": "status",
      "label": "Estado",
      "type": "status",
      "width": "120px",
      "statusColors": {
        "active": "green",
        "inactive": "grey",
        "pending": "orange"
      }
    }
  ],
  
  "styling": {
    "striped": true,
    "hover": true,
    "dense": false,
    "elevation": 1
  },
  
  "pagination": {
    "defaultPageSize": 15,
    "pageSizeOptions": [10, 15, 25, 50]
  }
}
```

#### itemForm.json - Definición de Formulario

```json
{
  "fields": [
    {
      "key": "name",
      "label": "Nombre Completo",
      "type": "text",
      "required": true,
      "placeholder": "Juan Pérez",
      "rules": ["required", "minLength:3", "maxLength:100"],
      "help": "Nombre y apellidos completos",
      "order": 1
    },
    {
      "key": "email",
      "label": "Email",
      "type": "email",
      "required": true,
      "rules": ["required", "email", "unique:people,email"],
      "help": "Email corporativo válido",
      "order": 2
    },
    {
      "key": "dni",
      "label": "DNI/Pasaporte",
      "type": "text",
      "required": true,
      "pattern": "^[0-9A-Z]{8,10}$",
      "rules": ["required", "unique:people,dni"],
      "order": 3
    },
    {
      "key": "phone",
      "label": "Teléfono",
      "type": "tel",
      "required": false,
      "pattern": "^\\+?[0-9\\s\\-\\(\\)]{9,}$",
      "order": 4
    },
    {
      "key": "department_id",
      "label": "Departamento",
      "type": "select",
      "required": true,
      "apiEndpoint": "/api/departments",
      "optionsLabelField": "name",
      "optionsValueField": "id",
      "rules": ["required"],
      "order": 5
    },
    {
      "key": "role_id",
      "label": "Rol",
      "type": "select",
      "required": true,
      "options": [
        { "value": 1, "label": "Junior Developer" },
        { "value": 2, "label": "Senior Developer" },
        { "value": 3, "label": "Team Lead" }
      ],
      "rules": ["required"],
      "order": 6
    },
    {
      "key": "skills",
      "label": "Habilidades",
      "type": "multiselect",
      "required": false,
      "apiEndpoint": "/api/skills",
      "optionsLabelField": "name",
      "optionsValueField": "id",
      "help": "Selecciona todas las habilidades aplicables",
      "order": 7
    },
    {
      "key": "bio",
      "label": "Biografía",
      "type": "textarea",
      "required": false,
      "rows": 4,
      "maxLength": 500,
      "placeholder": "Cuéntanos sobre ti...",
      "order": 8
    },
    {
      "key": "is_active",
      "label": "Activo",
      "type": "checkbox",
      "required": false,
      "defaultValue": true,
      "order": 9
    }
  ],
  
  "layout": {
    "columns": 1,
    "gap": 16,
    "sectioning": [
      {
        "title": "Información Básica",
        "fields": ["name", "email", "dni"]
      },
      {
        "title": "Información Laboral",
        "fields": ["department_id", "role_id", "skills"]
      },
      {
        "title": "Detalles Adicionales",
        "fields": ["phone", "bio", "is_active"]
      }
    ]
  }
}
```

#### filters.json - Definición de Filtros

```json
{
  "fields": [
    {
      "key": "search",
      "label": "Buscar",
      "type": "text",
      "placeholder": "Nombre, email, dni...",
      "searchFields": ["name", "email", "dni"],
      "debounce": 300,
      "order": 1
    },
    {
      "key": "department_id",
      "label": "Departamento",
      "type": "select",
      "apiEndpoint": "/api/departments",
      "multiple": false,
      "clearable": true,
      "order": 2
    },
    {
      "key": "is_active",
      "label": "Estado",
      "type": "select",
      "options": [
        { "value": true, "label": "Activo" },
        { "value": false, "label": "Inactivo" }
      ],
      "clearable": true,
      "order": 3
    },
    {
      "key": "created_from",
      "label": "Creado desde",
      "type": "date",
      "clearable": true,
      "order": 4
    },
    {
      "key": "created_to",
      "label": "Creado hasta",
      "type": "date",
      "clearable": true,
      "order": 5
    }
  ],
  
  "layout": {
    "orientation": "horizontal",
    "spacing": 8
  },
  
  "behavior": {
    "applyOnChange": true,
    "showResetButton": true
  }
}
```

---

## 3. Validación de Schema

### JSON Schema para config.json

```json
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "title": "FormSchema Configuration",
  "type": "object",
  "required": ["model", "apiEndpoint", "title"],
  "properties": {
    "model": {
      "type": "string",
      "description": "Nombre del modelo Eloquent"
    },
    "apiEndpoint": {
      "type": "string",
      "pattern": "^/api/[a-z-]+$",
      "description": "Endpoint base de la API"
    },
    "title": {
      "type": "string",
      "minLength": 3,
      "maxLength": 100
    },
    "tableConfig": {
      "type": "object",
      "required": ["columns"],
      "properties": {
        "columns": {
          "type": "array",
          "items": {
            "type": "object",
            "required": ["key", "label", "type"],
            "properties": {
              "key": { "type": "string" },
              "label": { "type": "string" },
              "type": {
                "type": "string",
                "enum": ["text", "number", "date", "status", "relation"]
              }
            }
          }
        }
      }
    }
  }
}
```

### Validar en Tiempo de Build

```typescript
// scripts/validate-schemas.ts
import AjvModule from "ajv";
import fs from "fs";
import path from "path";

const ajv = new AjvModule();

const schema = JSON.parse(
  fs.readFileSync("./schemas/form-schema.json", "utf-8")
);

const configFiles = [
  "./resources/js/pages/People/people-form/config.json",
  "./resources/js/pages/Certification/certifications-form/config.json",
];

for (const file of configFiles) {
  const config = JSON.parse(fs.readFileSync(file, "utf-8"));
  const validate = ajv.compile(schema);
  
  if (!validate(config)) {
    console.error(`❌ Invalid config in ${file}:`);
    console.error(validate.errors);
    process.exit(1);
  } else {
    console.log(`✅ Valid: ${file}`);
  }
}

console.log("✅ All configurations are valid!");
```

Ejecutar antes de build:

```json
{
  "scripts": {
    "validate:configs": "ts-node scripts/validate-schemas.ts",
    "build": "npm run validate:configs && vite build"
  }
}
```

---

## 4. Extensibilidad: Agregar Nuevas Capacidades

### Agregar Tipo de Campo Peoplealizado

```json
// itemForm.json
{
  "fields": [
    {
      "key": "custom_rating",
      "label": "Calificación Custom",
      "type": "custom:star-rating",  // ← Tipo peoplealizado
      "stars": 5,
      "color": "amber",
      "customComponent": "StarRating"
    }
  ]
}
```

Implementar en FormData.vue:

```vue
<template>
  <!-- ... otros campos ... -->
  
  <!-- CUSTOM: Star Rating -->
  <StarRating
    v-else-if="field.type.startsWith('custom:')"
    v-model="formData[field.key]"
    :field="field"
    :component-name="extractComponentName(field.type)"
  />
</template>

<script setup>
// Importar dinámicamente componentes custom
const StarRating = defineAsyncComponent(
  () => import('@/components/custom/StarRating.vue')
);

function extractComponentName(type: string): string {
  return type.replace('custom:', '');
}
</script>
```

### Sistema de Plugins

```json
{
  "plugins": [
    {
      "name": "field-visibility",
      "config": {
        "rules": [
          {
            "field": "role_id",
            "visible": "isDeveloper()",
          }
        ]
      }
    },
    {
      "name": "field-dependencies",
      "config": {
        "rules": [
          {
            "field": "department_id",
            "requires": ["company_id"],
            "message": "Selecciona empresa primero"
          }
        ]
      }
    }
  ]
}
```

---

## 5. Migración de Imperativo a Declarativo

### Paso 1: Identificar Lo Que Cambiar

```vue
<!-- ANTES (Todo en .vue) -->
<script setup>
const tableHeaders = [
  { title: 'ID', key: 'id' },
  { title: 'Name', key: 'name' },
  // ... 10 más definidos aquí
];

const formFields = [
  { key: 'name', label: 'Name', rules: [...] },
  // ... 10 más definidos aquí
];
</script>
```

### Paso 2: Extraer a JSON

```json
// config.json
{
  "tableConfig": {
    "columns": [ /* lo que estaba en tableHeaders */ ]
  },
  "itemForm": {
    "fields": [ /* lo que estaba en formFields */ ]
  }
}
```

### Paso 3: Actualizar Componente

```vue
<!-- DESPUÉS (Limpio y reutilizable) -->
<script setup>
import config from './people-form/config.json';

const tableHeaders = computed(() => 
  config.tableConfig.columns.map(col => ({
    title: col.label,
    key: col.key,
  }))
);
</script>
```

---

## Conclusión: Configuración como Primera Clase

JSON-Driven Configuration transforma cómo pensamos en construcción de UIs:

- ✅ **Separación:** Lógica de presentación y datos
- ✅ **Mantenimiento:** Cambios sin recompilación
- ✅ **Reutilización:** Una configuración, múltiples usos
- ✅ **Extensibilidad:** Agregar capacidades sin tocar código
- ✅ **Auditoría:** Todo versionado en Git

Una verdadera plataforma de desarrollo.

---

**Próximo capítulo:** [08_CASOS_DE_USO_PATRONES.md](08_CASOS_DE_USO_PATRONES.md)

¿Cómo se implementan casos reales?
