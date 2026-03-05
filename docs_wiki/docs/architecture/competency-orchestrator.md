# Competency-First Orchestrator (Scenario Planning)

El proceso de **Orquestación de Competencias** (Step 2 del Scenario Planner) introduce la filosofía *Competency-First* en Stratos. 

En lugar de crear roles inflados o inconexos para afrontar escenarios futuros, Stratos extrae primero las **Competencias Core** requeridas y las contrasta de manera inteligente (utilizando la base de Embeddings Vectoriales) con el Catálogo de Competencias Actual de la empresa. 

Esto previene la fragmentación organizacional y fomenta el *Upskilling* antes de crear posiciones redundantes.

---

## 🏗 Arquitectura de la Orquestación

La lógica de orquestación se consolida en el endpoint `POST /api/scenarios/{id}/step2/orchestrate-capabilities` y abarca tres subprocesos:

### 1. Extracción de Competencias del Escenario (Step 1 -> Step 2)
El Step 1 detalla un escenario futuro usando LLMs. En lugar de sugerir roles de inmediato, extrae **Competencias (Alien Competencies)**. Estas se almacenan provisoriamente enlazadas al `$scenario->id` mediante la tabla pivote `scenario_role_competency`.

### 2. Similitud Vectorial (Cross-Mapping con Embeddings)
El orquestador itera sobre estas "competencias alienígenas" midiendo su distancia vectorial contra el Catálogo de Competencias base usando `EmbeddingService`. 
- **Match > 90% (Impacto Orgánico 🟩):** La competencia descubierta es semánticamente igual a una competencia existente en la empresa. El orquestador identifica qué roles actuales (Pivot/Incumbentes) ya tienen esta competencia, determinando la necesidad de **Upskilling** sin alterar el modelo organizativo.
- **Match < 90% (Alien Competencies 🟨):** La competencia no existe en la empresa. Debe ser adquirida o desarrollada estructuralmente.

### 3. Role Bundling a través de IA
Para las competencias "Alien", crear un rol nuevo por cada competencia generaría un caos estructural (micro-cargos irrelevantes). Para mitigarlo, Stratos invoca a `RoleDesignerService->bundleNewCapabilities($newCompetencies, $candidateRoles)`:
Un agente de IA agrupa estas competencias eficientemente en **Clústers Estructurales**:
- **Enrichment:** Sugiere incrustar las competencias en un Rol Existente (por su similitud de naturaleza).
- **Creation:** Reúne competencias incompatibles con el catálogo actual o demasiado avanzadas y sugiere la creación de un nuevo Rol Integral (y le da un título).

---

## 💻 Flujo de Integración Full-Stack

### API y Endpoints
- **Gateway Endpoint:** `Step2RoleCompetencyController::orchestrateCapabilities` despacha la solicitud principal.
- **Payload Return:**
    ```json
    {
       "summary": { "total": 10, "existing_matched": 4, "new_alien": 6 },
       "organic_impact": [
            { "official_competency": "Data Engineering", "action_required": "upskill" }
       ],
       "ai_orchestration": [
            { "type": "enrichment", "target_role_name": "Analista Senior", "rationale": "Sinergia de datos" },
            { "type": "creation", "target_role_name": "Ingeniero IA", "rationale": "Competencias hiper-nicho alienígenas" }
       ]
    }
    ```

### Integración en el Frontend (Vue 3 + Pinia)
El proceso es manejado del lado del cliente por la vista principal de diseño del Step 2, `IncubatedCubeReview.vue`.

1. **Auto-Trigger:** Al montar el componente, `fetchData()` llama en segundo plano a la acción de Pinia `orchestrateCompetencies()`.
2. **UX en Tiempo Real:** Mientras el IA y los vectores están procesando suposiciones, se renderiza un estado de carga "glassmorphic".
3. **El Panel de Control Visual:** Una vez procesado, el motor renderiza dos *Tarjetas de Impacto*:
   - 🌿 **Balde Verde (Impacto Orgánico):** Advierte sobre los roles pre-existentes que se convertirán en "Vectores de Transformación".
   - 🛸 **Balde Amarillo (Empaquetado IA):** Muestra el plan de Role Bundling (creación vs enriquecimiento), fundamentando las decisiones arquitectónicas propuestas.

Tras interactuar con este panel de Inteligencia Organizativa, el usuario puede descender a visualizar los "Cubes" de manera más precisa y fundamentada, habiendo prevenido contaminación prematura del Catálogo.

---

## 🛠️ Tecnologías Involucradas

- **PostgreSQL / pgvector:** Utilizado vía `EmbeddingService` para búsquedas K-NN de coseno y similitud de texto.
- **OpenAI / LLMs Agent:** `GptService` estructurado con JSON Response format para el método `bundleNewCapabilities`.
- **Vue 3 / Transition Components:** La UI usa `<v-expand-transition>` y `StCardGlass` para un despliegue progresivo, moderno y "wow" del panel de orquestación.
