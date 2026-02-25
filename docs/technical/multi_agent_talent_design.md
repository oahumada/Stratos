# Documentación Técnica: Sistema Multi-Agente para Diseño de Escenarios y Talento (Stratos Agents)

## 1. Visión General

Se ha evolucionado el sistema de generación de escenarios de una arquitectura de "Prompt Monolítico" a un flujo de trabajo orquestado por múltiples **Agentes de IA Especializados**. Este cambio permite separar el diseño estratégico (Capacidades) del diseño organizacional (Roles y Talent Management), logrando mayor precisión y modularidad.

## 2. Flujo de Trabajo (Workflow)

El proceso se divide ahora en dos pasos críticos:

### Paso 1: Planificación Estratégica (Strategic Blueprint)

- **Agente:** `Scenario Planner` (Planificador Estratégico).
- **Misión:** Analizar el contexto del usuario y proponer exclusivamente una arquitectura de **Capacidades**, **Competencias** y **Skills**.
- **Decisión de Diseño:** En este paso se omiten deliberadamente los roles para evitar sesgos de ejecución prematuros.
- **Prompt:** `resources/prompt_instructions/scenario_planner_es.md`.

### Paso 2: Diseño de Talento (Talent Design)

- **Agentes:** `Role Designer` (Diseñador de Roles) y `Competency Curator` (Curador de Competencias).
- **Misión:** Comparar el Blueprint del Paso 1 con el **Catálogo Actual** de la organización para proponer una evolución coherente.
- **Acciones Propuestas:**
    - **Roles:** NEW (Nuevo), EVOLVE (Enriquecimiento), REPLACE (Sustitución).
    - **Catálogo:** ADD, MODIFY, REPLACE de competencias globales.
- **Prompt:** `resources/prompt_instructions/talent_design_orchestration_es.md`.

---

## 3. Componentes de Software

### Backend (Laravel)

#### `TalentDesignOrchestratorService.php`

- **Propósito:** Es el "cerebro" del Paso 2.
- **Lógica:**
    1. Recupera el catálogo actual de roles y competencias de la base de datos (`Roles`, `Competency`).
    2. Formatea el Blueprint generado en el Paso 1.
    3. Construye un contexto rico que incluye la brecha entre "lo que tenemos" y "lo que necesitamos".
    4. Invoca la colaboración entre agentes mediante `AiOrchestratorService`.

#### `AiOrchestratorService.php`

- **Mejora:** Se añadió soporte para `systemPromptOverride`. Esto permite inyectar instrucciones específicas de "misión" (de los archivos .md) a agentes genéricos de la base de datos sin alterar su configuración base.

#### `ScenarioGenerationService.php`

- **Mejora:** Introducción del parámetro `type` en el generador de prompts. Ahora permite alternar entre `planner` (estrategia pura) y `default` (v1 heredada).

#### Rutas de API

- Nuevos endpoints registrados bajo el prefijo `strategic-planning/scenarios/{id}/step2`:
    - `POST /design-talent`: Dispara la orquestación de agentes del Paso 2.

### Frontend (Vue 3 + Vuetify + Pinia)

#### `roleCompetencyStore.ts`

- **Nueva Acción:** `designTalent()`. Maneja la llamada asíncrona a la API de orquestación y expone las propuestas para su renderizado.

#### `RoleCompetencyMatrix.vue`

- **Interfaz:** Se añadió el botón **"Consultar Agentes"** con ícono de robot (`mdi-robot`).
- **Experiencia:** Implementa estados de carga local (`isDesigning`) para dar feedback visual mientras la IA "razona".

#### `AgentProposalsModal.vue` (Nuevo)

- **Función:** Componente dedicado para visualizar las sugerencias de la IA.
- **Características:**
    - Categorización visual por color (Éxito para nuevos, Info para evoluciones, Warning para sustituciones).
    - Explicación de la "Racionalidad" (por qué el agente propone ese cambio).
    - Desglose de la composición de talento (porcentaje humano vs. sintético sugerido).

---

## 4. Estructuras de Datos (JSON Schema)

### Blueprint Estratégico (Paso 1)

```json
{
  "scenario_metadata": { ... },
  "capabilities": [
    {
      "id": "CAP-01",
      "competencies": [
        { "id": "C1", "skills": [...] }
      ]
    }
  ]
}
```

### Propuestas de Talento (Paso 2)

```json
{
  "role_proposals": [
    {
      "type": "NEW|EVOLVE|REPLACE",
      "proposed_name": "...",
      "talent_composition": { "human_percentage": 70, "synthetic_percentage": 30 }
    }
  ],
  "catalog_proposals": [ ... ]
}
```

---

## 5. Próximos Pasos (Roadmap de Implementación)

1.  **Persistencia de Aprobaciones:** Implementar los handlers en `AgentProposalsModal.vue` para que al pulsar "Aprobar", las propuestas se guarden físicamente en las tablas `roles` y `competencies`.
2.  **Mapeo Automático de Brechas:** Utilizar los resultados del Paso 2 para pre-poblar la matriz de Role-Competency Matrix automáticamente.
3.  **Chat en Vivo con los Agentes:** Habilitar un panel lateral en el modal para refinar las propuestas mediante lenguaje natural ("Sugeridme un rol más junior en esta área").
