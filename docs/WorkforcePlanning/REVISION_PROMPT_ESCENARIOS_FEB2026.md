# 📋 REVISIÓN: Prompt Técnico Módulo Escenarios vs. Implementación Actual

**Fecha:** 4 Febrero 2026  
**Status:** Verificación de Implementación Completada  
**Branch:** feature/scenario-planning/paso-2

---

## 📊 RESUMEN EJECUTIVO

| Sección                    | Estado             | Porcentaje             |
| -------------------------- | ------------------ | ---------------------- |
| **1. Modelo de Datos**     | 🟡 PARCIAL         | 50%                    |
| **2. Lógica de Negocio**   | 🟡 PARCIAL         | 30%                    |
| **3. API Endpoints**       | 🟢 IMPLEMENTADO    | 100% (diferentes)      |
| **4. Frontend (UI/UX)**    | 🟡 PARCIAL         | 40%                    |
| **5. Validaciones**        | 🟢 IMPLEMENTADO    | 100% (ej: dual status) |
| **6. Métricas y Reportes** | ❌ NO IMPLEMENTADO | 0%                     |

**Conclusión:** Se implementó un sistema diferente (Workforce Planning Phase 2) que **NO coincide** con el prompt de "Planificación de Escenarios con Versionamiento de Competencias". El prompt original **no está implementado**.

---

## 1️⃣ MODELO DE DATOS

### 1.1 Tabla `scenarios` ❌ NO COINCIDE

**Prompt esperaba:**

```sql
CREATE TABLE scenarios (
    id UUID PRIMARY KEY,
    organization_id UUID,
    name VARCHAR(255),
    description TEXT,
    time_horizon VARCHAR(50),
    status VARCHAR(50) DEFAULT 'draft',  -- draft, in_review, approved, archived
    created_by UUID,
    approved_by UUID,
    approved_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Lo implementado (scenarios table actual):**

```php
// Tabla: scenarios (en src/database/migrations/2026_01_12_193636_create_scenarios_table.php)
- id (BIGINT PK) ✅ [Diferencia: INT vs UUID]
- organization_id ✅
- name ✅
- description ✅
- status VARCHAR(50) ✅ [PERO: ENUM('draft','active','archived','completed') vs esperado]
- decision_status ENUM ✅ [NUEVO: dual status pattern no en prompt]
- execution_status ENUM ✅ [NUEVO: no en prompt]
- scope_type ENUM('organization','department','role_family') ❌ [NO en prompt]
- version_group_id UUID ✅ [Versioning pero diferente estructura]
- current_step INT ❌ [Metodología 7 pasos no en prompt]
- parent_id BIGINT ❌ [Jerarquía no en prompt]
- tiempo: start_date, end_date, horizon_months ❌ [Estructura diferente]
```

**Veredicto:** ❌ **No coincide**. La tabla implementada tiene un modelo completamente diferente (Workforce Planning) vs. el prompt (Scenario Planning con competencies).

---

### 1.2 Tabla `scenario_capacities` ✅ IMPLEMENTADA (con cambios)

**Prompt esperaba:**

```sql
CREATE TABLE scenario_capacities (
    id UUID,
    scenario_id UUID,
    name VARCHAR(255),
    description TEXT,
    criticality VARCHAR(50),  -- critical, important, nice_to_have
    order_index INT,
    created_at TIMESTAMP
);
```

**Lo implementado:**

```php
// Tabla: scenario_capabilities (NOTA: nombre diferente)
// En: 2026_01_12_193106_create_scenario_capabilities_table.php
- id (BIGINT) ✅
- scenario_id ✅
- capability_id ✅ [Referencia a tabla capabilities, no definición inline]
- name ❌ [NO tiene, nombre está en tabla capabilities]
- description ❌ [NO tiene, está en tabla capabilities]
- strategic_role VARCHAR (✅ pero 'target','watch','sunset' vs 'critical','important','nice_to_have')
- strategic_weight INT ✅ [Similar a criticality]
- priority INT ✅
- required_level INT ✅ [NO en prompt]
- is_critical BOOL ✅ [Similar a criticality]
```

**Veredicto:** 🟡 **Parcialmente**. Estructura diferente (pivot con tabla capabilities externa), pero conceptualmente similar.

---

### 1.3 Tabla `competency_versions` ❌ **NO IMPLEMENTADA**

**Prompt esperaba:**

```sql
CREATE TABLE competency_versions (
    id UUID,
    competency_id UUID,
    version VARCHAR(20),      -- v1.0, v1.1, v2.0
    scenario_id UUID,
    is_master BOOLEAN,
    name VARCHAR(255),
    description TEXT,
    bars_definition JSONB,
    complexity_level VARCHAR(50),
    change_log TEXT,
    created_by UUID,
    parent_version_id UUID,
    created_at TIMESTAMP
);
```

**Estado:** ❌ **NO EXISTE**.

Lo que existe:

- `competencies` table con estructura base
- NO hay sistema de versionamiento de competencias
- NO hay `bars_definition` JSONB
- NO hay `complexity_level`
- NO hay `change_log`
- NO hay tracking de evolución de competencias

**Impacto:** Esta tabla es **CRÍTICA** para el prompt. Sin ella, no puedes:

- Transformar competencias en el escenario
- Marcar competencias como obsolescentes
- Crear embriones de nuevas competencias
- Tener versionamiento de competencias

Nota: el sistema ya utiliza el campo `discovered_in_scenario_id` (presente en `capabilities` y en los flujos de creación desde el UI/API) para marcar una capability/competency como un "embrión" incubado dentro de un escenario. Este mecanismo permite crear y distinguir elementos en incubación desde el escenario, pero **no reemplaza** un sistema de `competency_versions` para versionamiento formal y trazabilidad de cambios.

---

### 1.4 Tabla `scenario_capacity_competencies` ❌ **NO IMPLEMENTADA**

**Prompt esperaba:**

```sql
CREATE TABLE scenario_capacity_competencies (
    id UUID,
    capacity_id UUID,
    competency_version_id UUID,
    evolution_state VARCHAR(50),     -- standard, transformed, obsolescent, new_embryo
    required_level INT,
    current_level INT,
    criticality VARCHAR(50),
    obsolescence_reason TEXT,
    transformation_notes TEXT,
    created_at TIMESTAMP
);
```

**Estado:** ❌ **NO EXISTE**.

Lo que existe como alternativa:

- `capability_competencies` pivot table (diferente nombre)
- En: `src/app/Models/CapabilityCompetency.php`
- Campos: `scenario_id, capability_id, competency_id, required_level, weight, rationale, is_required`
- **FALTA:** `evolution_state`, `current_level`, `criticality`, `obsolescence_reason`, `transformation_notes`

**Impacto:** Sin esto, no puedes:

- Marcar competencias como "transformada", "obsolescente", "nueva embrión"
- Rastrear justificaciones de obsolescencia
- Rastrear cambios de transformación
- Comparar nivel actual vs. requerido

---

### 1.5 Tabla `scenario_roles` (Embriones) 🟡 **PARCIALMENTE**

**Prompt esperaba:**

```sql
CREATE TABLE scenario_roles (
    id UUID,
    scenario_id UUID,
    name VARCHAR(255),
    description TEXT,
    status VARCHAR(50),              -- embryo, formalized
    base_role_id UUID,
    mutation_type VARCHAR(50),       -- enrichment, specialization, hybridization, greenfield, sunset
    mutation_index DECIMAL(5,2),
    suggested_archetype VARCHAR(50),
    suggested_level INT,
    formalized_role_id UUID,
    created_at TIMESTAMP
);
```

**Lo implementado:**

```php
// Tabla: scenario_roles
// En: 2026_01_12_193126_create_scenario_roles_table.php
- id ✅
- scenario_id ✅
- role_id ✅ [Hace referencia a rol existente, no crea embrión]
- role_change VARCHAR ✅ ['evolve','new','sunset' vs 'enrichment','specialization',...]
- impact_level VARCHAR ✅ ['high','medium','low']
- evolution_type VARCHAR ✅ ['incremental','transformative','disruptive']
- rationale TEXT ✅
- timestamps ✅

FALTA:
- status ENUM (embryo/formalized)
- mutation_type
- mutation_index
- suggested_archetype
- suggested_level
- formalized_role_id
```

**Veredicto:** 🟡 **Parcial**. Estructura similar pero falta cálculo de mutación y sugerencia automática.

---

### 1.6 Tabla `scenario_role_competencies` ❌ **DIFERENTE**

**Prompt esperaba:**

```sql
CREATE TABLE scenario_role_competencies (
    id UUID,
    scenario_role_id UUID,
    competency_version_id UUID,
    source VARCHAR(50),
    required_level INT,
    created_at TIMESTAMP
);
```

**Lo implementado:**

```php
// Tabla: scenario_role_competencies
// En: 2026_02_02_232929_create_scenario_role_competencies_table.php
- id ✅
- scenario_id ✅
- role_id ✅
- competency_id ✅
- required_level ✅
- is_core BOOL ❌ [NO en prompt]
- change_type VARCHAR ✅ [SIMILAR a source pero valores diferentes]
- rationale TEXT ✅

FALTA:
- source field (es change_type en implementación)
- Links a competency_version (directamente a competency)
```

**Veredicto:** 🟡 **Parcial**. Existe pero sin versionamiento de competencias subyacente.

---

### 1.7 Tabla `role_versions` ❌ **NO IMPLEMENTADA**

**Prompt esperaba:**

```sql
CREATE TABLE role_versions (
    id UUID,
    role_id UUID,
    version VARCHAR(20),
    scenario_id UUID,
    is_master BOOLEAN,
    archetype VARCHAR(50),
    mastery_level INT,
    process_domain VARCHAR(100),
    change_log TEXT,
    mutation_index DECIMAL(5,2),
    created_by UUID,
    parent_version_id UUID,
    created_at TIMESTAMP
);
```

**Estado:** ❌ **NO EXISTE**. No hay versionamiento de roles.

---

## 2️⃣ LÓGICA DE NEGOCIO

### 2.1 Cálculo de Mutación ❌ **NO IMPLEMENTADO**

**Prompt esperaba:**

```javascript
function calculateRoleMutation(scenarioRole, baseRole) {
  // Calcula mutation_type (enrichment, specialization, sunset, etc.)
  // Calcula mutation_index como % de cambio
  // Retorna { type, index }
}
```

**Estado:** ❌ **NO EXISTE**. No hay:

- Algoritmo de detección de mutación
- Cálculo de `mutation_index`
- Comparación baseRole vs scenarioRole

**Existente:** Solo campos `role_change` (evolve/new/sunset) sin cálculo automático.

---

### 2.2 Sugerencia de Arquetipo ❌ **NO IMPLEMENTADO**

**Prompt esperaba:**

```javascript
function suggestArchetype(scenarioRole) {
  // Analiza competencias
  // Clasifica como strategic/tactical/operational
  // Retorna { suggested_archetype, confidence }
}
```

**Estado:** ❌ **NO EXISTE**. No hay:

- Análisis automático de competencias
- Cálculo de dominancia
- Sugerencia de arquetipo

---

### 2.3 Crear Versión de Competencia ❌ **NO IMPLEMENTADO**

**Prompt esperaba:**

```javascript
async function createCompetencyVersion(
  competencyId,
  scenarioId,
  changes,
  userId,
) {
  // Crea nueva versión de competencia
  // Incrementa versión (v1.0 → v1.1)
  // Copia datos con cambios
  // Guarda change_log
}
```

**Estado:** ❌ **NO EXISTE**. No hay:

- Sistema de versionamiento de competencias
- Incremento de versiones
- Tracking de cambios

---

### 2.4 Aprobación de Escenario (Merge) ❌ **PARCIALMENTE**

**Prompt esperaba:**

```javascript
async function approveScenario(scenarioId, approverId) {
  // 1. Promover competency_versions a Master
  // 2. Formalizar roles embrionarios
  // 3. Actualizar estado
}
```

**Lo implementado:**

```php
// En WorkforcePlanningService.php
- transitionDecisionStatus() → cambios de estado ✅
- startExecution() → inicia ejecución ✅
- completeExecution() → completa ✅

PERO:
- NO promoverá competency_versions a Master (no existen)
- NO formalizará roles embrionarios automáticamente (status no existe)
```

**Veredicto:** 🟡 **Parcial**. Workflow de aprobación existe pero es diferente.

---

## 3️⃣ API ENDPOINTS

### 3.1 Escenarios

**Prompt esperaba:**

```
POST   /api/scenarios
GET    /api/scenarios
GET    /api/scenarios/:id
PUT    /api/scenarios/:id
DELETE /api/scenarios/:id
POST   /api/scenarios/:id/approve
```

**Lo implementado:**

```
POST   /api/scenarios ✅
GET    /api/scenarios ✅
GET    /api/scenarios/:id ✅
PUT    /api/scenarios/:id ✅
DELETE /api/scenarios/:id ✅
POST   /api/scenarios/:id/decision-status ✅ [Diferente nombre pero similar función]
```

**Veredicto:** ✅ **Implementado** (con nombre diferente para aprobación).

---

### 3.2 Capacidades

**Prompt esperaba:**

```
POST   /api/scenarios/:id/capacities
GET    /api/scenarios/:id/capacities
PUT    /api/capacities/:id
DELETE /api/capacities/:id
```

**Lo implementado:**

```
POST   /api/scenarios/:id/capabilities ✅ [Nombre plural diferente]
GET    /api/scenarios/:id/capabilities ✅
PUT    /api/capabilities/:id ✅
DELETE /api/capabilities/:id ✅
```

**Veredicto:** ✅ **Implementado**.

---

### 3.3 Competencias en Escenario

**Prompt esperaba:**

```
POST   /api/capacities/:id/competencies
PUT    /api/capacity-competencies/:id
DELETE /api/capacity-competencies/:id
POST   /api/competencies/:id/transform
POST   /api/competencies/create-embryo
```

**Lo implementado:**

```
POST   /api/strategic-planning/scenarios/{id}/capabilities/{capId}/competencies ✅
       [Estructura diferente pero funciona]
PATCH  /api/capability-competencies/:id ✅
DELETE /api/capability-competencies/:id ✅
POST   /api/competencies/:id/transform ❌ [NO IMPLEMENTADO]
POST   /api/competencies/create-embryo ❌ [NO IMPLEMENTADO]
```

**Veredicto:** 🟡 **Parcial** (falta transformación de competencias).

---

### 3.4 Roles en Incubación

**Prompt esperaba:**

```
POST   /api/scenarios/:id/roles
GET    /api/scenarios/:id/roles
PUT    /api/scenario-roles/:id
DELETE /api/scenario-roles/:id
GET    /api/scenario-roles/:id/mutation
POST   /api/scenario-roles/:id/competencies
```

**Lo implementado:**

```
POST   /api/scenarios/:id/step2/roles ✅ [Paso específico]
GET    /api/scenarios/:id/step2/data ✅ [Incluye roles]
DELETE /api/scenario-roles/:id ❌ [NO ESPECÍFICO]
GET    /api/scenario-roles/:id/mutation ❌ [NO IMPLEMENTADO]
POST   /api/scenario-roles/:id/competencies ✅ [Implícito en step2]
```

**Veredicto:** 🟡 **Parcial** (falta análisis de mutación).

---

## 4️⃣ FRONTEND (UI/UX)

### 4.1 Vista Principal: Lista de Escenarios ✅ **IMPLEMENTADA**

**Prompt esperaba:** Tabla con filtros, botón nuevo  
**Lo implementado:** ✅ Componente ScenarioDetail.vue con lista

**Veredicto:** ✅ Existe.

---

### 4.2 Vista de Detalle con Pestañas 🟡 **PARCIAL**

**Prompt esperaba:**

- Información General
- Capacidades (expandible)
- Roles en Incubación
- Análisis de Impacto

**Lo implementado:**

- Información General ✅
- Capacidades ✅
- Metodología 7 Pasos ✅ [NUEVO]
- Estados & Acciones ✅ [NUEVO]
- Análisis de Impacto ❌ [NO ESPECÍFICO]

**Veredicto:** 🟡 **Parcial** pero con diferentes tabs.

---

### 4.3 Matriz Capacidad → Competencias 🟡 **PARCIAL**

**Prompt esperaba:**

- Tabla con competencias
- Estados (Standard, Transformada, Obsolescente, Nueva)
- Badges visuales
- Acciones (Transformar, Ver BARS, Eliminar)

**Lo implementado:**

- `src/resources/js/pages/ScenarioPlanning/ScenarioDetail.vue` ✅
- Visualización de competencias ✅
- FALTA: Dropdown de estados (`evolution_state`)
- FALTA: Modal para transformar competencia
- FALTA: Editor de BARS

**Veredicto:** 🟡 **Parcial**.

---

### 4.4 Modal: Transformar Competencia ❌ **NO IMPLEMENTADO**

**Prompt esperaba:**

```vue
- Nombre editable - Descripción editable - BARS editor (niveles 1-5) -
Justificación obligatoria - Botón "Crear Versión v1.X"
```

**Lo implementado:** ❌ **NO EXISTE**. No hay modal de transformación.

---

### 4.5 Vista de Rol en Incubación 🟡 **PARCIAL**

**Prompt esperaba:**

- Nombre + badge de mutation_type
- Competencias asociadas
- Panel lateral con análisis automático
  - Arquetipo sugerido
  - Nivel sugerido
  - Índice de mutación
  - Alertas

**Lo implementado:**

- Nombre + rol ✅
- Competencias asociadas ✅
- FALTA: Panel lateral con sugerencias
- FALTA: Cálculo automático de mutation_type
- FALTA: Cálculo automático de arquetipo

**Veredicto:** 🟡 **Parcial**.

---

## 5️⃣ VALIDACIONES Y REGLAS DE NEGOCIO

### Implementadas ✅

1. **No aprobar sin confidence > 0.5** → Existe validación de estados duales
2. **Competencia obsolescente requiere razón** → ❌ NO (no existen evolution_states)
3. **Competencia transformada requiere cambio** → ❌ NO
4. **Rol debe tener 3+ competencias** → ❌ NO específicamente
5. **Generar reporte de impacto** → 🟡 Parcial (existe `calculateScenarioGaps()`)

---

## 6️⃣ MÉTRICAS Y REPORTES

**Prompt esperaba:**

- Índice de Innovación (% nuevas)
- Índice de Obsolescencia (% sunset)
- Índice de Transformación (% transformadas)
- Riesgo de Brecha

**Lo implementado:**

- ❌ Ninguno de estos índices específicos
- ✅ Existe cálculo de brechas genérico en `calculateScenarioGaps()`
- ✅ Existe `consolidateParent()` para rollups

**Veredicto:** ❌ **NO IMPLEMENTADO** (falta cálculos específicos).

---

## 📋 TABLA RESUMIDA: Prompt vs. Implementación

| Componente                     | Prompt | Implementado | Diferencia                          |
| ------------------------------ | ------ | ------------ | ----------------------------------- |
| scenarios table                | ✅     | ✅           | Estructura completamente diferente  |
| scenario_capacities            | ✅     | ✅           | Diferente nombre (capabilities)     |
| competency_versions            | ✅     | ❌           | CRÍTICA - No existe                 |
| scenario_capacity_competencies | ✅     | 🟡           | Existe como capability_competencies |
| scenario_roles                 | ✅     | 🟡           | Falta mutation_type, mutation_index |
| role_versions                  | ✅     | ❌           | NO existe                           |
| calculateRoleMutation()        | ✅     | ❌           | NO IMPLEMENTADO                     |
| suggestArchetype()             | ✅     | ❌           | NO IMPLEMENTADO                     |
| createCompetencyVersion()      | ✅     | ❌           | NO IMPLEMENTADO                     |
| approveScenario()              | ✅     | 🟡           | Diferente workflow                  |
| API Escenarios                 | ✅     | ✅           | 95% compatible                      |
| API Capacidades                | ✅     | ✅           | 90% compatible                      |
| API Competencias               | ✅     | 🟡           | 50% falta transform/embryo          |
| API Roles                      | ✅     | 🟡           | 60% falta mutation analysis         |
| UI Lista escenarios            | ✅     | ✅           | ✅                                  |
| UI Detalle con tabs            | ✅     | 🟡           | Tabs diferentes                     |
| UI Matriz competencias         | ✅     | 🟡           | Incompleta                          |
| UI Modal transformar           | ✅     | ❌           | NO EXISTE                           |
| UI Rol incubación              | ✅     | 🟡           | Falta análisis automático           |
| Métricas & reportes            | ✅     | ❌           | NO IMPLEMENTADO                     |

---

## 🎯 CONCLUSIÓN Y RECOMENDACIONES

### Estado Actual

✅ **LO QUE ESTÁ BIEN:**

- Estructura base de escenarios, capacidades y roles existe
- API endpoints básicos funcionan
- Frontend tiene componentes de visualización
- Workflow de aprobación existe (aunque diferente)

❌ **LO QUE FALTA (CRÍTICO):**

1. **Sistema de Versionamiento de Competencias**
   - Tabla `competency_versions` - NECESARIA
   - No hay evolución de competencias
   - No hay BARS redefinibles

2. **Cálculos Automáticos**
   - `calculateRoleMutation()` - No existe
   - `suggestArchetype()` - No existe
   - Indices de innovación/obsolescencia - No existen

3. **Estados de Evolución de Competencias**
   - `evolution_state` (standard, transformed, obsolescent, new_embryo)
   - `transformation_notes`
   - `obsolescence_reason`

4. **UI Completa**
   - Modal de transformación de competencias
   - Panel de análisis automático de roles
   - Métricas visuales

### ¿DEBO IMPLEMENTAR TODO DESDE CERO?

**Opción A: Implementar el Prompt Original**

- Modificar estructura actual para que coincida
- Crear tablas de versionamiento de competencias
- Implementar cálculos automáticos
- Crear UI faltante
- **Tiempo estimado:** 3-4 semanas
- **Riesgo:** Romper lo que ya funciona

**Opción B: Extender lo Actual**

- Agregar campos de `evolution_state` a capability_competencies
- Crear sistema ligero de versionamiento
- Implementar cálculos como servicios
- Mejorar UI existente
- **Tiempo estimado:** 1-2 semanas
- **Riesgo:** Menor, cambios incrementales

**Opción C: Documentar la Brecha**

- Mantener implementación actual
- Documentar qué del prompt no se implementó
- Priorizar para próximo sprint
- **Tiempo estimado:** Inmediato
- **Riesgo:** Ninguno

### Recomendación

**Implementar Opción B** (Extender lo Actual):

1. Agregar `evolution_state` enum a `capability_competencies`
2. Crear servicio `CompetencyVersioningService` ligero
3. Implementar cálculos de mutación como helper methods
4. Mejorar UI con modal de transformación
5. Agregar métricas básicas al dashboard

---

## 📝 PRÓXIMOS PASOS

Si decides implementar el prompt original:

### Fase 1: Database (1 semana)

- [ ] Crear migración `competency_versions` table
- [ ] Crear migración `role_versions` table
- [ ] Agregar campos a `scenario_capacity_competencies`
- [ ] Agregar `mutation_type`, `mutation_index` a `scenario_roles`

### Fase 2: Backend (1.5 semanas)

- [ ] Implementar `CompetencyVersioningService`
- [ ] Implementar `calculateRoleMutation()`
- [ ] Implementar `suggestArchetype()`
- [ ] Crear endpoints `/competencies/{id}/transform` y `/competencies/create-embryo`
- [ ] Crear tests para nuevas funciones

### Fase 3: Frontend (1 semana)

- [ ] Crear modal `TransformCompetencyModal.vue`
- [ ] Crear modal `RoleAnalysisPanel.vue`
- [ ] Agregar UI para evolution_states
- [ ] Implementar métricas visuales

### Fase 4: Testing & Polish (1 semana)

- [ ] Tests E2E completos
- [ ] Performance testing
- [ ] Documentación actualizada

---

**Total estimado:** 4-5 semanas para implementación completa del prompt original.

---

_Documento generado el 2026-02-04 como resultado de auditoría técnica._
