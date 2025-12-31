# Capítulo 5: form-schema-complete.php - Motor de Generación de Rutas

**Duración de lectura:** 20 minutos  
**Nivel:** Intermedio-Avanzado  
**Conceptos clave:** Meta-programación, Convención sobre configuración, DRY

---

## Introducción: El Generador Automático

Si el `FormSchemaController` es dinámico en **tiempo de ejecución**, `form-schema-complete.php` es dinámico en **tiempo de registración**.

```
                    Tradicional
                    
Route::get('/api/people', [FormSchemaController::class, 'index']);
Route::post('/api/people', [FormSchemaController::class, 'store']);
Route::get('/api/people/{id}', [FormSchemaController::class, 'show']);
Route::put('/api/people/{id}', [FormSchemaController::class, 'update']);
Route::patch('/api/people/{id}', [FormSchemaController::class, 'update']);
Route::delete('/api/people/{id}', [FormSchemaController::class, 'destroy']);
Route::post('/api/people/search', [FormSchemaController::class, 'search']);

Route::get('/api/certification', [FormSchemaController::class, 'index']);
Route::post('/api/certification', [FormSchemaController::class, 'store']);
... (7 más por modelo)

                    Con FormSchema
                    
$formSchemaModels = [
    'People' => 'people',
    'Certification' => 'certifications',
];

// ✅ Generar automáticamente 8 rutas × 2 modelos = 16 rutas
```

---

## 1. Estructura Básica

### form-schema-complete.php

```php
<?php

/**
 * RUTAS GENERADAS DINÁMICAMENTE
 * 
 * Este archivo contiene la máquina que genera rutas CRUD
 * para múltiples modelos.
 * 
 * Principio: DRY - Define una vez, générate múltiples veces
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormSchemaController;

/**
 * CONFIGURACIÓN: Mapeo de modelos a rutas
 * 
 * Formato:
 *   'NombreModelo' => 'ruta-plural'
 * 
 * Ejemplos:
 *   'People' => 'people'           → /api/people
 *   'Certification' => 'certifications' → /api/certifications
 *   'Role' => 'roles'              → /api/roles
 * 
 * Convención:
 *   - Clave: Nombre del Modelo Eloquent (singular, Pascal case)
 *   - Valor: Ruta de la API (minúscula, usualmente plural)
 */
$formSchemaModels = [
    'People' => 'people',
    'Certification' => 'certifications',
    'Role' => 'roles',
    'Skill' => 'skills',
    'Department' => 'departments',
];

---

## 2. Integración con Repository Pattern

La arquitectura es:

```
form-schema-complete.php (registra rutas)
    ↓ apunta a
FormSchemaController::index() / store() / etc. (orquestación)
    ↓ delega a
{Model}Repository (lógica de persistencia)
    ↓ usa
{Model} Eloquent (mapeo a BD)
```

**Responsabilidades**:

| Capa | Responsabilidad |
|------|-----------------|
| `form-schema-complete.php` | Registrar rutas dinámicamente |
| `FormSchemaController` | Recibir HTTP, inicializar modelo/repo, retornar respuesta |
| `{Model}Repository` | Ejecutar queries, filtros, validaciones BD-específicas |
| `{Model}` Model | Mapear a tabla, relaciones, mutadores |

**Ventaja**: Cambios en BD (filtros, validaciones, eager loading) van en Repository, **no en form-schema-complete.php ni FormSchemaController**.

---

## 3. Generación de Rutas

/**
 * GENERACIÓN: Loop que crea rutas
 * 
 * Para cada modelo en mapeo:
 *   1. Crear rutas HTTP (GET, POST, PUT, DELETE, etc.)
 *   2. Pasar nombre del modelo al controller
 *   3. Registrar nombreado para referencias
 */
Route::prefix('api')->group(function () use ($formSchemaModels) {
    
    foreach ($formSchemaModels as $modelName => $routeName) {
        
        /**
         * GET /api/[route]
         * 
         * Listar registros con paginación
         * Parámetros: page, per_page, sort, order
         * Respuesta: Array de items + meta de paginación
         */
        Route::get($routeName, function (Request $request) use ($modelName) {
            return (new FormSchemaController())
                ->index($request, $modelName);
        })->name("api.{$routeName}.index");
        
        /**
         * POST /api/[route]
         * 
         * Crear nuevo registro
         * Body: Datos del modelo
         * Respuesta: Item creado con ID
         * Status: 201 Created
         */
        Route::post($routeName, function (Request $request) use ($modelName) {
            return (new FormSchemaController())
                ->store($request, $modelName);
        })->name("api.{$routeName}.store");
        
        /**
         * GET /api/[route]/{id}
         * 
         * Obtener detalle de un registro
         * Incluye: Datos completos + relaciones
         * Respuesta: Item individual
         * Status: 200 OK o 404 Not Found
         */
        Route::get("{$routeName}/{id}", function (Request $request, $id) use ($modelName) {
            return (new FormSchemaController())
                ->show($request, $modelName, $id);
        })->name("api.{$routeName}.show");
        
        /**
         * PUT /api/[route]/{id}
         * 
         * Reemplazar registro completo
         * Body: Todos los campos (completo)
         * Semántica: PUT = remplazo completo
         * Status: 200 OK
         */
        Route::put("{$routeName}/{id}", function (Request $request, $id) use ($modelName) {
            return (new FormSchemaController())
                ->update($request, $modelName, $id);
        })->name("api.{$routeName}.update");
        
        /**
         * PATCH /api/[route]/{id}
         * 
         * Actualizar campos específicos
         * Body: Solo campos a actualizar
         * Semántica: PATCH = actualización parcial
         * Status: 200 OK
         */
        Route::patch("{$routeName}/{id}", function (Request $request, $id) use ($modelName) {
            return (new FormSchemaController())
                ->update($request, $modelName, $id);
        })->name("api.{$routeName}.update");
        
        /**
         * DELETE /api/[route]/{id}
         * 
         * Eliminar un registro
         * Body: Vacío
         * Respuesta: Vacía
         * Status: 204 No Content
         */
        Route::delete("{$routeName}/{id}", function (Request $request, $id) use ($modelName) {
            return (new FormSchemaController())
                ->destroy($request, $modelName, $id);
        })->name("api.{$routeName}.destroy");
        
        /**
         * POST /api/[route]/search
         * 
         * Búsqueda avanzada con filtros
         * Body: { query, filters, sort, order, page }
         * Respuesta: Array de items + meta
         * Status: 200 OK
         */
        Route::post("{$routeName}/search", function (Request $request) use ($modelName) {
            return (new FormSchemaController())
                ->search($request, $modelName);
        })->name("api.{$routeName}.search");
        
        /**
         * POST /api/[route]/search-with-relations
         * 
         * Búsqueda que incluye relaciones (eager loading)
         * Útil para: Dropdown lists, select fields
         * Body: { query, relations: ['skills', 'department'] }
         * Respuesta: Items con relaciones cargadas
         */
        Route::post("{$routeName}/search-with-relations", function (Request $request) use ($modelName) {
            return (new FormSchemaController())
                ->searchWithRelations($request, $modelName);
        })->name("api.{$routeName}.search-with-relations");
    }
    
});

/**
 * RUTAS NO GENERADAS (Endpoints especializados)
 * 
 * Estos NO están en form-schema-complete.php
 * porque requieren lógica peoplealizada:
 */

Route::prefix('api')->group(function () {
    
    // Dashboard - Estadísticas peoplealizadas
    Route::get('dashboard', [DashboardController::class, 'index']);
    
    // GapAnalysis - Cálculo de brechas
    Route::post('gap-analysis', [GapAnalysisController::class, 'calculate']);
    
    // LearningPaths - Rutas de aprendizaje
    Route::get('learning-paths', [LearningPathController::class, 'index']);
    
    // Marketplace - Oportunidades internas
    Route::get('marketplace/opportunities', [MarketplaceController::class, 'opportunities']);
    Route::post('marketplace/apply', [MarketplaceController::class, 'apply']);
});
```

---

## 2. Anatomía de una Ruta Generada

### Desglose Detallado

```php
Route::post('people/search', function (Request $request) use ($modelName) {
    return (new FormSchemaController())
        ->search($request, $modelName);
})->name("api.people.search");

//   │      │        │          │       │         │            └─ Nombre para generar URLs
//   │      │        │          │       │         └─ Closure que ejecuta la lógica
//   │      │        │          │       └─ Modelo 'People' disponible en closure
//   │      │        │          └─ Request inyectada automáticamente
//   │      │        └─ Subruta (dentro de prefix 'api')
//   │      └─ Verbo HTTP
//   └─ Registro de ruta
```

### Cómo se Ejecuta

```
Paso 1: Usuario en frontend
   axios.post('/api/people/search', { query: 'aws' })

Paso 2: Laravel enrutador
   Busca coincidencia en rutas registradas
   Encuentra: Route::post('people/search', ...)
   
Paso 3: Ejecuta closure
   new FormSchemaController()
   ->search($request, 'People')  ← Parámetro modelName

Paso 4: Controller lógica
   initializeForModel('People')
   // Carga App\Models\People
   // Carga PeopleRepository
   
   $results = $this->repository->search('aws')
   
   return response()->json([...])

Paso 5: Respuesta al frontend
   {
     "data": [...],
     "meta": {...}
   }
```

---

## 3. Mapeo de Modelos: Convenciones

### Decisiones de Diseño

```php
// OPCIÓN 1: Singular (actual)
$formSchemaModels = [
    'People' => 'people',           // /api/people
    'Certification' => 'certification',  // /api/certification
];

// OPCIÓN 2: Plural (alternativa)
$formSchemaModels = [
    'People' => 'people',           // /api/people
    'Certification' => 'certifications',  // /api/certifications
];

// OPCIÓN 3: Mixto (inconsistente ❌)
$formSchemaModels = [
    'People' => 'people',           // /api/people
    'Certification' => 'certifications',  // /api/certifications
];
```

**En TalentIA actual:** Mixto (INCONSISTENCIA antes de consolidación)

```php
// ANTES (Problema)
'People' => 'people',           // singular
'Certification' => 'certifications',  // plural
'Role' => 'role',               // singular ❌
'Skill' => 'skills',            // plural

// DESPUÉS (Consolidado)
'People' => 'people',           // singular, pero consistente
'Certification' => 'certifications',  // plural, pero consistente
'Role' => 'roles',              // plural ✅
'Skill' => 'skills',            // plural ✅
```

**Recomendación:** Elegir UNA convención y mantener:

```php
// OPCIÓN A: Plural (REST puro)
$formSchemaModels = [
    'People' => 'people',
    'Certification' => 'certifications',
    'Role' => 'roles',
    'Skill' => 'skills',
];
// Ventaja: Semánticamente correcto
// Desventaja: Los plurales en inglés son irregulares

// OPCIÓN B: Singular (más simple)
$formSchemaModels = [
    'People' => 'people',
    'Certification' => 'certification',
    'Role' => 'role',
    'Skill' => 'skill',
];
// Ventaja: Predecible (Modelo → ruta sin transformación)
// Desventaja: Poco convencional para APIs REST
```

---

## 4. Integración con web.php

### Diferencia Crítica

```php
// routes/web.php (Inertia.js, página completa)
Route::get('/people', [PeopleController::class, 'index'])->name('people.index');
Route::get('/people/{id}', [PeopleController::class, 'show'])->name('people.show');

// routes/form-schema-complete.php (API endpoints)
Route::get('api/people', [FormSchemaController::class, 'index']);
Route::post('api/people/search', [FormSchemaController::class, 'search']);

// routes/api.php (Endpoints especiales, sin CRUD)
Route::get('api/dashboard', [DashboardController::class, 'index']);
```

### Flujo Combinado

```
Usuario navegación
    ├─ GET /people
    │  ▼
    │  Route en web.php
    │  ▼
    │  PeopleController::index()
    │  ▼
    │  Renderiza Inertia.js → Vue → PeopleIndex.vue
    │  ▼
    │  Carga en navegador
    │
    └─ Usuario busca en tabla
       ▼
       FormSchema.vue llama
       ▼
       axios.post('/api/people/search', {...})
       ▼
       Route en form-schema-complete.php
       ▼
       FormSchemaController::search('People')
       ▼
       Retorna JSON
       ▼
       FormSchema.vue re-renderiza tabla
```

---

## 5. Casos Especiales y Extensiones

### Agregar Rutas Peoplealizadas

```php
// form-schema-complete.php

// ... mapeo y loop de generación ...

// DESPUÉS del loop: Rutas peoplealizadas

Route::prefix('api')->group(function () {
    
    // Endpoint específico para People: Asignar habilidades
    Route::post('people/{id}/skills', function (Request $request, $id) {
        $people = People::findOrFail($id);
        $people->skills()->sync($request->get('skill_ids'));
        return response()->json(['message' => 'Skills assigned']);
    })->name('api.people.assign-skills');
    
    // Endpoint específico para Certification: Renovar
    Route::post('certifications/{id}/renew', function (Request $request, $id) {
        $cert = Certification::findOrFail($id);
        $cert->update(['expires_at' => $request->get('new_expiry')]);
        return response()->json($cert);
    })->name('api.certifications.renew');
    
});
```

### Proteger Rutas con Middleware

```php
// form-schema-complete.php

Route::prefix('api')
    ->middleware(['auth', 'verified'])  // ← Añadir middleware
    ->group(function () use ($formSchemaModels) {
        
        foreach ($formSchemaModels as $modelName => $routeName) {
            // ... rutas ...
        }
        
    });

// O middleware selectivo por modelo:
Route::prefix('api')->group(function () use ($formSchemaModels) {
    
    foreach ($formSchemaModels as $modelName => $routeName) {
        
        // Lectura: sin auth
        Route::get($routeName, [FormSchemaController::class, 'index']);
        
        // Escritura: requiere auth
        Route::post($routeName, [FormSchemaController::class, 'store'])
            ->middleware('auth');
        
        Route::put("{$routeName}/{id}", [FormSchemaController::class, 'update'])
            ->middleware('auth');
        
        Route::delete("{$routeName}/{id}", [FormSchemaController::class, 'destroy'])
            ->middleware('auth');
    }
    
});
```

---

## 6. Debugging y Verificación

### Ver Rutas Registradas

```bash
# Listar todas las rutas
php artisan route:list

# Salida esperada:
# GET|HEAD   /api/people ........................ api.people.index
# POST       /api/people ........................ api.people.store
# GET|HEAD   /api/people/{id} .................. api.people.show
# PUT        /api/people/{id} .................. api.people.update
# PATCH      /api/people/{id} .................. api.people.update
# DELETE     /api/people/{id} .................. api.people.destroy
# POST       /api/people/search ................ api.people.search
#
# POST       /api/certifications .............. api.certifications.store
# GET|HEAD   /api/certifications .............. api.certifications.index
# ...

# Filtrar por modelo específico
php artisan route:list | grep people

# Filtrar por método HTTP
php artisan route:list --method=POST
```

### Verificar Named Routes

```php
// En controller o test:

// Generar URL completa
route('api.people.index')     // /api/people
route('api.people.show', ['id' => 42])  // /api/people/42
route('api.people.search')    // /api/people/search

// En Vue (si inyectado):
{{ route('api.people.store') }}  // /api/people
```

### Comprobar Duplicados

```bash
# Verificar que NO hay rutas duplicadas
php artisan route:list | sort | uniq -d

# Debe retornar VACÍO (no hay duplicados)
```

---

## 7. Evolución: De Manual a Automático

### Fase 1: Totalmente Manual (Problema)

```php
// routes/api.php - 100 líneas de rutas duplicadas

Route::get('people', [FormSchemaController::class, 'index']);
Route::post('people', [FormSchemaController::class, 'store']);
Route::get('people/{id}', [FormSchemaController::class, 'show']);
// ... 5 rutas más ...

Route::get('certifications', [FormSchemaController::class, 'index']);
Route::post('certifications', [FormSchemaController::class, 'store']);
// ... 5 rutas más ...

// Problema: Cambiar método de show() requiere actualizar 10 rutas
```

### Fase 2: Parcialmente Automático (Transición)

```php
// routes/form-schema-complete.php - Generador básico
foreach ($models as $model) {
    Route::get($model, 'FormSchemaController@index');
    // ... etc ...
}

// routes/api.php - Endpoints especiales
Route::post('dashboard', 'DashboardController@index');
```

### Fase 3: Completamente Automático (Actual)

```php
// form-schema-complete.php - Generador robusto
Route::prefix('api')->group(function () use ($formSchemaModels) {
    foreach ($formSchemaModels as $modelName => $routeName) {
        // 8 rutas por modelo, automáticamente
    }
});

// api.php - Solo endpoints especiales (no CRUD)
Route::get('dashboard', ...);
Route::post('gap-analysis', ...);

// ✅ DRY garantizado: cambio en generator beneficia a TODO
```

---

## Conclusión: Un Archivo, Infinitos Modelos

`form-schema-complete.php` encarna:

- ✅ **DRY:** Define rutas una sola vez
- ✅ **Escalabilidad:** Agregar modelo = 1 línea
- ✅ **Consistencia:** Todos los modelos tienen mismo API
- ✅ **Mantenibilidad:** Cambio central beneficia todo
- ✅ **Convención:** Mapeo visible, modificable

Una verdadera máquina de código.

---

**Próximo capítulo:** [06_FORMSCHEMA_VUE.md](06_FORMSCHEMA_VUE.md)

¿Cómo renderiza FormSchema.vue cualquier modelo dinámicamente?
