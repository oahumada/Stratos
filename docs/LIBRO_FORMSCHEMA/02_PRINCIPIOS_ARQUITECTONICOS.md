# Capítulo 2: Principios Arquitectónicos

**Duración de lectura:** 20 minutos  
**Nivel:** Intermedio  
**Temas:** SOLID, Clean Code, Design Patterns

---

## Introducción

FormSchema Pattern no es mágico. Funciona porque **aplica rigorosamente** principios de arquitectura de software comprobados:

- **D**RY (Don't Repeat Yourself)
- **S**OLID Principles
- Clean Code
- Design Patterns

Entender ESTOS principios es entender POR QUÉ el patrón es superior.

---

## 1. DRY: Don't Repeat Yourself

### Definición Formal

> "Every piece of knowledge must have a single, unambiguous, 
> authoritative representation within a system."
>
> — Andy Hunt & Dave Thomas, "The Pragmatic Programmer"

### En FormSchema Pattern

**El conocimiento sobre "cómo hacer un CRUD" existe en UN solo lugar:**

```
Conocimiento centralizado:
┌─────────────────────────────────────────┐
│    FormSchemaController                 │
│                                         │
│  - Cómo buscar con filtros             │
│  - Cómo validar datos                  │
│  - Cómo manejar excepciones            │
│  - Cómo paginar resultados             │
│  - Cómo formatear respuestas           │
└─────────────────────────────────────────┘
                    ▲
                    │ usado por
        ┌───────────┴───────────┐
        │                       │
   People CRUD         Certification CRUD
   Skills CRUD         Role CRUD
   ... 100+ modelos
```

**Ventaja:**
- Bug encontrado en búsqueda = fix en 1 lugar = fix para todos los CRUDs
- Cambio en lógica de filtrado = 1 cambio centralizado
- Testing = test FormSchemaController 1 vez = todos los CRUDs funcionan

**Métrica:**
- Antes: 10 controladores × 50 líneas lógica común = 500 líneas duplicadas
- Ahora: 1 FormSchemaController × 50 líneas = 50 líneas

**Reducción:** 90% de duplicación eliminada

---

## 2. SOLID Principles

### S - Single Responsibility Principle (SRP)

**Definición:** Una clase debe tener una sola razón para cambiar.

#### Antes (VIOLADO)

```php
class CertificationController extends Controller
{
    // Responsabilidad 1: HTTP input/output
    public function index(Request $request) { }
    
    // Responsabilidad 2: Validación de datos
    private function validateCertification($data) { }
    
    // Responsabilidad 3: Búsqueda y filtrado
    public function search(Request $request) { }
    
    // Responsabilidad 4: Error handling
    // (esparcida por todas partes)
    
    // Responsabilidad 5: Acceso a BD
    // (directo a Eloquent)
}
```

**Problemas:**
- Si validación cambia → toca CertificationController
- Si búsqueda cambia → toca CertificationController
- Si error handling cambia → toca CertificationController
- 10 CRUDs = 10 razones para cambiar cada uno

#### Después (RESPETADO)

```php
// Responsabilidad 1: HTTP (RouteModel Binding)
FormSchemaController → maneja Request/Response

// Responsabilidad 2: Validación
FormSchemaRequest → validación centralizada

// Responsabilidad 3: Negocio
Repository/Service → lógica de búsqueda

// Responsabilidad 4: Persistencia
Model → acceso a BD

class FormSchemaController
{
    // SOLO responsabilidad: Orquestar request → response
    // Delega todo lo demás
}
```

**Beneficio:**
- Cada clase tiene UNA razón para cambiar
- Cambios localizados = bajo riesgo
- Fácil testing (mock dependencies)

---

### O - Open/Closed Principle (OCP)

**Definición:** Software debe estar ABIERTO para extensión pero CERRADO para modificación.

#### Antes (VIOLADO)

```php
// Si queremos agregar nuevo tipo de búsqueda:
// → Modificamos CertificationController (cambio existente)
// → Modificamos PeopleController (cambio existente)
// → ... 10 archivos más

// NO es extensible sin modificar código existente
```

#### Después (RESPETADO)

```php
// Para agregar nuevo tipo de búsqueda:
// → Agregamos método a FormSchemaController
// → AUTOMÁTICAMENTE disponible para TODOS los CRUDs
// → Ningún código existente es modificado
// → Solo AGREGAMOS, no cambiamos

$formSchemaModels = [
    'Certification' => 'certifications',  // NUEVO
    'People' => 'people',                 // EXISTENTE (sin cambios)
];
```

**Beneficio:**
- Código existente está protegido
- Nuevos CRUDs no requieren cambios a código viejo
- Menos riesgo de breaking changes

---

### L - Liskov Substitution Principle (LSP)

**Definición:** Subtipos deben ser substituibles por sus supertios sin romper el código.

#### En FormSchema

```php
// FormSchemaController maneja CUALQUIER modelo:
class FormSchemaController
{
    public function index(Request $request, string $modelName)
    {
        // Funciona con People, Certification, Role, cualquier modelo
        // LSP garantiza que Certification "se comporta" como People
    }
}

// Esto funciona porque:
// - Todos los modelos extienden Eloquent Model
// - Todos implementan interfaz consistente
// - FormSchemaController NO asume nada específico de un modelo
```

**Beneficio:**
- Genérico = reutilizable
- Nuevos modelos = automáticamente soportados
- Sin cambios al controller

---

### I - Interface Segregation Principle (ISP)

**Definición:** Clientes NO deben depender de interfaces que no usan.

#### En FormSchema

```php
// Frontend SOLO consume:
interface CRUDApi {
    GET /api/[model]           → lista
    POST /api/[model]          → crear
    PUT /api/[model]/{id}      → actualizar
    DELETE /api/[model]/{id}   → eliminar
}

// No expone:
- Métodos internos
- Helpers privados
- Detalles de implementación

// Frontend usa SOLO lo que necesita
```

**Beneficio:**
- Contrato claro y consistente
- Frontend no tiene acoplamiento innecesario
- Fácil cambiar implementación interna sin afectar cliente

---

### D - Dependency Inversion Principle (DIP)

**Definición:** Depender de abstracciones, no de concreciones.

#### Antes (VIOLADO)

```php
class CertificationController
{
    public function index()
    {
        // Acoplado directamente a Certification model
        $items = Certification::paginate();
        
        // Si quiero cambiar a otro storage (API, NoSQL) → refactor
    }
}
```

#### Después (RESPETADO)

```php
class FormSchemaController
{
    // Depende de abstracción (Repository interface)
    private $repository;
    
    public function __construct(RepositoryInterface $repo)
    {
        $this->repository = $repo;
    }
    
    public function index()
    {
        // Funciona con CUALQUIER repositorio
        // BD, API, caché, etc. → todo funciona
        $items = $this->repository->all();
    }
}
```

**Beneficio:**
- Bajo acoplamiento
- Fácil testing (mock repository)
- Cambiable sin refactor

---

## 3. Design Patterns Utilizados

### Pattern 1: Strategy Pattern

**En:** FormSchemaController

```
Problema: "Necesito ejecutar lógica diferente según el modelo"

Solución: Strategy Pattern
┌──────────────────────────────┐
│  FormSchemaController        │
│  (Context)                   │
│                              │
│  - initializeForModel()      │ ← selecciona estrategia
│  - execute operación         │
└──────────────────────────────┘
           │
      ┌────┴─────┬──────────┬──────────┐
      │           │          │          │
   People    Certification  Role      Skill
   Strategy   Strategy      Strategy  Strategy
```

**Código:**
```php
class FormSchemaController
{
    private $modelClass;
    private $repository;
    
    public function initializeForModel(string $modelName)
    {
        // Selecciona la estrategia correcta
        $class = "App\\Models\\" . $modelName;
        $this->modelClass = new $class();
        $this->repository = $this->getRepository($modelName);
    }
    
    public function index(Request $request)
    {
        // Ejecuta la estrategia
        return $this->repository->search($request->all());
    }
}
```

---

### Pattern 2: Repository Pattern

**En:** Abstracción de acceso a datos

```php
interface RepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function search(array $criteria);
}

class GenericRepository implements RepositoryInterface
{
    protected $model;
    
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    
    public function all()
    {
        return $this->model->paginate();
    }
    
    // ... resto de métodos
}
```

**Beneficio:**
- Separa lógica de BD del controlador
- Reutilizable para múltiples modelos
- Fácil testing

---

### Pattern 3: Factory Pattern

**En:** form-schema-complete.php

```php
// Factory genera rutas dinámicamente
$formSchemaModels = [
    'People' => 'people',
    'Certification' => 'certifications',
];

foreach ($formSchemaModels as $modelName => $routeName) {
    // Factory crea rutas automáticamente
    Route::get($routeName, function () use ($modelName) {
        // El patrón Factory decide qué controller instanciar
        return app(FormSchemaController::class)->handle($modelName);
    });
}
```

---

### Pattern 4: Template Method Pattern

**En:** FormSchema.vue

```vue
<template>
  <!-- Template fijo para TODOS los CRUDs -->
  <div>
    <TableComponent :config="tableConfig" />
    <SearchComponent :filters="filters" />
    <FormDialog :schema="itemForm" />
  </div>
</template>

<!-- Configuración específica por modelo vía JSONs -->
<script setup>
const tableConfig = require('./certifications-form/tableConfig.json');
const itemForm = require('./certifications-form/itemForm.json');
const filters = require('./certifications-form/filters.json');
</script>
```

---

## 4. Principios de Clean Code

### 1. Significative Names (Nombres Significativos)

**Antes:**
```php
public function p(Request $r) { } // ¿Qué es p? ¿Request $r?

private function val($d) { }      // ¿val? ¿d?
```

**Después:**
```php
public function index(Request $request) { }     // Claro

private function validateData($data) { }        // Evidente
```

---

### 2. Functions Should Do One Thing

**Antes:**
```php
public function search(Request $request)
{
    // Validar
    // Construir query
    // Aplicar filtros
    // Paginar
    // Formatear respuesta
    // Error handling
    // Loguear
    // (200 líneas)
}
```

**Después:**
```php
public function search(Request $request)
{
    $criteria = $this->extractCriteria($request);
    $results = $this->repository->search($criteria);
    return $this->formatResponse($results);
    // (10 líneas, clara intención)
}
```

---

### 3. DRY Comments Código, No Intención

**Antes:**
```php
public function getCertification($id)
{
    // Get certification from DB (obvio)
    $cert = Certification::find($id);
    // If not found, return error (obvio)
    if (!$cert) return null;
}
```

**Después:**
```php
public function getCertification($id): ?Certification
{
    // return null if certification doesn't exist
    // (el tipo hace obvio qué hace)
    return Certification::find($id);
}
```

---

## 5. Anti-Patterns Evitados

### Anti-Pattern 1: Copy-Paste Programming

**Antes:**
```
PeopleController.php → Copy-Paste → CertificationController.php
CertificationController.php → Copy-Paste → RoleController.php
... 10+ veces
```

**Ahora:**
```
FormSchemaController.php → Usado por todos → 0 copy-paste
```

---

### Anti-Pattern 2: God Object

**Antes:**
```php
// CertificationController hace TODO
class CertificationController
{
    public function index() { }
    public function store() { }
    public function show() { }
    public function update() { }
    public function destroy() { }
    public function search() { }
    public function validateCertification() { }
    public function formatResponse() { }
    public function logAction() { }
    // ... 50+ métodos
}
```

**Ahora:**
```php
// FormSchemaController hace SOLO orquestación
class FormSchemaController
{
    public function search() { }
    public function store() { }
    // ... (delega resto a clases especializadas)
}
```

---

### Anti-Pattern 3: Lava Flow (código muerto acumulado)

**Antes:**
```php
// Controladores viejos no se usan pero se mantienen
PeopleController v1.0 (viejo)
PeopleController v2.0 (viejo)
PeopleController v3.0 (actual)
// Qué versión es "correcta"?
```

**Ahora:**
```
FormSchemaController (única versión)
// Claro cuál es la versión correcta
```

---

## Conclusión: Por Qué Funciona

FormSchema Pattern FUNCIONA porque:

1. ✅ **Respeta DRY** → 0% duplicación
2. ✅ **Respeta SOLID** → arquitectura flexible
3. ✅ **Usa Design Patterns comprobados** → soluciones conocidas
4. ✅ **Evita Anti-Patterns** → código limpio
5. ✅ **Sigue Clean Code** → legible y mantenible

**No es magia. Es arquitectura sólida.**

---

## Ejercicio Práctico

**Pregunta:** Identifica qué principio VIOLADO encontrarías en este código:

```php
class PeopleController extends Controller
{
    public function index() {
        $people = People::all();
        foreach ($people as $people) {
            // Validar
            if (!$people->email) return error();
            
            // Calcular algo
            $people->age = now()->diff($people->born_at);
            
            // Formatear
            $people->full_name = $people->first_name . ' ' . $people->last_name;
        }
        return response()->json($people);
    }
}
```

**Respuestas esperadas:**
- ❌ SRP violado (múltiples responsabilidades)
- ❌ DRY violado (mismo código en RoleController, SkillController)
- ❌ Separación de concerns (validación, cálculo, formateo mezclados)

---

**Próximo capítulo:** [03_ARQUITECTURA_GENERAL.md](03_ARQUITECTURA_GENERAL.md)

¿Cómo se conectan todos estos componentes?
