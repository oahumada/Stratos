# 📊 WORKFORCE PLANNING - Especificación Técnica Completa

**Versión:** 1.0  
**Fecha:** 4 Enero 2026  
**Estado:** 🔄 En revisión  
**Prioridad:** 🔴 ALTA

---

## 📑 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Objetivos y Alcance](#objetivos-y-alcance)
3. [Arquitectura de Bloques](#arquitectura-de-bloques)
4. [Modelos de Datos](#modelos-de-datos)
5. [Endpoints API](#endpoints-api)
6. [Componentes Frontend](#componentes-frontend)
7. [User Stories](#user-stories)
8. [Criterios de Aceptación](#criterios-de-aceptación)
9. [Integración con Módulos Existentes](#integración-con-módulos-existentes)

---

## Descripción General

El módulo **Workforce Planning** es un sistema integrado que conecta:

- **Hoy:** Skills actuales + Talento interno + Capacidades presentes
- **Futuro:** Demandas del negocio + Roles emergentes + Transformaciones

Orquesta decisiones de dotación: talento interno → mercado externo → desarrollo → sucesión → desvinculación.

---

## Objetivos y Alcance

### Objetivos Principales

1. Anticipar necesidades de talento en horizonte 12-36 meses
2. Maximizar cobertura con talento interno (marketplace)
3. Optimizar reclutamiento externo basado en brechas reales
4. Planificar desarrollo y reconversión de talentos
5. Gestionar sucesión en roles críticos
6. Planificar desvinculaciones de manera estratégica

### Alcance

```
✅ Incluido en MVP Fase 2:
   - Bloques 1-4 (Base estratégica, Oferta interna, Demanda futura, Matching)
   - Analítica básica y KPIs
   - Dashboard de Workforce Planning
   - Marketplace mejorado (integración)

⏳ Roadmap futuro (Fase 3):
   - Bloques 5-7 (Reclutamiento externo, Desarrollo, Desvinculaciones)
   - IA/ML avanzado (predicción de rotación, skills emergentes)
   - Integración con plataformas externas (ATS, LMS)
```

---

## Arquitectura de Bloques

### Flujo General

```
┌─────────────────────────────────────────────────────────────────┐
│  BLOQUE 1: Base Estratégica y Modelo de Roles/Skills           │
│  - Mapa de roles (familias, niveles)                           │
│  - Diccionario de skills (técnicas, conductuales)              │
│  - Mapeo Roles ↔ Skills requeridas                             │
└──────────────────────┬──────────────────────────────────────────┘
                       │
       ┌───────────────┴──────────────────┐
       │                                  │
┌──────▼────────────────┐      ┌──────────▼──────────────┐
│ BLOQUE 2:             │      │ BLOQUE 3:              │
│ Oferta Interna       │      │ Demanda Futura         │
│ (Skills Actuales)     │      │ (Escenarios)           │
│ - Perfiles por persona│      │ - Proyecciones negocio │
│ - Marketplace interno │      │ - Roles emergentes     │
│ - Movilidad disponible│      │ - Automatización       │
└──────┬────────────────┘      └──────────┬──────────────┘
       │                                  │
       └──────────────────┬───────────────┘
                          │
            ┌─────────────▼────────────────┐
            │ BLOQUE 4:                    │
            │ Matching Interno             │
            │ - Sugerir candidatos internos│
            │ - Calcular gaps de skills    │
            │ - Simular cobertura interna  │
            └──────────────┬───────────────┘
                           │
            ┌──────────────┴────────────────┐
            │                               │
    ┌───────▼──────────┐          ┌────────▼────────────┐
    │ Cobertura Interna│          │ Brecha Externa      │
    │ (Bloque 5 Future)│          │ (Bloque 5 Future)   │
    │ - Movilidad      │          │ - Reclutamiento     │
    │ - Reconversión   │          │ - Selección         │
    │ - Sucesión       │          │ - Fuentes           │
    └──────────────────┘          └─────────────────────┘
            │                               │
            └──────────────┬────────────────┘
                           │
            ┌──────────────▼────────────────┐
            │ BLOQUE 6 & 7: Desarrollo      │
            │ y Desvinculaciones (Future)   │
            └───────────────────────────────┘
```

---

## Modelos de Datos

### Tablas Nuevas Requeridas

> Nota: esta sección documenta el diseño histórico del módulo. En la implementación actual la tabla canónica es `scenarios` (ver `src/app/Models/Scenario.php`). La nomenclatura `workforce_planning_scenarios` se mantiene aquí por trazabilidad histórica, pero está deprecada y no debe usarse en nuevo código.

#### 1. (Histórico) `workforce_planning_scenarios`

```sql
-- Diseñado históricamente como contenedor de escenarios. Use `scenarios` en la implementación actual.
CREATE TABLE workforce_planning_scenarios (
  id BIGINT PRIMARY KEY,
  organization_id BIGINT NOT NULL,
  name VARCHAR(255),                    -- "Escenario Base", "Conservador", "Agresivo"
  description TEXT,
  horizon_months INT,                   -- 12, 24, 36
  status ENUM('draft', 'active', 'archived'),
  fiscal_year INT,                      -- 2026, 2027, etc
  created_by BIGINT,                    -- Usuario que creó
  created_at TIMESTAMP,
  updated_at TIMESTAMP,

  FOREIGN KEY (organization_id) REFERENCES organizations(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
);
```

#### 2. `workforce_planning_role_forecasts`

```sql
CREATE TABLE workforce_planning_role_forecasts (
  id BIGINT PRIMARY KEY,
  scenario_id BIGINT NOT NULL,
  role_id BIGINT NOT NULL,
  department_id BIGINT,
  location_id BIGINT,

  -- Dotación proyectada
  headcount_current INT,                -- Dotación actual
  headcount_projected INT,              -- Dotación futura proyectada
  growth_rate DECIMAL(5,2),             -- % de crecimiento
  variance_reason TEXT,                 -- Justificación del cambio

  -- Skills requeridas futuro
  critical_skills JSON,                 -- Array de skill_ids críticas
  emerging_skills JSON,                 -- Array de skill_ids emergentes
  declining_skills JSON,                -- Array de skill_ids en declive

  -- Status
  status ENUM('draft', 'approved', 'archived'),
  approved_by BIGINT,
  approved_at TIMESTAMP,

  created_at TIMESTAMP,
  updated_at TIMESTAMP,

  FOREIGN KEY (scenario_id) REFERENCES workforce_planning_scenarios(id),
  FOREIGN KEY (role_id) REFERENCES roles(id),
  FOREIGN KEY (department_id) REFERENCES departments(id)
};
```

#### 3. `workforce_planning_matches`

```sql
CREATE TABLE workforce_planning_matches (
  id BIGINT PRIMARY KEY,
  scenario_id BIGINT NOT NULL,
  forecast_id BIGINT NOT NULL,
  person_id BIGINT NOT NULL,

  -- Evaluación del match
  match_score DECIMAL(5,2),             -- 0-100
  skill_match DECIMAL(5,2),             -- Cobertura de skills requeridas
  readiness_level ENUM('immediate', 'short_term', 'long_term', 'not_ready'),
  gaps JSON,                            -- Array de skills con gap

  -- Tipo de transición
  transition_type ENUM('promotion', 'lateral', 'reskilling', 'no_match'),
  transition_months INT,                -- Meses requeridos para la transición
  development_path_id BIGINT,           -- Link a learning path si aplica

  -- Score de riesgo
  risk_score DECIMAL(5,2),              -- 0-100 (rotación, fit cultural, etc)
  risk_factors JSON,                    -- ["alto_costo", "baja_cultura_fit"]

  recommendation TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,

  FOREIGN KEY (scenario_id) REFERENCES workforce_planning_scenarios(id),
  FOREIGN KEY (forecast_id) REFERENCES workforce_planning_role_forecasts(id),
  FOREIGN KEY (person_id) REFERENCES people(id),
  FOREIGN KEY (development_path_id) REFERENCES development_paths(id)
};
```

#### 4. `workforce_planning_skill_gaps`

```sql
CREATE TABLE workforce_planning_skill_gaps (
  id BIGINT PRIMARY KEY,
  scenario_id BIGINT NOT NULL,
  department_id BIGINT,
  role_id BIGINT,

  -- Skill
  skill_id BIGINT NOT NULL,

  -- Gap analysis
  current_proficiency DECIMAL(3,1),     -- Nivel actual (0-10)
  required_proficiency DECIMAL(3,1),    -- Nivel requerido futuro (0-10)
  gap DECIMAL(3,1),                     -- required - current

  -- Cobertura
  people_with_skill INT,                -- Cuántos en la org tienen esta skill
  coverage_percentage DECIMAL(5,2),     -- % de cobertura actual

  priority ENUM('critical', 'high', 'medium', 'low'),
  remediation_strategy ENUM('training', 'hiring', 'reskilling', 'outsourcing'),
  estimated_cost DECIMAL(10,2),
  timeline_months INT,

  created_at TIMESTAMP,
  updated_at TIMESTAMP,

  FOREIGN KEY (scenario_id) REFERENCES workforce_planning_scenarios(id),
  FOREIGN KEY (skill_id) REFERENCES skills(id),
  FOREIGN KEY (role_id) REFERENCES roles(id)
};
```

#### 5. `workforce_planning_succession_plans`

```sql
CREATE TABLE workforce_planning_succession_plans (
  id BIGINT PRIMARY KEY,
  scenario_id BIGINT NOT NULL,
  role_id BIGINT NOT NULL,
  department_id BIGINT,

  -- Rol crítico
  criticality_level ENUM('critical', 'important', 'standard'),
  impact_if_vacant TEXT,                -- Descripción del impacto

  -- Sucesores potenciales
  primary_successor_id BIGINT,
  secondary_successor_id BIGINT,
  tertiary_successor_id BIGINT,

  -- Status del sucesor principal
  primary_readiness_level ENUM('ready_now', 'ready_12m', 'ready_24m', 'not_ready'),
  primary_readiness_percentage INT,     -- 0-100
  primary_gap_json JSON,                -- Skills a desarrollar

  -- Plan de desarrollo para sucesor
  development_plan_id BIGINT,           -- Link a development path

  -- Riesgos
  succession_risk TEXT,
  mitigation_actions TEXT,

  status ENUM('draft', 'approved', 'monitoring', 'executed', 'archived'),
  approved_by BIGINT,
  approved_at TIMESTAMP,

  created_at TIMESTAMP,
  updated_at TIMESTAMP,

  FOREIGN KEY (scenario_id) REFERENCES workforce_planning_scenarios(id),
  FOREIGN KEY (role_id) REFERENCES roles(id),
  FOREIGN KEY (primary_successor_id) REFERENCES people(id),
  FOREIGN KEY (secondary_successor_id) REFERENCES people(id),
  FOREIGN KEY (tertiary_successor_id) REFERENCES people(id),
  FOREIGN KEY (development_plan_id) REFERENCES development_paths(id)
};
```

#### 6. `workforce_planning_analytics`

```sql
CREATE TABLE workforce_planning_analytics (
  id BIGINT PRIMARY KEY,
  scenario_id BIGINT NOT NULL,

  -- Métricas generales
  total_headcount_current INT,
  total_headcount_projected INT,
  net_growth INT,

  -- Cobertura interna
  internal_coverage_percentage DECIMAL(5,2),    -- % cubierto con talento interno
  external_gap_percentage DECIMAL(5,2),         -- % que requiere reclutamiento

  -- Skills
  total_skills_required INT,
  skills_with_gaps INT,
  critical_skills_at_risk INT,

  -- Sucesión
  critical_roles INT,
  critical_roles_with_successor INT,
  succession_risk_percentage DECIMAL(5,2),

  -- Estimaciones
  estimated_recruitment_cost DECIMAL(12,2),
  estimated_training_cost DECIMAL(12,2),
  estimated_external_hiring_months DECIMAL(4,1),

  -- Riesgos
  high_risk_positions INT,
  medium_risk_positions INT,

  calculated_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,

  FOREIGN KEY (scenario_id) REFERENCES workforce_planning_scenarios(id)
};
```

### Relaciones con Modelos Existentes

```
workforce_planning_scenarios
├── organizations (1:N)
├── departments (N:N)
├── people (N:N through matches)
├── roles (N:N)
└── skills (N:N)

workforce_planning_role_forecasts
├── roles
├── departments
├── skills (JSON array)
└── workforce_planning_scenarios

workforce_planning_matches
├── people
├── roles (through forecast)
├── development_paths (para learning paths)
└── people_role_skills (para validación)

workforce_planning_succession_plans
├── roles
├── people (3 niveles: principal, secundario, terciario)
└── development_paths
```

---

## Endpoints API

### Authentication

```
POST /api/auth/login
POST /api/auth/logout
GET /api/auth/user
```

### Scenarios Management

#### GET /api/workforce-planning/scenarios

```typescript
// Query params: page, per_page, status, fiscal_year
// Response
{
  data: [
    {
      id: 1,
      name: "Escenario Base 2026",
      description: "Proyección base sin cambios",
      horizon_months: 12,
      status: "active",
      fiscal_year: 2026,
      created_at: "2026-01-04T10:00:00Z",
      created_by: {
        id: 1,
        name: "Omar"
      }
    }
  ],
  pagination: { current_page: 1, total: 5, per_page: 20 }
}
```

#### POST /api/workforce-planning/scenarios

```typescript
// Request
{
  name: "Escenario Agresivo 2026",
  description: "Escenario con crecimiento acelerado",
  horizon_months: 24,
  fiscal_year: 2026
}

// Response: { id, name, status: "draft", ... }
```

#### GET /api/workforce-planning/scenarios/{id}

```typescript
// Response: Escenario completo con todas las relaciones
{
  id: 1,
  name: "...",
  role_forecasts: [
    {
      id: 100,
      role_id: 5,
      role: { id: 5, name: "Senior Developer" },
      headcount_current: 10,
      headcount_projected: 15,
      growth_rate: 50,
      critical_skills: [1, 3, 5],
      emerging_skills: [12, 15]
    }
  ],
  analytics: { ... }
}
```

#### PUT /api/workforce-planning/scenarios/{id}

```typescript
// Update scenario
{
  name: "...",
  status: "approved"
}
```

#### DELETE /api/workforce-planning/scenarios/{id}

```
// Soft delete / Archive
```

### Role Forecasts

#### POST /api/workforce-planning/scenarios/{scenario_id}/role-forecasts

```typescript
// Request
{
  role_id: 5,
  department_id: 2,
  headcount_current: 10,
  headcount_projected: 15,
  growth_rate: 50,
  critical_skills: [1, 3, 5],
  emerging_skills: [12, 15],
  variance_reason: "Expansión de producto digital"
}

// Response: Created forecast
```

#### GET /api/workforce-planning/scenarios/{scenario_id}/role-forecasts

```
List all forecasts for a scenario
```

#### GET /api/workforce-planning/scenarios/{scenario_id}/role-forecasts/{forecast_id}

```
Get specific forecast with related data
```

#### PUT /api/workforce-planning/scenarios/{scenario_id}/role-forecasts/{forecast_id}

```
Update forecast
```

### Matching & Cobertura Interna

#### GET /api/workforce-planning/scenarios/{scenario_id}/matches

```typescript
// Query params: role_id, department_id, sort_by (match_score), filter_by (readiness_level)
// Response
{
  data: [
    {
      id: 1,
      person: {
        id: 15,
        name: "Juan García",
        current_role: "Mid-Level Developer",
      },
      target_role: {
        id: 5,
        name: "Senior Developer",
      },
      match_score: 85,
      skill_match: 90,
      readiness_level: "short_term",
      gaps: [
        {
          skill_id: 3,
          skill_name: "Cloud Architecture",
          current: 5,
          required: 8,
          gap: 3,
        },
      ],
      transition_type: "promotion",
      transition_months: 6,
      risk_score: 15,
    },
  ];
}
```

#### POST /api/workforce-planning/scenarios/{scenario_id}/calculate-matches

```typescript
// POST (sin body o con parámetros específicos)
// Endpoint que triggerea el algoritmo de matching
// Response: { matches: [...], coverage_percentage: 75, gaps: [...] }
```

#### GET /api/workforce-planning/scenarios/{scenario_id}/skill-gaps

```typescript
// Query params: priority, department_id
// Response
{
  data: [
    {
      id: 1,
      skill: {
        id: 3,
        name: "Kubernetes",
      },
      current_proficiency: 4.5,
      required_proficiency: 7.5,
      gap: 3.0,
      people_with_skill: 3,
      coverage_percentage: 25,
      priority: "critical",
      remediation_strategy: "training",
      estimated_cost: 15000,
      timeline_months: 4,
    },
  ];
}
```

### Succession Planning

#### GET /api/workforce-planning/scenarios/{scenario_id}/succession-plans

```typescript
// Query params: criticality_level, department_id, status
// Response
{
  data: [
    {
      id: 1,
      role: { id: 10, name: "VP Engineering" },
      criticality_level: "critical",
      impact_if_vacant: "Paraliza decisiones técnicas críticas",
      primary_successor: {
        id: 45,
        name: "Ana López",
        readiness_level: "ready_12m",
        readiness_percentage: 75,
        development_plan_id: 123
      },
      secondary_successor: { ... },
      status: "approved"
    }
  ]
}
```

#### POST /api/workforce-planning/scenarios/{scenario_id}/succession-plans

```typescript
{
  role_id: 10,
  criticality_level: "critical",
  primary_successor_id: 45,
  secondary_successor_id: 46,
  primary_readiness_level: "ready_12m",
  development_plan_id: 123,
  succession_risk: "Baja documentación técnica"
}
```

### Analytics

#### GET /api/workforce-planning/scenarios/{scenario_id}/analytics

```typescript
// Response
{
  total_headcount_current: 250,
  total_headcount_projected: 290,
  net_growth: 40,
  internal_coverage_percentage: 75,
  external_gap_percentage: 25,
  total_skills_required: 45,
  skills_with_gaps: 18,
  critical_skills_at_risk: 5,
  critical_roles: 8,
  critical_roles_with_successor: 6,
  succession_risk_percentage: 25,
  estimated_recruitment_cost: 150000,
  estimated_training_cost: 85000,
  estimated_external_hiring_months: 4.5,
  high_risk_positions: 3,
  medium_risk_positions: 8
}
```

#### GET /api/workforce-planning/dashboard/summary

```typescript
// Vista general de todos los escenarios activos
{
  active_scenarios: 3,
  total_headcount_variance: 45,
  average_internal_coverage: 72,
  critical_roles_without_succession: 2,
  skills_needing_attention: 12,
  estimated_total_cost: 250000
}
```

---

## Componentes Frontend

### 1. Página Principal: WorkforcePlanning/Index.vue

**Ubicación:** `src/resources/js/pages/WorkforcePlanning/Index.vue`

**Estructura:**

```
┌─────────────────────────────────────────┐
│ Header: "Workforce Planning"             │
│ Subheader: "Planificación de dotación"  │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ SELECTOR DE ESCENARIO                   │
│ [Dropdown: Escenarios] [+ Nuevo]        │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ TAB 1: OVERVIEW (Dashboard)              │
│ - Cards KPI (dotación, gaps, etc)       │
│ - Gráficos: cobertura interna vs externa│
│ - Roles críticos sin sucesión            │
│ - Skills en riesgo                      │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ TAB 2: ROLE FORECASTS (Proyecciones)    │
│ - Tabla de roles con proyecciones       │
│ - Editar dotación futura                │
│ - Skills críticas por rol                │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ TAB 3: INTERNAL MATCHING (Cobertura)    │
│ - Matching automático                   │
│ - Candidatos por rol                     │
│ - Gaps de skills por persona             │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ TAB 4: SUCCESSION PLANNING (Sucesión)   │
│ - Roles críticos                        │
│ - Sucesores potenciales                  │
│ - Readiness levels                       │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ TAB 5: SKILL GAPS (Brechas de Skills)   │
│ - Matriz de gaps por skill               │
│ - Estrategias de remediación            │
│ - Costos estimados                      │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ TAB 6: ANALYTICS (Reportes)             │
│ - KPIs consolidados                     │
│ - Gráficos detallados                   │
│ - Exportar reportes                     │
└─────────────────────────────────────────┘
```

### 2. Componentes Específicos

#### WorkforcePlanning/ScenarioSelector.vue

```typescript
// Props
{
  scenarios: Scenario[],
  modelValue: Scenario
}

// Emits
emit('update:modelValue', scenario)
emit('create:scenario')
```

#### WorkforcePlanning/OverviewDashboard.vue

```
KPI Cards:
- Total Headcount (Current vs Projected)
- Internal Coverage %
- External Gap %
- Critical Skills at Risk
- Succession Risk %
- Estimated Costs (Recruitment + Training)

Charts:
- Line: Headcount evolution
- Pie: Internal vs External coverage
- Bar: Skills gaps by priority
- Heatmap: Critical roles readiness
```

#### WorkforcePlanning/RoleForecastsTable.vue

```
Columns:
- Role Name
- Department
- Headcount Current
- Headcount Projected
- Growth %
- Critical Skills
- Status
- Actions (Edit, Delete)

Features:
- Inline editing
- Sortable
- Filterable por department/role
```

#### WorkforcePlanning/MatchingResults.vue

```
- Role selector
- Results table:
  - Person Name
  - Match Score (% badge)
  - Skill Match %
  - Readiness Level
  - Gaps (expandable)
  - Transition Type
  - Actions (View Details, Assign Development Plan)

- Visualization:
  - Match score histogram
  - Readiness distribution
```

#### WorkforcePlanning/SuccessionPlanCard.vue

```
Per Critical Role:
- Role Name + Criticality
- Primary Successor (readiness %)
- Secondary Successor
- Tertiary Successor
- Impact if Vacant
- Development Plan Link
- Status Badge

Actions:
- Edit
- Approve
- View Development Plan
```

#### WorkforcePlanning/SkillGapsMatrix.vue

```
Matrix:
- Rows: Skills
- Columns: Departments / Roles
- Cell: Gap size + Color coding
  - Red: Critical gap
  - Orange: High gap
  - Yellow: Medium gap
  - Green: No gap

Interactions:
- Click cell → Details
- Filter by priority
- Show remediation options
```

---

## User Stories

### Bloque 1: Base Estratégica

**US-WFP-1.1:** Como HR Manager, quiero ver el catálogo de roles y skills para entender qué estructura de talento tengo hoy.

**Criterios:**

- [x] Ver listado de roles organizacionales
- [x] Ver diccionario de skills técnicas y conductuales
- [x] Ver mapeo de roles ↔ skills requeridas
- [x] Buscar/filtrar roles por familia, nivel, departamento

**Estimación:** 8 pts

---

### Bloque 2: Oferta Interna

**US-WFP-2.1:** Como HR Manager, quiero ver el perfil de skills de cada persona para identificar talento disponible.

**Criterios:**

- [x] Ver skill profile de cada persona
- [x] Ver proficiency levels
- [x] Identificar skills dominantes/emergentes/críticas
- [x] Ver historial de desarrollo de skills

**Estimación:** 5 pts

**US-WFP-2.2:** Como Manager, quiero acceder al marketplace interno para publicar vacantes y ver candidatos sugeridos.

**Criterios:**

- [x] Crear publicación de vacante/rol interno
- [x] Sistema sugiere candidatos internos por skills
- [x] Ver ranking de candidatos con match score
- [x] Postularse a vacantes como empleado

**Estimación:** 13 pts

---

### Bloque 3: Demanda Futura

**US-WFP-3.1:** Como Planning Manager, quiero crear un escenario de demanda futura para proyectar necesidades de talento.

**Criterios:**

- [x] Crear nuevo escenario (Base/Conservador/Agresivo)
- [x] Definir horizon (12/24/36 meses)
- [x] Proyectar dotación por rol/área
- [x] Identificar roles emergentes y en declive
- [x] Guardar como borrador, enviar a aprobación

**Estimación:** 13 pts

**US-WFP-3.2:** Como Strategy Head, quiero revisar y aprobar escenarios de demanda para validar alineación con negocio.

**Criterios:**

- [x] Ver escenarios en estado "pending_approval"
- [x] Revisar proyecciones y justificaciones
- [x] Aprobar o rechazar con comentarios
- [x] Notificaciones al creador

**Estimación:** 5 pts

---

### Bloque 4: Matching Interno

**US-WFP-4.1:** Como HR Analyst, quiero ejecutar el algoritmo de matching para identificar cobertura interna.

**Criterios:**

- [x] Seleccionar escenario
- [x] Calcular matches automáticamente
- [x] Ver candidatos sugeridos por rol
- [x] Ver match score y skill gaps
- [x] Filtrar por readiness level

**Estimación:** 13 pts

**US-WFP-4.2:** Como HR Manager, quiero ver el summary de cobertura interna para entender gaps externos.

**Criterios:**

- [x] Dashboard con % cobertura interna
- [x] % que requiere reclutamiento externo
- [x] Estimación de brecha por área/rol
- [x] Comparar escenarios

**Estimación:** 8 pts

---

### Bloque 4+: Skill Gaps

**US-WFP-4.3:** Como HR Analyst, quiero identificar brechas de skills críticas para planificar desarrollo.

**Criterios:**

- [x] Ver matriz de gaps (skills vs. cobertura)
- [x] Prioridad de gaps (crítico/alto/medio/bajo)
- [x] Personas por skill actual/requerida
- [x] Estrategias de remediación (training/hiring/reskilling)
- [x] Estimar costos y timeline

**Estimación:** 13 pts

---

### Bloque 4+: Succession Planning

**US-WFP-4.4:** Como HR Manager, quiero crear planes de sucesión para roles críticos para asegurar continuidad.

**Criterios:**

- [x] Identificar roles críticos
- [x] Asignar sucesores potenciales (primario/secundario/terciario)
- [x] Evaluar readiness level de sucesores
- [x] Crear/linkar development plans
- [x] Aprobar planes de sucesión

**Estimación:** 13 pts

**US-WFP-4.5:** Como Executive, quiero ver el status de sucesión en roles críticos para gestionar riesgos.

**Criterios:**

- [x] Dashboard de sucesión
- [x] Roles críticos sin sucesor
- [x] Readiness timeline
- [x] Riesgos potenciales
- [x] Status de development plans

**Estimación:** 8 pts

---

### Analytics & Reporting

**US-WFP-5.1:** Como Executive, quiero ver el dashboard consolidado de Workforce Planning para tomar decisiones estratégicas.

**Criterios:**

- [x] KPIs principales (headcount, gaps, succession, costs)
- [x] Gráficos de cobertura interna vs externa
- [x] Skills en riesgo
- [x] Roles críticos sin sucesión
- [x] Estimaciones de costos (recruitment + training)
- [x] Exportar reporte PDF/Excel

**Estimación:** 13 pts

---

## Criterios de Aceptación

### Criterios Técnicos

```
✅ Base de Datos
   - [x] Todas las tablas creadas y migradas
   - [x] Relaciones FK correctas
   - [x] Índices en columnas de búsqueda
   - [x] Seeders con datos de prueba

✅ Backend (APIs)
   - [x] Todos los endpoints implementados
   - [x] Validaciones en controllers
   - [x] Lógica en services/repositories
   - [x] Error handling y response consistent
   - [x] Autenticación y autorización
   - [x] Tests unitarios (>80% coverage)
   - [x] Tests de integración

✅ Frontend (Vue 3 + TypeScript)
   - [x] Componentes creados según especificación
   - [x] Tipos TypeScript completos
   - [x] Validaciones de formularios
   - [x] Estados de loading/error
   - [x] Responsive design
   - [x] Tests E2E (happy path + edge cases)
   - [x] Accesibilidad (WCAG AA mínimo)

✅ Integración
   - [x] APIs conectadas correctamente
   - [x] Datos fluyen entre componentes
   - [x] Puedo crear scenario → agregar forecasts → calcular matches
   - [x] Puedo crear succession plans y linkar development paths
```

### Criterios Funcionales

```
✅ Escenarios
   - [x] Crear, editar, eliminar escenarios
   - [x] Status: draft → approved → archived
   - [x] Duplicar escenario para comparar

✅ Role Forecasts
   - [x] Agregar proyecciones por rol
   - [x] Validar que headcount_projected > 0
   - [x] Skills críticas/emergentes/declining mapeadas

✅ Matching
   - [x] Algoritmo calcula scores correctamente
   - [x] Sugerencias ordenadas por match score DESC
   - [x] Gaps mostrados por skill con valores numéricos

✅ Succession
   - [x] Roles críticos identificados
   - [x] Sucesores primario/secundario/terciario asignados
   - [x] Readiness levels calculados correctamente
   - [x] Plans pueden ser aprobados

✅ Skill Gaps
   - [x] Matriz calcula gaps por skill
   - [x] Prioridades asignadas automáticamente
   - [x] Remediation strategies sugeridas
   - [x] Costos estimados realistas

✅ Analytics
   - [x] KPIs consolidados correctos
   - [x] Gráficos renderean correctamente
   - [x] Puede filtrar y comparar escenarios
   - [x] Export a PDF/Excel funciona
```

### Criterios de Calidad

```
✅ Código
   - [x] Sin errores console
   - [x] Sin warnings eslint
   - [x] Code style consistente
   - [x] Documentación de métodos/componentes

✅ UX
   - [x] Flujos intuitivos
   - [x] Mensajes claros
   - [x] Estados visuales (loading, error, success)
   - [x] No más de 3 clics para tarea común

✅ Performance
   - [x] Dashboard carga en < 2s
   - [x] Algoritmo matching < 5s para 1000 personas
   - [x] No memory leaks
   - [x] Pagination para listas grandes
```

---

## Integración con Módulos Existentes

### Con People/Skills (Existente)

**Conexión:**

- People.skills → workforce_planning_matches
- Skills.proficiency_levels → match calculation
- PeopleRoleSkills → skill profile construction

**API Calls:**

```typescript
// Get person skill profile
GET /api/people/{person_id}/skills
GET /api/people/{person_id}/role-skills?role_id={role_id}

// Skills diccionario
GET /api/skills
GET /api/skills/{skill_id}/people?proficiency_level=advanced
```

### Con Roles (Existente)

**Conexión:**

- Roles.skills_required → forecast demands
- Roles.level → succession planning

**API Calls:**

```typescript
// Get role details
GET /api/roles/{role_id}
GET /api/roles/{role_id}/required-skills

// Roles por departamento
GET /api/roles?department_id={id}
```

### Con Development Paths (Existente)

**Conexión:**

- DevelopmentPath → workforce_planning_matches
- DevelopmentPath → succession_plans
- DevelopmentPath.skills → gap remediation

**API Calls:**

```typescript
// Create dev path para matched person
POST / api / development - paths;
{
  (person_id, target_role_id, skills_to_develop);
}

// Link dev path a succession plan
PUT / api / workforce -
  planning / scenarios / { id } / succession -
  plans / { id };
{
  development_plan_id: 123;
}
```

### Con Dashboard (Existente)

**Integración:**

- Dashboard.Analytics mostrará widget de "Workforce Planning Summary"
- Links a scenarios activos
- Alertas de succession risk, skill gaps críticas

**Widget:**

```vue
<!-- Dashboard.vue -->
<WorkforcePlanningWidget
  v-if="user.has_role('HR_MANAGER')"
  :summary="workforceSummary"
/>
```

---

## Timeline Estimado (MVP Fase 2)

| Componente          | Estimación | Notas                         |
| ------------------- | ---------- | ----------------------------- |
| Base de Datos       | 3 pts      | Migraciones + seeders         |
| APIs Backend        | 21 pts     | Controllers, services, repos  |
| Frontend Index      | 13 pts     | Tabs, componentes principales |
| Matching Algoritmo  | 13 pts     | Logic core, tests             |
| Succession Planning | 8 pts      | Componentes, crud             |
| Analytics           | 8 pts      | Dashboard, charts             |
| Tests               | 13 pts     | Unit, integration, E2E        |
| Documentación       | 5 pts      | Docs, API docs                |
| **TOTAL**           | **84 pts** | ~2-3 sprints @ 30pts/sprint   |

---

## Próximos Pasos

1. ✅ Revisión de esta especificación
2. ⏳ Ajustes según feedback
3. ⏳ Crear rama `feature/workforce-planning`
4. ⏳ Desarrollo iterativo con tests
5. ⏳ Merge a main cuando tests pasen

---

**Versión:** 1.0  
**Última actualización:** 4 Enero 2026  
**Estado:** 🔄 Listo para revisión técnica
