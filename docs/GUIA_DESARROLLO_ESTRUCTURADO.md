# Guía de Desarrollo Estructurado - TalentIA

**Versión:** 1.0  
**Basado en:** Ejecución Exitosa Días 1-5 (MVP Backend)  
**Propósito:** Metodología escalable para desarrollo ordenado de módulos complejos

---

## 📋 Índice

1. [Filosofía del Proceso](#1-filosofía-del-proceso)
2. [Fases de Planificación](#2-fases-de-planificación)
3. [Ciclo Diario](#3-ciclo-diario)
4. [Estructura de Documentación](#4-estructura-de-documentación)
5. [Checklist de Progreso](#5-checklist-de-progreso)
6. [Convenciones de Código](#6-convenciones-de-código)
7. [Métricas y Seguimiento](#7-métricas-y-seguimiento)
8. [Escalabilidad a Módulos Complejos](#8-escalabilidad-a-módulos-complejos)
9. [Template Reutilizable](#9-template-reutilizable)

---

## 1. Filosofía del Proceso

### Principios Fundamentales

**1.1 Orden Garantiza Calidad**

```
Arquitectura Sólida → Documentación Clara → Testing Consistente → Ejecución Limpia
```

Cada día construye sobre el anterior sin deuda técnica. No avanzan al Día N+1 hasta que Día N esté 100% verificado.

**1.2 Documentación Primera, Código Después**

- Especificar QUÉ se va a hacer (memories.md)
- Documentar CÓMO se hizo (día-específico.md)
- Código es la implementación de la documentación

**1.3 Verificación Antes de Avanzar**

- ✅ Código sin errores (syntax check)
- ✅ Tests pasando (unit + integration)
- ✅ Endpoints respondiendo correctamente
- ✅ Datos en BD verificados
- Solo entonces: Pasar al siguiente día

**1.4 Una Sola Responsabilidad por Día**

```
Día 1: Base de datos (migraciones + modelos)
Día 2: Datos (seeders + relaciones)
Día 3: Lógica (servicios + algoritmos)
Día 4: API Parte 1 (controllers lectura)
Día 5: API Parte 2 (controllers CRUD)
Día 6: Frontend Core
Día 7: Componentes + Pulido
```

No mezclar responsabilidades. Si el Día 3 requiere cambios DB, crear nota y ejecutar al siguiente.

---

## 2. Fases de Planificación

### Fase 0: Escribir el "Memories" (Pre-Proyecto)

**Duración:** 2-3 horas  
**Output:** `memories.md` (documento de requisitos)

**Estructura de memories.md:**

```markdown
# Memories - [Nombre Módulo]

## 1. Objetivo

¿Qué problema resuelve?

## 2. Alcance MVP

¿Qué features sí incluye? ¿Cuáles no?

## 3. Casos de Uso

Flujos principales de usuario

## 4. Reglas de Negocio

Validaciones y restricciones

## 5. Modelo de Datos

Tablas, relaciones, campos

## 6. API/Endpoints

¿Qué se va a exponer?

## 7. Algoritmos Clave

Si hay lógica compleja, especificar en pseudocódigo

## 8. UI/UX

Páginas principales y componentes

## 9. Datos de Demo

Estructura de ejemplo para testing

## 10. Timeline

Planificación por día (1 semana típicamente)
```

**Ejemplo:** `docs/memories.md` (99K, muy detallado)

---

### Fase 1: Planificación de Sprints (1 semana)

**Duración:** 30 minutos  
**Input:** memories.md  
**Output:** Sprint planning document

**Estructura del Plan:**

```markdown
# Sprint [Nombre Módulo] - Semana [Fecha]

## Objetivo General

[1 frase clara]

## Breakdown por Día

### Día 1: [Tarea Principal]

- [ ] Subtarea 1
- [ ] Subtarea 2
- [ ] Subtarea 3
      **Entregable:** [Qué debe estar 100% listo]
      **Validación:** [Cómo verificar]

### Día 2: [Tarea Principal]

...

### Día 3-7: ...
```

**Ejemplo:** Lo que hicimos en Días 1-5:

- Día 1: Migraciones + Modelos (10 + 7 = 17 archivos)
- Día 2: Seeders (1 archivo con demo data)
- Día 3: Servicios (3 archivos + 3 comandos + 2 tests)
- Día 4: API Controllers Lectura (8 controllers + rutas)
- Día 5: API Controllers CRUD (3 controllers + 7 endpoints)

---

## 3. Ciclo Diario

### Estructura de un Día Típico (8-10 horas)

```
09:00-09:30  Lectura de Plan Diario + Setup
09:30-12:00  Implementación Bloque 1 (2.5 horas)
12:00-13:00  Almuerzo
13:00-16:00  Implementación Bloque 2 (3 horas)
16:00-17:00  Testing + Validación
17:00-18:00  Documentación + Cierre
```

### 3.1 Inicio de Día (09:00-09:30)

**Checklist de Inicio:**

```
1. [ ] Leer plan del día específico (5 min)
2. [ ] Verificar que Día anterior está 100% completo
3. [ ] Clonar/abrir repo con cambios
4. [ ] Terminal limpia (kill background processes)
5. [ ] Tests pasando del día anterior
6. [ ] Revisar documentación requerida

Comando:
  cd /workspaces/talentia/src
  php artisan test
  php artisan route:list
  git status
```

---

### 3.2 Implementación (09:30-16:00)

**Bloques de Trabajo:**

```
Bloque 1 (2.5h): Crear archivos + Lógica básica
  - Crear migraciones/models/controllers/services
  - Implementación core
  - Syntax check

Bloque 2 (3h): Completar + Testing
  - Terminar funcionalidad
  - Crear tests
  - Validar endpoints
```

**Patrón de Creación (para cada archivo):**

```php
// 1. Crear archivo
php artisan make:[migration|model|controller|command]

// 2. Implementar lógica
// - Usar type hints (TypeScript para frontend)
// - Agregar docstrings
// - Validaciones claras

// 3. Crear tests inmediatamente
php artisan make:test [TestName] --feature

// 4. Verificar
php artisan test
php artisan route:list (si es controller)
```

**Ejemplo Día 1: Crear Migración + Modelo**

```bash
# Crear migración
php artisan make:migration create_skills_table

# Implementar (edit file)
Schema::create('skills', function (Blueprint $table) { ... })

# Crear modelo
php artisan make:model Skill

# Implementar relaciones
class Skill extends Model {
    public function roles() { ... }
}

# Ejecutar
php artisan migrate

# Verificar
php artisan tinker
>>> App\Models\Skill::count()
```

---

### 3.3 Validación (16:00-17:00)

**Checklist de Validación Diaria:**

```
[ ] Código sin errores
    php artisan lint        # Si existe
    find . -name "*.php" | xargs php -l

[ ] Tests pasando
    php artisan test

[ ] Migraciones ejecutadas (si aplica)
    php artisan migrate:status

[ ] Rutas registradas (si API)
    php artisan route:list | grep [patrón]

[ ] Datos verificables
    php artisan tinker
    >>> [verificar modelos/datos]

[ ] Endpoints responden (si API)
    curl http://localhost:8000/api/[endpoint]

[ ] No hay warnings en logs
    tail -f storage/logs/laravel.log
```

**Salida esperada:**

```
✅ 0 syntax errors
✅ 5/5 tests passing
✅ Migrations up to date
✅ 17 routes registered
✅ 25 records in database
✅ All endpoints responding 200/201
✅ No errors in logs
```

---

### 3.4 Documentación + Cierre (17:00-18:00)

**Crear Documento Día-Específico:**

```markdown
# Día [N]: [Tarea Principal]

**Fecha:** [Fecha]
**Status:** ✅ COMPLETADO

## Lo Que Se Hizo

### Archivos Creados

- [ ] app/Models/[Model].php
- [ ] app/Services/[Service].php
- ...

### Tests Creados

- [ ] tests/Feature/[Test].php
- [ ] tests/Unit/[Test].php

### Validación

- ✅ 0 syntax errors
- ✅ 5/5 tests PASS
- ✅ X migraciones ejecutadas
- ✅ Y modelos funcionales
- ✅ Z endpoints respondiendo

## Próximo Día

[Resumen de qué viene]

## Notas

[Cualquier issue o decisión importante]
```

**Ejemplo:** [dia1_migraciones_modelos_completados.md](dia1_migraciones_modelos_completados.md)

**Actualizar "Estado Actual":**

```bash
# Editar estado_actual_mvp.md
# Marcar Día [N] como ✅ COMPLETADO
# Agregar resumen de entregables
# Actualizar progreso visual

# Commit
git add -A
git commit -m "Día [N] completado: [Resumen]"
```

---

## 4. Estructura de Documentación

### Jerarquía de Documentos

```
📁 docs/
├── 📄 memories.md                      ← Requisitos (ANTES de empezar)
├── 📄 estado_actual_mvp.md             ← Status central (ACTUALIZAR DIARIO)
├── 📁 planning/
│   └── 📄 sprint_[nombre].md           ← Plan de semana
├── 📁 diarios/
│   ├── 📄 dia1_[tarea].md
│   ├── 📄 dia2_[tarea].md
│   └── 📄 ...
├── 📁 api/
│   ├── 📄 endpoints.md
│   └── 📄 Postman_collection.json
├── 📁 arquitectura/
│   ├── 📄 modelos.md
│   ├── 📄 servicios.md
│   └── 📄 algoritmos.md
├── 📁 references/
│   ├── 📄 CHEATSHEET_COMANDOS.md
│   └── 📄 CHECKLIST_TAREAS.md
└── 📄 README.md                        ← Índice navegable
```

### Documentación Obligatoria por Día

**Siempre crear:**

1. **Documento Día-Específico** (Ej: `dia3_services_logica_negocio.md`)
    - Qué se hizo
    - Archivos creados
    - Tests y validación
    - Próximos pasos

2. **Actualización de `estado_actual_mvp.md`**
    - Marcar día como COMPLETADO
    - Actualizar progreso visual
    - Agregar resumen entregables

3. **Actualización de `README.md`** (si es necesario)
    - Agregar referencia a nuevo doc
    - Actualizar índice

---

## 5. Checklist de Progreso

### Template de Checklist Diario

```markdown
# Checklist Día [N] - [Tarea]

## Pre-Requisitos

- [ ] Día anterior 100% completado
- [ ] BD en estado consistente
- [ ] Tests heredados aún pasan
- [ ] Repo actualizado

## Implementación

- [ ] Crear archivos necesarios
- [ ] Implementar lógica
- [ ] Type hints/docstrings
- [ ] Validaciones

## Testing

- [ ] Unit tests creados
- [ ] Integration tests creados
- [ ] Todos tests PASS
- [ ] Code coverage > 80%

## Validación

- [ ] Syntax: 0 errors
- [ ] Migraciones: ejecutadas
- [ ] Modelos: funcionan con tinker
- [ ] Endpoints: responden (200/201)
- [ ] Datos: verificables

## Documentación

- [ ] Documento día-específico
- [ ] estado_actual_mvp.md actualizado
- [ ] README.md actualizado (si aplica)
- [ ] Ejemplos de uso

## Cierre

- [ ] Commit con descripción clara
- [ ] Tag opcional: git tag -a "dia-[n]"
- [ ] Resumen para Día [N+1]

## Métricas

- ⏱️ Tiempo invertido: \_\_\_ horas
- 📊 Archivos creados: \_\_\_
- ✅ Tests PASS: **_/_**
- 🚀 Endpoints funcionales: \_\_\_
```

**Uso:** Copiar al inicio de cada día, marcar con ✅ al completar.

---

## 6. Convenciones de Código

### 6.1 Arquitectura de Capas (FormSchema Pattern + Repository)

**Patrón: Request → Controller → Repository → Model → Database**

```
┌─────────────────────────────────────────────────┐
│  HTTP Request (form-schema-complete.php)       │
│  GET /api/people → FormSchemaController        │
└────────────────┬────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────┐
│  FormSchemaController (Orquestación)            │
│  ├─ Recibir Request HTTP                       │
│  ├─ Inicializar modelo/repositorio             │
│  ├─ Delegar lógica a repository                │
│  └─ Retornar Response JSON                     │
│                                                 │
│  public function index(Request $req, $model) {  │
│      $this->initializeForModel($model);        │
│      return $this->repository->index($req);    │
│  }                                              │
└────────────────┬────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────┐
│  {Model}Repository (Persistencia)               │
│  ├─ PeopleRepository extends Repository        │
│  ├─ RoleRepository extends Repository          │
│  ├─ SkillRepository extends Repository         │
│                                                 │
│  Métodos CRUD heredados:                       │
│  ├─ public function store($request) { ... }    │
│  ├─ public function show($request, $id) { ... }
│  ├─ public function update($request) { ... }   │
│  ├─ public function destroy($id) { ... }       │
│  └─ public function search($request) { ... }   │
│                                                 │
│  Puede overridear métodos para lógica custom   │
└────────────────┬────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────┐
│  {Model} Eloquent (Mapeo a BD)                  │
│  ├─ People Model                               │
│  ├─ Role Model                                 │
│  ├─ Skill Model                                │
│  └─ SELECT * FROM [tabla]                      │
└─────────────────────────────────────────────────┘
```

**¿Por qué esta arquitectura?**

| Ventaja | Motivo |
|---------|--------|
| **Testeable** | Mock Repository sin tocar BD |
| **Reutilizable** | 1 FormSchemaController para 10+ modelos |
| **Extensible** | Agregar lógica custom en {Model}Repository |
| **Mantenible** | Lógica BD en Repository, no dispersa |
| **Escalable** | Agregar modelo: solo 1 Repository, sin controlador |

### 6.2 Creación de Nuevo Modelo CRUD (10 minutos)

### 6.1 Nomenclatura

```
Modelos:        PascalCase (Skill, People, Role)
Migrations:     snake_case_timestamp (2025_12_27_100000_create_skills_table.php)
Controllers:    [Resource]Controller (SkillController, PeopleController)
Services:       [Action]Service (GapAnalysisService, MatchingService)
Commands:       kebab-case (gap:analyze, devpath:generate)
Tests:          [Feature]Test.php (GapAnalysisServiceTest.php)
Routes:         kebab-case (/api/gap-analysis, /api/People)
Composables:    use[Purpose] (useApi, useAuth)
Components:     PascalCase.vue (SkillsTable.vue, GapAnalysisCard.vue)
Pages:          PascalCase.vue (PeopleList.vue, RoleDetail.vue)
```

### 6.2 Estructura de Archivos

```php
// Controllers
class [Resource]Controller extends Controller {
    public function index()     // GET
    public function show($id)   // GET /{id}
    public function store()     // POST
    public function update($id) // PUT/PATCH
    public function destroy($id) // DELETE
}

// Services
class [Action]Service {
    public function execute(): ResultType {
        // Documentado, con type hints
    }
}

// Models
class Model extends Model {
    protected $fillable = [];
    protected $casts = [];

    // Relaciones
    public function relationship() { ... }

    // Scopes
    public function scopeActive() { ... }
}
```

### 6.3 Validaciones

```php
// En Controllers
$validated = $request->validate([
    'field' => ['required', 'string', 'max:255'],
    'email' => ['required', 'email', 'unique:users'],
    'role_id' => ['required', 'integer', 'exists:roles,id'],
]);

// En Models
protected $rules = [
    'name' => 'required|string|max:255',
];
```

### 6.4 Respuestas API

```php
// GET: 200 OK
return response()->json($data);

// POST: 201 Created
return response()->json($data, 201);

// Validación fallida: 422
return response()->json(['errors' => $errors], 422);

// No encontrado: 404
return response()->json(['error' => 'Not found'], 404);

// Error del servidor: 500
return response()->json(['error' => 'Server error'], 500);
```

---

## 7. Métricas y Seguimiento

### 7.1 Tabla de Progreso

**Template:**

```markdown
# Progreso Sprint [Nombre]

| Día | Tarea | Archivos | Tests | Endpoints | Status |
| --- | ----- | -------- | ----- | --------- | ------ |
| 1   | DB    | 17       | 0     | 0         | ✅     |
| 2   | Seeds | 1        | 0     | 0         | ✅     |
| 3   | Logic | 8        | 2     | 0         | ✅     |
| 4   | API-1 | 8        | 2     | 10        | ✅     |
| 5   | API-2 | 3        | 0     | 7         | ✅     |
| 6   | FE-1  | 9        | 0     | 0         | ⏳     |
| 7   | FE-2  | 7        | 2     | 0         | ⏳     |
```

### 7.2 KPIs a Monitorear

```
📈 Productividad
   - Archivos/hora
   - Líneas de código/día
   - Tests creados/día

✅ Calidad
   - Tests PASS %
   - Code coverage %
   - Syntax errors: 0
   - Build warnings: 0

⏱️ Timeline
   - Horas estimadas vs reales
   - Slack buffer (20-30%)
   - Riesgo de retraso: bajo/medio/alto

📊 Técnico
   - Endpoints funcionales
   - Validaciones en place
   - Documentación %
```

---

## 8. Escalabilidad a Módulos Complejos

### 8.1 Cuando Aumenta Complejidad

**Síntomas:**

- Más de 20 archivos por día
- Algoritmos complejos (> 50 líneas)
- Múltiples dependencias entre módulos
- Tests no son suficientes

**Respuesta:**

1. **Dividir en Sub-Sprints**

    ```
    Sprint Principal (1 semana)
    ├─ Sprint 1: Core funcionalidad (3 días)
    ├─ Sprint 2: Integraciones (2 días)
    └─ Sprint 3: Optimización (2 días)
    ```

2. **Aumentar Documentación**

    ```
    Per módulo:
    - Algorithm spec (pseudocódigo)
    - Architectural diagrams (Mermaid)
    - Integration guide (cómo conecta)
    - Testing strategy (qué validar)
    ```

3. **Crear Equipo de Revisión**

    ```
    Daily standup (15 min):
    - Qué hicimos ayer
    - Qué hacemos hoy
    - Blockers

    End-of-day review (30 min):
    - Code review
    - Validación
    - Aprobación para día siguiente
    ```

4. **Agregar Capas de Testing**
    ```
    Unit Tests        → Lógica individual
    Integration Tests → Entre componentes
    E2E Tests         → Flujo completo
    Performance Tests → Si es crítico
    ```

### 8.2 Template para Módulos Grandes

```markdown
# Sprint [Módulo Complejo] - [Fechas]

## Descripción General

[Qué es, por qué importa]

## Requisitos Críticos

[3-5 requisitos must-have]

## Riesgos Identificados

[ ] Risk 1: [Impacto, Mitigación]
[ ] Risk 2: ...

## Desglose en Sub-Sprints

### Sub-Sprint 1: [Core]

Días 1-3

- [ ] Tarea 1.1
- [ ] Tarea 1.2
- [ ] Validación 1

### Sub-Sprint 2: [Integration]

Días 4-5

- [ ] Tarea 2.1
- [ ] Tarea 2.2
- [ ] Validación 2

### Sub-Sprint 3: [Optimization]

Días 6-7

- [ ] Tarea 3.1
- [ ] Validación 3

## Dependencias

[Módulos que deben estar listos primero]

## Success Criteria

- [ ] Todos tests PASS (> 90% coverage)
- [ ] API endpoints documentados
- [ ] Zero critical bugs
- [ ] Performance acceptable
```

---

## 9. Template Reutilizable

### 9.1 Checklist de Inicio de Módulo

```markdown
# Checklist Nuevo Módulo: [Nombre]

## Fase 0: Requisitos

- [ ] memories.md creado (requisitos detallados)
- [ ] Stakeholders alineados
- [ ] Mockups/wireframes (si aplica)
- [ ] Datos de ejemplo definidos

## Fase 1: Planificación

- [ ] Sprint plan creado (breakdown por día)
- [ ] Riesgos identificados
- [ ] Timeline acordado
- [ ] Equipo asignado

## Fase 2: Setup

- [ ] Rama git creada (git checkout -b feature/[nombre])
- [ ] Directorio estructura creada
- [ ] Documentación directorio creado
- [ ] CI/CD configurado

## Fase 3: Ejecución Diaria

[Usar Checklist Diario anterior]

## Fase 4: Integración

- [ ] Merge a rama principal
- [ ] Tests de integración pasan
- [ ] Documentación final actualizada
- [ ] Deployment verificado

## Fase 5: Post-Mortem

- [ ] Retrospectiva realizada
- [ ] Lecciones documentadas
- [ ] Proceso mejorado
```

### 9.2 Template de Sprint Plan

```markdown
# Sprint [Módulo] - Semana [Fecha]

## Visión

[1-2 frases de qué se va a lograr]

## Objetivo Medible

[Qué va a estar 100% funcional al final]

## Días Asignados

[Cuántos días se dedican]

## Daily Breakdown

### Día 1: [Tarea]

**Objetivo:** [Qué debe estar listo al final del día]
**Archivos:** [Cuántos esperas crear]
**Tests:** [Cuántos tests esperas crear]
**Validación:** [Cómo verificarás]
**Entregable:** [Link a documento/commit]

### Día 2: [Tarea]

...

## Riesgos

- [ ] Risk 1: [Mitigation]
- [ ] Risk 2: [Mitigation]

## Success Criteria

- [ ] Criterio 1
- [ ] Criterio 2
- [ ] Criterio 3

## Notes

[Cualquier observación importante]
```

---

## 10. Caso de Estudio: MVP Días 1-5

### Cómo Aplicamos Esta Estructura

```
Semana 1: MVP Backend

Día 1: Database ✅
  Archivos: 10 migraciones + 7 modelos = 17
  Tests: 0 (DB structure is validation)
  Validación: migrate:status, tinker
  Documento: dia1_migraciones_modelos_completados.md

Día 2: Seeders ✅
  Archivos: 1 seeder grande + ajustes
  Tests: 0 (data is validation)
  Validación: db:seed, tinker count()
  Documento: dia2_seeders_completados.md

Día 3: Services ✅
  Archivos: 3 services + 3 commands
  Tests: 2 Pest feature tests
  Validación: tests PASS, artisan commands work
  Documento: dia3_services_logica_negocio.md

Día 4: API Part 1 ✅
  Archivos: 8 controllers
  Tests: Tests en controllers (validation)
  Validación: route:list, curl tests
  Documento: Implícito en estado_actual_mvp.md

Día 5: API Part 2 ✅
  Archivos: 3 controllers + routes
  Tests: Postman collection
  Validación: 17 endpoints functional
  Documento: dia5_api_endpoints.md + dia5_resumen_entrega.md

Resultado: 100% Backend, 17 endpoints, documentado
```

---

## Conclusión

**Este proceso garantiza:**

✅ **Orden:** Cada día tiene responsabilidad clara  
✅ **Verificación:** Nada avanza sin 100% completitud  
✅ **Documentación:** Cada paso queda registrado  
✅ **Escalabilidad:** Se adapta a módulos complejos  
✅ **Trazabilidad:** Puedes saber qué pasó cada día  
✅ **Reutilización:** Próximos módulos usan el mismo proceso

**Aplicable a:**

- Nuevos módulos de competencias
- Funcionalidades de IA/IA
- Integraciones externas
- Refactoring de código existente

---

**Documento Base para Futuros Sprints**  
Actualizar según aprendizajes de nuevas ejecuciones.
