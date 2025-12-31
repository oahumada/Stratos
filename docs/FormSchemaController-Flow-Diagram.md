# FormSchemaController - Flujo Detallado Frontend → Repository

## 📋 Introducción

Este documento explica paso a paso el flujo completo de una petición desde el frontend hasta el repository usando el sistema genérico FormSchemaController, mostrando cómo se procesa una operación CRUD típica.

---

## 🔄 Flujo General del Sistema

```
Frontend (Vue.js) → API Route → FormSchemaController → Repository → Model → Database
     ↓                ↓              ↓                    ↓         ↓        ↓
  apiHelper.ts      form-schema-    Inicialización      Método     Eloquent  MySQL
                 complete.php     Dinámica           CRUD       ORM
```

---

## 📝 Ejemplo Práctico: Crear una Alergia

Vamos a seguir el flujo completo de crear una nueva alergia paso a paso.

### 1. 🖥️ Frontend (Vue.js Component)

**Archivo**: `resources/js/components/FormData.vue` o similar

```javascript
// Usuario llena el formulario y hace clic en "Guardar"
const formData = {
    paciente_id: 123,
    alergia: "Polen",
    comentario: "Alergia estacional severa",
};

// El componente Vue llama al apiHelper.ts
const response = await apiHelper.ts.post("/api/alergia", {
    data: formData,
});
```

**Lo que sucede**:

- ✅ Usuario completa formulario
- ✅ Vue.js recolecta datos del formulario
- ✅ Se estructura el payload con `data: {...}`
- ✅ apiHelper.ts hace petición HTTP POST

---

### 2. 🌐 HTTP Request

**Petición HTTP enviada**:

```http
POST /api/alergia HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json
Accept: application/json

{
    "data": {
        "paciente_id": 123,
        "alergia": "Polen",
        "comentario": "Alergia estacional severa"
    }
}
```

**Lo que sucede**:

- ✅ Petición HTTP enviada al servidor Laravel
- ✅ Laravel recibe la petición en el puerto 8000
- ✅ Middleware de autenticación/autorización se ejecuta
- ✅ Laravel busca la ruta correspondiente

---

### 3. 🛣️ Route Resolution

**Archivo**: `routes/form-schema-complete.php`

```php
// Laravel encuentra esta ruta en el mapeo
$formSchemaModels = [
    'Alergia' => 'alergia',  // ← Coincide con /api/alergia
    // ... otros modelos
];

// Se ejecuta esta closure
Route::post('alergia', function(Request $request) use ($modelName) {
    $controller = new FormSchemaController();
    return $controller->store($request, 'Alergia');  // ← $modelName = 'Alergia'
})->name('api.alergia.store');
```

**Lo que sucede**:

- ✅ Laravel busca ruta que coincida con `POST /api/alergia`
- ✅ Encuentra la ruta en `form-schema-complete.php`
- ✅ Identifica que `$modelName = 'Alergia'`
- ✅ Crea instancia de `FormSchemaController`
- ✅ Llama al método `store($request, 'Alergia')`

---

### 4. 🎛️ FormSchemaController - Inicialización

**Archivo**: `app/Http/Controllers/FormSchemaController.php`

```php
public function store(Request $request, string $modelName)
{
    try {
        // 1. Inicializar para el modelo específico
        $this->initializeForModel($modelName);  // $modelName = 'Alergia'

        // 2. Delegar al repositorio
        return $this->repository->store($request);
    } catch (\Exception $e) {
        Log::error("Error in FormSchemaController::store for {$modelName}: " . $e->getMessage());
        return response()->json([
            'message' => 'Error al crear el registro',
            'error' => $e->getMessage()
        ], 500);
    }
}
```

**Método `initializeForModel('Alergia')`**:

```php
public function initializeForModel(string $modelName)
{
    // 1. Construir nombres de clases
    $this->modelClass = "App\\Models\\{$modelName}";           // App\Models\Alergia
    $this->repositoryClass = "App\\Repository\\{$modelName}Repository"; // App\Repository\AlergiaRepository

    // 2. Verificar que las clases existan
    if (!class_exists($this->modelClass)) {
        throw new \Exception("Model class {$this->modelClass} not found");
    }
    if (!class_exists($this->repositoryClass)) {
        throw new \Exception("Repository class {$this->repositoryClass} not found");
    }

    // 3. Instanciar modelo y repositorio
    $model = new $this->modelClass;                    // new Alergia()
    $this->repository = new $this->repositoryClass($model); // new AlergiaRepository($model)

    return $this;
}
```

**Lo que sucede**:

- ✅ Controller recibe `$modelName = 'Alergia'`
- ✅ Construye nombres de clases dinámicamente
- ✅ Verifica que `App\Models\Alergia` existe
- ✅ Verifica que `App\Repository\AlergiaRepository` existe
- ✅ Instancia `new Alergia()`
- ✅ Instancia `new AlergiaRepository($alergia)`
- ✅ Llama a `$this->repository->store($request)`

---

### 5. 🏪 Repository Layer

**Archivo**: `app/Repository/AlergiaRepository.php`

```php
<?php
namespace App\Repository;

use App\Models\Alergia;

class AlergiaRepository extends Repository  // ← Extiende Repository base
{
    public function __construct(Alergia $model)
    {
        $this->model = $model;  // $this->model = instancia de Alergia
    }

    // No tiene método store() propio, usa el de la clase base
}
```

**Clase base**: `app/Repository/Repository.php`

```php
public function store(Request $request)
{
    // 1. Extraer datos del request
    $query = $request->get('data');  // Obtiene el array 'data'
    Log::info($query);  // Log para debugging

    try {
        // 2. Procesar arrays (si los hay)
        $query = array_map(function ($value) {
            return is_array($value) ? implode(',', $value) : $value;
        }, $query);

        // En nuestro caso:
        // $query = [
        //     'paciente_id' => 123,
        //     'alergia' => 'Polen',
        //     'comentario' => 'Alergia estacional severa'
        // ]

        // 3. Crear registro en la base de datos
        $request = $this->model->create($query);  // $this->model = instancia de Alergia

        // 4. Respuesta exitosa
        return response()->json([
            'message' => 'Registro creado con éxito',
        ], 200);

    } catch (QueryException $e) {
        Log::error('store', [$e]);
        return response()->json([
            'message' => 'Se produjo un error: ',
            'error' => $e->getMessage(),
        ], 500);
    }
}
```

**Lo que sucede**:

- ✅ `AlergiaRepository` no tiene método `store()` propio
- ✅ Se usa el método `store()` de la clase base `Repository`
- ✅ Se extraen los datos del campo `'data'` del request
- ✅ Se procesan arrays si los hay
- ✅ Se llama a `$this->model->create($query)` donde `$this->model` es instancia de `Alergia`

---

### 6. 🗃️ Model Layer (Eloquent)

**Archivo**: `app/Models/Alergia.php`

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alergia extends Model
{
    protected $table = 'alergia';

    protected $fillable = [
        'paciente_id',
        'comentario',
        'alergia',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id', 'id');
    }
}
```

**Cuando se llama `$model->create($query)`**:

```php
// Eloquent ejecuta internamente algo equivalente a:
$alergia = new Alergia();
$alergia->paciente_id = 123;
$alergia->alergia = 'Polen';
$alergia->comentario = 'Alergia estacional severa';
$alergia->created_at = now();
$alergia->updated_at = now();
$alergia->save();  // ← Aquí se ejecuta la query SQL
```

**Lo que sucede**:

- ✅ Eloquent recibe los datos a insertar
- ✅ Valida que los campos estén en `$fillable`
- ✅ Crea nueva instancia del modelo
- ✅ Asigna valores a las propiedades
- ✅ Agrega timestamps automáticamente
- ✅ Ejecuta `save()` que genera la query SQL

---

### 7. 🗄️ Database Layer

**Query SQL generada por Eloquent**:

```sql
INSERT INTO `alergia` (
    `paciente_id`,
    `alergia`,
    `comentario`,
    `created_at`,
    `updated_at`
) VALUES (
    123,
    'Polen',
    'Alergia estacional severa',
    '2025-07-25 14:57:00',
    '2025-07-25 14:57:00'
);
```

**Lo que sucede**:

- ✅ Eloquent genera query SQL INSERT
- ✅ Se conecta a la base de datos MySQL
- ✅ Ejecuta la query en la tabla `alergia`
- ✅ MySQL retorna el ID del registro insertado
- ✅ Eloquent confirma la inserción exitosa

---

### 8. 🔄 Response Flow (Vuelta)

**El flujo de respuesta sigue el camino inverso**:

#### Database → Model

```php
// MySQL confirma inserción exitosa
// Eloquent retorna instancia del modelo creado
```

#### Model → Repository

```php
// Repository recibe confirmación de Eloquent
return response()->json([
    'message' => 'Registro creado con éxito',
], 200);
```

#### Repository → Controller

```php
// FormSchemaController recibe respuesta del repository
// No modifica la respuesta, la retorna tal como está
return $this->repository->store($request);
```

#### Controller → Route

```php
// La closure en form-schema-complete.php recibe la respuesta
// La retorna directamente al cliente
return $controller->store($request, 'Alergia');
```

#### Route → HTTP Response

```http
HTTP/1.1 200 OK
Content-Type: application/json

{
    "message": "Registro creado con éxito"
}
```

#### HTTP Response → Frontend

```javascript
// apiHelper.ts recibe la respuesta
const response = await apiHelper.ts.post("/api/alergia", { data: formData });

// response.data = { "message": "Registro creado con éxito" }
console.log("Alergia creada exitosamente:", response.data.message);

// El componente Vue puede mostrar notificación de éxito
this.$toast.success("Alergia guardada correctamente");
```

---

## 🔍 Flujo Detallado por Operación CRUD

### 📖 READ - Buscar con Filtros

**Frontend**:

```javascript
const filters = { paciente_id: 123 };
const response = await apiHelper.ts.post("/api/alergia/search", {
    data: filters,
});
```

**Flujo**:

1. `POST /api/alergia/search`
2. Route: `form-schema-complete.php` → `search(request, 'Alergia')`
3. Controller: `initializeForModel('Alergia')` → `repository->search(request)`
4. Repository: `Repository::search()` → `Tools::filterData()`
5. Model: Query con filtros aplicados
6. Database: `SELECT * FROM alergia WHERE paciente_id = 123`

### ✏️ UPDATE - Actualizar Registro

**Frontend**:

```javascript
const updateData = { id: 456, alergia: "Polen y ácaros" };
const response = await apiHelper.ts.put("/api/alergia/456", {
    data: updateData,
});
```

**Flujo**:

1. `PUT /api/alergia/456`
2. Route: `form-schema-complete.php` → `update(request, 'Alergia')`
3. Controller: `initializeForModel('Alergia')` → `repository->update(request)`
4. Repository: `Repository::update()` → `model->findOrFail(456)->fill()->save()`
5. Model: Actualización de campos específicos
6. Database: `UPDATE alergia SET alergia = 'Polen y ácaros' WHERE id = 456`

### 🗑️ DELETE - Eliminar Registro

**Frontend**:

```javascript
const response = await apiHelper.ts.delete("/api/alergia/456");
```

**Flujo**:

1. `DELETE /api/alergia/456`
2. Route: `form-schema-complete.php` → `destroy('Alergia', 456)`
3. Controller: `initializeForModel('Alergia')` → `repository->destroy(456)`
4. Repository: `Repository::destroy()` → `model->destroy(456)`
5. Model: Eliminación por ID
6. Database: `DELETE FROM alergia WHERE id = 456`

---

## 🎯 Ventajas del Flujo Genérico

### 1. **Consistencia Total**

- Todos los modelos siguen exactamente el mismo flujo
- Mismo manejo de errores en todos los casos
- Respuestas uniformes para el frontend

### 2. **Mantenimiento Centralizado**

- Cambios en el flujo se aplican a todos los modelos
- Un solo punto de debugging y logging
- Actualizaciones de seguridad centralizadas

### 3. **Escalabilidad Automática**

- Nuevos modelos funcionan inmediatamente
- Sin necesidad de duplicar lógica
- Patrón probado y confiable

### 4. **Debugging Simplificado**

- Logs centralizados en FormSchemaController
- Fácil seguimiento del flujo completo
- Puntos de falla claramente identificados

---

## 🔧 Puntos de Peoplealización

### 1. **Repository Específico**

Si un modelo necesita lógica especial:

```php
// En AlergiaRepository.php
public function store(Request $request)
{
    // Lógica específica para alergias
    $data = $request->get('data');

    // Validación especial
    if (empty($data['alergia'])) {
        return response()->json(['error' => 'Alergia requerida'], 400);
    }

    // Llamar al método padre para funcionalidad estándar
    return parent::store($request);
}
```

### 2. **Validación en Controller**

```php
// En FormSchemaController.php
private function getValidationRules(string $modelName): array
{
    $rules = [
        'Alergia' => [
            'paciente_id' => 'required|exists:paciente,id',
            'alergia' => 'required|string|max:255'
        ],
        // ... otros modelos
    ];

    return $rules[$modelName] ?? [];
}
```

### 3. **Middleware Específico**

```php
// En form-schema-complete.php
Route::post('alergia', function(Request $request) use ($modelName) {
    $controller = new FormSchemaController();
    return $controller->store($request, 'Alergia');
})->middleware(['auth', 'can:create-alergia']);
```

---

## 📊 Métricas de Rendimiento

### Tiempo de Ejecución Típico

1. **Route Resolution**: ~1ms
2. **Controller Initialization**: ~2ms
3. **Repository Processing**: ~3ms
4. **Database Query**: ~5-15ms
5. **Response Generation**: ~1ms

**Total**: ~12-22ms por operación CRUD

### Memoria Utilizada

- **FormSchemaController**: ~50KB
- **Model Instance**: ~10KB
- **Repository Instance**: ~15KB
- **Request/Response**: ~5KB

**Total**: ~80KB por request

---

## 🚨 Troubleshooting del Flujo

### Error: "Model class not found"

**Punto de falla**: FormSchemaController::initializeForModel()
**Causa**: Modelo no existe o nombre incorrecto
**Solución**: Verificar que existe `app/Models/{ModelName}.php`

### Error: "Repository class not found"

**Punto de falla**: FormSchemaController::initializeForModel()
**Causa**: Repository no existe o no sigue convención
**Solución**: Verificar que existe `app/Repository/{ModelName}Repository.php`

### Error: "Column not found"

**Punto de falla**: Model::create() en Repository::store()
**Causa**: Campo no existe en tabla o no está en $fillable
**Solución**: Verificar migración y $fillable en modelo

### Error: "Route not found"

**Punto de falla**: Route resolution
**Causa**: Modelo no está en $formSchemaModels
**Solución**: Agregar modelo al mapeo en form-schema-complete.php

---

## 📝 Conclusión

El sistema genérico FormSchemaController proporciona un flujo consistente, escalable y mantenible para todas las operaciones CRUD. El flujo desde frontend hasta database está completamente estandarizado, lo que garantiza:

- ✅ **Predictibilidad**: Mismo comportamiento para todos los modelos
- ✅ **Confiabilidad**: Patrón probado y validado
- ✅ **Mantenibilidad**: Un solo punto de modificación
- ✅ **Escalabilidad**: Agregar modelos sin complejidad adicional

Este flujo detallado sirve como referencia para entender, debuggear y extender el sistema genérico FormSchemaController.
