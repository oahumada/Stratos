# Master Stratos Memory – Single Source of Truth

**Última actualización:** 18 Febrero 2026  
**Status Global:**

- Core MVP (Backend/Frontend) ✅ COMPLETADO
- Scenario Planning (Fases 1-7) ✅ COMPLETADO
- Metodología de 7 Pasos Estratégicos ✅ IMPLEMENTADA
- Patrón CRUD: ✅ Consolidado en FormSchemaController

---

## 🎯 RESUMEN EJECUTIVO: SCENARIO PLANNING (7 Pasos)

El módulo de planificación ha culminado su desarrollo integral, permitiendo la orquestación de viabilidad organizacional mediante 7 pasos tácticos:

1.  **Setup y Simulación What-if**: Definición de horizontes e importación de mallas sugeridas por LLM.
2.  **Diseño de Roles y Competencias**: Modelado fino de mallas técnicas y conductuales IA-Ready.
3.  **Contraste con la Realidad**: Matching de plantilla real, detección de brechas FTE y planes de sucesión basados en datos verídicos.
4.  **Estrategias de Cierre 4B + Talento Híbrido**: Definición de acciones Build, Buy, Borrow y Bot (IA). El sistema prioriza agentes sintéticos si el diseño técnico lo indica (`synthetic_leverage` > 50%).
5.  **Planificación y Presupuesto**: Cuantificación financiera paramétrica (Salario Base, Fee Reclutamiento, Costo Formación, Licenciamiento Bot).
6.  **Comparación de Escenarios**: Benchmarking de KPIs (IQ, Inversión, Gaps) entre diferentes versiones del plan.
7.  **Dashboard Ejecutivo y Gobernanza**: Visión consolidada para CHRO/CFO con evaluación de riesgo de ejecución y flujo de aprobación.

---

## 📑 ÍNDICE DE CONTENIDOS

1. [VOL I: ARQUITECTURA TÉCNICA Y CORE](#vol-i-arquitectura-técnica-y-core)
2. [VOL II: MODELO DE DATOS Y DOMINIO](#vol-ii-modelo-de-datos-y-dominio)
3. [VOL III: LÓGICA DE NEGOCIO Y CÁLCULOS](#vol-iii-lógica-de-negocio-y-cálculos)
4. [VOL IV: GUÍA OPERATIVA Y API](#vol-iv-guía-operativa-y-api)
5. [VOL V: BITÁCORA HISTÓRICA](#vol-v-bitácora-histórica)

---

# VOL I: ARQUITECTURA TÉCNICA Y CORE

## 1. Stack Tecnológico

- **Backend**: Laravel 10+ (PHP 8.2), PostgreSQL 17.
- **Frontend**: Vue 3 (Composition API), TypeScript, Vuetify 3.
- **IA/ML**: Soporte para **pgvector** habilitado para búsquedas semánticas.

## 2. Patrón CRUD Consolidado (JSON-Driven)

**Única Fuente de Verdad**: `FormSchemaController` + `form-schema-complete.php`.

- **Flujo**: Registrar modelo en `form-schema-complete.php` -> Crear 4 JSONs de config -> Copiar `Index.vue` genérico.
- **Tiempo**: 15-30 minutos por módulo CRUD completo.

## 3. Decisiones Arquitectónicas (Sello Stratos)

- **Orquestador, no Registrador**: El sistema no es un repositorio pasivo, es un motor de viabilidad.
- **Integridad de Dominio**: Toda feature debe responder a: "¿Cómo esto mejora el IQ Organizacional?".
- **Talento Híbrido**: Los Agentes IA son fuerza laboral de primera clase.

---

# VOL II: MODELO DE DATOS Y DOMINIO

## 1. Entidades Principales

- **Organization**: Multi-tenant nativo vía `organization_id`.
- **People**: Inventario de talento real.
- **Skills**: Taxonomía con niveles 1-5.
- **Roles**: Asociados a competencias vía `role_competencies` (incluye criticidad, estrategia y tipo de cambio).
- **Scenarios**: Entidad maestra de planificación estratégica.
- **Talent Blueprints**: Diseños de roles generados por IA con porcentaje de sintetización.

## 2. Escala de Dominio (1-5)

1. **Básico**: Teórico.
2. **Intermedio**: Con supervisión ocasional.
3. **Avanzado**: Autónomo.
4. **Experto**: Referente interno/Mentor.
5. **Maestro**: Autoridad reconocida/Innovador.

---

# VOL III: LÓGICA DE NEGOCIO Y CÁLCULOS

## 1. Algoritmos de Planificación Financiera (4B)

El sistema cuantifica el impacto financiero basado en supuestos configurables:

- **BUY (Contratar)**: `Salario Anual * (Fee Reclutamiento / 100)`.
- **BUILD (Desarrollar)**: `Costo Unitario de Formación`.
- **BORROW (Tercerizar)**: `(Salario Anual / 12) * 6 meses * (1 + Premium Contingente / 100)`.
- **BOT (IA)**: `$2,000 (Setup) + (Costo Mensual Bot * 12)`.

## 2. Motor de Matching y Gaps

- **Fittedness Score**: Cálculo de match % entre la skill de la persona y el `required_level` del rol.
- **Gap Calculation**: `gap = max(0, required_level - current_level)`.
- **Status de Match**:
    - > 90%: Listo para el rol.
    - 70-90%: Candidato potencial con desarrollo.
    - <50%: No recomendado.

---

# VOL IV: GUÍA OPERATIVA Y API

## 1. Endpoints Estratégicos (prefix `strategic-planning`)

- `POST /api/strategic-planning/scenarios/{id}/simulate-growth` // Simular impacto futuro.
- `GET  /api/strategic-planning/scenarios/{id}/summary` // Resumen ejecutivo consolidado.
- `GET  /api/strategic-planning/scenarios/{id}/compare-versions` // Benchmarking de KPIs.
- `GET  /api/scenarios/{id}/step2/role-forecasts` // FTE Actual vs Futuro.
- `GET  /api/scenarios/{id}/step2/matching-results` // Matching técnico.
- `GET  /api/scenarios/{id}/step2/succession-plans` // Planes de Sucesión.

## 2. Endpoints Core (Legacy MVP)

- `GET /api/dashboard/metrics` // KPIs generales de talento.
- `GET /api/People` // Listado de talento con filtros.
- `POST /api/gap-analysis` // Análisis de brechas puntual Persona/Rol.

---

# VOL V: BITÁCORA HISTÓRICA RECIENTE

### [2026-02-18] Integración Live con DeepSeek ✅

- **Hito**: Estabilización del motor de IA "Live" y arquitectura agnóstica de LLM.
- **Detalle**:
    - **Depuración DeepSeek**: Resuelto error 401 persistente implementando la clase `DeepSeekLLM` en Python, que fuerza el endpoint correcto y evita el desvío automático de CrewAI hacia OpenAI.
    - **Eficiencia de Costos**: Pruebas reales confirman un **92% de eficiencia en Context Caching** (147k tokens cacheados vs 12k nuevos), reduciendo drásticamente el costo operativo.
    - **Agnosticismo**: Creada la `GUIA_LLM_AGNOSTICO.md` y factoría de LLM que permite alternar entre OpenAI, DeepSeek y Abacus vía `.env`.
    - **Soporte Blueprint**: Optimizado el parser de respuestas para manejar objetos `CrewOutput` y asegurar la persistencia de blueprints complejos.

### [2026-02-17] Optimización y Testing Steps 6-7 ✅

- **Hito**: Estabilización completa de tests para _Scenario Planning_ (Fases 6 y 7).
- **Detalle**: Implementado cálculo de `Synthetization Index` (adoptación IA). Refactorización de `CapabilityCompetencyController` para reducir deuda técnica. Corrección de factories y modelos para integridad referencial en PostgreSQL.

### [2026-02-17] Metodología de 7 Pasos Completada ✅

- **Hito**: Lanzamiento de Step 6 (Comparison) y Step 7 (Executive Dashboard).
- **Detalle**: El sistema ya es capaz de benchmarckear planes y ofrecer gobernanza de aprobación final.

### [2026-02-16] Fase 3: Contraste Realidad ✅

- **Hito**: Conexión con inventario real de personas.
- **Detalle**: Implementado motor de matching y planes de sucesión sobre datos vivos.

### [2026-02-14] IA-Ready Role Competencies ✅

- **Hito**: Evolución de la tabla pivote de competencias.
- **Detalle**: Soporte para estrategias 4B por competencia y pgvector habilitado.

---

**Nota Final**: Este documento es la verdad única del sistema. Para detalles de implementación técnica de componentes Vue o controladores específicos, consulte el código fuente siguiendo los patrones documentados en el VOL I.

---

# APÉNDICE: MARCO TEÓRICO Y REFERENCIAS MVP

## 1. Contexto del Producto (Strato)

**Strato** es una plataforma SaaS + consultoría para gestión estratégica de talento basada en skills, IA y orquestación de viabilidad.

- **Orquestador, no Registrador**: Capa de inteligencia por encima de ERPs (Buk/SAP) para dirigir tanto al talento humano como a los Agentes IA.
- **Talento Híbrido**: El sistema trata a los Agentes IA como fuerza laboral de primera clase.

## 2. Reglas de Negocio Core

### 2.1 Escala de Dominio (1-5)

1. **Básico**: Conocimiento teórico, requiere supervisión constante.
2. **Intermedio**: Puede ejecutar tareas con supervisión ocasional.
3. **Avanzado**: Ejecuta de forma autónoma, resuelve problemas complejos.
4. **Experto**: Referente interno, mentorea a otros.
5. **Maestro**: Autoridad reconocida, innova y define estándares.

### 2.2 Algoritmo de Gap Analysis (Fórmula)

`gap = max(0, required_level - current_level)`

- **Match >90%**: "Listo para el rol".
- **Match 70-90%**: "Candidato potencial, requiere desarrollo".
- **Match 50-70%**: "Brecha significativa, ruta de desarrollo larga".
- **Match <50%**: "No recomendado para este rol".

## 3. UI/UX y Sistema de Diseño

- **Framework**: Vuetify 3.
- **Tipografía**: Inter (UI), Roboto Mono (Datos).
- **Colores**:
    - Primary: `#2563eb` (Acciones)
    - Success: `#10b981` (Skills OK)
    - Error: `#ef4444` (Gaps Críticos)
- **Patrón Atómico**: Componetización en `SkillBadge`, `MatchPercentage`, `RoleCard`.

## 4. Datos de Demo (TechCorp)

Para validación y storytelling, el sistema utiliza la empresa **"TechCorp"**:

- **20 Personas** en Engineering, Product y Operations.
- **8 Roles**: Frontend, Backend, DevOps, QA, PM.
- **30 Skills**: Técnicas (React, Node, Python) y Soft (Leadership, Communication).
- **Casos de Éxito**: Ana García (Engineer lista para Senior), María Rodríguez (Backend lista para Tech Lead).

---

_Fin de la Memoria Unificada._
