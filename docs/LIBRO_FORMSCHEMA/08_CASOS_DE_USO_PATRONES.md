# Capítulo 8: Casos de Uso y Patrones Reales

**Duración de lectura:** 30 minutos  
**Nivel:** Avanzado  
**Conceptos clave:** Aplicación práctica, Patrones reales, Integración full-stack

---

## Introducción: De la Teoría a la Práctica

Los capítulos anteriores explicaban **cómo** funciona FormSchema Pattern. Este capítulo muestra **cómo se usa** en escenarios reales.

---

## Caso 1: CRUD Simple - Personas

### Requisitos

```
Listar todas las personas
Buscar por nombre/email
Crear nueva persona
Editar persona existente
Eliminar persona
```

### Paso 1: Crear Migraci\u00f3n

```php
// database/migrations/2024_01_01_create_people_table.php
Schema::create('people', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('dni')->unique();
    $table->foreignId('department_id')->constrained();
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->timestamps();
    $table->softDeletes();
});
```

### Paso 2: Crear Modelo

```php
// app/Models/Person.php
class Person extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['name', 'email', 'dni', 'department_id', 'status'];
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    // Para búsqueda
    public function getSearchableColumns(): array
    {
        return ['name', 'email', 'dni'];
    }
}
```

### Paso 3: Registrar en form-schema-complete.php

```php
// routes/form-schema-complete.php
$formSchemaModels = [
    'Person' => 'person',
    // ... otros modelos ...
];
```

✅ **Automáticamente:** 8 rutas creadas

### Paso 4: Crear Configuración JSON

```json
// resources/js/pages/Person/person-form/config.json
{
  "model": "Person",
  "apiEndpoint": "/api/person",
  "title": "Personas",
  "singularName": "Persona",
  "pluralName": "Personas"
}
```

```json
// resources/js/pages/Person/person-form/tableConfig.json
{
  "columns": [
    { "key": "id", "label": "ID", "type": "number", "width": "80px" },
    { "key": "name", "label": "Nombre", "type": "text", "width": "200px" },
    { "key": "email", "label": "Email", "type": "email", "width": "250px" },
    {
      "key": "department.name",
      "label": "Departamento",
      "type": "relation",
      "relationModel": "Department",
      "relationField": "name"
    },
    { "key": "status", "label": "Estado", "type": "status" },
    { "key": "created_at", "label": "Creado", "type": "date" }
  ]
}
```

```json
// resources/js/pages/Person/person-form/itemForm.json
{
  "fields": [
    {
      "key": "name",
      "label": "Nombre Completo",
      "type": "text",
      "required": true,
      "rules": ["required", "minLength:3"]
    },
    {
      "key": "email",
      "label": "Email",
      "type": "email",
      "required": true,
      "rules": ["required", "email"]
    },
    {
      "key": "dni",
      "label": "DNI/Pasaporte",
      "type": "text",
      "required": true,
      "rules": ["required"]
    },
    {
      "key": "department_id",
      "label": "Departamento",
      "type": "select",
      "required": true,
      "apiEndpoint": "/api/departments",
      "rules": ["required"]
    },
    {
      "key": "status",
      "label": "Estado",
      "type": "select",
      "required": true,
      "options": [
        { "value": "active", "label": "Activo" },
        { "value": "inactive", "label": "Inactivo" }
      ]
    }
  ]
}
```

```json
// resources/js/pages/Person/person-form/filters.json
{
  "fields": [
    {
      "key": "search",
      "label": "Buscar",
      "type": "text",
      "placeholder": "Nombre, email, dni..."
    },
    {
      "key": "department_id",
      "label": "Departamento",
      "type": "select",
      "apiEndpoint": "/api/departments",
      "clearable": true
    },
    {
      "key": "status",
      "label": "Estado",
      "type": "select",
      "options": [
        { "value": "active", "label": "Activo" },
        { "value": "inactive", "label": "Inactivo" }
      ],
      "clearable": true
    }
  ]
}
```

### Paso 5: Crear Componente Index

```vue
<!-- resources/js/pages/Person/Index.vue -->
<template>
  <AppLayout title="Personas">
    <FormSchema :config="config" />
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import FormSchema from '@/components/FormSchema.vue';
import configJson from './person-form/config.json';

const config = ref(configJson);
</script>
```

### Paso 6: Registrar Ruta Web

```php
// routes/web.php
Route::get('/person', [PersonController::class, 'index'])->name('person.index');

// PersonController retorna Inertia page
class PersonController extends Controller
{
    public function index()
    {
        return inertia('Person/Index');
    }
}
```

### Paso 7: Agregar al Sidebar

```vue
<!-- resources/js/components/AppSidebar.vue -->
<v-list-item 
  to="/person" 
  title="Personas"
  prepend-icon="mdi-account"
/>
```

✅ **CRUD COMPLETO EN 30 MINUTOS**

---

## Caso 2: Relaciones Many-to-Many - Persona + Habilidades

### Modelo Actualizado

```php
// app/Models/Person.php
class Person extends Model
{
    // Relación many-to-many
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'person_skill');
    }
}
```

### Configuración para Many-to-Many

```json
// person-form/itemForm.json - Campo actualizado
{
  "key": "skills",
  "label": "Habilidades",
  "type": "multiselect",
  "apiEndpoint": "/api/skills",
  "optionsLabelField": "name",
  "optionsValueField": "id",
  "help": "Selecciona todas las habilidades"
}
```

### Controller Personalizado (si es necesario)

```php
// app/Repository/PersonRepository.php
class PersonRepository extends GenericRepository
{
    public function update($id, array $data): Model
    {
        $person = $this->model->findOrFail($id);
        
        // Extraer habilidades
        $skills = $data['skills'] ?? [];
        unset($data['skills']);
        
        // Actualizar datos personales
        $person->update($data);
        
        // Sincronizar relación many-to-many
        if (!empty($skills)) {
            $person->skills()->sync($skills);
        }
        
        return $person->fresh(['skills']);
    }
    
    public function create(array $data): Model
    {
        $skills = $data['skills'] ?? [];
        unset($data['skills']);
        
        $person = $this->model->create($data);
        
        if (!empty($skills)) {
            $person->skills()->sync($skills);
        }
        
        return $person->fresh(['skills']);
    }
}
```

### Registrar Repository

```php
// routes/form-schema-complete.php - FormSchemaController
// Automáticamente busca PersonRepository si existe
// Si no existe, usa GenericRepository
```

✅ **Many-to-many soportado sin cambios en FormSchemaController**

---

## Caso 3: Validaciones Complejas

### Validador Específico

```php
// app/Validators/PersonValidator.php
class PersonValidator extends FormValidator
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:people,email',
            'dni' => 'required|regex:/^[0-9A-Z]{8,10}$/|unique:people,dni',
            'department_id' => 'required|exists:departments,id',
            'status' => 'required|in:active,inactive',
        ];
    }
    
    public function messages(): array
    {
        return [
            'dni.regex' => 'DNI debe ser 8-10 caracteres alfanuméricos',
            'email.unique' => 'Este email ya existe',
            'dni.unique' => 'Este DNI ya existe',
        ];
    }
    
    /**
     * Validación personalizada compleja
     */
    public function validateCustomRules(array $data): bool
    {
        // Regla: Si department = IT, skills requerido
        if ($data['department_id'] == 3) { // IT dept
            if (empty($data['skills'])) {
                throw new ValidationException(
                    'IT department requires at least one skill'
                );
            }
        }
        
        return true;
    }
}
```

### Usar en Controller

```php
// app/Http/Controllers/FormSchemaController.php
// Ya lo hace automáticamente:
// $this->validator = new FormValidator($modelName);
// Busca AppValidators\PersonValidator si existe
```

---

## Caso 4: Búsqueda Avanzada

### Endpoint Especializado

```php
// routes/form-schema-complete.php
Route::post('{routeName}/search', function (Request $request) use ($modelName) {
    return (new FormSchemaController())
        ->search($request, $modelName);
});

// Que llama a FormSchemaController::search()
```

### Implementación en Frontend

```vue
<!-- FormSchema.vue -->
<script setup>
async function performSearch() {
  const response = await axios.post(
    `${apiEndpoint.value}/search`,
    {
      query: searchQuery.value,
      filters: {
        department_id: selectedDepartment.value,
        status: selectedStatus.value,
      },
      sort: 'created_at',
      order: 'desc',
      page: currentPage.value
    }
  );
  
  items.value = response.data.data;
}
</script>
```

### Backend Search Avanzado

```php
// app/Repository/PersonRepository.php
public function advancedSearch(
    string $query = '',
    array $filters = [],
    string $sort = 'created_at',
    string $order = 'desc',
    int $page = 1
)
{
    $builder = $this->model->query();
    
    // Búsqueda de texto completo
    if ($query) {
        $builder->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('dni', 'like', "%{$query}%");
        });
    }
    
    // Filtros específicos
    if (!empty($filters['department_id'])) {
        $builder->where('department_id', $filters['department_id']);
    }
    
    if (!empty($filters['status'])) {
        $builder->where('status', $filters['status']);
    }
    
    // Incluir relaciones si son buscadas
    $builder->with(['department', 'skills']);
    
    // Ordenar y paginar
    return $builder
        ->orderBy($sort, $order)
        ->paginate(15);
}
```

---

## Caso 5: Exportar a CSV

### Endpoint en Controller

```php
// app/Http/Controllers/FormSchemaController.php
public function export(Request $request, string $modelName)
{
    $this->initializeForModel($modelName);
    
    $items = $this->repository->all(limit: 10000);
    
    return response()->streamDownload(
        function () use ($items) {
            $file = fopen('php://output', 'w');
            
            // Header
            if ($items->count() > 0) {
                fputcsv($file, array_keys($items[0]->toArray()));
            }
            
            // Data
            foreach ($items as $item) {
                fputcsv($file, $item->toArray());
            }
            
            fclose($file);
        },
        strtolower($modelName) . '-' . now()->format('Y-m-d-His') . '.csv'
    );
}
```

### Ruta para exportar

```php
// routes/form-schema-complete.php
Route::get('{routeName}/export', function (Request $request) use ($modelName) {
    return (new FormSchemaController())
        ->export($request, $modelName);
})->name("api.{$routeName}.export");
```

### Usar desde Frontend

```vue
<v-btn 
  icon="mdi-download"
  @click="exportData"
  title="Descargar CSV"
/>

<script setup>
function exportData() {
  window.location.href = `${apiEndpoint.value}/export`;
}
</script>
```

---

## Caso 6: Campos Personalizados Dinámicos

### Configuración JSON con Custom Field

```json
{
  "key": "custom_metadata",
  "label": "Metadatos",
  "type": "custom:json-editor",
  "component": "JsonEditor",
  "editorOptions": {
    "mode": "code",
    "mainMenuBar": true,
    "navigationBar": false
  }
}
```

### Componente Custom

```vue
<!-- components/custom/JsonEditor.vue -->
<template>
  <div class="json-editor">
    <v-btn @click="toggle">
      {{ editing ? 'View' : 'Edit' }} JSON
    </v-btn>
    
    <v-card v-if="editing">
      <textarea v-model="jsonText"></textarea>
      <v-btn @click="save">Save</v-btn>
    </v-card>
    
    <pre v-else>{{ prettyJson }}</pre>
  </div>
</template>

<script setup>
const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);

const editing = ref(false);
const jsonText = ref(JSON.stringify(props.modelValue, null, 2));

const prettyJson = computed(() =>
  JSON.stringify(props.modelValue, null, 2)
);

function save() {
  try {
    const parsed = JSON.parse(jsonText.value);
    emit('update:modelValue', parsed);
    editing.value = false;
  } catch (e) {
    alert('Invalid JSON');
  }
}

function toggle() {
  editing.value = !editing.value;
}
</script>
```

---

## Patrón: Cuándo Usar FormSchema vs Custom Controller

### ✅ USA FORMSCHEMA SI:

```
✓ CRUD simple (Create, Read, Update, Delete)
✓ Búsqueda básica + filtros
✓ Relaciones simples (BelongsTo)
✓ Validaciones estándar
✓ Tabla con formulario modal
✓ Exportar a CSV
✓ Paginación estándar
```

### ❌ CUSTOM CONTROLLER SI:

```
✗ Lógica de negocio compleja
✗ Múltiples pasos en transacción
✗ Relaciones dinámicas complejas
✗ Validaciones interdependientes
✗ Flujo con estados (workflow)
✗ Cálculos complejos previos
✗ Integración con sistemas externos
```

---

## Conclusión: Poder Práctico

FormSchema Pattern brilla en 80% de casos CRUD típicos:

- ✅ Implementar rápido
- ✅ Mantener fácil
- ✅ Escalar sin problemas
- ✅ Código predecible

Los casos complejos tienen sus propios controllers, sin conflicto.

---

**Próximo capítulo:** [09_ANTI_PATRONES_LIMITACIONES.md](09_ANTI_PATRONES_LIMITACIONES.md)

¿Cuáles son los gotchas y limitaciones?
