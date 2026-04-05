# 🧭 Stratos — Dominios de Planificación: Workforce Planning vs. Talent Planning

> **Fecha**: 5 de abril de 2026  
> **Objetivo**: Eliminar ambigüedad entre "brecha dotacional" y "brecha de competencias" en la arquitectura de Stratos.  
> **Audiencia**: Equipo técnico, product managers, stakeholders.

---

## 📋 Resumen del problema

Stratos utiliza el término **"gap"** (brecha) en dos contextos fundamentalmente distintos que se solapan en la implementación actual:

|                        | **Workforce Planning (Dotacional)**                              | **Talent Planning (Competencias)**                                         |
| ---------------------- | ---------------------------------------------------------------- | -------------------------------------------------------------------------- |
| **Pregunta central**   | _¿Cuántas personas necesitamos?_                                 | _¿Qué tan capaces son las que tenemos?_                                    |
| **Nombre en Stratos**  | **Stratos Horizon**                                              | **Talent Intelligence Core**                                               |
| **Unidad de medida**   | Headcount / FTE / horas-persona                                  | Nivel de proficiency (1-5)                                                 |
| **Tipo de gap**        | **Headcount gap** = requeridos − disponibles                     | **Proficiency gap** = nivel requerido − nivel actual                       |
| **Ejemplo**            | "Necesitamos 12 Cloud Architects, tenemos 8 → gap de 4 personas" | "Cloud Architecture requiere nivel 4, promedio actual es 2.3 → gap de 1.7" |
| **Horizonte temporal** | Trimestral / anual                                               | Continuo                                                                   |
| **Acción de cierre**   | Contratar, reasignar, subcontratar                               | Capacitar, desarrollar, mentorar                                           |
| **Responsable**        | HR Operations / Finance                                          | L&D / Talent Development                                                   |

---

## 🏗️ Mapeo a la arquitectura de Stratos

### Dominio 1: Stratos Horizon (Workforce Planning — Dotacional)

> **Foco**: Oferta y demanda de personas. Cuántos necesitamos vs. cuántos tenemos.

```
Modelos:
├── WorkforceDemandLine        → volumen, horas, productividad, cobertura
├── WorkforceActionPlan        → acciones para cubrir déficit dotacional
├── ScenarioSkillDemand        → required_headcount vs current_headcount ← DOTACIONAL
│                              → required_level vs current_avg_level    ← COMPETENCIAS (mixto!)
├── StrategicPlanningScenarios → escenarios what-if de dotación
└── ScenarioClosureStrategy    → estrategias de cierre (contratar/reskill/reasignar)

Servicios:
├── WorkforcePlanningController → CRUD escenarios, demand lines, action plans
├── ScenarioAnalyticsController → analytics de gap dotacional (placeholder getSkillGaps)
└── WorkforceSensitivity*       → análisis de sensibilidad de variables dotacionales
```

**Outputs**: Cuántas personas contratar, reasignar o liberar. Costo de la fuerza laboral. Timeline de contratación.

### Dominio 2: Talent Intelligence Core (Talent Planning — Competencias)

> **Foco**: Nivel de competencia de las personas que ya tenemos. Qué tan buenas son vs. qué tan buenas necesitamos que sean.

```
Modelos:
├── people_role_skills         → current_level vs required_level POR PERSONA
├── ScenarioRoleSkill          → current_level vs required_level POR ROL en escenario
├── ScenarioRoleCompetency     → competencia × rol con nivel requerido
├── Skill / Competency         → catálogo de skills y competencias
└── LmsLearnerProfile          → perfil adaptativo del learner

Servicios:
├── LearningBlueprintService   → detecta proficiency gaps → genera plan de formación
├── SuccessionPlanningService  → evalúa readiness basada en cierre de gaps
├── StratosIqService           → average_gap organizacional (proficiency)
├── SkillIntelligenceService   → análisis de skills, tendencias, benchmarks
├── TalentRiskAnalyticsService → risk scoring basado en skill_gap
└── AnalyzeTalentGap (Job)     → análisis profundo de gap competencia + IA

Agentes:
├── Estratega de Talento       → análisis estratégico de brechas de competencias
├── Coach de Crecimiento       → planes de desarrollo personalizados
├── Curador de Competencias    → taxonomía dinámica de competencias
└── Selector de Talento        → matching candidato-puesto por competencias
```

**Outputs**: Qué capacitar, qué skills desarrollar, quién necesita mentoría, qué cursos crear.

### Zona de integración: Donde se conectan (no se solapan)

```
┌─────────────────────────┐           ┌─────────────────────────┐
│   STRATOS HORIZON       │           │   TALENT INTELLIGENCE   │
│   (Dotacional)          │           │   (Competencias)        │
│                         │           │                         │
│  "Necesitamos 4 más     │──────────▶│  "Los 8 que tenemos     │
│   Cloud Architects"     │  informa  │   están en nivel 2.3,   │
│                         │           │   necesitan nivel 4"    │
│  Headcount gap: 4       │           │   Proficiency gap: 1.7  │
│                         │◀──────────│                         │
│  "¿Contratar o          │  informa  │  "Reskilling de 3       │
│   reskill interno?"     │           │   personas cierra 75%   │
│                         │           │   del gap en 4 meses"   │
└───────────┬─────────────┘           └───────────┬─────────────┘
            │                                     │
            │         ZONA DE INTEGRACIÓN          │
            │                                     │
            ▼                                     ▼
┌─────────────────────────────────────────────────────────────┐
│                     ACCIONES DE CIERRE                       │
│                                                             │
│  ┌─────────────────────┐    ┌────────────────────────────┐  │
│  │ Dotacional:         │    │ Competencias:              │  │
│  │ • Contratar 2       │    │ • Ruta Praxis para 6       │  │
│  │ • Reasignar 1       │    │ • Comunidad Ágora Cloud    │  │
│  │ • Subcontratar 1    │    │ • Mentoría 1:1 para 3      │  │
│  └─────────────────────┘    └────────────────────────────┘  │
│                                                             │
│  ScenarioClosureStrategy.strategy:                          │
│  • "hire"        → Dotacional                               │
│  • "reskill"     → Competencias (→ Praxis)                  │
│  • "reallocate"  → Dotacional                               │
│  • "upskill"     → Competencias (→ Ágora)                   │
│  • "outsource"   → Dotacional                               │
│  • "mentor"      → Competencias (→ Ágora)                   │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔍 Ambigüedades detectadas y correcciones

### Ambigüedad 1: `ScenarioSkillDemand` mezcla ambos dominios

**Antes** (ambiguo):

```php
// ¿Qué tipo de gap es? Ambos coexisten en el mismo modelo
$demand->getGapHeadcount();  // → dotacional
$demand->getGapLevel();      // → competencias
```

**Después** (explícito):

```php
// Nombres explícitos que distinguen el dominio
$demand->getHeadcountGap();    // → gap dotacional (personas faltantes)
$demand->getProficiencyGap();  // → gap de competencias (niveles)
```

**Justificación**: `ScenarioSkillDemand` es legítimamente un modelo **puente** entre ambos dominios. Un escenario puede necesitar "4 personas más con nivel 4 en Cloud Architecture". Eso es un gap dotacional (4 personas) Y un gap de proficiency (nivel 4 vs nivel actual 2.3). Pero los métodos deben nombrar explícitamente qué tipo de gap retornan.

### Ambigüedad 2: `CommunityFormationService` no distingue origen del gap

**Antes** (ambiguo):

```php
// ¿Es gap de headcount o de proficiency? El caller decide, pero la interfaz no lo dice
$service->analyzeAndSuggest($orgId, [
    ['skill_name' => 'Cloud', 'gap_size' => 1.7, 'affected_count' => 12]
]);
```

**Después** (explícito):

```php
// El campo gap_type aclara de dónde viene la brecha
$service->analyzeAndSuggest($orgId, [
    ['skill_name' => 'Cloud', 'gap_size' => 1.7, 'affected_count' => 12, 'gap_type' => 'proficiency']
]);
// gap_type: 'proficiency' (de Talent Planning) o 'headcount' (de Workforce Planning)
```

### Ambigüedad 3: `AnalyzeTalentGap` usa "Talent Gap" pero analiza competency gap

**Corrección**: PHPDoc clarificado para especificar que este job analiza **proficiency gaps** (brecha entre nivel requerido y actual de una competencia en un rol), no gaps dotacionales.

### Ambigüedad 4: `StratosIqService.average_gap` no dice qué tipo de gap

**Corrección**: PHPDoc clarificado: `average_gap` = **average proficiency gap** (promedio de `required_level - current_level` en `people_role_skills`). No es un gap de headcount.

---

## 📐 Reglas de diseño para el futuro

### Regla 1: Nombrar el tipo de gap siempre

```
❌ $gap, gap_size, getGap()
✅ $headcountGap, $proficiencyGap, getHeadcountGap(), getProficiencyGap()
```

### Regla 2: Interfaces que consumen gaps deben declarar qué tipo aceptan

```php
// ❌ Ambiguo
function analyzeGap(array $gap): array;

// ✅ Explícito
/**
 * @param array $gap {
 *   skill_name: string,
 *   gap_size: float,        // proficiency gap (levels) or headcount gap (persons)
 *   gap_type: 'proficiency'|'headcount',
 *   affected_count: int
 * }
 */
function analyzeGap(array $gap): array;
```

### Regla 3: `ScenarioClosureStrategy.strategy` indica el dominio

| Strategy     | Dominio      | Acción                       |
| ------------ | ------------ | ---------------------------- |
| `hire`       | Dotacional   | Contratar externamente       |
| `reallocate` | Dotacional   | Reasignar internamente       |
| `outsource`  | Dotacional   | Subcontratar                 |
| `reskill`    | Competencias | Capacitar vía Praxis         |
| `upskill`    | Competencias | Desarrollar vía Ágora/Praxis |
| `mentor`     | Competencias | Mentoría vía Ágora           |

### Regla 4: Reportes y dashboards deben separar visualmente

```
Dashboard Horizon (WFP):
├── Headcount actual vs. requerido (personas)
├── Costo de dotación proyectado ($)
├── Timeline de contratación
└── Cobertura dotacional (%)

Dashboard Talent Intelligence:
├── Proficiency promedio vs. requerida (nivel 1-5)
├── Skills con mayor brecha
├── Progreso de cierre de brechas (%)
└── Efectividad de formación (Praxis ROI)
```

---

## 🔗 Modelo conceptual integrado

```
                    STRATOS PLATFORM
    ╔══════════════════════════════════════════╗
    ║                                          ║
    ║   ┌──────────────┐  ┌────────────────┐   ║
    ║   │   HORIZON    │  │    TALENT       │   ║
    ║   │  (Dotacional)│  │  INTELLIGENCE   │   ║
    ║   │              │  │ (Competencias)  │   ║
    ║   │  ¿Cuántos?   │  │  ¿Qué nivel?   │   ║
    ║   │  Headcount   │  │  Proficiency    │   ║
    ║   │  FTE, costo  │  │  Skills, gaps   │   ║
    ║   └──────┬───────┘  └───────┬────────┘   ║
    ║          │    INTEGRACIÓN    │            ║
    ║          │  ScenarioSkill    │            ║
    ║          │  Demand (puente)  │            ║
    ║          └────────┬─────────┘            ║
    ║                   │                      ║
    ║          ┌────────▼─────────┐            ║
    ║          │  CLOSURE ENGINE  │            ║
    ║          │                  │            ║
    ║          │ hire → Dotacional│            ║
    ║          │ reskill → Praxis │            ║
    ║          │ upskill → Ágora  │            ║
    ║          │ mentor → Ágora   │            ║
    ║          └────────┬─────────┘            ║
    ║                   │                      ║
    ║     ┌─────────────┼─────────────┐        ║
    ║     ▼             ▼             ▼        ║
    ║  ┌──────┐   ┌──────────┐  ┌─────────┐   ║
    ║  │PRAXIS│   │  ÁGORA   │  │  NEXUS  │   ║
    ║  │(LMS) │   │(Comunid.)│  │  (IA)   │   ║
    ║  └──────┘   └──────────┘  └─────────┘   ║
    ╚══════════════════════════════════════════╝
```

### Flujo de decisión

```
1. HORIZON detecta: "Faltan 4 Cloud Architects" (headcount gap)
2. TALENT INTEL analiza: "Los 8 actuales están en nivel 2.3/4.0" (proficiency gap)
3. CLOSURE ENGINE decide:
   a. Contratar 2 (dotacional → hire)
   b. Reskill 2 de infra a cloud (dotacional → reallocate + competencias → reskill)
   c. Upskill los 8 actuales de 2.3 a 4.0 (competencias → upskill)
4. PRAXIS crea ruta de Cloud Architecture para los 10
5. ÁGORA crea comunidad de Cloud Architects con mentores
6. NEXUS enriquece: "En el sector Fintech, empresas que combinaron
   contratación (50%) + reskilling (50%) cerraron gaps en 4.2 meses"
```

---

## 📊 Resumen de cambios implementados

| Archivo                     | Cambio                                                                             | Tipo           |
| --------------------------- | ---------------------------------------------------------------------------------- | -------------- |
| `ScenarioSkillDemand`       | `getGapHeadcount()` → `getHeadcountGap()`, `getGapLevel()` → `getProficiencyGap()` | Renaming       |
| `CommunityFormationService` | Acepta `gap_type` en input, documenta tipos                                        | PHPDoc + param |
| `CommunityController`       | `suggestFromGaps` acepta `gap_type` opcional                                       | Validation     |
| `AnalyzeTalentGap` job      | PHPDoc clarifica: proficiency gap, no headcount                                    | PHPDoc         |
| `LearningBlueprintService`  | PHPDoc clarifica: detecta proficiency gaps individuales                            | PHPDoc         |
| `StratosIqService`          | PHPDoc clarifica: `average_gap` = average proficiency gap                          | PHPDoc         |

---

_Documento de referencia para el equipo técnico. Mantener actualizado al agregar nuevas funcionalidades de gap analysis._
