# Capítulo 4: FormSchemaController - Orquestación Dinámica

**Duración de lectura:** 30 minutos  
**Nivel:** Avanzado  
**Conceptos clave:** Reflexión, Strategy Pattern, Inyección de dependencias

---

## Introducción: El Corazón del Sistema

El `FormSchemaController` es el componente más sofisticado de FormSchema Pattern. Mientras que los componentes Vue son reutilizables pero estáticos, el controller es **dinámicamente adaptable** a cualquier modelo sin modificación de código.

```php
// Un ÚNICO controller para MÚLTIPLES modelos:

FormSchemaController::index(People)        → Retorna Peopleas
FormSchemaController::index(Certification) → Retorna Certificaciones
FormSchemaController::index(Role)          → Retorna Roles
FormSchemaController::index(Skill)         → Retorna Habilidades

// Mismo código, diferentes resultados
```

---

## 1. Estructura del Controller

### Clase Base

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Repository\{GenericRepository, RepositoryInterface};
use App\Validators\FormValidator;

class FormSchemaController extends Controller
{
    // Estado interno (dinámico por solicitud)
    private ?string $modelClass = null;
    private ?RepositoryInterface $repository = null;
    private ?FormValidator $validator = null;
    
    // Configuración
    private const DEFAULT_PAGE_SIZE = 15;
    private const MAX_PAGE_SIZE = 100;
    
    /**
     * MÉTODO CLAVE: Inicializa el controller para un modelo específico
     * 
     * Responsabilidad: Convertir nombre del modelo en instancias funcionales
     * Patrón: Strategy Pattern + Reflexión
     */
    private function initializeForModel(string $modelName): void
    {
        // 1. Resolver clase del modelo
        $this->modelClass = $this->resolveModelClass($modelName);
        
        // 2. Resolver repositorio
        $this->repository = $this->resolveRepository($modelName);
        
        // 3. Resolver validador
        $this->validator = new FormValidator($modelName);
    }
    
    /**
     * Reflexión: Convierte string a clase
     * 
     * Entrada: "People"
     * Proceso: 
     *   1. Agrega namespace: "App\Models\People"
     *   2. Verifica que clase existe
     *   3. Verifica que hereda de Model
     * Salida: App\Models\People (clase)
     */
    private function resolveModelClass(string $modelName): string
    {
        // Validar nombre: solo alphanumèricos
        if (!preg_match('/^[A-Za-z0-9_]+$/', $modelName)) {
            throw new \InvalidArgumentException(
                "Invalid model name: {$modelName}"
            );
        }
        
        // Construir nombre de clase
        $class = "App\\Models\\" . $modelName;
        
        // Verificar que existe
        if (!class_exists($class)) {
            throw new \RuntimeException(
                "Model not found: {$class}"
            );
        }
        
        // Verificar que es Eloquent Model
        if (!is_subclass_of($class, Model::class)) {
            throw new \RuntimeException(
                "{$class} must be Eloquent Model"
            );
        }
        
        return $class;
    }
    
    /**
     * Strategy Pattern: Selecciona repository más específico disponible
     * 
     * Estrategia 1: Repositorio específico del modelo
     *   ├─ App\Repository\PeopleRepository
     *   └─ Si existe, usar
     * 
     * Estrategia 2: Repositorio genérico
     *   ├─ App\Repository\GenericRepository
     *   └─ Si no existe específico, usar este
     */
    private function resolveRepository(
        string $modelName
    ): RepositoryInterface {
        
        // Intentar repositorio específico
        $specificClass = "App\\Repository\\" . $modelName . "Repository";
        
        if (class_exists($specificClass)) {
            $model = app($this->modelClass);
            return new $specificClass($model);
        }
        
        // Fallback a genérico
        $model = app($this->modelClass);
        return new GenericRepository($model);
    }
}
```

---

## 2. Repository Pattern: La Capa de Persistencia

### 2.1 ¿Por qué Repository Pattern?

FormSchemaController **NO contiene lógica de BD**. En su lugar, delega en **Repository Pattern**:

```php
// ❌ INCORRECTO: Lógica BD en Controller
public function index($modelName) {
    return $modelName::with('relations')->get(); // ← BD en Controller
}

// ✅ CORRECTO: Delegado a Repository
public function index($modelName) {
    $this->initializeForModel($modelName);
    return $this->repository->index(); // ← Lógica centralizada
}
```

**Beneficios:**
- Controller enfocado en **orquestación HTTP**, no en SQL
- Repository enfocado en **persistencia de datos**
- Testeable: mock Repository sin tocar BD
- Extensible: override métodos por modelo sin afectar otros

### 2.2 Arquitectura en Capas con Repository

```
┌────────────────────────────────────────────────────────────┐
│  HTTP Requests (form-schema-complete.php)                 │
│  GET /api/people → FormSchemaController@index('People')   │
└─────────────────────┬──────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────┐
│  FormSchemaController (Orquestación)                       │
│  ├─ Inicializa modelo                                     │
│  ├─ Crea repositorio dinámicamente                        │
│  └─ Delega lógica a repository                            │
│                                                            │
│  public function index(Request $req, string $modelName) {  │
│      $this->initializeForModel($modelName);               │
│      return $this->repository->index($req);               │
│  }                                                         │
└─────────────────────┬──────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────┐
│  {Model}Repository (Lógica de Persistencia)                │
│  ├─ PeopleRepository extends Repository                   │
│  ├─ RoleRepository extends Repository                     │
│  └─ SkillRepository extends Repository                    │
│                                                            │
│  Hereda métodos CRUD genéricos:                           │
│  ├─ store(Request $request)                              │
│  ├─ update(Request $request)                             │
│  ├─ destroy($id)                                         │
│  ├─ show(Request $request, $id)                          │
│  └─ search(Request $request)                             │
│                                                            │
│  Puede overridear para lógica custom:                     │
│  public function search($request) { ... }                │
└─────────────────────┬──────────────────────────────────────┘
                      │
┌─────────────────────▼──────────────────────────────────────┐
│  {Model} Eloquent (Mapeo a BD)                             │
│  ├─ People                                                │
│  ├─ Role                                                  │
│  └─ Skill                                                 │
│                                                            │
│  SELECT * FROM people WHERE ...                           │
└────────────────────────────────────────────────────────────┘
```

### 2.3 Polimorfismo Dinámico en Acción

La magia está en `initializeForModel()`:

```php
private function initializeForModel(string $modelName): void
{
    // Convierte string a clase dinámicamente
    $this->modelClass = "App\\Models\\{$modelName}";
    $this->repositoryClass = "App\\Repository\\{$modelName}Repository";
    
    // Ejemplos en runtime:
    // 'People'    → App\Models\People, App\Repository\PeopleRepository
    // 'Role'      → App\Models\Role, App\Repository\RoleRepository
    // 'Skill'     → App\Models\Skill, App\Repository\SkillRepository
    
    // Instancia el repositorio dinámicamente
    $model = app($this->modelClass);
    $this->repository = new $this->repositoryClass($model);
}
```

**FormSchemaController NO CONOCE** el tipo de modelo en tiempo de compilación. Pero funciona porque:

1. Cada `{Model}Repository` implementa `RepositoryInterface`
2. El controller llama métodos definidos en la interfaz
3. Laravel resuelve la clase dinámica en runtime

### 2.4 Strategy Pattern: Repository Base + Especializaciones

```php
// Repository.php (Base - CRUD genérico)
abstract class Repository implements RepositoryInterface {
    protected $model;
    
    public function store(Request $request) { ... }
    public function update(Request $request) { ... }
    public function destroy($id) { ... }
    public function show(Request $request, $id) { ... }
    public function search(Request $request) { ... }
}

// PeopleRepository.php (Especializado)
class PeopleRepository extends Repository {
    public function __construct() {
        parent::__construct(new People());
    }
    
    // Hereda 5 métodos genéricos
    // Puede overridear si necesita lógica custom
    
    // Ejemplo: Search custom solo para People
    public function search(Request $request) {
        $filters = $request->input('data', []);
        $query = $this->model
            ->with('skills', 'roles')  // Eager loading
            ->where('status', 'active');
        return Tools::filterData($filters, $query);
    }
}
```

### 2.5 Flujo Completo: GET /api/people

```
1️⃣  HTTP Request
    GET /api/people?page=1&per_page=15

2️⃣  form-schema-complete.php resuelve ruta
    Route::get('/people', [FormSchemaController::class, 'index']);
    
3️⃣  FormSchemaController::index('People')
    └─ initializeForModel('People')
       ├─ $this->modelClass = "App\Models\People"
       ├─ $this->repositoryClass = "App\Repository\PeopleRepository"
       └─ $this->repository = new PeopleRepository(new People())

4️⃣  FormSchemaController delega a repository
    return $this->repository->index($request);

5️⃣  PeopleRepository::index() (heredado de Repository base)
    └─ $this->model->query()->select("*")->paginate(15)
       └─ SELECT * FROM people LIMIT 15;

6️⃣  Eloquent ejecuta SQL y retorna Collection

7️⃣  Repository retorna JSON
    {
      "data": [...],
      "meta": { "page": 1, "total": 150, ... }
    }
```

### 2.6 Extensibilidad: Agregar Lógica Custom por Modelo

Sin modificar FormSchemaController, puedes personalizar cualquier modelo:

```php
// Caso 1: Search custom para People
class PeopleRepository extends Repository {
    public function search(Request $request) {
        // Búsqueda especial: eager load relaciones
        $filters = $request->input('data', []);
        $query = $this->model->with('skills', 'currentRole');
        return Tools::filterData($filters, $query);
    }
}

// Caso 2: Store custom para Role (validaciones específicas)
class RoleRepository extends Repository {
    public function store(Request $request) {
        // Validar permisos antes de crear
        if (!auth()->user()->can('create_role')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return parent::store($request);
    }
}

// Caso 3: Destroy custom para Skill (soft delete)
class SkillRepository extends Repository {
    public function destroy($id) {
        $skill = $this->model->findOrFail($id);
        $skill->update(['deleted_at' => now()]); // Soft delete
        return response()->json(['message' => 'Soft deleted']);
    }
}
```

---

## 3. Operaciones CRUD

### GET - Listar (Index)

```php
/**
 * GET /api/[model]
 * 
 * Parámetros query:
 *   - page: número de página (default: 1)
 *   - per_page: registros por página (default: 15, max: 100)
 *   - sort: campo para ordenar
 *   - order: asc|desc
 * 
 * Retorna:
 *   {
 *     "data": [...],
 *     "meta": {
 *       "current_page": 1,
 *       "last_page": 5,
 *       "total": 72,
 *       "per_page": 15
 *     }
 *   }
 */
public function index(Request $request, string $modelName)
{
    try {
        // 1. INICIALIZAR para el modelo
        $this->initializeForModel($modelName);
        
        // 2. VALIDAR parámetros de paginación
        $pageSize = min(
            (int) $request->get('per_page', self::DEFAULT_PAGE_SIZE),
            self::MAX_PAGE_SIZE
        );
        
        // 3. RECUPERAR datos con filtros opcionales
        $items = $this->repository->paginate(
            page: $request->get('page', 1),
            perPage: $pageSize,
            filters: $request->all()
        );
        
        // 4. RETORNAR respuesta
        return response()->json([
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'total' => $items->total(),
                'per_page' => $pageSize,
            ],
            'message' => 'Records fetched successfully'
        ]);
        
    } catch (\Exception $e) {
        return $this->errorResponse(
            $e->getMessage(),
            400
        );
    }
}
```

### POST - Crear (Store)

```php
/**
 * POST /api/[model]
 * 
 * Body:
 *   {
 *     "name": "John Doe",
 *     "email": "john@example.com",
 *     ...
 *   }
 * 
 * Respuesta (201):
 *   {
 *     "id": 42,
 *     "name": "John Doe",
 *     "email": "john@example.com",
 *     ...
 *   }
 */
public function store(Request $request, string $modelName)
{
    try {
        // 1. INICIALIZAR
        $this->initializeForModel($modelName);
        
        // 2. VALIDAR datos
        $validated = $this->validator->validate(
            $request->all(),
            'create'  // reglas específicas para creación
        );
        
        // 3. CREAR registro
        $item = $this->repository->create($validated);
        
        // 4. RETORNAR
        return response()->json(
            $item->fresh(),
            201,
            ['Location' => route('api.show', $item->id)]
        );
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return $this->errorResponse(
            'Validation failed',
            422,
            $e->errors()
        );
    } catch (\Exception $e) {
        return $this->errorResponse($e->getMessage(), 400);
    }
}
```

### GET - Mostrar (Show)

```php
/**
 * GET /api/[model]/{id}
 * 
 * Retorna detalle de 1 registro con relaciones
 */
public function show(
    Request $request,
    string $modelName,
    int $id
) {
    try {
        $this->initializeForModel($modelName);
        
        $item = $this->repository->findWithRelations($id);
        
        if (!$item) {
            return $this->errorResponse(
                'Record not found',
                404
            );
        }
        
        return response()->json([
            'data' => $item,
            'message' => 'Record retrieved'
        ]);
        
    } catch (\Exception $e) {
        return $this->errorResponse($e->getMessage(), 400);
    }
}
```

### PUT - Actualizar (Update)

```php
/**
 * PUT /api/[model]/{id}
 * 
 * Body: Datos completos (reemplazo)
 * 
 * Diferencia con PATCH:
 *   PUT  → Reemplaza recurso completo
 *   PATCH → Actualiza campos específicos
 */
public function update(
    Request $request,
    string $modelName,
    int $id
) {
    try {
        $this->initializeForModel($modelName);
        
        // Validar que existe
        if (!$this->repository->exists($id)) {
            return $this->errorResponse(
                'Record not found',
                404
            );
        }
        
        // Validar datos
        $validated = $this->validator->validate(
            $request->all(),
            'update'  // reglas específicas para actualización
        );
        
        // Actualizar
        $item = $this->repository->update($id, $validated);
        
        return response()->json([
            'data' => $item,
            'message' => 'Record updated'
        ]);
        
    } catch (\Exception $e) {
        return $this->errorResponse($e->getMessage(), 400);
    }
}
```

### DELETE - Eliminar (Destroy)

```php
/**
 * DELETE /api/[model]/{id}
 * 
 * Retorna: 204 No Content (recurso eliminado)
 * 
 * Consideraciones:
 *   - ¿Soft delete o hard delete?
 *   - ¿Validar dependencias?
 *   - ¿Auditar eliminación?
 */
public function destroy(
    Request $request,
    string $modelName,
    int $id
) {
    try {
        $this->initializeForModel($modelName);
        
        // Validar dependencias
        if ($this->repository->hasDependencies($id)) {
            return $this->errorResponse(
                'Cannot delete: has dependent records',
                422
            );
        }
        
        // Eliminar
        $this->repository->delete($id);
        
        // 204 No Content
        return response()->noContent();
        
    } catch (\Exception $e) {
        return $this->errorResponse($e->getMessage(), 400);
    }
}
```

---

## 3. Operaciones Avanzadas

### Búsqueda con Filtros

```php
/**
 * POST /api/[model]/search
 * 
 * Body:
 *   {
 *     "query": "aws",
 *     "filters": {
 *       "status": "active",
 *       "level": ["junior", "mid"]
 *     },
 *     "sort": "name",
 *     "order": "asc",
 *     "page": 1
 *   }
 */
public function search(Request $request, string $modelName)
{
    try {
        $this->initializeForModel($modelName);
        
        // Construir query
        $searchQuery = $request->get('query', '');
        $filters = $request->get('filters', []);
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        $page = $request->get('page', 1);
        
        // Ejecutar búsqueda
        $results = $this->repository->advancedSearch(
            query: $searchQuery,
            filters: $filters,
            sort: $sort,
            order: $order,
            page: $page
        );
        
        return response()->json([
            'data' => $results->items(),
            'meta' => [
                'total' => $results->total(),
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
            ],
            'message' => 'Search completed'
        ]);
        
    } catch (\Exception $e) {
        return $this->errorResponse($e->getMessage(), 400);
    }
}
```

### Exportar a CSV

```php
/**
 * GET /api/[model]/export?format=csv
 * 
 * Descargar todos registros en CSV
 */
public function export(Request $request, string $modelName)
{
    try {
        $this->initializeForModel($modelName);
        
        $format = $request->get('format', 'csv');
        
        // Recuperar TODOS (con límite razonable)
        $items = $this->repository->all(limit: 10000);
        
        if ($format === 'csv') {
            return $this->exportCsv($items, $modelName);
        } elseif ($format === 'excel') {
            return $this->exportExcel($items, $modelName);
        }
        
        return $this->errorResponse('Format not supported', 400);
        
    } catch (\Exception $e) {
        return $this->errorResponse($e->getMessage(), 400);
    }
}

private function exportCsv($items, string $modelName)
{
    // Crear archivo CSV temporal
    $fileName = strtolower($modelName) . '-' . now()->format('Y-m-d-His') . '.csv';
    
    return response()->streamDownload(
        function () use ($items) {
            $handle = fopen('php://output', 'w');
            
            // Header
            if ($items->count() > 0) {
                fputcsv($handle, array_keys($items[0]->toArray()));
            }
            
            // Data
            foreach ($items as $item) {
                fputcsv($handle, $item->toArray());
            }
            
            fclose($handle);
        },
        $fileName,
        ['Content-Type' => 'text/csv']
    );
}
```

---

## 4. Manejo de Errores

### Error Response Helper

```php
/**
 * Respuesta estándar para errores
 * 
 * Ventaja: Consistencia en todos los endpoints
 */
private function errorResponse(
    string $message,
    int $statusCode = 400,
    array $errors = []
): \Illuminate\Http\JsonResponse {
    
    $response = [
        'message' => $message,
        'status' => 'error',
        'timestamp' => now()->toIso8601String(),
    ];
    
    if (!empty($errors)) {
        $response['errors'] = $errors;
    }
    
    // Logging para auditoría
    \Log::error('FormSchemaController Error', [
        'message' => $message,
        'status' => $statusCode,
        'errors' => $errors,
        'user_id' => auth()->id() ?? null,
    ]);
    
    return response()->json($response, $statusCode);
}
```

### Validación Centralizada

```php
class FormValidator
{
    private string $modelName;
    private array $rules = [];
    private array $messages = [];
    
    public function __construct(string $modelName)
    {
        $this->modelName = $modelName;
        $this->loadRules();
    }
    
    private function loadRules(): void
    {
        // Busca App\Validators\[Model]Validator
        $validatorClass = "App\\Validators\\" . 
                         $this->modelName . "Validator";
        
        if (class_exists($validatorClass)) {
            $validator = new $validatorClass();
            $this->rules = $validator->rules();
            $this->messages = $validator->messages();
        } else {
            // Fallback a validación genérica
            $this->rules = $this->getDefaultRules();
        }
    }
    
    /**
     * Valida datos según reglas del modelo
     */
    public function validate(
        array $data,
        string $action = 'create'  // 'create' o 'update'
    ): array {
        
        // Reglas específicas por acción
        $rules = $action === 'update' 
            ? $this->getUpdateRules()
            : $this->rules;
        
        return validator($data, $rules, $this->messages)
            ->validate();
    }
    
    private function getUpdateRules(): array
    {
        // Para UPDATE, requiredirlos son opcionales
        return array_map(
            fn($rule) => str_replace('required', 'sometimes', $rule),
            $this->rules
        );
    }
}
```

---

## 5. Seguridad

### Autorización

```php
/**
 * Verificar permisos antes de operar
 */
private function authorizeAction(
    string $action,
    Model $model = null
): void {
    $user = auth()->user();
    
    if (!$user) {
        throw new \Illuminate\Auth\AuthenticationException();
    }
    
    // Política de autorización
    $policy = $this->resolvePolicyFor($this->modelClass);
    
    if (!$policy) {
        return; // Sin política = permitir
    }
    
    $authorized = match($action) {
        'view'   => $policy->view($user, $model),
        'create' => $policy->create($user),
        'update' => $policy->update($user, $model),
        'delete' => $policy->delete($user, $model),
    };
    
    if (!$authorized) {
        throw new \Illuminate\Auth\Access\AuthorizationException();
    }
}

/**
 * En cada método:
 */
public function store(Request $request, string $modelName)
{
    $this->initializeForModel($modelName);
    $this->authorizeAction('create');  // ← Verificar
    
    // ... resto del código
}
```

### Filtering de Salida

```php
/**
 * No retornar campos sensibles
 */
public function show(Request $request, string $modelName, int $id)
{
    $this->initializeForModel($modelName);
    
    $item = $this->repository->findWithRelations($id);
    
    // Filtrar campos sensibles
    $item->hideFields(['password_hash', 'api_token']);
    
    return response()->json(['data' => $item]);
}
```

---

## 6. Pruebas del Controller

### Ejemplo: Test de Store

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\People;

class FormSchemaControllerTest extends TestCase
{
    /** @test */
    public function it_creates_a_people()
    {
        // Arrange
        $data = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'dni' => '12345678',
        ];
        
        // Act
        $response = $this->postJson('/api/people', $data);
        
        // Assert
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id',
                     'name',
                     'email',
                     'dni',
                 ]);
        
        $this->assertDatabaseHas('people', $data);
    }
    
    /** @test */
    public function it_validates_required_fields()
    {
        // Act
        $response = $this->postJson('/api/people', [
            'name' => 'Juan'
            // Falta email y dni
        ]);
        
        // Assert
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email', 'dni']);
    }
    
    /** @test */
    public function it_searches_by_query()
    {
        // Arrange
        People::factory()->create(['name' => 'AWS Expert']);
        People::factory()->create(['name' => 'Java Developer']);
        
        // Act
        $response = $this->postJson('/api/people/search', [
            'query' => 'aws'
        ]);
        
        // Assert
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }
}
```

---

## Conclusión: Poder de la Dinamicidad

El `FormSchemaController` es **genérico pero flexible**:

- ✅ **Mismo código** para múltiples modelos
- ✅ **Reflexión** para resolver clases dinámicamente
- ✅ **Strategy Pattern** para adaptarse a necesidades
- ✅ **Manejo centralizado** de errores y seguridad
- ✅ **Validación** consistente pero customizable

Un cambio en el controller beneficia a **TODOS los modelos**.

---

**Próximo capítulo:** [05_FORM_SCHEMA_COMPLETE_PHP.md](05_FORM_SCHEMA_COMPLETE_PHP.md)

¿Cómo se generan las rutas dinámicamente sin duplicación?
