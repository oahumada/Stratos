# Paso 2: Diseño de Roles y Competencias — Guía de Rediseño

> **Versión:** 2.0
> **Fecha:** 2026-02-24
> **Estado:** ✅ IMPLEMENTADO — Backend completo · Frontend Fases 1–4 operativo
> **Contexto:** Documento de referencia para el rediseño del Paso 2 del wizard de Planificación de Escenarios en Stratos.

---

## 1. Propósito del Paso 2

El **Paso 1** del wizard generó un _blueprint estratégico_: un mapa de Capacidades → Competencias → Skills → Roles sugeridos, producido por el LLM a partir del contexto organizacional.

El **Paso 2** tiene como misión transformar ese blueprint abstracto en **diseño organizacional concreto y validado**:

- Determinar qué roles deben crearse, evolucionar o eliminarse.
- Mapear cada rol a las competencias que requiere, con un tipo de cambio estratégico (mantención, transformación, enriquecimiento, extinción) y un nivel de maestría objetivo.
- Validar la coherencia arquitectónica de cada asignación usando el **Modelo del Cubo**.
- Aprobar el diseño para que roles y competencias nuevas/modificadas entren en estado `incubation` y sean visibles en el resto de la plataforma.

---

## 2. El Modelo del Cubo (marco teórico)

Cada rol en Stratos se posiciona en un cubo tridimensional:

```
         Nivel de Maestría (Y)
         5 │         ●
         4 │     ●       ●
         3 │  ●               ●
         2 │
         1 └──────────────────────── Proceso de Negocio (Z)
            \
             \ Arquetipo (X)
              E = Estratégico
              T = Táctico
              O = Operacional
```

| Dimensión                  | Valores          | Descripción                                           |
| -------------------------- | ---------------- | ----------------------------------------------------- |
| **Arquetipo (X)**          | E / T / O        | Nivel de responsabilidad y tipo de decisión del rol   |
| **Nivel de Maestría (Y)**  | 1 – 5            | Profundidad de dominio esperada para cada competencia |
| **Proceso de Negocio (Z)** | Variable por org | Área funcional donde opera el rol                     |

### Reglas de coherencia arquitectónica (Semáforo)

| Condición                               | Semáforo   | Acción sugerida                                                     |
| --------------------------------------- | ---------- | ------------------------------------------------------------------- |
| Arquetipo O + Nivel > 3 (sin referente) | 🟡 Info    | Sobrecarga técnica — considerar bajar nivel o marcar como Referente |
| Arquetipo O + Nivel > 3 + es Referente  | 🟢 OK      | Coherente — rol actúa como mentor técnico                           |
| Arquetipo E + Nivel < 4                 | 🔵 Info    | Competencia de apoyo — válida como no-core                          |
| Arquetipo T + Nivel > 4 (sin referente) | 🟠 Warning | Posible desalineación — considerar ascender a Estratégico           |
| Resto de combinaciones                  | 🟢 OK      | Coherente                                                           |

> **¿Por qué importa el cubo en el Paso 2?**
> Sin el cubo, la matriz es una tabla de checkboxes. Con el cubo, cada celda tiene semántica estratégica: no solo dice "este rol necesita esta competencia" sino "con qué profundidad y por qué razón arquitectónica". Eso convierte el Paso 2 en arquitectura de talento, no en administración de datos.

---

## 3. Tipos de cambio estratégico (change_type)

Cada asignación rol-competencia en la matriz tiene un tipo de cambio que indica la intención de diseño:

| Tipo             | Ícono | Significado                             | Cuándo usarlo                        |
| ---------------- | ----- | --------------------------------------- | ------------------------------------ |
| `maintenance`    | ✅    | La competencia se mantiene igual        | Rol estable, competencia ya presente |
| `transformation` | 🔄    | Requiere upskilling — salta de nivel    | Rol que crece en profundidad         |
| `enrichment`     | 📈    | Se añade una competencia nueva al rol   | Expansión horizontal del rol         |
| `extinction`     | 📉    | La competencia desaparecerá de este rol | Automatización o rediseño del puesto |

---

## 4. Tipos de propuesta de roles (del agente)

| Tipo      | Significado                                                    |
| --------- | -------------------------------------------------------------- |
| `NEW`     | Crear un rol completamente nuevo en el catálogo                |
| `EVOLVE`  | Tomar un rol existente y añadirle nuevas competencias          |
| `REPLACE` | Eliminar un rol obsoleto y proponer uno nuevo que lo reemplace |

## 5. Tipos de propuesta de competencias (del agente)

| Tipo      | Significado                                                   |
| --------- | ------------------------------------------------------------- |
| `ADD`     | Incorporar una nueva competencia al catálogo global           |
| `MODIFY`  | Actualizar la definición/niveles de una competencia existente |
| `REPLACE` | Sustituir una competencia vieja por una versión moderna       |

---

## 6. Flujo completo del Paso 2 (rediseñado)

```
┌─────────────────────────────────────────────────────────────────────┐
│  ESTADO INICIAL                                                      │
│  • Blueprint del Paso 1 listo (capabilities/competencies/roles LLM) │
│  • Catálogo activo de la organización disponible                     │
│  • Matriz del Paso 2: VACÍA                                          │
└─────────────────────────────┬───────────────────────────────────────┘
                              │
                              ▼
╔═════════════════════════════════════════════════════════════════════╗
║  FASE 1: CONSULTA A LOS AGENTES                          ✅ LISTO   ║
║  (obligatoria — primer paso siempre)                                ║
╠═════════════════════════════════════════════════════════════════════╣
║  Ruta: POST /api/scenarios/{id}/step2/design-talent                 ║
║  Servicio: TalentDesignOrchestratorService::orchestrate()           ║
║                                                                     ║
║  Input que recibe el agente:                                        ║
║  • Blueprint Paso 1 (caps → comps → skills → roles sugeridos)       ║
║  • Catálogo actual: roles activos de la organización                ║
║  • Catálogo actual: competencias activas de la organización         ║
║  • Roles del escenario con arquetipo, FTE, human_leverage           ║
║  • Mappings ya existentes (para no duplicar propuestas)             ║
║  • Horizonte del escenario (meses)                                  ║
║                                                                     ║
║  Output del agente (JSON estructurado):                             ║
║  • role_proposals[]    → NEW / EVOLVE / REPLACE                     ║
║    - proposed_name, description                                     ║
║    - archetype (E/T/O)                                              ║
║    - competency_mappings[]: competency_name/id + change_type +      ║
║      required_level + is_core + rationale                           ║
║    - fte_suggested, talent_composition (human% / synthetic%)        ║
║  • catalog_proposals[] → ADD / MODIFY / REPLACE                     ║
║    - proposed_name, action_rationale                                ║
║  • alignment_score: 0.0 – 1.0                                       ║
╚═════════════════════════════════════════════════════════════════════╝
                              │
                              ▼
╔═════════════════════════════════════════════════════════════════════╗
║  FASE 2: PANEL DE REVISIÓN DE PROPUESTAS                 ✅ LISTO   ║
║  (usuario revisa antes de que nada se escriba en BD)                ║
╠═════════════════════════════════════════════════════════════════════╣
║  Componente: AgentProposalsModal.vue (reescrito como panel)         ║
║                                                                     ║
║  Por cada role_proposal el usuario puede:                           ║
║  [✅ Aprobar]   → entra a la matriz con source='agent'             ║
║  [✏️  Modificar] → edit inline (arquetipo, FTE, niveles, change_type║
║  [❌ Rechazar]  → descartado (no genera ningún registro)            ║
║                                                                     ║
║  Semáforo del Cubo visible en tiempo real en la tabla de            ║
║  competency_mappings de cada propuesta.                             ║
║                                                                     ║
║  [Aprobar Todo]  [Rechazar Todo]  [Confirmar selección (N/M)]       ║
╚═════════════════════════════════════════════════════════════════════╝
                              │
                              │  POST /step2/agent-proposals/apply
                              │  (batch: persiste solo los aprobados)
                              ▼
╔═════════════════════════════════════════════════════════════════════╗
║  FASE 3: REVISIÓN Y AJUSTE MANUAL EN LA MATRIZ           ✅ LISTO   ║
║  (matriz ya poblada con propuestas aprobadas)                       ║
╠═════════════════════════════════════════════════════════════════════╣
║  La matriz muestra las celdas pre-llenadas con:                     ║
║  🤖 source='agent'  → celda con badge de origen                    ║
║  👤 source='manual' → celda sin badge (estilo neutral)             ║
║                                                                     ║
║  El usuario puede:                                                  ║
║  • Editar cualquier celda (click → RoleCompetencyStateModal)        ║
║  • Agregar competencias que el agente no propuso                    ║
║  • Eliminar propuestas que no convencen                             ║
║  • Agregar nuevos roles manualmente (+ Nuevo Rol)                   ║
║  • Volver a consultar agentes → ahora recibe mappings actuales      ║
║    para proponer solo lo que falta                                  ║
╚═════════════════════════════════════════════════════════════════════╝
                              │
                              ▼
╔═════════════════════════════════════════════════════════════════════╗
║  FASE 4: APROBACIÓN FINAL → INCUBACIÓN                   ✅ LISTO   ║
╠═════════════════════════════════════════════════════════════════════╣
║  Ruta: POST /api/scenarios/{id}/step2/finalize                      ║
║  Componente: Botón "Finalizar Paso 2" + dialog de confirmación      ║
║                                                                     ║
║  Pre-conditions verificadas por el backend:                         ║
║  • Al menos 1 rol en el escenario                                   ║
║  • Todos los roles tienen arquetipo definido (E/T/O)                ║
║                                                                     ║
║  Efectos al aprobar:                                                ║
║  • Roles con role_change='create' → status = 'in_incubation'        ║
║  • Competencias source='agent' → status = 'in_incubation'           ║
║  • Skills discovered_in_scenario_id → maturity_status = 'incubation'║
║  • Escenario → status = 'incubating'                                ║
╚═════════════════════════════════════════════════════════════════════╝
```

---

## 7. Wireframe del Panel de Revisión (Fase 2)

```
┌─────────────────────────────────────────────────────────────────────────┐
│  🤖 Propuestas del Agente Diseñador de Roles    Alineación: 94%   [X]   │
├─────────────────────────────────────────────────────────────────────────┤
│  ℹ️  El Diseñador de Roles propone cambios basado en el blueprint         │
│                                                                         │
│  ROLES PROPUESTOS  3/5 ●          [Aprobar todos] [Rechazar todos]      │
│  ───────────────────────────────────────────────────────────────────    │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │ 🟢 NUEVO   "AI Talent Engineer"                     [Aprobado ✓] │   │
│  │  Diseña y optimiza sistemas de capacidades humanas               │   │
│  │  Arquetipo: [E] [T▶] [O]    FTE: [1.0]                          │   │
│  │  Mix Humano / IA:  👤 40% / 🤖 60%                               │   │
│  │                                                                  │   │
│  │  Competencias propuestas (3):                                    │   │
│  │  ┌─────────────────┬──────────────┬───────┬──────┬──────────┐  │   │
│  │  │ Competencia     │ Tipo cambio  │ Nivel │ Core │ Semáforo │  │   │
│  │  ├─────────────────┼──────────────┼───────┼──────┼──────────┤  │   │
│  │  │ MLOps Eng.      │ 📈 Enriq.    │[1][2][3][4▶][5]│ ✓  │ 🟢 │  │   │
│  │  │ Python Stack    │ 📈 Enriq.    │[1][2][3▶][4][5]│ ✓  │ 🟢 │  │   │
│  │  │ Liderazgo Téc.  │ ✅ Mant.     │[1][2▶][3][4][5]│    │ 🟢 │  │   │
│  │  └─────────────────┴──────────────┴───────┴──────┴──────────┘  │   │
│  │                              [Rechazar] [Aprobado ✓]             │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  PROPUESTAS DE CATÁLOGO  2 ●          [Aprobar todos] [Rechazar todos]  │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │ 🟢 ADD   "MLOps Engineering"    Necesaria para el blueprint IA   │[✓]│
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  3 roles y 2 competencias seleccionadas        [Cancelar] [Confirmar (5)]│
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 8. Wireframe de la Matriz (Fase 3)

```
  Escenario: Transformación Digital 2026    Horizonte: 12 meses
  [🤖 Consultar Agentes]  [+ Nuevo Rol]    [✅ Finalizar Paso 2]

  Tabs por capability: [Tecnología e IA ●3] [Liderazgo ●1] [Operaciones]

                  │ MLOps Eng. │ Python    │ Liderazgo │ (+ Agregar)
  ────────────────┼────────────┼───────────┼───────────┤
  AI Talent Eng.  │🤖📈Lv4 🟢 │🤖📈Lv3 🟢 │🤖✅Lv2 🟢 │
  [T] 1.0 FTE     │            │           │           │
  ────────────────┼────────────┼───────────┼───────────┤
  Data Analyst    │🤖📈Lv3 🟢  │👤🔄Lv4 🟢 │   [+]     │
  [T] 2.0 FTE     │            │           │           │
  ────────────────┼────────────┼───────────┼───────────┤
  + Nuevo Rol     │            │           │           │

  Leyenda:  🤖 Propuesto por agente   👤 Asignado manualmente
            ✅ 📈 🔄 📉 = change_type   Lv = nivel requerido
            🟢🟡🟠 = semáforo cubo
```

---

## 9. Modelo de datos afectado

### Tabla: `scenario_role_competencies` (migración ejecutada ✅)

| Columna                 | Tipo      | Descripción                                                            |
| ----------------------- | --------- | ---------------------------------------------------------------------- |
| `id`                    | int       | PK                                                                     |
| `scenario_id`           | int       | FK scenario                                                            |
| `role_id`               | int       | FK scenario_roles.id                                                   |
| `competency_id`         | int       | FK competencies.id                                                     |
| `change_type`           | enum      | maintenance / transformation / enrichment / extinction                 |
| `required_level`        | int       | 1 – 5                                                                  |
| `is_core`               | bool      | Si es crítica para el rol                                              |
| `is_referent`           | bool      | Si el rol actúa como referente técnico                                 |
| `rationale`             | text      | Justificación del diseño                                               |
| `competency_version_id` | int\|null | FK competency_versions (para transformaciones)                         |
| **`source`**            | enum      | ✅ **NUEVO (migrado):** `agent` / `manual` / `auto` — default `manual` |

### Tabla: `scenario_roles` (existente, sin cambios)

Campos clave usados en el Paso 2:

| Columna          | Descripción                                                         |
| ---------------- | ------------------------------------------------------------------- |
| `archetype`      | E / T / O — dimensión X del cubo                                    |
| `fte`            | FTE estimado del rol en el escenario                                |
| `human_leverage` | % de carga humana (0-100)                                           |
| `role_change`    | create / modify / eliminate / maintain                              |
| `evolution_type` | new_role / upgrade_skills / transformation / downsize / elimination |

---

## 10. Endpoints — Estado final

### `POST /api/scenarios/{id}/step2/design-talent` ✅ (modificado)

Controlador: `ScenarioController::designTalent()`
Servicio: `TalentDesignOrchestratorService::orchestrate()`

**Contexto enriquecido que ahora recibe el agente:**

- Blueprint Paso 1 (capabilities → competencies → skills)
- Catálogo actual: roles y competencias activos de la organización
- Roles del escenario con arquetipo, FTE y human_leverage
- Mappings ya existentes en la matriz (evita duplicar propuestas)

### `POST /api/scenarios/{id}/step2/agent-proposals/apply` ✅ (nuevo)

Controlador: `ScenarioController::applyAgentProposals()`
Servicio: `TalentDesignOrchestratorService::applyProposals()`

**Request body:**

```json
{
    "approved_role_proposals": [
        {
            "type": "NEW",
            "proposed_name": "AI Talent Engineer",
            "archetype": "T",
            "fte_suggested": 1.0,
            "talent_composition": {
                "human_percentage": 40,
                "synthetic_percentage": 60
            },
            "competency_mappings": [
                {
                    "competency_name": "MLOps Engineering",
                    "competency_id": null,
                    "change_type": "enrichment",
                    "required_level": 4,
                    "is_core": true,
                    "rationale": "Esencial para pipelines de ML"
                }
            ]
        }
    ],
    "approved_catalog_proposals": [
        {
            "type": "ADD",
            "proposed_name": "MLOps Engineering",
            "action_rationale": "Necesaria para el blueprint de IA"
        }
    ]
}
```

**Response:**

```json
{
    "success": true,
    "message": "Propuestas aplicadas correctamente",
    "stats": {
        "roles_created": 1,
        "roles_evolved": 0,
        "mappings_created": 3,
        "competencies_created": 1
    }
}
```

**Lógica del backend (`applyProposals()`):**

1. Para `catalog_proposals` ADD/MODIFY → crea/actualiza `competencies` en `status='in_incubation'`, guarda mapa nombre→id
2. Para `role_proposals` NEW → crea `roles` + `scenario_roles` con archetype/fte/leverage
3. Para `role_proposals` EVOLVE/REPLACE → actualiza `scenario_roles` del rol existente
4. Para cada `competency_mapping` → resuelve competency_id (por id o por nombre), crea `scenario_role_competencies` con `source='agent'`
5. Dispara `RoleSkillDerivationService` para derivar skills

### `POST /api/scenarios/{id}/step2/finalize` ✅ (nuevo)

Controlador: `ScenarioController::finalizeStep2()`
Servicio: `TalentDesignOrchestratorService::finalizeStep2()`

**Pre-conditions (verificadas por el backend):**

- `ScenarioRole::count() > 0` → al menos un rol
- `ScenarioRole::whereNull('archetype')->count() === 0` → todos tienen arquetipo

**Efectos al aprobar:**

- `roles` con `role_change='create'` → `status = 'in_incubation'`
- `competencies` con `source='agent'` → `status = 'in_incubation'`
- `skills` con `discovered_in_scenario_id` → `maturity_status = 'incubation'`
- `scenarios` → `status = 'incubating'`

---

## 11. Archivos implementados

### Backend

| Archivo                                                                                    | Cambio                                                                                                       |
| ------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------------------------------ |
| `database/migrations/2026_02_25_012753_add_source_to_scenario_role_competencies_table.php` | ✅ Migración ejecutada                                                                                       |
| `app/Models/ScenarioRoleCompetency.php`                                                    | ✅ `source` en `$fillable`                                                                                   |
| `app/Services/Talent/TalentDesignOrchestratorService.php`                                  | ✅ Reescrito: `orchestrate()` enriquecido + `applyProposals()` + `finalizeStep2()` + `resolveCompetencyId()` |
| `app/Http/Controllers/Api/ScenarioController.php`                                          | ✅ Endpoints `applyAgentProposals()` + `finalizeStep2()`                                                     |
| `routes/api.php`                                                                           | ✅ Rutas `agent-proposals/apply` + `finalize` registradas                                                    |

### Frontend

| Archivo                                                                   | Cambio                                                            |
| ------------------------------------------------------------------------- | ----------------------------------------------------------------- |
| `resources/js/components/ScenarioPlanning/Step2/AgentProposalsModal.vue`  | ✅ Reescrito como Panel de Revisión full-screen                   |
| `resources/js/stores/roleCompetencyStore.ts`                              | ✅ `source` en tipo + `applyAgentProposals()` + `finalizeStep2()` |
| `resources/js/components/ScenarioPlanning/Step2/RoleCompetencyMatrix.vue` | ✅ Botón "Finalizar Paso 2" + dialog de confirmación + handlers   |

---

## 12. Deuda técnica pendiente

| #   | Tarea                                                                                                                                   | Prioridad                      |
| --- | --------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------ |
| 1   | Mostrar badge de origen (🤖/👤) en celdas de la matriz (Fase 3)                                                                         | Media                          |
| 2   | Reducir complejidad cognitiva de `applyProposals()` (SonarQube → 34/15) extrayendo sub-métodos                                          | Baja                           |
| 3   | Unificar sistema de agentes (Paso 1 también pase por `AiOrchestratorService`)                                                           | Baja — post-estabilidad Paso 2 |
| 4   | Prompt del agente: actualizar `talent_design_orchestration_es.md` para instruir explícitamente el formato `competency_mappings` por rol | Media                          |

---

## 13. Decisiones de diseño tomadas

| Decisión                                        | Alternativa descartada                       | Razón                                                                                                |
| ----------------------------------------------- | -------------------------------------------- | ---------------------------------------------------------------------------------------------------- |
| **Agente va primero, matriz se puebla después** | Matriz manual primero, agente como co-piloto | La matriz empieza vacía en escenarios nuevos; el agente tiene más contexto estratégico en este punto |
| **Panel de revisión full-screen (no modal)**    | Modal existente con scroll                   | Las propuestas pueden ser 10+ roles × 5+ competencias cada uno; un modal no escala                   |
| **Apply en batch al confirmar**                 | Guardar cada aprobación individualmente      | Evita estados intermedios incoherentes en la BD; el usuario puede cambiar de opinión hasta confirmar |
| **Columna `source` en mappings**                | Sin trazabilidad de origen                   | Permite auditar qué decidió el agente vs el humano, y regenerar propuestas sin pisar trabajo manual  |
| **Cubo validado en Fase 2 también**             | Solo en Fase 3 (modal de edición)            | El usuario debe ver incoherencias antes de aprobar, no después                                       |
| **Finalización como gate explícito**            | Avance automático al completar mappings      | Las decisiones de incubación tienen impacto en el catálogo real; requieren intención explícita       |

---

## 14. Guía de prueba manual

### Paso a paso para validar el flujo completo

1. **Abrir un escenario** que tenga el Paso 1 completado (blueprint listo con capabilities)
2. Ir a la sección **Paso 2 → Matriz de Roles y Competencias**
3. Click en **"Consultar Agentes"** → el panel de revisión se abre con estado `loading`
4. Cuando carguen las propuestas:
    - Verificar que aparecen `role_proposals` y `catalog_proposals`
    - Ver el **Alignment Score** en el header
    - Cambiar el arquetipo de un rol y verificar que el semáforo cambia
    - Aprobar algunos, rechazar otros, aprobar todos del catálogo
5. Click en **"Confirmar y aplicar (N)"** → POST a `/agent-proposals/apply`
6. Verificar que la matriz se recarga y muestra los roles con sus competencias
7. Editar manualmente una celda (click → modal de edición)
8. Click en **"Finalizar Paso 2"** → dialog de confirmación
9. Confirmar → POST a `/step2/finalize`
10. Verificar en BD: `scenarios.status = 'incubating'`, `roles.status = 'in_incubation'`

### Casos de error a verificar

| Caso                                     | Comportamiento esperado                           |
| ---------------------------------------- | ------------------------------------------------- |
| Finalizar sin roles                      | Backend retorna 422: "Debe haber al menos un rol" |
| Finalizar con rol sin arquetipo          | Backend retorna 422: "X roles sin arquetipo"      |
| Confirmar propuestas sin aprobar ninguna | Botón deshabilitado en el frontend                |
| Red error en `/apply`                    | Alert con mensaje de error del servidor           |

---

_Documento actualizado el 2026-02-24 — versión 2.0 con implementación completa._
