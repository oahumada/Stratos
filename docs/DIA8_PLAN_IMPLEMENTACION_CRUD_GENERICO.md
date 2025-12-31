# DÍA 8: Plan de Implementación - CRUD Genérico con FormSchema

**Fecha:** 29 de Diciembre 2025 (Actualizado 31 Diciembre 2025)  
**Estado:** ✅ COMPLETADO - Implementación Final Ejecutada  
**Objetivo:** Usar FormSchemaController + form-schema-complete.php para nuevos CRUDs

---

## 📌 Estado Actual (31 Diciembre 2025)

✅ **COMPLETADO:**
- FormSchemaController implementado y funcional
- form-schema-complete.php generando todas las rutas CRUD automáticamente
- 8 endpoints por modelo generados sin duplicación
- Sin controladores individuales para CRUD genérico
- Mapeo de modelos centralizado en `$formSchemaModels`

**Implementación actual:**
```php
// /routes/form-schema-complete.php
$formSchemaModels = [
    'People' => 'people',
    'Skills' => 'skills',
    'Department' => 'departments',
    'Role' => 'roles',  // Antes era 'role', ahora plural para consistencia
];

// Genera automáticamente TODAS las rutas CRUD
// sin código repetido, sin controladores individuales
```

---

## 🎯 Para Futuros Componentes CRUD

⚠️ **ANTES de empezar cualquier tarea, revisa estos documentos:**

1. [FormSchemaController-Complete-Documentation.md](FormSchemaController-Complete-Documentation.md)
   - Visión completa de cómo debe funcionar el controller
   - Métodos, flujo de ejecución, responsabilidades

2. [FormSchema-Routes-Documentation.md](FormSchema-Routes-Documentation.md)
   - Estructura de rutas genéricas
   - Patrón de mapeo modelos → rutas
   - Convención de nombres

3. [FormSchemaController-Flow-Diagram.md](FormSchemaController-Flow-Diagram.md)
   - Diagrama de flujo del sistema
   - Interacción controller → repository → model

4. [FormSchemaController-Executive-Summary.md](FormSchemaController-Executive-Summary.md)
   - Resumen ejecutivo rápido
   - Decisiones arquitectónicas

5. [FormSchemaTestingSystem.md](FormSchemaTestingSystem.md)
   - Cómo probar el CRUD genérico
   - Casos de prueba, comandos curl

6. [FormSchemaController-Migration.md](FormSchemaController-Migration.md)
   - Guía de migración de controladores individuales → genérico
   - Evitar duplicación de código

**Tiempo estimado:** 15 minutos  
**Resultado:** Entender completamente la arquitectura antes de escribir código

---

## 1. Análisis de Estado Actual

### 1.1 FormSchema Complete Routes (`/routes/form-schema-complete.php`)
**Estado:** 60% completado

✅ **Completado:**
- Estructura base de rutas genéricas con mapeo de modelos
- Loop forEach generando rutas dinámicas para cada modelo
- Métodos HTTP mapeados correctamente (GET, POST, PUT, PATCH, DELETE)
- Convención de nombres de rutas con prefijo `api.`
- 8 rutas por modelo + extras

⚠️ **Problemas identificados:**
1. **Prefijo `/api/` faltante** - Las rutas se registran sin `/api/` explícito
   - Se genera: `people` en lugar de `api/people`
   - Solución: Envolver con `Route::prefix('api')`

2. **Parámetros inconsistentes** - Algunos métodos faltan `$id`
   - `update()` no recibe `$id` en parámetros de closure
   - `destroy()` sí lo recibe correctamente

3. **Middleware incompleto** - Dice TODO para auth:sanctum
   - Está removido en línea 28: `Route::group([], function()`
   - Debería ser: `Route::middleware(['auth:sanctum'])->group(...)`

4. **Métodos duplicados** - Rutas POST conflictivas
   - POST `/people` para store
   - POST `/people/search` también espera POST
   - Patrón incorrecto: búsqueda debería ser GET con query params

5. **searchWithPeople incompleto** - Línea 77+ truncada
   - Falta implementación completa del método

### 1.2 FormSchemaController (`/app/Http/Controllers/FormSchemaController.php`)
**Estado:** 45% completado

✅ **Completado:**
- `initializeForModel()` - Mapeo dinámico de modelo a clase
- `store()` - Delegación al repositorio
- `update()` - Delegación al repositorio
- Mapeo de vistas (getViewMap)
- Manejo de excepciones básico

❌ **Falta implementar:**
1. `show()` - GET por ID
2. `destroy()` - DELETE con soft deletes
3. `search()` - Búsqueda con filtros
4. `searchWithPeople()` - Búsqueda con joins
5. `getViewMap()` - Mapeo completo de vistas
6. `getConsultaViewMap()` - Para vistas de consulta
7. **Validación** - No hay validación de requests
8. **Repositorio fallback** - Si no existe repository, usar model directo

### 1.3 Módulo People - Bugs Pendientes
**Errores vistos en logs:**
```
TypeError in app/Repository/Repository.php:28
array_map(): Argument #2 ($array) must be of type array, null given
```
**Causa:** Problema en base Repository al procesar filtros nulos

**Otros issues:**
- Form validation incompleta
- Relaciones no siempre cargadas
- Soft deletes sin marcar como deleted_at

---

## 2. Tareas Secuenciales para DÍA 8

### TAREA 1: Depurar Módulo People (1-2 horas)
**Prioridad:** 🔴 CRÍTICA

**Pasos:**
1. Revisar `/app/Repository/Repository.php` línea 28
   - Validar que `$filters` no sea null antes de `array_map()`
   - Agregar guards: `if (empty($filters)) return $this;`

2. Validar relaciones People
   - Verificar que Organization FK existe
   - Cargar relación con `with('organization')`
   - Probar factory genera datos válidos

3. Probar CRUD completo vía curl/API
   ```bash
   # GET todos
   curl http://localhost:8000/api/people
   
   # GET por ID
   curl http://localhost:8000/api/people/1
   
   # POST crear
   curl -X POST http://localhost:8000/api/people \
     -H "Content-Type: application/json" \
     -d '{"first_name":"Test","last_name":"User","email":"test@test.com","organization_id":1}'
   
   # PUT actualizar
   curl -X PUT http://localhost:8000/api/people/1 \
     -H "Content-Type: application/json" \
     -d '{"first_name":"Updated"}'
   
   # DELETE
   curl -X DELETE http://localhost:8000/api/people/1
   ```

4. Validar respuestas JSON
   - Estructura correcta
   - Relaciones cargadas
   - Soft deletes funcionales

### TAREA 2: Arreglar FormSchemaController (2-3 horas)
**Prioridad:** 🟠 ALTA

**Métodos a implementar:**

```php
// 1. show($request, $modelName, $id)
public function show(Request $request, string $modelName, $id)
{
    try {
        $this->initializeForModel($modelName);
        $record = $this->repository->find($id);
        
        if (!$record) {
            return response()->json(['message' => 'Not found'], 404);
        }
        
        return response()->json($record);
    } catch (\Exception $e) {
        Log::error("Error in show: " . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

// 2. destroy($modelName, $id)
public function destroy(string $modelName, $id)
{
    try {
        $this->initializeForModel($modelName);
        $this->repository->destroy($id);
        
        return response()->json(['message' => 'Deleted successfully']);
    } catch (\Exception $e) {
        Log::error("Error in destroy: " . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

// 3. search($request, $modelName)
public function search(Request $request, string $modelName)
{
    try {
        $this->initializeForModel($modelName);
        
        $filters = $request->query('filters', []);
        $sort = $request->query('sort', 'id');
        $direction = $request->query('direction', 'asc');
        $per_page = $request->query('per_page', 15);
        
        $query = $this->repository->applyFilters($filters)
                                 ->orderBy($sort, $direction)
                                 ->paginate($per_page);
        
        return response()->json($query);
    } catch (\Exception $e) {
        Log::error("Error in search: " . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
```

### TAREA 3: Completar FormSchema Routes (1-2 horas)
**Prioridad:** 🟠 ALTA

**Cambios necesarios:**

1. **Agregar prefijo `/api/`**
   ```php
   Route::prefix('api')->group(function() use ($formSchemaModels) {
       foreach ($formSchemaModels as $modelName => $routeName) {
           // todas las rutas aquí
       }
   });
   ```

2. **Separar búsqueda de CRUD**
   ```php
   // CRUD estándar
   Route::apiResource($routeName, FormSchemaController::class);
   
   // Búsqueda especial
   Route::post("{$routeName}/search", [...]);
   ```

3. **Corregir parámetros**
   - Asegurar todos los métodos reciben `$id` cuando necesario
   - Mantener consistencia en nombres de variables

4. **Habilitar autenticación**
   ```php
   // Cambiar de:
   Route::group([], function() {
   
   // A:
   Route::middleware(['auth:sanctum'])->group(function() {
   ```

5. **Completar método searchWithPeople**
   - Línea 77+ está truncada
   - Implementar joins con múltiples modelos

### TAREA 4: Validación y Relaciones (1 hora)
**Prioridad:** 🟡 MEDIA

**Implementar en FormSchemaController:**

```php
// Obtener config de validación del modelo
private function getValidationRules(string $modelName): array
{
    // Leer desde config JSON o modelo
    return [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:people',
        'organization_id' => 'required|exists:organization,id',
    ];
}

// Cargar relaciones automáticamente
private function loadRelations($query, string $modelName)
{
    $relations = [
        'People' => ['organization', 'skills', 'roles'],
        'Skills' => ['organization'],
        'Department' => ['organization'],
    ];
    
    return $query->with($relations[$modelName] ?? []);
}
```

---

## 3. Checklist de Pruebas

### Por cada modelo (People, Skills, Department, Role):
- [ ] GET /api/{model} - retorna lista con paginación
- [ ] GET /api/{model}/{id} - retorna registro específico
- [ ] POST /api/{model} - crea registro con validación
- [ ] PUT /api/{model}/{id} - actualiza record
- [ ] DELETE /api/{model}/{id} - soft delete funciona
- [ ] POST /api/{model}/search - búsqueda con filtros
- [ ] Relaciones cargadas correctamente
- [ ] Respuestas JSON válidas

### Frontend:
- [ ] Página /people carga sin errores
- [ ] Tabla muestra datos
- [ ] Botones CRUD funcionan
- [ ] Filtros trabajan
- [ ] Paginación funciona

---

## 4. Orden de Ejecución Recomendado

```
8:00 - 9:30  → TAREA 1: Depurar People
9:30 - 11:00 → TAREA 2: FormSchemaController métodos faltantes
11:00- 12:00 → TAREA 3: FormSchema Routes arreglos
12:00- 13:00 → TAREA 4: Validación y relaciones
13:00- 14:00 → Testing completo
14:00+       → Documentación y limpieza
```

---

## 5. Archivos Clave a Modificar

**Controllers:**
- `/src/app/Http/Controllers/FormSchemaController.php` (completar métodos)

**Routes:**
- `/src/routes/form-schema-complete.php` (arreglar prefijos, parámetros)
- `/src/routes/api.php` (si es necesario registrar rutas)

**Repositories:**
- `/src/app/Repository/Repository.php` (base con bug en array_map)
- `/src/app/Repository/PeopleRepository.php` (validar implementación)

**Models:**
- `/src/app/Models/People.php` (relaciones)
- `/src/app/Models/Skills.php`, etc.

**Migrations:**
- Revisar constraints y foreign keys

---

## 6. Notas Importantes

### Patrón JSON-Driven CRUD
El objetivo es que al agregar un modelo nuevo, solo necesites:
1. Crear modelo + migration
2. Crear factory (si es necesario datos de prueba)
3. Crear repository
4. Crear config JSONs en `resources/js/pages/{Model}`
5. Agregar al mapeo en `form-schema-complete.php`

**No** necesitas crear controlador ni rutas específicas.

### Soft Deletes
- Asegurar que modelos usen `SoftDeletes`
- Migrations tengan `softDeletes()` en table
- Repository filtre `whereNull('deleted_at')` automáticamente

### Autenticación
- Para DÍA 8 trabajar sin auth (ya está removido)
- Para PRODUCCIÓN: agregar `auth:sanctum` middleware
- Implementar login/token en frontend próximamente

---

## 7. Documentación de Referencia Rápida

📚 **Documentación FormSchema disponible en /docs:**
- `FormSchemaController-Complete-Documentation.md` → Especificación técnica completa
- `FormSchema-Routes-Documentation.md` → Estructura de rutas y mapeo
- `FormSchemaController-Flow-Diagram.md` → Flujo visual del sistema
- `FormSchemaController-Executive-Summary.md` → Resumen de decisiones
- `FormSchemaTestingSystem.md` → Casos de prueba y comandos
- `FormSchemaController-Migration.md` → Patrón de migración de código
- `PATRON_JSON_DRIVEN_CRUD.md` → Patrón arquitectónico general
- `DIA7_RESUMEN_INSTALACION_ENTORNO.md` → Estado previo (Día 7)

**Consulta estos documentos durante la implementación, especialmente si encuentras dudas sobre:**
- Cómo mapear modelos a repositorios
- Validación de requests
- Carga de relaciones
- Paginación y búsqueda con filtros

---

## 8. Timeline Final

**Estado del Día 7:** ✅ Entorno listo, API básica funciona, datos de prueba creados

**Estado esperado Día 8:** ✅ CRUD completo funcional, FormSchema genérico productivo

**Bloqueadores principales resueltos:**
- ✅ SQLite configurado
- ✅ Dependencias instaladas
- ✅ Modelo People funcional
- 🔴 FormSchemaController incompleto
- 🔴 Rutas con bugs de parámetros
- 🔴 Repository error en array_map

**Próximo Día 9:** Autenticación Sanctum + Seeders reales


