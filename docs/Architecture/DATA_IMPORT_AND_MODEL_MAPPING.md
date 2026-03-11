# 🗄️ Proceso de Importación y Mapeo de Datos (LLM a DB)

Este documento describe el proceso técnico mediante el cual Stratos transforma un objeto JSON masivo generado por una IA (Cerebro/Abacus) en una estructura relacional coherente dentro de la base de datos PostgreSQL.

---

## 🏗️ Flujo General

1.  **Recepción**: El `GenerateScenarioFromLLMJob` recibe la respuesta completa del LLM tras el razonamiento de los agentes.
2.  **Persistencia Inicial**: La respuesta cruda se guarda en `scenario_generations.llm_response`.
3.  **Activación del "Datafier"**: Se invoca a `ScenarioGenerationService@finalizeScenarioImport`.
4.  **Transaccionalidad**: Todo el proceso ocurre dentro de una `DB::transaction` para asegurar la integridad referencial.

---

## 🛠️ Lógica de Mapeo (Entidad por Entidad)

El sistema sigue una jerarquía descendente (`Escenario` → `Capacidad` → `Competencia` → `Habilidad`) utilizando una estrategia de **Deduplicación Inteligente**.

### 1. El Escenario (Scenario)

Si el escenario aún no existe (es una generación nueva), se crea en la tabla `scenarios`.

- **Source Link**: Se vincula mediante `source_generation_id` para trazabilidad hacia el prompt original.
- **Identidad**: Se asigna un `owner_user_id` y `organization_id` para aislamiento multi-tenant.

### 2. Capacidades (Capabilities)

El sistema recorre el array `capabilities` del JSON.

- **updateOrCreate**: Se busca una capacidad existente por `organization_id` + `name`.
- **Status**: Si la capacidad es nueva (descubierta por la IA), se marca como `in_incubation`.
- **Pivot**: Se vincula al escenario en la tabla `scenario_capability` con el rol estratégico de "Target".

### 3. Competencias (Competencies)

Dentro de cada capacidad, se procesan las competencias.

- **Mapeo Relacional**: Se crea/actualiza la competencia y se vincula a la capacidad superior.
- **Aislamiento**: Se vuelve a validar el `organization_id` para evitar fugas de datos entre clientes.
- **Status**: Se marca como `in_incubation` si no existía en el catálogo global de la empresa.

### 4. Habilidades (Skills)

La unidad mínima de la arquitectura.

- **Normalización**: El sistema acepta tanto strings simples como objetos complejos de habilidades.
- **Deduplicación**: Se busca por nombre dentro de la organización. Si ya existe una habilidad llamada "Python Avanzado", se reutiliza el ID existente en lugar de crear uno nuevo.
- **Categorización**: Se hereda el nombre de la `Capability` padre como categoría por defecto.

---

## 🛡️ Reglas de Oro de la Importación

1.  **Multi-tenancy Estricto**: Todas las consultas y creaciones incluyen obligatoriamente el `organization_id`. Jamás se cruzan datos entre organizaciones.
2.  **Idempotencia**: Si se ejecuta el proceso de importación dos veces sobre el mismo JSON, el estado de la base de datos no cambia (gracias a `updateOrCreate`).
3.  **Preservación de ID del LLM**: Se guarda el `llm_id` original (ej: "CAP-01") en la base de datos para facilitar la depuración y el rastreo de chunks en tiempo real.
4.  **Incubación**: Nada de lo que la IA genera entra directamente al "Catálogo Maestro" como verificado. Todo queda en estado "Incubación" hasta que un líder humano lo valida.

---

## 🧠 Inteligencia Vectorial (Embeddings)

Al finalizar la inserción de cada nodo (Capability/Competency/Skill), el sistema dispara un evento asíncrono (si está configurado) para generar el vector semántico:

1.  Se envía el texto al modelo `text-embedding-3-small`.
2.  El vector se guarda en la columna `embedding` (PostgreSQL `vector`).
3.  Esto permite que el gráfico de conocimiento pueda encontrar relaciones semánticas ("¿Qué tan parecida es esta nueva capacidad IA a lo que ya tenemos?").

---

**"Convertimos alucinaciones estructuradas en activos organizacionales reales."**
_Actualizado: 11 de Marzo de 2026_
