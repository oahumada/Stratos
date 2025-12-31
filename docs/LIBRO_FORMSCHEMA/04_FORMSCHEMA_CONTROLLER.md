# Capítulo 4: FormSchemaController - Orquestación Dinámica

**Duración de lectura:** 30 minutos  
**Nivel:** Avanzado  
**Conceptos clave:** Reflexión, Strategy Pattern, Inyección de dependencias

---

## Introducción: El Corazón del Sistema

El `FormSchemaController` es el componente más sofisticado de FormSchema Pattern. Mientras que los componentes Vue son reutilizables pero estáticos, el controller es **dinámicamente adaptable** a cualquier modelo sin modificación de código.

```php
// Un ÚNICO controller para MÚLTIPLES modelos:

FormSchemaController::index(Person)        → Retorna Personas
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
     * Entrada: "Person"
     * Proceso: 
     *   1. Agrega namespace: "App\Models\Person"
     *   2. Verifica que clase existe
     *   3. Verifica que hereda de Model
     * Salida: App\Models\Person (clase)
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
     *   ├─ App\Repository\PersonRepository
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

## 2. Operaciones CRUD

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
use App\Models\Person;

class FormSchemaControllerTest extends TestCase
{
    /** @test */
    public function it_creates_a_person()
    {
        // Arrange
        $data = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'dni' => '12345678',
        ];
        
        // Act
        $response = $this->postJson('/api/person', $data);
        
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
        $response = $this->postJson('/api/person', [
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
        Person::factory()->create(['name' => 'AWS Expert']);
        Person::factory()->create(['name' => 'Java Developer']);
        
        // Act
        $response = $this->postJson('/api/person/search', [
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
