# Capítulo 11: Repository Pattern - Capa de Persistencia

**Duración de lectura:** 25 minutos  
**Nivel:** Avanzado  
**Conceptos clave:** Data Access Object, Abstracción de BD, Strategy Pattern

---

## Introducción: Separación de Concerns

El **Repository Pattern** es el corazón del FormSchema. Mientras que:
- **FormSchemaController** orquesta HTTP
- **Repository** encapsula toda la lógica de persistencia

Esta separación es **crítica** para mantenibilidad y testabilidad.

---

## 1. ¿Qué es Repository Pattern?

### Definición

Un **Repository** es un objeto que actúa como una **colección en memoria** de entidades persistidas.

```php
// ❌ Sin Repository (lógica BD mezclada con Controller)
class PeopleController extends Controller {
    public function index() {
        return People::with('skills')
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->paginate(15);  // ← BD en Controller
    }
}

// ✅ Con Repository (lógica BD centralizada)
class PeopleRepository extends Repository {
    public function index(Request $request) {
        return $this->model
                    ->with('skills')
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->paginate(15);  // ← BD en Repository
    }
}

class PeopleController extends Controller {
    public function index(Request $request) {
        $this->initializeForModel('People');
        return $this->repository->index($request);  // ← Controller orquesta
    }
}
```

### Ventajas

| Aspecto | Antes | Después |
|--------|-------|---------|
| **Dónde vive lógica BD** | Controller (dispersa) | Repository (centralizada) |
| **Testabilidad** | Difícil (requiere BD real) | Fácil (mock Repository) |
| **Reutilización** | Controlador por modelo | 1 Repository para lógica shared |
| **Mantenimiento** | Cambio en BD = modificar controller | Cambio en BD = modificar repository |
| **Queries complejas** | Espagueti en controller | Métodos claros en repository |

---

## 2. Jerarquía de Repositorios en FormSchema

### 2.1 Estructura Base

```
┌─────────────────────────────────┐
│  RepositoryInterface             │
│  (Contrato: métodos que deben   │
│   implementar todos)            │
│                                  │
│  ├─ store()                      │
│  ├─ update()                     │
│  ├─ destroy()                    │
│  ├─ show()                       │
│  └─ search()                     │
└────────────────┬────────────────┘
                 │
        ┌────────▼────────┐
        │ Repository      │
        │ (Base abstract) │
        │                 │
        │ Implementa      │
        │ métodos CRUD    │
        │ genéricos para  │
        │ todos los       │
        │ modelos         │
        └────────┬────────┘
                 │
    ┌────────────┼────────────┐
    │            │            │
    ▼            ▼            ▼
PeopleRepo  RoleRepo     SkillRepo
├─ store()  ├─ store()   ├─ store()
├─ update() ├─ update()  ├─ update()
├─ destroy()├─ destroy() ├─ destroy()
├─ show()   ├─ show()    ├─ show()
├─ search() ├─ search()  ├─ search()
│           │            │
├─ search() ├─ search()  └─ Puede
│  custom   │  custom      override
│  (override)(override)    métodos
└──────────┘└──────────┘
```

### 2.2 RepositoryInterface

```php
<?php

namespace App\Repository;

use Illuminate\Http\Request;

interface RepositoryInterface
{
    /**
     * Crear nuevo registro
     */
    public function store(Request $request);
    
    /**
     * Actualizar registro existente
     */
    public function update(Request $request);
    
    /**
     * Eliminar registro
     */
    public function destroy($id);
    
    /**
     * Obtener un registro por ID
     */
    public function show(Request $request, $id);
    
    /**
     * Búsqueda con filtros
     */
    public function search(Request $request);
}
```

### 2.3 Repository Base

```php
<?php

namespace App\Repository;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Tools;

abstract class Repository implements RepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Crear: INSERT INTO tabla
     */
    public function store(Request $request)
    {
        try {
            $data = array_map(function ($value) {
                return is_array($value) ? implode(',', $value) : $value;
            }, $request->get('data', []));

            $this->model->create($data);
            return response()->json([
                'message' => 'Registro creado con éxito',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Leer: SELECT * FROM tabla WHERE id
     */
    public function show(Request $request, $id)
    {
        try {
            $record = $this->model->findOrFail($id);
            return response()->json($record, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Registro no encontrado'
            ], 404);
        }
    }

    /**
     * Actualizar: UPDATE tabla SET
     */
    public function update(Request $request)
    {
        try {
            $id = $request->input('data.id');
            $dataToUpdate = $request->input('data');
            unset($dataToUpdate['id']);

            $record = $this->model->findOrFail($id);
            $record->fill($dataToUpdate)->save();

            return response()->json([
                'message' => 'Registro actualizado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar'
            ], 500);
        }
    }

    /**
     * Eliminar: DELETE FROM tabla WHERE id
     */
    public function destroy($id)
    {
        try {
            $this->model->findOrFail($id)->delete();
            return response()->json([
                'message' => 'Registro eliminado'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar'
            ], 500);
        }
    }

    /**
     * Búsqueda: SELECT * FROM tabla WHERE filters
     */
    public function search(Request $request)
    {
        try {
            $filters = $request->input('data', []);
            $query = $this->model->query()->select("*");
            return Tools::filterData($filters, $query);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

**Notar**:
- Repository base **NO CONOCE** el modelo específico
- `$this->model` es genérico (puede ser People, Role, Skill)
- Todos los CRUD son agnósticos de modelo

---

## 3. Repositorios Específicos por Modelo

### 3.1 PeopleRepository

```php
<?php

namespace App\Repository;

use App\Models\People;

class PeopleRepository extends Repository
{
    public function __construct()
    {
        // Inyecta el modelo específico
        parent::__construct(new People());
    }

    /**
     * Override: Search customizado para People
     */
    public function search(Request $request)
    {
        $filters = $request->input('data', []);
        
        // Eager load relaciones específicas de People
        $query = $this->model
            ->with('skills', 'currentRole', 'department')
            ->where('status', 'active');
        
        return Tools::filterData($filters, $query);
    }
}
```

### 3.2 RoleRepository

```php
<?php

namespace App\Repository;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(new Role());
    }

    /**
     * Override: Store customizado para Role
     * Validar permisos antes de crear
     */
    public function store(Request $request)
    {
        // Validar autorización
        if (!auth()->user()->can('create_role')) {
            return response()->json([
                'error' => 'No tienes permiso para crear roles'
            ], 403);
        }

        // Luego ejecutar lógica base
        return parent::store($request);
    }

    /**
     * Override: Destroy customizado para Role
     * Evitar eliminar roles críticos
     */
    public function destroy($id)
    {
        $role = $this->model->findOrFail($id);

        // Proteger roles críticos
        if (in_array($role->code, ['ADMIN', 'SYSTEM'])) {
            return response()->json([
                'error' => 'No puedes eliminar roles críticos'
            ], 403);
        }

        return parent::destroy($id);
    }
}
```

### 3.3 SkillRepository

```php
<?php

namespace App\Repository;

use App\Models\Skill;

class SkillRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(new Skill());
    }

    /**
     * Override: Destroy con soft delete
     */
    public function destroy($id)
    {
        $skill = $this->model->findOrFail($id);
        
        // Soft delete en lugar de hard delete
        $skill->update(['deleted_at' => now()]);
        
        return response()->json([
            'message' => 'Skill marcada como eliminada'
        ], 200);
    }
}
```

---

## 4. Flujo de Ejecución Completo

### 4.1 GET /api/people

```
1. HTTP Request
   GET /api/people?page=1

2. form-schema-complete.php
   Route::get('/people', [FormSchemaController::class, 'index']);

3. FormSchemaController::index(Request $request, 'People')
   {
       $this->initializeForModel('People');
       // ↓ Resuelve:
       // $this->modelClass = "App\Models\People"
       // $this->repositoryClass = "App\Repository\PeopleRepository"
       // $this->repository = new PeopleRepository()
       
       return $this->repository->index($request);
   }

4. PeopleRepository::index($request)
   {
       // Hereda de Repository
       $filters = $request->input('data', []);
       $query = $this->model->query()->select("*");
       return Tools::filterData($filters, $query);
   }

5. Tools::filterData($filters, $query)
   {
       // Aplica filtros dinámicos
       foreach ($filters as $field => $value) {
           if ($value) {
               $query->where($field, $value);
           }
       }
       return response()->json([
           'data' => $query->paginate(15)
       ]);
   }

6. Response
   {
     "data": [...15 people...],
     "meta": { "total": 150, "page": 1 }
   }
```

### 4.2 POST /api/people (Crear)

```
1. HTTP Request
   POST /api/people
   Body: { "data": { "name": "John", "email": "john@..." } }

2. FormSchemaController::store(Request $request, 'People')
   {
       $this->initializeForModel('People');
       return $this->repository->store($request);
   }

3. PeopleRepository::store($request)
   // Hereda de Repository base
   {
       $data = $request->get('data');
       $this->model->create($data);
       return response()->json([
           'message' => 'Registro creado'
       ], 201);
   }

4. People::create($data)
   // Eloquent
   {
       INSERT INTO people (name, email, ...) VALUES (...)
   }

5. Response
   Status: 201 Created
   { "message": "Registro creado" }
```

---

## 5. Extensibilidad: Cuándo Override Métodos

### Patrón: Base + Específico

```php
// Opción 1: Usar método base (95% de casos)
class SkillRepository extends Repository {
    // Hereda store(), update(), destroy(), show(), search()
    // No override - perfectamente funcional
}

// Opción 2: Override un método (5% de casos)
class RoleRepository extends Repository {
    public function destroy($id) {
        // Lógica custom
        if ($this->isSystemRole($id)) {
            throw new \Exception("Cannot delete system role");
        }
        return parent::destroy($id);
    }
}

// Opción 3: Override múltiples métodos (raro)
class PeopleRepository extends Repository {
    public function store(Request $request) { ... }
    public function destroy($id) { ... }
    public function search(Request $request) { ... }
    
    // show() y update() usan base
}
```

### Ejemplos de Cuándo Override

| Caso | Repositorio | Método | Razón |
|------|-------------|--------|-------|
| Búsqueda con joins | PeopleRepository | search() | Eager load relaciones |
| Validación de autorización | RoleRepository | store() | Verificar permisos |
| Soft delete | SkillRepository | destroy() | No hard delete |
| Transformación de datos | CertificationRepository | show() | Mapear respuesta |
| Lógica de negocio | DepartmentRepository | update() | Validar cambios |

---

## 6. Ventajas del Repository Pattern en FormSchema

### 6.1 Testabilidad

```php
// Test SIN necesidad de BD real

use PHPUnit\Framework\TestCase;
use Mockery as m;

class PeopleRepositoryTest extends TestCase {
    public function test_search_with_filters() {
        // Mock el modelo
        $mockPeople = m::mock(People::class);
        $mockPeople->shouldReceive('with')
                   ->with('skills')
                   ->andReturnSelf();
        $mockPeople->shouldReceive('where')
                   ->with('status', 'active')
                   ->andReturnSelf();
        
        // Instanciar repository con mock
        $repository = new PeopleRepository($mockPeople);
        
        // Ejecutar y verificar
        $result = $repository->search($request);
        $this->assertTrue($result->ok());
    }
}
```

### 6.2 Reutilización

```php
// FormSchemaController funciona para TODOS los modelos
// Sin modificación, sin hardcoding

public function index(Request $request, string $modelName) {
    $this->initializeForModel($modelName);  // ← Dinámico
    return $this->repository->index($request);  // ← Polimórfico
}

// Puede ser llamado como:
// index(Request, 'People')  → PeopleRepository
// index(Request, 'Role')    → RoleRepository
// index(Request, 'Skill')   → SkillRepository
```

### 6.3 Mantenimiento Centralizado

```
Cambio: Agregar eager loading para todas las búsquedas de People

❌ Sin Repository:
   - Modificar PeopleController
   - Modificar PeopleService (si existe)
   - Modificar tests en ambos
   - Riesgo de olvidar un lugar

✅ Con Repository:
   - Modificar PeopleRepository::search()
   - Actualizar test de repository
   - Done - centralizado
```

---

## 7. Comparativa: Arquitecturas Alternativas

### Sin Repository (Monolítica)

```
Controller contiene TODO:
├─ Lógica HTTP
├─ Validación
├─ Queries BD
├─ Transformación datos
└─ Respuestas

❌ Problemas:
   - Difícil de testear
   - Lógica BD dispersa
   - Difícil de reutilizar
```

### Con Repository (Separada)

```
Controller → Repository → Model
├─ Controller: Solo orquestación
├─ Repository: Lógica BD centralizada
└─ Model: Mapeo a BD

✅ Ventajas:
   - Fácil de testear
   - Lógica centralizada
   - Fácil reutilizar
```

### Con Repository + Service (Máxima separación)

```
Controller → Service → Repository → Model
├─ Controller: HTTP
├─ Service: Lógica negocio
├─ Repository: Persistencia
└─ Model: Mapeo

✅ Para sistemas muy grandes
❌ Puede ser over-engineering para CRUD simple
```

**FormSchema elige**: Repository (sin Service extra)
- Suficientemente separado para CRUD
- No over-engineered
- Balance perfecto de simplicidad

---

## Conclusión

**Repository Pattern es fundamental en FormSchema** porque:

1. **Separa concerns**: Controller para HTTP, Repository para BD
2. **Habilita polimorfismo**: Un FormSchemaController para 10+ modelos
3. **Facilita testing**: Mock Repository sin tocar BD
4. **Centraliza lógica**: Cambios en BD van en Repository
5. **Escala naturalmente**: Agregar modelo = 1 Repository específico

El patrón es la razón por la cual FormSchema es **realmente genérico** y no solo "reutilizable con copy-paste".
