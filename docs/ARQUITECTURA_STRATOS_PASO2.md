# 📊 Arquitectura Técnica Stratos: Paso 2 - Mapeo Roles ↔ Competencias

**Versión:** 1.0  
**Fecha:** 2026-02-02  
**Audiencia:** Desarrolladores (Backend + Frontend)  
**Status:** Documento Canónico

---

## 1. Introducción a Stratos

### 1.1 Objetivo del Sistema

**Stratos** es una plataforma de planificación dotacional estratégica basada en **escenarios**, **capacidades** y **evolución organizacional**.

A diferencia de sistemas tradicionales que responden a "¿cuántas personas necesitamos?", Stratos responde a preguntas más profundas:

- **¿Qué capacidades son estratégicas en nuestro futuro?**
- **¿Cómo evolucionan nuestros roles para alcanzar esas capacidades?**
- **¿Qué brecha existe hoy vs. lo que necesitaremos?**
- **¿Cómo priorizamos el desarrollo de talento para cerrar esas brechas?**

**Audiencia:**

- 🎯 RRHH Estratégico: Diseñar escenarios y capacidades
- 🎯 Líderes de Negocio: Entender impacto de transformación en roles
- 🎯 Gestores de Talento: Ejecutar planes de desarrollo

### 1.2 Diferenciadores Clave

| Aspecto          | Enfoque Tradicional | Stratos                                                        |
| ---------------- | ------------------- | -------------------------------------------------------------- |
| **Ciclo**        | Anual, estático     | Escenarios dinámicos rolling                                   |
| **Abstracción**  | Puestos de trabajo  | Capabilities → Competencies → Skills                           |
| **Evolución**    | Implícita o ausente | Explícita: maintenance, transformation, enrichment, extinction |
| **Análisis**     | Manual, ad-hoc      | Automático: IQ, Readiness, Confidence Score                    |
| **Trazabilidad** | Nula                | Completa: `discovered_in_scenario_id`, `change_type`, `source` |

### 1.3 Niveles de Gestión

Stratos opera en **3 niveles jerárquicos**:

```
┌──────────────────────────────────────────────────────────────┐
│ NIVEL ESTRATÉGICO                                            │
│ "¿Qué capacidades nos hacen ganadores en 2026?"             │
│ → Scenarios + Capabilities + Strategic Roles                │
│ → Responsables: Ejecutivos, RRHH Estratégico               │
└──────────────────────────────────────────────────────────────┘
                            ↓
┌──────────────────────────────────────────────────────────────┐
│ NIVEL TÁCTICO (← PASO 2 OCURRE AQUÍ)                         │
│ "¿Qué competencias necesitan nuestros roles para alcanzar   │
│  esas capacidades?"                                          │
│ → Scenario Roles + Scenario Role Competencies               │
│ → Responsables: Líderes de negocio, RRHH Táctico            │
└──────────────────────────────────────────────────────────────┘
                            ↓
┌──────────────────────────────────────────────────────────────┐
│ NIVEL OPERACIONAL                                            │
│ "¿Qué skills específicas requiere cada rol? ¿Qué personas   │
│  las tienen hoy?"                                            │
│ → Scenario Role Skills + Person Role Skills                 │
│ → Responsables: Managers, Especialistas de Talento          │
└──────────────────────────────────────────────────────────────┘
```

---

## 2. Arquitectura Lógica

### 2.1 Modelo Conceptual Vertical: Capabilities → Competencies → Skills

La **arquitectura jerárquica** de abstracción:

```
CAPABILITY (Pilar Estratégico)
├─ Definición: Área amplia de capacidad organizacional
├─ Ejemplo: "Digital Transformation"
├─ Nivel: Ejecutivo/Estratégico
└─ Descubrimiento: Puede nacer en un escenario (discovered_in_scenario_id)
    │
    ├─ COMPETENCY (Bloque Táctico)
    │  ├─ Definición: Agrupación coherente de skills
    │  ├─ Ejemplo: "Cloud Architecture" (dentro de Digital)
    │  ├─ Nivel: Líder de negocio / Gestor de Talento
    │  │
    │  └─ SKILL (Unidad Operacional)
    │     ├─ Definición: Habilidad específica, evaluable 1-5
    │     ├─ Ejemplos: "AWS EC2", "Kubernetes", "Security"
    │     ├─ Nivel: Individual, Persona
    │     └─ Medible: current_level vs required_level
    │
    └─ relationship: competency_skills (con weight)
       └─ Ejemplo: "Cloud Architecture" = 30% AWS + 40% Kubernetes + 30% Security
```

**Por qué esta estructura:**

- ✅ **Escalabilidad:** Nuevas skills se agregan sin rediseñar competencies
- ✅ **Reutilización:** Una competency sirve múltiples roles y escenarios
- ✅ **Granularidad:** Cada nivel sirve un propósito diferente
- ✅ **Mantenibilidad:** Cambios en competency_skills propagan automáticamente

### 2.2 Modelo Conceptual Horizontal: Escenario → Roles → Competencies → Skills → Personas

El **flujo de datos desde la estrategia hacia la acción**:

```
SCENARIO (Futuro Hipotético)
│
├─ ¿Qué capabilities son críticas?
│  └─ scenario_capabilities
│     ├─ capability_id
│     ├─ strategic_weight (importancia: 0.0-1.0)
│     └─ priority
│
├─ ¿Qué roles existirán?
│  └─ scenario_roles
│     ├─ role_id (existente o nuevo)
│     ├─ evolution_type (new_role, transformation, downsize, etc.)
│     └─ impact_level
│
├─ ¿Qué competencias requiere cada rol?
│  └─ scenario_role_competencies ← PASO 2 OCURRE AQUÍ
│     ├─ competency_id
│     ├─ required_level (1-5)
│     ├─ change_type (maintenance, transformation, enrichment, extinction)
│     └─ rationale
│
├─ ¿Qué skills se derivan? (Automático)
│  └─ scenario_role_skills ← Generado por RoleSkillDerivationService
│     ├─ skill_id
│     ├─ required_level (heredado de competency)
│     ├─ change_type (heredado de competency)
│     └─ source ('competency' o 'manual')
│
└─ ¿Qué personas tenemos? (Diagnóstico)
   └─ person_role_skills
      ├─ current_level (evaluación actual)
      ├─ evidence_source (self_assessment, manager_review, certification, test)
      └─ → GAP = required_level - current_level
```

### 2.3 Ciclo de Vida: Pasos Conceptuales

```
┌─────────────────────────────────────────────────────────┐
│ PASO 1 (Fase Estratégica): Diseñar Escenario           │
│ ├─ Crear scenarios row                                 │
│ ├─ Definir scenario_capabilities (qué es crítico)     │
│ ├─ Definir competencies si no existen                 │
│ └─ OUTPUT: scenario_capabilities poblada              │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ PASO 2 (Fase Táctica): Mapear Roles ↔ Competencies   │
│ ├─ Crear scenario_roles (roles en escenario)          │
│ ├─ Asignar competencies a roles                       │
│ ├─ Definir change_type y required_level               │
│ └─ OUTPUT: scenario_role_competencies poblada         │
│ ← Aquí ocurre la UI/matriz interactiva                │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ PASO 3 (Fase Operacional): Derivar & Analizar         │
│ ├─ RoleSkillDerivationService genera scenario_role_   │
│   skills a partir de competencies × skills            │
│ ├─ ScenarioAnalyticsService calcula:                 │
│    ├─ Scenario IQ (0-100)                            │
│    ├─ Readiness por competency, capability, rol     │
│    ├─ Gaps específicos (skill, competency, role)    │
│    └─ Confidence Score (calidad de datos)           │
│ └─ OUTPUT: Recomendaciones y priorización             │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ PASO 4+ (Ejecución): Planes de Desarrollo             │
│ ├─ Crear learning paths para transformation/enrichment│
│ ├─ Asignar mentores, coaches, cursos                 │
│ ├─ Monitorear progreso vs. required_level            │
│ └─ Ajustar escenarios con nuevos datos               │
└─────────────────────────────────────────────────────────┘
```

---

## 3. Modelo de Datos

### 3.1 Tabla: `capabilities`

**Propósito:** Definir pilares estratégicos de capacidad organizacional

**Campos Clave:**

| Campo                       | Tipo        | Descripción                                       |
| --------------------------- | ----------- | ------------------------------------------------- |
| `id`                        | PK          | Identificador único                               |
| `organization_id`           | FK          | Multi-tenancy                                     |
| `name`                      | string(255) | Ej: "Digital Transformation"                      |
| `description`               | text        | Propósito, contexto                               |
| `category`                  | enum        | `technical`, `behavioral`, `digital`, `strategic` |
| `status`                    | enum        | `draft`, `active`, `deprecated`                   |
| `discovered_in_scenario_id` | FK nullable | Qué escenario reveló esta capability (incubación) |

**Relaciones:**

- ← `competencies` (1:N) - Competencias que la componen
- ← `scenario_capabilities` (1:N) - Asignación en escenarios

**Ejemplo:**

```sql
INSERT INTO capabilities (name, category, status, discovered_in_scenario_id) VALUES
('Digital Transformation', 'strategic', 'active', NULL),
('Generative AI & LLMs', 'technical', 'active', 5), -- Descubierta en escenario 5
('User-Centered Design', 'behavioral', 'active', NULL);
```

**Notas Especiales:**

- 🔍 **`discovered_in_scenario_id`:** Permite "incubación" de capacidades. Una capability nueva puede nacer en un escenario específico. Si funciona, se "promueve" a nivel global (set a NULL o crear flag `is_global`).
- 📊 **Reutilización:** Una capability es reutilizable entre escenarios. Si "Digital Transformation" es crítica en 2026, también lo será en 2027 (a menos que status = deprecated).

---

### 3.2 Tabla: `competencies`

**Propósito:** Agrupaciones coherentes de skills que conforman una capability

**Campos Clave:**

| Campo             | Tipo        | Descripción                |
| ----------------- | ----------- | -------------------------- |
| `id`              | PK          | Identificador único        |
| `organization_id` | FK          | Multi-tenancy              |
| `capability_id`   | FK          | Qué capability la contiene |
| `name`            | string(255) | Ej: "Cloud Architecture"   |
| `description`     | text        | Qué es, cómo se mide       |

**Relaciones:**

- → `capabilities` (N:1) - La capability que la contiene
- → `competency_skills` (1:N) - Skills que la componen
- ← `scenario_role_competencies` (1:N) - Asignación en roles/escenarios

**Ejemplo:**

```sql
INSERT INTO competencies (capability_id, name) VALUES
(1, 'Cloud Architecture'),      -- Dentro de Digital Transformation
(1, 'Data Analytics'),
(2, 'Prompt Engineering'),       -- Dentro de Generative AI
(2, 'LLM Fine-tuning');
```

**Notas Especiales:**

- 🎯 **Puente:** Competencies conectan el mundo estratégico (capabilities) con el operacional (skills y personas).
- 📦 **Catálogo:** El catálogo de competencies es **global**, reutilizable en múltiples escenarios y roles.
- 🔗 **N:1 a Capability:** Pero una competency puede contribuir a múltiples capabilities (si normalizamos, sería una tabla N:N - considerar en futuro).

---

### 3.3 Tabla: `competency_skills`

**Propósito:** Relación N:N entre competencies y skills, **con peso**

**Campos Clave:**

| Campo           | Tipo         | Descripción                          |
| --------------- | ------------ | ------------------------------------ |
| `id`            | PK           | Identificador único                  |
| `competency_id` | FK           | Competency                           |
| `skill_id`      | FK           | Skill                                |
| `weight`        | decimal(3,2) | Importancia: 0.0–1.0 (ej: 0.4 = 40%) |

**Relaciones:**

- → `competency` (N:1)
- → `skill` (N:1)

**Ejemplo:**

```sql
INSERT INTO competency_skills (competency_id, skill_id, weight) VALUES
(1, 101, 0.3),  -- Cloud Architecture: 30% AWS EC2
(1, 102, 0.4),  --                   40% Kubernetes
(1, 103, 0.3);  --                   30% Security
```

**Notas Especiales:**

- ⚖️ **Weight:** Importancia relativa de cada skill en la competency. Usado en `ScenarioAnalyticsService` para calcular **Competency Readiness** como promedio ponderado.
- ✅ **Suma:** Los weights de una competency no necesitan sumar exactamente 1.0 (se normaliza automáticamente en el cálculo).
- 🔄 **Propagación:** Si cambias `competency_skills.weight`, afecta directamente los cálculos de Readiness futuros (impacto en `ScenarioAnalyticsService`).

---

### 3.4 Tabla: `scenarios`

**Propósito:** Escenarios futuros hipotéticos (snapshots estratégicos)

**Campos Clave:**

| Campo             | Tipo        | Descripción                                         |
| ----------------- | ----------- | --------------------------------------------------- |
| `id`              | PK          | Identificador único                                 |
| `organization_id` | FK          | Multi-tenancy                                       |
| `name`            | string(255) | Ej: "Adopción IA Generativa 2026"                   |
| `description`     | text        | Propósito, supuestos estratégicos                   |
| `horizon_months`  | integer     | 12, 18, 24, etc.                                    |
| `status`          | enum        | `draft`, `in_review`, `approved`, `active`          |
| `assumptions`     | json        | Supuestos clave (crecimiento, automatización, etc.) |

**Relaciones:**

- ← `scenario_capabilities` (1:N)
- ← `scenario_roles` (1:N)
- ← `scenario_role_competencies` (1:N)
- ← `scenario_role_skills` (1:N)

**Ejemplo:**

```sql
INSERT INTO scenarios (name, horizon_months, status, assumptions) VALUES
('Adopción IA 2026', 18, 'approved', '{"growth": "15%", "automation": "20%"}');
```

**Notas Especiales:**

- 📸 **Snapshot:** Un escenario es una fotografía del futuro en un momento específico. No es "mutable" una vez aprobado (versionado implícito).
- 🔀 **Múltiples:** Una organización puede tener 3+ escenarios simultáneamente (base, conservative, aggressive) para comparar riesgos.
- 🗓️ **Horizon:** Importante para análisis de phasing (qué cambios primero, en qué orden).

---

### 3.5 Tabla: `scenario_capabilities`

**Propósito:** Definir qué capabilities son críticas **en este escenario específico**

**Campos Clave:**

| Campo              | Tipo         | Descripción                                                  |
| ------------------ | ------------ | ------------------------------------------------------------ |
| `scenario_id`      | FK           | Escenario                                                    |
| `capability_id`    | FK           | Capability                                                   |
| `strategic_weight` | decimal(3,2) | Peso: 0.0–1.0 (ej: 0.4 = 40% de prioridad)                   |
| `priority`         | integer      | Ranking: 1 (más crítica), N                                  |
| `strategic_role`   | enum         | `critical_business`, `critical_transformation`, `supporting` |
| `rationale`        | text         | Por qué esta capability es crítica aquí                      |

**Relaciones:**

- → `scenario` (N:1)
- → `capability` (N:1)

**Ejemplo:**

```sql
INSERT INTO scenario_capabilities VALUES
(5, 1, 0.4, 1, 'critical_transformation', 'Digital es eje central de 2026'),
(5, 2, 0.4, 2, 'critical_transformation', 'IA es diferenciador competitivo'),
(5, 3, 0.2, 3, 'supporting', 'UX debe ser excelente pero no es bloqueante');
```

**Notas Especiales:**

- 🎯 **Strategic Weight:** Importancia de la capability **en este escenario**. Usada en `ScenarioAnalyticsService` para calcular el **Scenario IQ final**.
- 🔑 **Composite Key:** (`scenario_id`, `capability_id`) es única. Una capability aparece 0 o 1 vez en cada escenario.
- 📊 **Priorización:** Permite responder "¿En qué orden priorizamos el desarrollo?" → ordenar por priority.

---

### 3.6 Tabla: `scenario_roles`

**Propósito:** Roles que existirán **en este escenario** (nueva configuración organizacional)

**Campos Clave:**

| Campo            | Tipo | Descripción                                                               |
| ---------------- | ---- | ------------------------------------------------------------------------- |
| `id`             | PK   | Identificador único                                                       |
| `scenario_id`    | FK   | Escenario                                                                 |
| `role_id`        | FK   | Rol (existente en roles table)                                            |
| `role_change`    | enum | `create` (nuevo), `modify`, `eliminate`, `maintain`                       |
| `impact_level`   | enum | `critical`, `high`, `medium`, `low`                                       |
| `evolution_type` | enum | `new_role`, `upgrade_skills`, `transformation`, `downsize`, `elimination` |
| `rationale`      | text | Por qué este rol existe/cambia en el escenario                            |

**Relaciones:**

- → `scenario` (N:1)
- → `role` (N:1)
- ← `scenario_role_competencies` (1:N)

**Ejemplo:**

```sql
INSERT INTO scenario_roles VALUES
(NULL, 5, 10, 'modify', 'high', 'upgrade_skills',
 'Senior Developer necesita Cloud + IA skills'),
(NULL, 5, 50, 'create', 'critical', 'new_role',
 'AI Engineer rol completamente nuevo'),
(NULL, 5, 20, 'modify', 'medium', 'transformation',
 'Ops Manager evoluciona a Platform Engineer');
```

**Notas Especiales:**

- 🎭 **Evolution Type:** Narrativa clara del cambio. Combinado con `scenario_role_competencies.change_type`, proporciona contexto completo.
- 🔗 **FK a roles:** Apunta a tabla `roles` (catálogo global). Un rol puede existir en múltiples escenarios.
- 🚀 **Impact Level:** Crítico para priorizar implementación y gestión del cambio.

---

### 3.7 Tabla: `role_competencies` (Referencia Estática Global)

**Propósito:** Competencias que requiere un rol **en general**, sin escenario

**Campos Clave:**

| Campo            | Tipo    | Descripción              |
| ---------------- | ------- | ------------------------ |
| `id`             | PK      | Identificador único      |
| `role_id`        | FK      | Rol                      |
| `competency_id`  | FK      | Competency               |
| `required_level` | integer | 1–5                      |
| `is_core`        | boolean | ¿Es central para el rol? |
| `rationale`      | text    | Por qué se requiere      |

**Relaciones:**

- → `role` (N:1)
- → `competency` (N:1)

**Ejemplo:**

```sql
INSERT INTO role_competencies VALUES
(NULL, 10, 1, 4, true, 'Senior Dev siempre necesita Cloud avanzado'),
(NULL, 10, 5, 3, false, 'Leadership es valorado pero no crítico');
```

**Nota:** `role_competencies` define el **rol "por defecto"**. En ausencia de escenarios, es la referencia.

---

### 3.8 Tabla: `scenario_role_competencies` ⭐ (PASO 2)

**Propósito:** Competencias requeridas por un rol **específicamente en este escenario**

**⭐ CRÍTICA PARA PASO 2:** Esta tabla es **donde ocurre la matriz interactiva** del Paso 2.

**Campos Clave:**

| Campo             | Tipo     | Descripción                                                                 |
| ----------------- | -------- | --------------------------------------------------------------------------- |
| `id`              | PK       | Identificador único                                                         |
| `scenario_id`     | FK       | Escenario                                                                   |
| `role_id`         | FK       | Rol en escenario                                                            |
| `competency_id`   | FK       | Competency requerida                                                        |
| `required_level`  | integer  | 1–5 (nivel futuro requerido)                                                |
| `is_core`         | boolean  | ¿Crítica para este rol en este escenario?                                   |
| **`change_type`** | **enum** | **`maintenance`**, **`transformation`**, **`enrichment`**, **`extinction`** |
| `rationale`       | text     | Justificación de la asociación                                              |

**Relaciones:**

- → `scenario`, `role`, `competency`
- ← `scenario_role_skills` (1:N) - Las skills se derivan automáticamente

**Estados de `change_type`:**

| Estado                | Significado         | Ejemplo                                          | Acción                           |
| --------------------- | ------------------- | ------------------------------------------------ | -------------------------------- |
| **Maintenance** ✅    | Se mantiene igual   | Cloud skills en Developer (sigue siendo crítica) | No requiere training             |
| **Transformation** 🔄 | Requiere upskilling | Developer: AWS 3→4 (más profundo)                | Cursos avanzados, proyecto-based |
| **Enrichment** 📈     | Nueva competencia   | Developer: IA (completamente nueva)              | Bootcamp, mentoría               |
| **Extinction** 📉     | Desaparecerá        | Developer: Flash (obsoleto en 2026)              | Transición planificada           |

**Ejemplo de Matriz (Paso 2):**

```
┌─────────────────────┬──────────────────┬─────────────────┐
│ Rol                 │ Cloud Arch       │ IA Engineering  │
├─────────────────────┼──────────────────┼─────────────────┤
│ Senior Developer    │ ✅ Maintenance   │ 📈 Enrichment   │
│                     │ Level: 4         │ Level: 3        │
├─────────────────────┼──────────────────┼─────────────────┤
│ Ops Manager         │ 📉 Extinction    │ ✅ Maintenance  │
│                     │ Timeline: 12mo   │ Level: 2        │
├─────────────────────┼──────────────────┼─────────────────┤
│ AI Engineer (Nuevo) │ 📈 Enrichment    │ 🔄 Transform    │
│                     │ Level: 4         │ 2→4 (18 meses)  │
└─────────────────────┴──────────────────┴─────────────────┘
```

**Notas Especiales:**

- 🔑 **Composite Key:** (`scenario_id`, `role_id`, `competency_id`) es única.
- 🎯 **Change Type:** Es el campo crítico que diferencia Paso 2 del Paso 1. Define la estrategia de transición.
- 🔄 **Derivación:** Cuando guardas una fila aquí, `RoleSkillDerivationService` automáticamente genera `scenario_role_skills`.

---

### 3.9 Tabla: `role_skills` (Referencia Estática Global)

**Propósito:** Skills necesarias en un rol **en general** (sin escenario)

**Campos Clave:**

| Campo            | Tipo        | Descripción                                                                     |
| ---------------- | ----------- | ------------------------------------------------------------------------------- |
| `id`             | PK          | Identificador único                                                             |
| `role_id`        | FK          | Rol                                                                             |
| `skill_id`       | FK          | Skill                                                                           |
| `required_level` | integer     | 1–5                                                                             |
| `is_critical`    | boolean     | ¿Essential para el rol?                                                         |
| **`source`**     | **enum**    | **`competency`** (derivada automáticamente) **`manual`** (agregada manualmente) |
| `competency_id`  | FK nullable | Qué competency la genera (si source='competency')                               |

**Relaciones:**

- → `role`, `skill`
- ← competency (via `competency_id`)

**Ejemplo:**

```sql
INSERT INTO role_skills VALUES
-- Skills derivadas de competencies
(NULL, 10, 101, 4, true, 'competency', 1),  -- AWS EC2 (de Cloud Arch)
(NULL, 10, 102, 4, true, 'competency', 1),  -- Kubernetes
-- Skills manuales (excepciones)
(NULL, 10, 200, 3, false, 'manual', NULL);  -- Agile Methodology (especialización)
```

**Diferencia vs `scenario_role_skills`:**

| `role_skills`                        | `scenario_role_skills`                                  |
| ------------------------------------ | ------------------------------------------------------- |
| **Referencia global**                | **Derivada por escenario**                              |
| Define lo que "típicamente" necesita | Define lo que necesita **en este escenario específico** |
| Usado para catálogo + templates      | Usado para análisis, IQ, gaps                           |
| No tiene `change_type`               | Tiene `change_type`                                     |
| Source: `competency` o `manual`      | Source: `competency` o `manual`                         |

---

### 3.10 Tabla: `scenario_role_skills` ⭐ (GENERADA AUTOMÁTICAMENTE)

**Propósito:** Skills requeridas por un rol **en este escenario** (generada por `RoleSkillDerivationService`)

**Campos Clave:**

| Campo             | Tipo        | Descripción                                                                 |
| ----------------- | ----------- | --------------------------------------------------------------------------- |
| `id`              | PK          | Identificador único                                                         |
| `scenario_id`     | FK          | Escenario                                                                   |
| `role_id`         | FK          | Rol                                                                         |
| `skill_id`        | FK          | Skill                                                                       |
| `required_level`  | integer     | 1–5 (heredado de competency)                                                |
| `is_critical`     | boolean     | Criticidad                                                                  |
| **`change_type`** | **enum**    | **`maintenance`**, **`transformation`**, **`enrichment`**, **`extinction`** |
| **`source`**      | **enum**    | **`competency`** (auto-derivada) **`manual`** (respetada)                   |
| `competency_id`   | FK nullable | Qué competency la generó                                                    |
| `rationale`       | text        | Trazabilidad                                                                |

**Relaciones:**

- → `scenario`, `role`, `skill`
- ← `person_role_skills` (para comparar actual vs requerida)

**Generación (RoleSkillDerivationService):**

```
INPUT: scenario_role_competencies (fila agregada en Paso 2)
├─ scenario_id = 5
├─ role_id = 50 (AI Engineer)
├─ competency_id = 10 (Prompt Engineering)
├─ required_level = 4
└─ change_type = 'enrichment'

LÓGICA:
├─ Buscar competency_skills donde competency_id = 10
├─ Encontrar: [Skill 301 (40%), Skill 302 (35%), Skill 303 (25%)]
│
└─ Para cada skill:
   ├─ Crear scenario_role_skills
   ├─ required_level = 4 (del competency)
   ├─ change_type = 'enrichment' (heredado)
   └─ source = 'competency'

OUTPUT: 3 filas en scenario_role_skills
├─ Skill 301 (Prompt Design), level 4, enrichment
├─ Skill 302 (GPT API), level 4, enrichment
└─ Skill 303 (Evaluation), level 4, enrichment
```

**Manejo de Excepciones (Manual Skills):**

```
Si usuario agregó manualmente:
INSERT scenario_role_skills (scenario_id, role_id, skill_id, source='manual')

Entonces RoleSkillDerivationService:
├─ Busca skills existentes con source='manual'
├─ NO las sobrescribe
└─ Respeta especialización
```

**Notas Especiales:**

- 🤖 **Automática:** La mayoría de filas se crean por `RoleSkillDerivationService`, no manualmente.
- 📍 **Source:** Permite auditoría: ¿vino de competency o fue agregada manualmente?
- 🔄 **Propagación:** Si cambias `competency_skills.weight`, los readiness scores de futuros análisis se recalculan (service-layer, no database trigger).

---

### 3.11 Tabla: `people`

**Propósito:** Registro de personas en la organización

**Campos Clave:**

| Campo                     | Tipo          | Descripción                                  |
| ------------------------- | ------------- | -------------------------------------------- |
| `id`                      | PK            | Identificador único                          |
| `organization_id`         | FK            | Multi-tenancy                                |
| `first_name`, `last_name` | string        | Nombre                                       |
| `email`                   | string unique | Email                                        |
| `current_role_id`         | FK            | Rol actual                                   |
| `level`                   | enum          | `junior`, `mid`, `senior`, `lead`, `manager` |
| `hire_date`               | date          | Antigüedad                                   |
| `status`                  | enum          | `active`, `on_leave`, `terminated`           |

**Relaciones:**

- ← `person_role_skills` (1:N) - Perfil de skills

**Ejemplo:**

```sql
INSERT INTO people VALUES
(1, 1, 'Juan', 'Pérez', 'juan@corp.com', 10, 'senior', '2015-03-15', 'active');
```

---

### 3.12 Tabla: `person_role_skills`

**Propósito:** Perfil de skills actual de una persona **para un rol específico**

**Campos Clave:**

| Campo                 | Tipo        | Descripción                                                                                 |
| --------------------- | ----------- | ------------------------------------------------------------------------------------------- |
| `id`                  | PK          | Identificador único                                                                         |
| `person_id`           | FK          | Persona                                                                                     |
| `role_id`             | FK          | Rol en el que está siendo evaluada                                                          |
| `skill_id`            | FK          | Skill                                                                                       |
| **`current_level`**   | **integer** | **1–5 (evaluación actual)**                                                                 |
| `verified`            | boolean     | ¿Ha sido verificada?                                                                        |
| **`evidence_source`** | **enum**    | **`self_assessment`**, **`manager_review`**, **`certification`**, **`test`**, **`project`** |
| `evidence_date`       | date        | Cuándo se evaluó                                                                            |
| `notes`               | text        | Observaciones                                                                               |

**Relaciones:**

- → `person`, `role`, `skill`
- ← Comparada contra `scenario_role_skills.required_level` para calcular gaps

**Ejemplo:**

```sql
INSERT INTO person_role_skills VALUES
(NULL, 1, 10, 101, 3, true, 'certification', '2025-10-15', 'AWS Solutions Architect'),
(NULL, 1, 10, 102, 2, true, 'project', '2025-12-01', 'Kubernetes proyecto piloto'),
(NULL, 1, 10, 302, NULL, false, 'self_assessment', NULL, 'Nunca ha usado GPT API');
```

**Gap Calculation:**

```
Para Juan en rol 10 (AI Engineer), escenario 5:
├─ Skill 101 (AWS): required=4, current=3 → Gap=1
├─ Skill 102 (Kubernetes): required=4, current=2 → Gap=2
├─ Skill 302 (GPT API): required=4, current=NULL → Gap=4 (asume 0 o no evaluada)
└─ Total Readiness = min(3/4, 2/4, 0/4) = 0 (crítico)
```

**Notas Especiales:**

- 📊 **Evidence Source:** Calidad de la evaluación. `test` y `certification` son más confiables que `self_assessment`.
- 🔄 **Current Level:** Punto de partida para comparar vs `scenario_role_skills.required_level`.
- 🚨 **NULL:** Si una persona nunca ha sido evaluada en una skill, se asume nivel 0 para cálculos de gap.

---

## 4. Flujos de Negocio Principales

### 4.1 Diseño de Escenario (Fase 1-2)

#### 4.1.1 Fase 1: Definir Capabilities Estratégicas

**Quién:** Ejecutivos, RRHH Estratégico  
**Inputs:**

- Estrategia de negocio (visión 2026-2027)
- Análisis de drivers: transformación digital, crecimiento, automatización
- Catálogo de capabilities existentes (o crear nuevas)

**Proceso:**

```
1. Crear scenarios row
   ├─ Nombre: "Adopción IA Generativa 2026"
   ├─ Horizon: 18 meses
   └─ Assumptions: {"growth": "15%", "automation": "25%"}

2. Identificar capabilities estratégicas
   ├─ "Digital Transformation" → strategic_weight=0.4
   ├─ "Generative AI & LLMs" → strategic_weight=0.3
   └─ "User Research & Design" → strategic_weight=0.3

3. Crear scenario_capabilities rows
   ├─ Asignar strategic_weight (importancia 0.0-1.0)
   ├─ Documentar priority (1, 2, 3...)
   └─ Rationale: "Por qué esta capability es crítica"

4. Crear competencies si no existen
   ├─ "Cloud Architecture" (parte de Digital)
   ├─ "Prompt Engineering" (parte de IA)
   └─ Crear competency_skills × skill associations
```

**Output:**

- `scenarios` con status='draft'
- `scenario_capabilities` populada
- `competencies` + `competency_skills` actualizadas/creadas

**Validaciones:**

- ✅ Strategic weights suman ~1.0 (recomendado pero no obligatorio)
- ✅ Toda capability tiene >= 1 competency
- ✅ Toda competency tiene >= 1 skill

---

#### 4.1.2 Fase 2: Mapear Roles ↔ Competencies (PASO 2)

**Quién:** Líderes de Negocio, RRHH Táctico  
**Inputs:**

- scenario (status='draft', de Fase 1)
- `scenario_capabilities` (definidas en Fase 1)
- Catálogo de roles + competencies

**Proceso (UI: Matriz Interactiva):**

```
PANTALLA: RoleCompetencyMatrix.vue

1. Selector de Contexto
   ├─ Escenario: [Adopción IA 2026] ✓
   ├─ Horizonte: 18 meses
   └─ [+ Crear nuevo rol]

2. Matriz Rol × Competency
   ┌──────────────────┬─────────────────┬──────────────────┐
   │ Rol              │ Cloud Arch      │ Prompt Engineer  │
   ├──────────────────┼─────────────────┼──────────────────┤
   │ Senior Dev       │ ✅ Mantención   │ 📈 Enriquecimiento│
   │ (5 FTE)          │ Level 4         │ Level 3          │
   ├──────────────────┼─────────────────┼──────────────────┤
   │ AI Engineer      │ 📈 Enriquec.    │ 🔄 Transformación│
   │ (Nuevo)          │ Level 4         │ 2→4 (18 meses)   │
   ├──────────────────┼─────────────────┼──────────────────┤
   │ Ops Manager      │ 📉 Extinción    │ ✅ Mantención    │
   │ (2 FTE)          │ Timeline: 12mo   │ Level 2          │
   └──────────────────┴─────────────────┴──────────────────┘

3. Clic en Celda → Modal de Edición
   ├─ Competencia: Cloud Architecture
   ├─ Rol: Senior Developer
   │
   ├─ Estado actual:
   │  ○ ✅ Mantención (sin cambios)
   │  ○ 🔄 Transformación (upskilling requerido)
   │  ○ 📈 Enriquecimiento (nueva/mejorada)
   │  ○ 📉 Extinción (desaparecerá)
   │
   ├─ Si seleccionas TRANSFORMACIÓN:
   │  ├─ Nivel actual: 3 (Intermedio) [auto-llenado]
   │  ├─ Nivel futuro: [Selector 1-5] = 4
   │  ├─ Timeline: 12 meses
   │  └─ ¿Generar Learning Path? [Sí / No]
   │
   └─ [Guardar] [Cancelar]

4. Guardar Matriz Completa
   ├─ INSERT/UPDATE scenario_role_competencies
   ├─ Crear scenario_roles si son nuevos
   ├─ RoleSkillDerivationService se gatilla automáticamente
   └─ Actualizar scenario_role_skills
```

**Output:**

- `scenario_roles` populada (roles en escenario)
- `scenario_role_competencies` completa (la matriz)
- `scenario_role_skills` auto-generada (siguiente paso automático)
- `scenarios` con status='in_review' (listo para validación)

**Validaciones:**

- ✅ Todo rol tiene >= 1 competency
- ✅ Todo competency asignado tiene change_type válido
- ✅ Si transformation: nivel futuro > nivel actual
- ✅ Si extinction: timeline especificado
- ✅ Rationale no vacío (por qué este cambio)

---

### 4.2 Derivación Automática de Skills (RoleSkillDerivationService)

**Cuándo se ejecuta:** Después de Paso 2 (guardar scenario_role_competencies)

**Objetivo:** Generar automáticamente `scenario_role_skills` a partir de la matriz de competencies.

#### 4.2.1 Algoritmo de Derivación

```php
// Pseudocódigo
foreach scenario_role_competency as $src_comp {
    // Para cada competency asignada a un rol en un escenario

    $competency_skills = competency_skills
        ->where('competency_id', $src_comp->competency_id)
        ->get();

    foreach ($competency_skills as $cs) {
        // Para cada skill en la competency

        $skill_gap = scenario_role_skills::firstOrCreate(
            [
                'scenario_id' => $src_comp->scenario_id,
                'role_id' => $src_comp->role_id,
                'skill_id' => $cs->skill_id,
                'source' => 'competency'  // Marcado como auto-derivado
            ]
        );

        // Copiar datos del competency
        $skill_gap->update([
            'required_level' => $src_comp->required_level,  // Heredado
            'change_type' => $src_comp->change_type,         // Heredado
            'competency_id' => $src_comp->competency_id,
            'is_critical' => $src_comp->is_core,
            'rationale' => "Derived from {$competency->name}"
        ]);
    }
}

// RESPETO excepciones (skills manuales)
foreach scenario_role_skills as $manual_skill {
    if ($manual_skill->source === 'manual') {
        // NO tocar - usuario la agregó explícitamente
        continue;
    }
}
```

#### 4.2.2 Ejemplo Concreto

**Input:** Matriz Paso 2 - AI Engineer requiere "Prompt Engineering"

```
scenario_role_competencies:
├─ scenario_id: 5
├─ role_id: 50 (AI Engineer)
├─ competency_id: 10 (Prompt Engineering)
├─ required_level: 4
└─ change_type: 'enrichment'
```

**RoleSkillDerivationService busca:**

```sql
SELECT * FROM competency_skills WHERE competency_id = 10;

Resultado:
├─ Skill 301: "Prompt Design Patterns" (weight: 0.4)
├─ Skill 302: "GPT API Usage" (weight: 0.35)
└─ Skill 303: "Evaluation Frameworks" (weight: 0.25)
```

**Output: Genera 3 filas en scenario_role_skills:**

```sql
INSERT INTO scenario_role_skills VALUES
(NULL, 5, 50, 301, 4, true, 'enrichment', 'competency', 10, 'Derived from Prompt Engineering'),
(NULL, 5, 50, 302, 4, true, 'enrichment', 'competency', 10, 'Derived from Prompt Engineering'),
(NULL, 5, 50, 303, 4, true, 'enrichment', 'competency', 10, 'Derived from Prompt Engineering');
```

#### 4.2.3 Manejo de Excepciones

**Caso: Usuario agregó skill manual**

```sql
-- Usuario agregó manualmente:
INSERT INTO scenario_role_skills
(scenario_id, role_id, skill_id, required_level, source) VALUES
(5, 50, 400, 3, 'manual');  -- "Leadership for Tech Teams" (especialización)
```

**RoleSkillDerivationService:**

```php
// Busca skills que ya existen con source='manual'
$manual_skills = scenario_role_skills
    ->where('scenario_id', $scenario_id)
    ->where('role_id', $role_id)
    ->where('source', 'manual')
    ->get();

// NO las sobrescribe - las preserva
foreach ($manual_skills as $ms) {
    $preserve[] = $ms->skill_id;
}

// Al derivar, salta skills que están en $preserve
```

**Resultado:** AI Engineer tiene:

- 3 skills derivadas (Prompt Engineering competency)
- 1 skill manual (Leadership)
- **Total: 4 skills en escenario**

---

### 4.3 Cálculo del Scenario IQ (ScenarioAnalyticsService)

**Objetivo:** Calcular un indicador de 0-100 que mida "¿Qué tan lista está la organización para este escenario?"

#### 4.3.1 Fórmula Paso a Paso

**Nivel 1: Skill Readiness**

Para cada combinación (rol, skill, escenario):

```
SkillReadiness = min(1.0, current_level / required_level)

Interpretación:
├─ Si current >= required → 1.0 (100%)
├─ Si current < required → current/required (parcial)
└─ Si current = 0 → 0.0 (no existe)

Ejemplo:
├─ Skill: "Kubernetes"
├─ required_level: 4 (Advanced)
├─ current_level: 2 (Intermediate)
└─ SkillReadiness = min(1.0, 2/4) = 0.5 (50%)
```

**Nivel 2: Competency Readiness**

Para cada competency en cada rol:

```
CompetencyReadiness = Σ (SkillReadiness_i × weight_i) / Σ (weight_i)
                    = promedio ponderado

Ejemplo: "Cloud Architecture" en Senior Developer
├─ Skill 101 (AWS EC2): SkillReadiness=0.75, weight=0.3 → 0.225
├─ Skill 102 (Kubernetes): SkillReadiness=0.5, weight=0.4 → 0.200
├─ Skill 103 (Security): SkillReadiness=1.0, weight=0.3 → 0.300
└─ CompetencyReadiness = (0.225 + 0.200 + 0.300) / 1.0 = 0.725 (72.5%)

Nota: Los weights se normalizan automáticamente
```

**Nivel 3: Capability Readiness**

Para cada capability:

```
CapabilityReadiness = Σ (CompetencyReadiness_i) / Ncount

Ejemplo: "Digital Transformation"
├─ Cloud Architecture: 0.725
├─ Data Analytics: 0.800
└─ Automation: 0.620
└─ CapabilityReadiness = (0.725 + 0.800 + 0.620) / 3 = 0.715 (71.5%)
```

**Nivel 4: Scenario IQ**

El indicador final:

```
ScenarioIQ = Σ (CapabilityReadiness_i × strategic_weight_i) × 100

Ejemplo: Escenario "IA 2026"
├─ Digital Transformation: 0.715, strategic_weight=0.4 → 0.286
├─ Generative AI: 0.450, strategic_weight=0.4 → 0.180
├─ User Research: 0.850, strategic_weight=0.2 → 0.170
└─ ScenarioIQ = (0.286 + 0.180 + 0.170) × 100 = 63.6
```

#### 4.3.2 Interpretación de Scenario IQ

| Rango      | Interpretación     | Acción                                                        |
| ---------- | ------------------ | ------------------------------------------------------------- |
| **0–33**   | 🔴 Alto Riesgo     | Transformación masiva. Requiere inversión crítica en talento. |
| **34–66**  | 🟡 Riesgo Moderado | Gaps específicos. Priorizar por capability.                   |
| **67–80**  | 🟢 Bajo Riesgo     | Organización mayormente lista. Ajustes menores.               |
| **81–100** | 🟢🟢 Listo         | Excelente posición. Enfoque en sostenibilidad.                |

**Ejemplo de Decisión:**

```
Escenario 1 (Base): ScenarioIQ = 63.6 → Viable con esfuerzo moderado
Escenario 2 (Aggressive): ScenarioIQ = 42.1 → Alto riesgo, requiere plan detallado
Escenario 3 (Conservative): ScenarioIQ = 78.5 → Bajo riesgo, preferible
└─ Decisión: Perseguir Escenario 3 como base, Escenario 1 como contingencia
```

---

### 4.4 Confidence Score

Indicador de calidad de datos.

```
Para cada person_role_skills:

evidence_score = {
    'self_assessment': 0.5,
    'manager_review': 0.7,
    'project': 0.8,
    'certification': 0.9,
    'test': 1.0
}

ConfidenceScore = Σ (evidence_score) / count × 100
```

**Ejemplo:**

```
Juan (AI Engineer):
├─ AWS EC2: test (1.0)
├─ Kubernetes: certification (0.9)
├─ GPT API: self_assessment (0.5)
└─ ConfidenceScore = (1.0 + 0.9 + 0.5) / 3 × 100 = 80%
└─ Interpretación: 80% confianza en datos (bueno, no perfecto)
```

---

### 4.5 Diagnóstico de Gaps

#### 4.5.1 Gap a Nivel de Skill

```
SkillGap = required_level - current_level

Ejemplo:
├─ Skill: "Prompt Engineering"
├─ required_level: 4
├─ current_level: 1
└─ Gap = 3 (crítico, requiere 3 niveles de desarrollo)
```

**Priorización:**

- Gaps en **required_level alto** (4-5) = más críticos
- Gaps en **essential/is_critical=true** = más críticos
- Gaps en **multiple people** = más impacto

#### 4.5.2 Gap a Nivel de Competency

```
Roles con brecha de "Cloud Architecture":
├─ Senior Developer: 2 skills con gap (AWS, Kubernetes)
├─ Data Analyst: 1 skill con gap (Security)
└─ Ops Manager: 3 skills con gap (todo)
```

#### 4.5.3 Gap a Nivel de Capability

```
"Digital Transformation" readiness: 63%
├─ Cloud Architecture: 72% (relativamente fuerte)
├─ Data Analytics: 55% (gap moderado)
└─ Automation: 48% (gap crítico)
```

**Priorización estratégica:**

```
1. Invertir en "Automation" (readiness 48%) → más impacto en IQ
2. Luego "Data Analytics" (readiness 55%)
3. Mantener "Cloud Architecture" (readiness 72%)
```

---

## 5. Ejemplo Guiado: "Adopción IA Generativa 2026"

### 5.1 Setup Inicial

**Contexto:**

```
Organización: TechCorp
Escenario: Adopción IA Generativa 2026
Horizonte: 18 meses
Roles Involved: Product Manager, AI Engineer, UX Designer
Persona Representativa: Juan (Senior Developer)
```

### 5.2 Paso 1: Capabilities Estratégicas

**Definidas en FASE 1:**

```sql
INSERT INTO scenario_capabilities VALUES
(5, 1, 0.4, 1, 'critical_transformation',
 'Digital Transformation es eje central para competir en 2026'),
(5, 2, 0.35, 2, 'critical_transformation',
 'Generative AI es diferenciador clave'),
(5, 3, 0.25, 3, 'supporting',
 'User Research requiere ser excelente pero no es bloqueante');
```

| Capability             | Strategic Weight | Priority | Status     |
| ---------------------- | ---------------- | -------- | ---------- |
| Digital Transformation | 0.40             | 1        | Critical   |
| Generative AI & LLMs   | 0.35             | 2        | Critical   |
| User Research & Design | 0.25             | 3        | Supporting |

**Competencies Asociadas:**

| Capability             | Competency         | Skills                                            |
| ---------------------- | ------------------ | ------------------------------------------------- |
| Digital Transformation | Cloud Architecture | AWS, Kubernetes, Security                         |
|                        | Data Analytics     | SQL, Python, BI Tools                             |
| Generative AI & LLMs   | Prompt Engineering | Design Patterns, API Usage, Evaluation            |
|                        | LLM Fine-tuning    | Transfer Learning, Hyperparameters, Training Data |
| User Research          | UX Research        | User Interviews, Personas, Testing                |
|                        | Design System      | Components, Accessibility, Documentation          |

### 5.3 Paso 2: Mapeo Roles ↔ Competencies (MATRIZ)

**LA MATRIZ INTERACTIVA:**

```
┌─────────────────────┬──────────────────────┬───────────────────┬────────────────┐
│ Rol                 │ Cloud Arch           │ Prompt Engineer   │ UX Research    │
├─────────────────────┼──────────────────────┼───────────────────┼────────────────┤
│ Product Manager     │ 📈 ENRICHMENT        │ 📈 ENRICHMENT     │ ✅ MAINT       │
│ (Existente, 3 FTE)  │ Level: 2             │ Level: 2          │ Level: 3       │
│                     │ Rationale: Entender  │ Rationale: Diseñar│ Rationale:     │
│                     │ infraestructura      │ features con IA   │ ya experto     │
├─────────────────────┼──────────────────────┼───────────────────┼────────────────┤
│ AI Engineer         │ 📈 ENRICHMENT        │ 🔄 TRANSFORM      │ ✅ MAINT       │
│ (Nuevo, 4 FTE)      │ Level: 3             │ 2 → 4 (18mo)      │ Level: 1       │
│                     │ Rationale: Deploy    │ Rationale: Core   │ Rationale:     │
│                     │ modelos en cloud     │ skill de rol      │ básico         │
├─────────────────────┼──────────────────────┼───────────────────┼────────────────┤
│ UX Designer         │ ❌ NO ASIGNADA       │ 📈 ENRICHMENT     │ 🔄 TRANSFORM   │
│ (Existente, 5 FTE)  │                      │ Level: 1          │ 3 → 4 (12mo)   │
│                     │                      │ Rationale: AI-    │ Rationale:     │
│                     │                      │ powered UX        │ evolución      │
├─────────────────────┼──────────────────────┼───────────────────┼────────────────┤
│ Data Scientist      │ ✅ MAINTENANCE       │ 🔄 TRANSFORM      │ ❌ NO ASIGNADA │
│ (Existente, 2 FTE)  │ Level: 3             │ 2 → 3 (12mo)      │                │
│                     │ Rationale: Sigue     │ Rationale: IA     │                │
│                     │ siendo crítica       │ aplica a análisis │                │
└─────────────────────┴──────────────────────┴───────────────────┴────────────────┘
```

**Resumen de scenario_role_competencies:**

```sql
INSERT INTO scenario_role_competencies VALUES
-- Product Manager
(NULL, 5, 20, 1, 2, false, 'enrichment', 'Entender cloud para product decisions'),
(NULL, 5, 20, 10, 2, true, 'enrichment', 'Diseñar features basadas en IA'),
(NULL, 5, 20, 11, 3, true, 'maintenance', 'UX Research sigue siendo crítica'),

-- AI Engineer
(NULL, 5, 50, 1, 3, true, 'enrichment', 'Deployar modelos en cloud'),
(NULL, 5, 50, 10, 4, true, 'transformation', 'Core skill - pasar de 2→4'),
(NULL, 5, 50, 11, 1, false, 'maintenance', 'Básica comprensión de UX'),

-- UX Designer
(NULL, 5, 30, 10, 1, true, 'enrichment', 'AI-powered UX es nueva'),
(NULL, 5, 30, 11, 4, true, 'transformation', 'Evolucionar de 3→4'),

-- Data Scientist
(NULL, 5, 40, 1, 3, false, 'maintenance', 'Cloud sigue siendo crítica'),
(NULL, 5, 40, 10, 3, true, 'transformation', 'IA aplica a análisis datos');
```

### 5.4 Paso 3: Derivación Automática de Skills

**RoleSkillDerivationService ejecuta automáticamente:**

**Para AI Engineer → "Prompt Engineering" (change_type='transformation'):**

```sql
-- Busca competency_skills de Prompt Engineering
SELECT * FROM competency_skills WHERE competency_id = 10;

Resultado:
├─ Skill 301: "Prompt Design Patterns" (weight: 0.40)
├─ Skill 302: "GPT API Usage" (weight: 0.35)
└─ Skill 303: "Evaluation Frameworks" (weight: 0.25)

-- Genera 3 filas en scenario_role_skills:
INSERT INTO scenario_role_skills (scenario_id, role_id, skill_id,
    required_level, change_type, source) VALUES
(5, 50, 301, 4, 'transformation', 'competency'),
(5, 50, 302, 4, 'transformation', 'competency'),
(5, 50, 303, 4, 'transformation', 'competency');
```

**Resultado Completo: scenario_role_skills para AI Engineer:**

```
AI Engineer (role_id = 50) en Escenario 5:

From Cloud Architecture (level 3, enrichment):
├─ Skill 101 (AWS): required=3, source=competency, change=enrichment
├─ Skill 102 (Kubernetes): required=3, source=competency, change=enrichment
└─ Skill 103 (Security): required=3, source=competency, change=enrichment

From Prompt Engineering (level 4, transformation):
├─ Skill 301 (Design Patterns): required=4, source=competency, change=transformation
├─ Skill 302 (GPT API): required=4, source=competency, change=transformation
└─ Skill 303 (Evaluation): required=4, source=competency, change=transformation

Manual Skills (si las hay):
└─ Skill 400 (Leadership): required=3, source=manual, change=NULL

TOTAL: 10 skills para AI Engineer en este escenario
```

### 5.5 Análisis IQ

**Current State (Hoy, 2026-01-01):**

```
Juan's Current Level (person_role_skills):
├─ AWS: 2
├─ Kubernetes: 1
├─ Security: 0 (nunca evaluado)
├─ Python: 4 (su strength actual)
└─ Prompt Engineering: 0 (no existe aún)

Cálculo por Rol/Skill:
├─ Skill 101 (AWS): current=2, required=3 → readiness=0.67
├─ Skill 102 (Kubernetes): current=1, required=3 → readiness=0.33
├─ Skill 103 (Security): current=0, required=3 → readiness=0.0
├─ Skill 301 (Prompt Design): current=0, required=4 → readiness=0.0
├─ Skill 302 (GPT API): current=0, required=4 → readiness=0.0
└─ Skill 303 (Evaluation): current=0, required=4 → readiness=0.0

CompetencyReadiness (Cloud Architecture):
├─ = (0.67×0.3 + 0.33×0.4 + 0.0×0.3) / 1.0
├─ = (0.201 + 0.132 + 0.0) / 1.0
└─ = 0.333 (33%)

CompetencyReadiness (Prompt Engineering):
├─ = (0.0×0.4 + 0.0×0.35 + 0.0×0.25) / 1.0
└─ = 0.0 (0%)

CapabilityReadiness (Digital Transformation):
├─ = Cloud + Data = 0.333 + 0.5 / 2
└─ = 0.416 (42%)

CapabilityReadiness (Generative AI):
├─ = Prompt Engineering + LLM Fine-tuning = 0.0 + 0.2 / 2
└─ = 0.1 (10%)

---

ScenarioIQ (Overall):
├─ Digital: 0.416 × 0.40 = 0.166
├─ AI: 0.1 × 0.35 = 0.035
├─ UX: 0.85 × 0.25 = 0.212
└─ TOTAL = (0.166 + 0.035 + 0.212) × 100 = 41.3
```

**Interpretación:** 🟡 Riesgo moderado. La organización puede ejecutar, pero requiere plan de desarrollo agresivo en IA.

**Target State (18 meses después, 2027-06-01):**

```
Juan después de ejecutar planes de desarrollo:
├─ AWS: 3 (completó curso)
├─ Kubernetes: 3 (completó proyecto)
├─ Security: 2 (awareness básica)
├─ Prompt Design: 3 (bootcamp + practica)
├─ GPT API: 3.5 (desarrollo en producción)
└─ Evaluation: 3 (experiencia práctica)

Readiness por Skill:
├─ AWS: 3/3 = 1.0
├─ Kubernetes: 3/3 = 1.0
├─ Security: 2/3 = 0.67
├─ Prompt Design: 3/4 = 0.75
├─ GPT API: 3.5/4 = 0.875
└─ Evaluation: 3/4 = 0.75

CompetencyReadiness (Cloud): (1.0×0.3 + 1.0×0.4 + 0.67×0.3) / 1.0 = 0.93
CompetencyReadiness (Prompt): (0.75×0.4 + 0.875×0.35 + 0.75×0.25) / 1.0 = 0.81

CapabilityReadiness (Digital): 0.93
CapabilityReadiness (AI): 0.81

ScenarioIQ = (0.93×0.40 + 0.81×0.35 + 0.85×0.25) × 100 = 87.5
```

**Interpretación:** 🟢 Bajo riesgo. Organización lista para escenario.

---

### 5.6 Gaps Identificados

**Resumen de Gaps (Current State):**

```
CRÍTICO - Capability "Generative AI & LLMs" (weight=0.35):
├─ Rol: AI Engineer (nuevo)
│  ├─ Skill: Prompt Design - Gap=4 (0→4)
│  ├─ Skill: GPT API - Gap=4 (0→4)
│  └─ Skill: Evaluation - Gap=4 (0→4)
│  └─ ACCIÓN: Bootcamp 8 semanas + mentoring
├─ Rol: Product Manager
│  ├─ Skill: LLM Product Thinking - Gap=2 (0→2)
│  └─ ACCIÓN: Workshop 2 semanas + mentoría

MODERADO - Capability "Digital Transformation" (weight=0.40):
├─ Rol: AI Engineer
│  ├─ Skill: Kubernetes - Gap=2 (1→3)
│  ├─ Skill: Security - Gap=3 (0→3)
│  └─ ACCIÓN: Online courses 6 semanas + proyecto piloto
├─ Rol: Data Scientist
│  └─ Skill: Data Privacy - Gap=2
│  └─ ACCIÓN: Certification program

BAJO - Capability "User Research" (weight=0.25):
├─ Rol: UX Designer
│  └─ Skill: AI-Powered UX - Gap=1 (3→4)
│  └─ ACCIÓN: Design workshop 2 semanas
```

**Plan de Acción (Priorización):**

```
FASE 1 (Meses 1-3): Capacitación crítica
├─ AI Engineer: Bootcamp Prompt Engineering (8 semanas)
├─ Data Scientist: Data Privacy Certification (6 semanas)
└─ ROI: Cierra gaps críticos en IA

FASE 2 (Meses 4-6): Especialización
├─ AI Engineer: Kubernetes Online Course + Proyecto
├─ Product Manager: AI Product Workshop
└─ ROI: Profundiza en Digital Transformation

FASE 3 (Meses 7-12): Consolidación
├─ Todos: Proyectos aplicados
├─ UX Designer: AI-Powered UX Workshop
└─ ROI: Maduración y transferencia

FASE 4 (Meses 13-18): Optimización
├─ Mentoría continua
├─ Evaluaciones frecuentes
└─ Ajustes según progreso
```

---

## 6. Buenas Prácticas y Decisiones de Diseño

### 6.1 Por qué Arquitectura Híbrida (Competencies + Skills)

**Problema Fundamental:**

```
❌ SOLO ROLES:
├─ "Necesitamos 5 Senior Developers"
├─ Impreciso: ¿Con qué skills?
├─ Inflexible: No soporta transformación de role
└─ → Imposible planificar cambio organizacional

❌ SOLO SKILLS:
├─ "Necesitamos 15 personas con Python"
├─ Granularidad excesiva: 100+ skills = inmanejable
├─ Desconexión: No ve relación con estrategia
└─ → Difícil alinear con capabilities estratégicas

✅ CAPABILITIES + COMPETENCIES + SKILLS:
├─ Nivel estratégico (executives): Hablan de Capabilities
├─ Nivel táctico (líderes negocio): Hablan de Competencies
├─ Nivel operacional (managers): Hablan de Skills
└─ → Cada uno entiende su nivel, conectados verticalmente
```

**Solución: Puentes entre Niveles**

```
STRATEGIC (Executive)
  "¿Qué Capabilities nos hacen ganadores?"
  → Digital Transformation, Generative AI, UX Excellence
           ↓
  Traducción: Capabilities → Competencies (Paso 1)

TACTICAL (Business Leader)
  "¿Cómo combinamos Competencies en Roles?"
  → Senior Dev = Cloud + Data + Leadership
           ↓
  Traducción: Competencies → Skills (Automático, Paso 3)

OPERATIONAL (Manager)
  "¿Qué personas tienen qué Skills hoy?"
  → Juan: Python 4, AWS 2, Kubernetes 1
           ↓
  Análisis: Current vs Required (Gap)
```

### 6.2 Ventajas para Escalabilidad

| Ventaja                   | Cómo                                                                | Beneficio                                      |
| ------------------------- | ------------------------------------------------------------------- | ---------------------------------------------- |
| **Reutilización**         | Una competency (ej: Cloud Architecture) se asigna a múltiples roles | No duplicar definiciones, mantener consistency |
| **Nuevos roles fáciles**  | Nuevo rol = seleccionar competencies existentes (no crear skills)   | Onboarding rápido sin rediseño                 |
| **Nuevas capabilities**   | Pueden nacer en escenarios (`discovered_in_scenario_id`)            | Experimentación segura antes de globalizar     |
| **Catálogo centralizado** | Skills son únicos, competencies son únicas                          | Single source of truth                         |
| **Cambio propagado**      | Si cambias `competency_skills.weight`, afecta todos los análisis    | Mantenibilidad, menos errores                  |

### 6.3 Ventajas para Mantenibilidad

| Aspecto                     | Ventaja                                                                                         |
| --------------------------- | ----------------------------------------------------------------------------------------------- |
| **Automatización**          | `RoleSkillDerivationService` genera scenario_role_skills automáticamente. Menos trabajo manual. |
| **Isolación de escenarios** | Un escenario no afecta otro. Permite experimentación sin riesgo.                                |
| **Trazabilidad**            | Campos como `change_type`, `source`, `discovered_in_scenario_id` permiten auditoría completa.   |
| **Versionado implícito**    | Scenarios son snapshots. Nueva versión = nuevo scenario row. No sobrescribir.                   |
| **Validaciones lógicas**    | Reglas claras: todo skill deriva de competency o es manual. Fácil validar.                      |

### 6.4 Cómo Extender el Modelo

#### 6.4.1 Nuevo Escenario

```
PASO 1: Crear base
├─ scenarios row (nombre, horizonte, assumptions)
├─ Copiar scenario_capabilities del escenario anterior (como template)
└─ Ajustar strategic_weight si es necesario

PASO 2: Mapear roles
├─ scenario_roles (roles nuevos o existentes)
├─ scenario_role_competencies (matriz)
└─ Documentar change_type y rationale

PASO 3: Automático
├─ RoleSkillDerivationService genera scenario_role_skills
├─ ScenarioAnalyticsService calcula IQ
└─ Dashboard muestra readiness y gaps

TIEMPO: ~30 minutos (una vez que el flujo es familiar)
```

#### 6.4.2 Nueva Capability

```
PASO 1: Crear capability
├─ INSERT capabilities (name, category, status, discovered_in_scenario_id)
└─ discovered_in_scenario_id = NULL (global) o scenario_id (incubation)

PASO 2: Definir competencies
├─ INSERT competencies para cada subdominio
└─ Documentar qué skills las componen

PASO 3: Mapping skills
├─ INSERT competency_skills (con weights)
└─ Validar sum de weights ≈ 1.0 (recomendado)

PASO 4: Integrar en escenarios
├─ scenario_capabilities (si es relevante en algún escenario)
├─ Asignar strategic_weight
└─ scenario_role_competencies (si algún rol la requiere)

TIEMPO: ~1-2 horas (análisis + definición)
```

#### 6.4.3 Nuevo Rol

```
OPCIÓN A: Estático (sin escenario)
├─ roles row (catálogo global)
├─ role_competencies (qué necesita típicamente)
├─ role_skills (derivadas automáticamente)
└─ Uso: Template para escenarios

OPCIÓN B: Escenario-specific
├─ scenario_roles (role nuevo solo en este escenario)
├─ scenario_role_competencies (matriz para este rol)
├─ RoleSkillDerivationService auto-genera scenario_role_skills
└─ Uso: Nuevos roles que emergen en escenarios

TIEMPO: 15-30 minutos por rol
```

### 6.5 Limitaciones y Trade-offs

| Aspecto                   | Decisión Actual                                                 | Pro                              | Con                                                      | Considerar Futuro                   |
| ------------------------- | --------------------------------------------------------------- | -------------------------------- | -------------------------------------------------------- | ----------------------------------- |
| **Derivación automática** | scenario_role_skills derivada de competencies                   | Consistency, less manual         | Menos flexible para excepciones                          | source='manual' permite excepciones |
| **Source semántica**      | source='competency' vs 'manual'                                 | Auditoría clara                  | Requiere disciplina en datos                             | Validaciones en UI                  |
| **Niveles (1-5)**         | Escalas fijas                                                   | Estándar, comparable             | No soporta escalas custom                                | JSON field para metadata            |
| **Change_type**           | 4 estados (maintenance, transformation, enrichment, extinction) | Cobertura 80% de casos           | Puede necesitar estados adicionales                      | Extender a 6-8 estados              |
| **Peso de competencies**  | weight en competency_skills                                     | Realista (skills no son iguales) | Requiere calibración                                     | Defaults predefinidos               |
| **Capability N:N**        | Actualmente N:1 (competency → capability)                       | Simplicidad                      | Una competency puede contribuir a múltiples capabilities | Refactor a N:N si es necesario      |

### 6.6 Patrones de Uso Recomendados

**Patrón 1: Scenario Comparison (Planificación Estratégica)**

```
Crear 3 escenarios:
├─ BASE (probabilidad 50%): Crecimiento 10%, poca automatización
├─ CONSERVATIVE (probabilidad 30%): Crecimiento 5%, RPA selectiva
└─ AGGRESSIVE (probabilidad 20%): Crecimiento 20%, IA extensiva

Comparar ScenarioIQ:
├─ BASE: 65 (viable)
├─ CONSERVATIVE: 78 (preferible)
└─ AGGRESSIVE: 42 (alto riesgo)

Decisión:
└─ Perseguir CONSERVATIVE, con plan de escalado hacia AGGRESSIVE

Beneficio: Risk-aware planning con opciones abiertas
```

**Patrón 2: Capability Incubation (Innovation)**

```
Capability nueva: "Blockchain & Web3"
├─ discovered_in_scenario_id = 6 (Escenario "Future Tech 2027")
├─ Status: draft (experimental)
└─ No es global aún

Después de 6 meses, si funciona:
├─ Update status = active
├─ Set discovered_in_scenario_id = NULL (promover a global)
└─ Integrar en otros escenarios

Beneficio: Experimentación segura sin impactar catalogo global
```

**Patrón 3: Role Evolution Narrative (Change Management)**

```
Rol: Operations Manager
├─ Hoy: Gestión de procesos, reducción de costos
├─ Futuro (Escenario 2026): Platform Engineer

scenario_roles:
├─ evolution_type = 'transformation'
├─ impact_level = 'high'

scenario_role_competencies:
├─ Operations (existing): change_type='extinction' (desaparece en 18 meses)
├─ Cloud Infrastructure: change_type='transformation' (3→4)
├─ DevOps Practices: change_type='enrichment' (nueva)

Narrativa clara: "Manager evoluciona de tradicional a modern ops"

Beneficio: Comunicación clara del cambio, gestión del talento proactiva
```

---

## Conclusión

Stratos implementa una arquitectura **jerárquica, trazable y automática** para planificación dotacional estratégica. La clave es el flujo:

```
PASO 1 (Estratégico)
  ↓ Define Capabilities + Competencies
PASO 2 (Táctico) ← LA MATRIZ
  ↓ Mapea Roles ↔ Competencies con change_type
PASO 3 (Operacional)
  ↓ Deriva Skills automáticamente
PASO 4 (Análisis)
  ↓ Calcula IQ, identifica gaps
PASO 5 (Ejecución)
  ↓ Planes de desarrollo + seguimiento
```

Cada nivel habla el lenguaje de quien lo usa, conectados por un modelo de datos limpio y extensible.
