### Diagrama(s) para documentar el módulo **Workforce Planning (WFP)** + **Modelamiento de Escenarios** (TalentIA)

A continuación tienes un set de diagramas (en **Mermaid**) para documentación: **qué hace**, **cómo se usa**, **componentes**, **elementos principales** y **cómo funciona** (workflow + estados + versionamiento). Puedes pegarlos en tu README, Wiki, Notion o cualquier soporte que renderice Mermaid.

#### 1) Vista de arquitectura (componentes y responsabilidades)

```mermaid
flowchart LR
  subgraph Actors["Actores"]
    CHRO["CHRO / Dirección"]
    HRBP["HRBP / Talent Manager"]
    AREA["Gerente de Área"]
    CONS["Consultor"]
  end

  subgraph UI["Frontend (Vue 3 + TS + Vuetify)"]
    WFP_BASE["WFP Baseline\n(Foto actual / capacidades actuales)"]
    SCN_LIST["Escenarios\n(Lista/Filters)"]
    SCN_STEP["Detalle Escenario\n(7 pasos / Stepper)"]
    SCN_ROLLUP["Vista Padre\n(Consolidación de Hijos)"]
    SCN_VERSION["Selector de Versiones\n(v1, v2, ...)"]
  end

  subgraph API["Backend (Laravel)"]
    CTRL["Controllers API\n(Scenarios, Templates, Versions)"]
    POL["Policies + Guards\n(multi-tenant + readonly approved)"]
    SVC["WorkforcePlanningService\n(core business logic)"]
    CALC["Supply/Gaps Engine\n(cálculo inventario y brechas)"]
    REC["Rules Recommender\n(estrategias sugeridas MVP)"]
    VERS["Versioning Engine\n(clon profundo + inmutabilidad)"]
    ROLL["Roll-up Engine\n(agrega hijos al padre)"]
    AUD["Audit Logger\n(status events)"]
  end

  subgraph DB["PostgreSQL (Multi-tenant)"]
    TPL["scenario_templates (global)"]
    SCN["workforce_scenarios\n(org_id + parent_id + scope + estados + versioning)"]
    DEM["scenario_skill_demands\n(demand + supply cache + flags heredadas)"]
    STR["scenario_closure_strategies\n(plan de cierre)"]
    MIL["scenario_milestones\n(hitos)"]
    EVT["scenario_status_events\n(auditoría)"]
    SKL["skills\n(scope_type: transversal/domain/specific)"]
    PS["person_skills\n(inventario base)"]
  end

  CHRO --> UI
  HRBP --> UI
  AREA --> UI
  CONS --> UI

  UI --> CTRL
  CTRL --> POL
  POL --> SVC

  SVC --> CALC
  SVC --> REC
  SVC --> VERS
  SVC --> ROLL
  SVC --> AUD

  CALC --> PS
  CALC --> DEM
  REC --> STR
  VERS --> SCN
  VERS --> DEM
  VERS --> STR
  VERS --> MIL
  ROLL --> SCN
  ROLL --> DEM
  AUD --> EVT

  CTRL --> TPL
  CTRL --> SCN
  CTRL --> SKL
```

**Lectura rápida:**  
- **WFP Baseline** vive como “foto actual” (skills reales desde `person_skills`).  
- **Escenarios** son una capa proyectiva encima: demandan skills futuras, calculan gaps, proponen estrategias, se aprueban y (si se ejecutan) se monitorean.  
- **Approved = inmutable** → cambios se hacen mediante **nueva versión**.

#### 2) Modelo de dominio (entidades y relaciones principales)

```mermaid
erDiagram
  ORGANIZATIONS ||--o{ USERS : has
  ORGANIZATIONS ||--o{ SKILLS : owns
  USERS ||--o{ PERSON_SKILLS : has
  SKILLS ||--o{ PERSON_SKILLS : measured_in

  SCENARIO_TEMPLATES ||--o{ WORKFORCE_SCENARIOS : instantiates

  ORGANIZATIONS ||--o{ WORKFORCE_SCENARIOS : plans
  WORKFORCE_SCENARIOS ||--o{ SCENARIO_SKILL_DEMANDS : defines
  WORKFORCE_SCENARIOS ||--o{ SCENARIO_CLOSURE_STRATEGIES : proposes
  WORKFORCE_SCENARIOS ||--o{ SCENARIO_MILESTONES : tracks
  WORKFORCE_SCENARIOS ||--o{ SCENARIO_STATUS_EVENTS : audits

  WORKFORCE_SCENARIOS ||--o{ WORKFORCE_SCENARIOS : parent_child

  SKILLS ||--o{ SCENARIO_SKILL_DEMANDS : demanded
  SKILLS ||--o{ SCENARIO_CLOSURE_STRATEGIES : closed_by

  USERS ||--o{ WORKFORCE_SCENARIOS : created_by
  USERS ||--o{ SCENARIO_CLOSURE_STRATEGIES : assigned_to
  USERS ||--o{ SCENARIO_STATUS_EVENTS : changed_by

  WORKFORCE_SCENARIOS {
    bigint id
    bigint organization_id
    bigint parent_id
    string name
    enum scope_type
    bigint scope_id
    uuid version_group_id
    int version_number
    bool is_current_version
    enum decision_status
    enum execution_status
    timestamp approved_at
    bigint approved_by
  }

  SKILLS {
    bigint id
    bigint organization_id
    string name
    enum scope_type  "transversal|domain|specific"
    string domain_tag
  }

  SCENARIO_SKILL_DEMANDS {
    bigint id
    bigint scenario_id
    bigint skill_id
    int required_headcount
    decimal required_level
    int current_headcount
    decimal current_avg_level
    enum priority
    bool is_mandatory_from_parent
  }
```

**Qué representa:**
- Un **escenario** puede ser **padre** (corporativo) y tener **hijos** (por área).  
- Las **skills** NO se organizan en árbol; se clasifican por alcance (`transversal/domain/specific`).  
- La demanda se define en `scenario_skill_demands`, y el supply se calcula desde `person_skills` (y se puede cachear).

#### 3) Máquina de estados (decisión vs ejecución + reglas clave)

```mermaid
stateDiagram-v2
  state "Decision Status" as DS {
    [*] --> draft
    draft --> simulated: simulate()
    simulated --> proposed: submit_for_review()
    proposed --> approved: approve()
    proposed --> rejected: reject()
    approved --> archived: archive()
    rejected --> archived: archive()
  }

  state "Execution Status" as ES {
    [*] --> not_started
    not_started --> in_progress: start()
    in_progress --> paused: pause()
    paused --> in_progress: resume()
    in_progress --> completed: complete()
  }

  note right of DS
    Regla: si decision_status == approved
    => escenario INMUTABLE
    Cambios => createNewVersion()
  end note

  note right of ES
    Regla: execution_status != not_started
    solo permitido si decision_status == approved
  end note
```

**Idea central:**  
- `simulated` = “what-if con cálculos”, no implica plan en marcha.  
- `approved` = “plan oficial”, queda **bloqueado**.  
- La ejecución (start/pause/complete) solo existe si está `approved`.

#### 4) Flujo de uso (cómo se usa y cómo “lo hace”)

```mermaid
sequenceDiagram
  autonumber
  actor U as Usuario (CHRO/HRBP/Gerente/Consultor)
  participant FE as Frontend (Stepper 7 pasos)
  participant API as API Laravel
  participant SVC as WorkforcePlanningService
  participant DB as PostgreSQL

  U->>FE: Crear Escenario (desde plantilla o en blanco)
  FE->>API: POST /workforce-scenarios/from-template
  API->>SVC: createScenarioFromTemplate()
  SVC->>DB: INSERT workforce_scenarios (draft, scope, parent_id?)
  SVC->>DB: INSERT scenario_skill_demands (incluye transversales)
  SVC-->>API: escenario creado
  API-->>FE: OK

  U->>FE: Paso 2-3: Completar demanda (headcount + nivel)
  FE->>API: PATCH /workforce-scenarios/{id}
  API->>DB: UPDATE scenario_skill_demands (solo si no approved)

  U->>FE: Simular (Paso 4: brechas)
  FE->>API: POST /workforce-scenarios/{id}/simulate
  API->>SVC: calculateSupply() + calculateGaps()
  SVC->>DB: SELECT person_skills (filtrado por scope)
  SVC->>DB: UPDATE scenario_skill_demands.current_* (cache)
  SVC-->>API: gaps + summary
  API-->>FE: Render brechas (simulated)

  U->>FE: Generar estrategias sugeridas (Paso 5)
  FE->>API: POST /workforce-scenarios/{id}/refresh-suggested-strategies
  API->>SVC: recommendStrategiesForGap() + upsert strategies
  SVC->>DB: INSERT scenario_closure_strategies (proposed)
  API-->>FE: OK (n creadas)

  U->>FE: Enviar a aprobación
  FE->>API: POST /workforce-scenarios/{id}/decision-status {to: proposed}
  API->>SVC: transitionDecisionStatus()
  SVC->>DB: INSERT scenario_status_events

  U->>FE: Aprobar
  FE->>API: POST /workforce-scenarios/{id}/decision-status {to: approved}
  API->>SVC: approveScenario()
  SVC->>DB: UPDATE workforce_scenarios(decision_status=approved, approved_at/by)
  SVC->>DB: INSERT scenario_status_events
  API-->>FE: Escenario bloqueado (read-only)

  U->>FE: (Opcional) Iniciar ejecución
  FE->>API: POST /workforce-scenarios/{id}/execution/start
  API->>SVC: startExecution()
  SVC->>DB: UPDATE workforce_scenarios(execution_status=in_progress)
  SVC->>DB: INSERT scenario_status_events

  U->>FE: Necesito cambios al plan aprobado
  FE->>API: POST /workforce-scenarios/{id}/versions
  API->>SVC: createNewVersion(clon profundo)
  SVC->>DB: INSERT new workforce_scenarios (draft, version+1, is_current_version=true)
  SVC->>DB: CLONE demands/strategies/milestones
  SVC->>DB: UPDATE old scenario is_current_version=false
  API-->>FE: Nueva versión editable creada
```

---

### Cómo explicar “qué hace” y “cómo lo hace” (texto para documentación)

#### Qué hace
- Permite **planificar el talento futuro** mediante **escenarios** (what-if → plan aprobado → ejecución), en lugar de solo describir el presente.  
- Soporta **escenarios globales** y **escenarios por área/familia de roles**, y permite **consolidación** (roll-up) cuando existe un escenario padre.  
- Mantiene trazabilidad: lo aprobado queda **inmutable** y los cambios se hacen con **versiones**.

#### Cómo lo hace (mecánica interna)
- **Supply (inventario)**: se calcula desde `person_skills`, filtrando por el **scope** del escenario.  
- **Demand (futuro)**: se define en `scenario_skill_demands` (headcount requerido + nivel requerido).  
- **Gaps**: se calcula `gap_headcount` y `gap_level` comparando supply vs demand.  
- **Estrategias**: se proponen/capturan en `scenario_closure_strategies` (build/buy/borrow/bridge/bind/bot).  
- **Estados**: separa “decisión” (draft→simulated→proposed→approved) de “ejecución” (not_started→in_progress→paused→completed).  
- **Versionamiento**: al aprobar, el escenario se bloquea; si se requiere ajuste, se crea una nueva versión con clon profundo (demands/strategies/milestones).

---

Si quieres, puedo adaptar estos diagramas a tu nomenclatura real (por ejemplo si usas `employees` en vez de `users`, `org_units` en vez de `departments`, o si ya existe `role_families`). Solo dime cómo se llaman esas entidades en tu modelo actual.