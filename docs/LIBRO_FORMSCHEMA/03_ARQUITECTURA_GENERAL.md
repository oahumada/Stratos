# Capítulo 3: Arquitectura General del Sistema

**Duración de lectura:** 25 minutos  
**Nivel:** Intermedio-Avanzado  
**Visual:** Diagramas de flujo y componentes

---

## Visión Integral del Sistema

FormSchema Pattern es una **arquitectura completa** que integra:

```
┌─────────────────────────────────────────────────────────────┐
│                   CAPAS Y COMPONENTES                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌────────────────────────────────────────────────────┐   │
│  │           PRESENTACIÓN (Frontend)                  │   │
│  │  ┌──────────────┐  ┌──────────────┐               │   │
│  │  │ FormSchema   │  │  FormData    │               │   │
│  │  │ (tabla)      │  │  (formulario)│               │   │
│  │  └──────────────┘  └──────────────┘               │   │
│  └────────────────────────────────────────────────────┘   │
│                        ▲                                     │
│                        │ HTTP/JSON                         │
│                        ▼                                     │
│  ┌────────────────────────────────────────────────────┐   │
│  │         ENRUTAMIENTO Y CONTROL (Routes)            │   │
│  │  ┌──────────────────────────────────────────────┐  │   │
│  │  │  form-schema-complete.php                    │  │   │
│  │  │  Genera rutas dinámicamente                  │  │   │
│  │  │  GET    /api/[model]                         │  │   │
│  │  │  POST   /api/[model]                         │  │   │
│  │  │  PUT    /api/[model]/{id}                    │  │   │
│  │  │  DELETE /api/[model]/{id}                    │  │   │
│  │  └──────────────────────────────────────────────┘  │   │
│  └────────────────────────────────────────────────────┘   │
│                        ▲                                     │
│                        │ Routing                            │
│                        ▼                                     │
│  ┌────────────────────────────────────────────────────┐   │
│  │    APLICACIÓN (Controllers & Business Logic)       │   │
│  │  ┌──────────────────────────────────────────────┐  │   │
│  │  │ FormSchemaController                        │  │   │
│  │  │ • index() - Listar                           │  │   │
│  │  │ • store() - Crear                            │  │   │
│  │  │ • show() - Mostrar                           │  │   │
│  │  │ • update() - Actualizar                      │  │   │
│  │  │ • destroy() - Eliminar                       │  │   │
│  │  │ • search() - Buscar con filtros              │  │   │
│  │  └──────────────────────────────────────────────┘  │   │
│  └────────────────────────────────────────────────────┘   │
│                        ▲                                     │
│                        │ Delegación                         │
│                        ▼                                     │
│  ┌────────────────────────────────────────────────────┐   │
│  │      PERSISTENCIA (Models & Repositories)          │   │
│  │  ┌──────────────┐  ┌──────────────┐               │   │
│  │  │ Repository   │  │ Eloquent     │               │   │
│  │  │ (abstracción)│  │ Models       │               │   │
│  │  └──────────────┘  └──────────────┘               │   │
│  │         ▲              ▲                            │   │
│  │         └──────┬───────┘                            │   │
│  │                │                                     │   │
│  │         ┌──────────────────┐                        │   │
│  │         │   Base de Datos  │                        │   │
│  │         │   (PostgreSQL)   │                        │   │
│  │         └──────────────────┘                        │   │
│  └────────────────────────────────────────────────────┘   │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 1. Capa de Presentación (Frontend)

### Componentes Vue

#### FormSchema.vue - El Contenedor Principal

```vue
<!-- Estructura típica -->
<template>
  <AppLayout>
    <!-- Encabezado con botón crear -->
    <div class="header">
      <h1>{{ config.titulo }}</h1>
      <v-btn @click="openCreateDialog">New Record</v-btn>
    </div>
    
    <!-- Búsqueda -->
    <FormData 
      v-model="searchQuery"
      :schema="filters"
      @search="performSearch"
    />
    
    <!-- Tabla -->
    <TableComponent
      :config="tableConfig"
      :data="items"
      :loading="loading"
      @edit="editItem"
      @delete="deleteItem"
    />
    
    <!-- Dialog de crear/editar -->
    <v-dialog v-model="dialog">
      <v-card>
        <FormData
          :schema="itemForm"
          :initial-data="editingItem"
          @submit="saveItem"
        />
      </v-card>
    </v-dialog>
  </AppLayout>
</template>

<script setup lang="ts">
// Importa configuración específica del modelo
import configJson from './[model]-form/config.json';
import tableConfigJson from './[model]-form/tableConfig.json';
import itemFormJson from './[model]-form/itemForm.json';
import filtersJson from './[model]-form/filters.json';

// Lógica genérica que funciona para CUALQUIER modelo
// (copiada desde Person/Index.vue sin cambios)
</script>
```

**Responsabilidades:**
- Renderizar tabla de datos
- Manejar búsqueda y filtros
- Mostrar diálogos CRUD
- Consumir endpoint API genérico

---

#### FormData.vue - Renderizador de Formularios

```vue
<template>
  <v-form @submit.prevent="submitForm">
    <div v-for="field in schema.fields" :key="field.key">
      <!-- Renderiza dinámicamente según type -->
      <v-text-field
        v-if="field.type === 'text'"
        v-model="formData[field.key]"
        :label="field.label"
        :rules="field.rules"
      />
      
      <v-date-field
        v-else-if="field.type === 'date'"
        v-model="formData[field.key]"
        :label="field.label"
      />
      
      <v-select
        v-else-if="field.type === 'select'"
        v-model="formData[field.key]"
        :items="getCatalog(field.key)"
        :label="field.label"
      />
      
      <!-- ... más tipos -->
    </div>
    <v-btn type="submit">Save</v-btn>
  </v-form>
</template>

<script setup lang="ts">
// Solo renderiza, no tiene lógica de negocio
// Completamente reutilizable para cualquier schema JSON
</script>
```

**Responsabilidades:**
- Renderizar campos dinámicamente
- Validar input cliente-side
- Emitir eventos de submit

---

#### Flujo de Datos en Frontend

```
Usuario abre página
        │
        ▼
FormSchema.vue carga
        │
        ├─→ Lee config.json (endpoints)
        ├─→ Lee tableConfig.json (columnas)
        ├─→ Lee itemForm.json (campos formulario)
        └─→ Lee filters.json (filtros)
        │
        ▼
axios.get('/api/[model]')  ← endpoint genérico
        │
        ▼
Datos cargan en tabla
        │
        ▼
Usuario interactúa:
        ├─→ Buscar → axios.post('/api/[model]/search')
        ├─→ Crear → axios.post('/api/[model]')
        ├─→ Editar → axios.put('/api/[model]/{id}')
        └─→ Eliminar → axios.delete('/api/[model]/{id}')
        │
        ▼
Respuesta → Re-renderizar tabla
```

---

## 2. Capa de Enrutamiento (Routes)

### form-schema-complete.php - El Generador de Rutas

```php
<?php

// Mapeo de modelos a rutas
$formSchemaModels = [
    'Person' => 'person',
    'Certification' => 'certifications',
    'Role' => 'roles',
    'Skill' => 'skills',
];

// Generar rutas automáticamente
Route::group([], function () use ($formSchemaModels) {
    foreach ($formSchemaModels as $modelName => $routeName) {
        
        // GET /api/[route] - Index
        Route::get($routeName, function (Request $request) use ($modelName) {
            $controller = new FormSchemaController();
            return $controller->index($request, $modelName);
        })->name("api.{$routeName}.index");
        
        // POST /api/[route] - Store
        Route::post($routeName, function (Request $request) use ($modelName) {
            $controller = new FormSchemaController();
            return $controller->store($request, $modelName);
        })->name("api.{$routeName}.store");
        
        // GET /api/[route]/{id} - Show
        Route::get("{$routeName}/{id}", function (Request $request, $id) use ($modelName) {
            $controller = new FormSchemaController();
            return $controller->show($request, $modelName, $id);
        })->name("api.{$routeName}.show");
        
        // ... PUT, PATCH, DELETE, POST search ...
    }
});
```

**Lo que genera automáticamente:**

```
GET    /api/person
POST   /api/person
GET    /api/person/{id}
PUT    /api/person/{id}
PATCH  /api/person/{id}
DELETE /api/person/{id}
POST   /api/person/search
...

GET    /api/certifications
POST   /api/certifications
GET    /api/certifications/{id}
... (8 rutas por modelo)
```

**Ventaja:** Nuevo modelo = 1 línea agregada a mapeo, y automáticamente 8 rutas creadas.

---

## 3. Capa de Aplicación (Controllers)

### FormSchemaController - El Orquestador

```php
class FormSchemaController extends Controller
{
    private $modelClass;
    private $repository;
    private $validator;
    
    /**
     * Inicializa el controller para un modelo específico
     * 
     * Ejemplo: initializeForModel('Person')
     * - Carga clase App\Models\Person
     * - Crea instancia de PersonRepository (o genérico)
     * - Selecciona validaciones para Person
     */
    private function initializeForModel(string $modelName): void
    {
        // Reflexión dinámica: convierte 'Person' → App\Models\Person
        $class = "App\\Models\\" . $modelName;
        $this->modelClass = $class;
        
        // Intenta cargar repositorio específico, si no existe usa genérico
        $repoClass = "App\\Repository\\" . $modelName . "Repository";
        if (class_exists($repoClass)) {
            $this->repository = new $repoClass(new $class());
        } else {
            $this->repository = new GenericRepository(new $class());
        }
        
        // Selecciona validador
        $this->validator = new FormValidator($modelName);
    }
    
    /**
     * GET /api/[model] - Listar
     */
    public function index(Request $request, string $modelName)
    {
        $this->initializeForModel($modelName);
        
        $items = $this->repository->search($request->all());
        
        return response()->json([
            'data' => $items,
            'message' => 'Success'
        ]);
    }
    
    /**
     * POST /api/[model] - Crear
     */
    public function store(Request $request, string $modelName)
    {
        $this->initializeForModel($modelName);
        
        // Validar datos
        $validated = $this->validator->validate($request->all());
        
        // Crear registro
        $item = $this->repository->create($validated);
        
        return response()->json($item, 201);
    }
    
    /**
     * GET /api/[model]/{id} - Mostrar
     */
    public function show(Request $request, string $modelName, $id)
    {
        $this->initializeForModel($modelName);
        
        $item = $this->repository->find($id);
        
        if (!$item) {
            return response()->json(['error' => 'Not found'], 404);
        }
        
        return response()->json($item);
    }
    
    /**
     * PUT /api/[model]/{id} - Actualizar
     */
    public function update(Request $request, string $modelName, $id)
    {
        $this->initializeForModel($modelName);
        
        $validated = $this->validator->validate($request->all());
        $item = $this->repository->update($id, $validated);
        
        return response()->json($item);
    }
    
    /**
     * DELETE /api/[model]/{id} - Eliminar
     */
    public function destroy(Request $request, string $modelName, $id)
    {
        $this->initializeForModel($modelName);
        
        $this->repository->delete($id);
        
        return response()->json(null, 204);
    }
    
    /**
     * POST /api/[model]/search - Buscar con filtros
     */
    public function search(Request $request, string $modelName)
    {
        $this->initializeForModel($modelName);
        
        $items = $this->repository->search(
            $request->get('search'),
            $request->get('filters', []),
            $request->get('page', 1)
        );
        
        return response()->json(['data' => $items]);
    }
}
```

**Flujo:**
1. Ruta entra con `$modelName` (ej: "Person")
2. `initializeForModel()` carga el modelo, repositorio, validador
3. Método ejecuta operación genérica
4. Retorna JSON

**Punto clave:** El controller **NO conoce** qué modelo está usando. Solo delega.

---

## 4. Capa de Persistencia (Models & Repositories)

### Flujo de Datos

```
FormSchemaController (pide datos)
        │
        ▼
Repository Interface (abstracción)
        │
        ├─→ PersonRepository (específico)
        ├─→ CertificationRepository (específico)
        └─→ GenericRepository (fallback)
        │
        ▼
Eloquent Models
        │
        ├─→ Person
        ├─→ Certification
        └─→ Role
        │
        ▼
Base de Datos
```

### GenericRepository - Fallback Reutilizable

```php
class GenericRepository implements RepositoryInterface
{
    protected $model;
    
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    
    public function all()
    {
        return $this->model->paginate(15);
    }
    
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }
    
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    
    public function update($id, array $data)
    {
        $item = $this->model->findOrFail($id);
        $item->update($data);
        return $item;
    }
    
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
    
    public function search($search = null, $filters = [], $page = 1)
    {
        $query = $this->model->query();
        
        if ($search) {
            // Busca en campos searchable del modelo
            foreach ($this->model->getSearchableColumns() as $column) {
                $query->orWhere($column, 'like', "%{$search}%");
            }
        }
        
        if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                $query->where($field, $value);
            }
        }
        
        return $query->paginate(15);
    }
}
```

---

## 5. Flujo Completo de una Solicitud

### Ejemplo: Usuario busca "aws" en Certifications

```
┌─────────────────────────────────────────────────────────┐
│ 1. FRONTEND - Usuario escribe "aws" en búsqueda        │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
    axios.post('/api/certifications/search', {
        search: 'aws',
        filters: { status: 'active' }
    })
                       │
                       ▼
┌──────────────────────────────────────────────────────────┐
│ 2. ROUTING - form-schema-complete.php                   │
│    Intercepta POST /api/certifications/search           │
│    Llama: FormSchemaController→search('Certification')  │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────────────────┐
│ 3. CONTROLLER - FormSchemaController                    │
│    search() {                                            │
│        initializeForModel('Certification')              │
│        → Carga App\Models\Certification                │
│        → Carga CertificationRepository                  │
│        $items = $this->repository->search(              │
│            'aws',                                       │
│            { status: 'active' }                         │
│        )                                                │
│        return response()->json($items)                  │
│    }                                                     │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────────────────┐
│ 4. REPOSITORY - CertificationRepository                 │
│    search() {                                            │
│        $query = Certification::query()                 │
│        $query->where('name', 'like', '%aws%')          │
│        $query->orWhere('provider', 'like', '%aws%')    │
│        $query->where('status', 'active')               │
│        return $query->paginate(15)                      │
│    }                                                     │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────────────────┐
│ 5. DATABASE - PostgreSQL                                │
│    SELECT * FROM certifications                         │
│    WHERE (name LIKE '%aws%' OR provider LIKE '%aws%')   │
│    AND status = 'active'                                │
│    LIMIT 15 OFFSET 0                                    │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────────────────┐
│ 6. RESPONSE - JSON                                      │
│    {                                                     │
│        "data": [                                        │
│            { id: 1, name: "AWS Solutions", ...},        │
│            { id: 2, name: "AWS Developer", ...},        │
│        ],                                               │
│        "meta": { current_page: 1, total: 2 }            │
│    }                                                     │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────────────────┐
│ 7. FRONTEND - FormSchema.vue                            │
│    Recibe respuesta, re-renderiza tabla                 │
│    Muestra 2 resultados encontrados                     │
└──────────────────────────────────────────────────────────┘
```

---

## 6. Patrones de Interacción

### Patrón 1: "Agregar Nuevo Modelo"

```
Paso 1: Crear Modelo
        └─→ app/Models/Certification.php

Paso 2: Crear Migración
        └─→ database/migrations/create_certifications_table.php

Paso 3: Registrar en form-schema-complete.php
        'Certification' => 'certifications',
        
        ✅ Automáticamente:
           - 8 rutas creadas
           - FormSchemaController soporta Certification
           - GenericRepository funciona con Certification

Paso 4: Crear Frontend
        └─→ /pages/Certifications/Index.vue (copiar + 4 imports)
        └─→ /pages/Certifications/certifications-form/ (4 JSONs)

Paso 5: Registrar Ruta Web
        Route::get('/certifications', ...)
        
Paso 6: Agregar Navlink
        └─→ AppSidebar.vue
        
✅ CRUD COMPLETO EN 15 MINUTOS
```

---

## Conclusión: Arquitectura Integrada

FormSchema Pattern es **arquitectura completa y coherente**:

- ✅ **Frontend:** Vue components genéricos + JSON config
- ✅ **Routes:** Generación dinámica sin duplicación
- ✅ **Controller:** Genérico con reflexión dinámica
- ✅ **Persistencia:** Repository pattern + Eloquent
- ✅ **BD:** Acceso optimizado y consistente

Cada parte sabe hacer UNA cosa bien y se comunica claramente con las otras.

---

**Próximo capítulo:** [04_FORMSCHEMA_CONTROLLER.md](04_FORMSCHEMA_CONTROLLER.md)

¿Cómo el controller es dinámico y soporta múltiples modelos?
