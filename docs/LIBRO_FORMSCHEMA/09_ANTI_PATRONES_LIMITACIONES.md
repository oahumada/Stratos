# Capítulo 9: Anti-Patrones y Limitaciones Conocidas

**Duración de lectura:** 20 minutos  
**Nivel:** Avanzado  
**Conceptos clave:** Limitaciones, Cuando NO usar, Workarounds

---

## Introducción: El Lado Oscuro

Ningún patrón es perfecto. Este capítulo documenta dónde FormSchema Pattern **no es la solución**.

---

## Anti-Patrón 1: Lógica de Negocio en Configuración

### ❌ MALO: Intentar expresar lógica compleja en JSON

```json
{
  "fields": [
    {
      "key": "total",
      "label": "Total",
      "type": "text",
      "calculate": "subtotal * quantity * (1 - discount/100)"
    }
  ]
}
```

**Problema:**
- JSON no es lenguaje de programación
- Cálculos complejos son ilegibles en JSON
- Debugging imposible
- Performance desconocido

### ✅ BUENO: Lógica en backend/frontend según corresponda

```php
// Backend: Calcular en Model

class Order extends Model
{
    public function getTotalAttribute()
    {
        return $this->subtotal 
            * $this->quantity 
            * (1 - ($this->discount / 100));
    }
}
```

```vue
// Frontend: Computed property en Vue

const total = computed(() => 
  formData.value.subtotal 
    * formData.value.quantity 
    * (1 - (formData.value.discount / 100))
);
```

---

## Anti-Patrón 2: Sobrecargar FormSchemaController

### ❌ MALO: Agregar métodos personalizados al controller genérico

```php
// ❌ NO HAGAS ESTO
class FormSchemaController extends Controller
{
    public function bulkApprove(Request $request, $modelName)
    {
        // Aprobación masiva (lógica de negocio específica)
    }
    
    public function generateReport(Request $request, $modelName)
    {
        // Reporte personalizado
    }
}
```

**Problema:**
- Mezcla lógica genérica con específica
- Controller se vuelve monolítico
- Difícil de testear

### ✅ BUENO: Crear controllers específicos

```php
// ✅ Crear BulkActionController
class BulkActionController extends Controller
{
    public function approve(Request $request)
    {
        $ids = $request->get('ids', []);
        $modelName = $request->get('model');
        
        $modelClass = "App\\Models\\" . $modelName;
        $modelClass::whereIn('id', $ids)
            ->update(['status' => 'approved']);
        
        return response()->json(['message' => 'Approved']);
    }
}

// ✅ Crear ReportController
class ReportController extends Controller
{
    public function generate(Request $request)
    {
        // Lógica específica de reportes
    }
}
```

---

## Anti-Patrón 3: Ignorar Validaciones de Negocio

### ❌ MALO: Solo validaciones de formato

```json
{
  "fields": [
    {
      "key": "hire_date",
      "label": "Fecha Contratación",
      "type": "date",
      "rules": ["required", "date"]
    }
  ]
}
```

**Problema:**
- No valida que hire_date < today
- No valida que hire_date > birth_date
- No valida conflictos con períodos específicos

### ✅ BUENO: Validación en múltiples capas

```php
// Backend: Validator
class EmployeeValidator
{
    public function rules()
    {
        return [
            'hire_date' => 'required|date|before:today',
        ];
    }
}

// Backend: Model - Lógica más compleja
class Employee extends Model
{
    public static function boot()
    {
        parent::boot();
        
        static::saving(function ($employee) {
            // Hire date debe ser después de fecha de nacimiento
            if ($employee->hire_date < $employee->birth_date->addYears(18)) {
                throw new ValidationException('Must be 18+');
            }
            
            // No pueden empezar en domingo
            if ($employee->hire_date->dayOfWeek === 0) {
                throw new ValidationException('Cannot hire on Sunday');
            }
        });
    }
}

// Frontend: Computed property
const canHire = computed(() => {
    const minAge = moment().subtract(18, 'years');
    return selectedBirthDate.value > minAge;
});
```

---

## Anti-Patrón 4: Relaciones Demasiado Profundas

### ❌ MALO: Tries eager loading infinito

```json
{
  "columns": [
    {
      "key": "department.manager.team.lead",
      "label": "Lead del Equipo del Manager"
    }
  ]
}
```

**Problema:**
- N+1 queries
- Queries enormes
- JSON response pesado
- Frontend lento

### ✅ BUENO: Limitar profundidad

```php
// Backend: Eager loading controlado
public function index(Request $request, string $modelName)
{
    // Máximo 2 niveles de relaciones
    $items = $this->repository
        ->with(['department', 'department.manager'])
        ->paginate(15);
    
    return response()->json($items);
}

// JSON response:
{
  "id": 1,
  "name": "Juan",
  "department": {
    "id": 5,
    "name": "IT",
    "manager": {
      "id": 10,
      "name": "Carlos"
    }
  }
}
```

---

## Anti-Patrón 5: Configuración Duplicada

### ❌ MALO: Mismo campo en múltiples archivos

```json
// config.json
{
  "fields": [
    { "key": "email", "type": "email" }
  ]
}

// itemForm.json
{
  "fields": [
    {
      "key": "email",
      "label": "Email",
      "type": "email",
      "rules": ["required", "email"]
    }
  ]
}

// tableConfig.json
{
  "columns": [
    { "key": "email", "label": "Email", "type": "email" }
  ]
}
```

**Problema:**
- Cambiar email en un lugar olvida los otros
- Inconsistencia garantizada
- Overhead de mantenimiento

### ✅ BUENO: Configuración centralizada

```json
// person-form/_field-definitions.json
{
  "email": {
    "label": "Email",
    "type": "email",
    "rules": ["required", "email"],
    "width": "250px",
    "searchable": true
  },
  "name": {
    "label": "Nombre",
    "type": "text",
    "rules": ["required"],
    "width": "200px",
    "searchable": true
  }
}

// itemForm.json (referencia)
{
  "fields": ["$ref:./_field-definitions.json#/email", "$ref:./_field-definitions.json#/name"]
}

// tableConfig.json (referencia)
{
  "columns": ["$ref:./_field-definitions.json#/email", "$ref:./_field-definitions.json#/name"]
}
```

---

## Anti-Patrón 6: Ignorar Caché en Búsquedas

### ❌ MALO: Buscar en cada request sin caché

```vue
// Frontend
async function loadDepartments() {
    const response = await axios.get('/api/departments');
    departments.value = response.data;
}

// Se llama en CADA render ❌
watch(() => selectedDepartment.value, () => {
    loadDepartments();
});
```

**Problema:**
- Request innecesarios
- Servidor sobrecargado
- Lentitud en la UI

### ✅ BUENO: Caché de catálogos

```vue
// Frontend - Cache inteligente
const departments = ref(null);

async function getDepartments() {
    // Usar caché si ya cargó
    if (departments.value !== null) {
        return departments.value;
    }
    
    const response = await axios.get('/api/departments');
    departments.value = response.data;
    return departments.value;
}

// Llamar UNA sola vez
onMounted(() => {
    getDepartments();
});
```

```php
// Backend - Usar caché Redis
public function getDepartments(Request $request)
{
    return Cache::remember('departments:all', 3600, function () {
        return Department::all();
    });
}

// Si se crea/actualiza departamento:
event('DepartmentUpdated', function () {
    Cache::forget('departments:all');
});
```

---

## Anti-Patrón 7: Performance - Queries Pesadas

### ❌ MALO: Sin limits ni indexes

```php
public function index(Request $request, string $modelName)
{
    // SELECT * FROM huge_table ❌
    return $this->repository->all();
}
```

**Problema:**
- Millones de registros en memoria
- Servidor crashea
- Response enormemente lenta

### ✅ BUENO: Paginación + Índices

```php
public function index(Request $request, string $modelName)
{
    // SELECT * FROM table ... LIMIT 15 OFFSET 0 ✅
    return $this->repository->paginate(15);
}

// Tabla migrations:
Schema::create('people', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique(); // Index automático
    $table->index('created_at');       // Index para sort
    $table->index('status');           // Index para filtros
});
```

---

## Anti-Patrón 8: Security - Exposición de Datos Sensibles

### ❌ MALO: Retornar TODO

```php
public function show(Request $request, string $modelName, $id)
{
    $item = $this->repository->find($id);
    
    // Retorna TODO incluyendo password_hash, api_token ❌
    return response()->json($item);
}
```

**Problema:**
- Expone datos sensibles
- API no es segura
- Violación de privacidad

### ✅ BUENO: Filtrar salida

```php
public function show(Request $request, string $modelName, $id)
{
    $item = $this->repository->find($id);
    
    // Hidden fields en Model
    $item->hidden = ['password_hash', 'api_token', 'ssn'];
    
    return response()->json($item);
}

// O en Model:
class Person extends Model
{
    protected $hidden = [
        'password_hash',
        'api_token',
        'ssn',
    ];
}
```

---

## Anti-Patrón 9: Conflictos de Timestamp

### ❌ MALO: Editar después de otro cambio

```
User A: Lee Person (updated_at: 2025-12-31 10:00)
User B: Edita Person (updated_at: 2025-12-31 10:05)
User A: Intenta editar con datos viejos
        ↓
        Sobrescribe cambios de User B ❌
```

### ✅ BUENO: Optimistic Locking

```php
// Migration: Agregar version
Schema::table('people', function (Blueprint $table) {
    $table->integer('version')->default(1);
});

// Controller: Validar version
public function update(Request $request, string $modelName, $id)
{
    $current = $this->repository->find($id);
    $submitted = $request->get('version');
    
    if ($current->version !== $submitted) {
        return response()->json([
            'error' => 'Record was modified by another user'
        ], 409);
    }
    
    $current->update($request->all());
    $current->increment('version');
    
    return response()->json($current);
}
```

---

## Anti-Patrón 10: Confundir Filtros con Búsqueda

### ❌ MALO: Todo mezclado

```json
{
  "filters": {
    "query": "buscar en todo",
    "department": "filtrar exacto",
    "status": "filtrar exacto",
    "name": "buscar por nombre"
  }
}
```

### ✅ BUENO: Separar claramente

```json
{
  "search": {
    "query": "texto libre en múltiples campos",
    "debounce": 300
  },
  "filters": {
    "department_id": "exacto",
    "status": "exacto",
    "created_from": "rango",
    "created_to": "rango"
  }
}
```

---

## Matriz: Cuándo NOT Usar FormSchema

| Situación | FormSchema | Alternativa |
|-----------|-----------|------------|
| CRUD simple | ✅ | - |
| Búsqueda simple | ✅ | - |
| Validaciones básicas | ✅ | - |
| Many-to-many | ✅ | - |
| **Cálculos complejos** | ❌ | Custom controller |
| **Lógica transaccional** | ❌ | Custom controller |
| **Relaciones dinámicas** | ❌ | GraphQL o REST custom |
| **Flujos de workflow** | ❌ | State machine |
| **Integraciones externas** | ❌ | Service layer |
| **Performance crítica** | ⚠️ | Optimizar + caché |

---

## Conclusión: Ser Realista

FormSchema Pattern es poderoso para 80% de casos.

Para el 20% restante: **úsalo donde aplica, crea custom donde no**.

La clave es **reconocer los límites** y elegir la herramienta correcta.

---

**Próximo capítulo:** [10_ESCALABILIDAD_MANTENIMIENTO.md](10_ESCALABILIDAD_MANTENIMIENTO.md)

¿Cómo mantener y escalar el patrón?
