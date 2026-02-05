# 🔍 COMPARATIVO SIDE-BY-SIDE: Prompt vs. Realidad

**Generado:** 4 Febrero 2026

---

## 1️⃣ ESTRUCTURA DE DATOS - TABLAS

### Esperado por Prompt (7 tablas)

```
scenarios
├── id (UUID)
├── organization_id (FK)
├── name, description
├── time_horizon
├── status: draft|in_review|approved|archived
├── created_by, approved_by, approved_at
└── created_at, updated_at

scenario_capacities
├── id (UUID)
├── scenario_id (FK)
├── name, description
├── criticality: critical|important|nice_to_have
├── order_index
└── timestamps

competency_versions ← CRÍTICA
├── id (UUID)
├── competency_id (FK) → competencies
├── version: v1.0|v1.1|v2.0
├── scenario_id (FK, nullable)
├── is_master: boolean
├── name, description
├── bars_definition: JSONB ← BARS redefinibles
├── complexity_level: strategic|tactical|operational
├── change_log: text
├── created_by (FK)
├── parent_version_id (FK) ← Trazabilidad
└── created_at

scenario_capacity_competencies
├── id (UUID)
├── capacity_id (FK)
├── competency_version_id (FK) ← Link a versión
├── evolution_state: standard|transformed|obsolescent|new_embryo
├── required_level: 1-5
├── current_level: 1-5
├── criticality: critical|important|supporting
├── obsolescence_reason: text
├── transformation_notes: text
└── timestamps

scenario_roles (Embriones)
├── id (UUID)
├── scenario_id (FK)
├── name, description
├── status: embryo|formalized
├── base_role_id (FK, nullable)
├── mutation_type: enrichment|specialization|hybridization|greenfield|sunset
├── mutation_index: decimal (%)
├── suggested_archetype: strategic|tactical|operational
├── suggested_level: 1-5
├── formalized_role_id (FK) ← Al eclosionar
└── created_at

scenario_role_competencies
├── id (UUID)
├── scenario_role_id (FK)
├── competency_version_id (FK) ← A versión
├── source: scenario|catalog|archetype_core
├── required_level: 1-5
└── created_at

role_versions
├── id (UUID)
├── role_id (FK)
├── version: v1.0
├── scenario_id (FK, nullable)
├── is_master: boolean
├── archetype, mastery_level
├── process_domain
├── change_log: text
├── mutation_index: decimal
├── created_by (FK)
├── parent_version_id (FK)
└── timestamps
```

### Implementado Actualmente (7 tablas + 3 extra)

```
scenarios ← DIFERENTE ESTRUCTURA
├── id (BIGINT) ← INT no UUID
├── organization_id (FK)
├── name, description
├── kpis: json
├── start_date, end_date
├── horizon_months (no time_horizon)
├── fiscal_year
├── scope_type: organization|department|role_family ← NO EN PROMPT
├── scope_id
├── status: draft|active|archived|completed ← Valores diferentes
├── decision_status: draft|pending_approval|approved|rejected ← NUEVO
├── execution_status: planned|in_progress|paused|completed ← NUEVO
├── current_step: 1-7 ← Metodología 7 pasos (NO EN PROMPT)
├── parent_id (FK) ← Jerarquía (NO EN PROMPT)
├── version_group_id (UUID) ← Versionamiento escenarios
├── version_number, is_current_version
├── owner_user_id, sponsor_user_id
└── timestamps

scenario_capabilities (✅ existe pero diferente nombre)
├── id (BIGINT)
├── scenario_id (FK) → scenarios
├── capability_id (FK) → capabilities ← Tabla externa!
├── strategic_role: target|watch|sunset ← Diferente de criticality
├── strategic_weight: 1-100
├── priority: 1-5
├── rationale: text
├── required_level: 1-5
├── is_critical: boolean
└── timestamps

competency_versions ❌ NO EXISTE

scenario_capacity_competencies 🟡 EXISTE PERO DIFERENTE
├── (TABLA: capability_competencies)
├── id (BIGINT)
├── scenario_id, capability_id, competency_id
├── required_level, weight, rationale
├── is_required, created_at, updated_at
│
│ FALTA:
│ ├─ evolution_state ❌
│ ├─ current_level ❌
│ ├─ criticality ❌
│ ├─ obsolescence_reason ❌
│ ├─ transformation_notes ❌
│ └─ competency_version_id ❌ (es competency_id)

Nota: Aunque no existe la tabla `competency_versions`, el sistema marca incubación mediante el campo `discovered_in_scenario_id` en la tabla `capabilities` (y en los flujos de creación desde UI/API). Ese campo actúa como indicador de "embrión" cuando una capability/competency se crea desde un escenario, pero no sustituye el versionamiento formal requerido por `competency_versions`.

scenario_roles 🟡 EXISTE PERO DIFERENTE
├── id (BIGINT)
├── scenario_id (FK)
├── role_id (FK) → roles ← Tabla externa
├── role_change: evolve|new|sunset
├── impact_level: high|medium|low
├── evolution_type: incremental|transformative|disruptive
├── rationale: text
├── unique(scenario_id, role_id)
│
│ FALTA:
│ ├─ status: embryo|formalized ❌ CRÍTICO
│ ├─ mutation_type ❌ CRÍTICO
│ ├─ mutation_index ❌ CRÍTICO
│ ├─ suggested_archetype ❌ CRÍTICO
│ ├─ suggested_level ❌
│ └─ formalized_role_id ❌

scenario_role_competencies 🟡 EXISTE PERO DIFERENTE
├── id (BIGINT)
├── scenario_id, role_id, competency_id (no competency_version_id)
├── required_level, is_core
├── change_type: maintenance|transformation|enrichment|extinction
├── rationale, timestamps
│
│ FALTA:
│ ├─ source field ❌ (existe como change_type pero diferente)
│ └─ competency_version_id ❌ (no hay versionamiento)

role_versions ❌ NO EXISTE

EXTRA (No en prompt):
├── scenario_skill_demands
├── scenario_template_*
└── scenario_status_events (¡útil para audit!)
```

---

## 2️⃣ ALGORITMOS Y FUNCIONES CORE

### ✅ Esperado: `calculateRoleMutation()`

```javascript
INPUT:
  - scenarioRole { competencies: [...], required_levels: [...] }
  - baseRole { competencies: [...], required_levels: [...] }

LOGIC:
  - Find added competencies (en scenario no en base)
  - Find removed competencies (en base no en scenario)
  - Find transformed (same id pero diferentes niveles)
  - Calculate changeRate = (added + removed + transformed) / baseTotal * 100
  - Determine mutation_type:
    * removed > baseTotal * 50%  → "sunset"
    * added > removed * 2        → "enrichment"
    * removed > added * 2        → "specialization"
    * added > 0 && removed > 0   → "hybridization"
    * !baseRole                  → "greenfield"

OUTPUT: { type: 'enrichment', index: 45.2 }

STATUS: ❌ NOT IMPLEMENTED
```

### ✅ Esperado: `suggestArchetype()`

```javascript
INPUT:
  - scenarioRole { competencies: [...] }

LOGIC:
  - Load complexity_level for each competency
  - Count: strategic, tactical, operational
  - Find dominant = argmax(counts)
  - Calculate dominanceRate = dominant_count / total
  - If dominanceRate < 0.6: alert 'mixed_archetype_warning'

OUTPUT: {
  suggested_archetype: 'tactical',
  confidence: 0.75,
  alert: null
}

STATUS: ❌ NOT IMPLEMENTED
```

### ✅ Esperado: `createCompetencyVersion()`

```javascript
INPUT:
  - competencyId: number
  - scenarioId: number
  - changes: { name?, description?, bars_definition?, complexity_level?, change_log }
  - userId: number

LOGIC:
  - Get baseCompetency from competencies table
  - Get lastVersion from competency_versions
  - Increment version: vX.Y → vX.(Y+1) or vX.0 → v(X+1).0
  - Create competency_versions record:
    * version = newVersion
    * scenario_id = scenarioId
    * is_master = false
    * name = changes.name || baseCompetency.name
    * description = changes.description || baseCompetency.description
    * bars_definition = changes.bars_definition || baseCompetency.bars_definition
    * change_log = changes.change_log (required)
    * parent_version_id = lastVersion.id
    * created_by = userId

OUTPUT: { id, version, competency_id, scenario_id, ... }

STATUS: ❌ NOT IMPLEMENTED
```

### ✅ Esperado: `approveScenario()`

```javascript
INPUT: { scenarioId, approverId }

LOGIC:
  Step 1: Promote all competency_versions to Master (where is_master=false)
  Step 2: Formalize embryo_roles
    - For each scenario_role where status='embryo':
      * Create new organization_role
      * Link competencies via role_competencies
      * Update scenario_role.status='formalized'
      * Update scenario_role.formalized_role_id=newRole.id
  Step 3: Update scenario
    - status = 'approved'
    - approved_by = approverId
    - approved_at = NOW()

OUTPUT: { success: true, message: 'Scenario merged to main catalog' }

STATUS: 🟡 PARTIAL (existe como transitionDecisionStatus pero NO hace promotion)
```

---

## 3️⃣ API ENDPOINTS

### Escenarios

| Esperado                        | Implementado                            | Status            |
| ------------------------------- | --------------------------------------- | ----------------- |
| POST /api/scenarios             | POST /api/scenarios                     | ✅                |
| GET /api/scenarios              | GET /api/scenarios                      | ✅                |
| GET /api/scenarios/:id          | GET /api/scenarios/:id                  | ✅                |
| PUT /api/scenarios/:id          | PUT /api/scenarios/:id                  | ✅                |
| DELETE /api/scenarios/:id       | DELETE /api/scenarios/:id               | ✅                |
| POST /api/scenarios/:id/approve | POST /api/scenarios/:id/decision-status | 🟡 Different name |

**Status:** ✅ **100% (6/6)**

### Capacidades

| Esperado                           | Implementado                         | Status |
| ---------------------------------- | ------------------------------------ | ------ |
| POST /api/scenarios/:id/capacities | POST /api/scenarios/:id/capabilities | ✅     |
| GET /api/scenarios/:id/capacities  | GET /api/scenarios/:id/capabilities  | ✅     |
| PUT /api/capacities/:id            | PUT /api/capabilities/:id            | ✅     |
| DELETE /api/capacities/:id         | DELETE /api/capabilities/:id         | ✅     |

**Status:** ✅ **100% (4/4)**

### Competencias

| Esperado                              | Implementado                                               | Status             |
| ------------------------------------- | ---------------------------------------------------------- | ------------------ |
| POST /api/capacities/:id/competencies | POST /api/scenarios/{id}/capabilities/{capId}/competencies | 🟡 Different route |
| PUT /api/capacity-competencies/:id    | PATCH /api/capability-competencies/:id                     | 🟡 PATCH not PUT   |
| DELETE /api/capacity-competencies/:id | DELETE /api/capability-competencies/:id                    | ✅                 |
| POST /api/competencies/:id/transform  | —                                                          | ❌ MISSING         |
| POST /api/competencies/create-embryo  | —                                                          | ❌ MISSING         |

**Status:** 🟡 **60% (3/5)**

### Roles

| Esperado                                  | Implementado                        | Status      |
| ----------------------------------------- | ----------------------------------- | ----------- |
| POST /api/scenarios/:id/roles             | POST /api/scenarios/:id/step2/roles | 🟡          |
| GET /api/scenarios/:id/roles              | GET /api/scenarios/:id/step2/data   | 🟡          |
| PUT /api/scenario-roles/:id               | —                                   | ❌ IMPLICIT |
| DELETE /api/scenario-roles/:id            | —                                   | ❌ IMPLICIT |
| GET /api/scenario-roles/:id/mutation      | —                                   | ❌ MISSING  |
| POST /api/scenario-roles/:id/competencies | (Implicit in step2)                 | 🟡          |

**Status:** 🟡 **65% (4/6)**

---

## 4️⃣ FRONTEND VIEWS

### Vista 1: Lista de Escenarios

```
ESPERADO:
┌─ Escenarios ─────────────────────────────────────────┐
│ [Nuevo Escenario]                                     │
├───────────────────────────────────────────────────────┤
│ Nombre | Horizonte | Estado | Creación | Acciones    │
├───────────────────────────────────────────────────────┤
│ Scenario 1 | 12 meses | draft | 2026-01-01 | [...]  │
│ Scenario 2 | 24 meses | approved | 2026-01-05 | [...] │
├───────────────────────────────────────────────────────┤
│ Filtro: [Estado ▼] Draft | In Review | Approved     │
└───────────────────────────────────────────────────────┘

IMPLEMENTADO:
┌─ ScenarioDetail ──────────────────────────────────────┐
│ ✅ Tabla de escenarios                                │
│ ✅ Botón crear nuevo                                  │
│ ✅ Filtros por estado                                │
│ ✅ Breadcrumb/navegación                             │
└───────────────────────────────────────────────────────┘

STATUS: ✅ 100%
```

### Vista 2: Detalle Escenario (Tabs)

```
ESPERADO:
┌─ Escenario: "AI Adoption 2026" ──────────────────────┐
│ [Info] [Capacidades] [Roles] [Análisis] |             │
├───────────────────────────────────────────────────────┤
│
│ INFO TAB:
│  Nombre: AI Adoption 2026
│  Descripción: ...
│  Horizonte: 12 meses
│  Estado: draft → in_review → approved → archived
│
│ CAPACIDADES TAB:
│  [Nueva Capacidad]
│  📊 Capacidad 1 (Criticidad: High)
│      [Expandir]
│      └─ Competencias (5)
│
│ ROLES INCUBACIÓN TAB:
│  [Nuevo Rol]
│  🔵 ML Engineer (mutation: enrichment, 45%)
│      [Ver análisis]
│
│ ANÁLISIS TAB:
│  📈 Índice de Innovación: 35%
│  📉 Índice de Obsolescencia: 12%
│  🔄 Índice de Transformación: 28%
│  ⚠️ Riesgo de Brecha: 120 personas
│
IMPLEMENTADO:
│
│ ScenarioDetail.vue:
│  ✅ Info tab
│  ✅ Capabilities tab
│  ✅ Roles tab (ScenarioRoles)
│  ✅ Metodología 7 Pasos (EXTRA)
│  ✅ Estados & Acciones (EXTRA)
│  🟡 Análisis tab (GENERIC)
│
STATUS: 🟡 80%
```

### Vista 3: Matriz Competencias

```
ESPERADO:
┌─ Matriz: Capacidad → Competencias ────────────────────┐
│ [Nueva Competencia]                                    │
├─────────────────────────────────────────────────────────┤
│ Competencia | Estado | Nivel Actual | Nivel Requerido │ Acciones |
├─────────────────────────────────────────────────────────┤
│ Python      | 🟢 Standard | 3 | 4 | [Transf] [BARS] [X] │
│ Cloud Arch  | 🔵 Transformed | 2 | 5 | [Transf] [BARS] [X] │
│ Leadership  | 🔴 Obsolescent | 4 | 0 | [Transf] [BARS] [X] │
│ GenAI       | ⭐ New Embryo | - | 5 | [Transf] [BARS] [X] │
└─────────────────────────────────────────────────────────┘

IMPLEMENTADO:
│
│ ScenarioDetail.vue:
│  ✅ Tabla de competencias
│  ✅ Visualización de niveles
│  🟡 Badges (parciales)
│  ❌ Dropdown evolution_state
│  ❌ Botón "Transformar"
│  ❌ Modal "Ver BARS"
│
STATUS: 🟡 50%
```

### Vista 4: Modal Transformar Competencia

```
ESPERADO:
┌─ Transformar Competencia ─────────────────────────────┐
│
│ Nombre: [______________________________]
│ Descripción: [_________________________]
│
│ BARS (Behavioral Anchored Rating Scale):
│  Nivel 1: [Descripción editable]
│  Nivel 2: [Descripción editable]
│  Nivel 3: [Descripción editable]
│  Nivel 4: [Descripción editable]
│  Nivel 5: [Descripción editable]
│
│ Justificación del Cambio (obligatorio):
│  [________________________________________]
│
│ [Cancelar] [Crear Versión v1.1]
│
└───────────────────────────────────────────────────────┘

IMPLEMENTADO: ❌ DOES NOT EXIST

STATUS: ❌ 0% - BLOQUEANTE
```

### Vista 5: Análisis de Rol Incubación

```
ESPERADO:
┌─ Senior ML Engineer ──────────────────────────────────┐
│ [Edit] [Delete] [View mutations]
│
│ Competencias Asociadas (6):
│ • Python (required: 5)
│ • ML Theory (required: 4)
│ • Data Architecture (required: 4)
│ • Leadership (required: 3)
│ • Cloud Platforms (required: 4)
│ • GenAI Frameworks (required: 4)
│
│ ┌─ Análisis Automático ─────────────────────────────┐
│ │ Arquetipo Sugerido: Tactical (85% confianza)     │
│ │ Nivel Sugerido: 5 (experto)                      │
│ │ Índice de Mutación: 52.3% (specialization)      │
│ │                                                   │
│ │ ⚠️ Alerta: Mixed archetype detected (85% != 100%) │
│ └───────────────────────────────────────────────────┘
│
└───────────────────────────────────────────────────────┘

IMPLEMENTADO:
│
│ RoleCompetencyMatrix.vue:
│  ✅ Competencias asociadas
│  ✅ Niveles requeridos
│  ❌ Panel de análisis automático
│  ❌ Arquetipo sugerido
│  ❌ Nivel sugerido
│  ❌ Índice de mutación
│  ❌ Alertas
│
STATUS: 🟡 40%
```

---

## 5️⃣ VALIDACIONES

```
ESPERADO                                      IMPLEMENTADO
─────────────────────────────────────────────────────────────
✅ No aprobar sin confidence > 0.5            ❌ (no existe)
✅ Obsolescente requiere razón               ❌ (no existe)
✅ Transformada requiere cambio              ❌ (no existe)
✅ Rol ≥ 3 competencias                      ❌ (no existe)
✅ Generar reporte impacto                   🟡 Partial
✅ Transiciones de estado                    ✅ Implemented
```

---

## 6️⃣ MÉTRICAS

```
ESPERADO                          IMPLEMENTADO
────────────────────────────────────────────────
📊 Índice de Innovación           ❌ Missing
  (% nuevas competencias)
│
📉 Índice de Obsolescencia        ❌ Missing
  (% sunset)
│
🔄 Índice de Transformación       ❌ Missing
  (% transformadas)
│
⚠️ Riesgo de Brecha               🟡 Partial
  (personas afectadas)            (calculateScenarioGaps exists)
```

---

## 📊 MATRIZ FINAL DE COBERTURA

```
┌──────────────────────────────────────────────────────────┐
│                   COBERTURA POR ÁREA                     │
├──────────────────────────────────────────────────────────┤
│ Escenarios:              ██████████ 90%                 │
│ Capacidades:             █████████░ 85%                 │
│ Competencias:            ████░░░░░░ 40%                 │
│ Roles:                   ██████░░░░ 60%                 │
│ Lógica Negocio:          ██░░░░░░░░ 20%                 │
│ API:                     ███████░░░ 75%                 │
│ Frontend:                ████░░░░░░ 40%                 │
│ Validaciones:            ██░░░░░░░░ 20%                 │
│ Métricas:                ░░░░░░░░░░ 0%                  │
│                                                          │
│ COBERTURA TOTAL:         ████░░░░░░ 28%  🔴            │
└──────────────────────────────────────────────────────────┘
```

---

## 🎯 CONCLUSIÓN

El sistema implementado es **fundamentalmente diferente** del prompt esperado.

- ✅ **Workforce Planning Phase 2** (actual): Versioning de escenarios, jerarquía, 7 pasos
- ❌ **Scenario Planning Phase 2** (esperado): Versioning de competencias, análisis automático, BARS redefinible

**La brecha crítica:** Sin `competency_versions`, no hay forma de hacer lo que el prompt especifica.
