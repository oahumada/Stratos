# 📋 OUTLINE - Arquitectura Técnica Stratos (Paso 2)

**Estado:** ⏳ VALIDACIÓN ESTRUCTURA  
**Fecha:** 2026-02-02  
**Audiencia:** Developers (backend + frontend)  
**Entrega Final:** `/docs/ARQUITECTURA_STRATOS_PASO2.md`

---

## ✅ SECCIONES PROPUESTAS

### 1️⃣ **INTRODUCCIÓN A TALENTIA** (~600 palabras)

#### 1.1 Objetivo del Sistema

- Qué es Stratos: plataforma de planificación dotacional estratégica
- A quién sirve: RRHH + Líderes de negocio + Ejecutivos
- Problema que resuelve: desconexión entre estrategia y talento

#### 1.2 Diferenciadores Clave

- ✅ Trabaja por **escenarios** (no planificación fija anual)
- ✅ Maneja **3 niveles de abstracción** (capabilities → competencies → skills)
- ✅ **Trazabilidad** del cambio (evolución_type, change_type, discovered_in_scenario_id)
- ✅ **Análisis automático** (RoleSkillDerivationService + ScenarioAnalyticsService)

#### 1.3 Niveles de Gestión

```
Estratégico    → Scenarios + Capabilities
Táctico        → Competencies por rol en escenario
Operacional    → Skills por rol y persona
```

---

### 2️⃣ **ARQUITECTURA LÓGICA** (~800 palabras)

#### 2.1 Modelo Conceptual: Capabilities → Competencies → Skills

- Diagrama texto ASCII
- Relación jerárquica
- Cómo se descubren nuevas capacidades (discovered_in_scenario_id)
- Ejemplo: "Cloud Architecture" capability → "AWS Expertise" competency → "EC2 Configuration" skill

#### 2.2 Modelo Conceptual: Escenario → Roles → Competencies → Skills → Personas

- Diagrama texto ASCII
- Flujo de datos de arriba hacia abajo
- Roles como "nexo" entre estrategia (escenario) y operación (personas)
- Ejemplo: Escenario "IA Generativa 2026" → Rol "AI Engineer" → Skills requeridas → Gap vs personas

#### 2.3 Ciclo de Vida Conceptual

```
PASO 1 (Fase 1-2): Diseñar Escenario
  ├─ Definir Capabilities estratégicas
  ├─ Definir Competencies (que ya existen o se crean)
  └─ Output: scenario_capabilities + competencies

PASO 2 (Fase Táctica): Mapear Roles ↔ Competencies
  ├─ Seleccionar/crear Roles en Escenario
  ├─ Asignar Competencies con change_type (transformación, extinción, enriquecimiento)
  └─ Output: scenario_role_competencies

PASO 3+ (Fase Operacional): Derivar Skills + Analizar
  ├─ RoleSkillDerivationService → scenario_role_skills
  ├─ ScenarioAnalyticsService → Scenario IQ + Gaps
  └─ Output: Readiness + Recomendaciones
```

---

### 3️⃣ **MODELO DE DATOS** (~1500 palabras)

#### 3.1 Tabla: `capabilities`

- **Propósito:** Pilares estratégicos de capacidad organizacional
- **Campos clave:** id, organization_id, name, category, status, discovered_in_scenario_id
- **Relaciones:** ← competencies (1:N), ← scenario_capabilities (1:N)
- **Notas especiales:**
  - `discovered_in_scenario_id`: qué escenario reveló esta capability
  - Permite "incubación": capacidades nuevas emergen en escenarios

#### 3.2 Tabla: `competencies`

- **Propósito:** Agrupaciones coherentes de skills dentro de una capability
- **Campos clave:** id, organization_id, capability_id, name, description
- **Relaciones:** ← capability (N:1), → competency_skills (1:N), → scenario_role_competencies (1:N)
- **Notas especiales:**
  - Puente entre mundo estratégico (capabilities) y operacional (skills)

#### 3.3 Tabla: `competency_skills`

- **Propósito:** Relación N:N entre competencies y skills con peso
- **Campos clave:** id, competency_id, skill_id, weight
- **Notas especiales:**
  - `weight`: importancia de cada skill en la competency (0.0–1.0)
  - Usado en `ScenarioAnalyticsService` para cálculo ponderado de readiness

#### 3.4 Tabla: `scenarios`

- **Propósito:** Escenarios futuros hipotéticos (estratégicos)
- **Campos clave:** id, organization_id, name, horizon_months, status, assumptions
- **Relaciones:** ← scenario_capabilities, ← scenario_roles, ← scenario_role_competencies, ← scenario_role_skills

#### 3.5 Tabla: `scenario_capabilities`

- **Propósito:** Qué capabilities son críticas en este escenario
- **Campos clave:** scenario_id, capability_id, strategic_role, strategic_weight, priority, rationale
- **Notas especiales:**
  - `strategic_weight`: importancia de la capability en el escenario (0.0–1.0)
  - Usado en `ScenarioAnalyticsService` para cálculo final del Scenario IQ

#### 3.6 Tabla: `scenario_roles`

- **Propósito:** Roles dentro del escenario (nueva configuración organizacional)
- **Campos clave:** id, scenario_id, role_id, role_change, impact_level, evolution_type, rationale
- **Relaciones:** → scenario_role_competencies (1:N)
- **Notas especiales:**
  - `role_change`: qué tipo de cambio experimenta el rol (crear, eliminar, modificar)
  - `evolution_type`: cómo evoluciona (upgrade_skills, downsize, transformation, new_role)

#### 3.7 Tabla: `role_competencies` (Referencia Estática)

- **Propósito:** Competencies que requiere un rol **en general** (sin escenario)
- **Campos clave:** id, role_id, competency_id, required_level, is_core
- **Relaciones:** ← role (N:1), ← competency (N:1)
- **⚠️ Diferencia vs scenario_role_competencies:**
  - `role_competencies`: **permanente**, describe el rol "por defecto"
  - `scenario_role_competencies`: **temporal**, describe el rol **dentro de un escenario específico**
  - Ejemplo: Developer requiere "Cloud Skills" siempre (role_competencies), pero en escenario 2026 requiere nivel 4 en IA (scenario_role_competencies)

#### 3.8 Tabla: `scenario_role_competencies` (Táctica)

- **Propósito:** Competencies requeridas por un rol **en este escenario específico**
- **Campos clave:** id, scenario_id, role_id, competency_id, required_level, is_core, **change_type**, rationale
- **⭐ CRÍTICA PARA PASO 2:**
  - `change_type`: estado de la competencia en el escenario
    - `maintenance` (✅) - Se mantiene igual
    - `transformation` (🔄) - Requiere upskilling
    - `enrichment` (📈) - Nueva competencia o mejorada
    - `extinction` (📉) - Desaparecerá del rol
  - `required_level`: nivel futuro requerido (1–5)
- **Relaciones:** ← scenario (N:1), ← role (N:1), ← competency (N:1)

#### 3.9 Tabla: `role_skills` (Referencia Estática)

- **Propósito:** Skills necesarias en un rol **en general** (sin escenario)
- **Campos clave:** id, role_id, skill_id, required_level, is_critical, **source**, competency_id
- **Notas especiales:**
  - `source='competency'`: skill derivada automáticamente desde competency_skills
  - `source='manual'`: skill agregada manualmente (excepción/especialización)
- **⚠️ Diferencia vs scenario_role_skills:**
  - `role_skills`: **referencia**, describe lo que "típicamente" necesita el rol
  - `scenario_role_skills`: **derivada**, describe lo que necesita **en este escenario**

#### 3.10 Tabla: `scenario_role_skills` (Operacional)

- **Propósito:** Skills requeridas por un rol **en este escenario** (derivadas automáticamente)
- **Campos clave:** id, scenario_id, role_id, skill_id, required_level, is_critical, **change_type**, source, competency_id, rationale
- **⭐ GENERADA POR RoleSkillDerivationService:**
  - `source='competency'`: skill generada desde scenario_role_competencies × competency_skills
  - `source='manual'`: skill agregada manualmente (respetada, no sobrescrita)
  - `change_type`: igual que scenario_role_competencies (maintenance, transformation, enrichment, extinction)

#### 3.11 Tabla: `people`

- **Propósito:** Registro de personas en la organización
- **Relaciones:** ← person_role_skills (1:N)

#### 3.12 Tabla: `person_role_skills`

- **Propósito:** Perfil de skills actual de una persona **para un rol específico**
- **Campos clave:** id, person_id, role_id, skill_id, current_level, verified, evidence_source, evidence_date
- **Notas especiales:**
  - `evidence_source`: fuente de la evaluación (self_assessment, manager_review, certification, test, etc.)
  - Usado en `ScenarioAnalyticsService` para calcular Readiness y Confidence Score
  - Permite comparar `current_level` vs `required_level` en `scenario_role_skills`

---

### 4️⃣ **FLUJOS DE NEGOCIO PRINCIPALES** (~2000 palabras)

#### 4.1 Diseño de Escenario (Fase 1-2)

##### 4.1.1 Fase 1: Definir Capabilities Estratégicas

```
INPUT:
├─ Estrategia de negocio (visión 2026)
├─ Análisis de drivers (digital, transformación, crecimiento)
└─ Catalogo de capabilities existentes

PROCESO:
├─ Seleccionar capabilities relevantes → scenario_capabilities
├─ Asignar strategic_weight (importancia 0.0-1.0)
└─ Documentar rationale (por qué esta capability)

OUTPUT:
└─ scenario_capabilities poblada
```

##### 4.1.2 Fase 2: Definir Competencies por Rol en Escenario

```
INPUT:
├─ Roles que existirán en el escenario (scenario_roles)
├─ scenario_capabilities (del paso anterior)
└─ Competencies del catálogo

PROCESO (PASO 2 = AQUÍ):
├─ Para cada rol:
│  ├─ Seleccionar/crear el rol en scenario_roles
│  ├─ Asignar competencies que requiere → scenario_role_competencies
│  ├─ Definir change_type (maintenance / transformation / enrichment / extinction)
│  └─ Si transformation: especificar required_level futuro
└─ Guardar matriz completa

OUTPUT:
└─ scenario_role_competencies poblada (+ scenario_roles si son nuevos)
```

#### 4.2 Derivación Automática de Skills (RoleSkillDerivationService)

##### 4.2.1 Algoritmo

```
ENTRADA:
└─ scenario_role_competencies (filas de matriz Paso 2)

LÓGICA:
├─ Para cada (scenario_id, role_id, competency_id):
│  ├─ Buscar competency_skills.* donde competency_id = X
│  └─ Para cada skill derivado:
│     ├─ Crear scenario_role_skills
│     ├─ Copiar required_level desde scenario_role_competencies
│     ├─ Copiar change_type desde scenario_role_competencies
│     └─ Marcar source='competency'
│
└─ Respeto excepciones:
   └─ Si existe scenario_role_skills con source='manual', no sobrescribir

SALIDA:
└─ scenario_role_skills completamente poblada
```

##### 4.2.2 Manejo de Excepciones (Manual Skills)

- Si usuario agregó manualmente una skill al rol (que no viene de competencies)
- `RoleSkillDerivationService` respeta `source='manual'` y no la toca
- Permite especialización y casos edge

#### 4.3 Cálculo del Scenario IQ (ScenarioAnalyticsService)

##### 4.3.1 Fórmula Paso a Paso

**Nivel 1: Skill Readiness**

```
SkillReadiness = min(1.0, current_level / required_level)

Ejemplo:
├─ Skill: "AWS EC2"
├─ required_level: 4 (Advanced)
├─ current_level: 2 (Intermediate)
└─ SkillReadiness = min(1.0, 2/4) = 0.5 (50%)
```

**Nivel 2: Competency Readiness**

```
CompetencyReadiness = Σ(SkillReadiness × weight) / Σ(weight)
                    = promedio ponderado de SkillReadiness

Ejemplo:
├─ Competency: "Cloud Architecture"
│  ├─ "AWS EC2": SkillReadiness=0.5, weight=0.3 → 0.15
│  ├─ "Kubernetes": SkillReadiness=0.8, weight=0.4 → 0.32
│  └─ "Security": SkillReadiness=0.9, weight=0.3 → 0.27
└─ CompetencyReadiness = (0.15 + 0.32 + 0.27) / 1.0 = 0.74 (74%)
```

**Nivel 3: Capability Readiness**

```
CapabilityReadiness = promedio de CompetencyReadiness

Ejemplo:
├─ Capability: "Digital Transformation"
│  ├─ "Cloud Architecture": 0.74
│  ├─ "Data Analytics": 0.85
│  └─ "Automation": 0.62
└─ CapabilityReadiness = (0.74 + 0.85 + 0.62) / 3 = 0.74 (74%)
```

**Nivel 4: Scenario IQ**

```
ScenarioIQ = Σ(CapabilityReadiness × strategic_weight) × 100

Ejemplo:
├─ Digital Transformation: 0.74, weight=0.4 → 0.296
├─ Innovation: 0.68, weight=0.3 → 0.204
└─ Operations: 0.81, weight=0.3 → 0.243
└─ ScenarioIQ = (0.296 + 0.204 + 0.243) × 100 = 74.3
```

##### 4.3.2 Interpretación

- ScenarioIQ 0-33: Alto riesgo (transformación masiva necesaria)
- ScenarioIQ 34-66: Riesgo moderado (gaps específicos)
- ScenarioIQ 67-100: Bajo riesgo (organización lista)

#### 4.4 Confidence Score

```
ENTRADA:
└─ Evidence sources en person_role_skills

CALCULO:
├─ self_assessment: 0.5x
├─ manager_review: 0.7x
├─ certification: 0.9x
├─ test: 1.0x
└─ ConfidenceScore = promedio ponderado × 100
```

#### 4.5 Diagnóstico de Gaps

##### 4.5.1 Gap a Nivel de Skill

```
SkillGap = required_level - current_level

Ejemplo:
├─ Skill: "Python Avanzado"
├─ required_level: 4
├─ current_level: 1
└─ Gap = 3 niveles
```

##### 4.5.2 Gap a Nivel de Competency

```
Roles con gaps de esta competency:
├─ Software Engineer: gap en 2 skills (3+2 niveles)
├─ Data Analyst: gap en 1 skill (2 niveles)
└─ Product Manager: completo
```

##### 4.5.3 Priorización de Gaps

- Por `strategic_weight` de capability (más peso = más crítico)
- Por `required_level` (gaps en niveles altos = más urgentes)
- Por número de personas afectadas

---

### 5️⃣ **EJEMPLO GUIADO: "Adopción IA Generativa 2026"** (~1500 palabras)

#### 5.1 Setup Inicial

```
Organización: TechCorp
Escenario: Adopción IA Generativa 2026
Horizonte: 18 meses
Roles Involved: Product Manager, AI Engineer, UX Designer
```

#### 5.2 Paso 1: Capabilities Estratégicas

```
├─ "Generative AI & LLMs" (strategic_weight=0.4)
├─ "Data Engineering" (strategic_weight=0.3)
└─ "User Research & Design" (strategic_weight=0.3)
```

#### 5.3 Paso 2: Mapeo Roles → Competencies (LA MATRIZ)

**Tabla de scenario_role_competencies:**

```
Product Manager:
├─ "LLM Product Thinking" - ENRICHMENT (new, level 3)
├─ "Data Literacy" - TRANSFORMATION (level 2→3)
└─ "User Research" - MAINTENANCE (level 3)

AI Engineer:
├─ "Prompt Engineering" - ENRICHMENT (new, level 4)
├─ "LLM Fine-tuning" - ENRICHMENT (new, level 4)
├─ "Python Advanced" - TRANSFORMATION (level 3→4)
└─ "MLOps" - MAINTENANCE (level 4)

UX Designer:
├─ "AI-Powered UX" - ENRICHMENT (new, level 2)
├─ "Prompt Crafting for UX" - ENRICHMENT (new, level 2)
├─ "User Research" - MAINTENANCE (level 4)
└─ "Prototyping" - MAINTENANCE (level 4)
```

#### 5.4 Paso 3: Derivación de Skills

**Para AI Engineer → "Prompt Engineering" (competency nueva):**

```
competency_skills:
├─ "GPT API Usage" - weight=0.3
├─ "Prompt Design Patterns" - weight=0.4
└─ "Evaluation Frameworks" - weight=0.3

Resultado scenario_role_skills:
├─ "GPT API Usage" - required_level=4, source='competency', change_type='enrichment'
├─ "Prompt Design Patterns" - required_level=4, source='competency', change_type='enrichment'
└─ "Evaluation Frameworks" - required_level=4, source='competency', change_type='enrichment'
```

#### 5.5 Análisis IQ

**Current State (Antes):**

```
ScenarioIQ = 45
├─ "Generative AI & LLMs": 0.2 (muy bajo, es nuevo)
├─ "Data Engineering": 0.85
└─ "User Research": 0.75
└─ IQ = (0.2×0.4 + 0.85×0.3 + 0.75×0.3) × 100 = 58.5
```

**Target State (Después de 18 meses):**

```
ScenarioIQ = 85
├─ "Generative AI & LLMs": 0.9 (training plan ejecutado)
├─ "Data Engineering": 0.85
└─ "User Research": 0.75
└─ IQ = (0.9×0.4 + 0.85×0.3 + 0.75×0.3) × 100 = 84.0
```

#### 5.6 Gaps Identificados

```
CRÍTICO (Capability weight 0.4):
├─ AI Engineer: "Prompt Engineering" (3 skills gaps, new)
├─ Product Manager: "LLM Product Thinking" (2 skills gaps, new)
└─ Acción: Bootcamp 8 semanas + mentoring

MODERADO (Capability weight 0.3):
├─ AI Engineer: "Python Advanced" (1→4 = 3 niveles gap)
└─ Acción: Online course 6 semanas + project-based

NO CRITICO:
├─ UX Designer: "AI-Powered UX" (manageable, nivel 2)
└─ Acción: Design workshop 2 semanas
```

---

### 6️⃣ **BUENAS PRÁCTICAS Y DECISIONES DE DISEÑO** (~1000 palabras)

#### 6.1 Por qué Arquitectura Híbrida (Competencies + Skills)

**Problema:** Tener solo skills es demasiado granular; tener solo roles es demasiado rígido

**Solución:** Competencies como "puente"

```
├─ Estratégico piensa en capabilities + competencies (nivel ejecutivo)
├─ Operacional piensa en skills + personas (nivel manager)
└─ Competencies conecta ambos mundos
```

#### 6.2 Ventajas para Escalabilidad

- ✅ Reutilizar competencies entre roles
- ✅ Agregar nuevos roles sin redeseñar skills (heredan competencies)
- ✅ Nuevas capabilities descubiertas en escenarios (discovered_in_scenario_id)
- ✅ Mantener catálogo de skills centralizado (evitar duplicados)

#### 6.3 Ventajas para Mantenibilidad

- ✅ Cambios en competency_skills se propagan automáticamente (RoleSkillDerivationService)
- ✅ Scenarios son "snapshots" aislados (no afecta otros escenarios)
- ✅ Historial de cambios (change_type) permite auditoría
- ✅ Separación de concerns: estrategia (scenario_role_competencies) vs operación (person_role_skills)

#### 6.4 Cómo Extender el Modelo

##### 6.4.1 Nuevo Escenario

```
1. Crear scenarios row
2. Copiar scenario_capabilities del escenario anterior (como template)
3. Crear scenario_roles (nuevos o existentes)
4. Crear scenario_role_competencies (matriz Paso 2)
5. RoleSkillDerivationService auto-genera scenario_role_skills
6. ScenarioAnalyticsService auto-calcula IQ
```

##### 6.4.2 Nueva Capability

```
1. Crear capabilities row (puede estar discovered_in_scenario_id = null, o en un escenario)
2. Agregar competencies que la componen
3. Para cada competency, definir competency_skills
4. En próximos escenarios, asignar a scenario_capabilities si es relevante
```

##### 6.4.3 Nuevo Rol

```
1. Opción A (sin escenario): Crear roles row + role_competencies + role_skills
2. Opción B (en escenario): Crear scenario_roles + scenario_role_competencies
   → RoleSkillDerivationService genera scenario_role_skills automáticamente
```

#### 6.5 Limitaciones y Trade-offs

| Aspecto                   | Decisión                                               | Trade-off                              |
| ------------------------- | ------------------------------------------------------ | -------------------------------------- |
| **Derivación automática** | scenario_role_skills derivada de competencies          | Menos flexible, pero consistente       |
| **Source semántrica**     | source='competency' vs 'manual'                        | Requiere disciplina en datos           |
| **Niveles (1-5)**         | Escalas fijas                                          | No soporta escalas customizadas (aún)  |
| **Change_type**           | Enum: maintenance/transformation/enrichment/extinction | Puede necesitar estados más granulares |

#### 6.6 Patrones de Uso

**Patrón 1: "Scenario Comparison"**

- Crear 2-3 escenarios (base, conservative, aggressive)
- Comparar IQs para entender riesgo
- Priorizar gaps por escenario

**Patrón 2: "Capability Incubation"**

- Nueva capability emerge en escenario (discovered_in_scenario_id ≠ null)
- Evaluar en ese escenario primero
- Si funciona, promover a catalogo global

**Patrón 3: "Role Evolution"**

- Usar evolution_type para describir transformación de rol
- Combinado con scenario_role_competencies change_type
- Proporciona narrativa clara del cambio

---

## 🎯 VALIDAR ANTES DE ESCRIBIR

### Preguntas para Omar:

1. **¿La estructura es correcta?** ¿Faltan secciones? ¿Sobran?

2. **¿El nivel de detalle es apropiado?**
   - ¿Más ejemplos?
   - ¿Menos teoría?
   - ¿Más diagramas?

3. **¿Hay secciones que necesiten más énfasis?**
   - ¿La fórmula de IQ necesita más detalle?
   - ¿Debería haber sección de "Consultas SQL útiles"?
   - ¿Agregar troubleshooting?

4. **¿Está bien el flow narrativo?**
   - ¿Las secciones 3-4 son el core (son las más largas)?
   - ¿La sección 5 (ejemplo) está bien después de 4?

5. **¿Algo que cambies, agregues o elimines?**

### Estimación de escritura (con este outline):

- **6.1 - 6.5:** ~3000 palabras
- **3.1 - 3.12:** ~1500 palabras
- **4.1 - 4.5:** ~2000 palabras
- **5.1 - 5.6:** ~1500 palabras
- **2.1 - 2.3:** ~800 palabras
- **1.1 - 1.3:** ~600 palabras

**Total estimado:** ~9400 palabras (~25 páginas)

**Tiempo de escritura:** ~45-60 minutos

---

✅ **NOMBRES ACTUALIZADOS:** TalentIA → Stratos

¿Validado? ¿Cambios?
