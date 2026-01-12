# ANÁLISIS GAP: Prompt Maestro vs Sistema Implementado

**Fecha:** 7 Enero 2026  
**Objetivo:** Identificar qué ya existe y qué es nuevo del Prompt Maestro

---

## 📊 RESUMEN EJECUTIVO

| Componente               | % Implementado | Status                              |
| ------------------------ | -------------- | ----------------------------------- |
| **Tablas Base**          | 70%            | ✅ Parcial - Faltan campos críticos |
| **Estados & Workflow**   | 20%            | ⚠️ Solo status básico               |
| **Versionamiento**       | 0%             | ❌ No existe                        |
| **Jerarquía Escenarios** | 0%             | ❌ No existe                        |
| **Skills por Alcance**   | 0%             | ❌ No existe                        |
| **Metodología 7 Pasos**  | 0%             | ❌ No existe                        |
| **Servicios de Negocio** | 40%            | ⚠️ Básicos, faltan key methods      |
| **API Endpoints**        | 50%            | ⚠️ CRUD básico, faltan workflows    |
| **Frontend**             | 30%            | ⚠️ Lista/detalle simple             |

**Conclusión:** Sistema base existe pero necesita evolución arquitectónica significativa.

---

## 1️⃣ MODELO DE DATOS (Migraciones)

### ✅ YA EXISTE

#### Tabla: `workforce_planning_scenarios`

```sql
-- Campos actuales:
id, organization_id, name, description, horizon_months,
status ('draft','pending_approval','approved','archived'),
fiscal_year, created_by, approved_by, approved_at,
template_id, scenario_type, target_date, time_horizon_weeks,
assumptions (json), custom_config (json), estimated_budget, owner
```

#### Tabla: `scenario_skill_demands` ✅

```sql
-- YA CREADA (2026_01_06_193815)
id, scenario_id, skill_id, role_id, department,
required_headcount, required_level, current_headcount, current_avg_level,
priority, rationale, target_date
```

#### Tabla: `scenario_closure_strategies` ✅

```sql
-- YA CREADA (2026_01_06_193815)
id, scenario_id, skill_id, strategy (6Bs), strategy_name, description,
estimated_cost, estimated_time_weeks, success_probability, risk_level,
status, action_items, assigned_to, target_completion_date
```

#### Tabla: `scenario_milestones` ✅

```sql
-- YA CREADA (2026_01_06_193815)
id, scenario_id, name, description, target_date, actual_date,
status, completion_percentage, deliverables, dependencies, owner_id, notes
```

#### Tabla: `scenario_templates` ✅

```sql
-- YA CREADA (2026_01_06_193804)
id, name, slug, description, scenario_type, industry, icon, config (json),
is_active, usage_count
```

#### Otras tablas WFP existentes:

- `workforce_planning_role_forecasts` ✅
- `workforce_planning_matches` ✅
- `workforce_planning_skill_gaps` ✅
- `workforce_planning_succession_plans` ✅
- `workforce_planning_analytics` ✅

---

### ❌ FALTA IMPLEMENTAR (Prompt Maestro)

#### 1. Campos en `workforce_planning_scenarios`

**Versionamiento (CRÍTICO):**

```sql
version_group_id UUID      -- ❌ NO EXISTE
version_number INT         -- ❌ NO EXISTE
is_current_version BOOLEAN -- ❌ NO EXISTE
```

**Jerarquía:**

```sql
parent_id BIGINT FK       -- ❌ NO EXISTE (nullable)
```

**Alcance/Scope:**

```sql
scope_type ENUM('organization','department','role_family') -- ❌ NO EXISTE
scope_id BIGINT           -- ❌ NO EXISTE (nullable)
```

**Metodología 7 Pasos:**

```sql
current_step INT DEFAULT 1  -- ❌ NO EXISTE
```

**Estados Mejorados:**

```sql
-- ACTUAL: status ENUM('draft','pending_approval','approved','archived')
-- NUEVO (Prompt Maestro):
decision_status ENUM('draft','simulated','proposed','approved','archived','rejected')
execution_status ENUM('not_started','in_progress','paused','completed')
```

**Otros:**

```sql
owner_id BIGINT FK users.id  -- ❌ NO EXISTE (solo hay 'owner' string)
last_simulated_at TIMESTAMP  -- ❌ NO EXISTE (útil para validaciones)
```

---

#### 2. Campos en `skills`

**Skills por Alcance (CRÍTICO):**

```sql
-- Tabla: skills
scope_type ENUM('transversal','domain','specific') DEFAULT 'domain'  -- ❌ NO EXISTE
domain_tag VARCHAR(100) -- ❌ NO EXISTE (ej: "Ventas", "TI")
```

**Actual:**

```sql
-- Solo tiene: id, organization_id, name, category, description, is_critical
```

---

#### 3. Campo en `scenario_skill_demands`

**Herencia Padre-Hijo (CRÍTICO):**

```sql
is_mandatory_from_parent BOOLEAN DEFAULT false  -- ❌ NO EXISTE
```

---

#### 4. Tabla NUEVA: `scenario_status_events` (Auditoría)

```sql
-- ❌ NO EXISTE - Crear completa
CREATE TABLE scenario_status_events (
    id BIGSERIAL PRIMARY KEY,
    scenario_id BIGINT FK workforce_planning_scenarios,
    from_decision_status VARCHAR(50),
    to_decision_status VARCHAR(50),
    from_execution_status VARCHAR(50),
    to_execution_status VARCHAR(50),
    changed_by BIGINT FK users,
    notes TEXT,
    created_at TIMESTAMP
);
```

---

## 2️⃣ MODELOS ELOQUENT

### ✅ YA EXISTE

- `StrategicPlanningScenarios` ✅ (con relations básicas)
- `ScenarioTemplate` ✅
- `ScenarioSkillDemand` ✅
- `ScenarioClosureStrategy` ✅
- `ScenarioMilestone` ✅

**Relaciones existentes en StrategicPlanningScenarios:**

```php
organization(), creator(), approver(), roleForecasts(), matches(),
skillGaps(), successionPlans(), analytics(), template(),
skillDemands(), closureStrategies()
```

---

### ❌ FALTA IMPLEMENTAR

#### 1. Modelo: `ScenarioStatusEvent`

```php
// ❌ NO EXISTE - Crear completo
```

#### 2. En `StrategicPlanningScenarios` - Agregar:

**Relations:**

```php
parent()              // ❌ belongsTo self
children()            // ❌ hasMany self
owner()               // ❌ belongsTo User
statusEvents()        // ❌ hasMany ScenarioStatusEvent
```

**Scopes:**

```php
scopeCurrentVersion()    // ❌ where('is_current_version', true)
scopeByVersionGroup()    // ❌ where('version_group_id', $id)
scopeByScope()           // ❌ where('scope_type', $type)->where('scope_id', $id)
scopeParents()           // ❌ whereNull('parent_id')
scopeChildren()          // ❌ whereNotNull('parent_id')
```

**Casts:**

```php
// ✅ Ya tiene: assumptions, custom_config, approved_at
// ❌ Agregar: decision_status, execution_status, scope_type
```

**Accessors/Mutators:**

```php
getIsApprovedAttribute()      // ❌ decision_status == 'approved'
getCanEditAttribute()         // ❌ !isApproved
getIsParentAttribute()        // ❌ parent_id === null
```

---

#### 3. En `Skill` - Agregar:

**Casts:**

```php
scope_type  // ❌ NO EXISTE (transversal/domain/specific)
```

**Scopes:**

```php
scopeTransversal()   // ❌ where('scope_type', 'transversal')
scopeDomain()        // ❌ where('scope_type', 'domain')
scopeSpecific()      // ❌ where('scope_type', 'specific')
scopeByDomain()      // ❌ where('domain_tag', $tag)
```

---

## 3️⃣ SERVICIOS (Business Logic)

### ✅ YA EXISTE

**Archivo:** `WorkforcePlanningService.php` ✅

**Métodos existentes:**

```php
✅ calculateMatches($scenarioId)
✅ calculateIndividualMatch($person, $forecast, $scenario)
✅ calculateSkillGaps($scenarioId)
✅ generateAnalytics($scenarioId)
✅ runFullAnalysis($scenarioId)
// ... más métodos de cálculo
```

---

### ❌ FALTA IMPLEMENTAR (10 métodos del Prompt Maestro)

```php
❌ createScenarioFromTemplate($organization, $template, $payload)
❌ syncParentMandatorySkills($childScenario)
❌ calculateSupply($scenario)  // Por scope
❌ calculateScenarioGaps($scenario)  // Actualizado con scope
❌ recommendStrategiesForGap($scenario, $gap, $preferences)
❌ refreshSuggestedStrategies($scenario, $preferences)
❌ transitionDecisionStatus($scenario, $toStatus, $user, $notes)
❌ startExecution / pauseExecution / completeExecution
❌ createNewVersion($originalApprovedScenario, $user, $notes)
❌ consolidateParent($parentScenario)  // Roll-up de hijos
```

**Nota:** Algunos métodos parcialmente existen pero necesitan adaptación para:

- Scope filtering (organization/department/role_family)
- Estados duales (decision + execution)
- Versionamiento

---

## 4️⃣ API ENDPOINTS

### ✅ YA EXISTE

**Rutas actuales:**

```php
✅ GET    /api/v1/workforce-planning/scenario-templates
✅ GET    /api/v1/workforce-planning/workforce-scenarios
✅ POST   /api/v1/workforce-planning/workforce-scenarios
✅ GET    /api/v1/workforce-planning/workforce-scenarios/{id}
✅ PUT    /api/v1/workforce-planning/workforce-scenarios/{id}
✅ DELETE /api/v1/workforce-planning/workforce-scenarios/{id}
✅ POST   /api/v1/workforce-planning/workforce-scenarios/{id}/calculate-gaps
✅ POST   /api/v1/workforce-planning/workforce-scenarios/{id}/refresh-suggested-strategies
✅ POST   /api/v1/workforce-planning/workforce-scenarios/{template}/instantiate-from-template
```

---

### ❌ FALTA IMPLEMENTAR

**Workflow & Transitions:**

```php
❌ POST /api/v1/workforce-scenarios/{id}/simulate
❌ POST /api/v1/workforce-scenarios/{id}/decision-status  // transition
❌ POST /api/v1/workforce-scenarios/{id}/execution/start
❌ POST /api/v1/workforce-scenarios/{id}/execution/pause
❌ POST /api/v1/workforce-scenarios/{id}/execution/complete
```

**Versionamiento:**

```php
❌ POST /api/v1/workforce-scenarios/{id}/versions  // createNewVersion
❌ GET  /api/v1/workforce-scenarios/{id}/versions  // listar por version_group
```

**Jerarquía:**

```php
❌ GET /api/v1/workforce-scenarios/{id}/rollup  // consolidación padre
❌ GET /api/v1/workforce-scenarios/{id}/children
```

**Supply/Demand por Scope:**

```php
❌ GET /api/v1/workforce-scenarios/{id}/supply   // Inventario actual
❌ GET /api/v1/workforce-scenarios/{id}/demand   // Demanda proyectada
```

---

## 5️⃣ POLICIES & AUTHORIZATION

### ✅ YA EXISTE (Supuesto)

- Probablemente hay policies básicas con multi-tenant check

### ❌ FALTA IMPLEMENTAR

**En WorkforceScenarioPolicy:**

```php
❌ update() bloqueado si decision_status == 'approved'
❌ delete() bloqueado si decision_status == 'approved'
❌ createNewVersion() solo si decision_status == 'approved'
❌ transition() validar transiciones permitidas
❌ startExecution() solo si decision_status == 'approved'
```

---

## 6️⃣ FRONTEND (Vue 3 + Vuetify)

### ✅ YA EXISTE

**Rutas/Páginas:**

```
✅ /workforce-planning (dashboard baseline)
✅ /workforce-planning/scenarios (lista básica)
✅ /workforce-planning/scenarios/:id (detalle básico)
```

**Componentes:**

```
✅ OverviewDashboard.vue
✅ RoleForecastsTable.vue
✅ MatchingResults.vue
✅ SkillGapsMatrix.vue
✅ SuccessionPlanCard.vue
✅ Charts: Headcount, Coverage, SkillGaps, etc.
```

---

### ❌ FALTA IMPLEMENTAR

**UI Stepper (7 Pasos):**

```vue
❌ <v-stepper> con 7 steps (metodología productizada)
   Step 1: Alcance y Supuestos (scope selector)
   Step 2: Inventario (Supply) - readonly calculado
   Step 3: Demanda (Demand) - editable demands table
   Step 4: Brechas (Gaps) - readonly calculado
   Step 5: Estrategias (6Bs selector)
   Step 6: Plan e Hitos (milestones timeline)
   Step 7: Monitoreo (versiones, comparaciones)
```

**Guardrails por Estado:**

```vue
❌ Si decision_status == 'approved': - Todo readonly - Mostrar botón "Crear
Nueva Versión" - Deshabilitar edición de demands/strategies ❌ Si
is_mandatory_from_parent == true: - Bloquear eliminación de skill demand -
Mostrar badge "Heredado de Padre"
```

**Vistas Nuevas:**

```vue
❌ ScenarioVersionHistory.vue // Listado de versiones ❌
ParentConsolidationView.vue // Roll-up de hijos ❌ ScopeSelector.vue //
organization/department/role_family ❌ StateTransitionDialog.vue // Workflow
transitions
```

**Chips de Estado Dual:**

```vue
❌ <v-chip> decision_status (draft/simulated/proposed/approved)
❌ <v-chip> execution_status (not_started/in_progress/completed)
```

---

## 7️⃣ SEEDERS & DATOS DEMO

### ✅ YA EXISTE (Parcial)

- Probablemente hay seeders básicos de TechCorp

### ❌ FALTA IMPLEMENTAR

**ScenarioTemplatesSeeder:**

```php
❌ Templates con skills transversales/domain sugeridas
❌ Config JSON con KPIs, estrategias, horizons
❌ Skills obligatorias para padre marcadas
```

**Demo Escenarios Jerárquicos:**

```php
❌ Escenario Padre: "Transformación Digital 2026" (scope: organization)
   - Skills transversales: Ética IA, Data Literacy

❌ Escenario Hijo 1: "Incremento Ventas Online" (scope: department Ventas)
   - Hereda transversales
   - Agrega domain: Marketing Digital, Analítica Web

❌ Escenario Hijo 2: "Modernización IT" (scope: department TI)
   - Hereda transversales
   - Agrega domain: Cloud Architecture, DevOps
```

---

## 8️⃣ TESTS

### ✅ YA EXISTE (Supuesto)

- Tests básicos de API

### ❌ FALTA IMPLEMENTAR

**Feature Tests Críticos:**

```php
❌ test_cannot_update_approved_scenario()
❌ test_cannot_delete_approved_scenario()
❌ test_can_create_new_version_from_approved()
❌ test_new_version_clones_relationships()
❌ test_child_inherits_mandatory_skills_from_parent()
❌ test_cannot_delete_mandatory_skill_demand_in_child()
❌ test_transition_decision_status_validates_workflow()
❌ test_cannot_start_execution_if_not_approved()
❌ test_tenant_isolation_on_scenarios()
❌ test_scope_filtering_in_supply_calculation()
```

---

## 📋 PLAN DE IMPLEMENTACIÓN SUGERIDO

### FASE 1: Fundamentos (8-12 horas)

1. ✅ Migraciones de campos faltantes

   - Versionamiento en scenarios
   - Jerarquía (parent_id)
   - Scope (scope_type/scope_id)
   - Estados duales
   - current_step
   - Skills scope_type/domain_tag
   - scenario_status_events table

2. ✅ Actualizar Modelos
   - Relations (parent, children, owner, statusEvents)
   - Scopes (currentVersion, byScope)
   - Casts

### FASE 2: Lógica de Negocio (12-16 horas)

3. ✅ Implementar 10 métodos en WorkforcePlanningService

   - createScenarioFromTemplate
   - syncParentMandatorySkills
   - calculateSupply (con scope)
   - transitionDecisionStatus
   - createNewVersion
   - consolidateParent
   - etc.

4. ✅ Policies & Validaciones
   - Bloqueos por estado approved
   - Validaciones de transiciones

### FASE 3: API (8-10 horas)

5. ✅ Nuevos Endpoints
   - Workflow transitions
   - Versioning
   - Rollup
   - Supply/Demand por scope

### FASE 4: Frontend (16-20 horas)

6. ✅ Stepper 7 Pasos
7. ✅ Guardrails por estado
8. ✅ Vistas de versiones/jerarquía
9. ✅ Chips de estado dual

### FASE 5: Datos & Tests (6-8 horas)

10. ✅ Seeders con jerarquías
11. ✅ Tests críticos

---

## 🎯 TOTAL ESTIMADO

| Fase           | Horas           |
| -------------- | --------------- |
| Fundamentos    | 8-12h           |
| Lógica Negocio | 12-16h          |
| API            | 8-10h           |
| Frontend       | 16-20h          |
| Datos & Tests  | 6-8h            |
| **TOTAL**      | **50-66 horas** |

**Con reutilización inteligente:** ~40-50 horas (20% ahorro)

---

## ⚖️ DECISIÓN ESTRATÉGICA

### OPCIÓN A: Evolución Incremental (Recomendado)

- Agregar campos/features sobre sistema actual
- Mantener compatibilidad con lo existente
- Migración gradual de datos
- **Tiempo:** 40-50h
- **Riesgo:** Bajo

### OPCIÓN B: Refactorización Completa

- Rediseñar desde cero con nueva arquitectura
- Migrar datos existentes
- **Tiempo:** 60-80h
- **Riesgo:** Medio-Alto

---

**FIN DEL ANÁLISIS**
