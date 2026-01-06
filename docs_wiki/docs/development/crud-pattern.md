# CRUD Pattern: JSON-Driven Development

El patrón **JSON-Driven CRUD** es el corazón de TalentIA. Permite crear módulos CRUD completos en **10 minutos** sin duplicar código.

---

## 🎯 Concepto Fundamental

En lugar de escribir código Vue para cada módulo, **configuramos el comportamiento con JSON**:

=== "❌ Enfoque Tradicional"
    ```vue
    <!-- PeopleIndex.vue -->
    <template>
      <v-data-table :headers="headers" ...>
        <!-- 150 líneas de código -->
      </v-data-table>
    </template>
    
    <!-- SkillsIndex.vue -->
    <template>
      <v-data-table :headers="headers" ...>
        <!-- Las mismas 150 líneas de código -->
      </v-data-table>
    </template>
    ```
    
    **Problema:** Duplicación de código, difícil mantenimiento

=== "✅ Enfoque JSON-Driven"
    ```vue
    <!-- Index.vue (reutilizable) -->
    <template>
      <FormSchema :config="config" />
    </template>
    ```
    
    ```json
    // people-config.json
    {
      "titulo": "People",
      "endpoint": "/api/people",
      "fields": [...]
    }
    ```
    
    **Ventaja:** 1 componente, N configuraciones

---

## 🏗️ Arquitectura Completa

```mermaid
graph TB
    A[Index.vue] -->|Importa| B[config.json]
    A -->|Importa| C[tableConfig.json]
    A -->|Importa| D[itemForm.json]
    A -->|Importa| E[filters.json]
    
    A -->|Usa| F[FormSchema.vue]
    F -->|Renderiza tabla con| C
    F -->|Renderiza formulario con| D
    F -->|Renderiza filtros con| E
    
    F -->|HTTP Request| G[/api/people]
    G -->|Resuelve a| H[FormSchemaController]
    H -->|Inicializa| I[PeopleRepository]
    I -->|Query| J[Database]
    
    style F fill:#4CAF50
    style H fill:#2196F3
```

---

## 📁 Estructura de Archivos

Para crear un CRUD completo necesitas:

```
resources/js/pages/YourModule/
├── Index.vue                      # Orquestador (40 líneas)
└── your-module-form/
    ├── config.json               # Configuración general
    ├── tableConfig.json          # Columnas de la tabla
    ├── itemForm.json             # Campos del formulario
    └── filters.json              # Filtros de búsqueda
```

---

## 🔧 Los 4 Archivos JSON

### 1. config.json - Configuración General

Define endpoints, permisos y metadatos del módulo.

```json
{
  "endpoints": {
    "index": "/api/people",
    "apiUrl": "/api/people"
  },
  "titulo": "People Management",
  "descripcion": "Manage people and their profiles",
  "permisos": {
    "crear": true,
    "editar": true,
    "eliminar": true
  }
}
```

### 2. tableConfig.json - Columnas de la Tabla

Define qué columnas mostrar en la tabla de listado.

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
      "value": "department.name",
      "sortable": false
    },
    {
      "text": "Actions",
      "value": "actions",
      "sortable": false
    }
  ],
  "options": {
    "dense": false,
    "itemsPerPage": 15,
    "search": true
  }
}
```

### 3. itemForm.json - Formulario

Define los campos del formulario de creación/edición.

```json
{
  "fields": [
    {
      "key": "name",
      "label": "Full Name",
      "type": "text",
      "rules": ["required"]
    },
    {
      "key": "email",
      "label": "Email Address",
      "type": "email",
      "rules": ["required", "email"]
    },
    {
      "key": "department_id",
      "label": "Department",
      "type": "select",
      "rules": ["required"],
      "catalog": "departments"
    },
    {
      "key": "hire_date",
      "label": "Hire Date",
      "type": "date",
      "rules": []
    }
  ],
  "catalogs": [
    {
      "name": "departments",
      "endpoint": "/api/departments",
      "valueKey": "id",
      "textKey": "name"
    }
  ]
}
```

**Tipos de campo soportados:**
- `text` - Input de texto
- `email` - Input de email
- `number` - Input numérico
- `date` - Date picker
- `select` - Dropdown de catálogo
- `textarea` - Área de texto
- `checkbox` - Checkbox booleano

### 4. filters.json - Filtros de Búsqueda

Define filtros para la búsqueda avanzada.

```json
{
  "filters": [
    {
      "key": "department_id",
      "label": "Department",
      "type": "select",
      "catalog": "departments"
    },
    {
      "key": "hire_date_from",
      "label": "Hired After",
      "type": "date"
    },
    {
      "key": "status",
      "label": "Status",
      "type": "select",
      "options": [
        { "value": "active", "text": "Active" },
        { "value": "inactive", "text": "Inactive" }
      ]
    }
  ]
}
```

---

## 🚀 Crear Nuevo CRUD en 5 Pasos

### Paso 1: Registrar Modelo (1 min)

Edita [`routes/form-schema-complete.php`](../../../routes/form-schema-complete.php):

```php
$formSchemaModels = [
    'People' => 'people',
    'Skills' => 'skills',
    'Role' => 'roles',
    'YourModel' => 'your-models',  // ← AGREGAR
];
```

✅ **Resultado:** Todas las rutas API se crean automáticamente:

```
GET    /api/your-models
POST   /api/your-models
GET    /api/your-models/{id}
PUT    /api/your-models/{id}
DELETE /api/your-models/{id}
POST   /api/your-models/search
```

### Paso 2: Crear Estructura de Archivos (1 min)

```bash
mkdir -p resources/js/pages/YourModel/your-model-form
cd resources/js/pages/YourModel

touch Index.vue
touch your-model-form/config.json
touch your-model-form/tableConfig.json
touch your-model-form/itemForm.json
touch your-model-form/filters.json
```

### Paso 3: Copiar Index.vue Base (1 min)

Copia desde [`People/Index.vue`](../../../resources/js/pages/People/Index.vue):

```vue
<script setup lang="ts">
import { ref } from 'vue';
import FormSchema from '@/components/FormSchema.vue';

// Importar configuraciones
import config from './your-model-form/config.json';
import tableConfig from './your-model-form/tableConfig.json';
import itemForm from './your-model-form/itemForm.json';
import filters from './your-model-form/filters.json';

const formSchemaRef = ref(null);
</script>

<template>
  <FormSchema
    ref="formSchemaRef"
    :config="config"
    :table-config="tableConfig"
    :item-form="itemForm"
    :filters="filters"
  />
</template>
```

### Paso 4: Llenar los 4 JSONs (5-8 min)

Usa las plantillas mostradas arriba y personaliza según tu modelo.

!!! tip "Copia desde módulos existentes"
    El módulo **People** es el más completo. Cópialo como base y modifica.

### Paso 5: Agregar Ruta en Vue Router (1 min)

Edita [`router/index.ts`](../../../resources/js/router/index.ts):

```typescript
{
  path: '/your-models',
  name: 'YourModels',
  component: () => import('@/pages/YourModel/Index.vue'),
  meta: { requiresAuth: true }
}
```

---

## ✅ Verificación

Prueba tu nuevo CRUD:

1. **Backend:**
   ```bash
   curl http://127.0.0.1:8000/api/your-models
   ```

2. **Frontend:**
   - Navega a `/your-models`
   - Verifica que la tabla carga
   - Crea un registro nuevo
   - Edita un registro
   - Elimina un registro

---

## 🎨 FormSchema.vue - El Componente Mágico

`FormSchema.vue` es el componente genérico que interpreta los JSONs y genera la UI:

**Lo que hace:**

1. **Recibe** los 4 JSONs como props
2. **Renderiza** tabla con columnas de `tableConfig`
3. **Genera** formulario con campos de `itemForm`
4. **Aplica** filtros de `filters`
5. **Ejecuta** peticiones HTTP a los endpoints de `config`

**Lo que NO necesitas hacer:**

- ❌ Escribir HTML de la tabla
- ❌ Escribir lógica de paginación
- ❌ Escribir validaciones de formulario
- ❌ Escribir peticiones HTTP
- ❌ Manejar estados de carga/error

Todo está **centralizado** en `FormSchema.vue`.

---

## 🔄 Backend: FormSchemaController

El controller genérico que maneja TODAS las peticiones CRUD:

```php
// routes/form-schema-complete.php
Route::get('/{model}', [FormSchemaController::class, 'index']);
Route::post('/{model}', [FormSchemaController::class, 'store']);
Route::get('/{model}/{id}', [FormSchemaController::class, 'show']);
Route::put('/{model}/{id}', [FormSchemaController::class, 'update']);
Route::delete('/{model}/{id}', [FormSchemaController::class, 'destroy']);
```

**Flujo interno:**

```php
// FormSchemaController::index()
public function index(Request $request, string $modelName)
{
    // 1. Resolver modelo dinámicamente
    $this->initializeForModel($modelName);
    // → $this->modelClass = "App\Models\People"
    // → $this->repository = new PeopleRepository()
    
    // 2. Delegar a repository
    return $this->repository->index($request);
    // → Ejecuta query, aplica filtros, retorna JSON
}
```

**Ventajas:**

✅ **1 controller** para 80+ modelos  
✅ **Sin duplicación** de código  
✅ **Fácil extender** con nuevos métodos  
✅ **Testing centralizado**

---

## 📊 Comparación: Tradicional vs JSON-Driven

| Aspecto | Tradicional | JSON-Driven |
|---------|-------------|-------------|
| **Archivos por CRUD** | 5-10 archivos | 5 archivos (1 Vue + 4 JSON) |
| **Líneas de código** | ~500 líneas | ~120 líneas |
| **Tiempo de desarrollo** | 2-4 horas | 10-15 minutos |
| **Duplicación** | Alta (90% código repetido) | Mínima (solo config) |
| **Mantenimiento** | Difícil (cambios en N archivos) | Fácil (cambio en 1 componente) |
| **Testing** | Test por cada módulo | Tests reutilizables |

---

## 🎯 Próximos Pasos

<div class="grid" markdown>

- **[Crear tu primer CRUD →](new-crud-guide.md)**
  
  Guía paso a paso con ejemplo completo.

- **[FormSchema API →](formschema-system.md)**
  
  Documentación completa de FormSchema.vue.

- **[Testing Strategy →](testing.md)**
  
  Cómo testear módulos JSON-Driven.

</div>

---

## 💡 Tips Avanzados

!!! success "Reutiliza Configuraciones"
    Crea un JSON base y extiéndelo para módulos similares.

!!! warning "Validación en Ambos Lados"
    Valida en el frontend (UX) y backend (seguridad).

!!! tip "Eager Loading"
    Configura relaciones en el Repository para evitar N+1 queries.

!!! example "Catálogos Compartidos"
    Múltiples forms pueden reutilizar el mismo catálogo.
