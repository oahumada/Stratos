# Strato — PROMPT MAESTRO PARA COPILOT
## Módulo: Workforce Planning (WFP) con Escenarios, Scopes, 7 pasos, estados y versionamiento inmutable

**Fecha de creación:** 7 Enero 2026  
**Status:** Blueprint para implementación  

---

### 0) CONTEXTO
Estoy desarrollando Strato (Laravel + PostgreSQL, Vue 3 + TS + Vuetify). Multi-tenant: todo dato debe aislarse por `organization_id`.

Este desarrollo NO reemplaza el Workforce Planning actual (baseline). Es una **sub-funcionalidad avanzada** dentro de WFP: "Planificación por Escenarios" que coexiste con el baseline.

---

### 1) PRINCIPIOS DE DISEÑO (OBLIGATORIOS)
1. Multi-tenant enforce: todas las queries y endpoints deben filtrar por `organization_id` del usuario autenticado.
2. Escenarios "what-if" vs "en marcha": separar **estado de decisión** y **estado de ejecución**.
3. Escenarios segmentados: no todos los escenarios son globales; pueden aplicar a áreas / familias de roles.
4. Jerarquía de escenarios: un "escenario padre" (corporativo) puede tener escenarios "hijo" (por área). Los hijos pueden avanzar a ritmos distintos.
5. NO usar jerarquía padre-hijo de skills. En su lugar: **skills por alcance**:
   - `transversal`: aplica a toda la organización
   - `domain`: aplica a un dominio/área o familia de roles
   - `specific`: específica de un rol
6. Inmutabilidad: cuando un escenario está `approved`, se vuelve read-only. Para cambios: **crear nueva versión** (clon profundo).

---

### 2) METODOLOGÍA "7 PASOS" (PRODUCTIZADA)
El escenario debe guiar al usuario con un stepper/workflow:
1. Alcance y supuestos (scope + horizonte + drivers)
2. Inventario (Supply) — foto actual (por scope)
3. Demanda futura (Demand) — headcount + nivel requerido (por scope)
4. Brechas (Gaps) — headcount gap + level gap + prioridad
5. Estrategias de cierre (Build/Buy/Borrow/Bridge/Bind/Bot)
6. Plan e hitos (milestones, responsables, fechas)
7. Monitoreo y recalibración (tracking + comparaciones + versión)

Guardar progreso: `current_step` (1..7) y opcional checklist por paso.

---

### 3) MODELO DE ALCANCE (SCOPE) — ESCENARIOS SEGMENTADOS
Implementar scope explícito. Un escenario puede:
- ser global (`organization`)
- ser por `department`
- ser por `role_family`

MVP: soportar 1 scope principal por escenario (en `workforce_scenarios.scope_type/scope_id`) + tabla puente opcional para multi-scope futuro.

---

### 4) ESTADOS (LIFECYCLE) — DOS CAPAS
#### 4.1 Estado de decisión (governance)
`draft`, `simulated`, `proposed`, `approved`, `archived`, `rejected` (opcional)
- `draft`: se edita
- `simulated`: ya calculó gaps (what-if)
- `proposed`: listo para revisión/aprobación
- `approved`: plan oficial (INMUTABLE)
- `archived`: histórico
- `rejected`: descartado (opcional)

#### 4.2 Estado de ejecución (delivery)
`not_started`, `in_progress`, `paused`, `completed`
Reglas:
- Si `decision_status != approved` => `execution_status` debe ser `not_started`
- Si `execution_status == completed` => `decision_status` debe ser `approved` (o `archived`)

---

### 5) VERSIONAMIENTO (OPCIÓN 1) — INMUTABLE
- Un escenario aprobado no se modifica.
- Para cambiarlo, se crea nueva versión `draft` (clon profundo).
Campos:
- `version_group_id` (UUID) agrupa versiones del mismo plan
- `version_number` int incremental
- `is_current_version` boolean (solo una true por group)
- `approved_at`, `approved_by`

---

### 6) MIGRACIONES (POSTGRES)
Crear/ajustar tablas:

#### 6.1 workforce_scenarios (agregar campos)
```sql
-- Campos a agregar/modificar en workforce_scenarios
parent_id bigint nullable FK workforce_scenarios.id
scope_type enum('organization','department','role_family') default 'organization'
scope_id bigint nullable
current_step int default 1
version_group_id uuid
version_number int default 1
is_current_version boolean default true
decision_status enum('draft','simulated','proposed','approved','archived','rejected') default 'draft'
execution_status enum('not_started','in_progress','paused','completed') default 'not_started'
approved_at timestamp nullable
approved_by bigint nullable FK users.id
owner_id bigint nullable FK users.id

-- Indexes
(organization_id, is_current_version)
(organization_id, decision_status)
(parent_id)
(version_group_id)
```

#### 6.2 skills (agregar campos)
```sql
scope_type enum('transversal','domain','specific') default 'domain'
domain_tag string nullable  -- ej "Ventas", "TI", "Legal"
```

#### 6.3 scenario_templates (global, no tenant)
Mantener plantillas globales con config JSON. Deben poder sugerir:
- lista de skills transversales y domain (por industry)
- KPIs
- estrategias sugeridas
- time horizons
- "skills obligatorias" (transversales) para padre

#### 6.4 scenario_skill_demands
```sql
CREATE TABLE scenario_skill_demands (
    id bigserial PRIMARY KEY,
    scenario_id bigint NOT NULL FK workforce_scenarios.id ON DELETE CASCADE,
    skill_id bigint NOT NULL FK skills.id ON DELETE CASCADE,
    role_id bigint nullable FK roles.id,
    department_id bigint nullable FK departments.id (si existe),
    required_headcount int NOT NULL,
    required_level decimal(2,1) NOT NULL,
    current_headcount int default 0,
    current_avg_level decimal(2,1) default 0,
    priority enum('critical','high','medium','low') default 'medium',
    rationale text nullable,
    is_mandatory_from_parent boolean default false,
    created_at timestamp,
    updated_at timestamp,
    UNIQUE(scenario_id, skill_id, role_id, department_id)
);
```

#### 6.5 scenario_closure_strategies
```sql
CREATE TABLE scenario_closure_strategies (
    id bigserial PRIMARY KEY,
    scenario_id bigint NOT NULL FK workforce_scenarios.id ON DELETE CASCADE,
    skill_id bigint NOT NULL FK skills.id,
    strategy enum('build','buy','borrow','bridge','bind','bot') NOT NULL,
    strategy_name varchar(255),
    description text,
    estimated_cost decimal(12,2) nullable,
    estimated_time_weeks int nullable,
    success_probability decimal(3,2) default 0.60,
    risk_level enum('low','medium','high') default 'medium',
    assigned_to bigint nullable FK users.id,
    status enum('proposed','approved','rejected','in_progress','completed') default 'proposed',
    action_items json nullable,
    created_at timestamp,
    updated_at timestamp,
    INDEX(scenario_id, status),
    INDEX(scenario_id, skill_id)
);
```

#### 6.6 scenario_milestones
```sql
CREATE TABLE scenario_milestones (
    id bigserial PRIMARY KEY,
    scenario_id bigint NOT NULL FK workforce_scenarios.id ON DELETE CASCADE,
    name varchar(255) NOT NULL,
    description text,
    target_date date NOT NULL,
    completed_date date nullable,
    status enum('pending','in_progress','completed','delayed','at_risk') default 'pending',
    completion_percentage int default 0,
    responsible_user_id bigint nullable FK users.id,
    created_at timestamp,
    updated_at timestamp
);
```

#### 6.7 scenario_status_events (auditoría)
```sql
CREATE TABLE scenario_status_events (
    id bigserial PRIMARY KEY,
    scenario_id bigint NOT NULL FK workforce_scenarios.id ON DELETE CASCADE,
    from_decision_status varchar(50) nullable,
    to_decision_status varchar(50) nullable,
    from_execution_status varchar(50) nullable,
    to_execution_status varchar(50) nullable,
    changed_by bigint NOT NULL FK users.id,
    notes text nullable,
    created_at timestamp
);
```

---

### 7) MODELOS ELOQUENT + POLICIES

**WorkforceScenario:**
- Relations: parent, children, skillDemands, closureStrategies, milestones, statusEvents
- Casts: decision_status, execution_status, scope_type
- Scopes: currentVersion(), byOrganization()

**ScenarioTemplate:**
- Global (sin organization_id)
- JSON config para skills sugeridas

**Skill:**
- Agregar: scope_type, domain_tag

**Policies:**
- view/update/delete restricted por organization_id
- update/delete bloqueado si decision_status == 'approved' (excepto acción createNewVersion)

---

### 8) SERVICIOS (BUSINESS LOGIC) — CENTRALIZAR
Crear `WorkforcePlanningService` con métodos:

#### 8.1 createScenarioFromTemplate(organization, template, payload)
- crea escenario padre o hijo según payload
- aplica `scope_type/scope_id`
- si es hijo, asigna `parent_id`
- importa skills desde template:
  - transversales sugeridas => si escenario padre: crear demands `is_mandatory_from_parent=true` cuando aplique
  - domain/specific sugeridas => opcional/seleccionables
- set current_step=1, decision_status='draft'

#### 8.2 syncParentMandatorySkills(childScenario)
- copiar (upsert) desde el padre todas las demands con `is_mandatory_from_parent=true`
- marcar en hijo también como `is_mandatory_from_parent=true`
- impedir que se eliminen en UI/API

#### 8.3 calculateSupply(scenario)
- calcular inventario desde person_skills:
  - headcount: count distinct user_id con current_level >= minLevel (default 2)
  - avg_level: avg(current_level)
- filtrar población por scope:
  - organization: todos users de org
  - department: users.department_id == scope_id (si existe)
  - role_family: roles.role_family_id o users.role_family_id (si existe)
Si columnas/tablas no existen, degradar con fallback sin romper (MVP), pero dejar TODO centralizado para adaptar luego.

#### 8.4 calculateScenarioGaps(scenario)
- para cada demand:
  - actualiza current_headcount, current_avg_level
  - gap_headcount = max(0, required_headcount - current_headcount)
  - gap_level = max(0, required_level - current_avg_level)
- retornar summary + gaps
- al finalizar, si decision_status == 'draft' puede pasar a 'simulated' por acción explícita (no automático)

#### 8.5 recommendStrategiesForGap(scenario, gap, preferences)
MVP reglas:
- si gap_headcount grande y time_pressure high => buy + borrow
- si gap_level alto pero gap_headcount bajo => build
- si budget_sensitivity high => build + bind + bridge
- si automation_allowed => bot sugerido

#### 8.6 refreshSuggestedStrategies(scenario, preferences)
- crea estrategias `proposed` sin duplicar (skill_id + strategy)
- prohibido si scenario approved

#### 8.7 transitionDecisionStatus(scenario, toStatus, user, notes)
- valida transiciones (draft->simulated->proposed->approved->archived)
- al aprobar:
  - decision_status='approved'
  - approved_at/by set
  - execution_status must be not_started
  - generar status_event
- prohibido aprobar si:
  - no hay demands
  - no hay gaps calculados recientemente (puedes guardar `last_simulated_at` opcional)
  - no hay al menos 1 estrategia approved (o regla MVP: al menos 1 propuesta)

#### 8.8 startExecution / pauseExecution / completeExecution
- solo si decision_status == approved
- log event

#### 8.9 createNewVersion(originalApprovedScenario, user, notes)
- solo si original decision_status == approved
- clon profundo:
  - replicate scenario, set version_number+1, new id, decision_status='draft', execution_status='not_started'
  - version_group_id igual al original
  - is_current_version = true
  - approved_at/by null
  - parent_id y scope se copian
- marcar original is_current_version=false (NO editar otras cosas)
- clonar demands/strategies/milestones (con nuevas FK scenario_id)
- log event en ambos (opcional)
- retornar nueva versión

#### 8.10 roll-up parent (consolidación)
`consolidateParent(parentScenario)`:
- considera SOLO hijos `is_current_version=true`
- agrega métricas:
  - coverage promedio ponderado
  - costos estimados
  - % hijos approved / in_progress / completed
- exponer en dashboard del padre

---

### 9) API (LARAVEL) — ENDPOINTS
Versionar rutas: `/api/v1/...`

```php
// Templates
GET /api/v1/scenario-templates

// Scenarios CRUD
POST /api/v1/workforce-scenarios (crear)
POST /api/v1/workforce-scenarios/{id}/from-template
GET /api/v1/workforce-scenarios (listar por tenant + filtros)
GET /api/v1/workforce-scenarios/{id}
PATCH /api/v1/workforce-scenarios/{id} (solo si no approved)
DELETE /api/v1/workforce-scenarios/{id} (solo si no approved)

// Workflow & Analysis
POST /api/v1/workforce-scenarios/{id}/simulate (calculate gaps y set decision_status=simulated)
POST /api/v1/workforce-scenarios/{id}/refresh-suggested-strategies

// State Management
POST /api/v1/workforce-scenarios/{id}/decision-status (transition)
POST /api/v1/workforce-scenarios/{id}/execution/start
POST /api/v1/workforce-scenarios/{id}/execution/pause
POST /api/v1/workforce-scenarios/{id}/execution/complete

// Versioning
POST /api/v1/workforce-scenarios/{id}/versions (createNewVersion)
GET /api/v1/workforce-scenarios/{id}/versions (listar por version_group_id)

// Hierarchy
GET /api/v1/workforce-scenarios/{id}/rollup (si es padre)
```

FormRequests para validación y Policies para authorize.

---

### 10) FRONTEND (VUE 3 + VUETIFY) — UX CLAVE

**Rutas:**
- /workforce-planning (baseline dashboard)
- /workforce-planning/scenarios (lista)
- /workforce-planning/scenarios/:id (detalle con stepper)

**UI:**
- **Lista**: chips doble estado (decision/execution), filtros por scope y status
- **Detalle**: `v-stepper` (7 pasos), con guardrails:
  - si approved => todo disabled y mostrar botón "Crear nueva versión"
  - si draft => permitir editar demands
  - si simulated/proposed => permitir refinar, pero bloquear edición de skills mandatory heredadas
- **Vista padre**: pestaña "Consolidado" con roll-up de hijos

---

### 11) SEEDERS (DEMO)
Crear seeds:

**ScenarioTemplatesSeeder:**
- Templates: AI Adoption (transversal), E-commerce growth (domain), Digital transformation

**Demo org TechCorp:**
- Escenario padre: "Transformación Digital 2026" (scope organization)
  - skills transversales mandatory: Ética en IA, Data Literacy básica
- Escenario hijo: "Incremento ventas online" (scope department Ventas)
  - hereda transversales
  - agrega domain: Marketing Digital, Analítica Web
- Crear person_skills dummy para que supply/gaps funcionen

---

### 12) TESTS (MÍNIMOS)
Feature tests:
- tenant isolation
- cannot update approved scenario (demands/strategies)
- createNewVersion clones relationships
- simulate returns structure
- child inherits mandatory skills

---

## ORDEN DE IMPLEMENTACIÓN RECOMENDADO

1. **Migraciones + Seeders**
   - Agregar campos a workforce_scenarios
   - Agregar campos a skills
   - Crear scenario_skill_demands
   - Crear scenario_closure_strategies
   - Crear scenario_milestones
   - Crear scenario_status_events
   - ScenarioTemplatesSeeder

2. **Modelos + Relaciones + Casts**
   - WorkforceScenario (relations, scopes, casts)
   - ScenarioSkillDemand
   - ScenarioClosureStrategy
   - ScenarioMilestone
   - ScenarioStatusEvent
   - Skill (agregar scope_type/domain_tag)

3. **Policies + Requests**
   - WorkforceScenarioPolicy
   - StoreWorkforceScenarioRequest
   - UpdateWorkforceScenarioRequest
   - TransitionDecisionStatusRequest

4. **Services (WFP service)**
   - WorkforcePlanningService con todos los métodos

5. **Controllers + Routes**
   - WorkforceScenarioController
   - ScenarioTemplateController
   - Rutas en api.php

6. **Frontend básico**
   - Lista de scenarios
   - Detalle con stepper
   - Modo read-only para approved
   - Botón "Crear nueva versión"

7. **Tests mínimos**
   - Feature tests básicos

---

## NOTAS IMPORTANTES

- ✅ Multi-tenant enforcement en TODAS las queries
- ✅ Inmutabilidad cuando decision_status == 'approved'
- ✅ Versionamiento profundo (clonar relaciones)
- ✅ Skills por alcance (NO jerarquía)
- ✅ Dos capas de estado (decisión + ejecución)
- ✅ Jerarquía padre-hijo de escenarios
- ✅ Metodología 7 pasos con stepper UI
- ✅ Consolidación (roll-up) para escenarios padre

---

**FIN DEL PROMPT MAESTRO**
