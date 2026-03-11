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
- **Trazabilidad de Supuestos (Assumptions)**: Se guarda el objeto original generado en el paso 1 (industria, objetivos, horizontes, presupuestos) en la relación `sourceGeneration.prompt_payload`. Esto alimenta el **ScenarioAssumptionsCard** en la UI de forma inmutable.

### 1.5 Protocolo de Bloqueo (Read-Only Mode)

Una vez que un escenario avanza a estado `approved`, `completed` o `active`, la arquitectura neural entra en modo estricto de solo lectura:

- **Capa UI**: Todos los componentes interactivos (nodos, sliders, text fields) en `NodeEditModal` se bloquean mediante la directiva `isReadonly`.
- **Feedback Visual**: Se despliega el banner de bloqueo (`mdi-lock-outline`) alertando al usuario sobre la integridad del diseño.

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
# 🧠 Especificación Técnica: Motor Scenario IQ (Talent Blueprinting)

## 1. Visión

Scenario IQ transforma la planificación de personal de una hoja de cálculo estática en un **Laboratorio de Simulación Organizacional**. Permite a los líderes crear "Gemelos Digitales" de sus equipos y predecir el impacto de cambios estratégicos antes de que ocurran.

---

## 2. Arquitectura del Motor

El motor opera sobre tres capas de inteligencia:

### A. La Capa de Datos (Digital Twin)

- **Knowledge Graph (Neo4j):** Mapea no solo quién reporta a quién, sino quién _influye_ en quién, las dependencias técnicas y el flujo real de conocimientos.
- **Vector State (Postgres + pgvector):** Almacena el perfil semántico de cada colaborador (DISC, skills, aspiraciones).

### B. El Enjambre de Simulación (Scenario Crew)

Utilizamos un crew de agentes especializados en CrewAI:

1.  **El Simulador Orgánico:** Ejecuta el cambio solicitado (ej: "Mover a 3 Senior Devs a un nuevo Squad").
2.  **El Analista de Riesgos de Continuidad:** Identifica qué procesos críticos se rompen al mover a esos expertos (pérdida de 'Tribal Knowledge').
3.  **El Predictor de Sinergia:** Simula la interacción de los perfiles DISC en el nuevo equipo y predice la probabilidad de fricción o aceleración.
4.  **El Guardián de Cultura:** Evalúa si el cambio diluye o refuerza los valores del Manifiesto Stratos en ese micro-entorno.

### C. La Capa de Salida (Blueprint)

- **Success Probability Score:** Probabilidad de que el nuevo escenario logre sus objetivos.
- **Time to Peak Performance:** Estimación de cuánto tardará el equipo en ser 100% productivo tras el cambio.
- **Risk/Opportunity Matrix:** Visualización de áreas críticas vs. ganancias de eficiencia.
- **Cultural Friction Index:** Porcentaje de fricción cultural proyectada por el cambio.
- **Synergy Score:** Puntuación de sinergia estimada del nuevo equipo (0-10).

---

## 3. Flujo de Trabajo (The Loop)

1.  **Drafting:** El usuario define el cambio (Headcount, Reorg, Automatización).
2.  **Hydration:** El sistema extrae el contexto completo del Grafo de Conocimiento.
3.  **Agentic Simulation:**
    - El Crew analiza el escenario durante 60-120 segundos.
    - Se realizan simulaciones Monte Carlo de "Probabilidad de Éxito" cruzando DISC y Skills.
4.  **Visualization:** El usuario interactúa con el panel `ScenarioSimulationStatus` que muestra KPIs en tiempo real (Probabilidad de Éxito, Sinergia, Fricción Cultural, Tiempo al Pico).
5.  **Auto-Remediación:** Si la fricción es alta, el botón "Generar Plan de Remediación" invoca al **Stratos Sentinel** que genera acciones concretas, capacitaciones y validación ética.
6.  **Commitment:** El escenario aceptado se convierte en el "Target Blueprint" para los módulos de Selección y Learning Paths.

---

## 4. API de Simulación y Remediación

### Endpoints Implementados

| Método | Ruta                                  | Controlador                                      | Descripción                                     |
| :----- | :------------------------------------ | :----------------------------------------------- | :---------------------------------------------- |
| `POST` | `/scenarios/{id}/simulate-growth`     | `ScenarioSimulationController@simulateGrowth`    | Simula crecimiento de talento y proyecta gaps   |
| `POST` | `/scenarios/{id}/crisis/attrition`    | `ScenarioIQController@simulateAttrition`         | Simulación de retiro masivo (War-gaming)        |
| `POST` | `/scenarios/{id}/crisis/obsolescence` | `ScenarioIQController@simulateObsolescence`      | Simulación de obsolescencia de habilidades      |
| `GET`  | `/api/career-paths/{peopleId}`        | `ScenarioIQController@getCareerPaths`            | Cálculo de rutas óptimas sobre Neo4j            |
| `POST` | `/scenarios/{id}/mitigate`            | `ScenarioSimulationController@getMitigationPlan` | Genera plan de mitigación agéntica vía Sentinel |

### Servicios de Inteligencia

- **`CrisisSimulatorService`**: Engine para C2 (Attrition, Skill Obsolescence, Restructuring).
- **`CareerPathService`**: Engine para C3 (Pathfinding, Stepping Stones, Mobility Index).
- **`AgenticScenarioService`**: Orquestador de simulaciones "What-If" complejas.

- Recibe las métricas de la simulación (fricción, sinergia, probabilidad).
- Invoca al agente **Stratos Sentinel** para generar un plan de remediación en formato JSON.
- Cada plan se registra en el **Audit Trail** para trazabilidad completa.

---

## 5. Diferenciadores Estratégicos (El Factor Unicornio)

| Característica    | Competencia Tradicional    | Scenario IQ (Stratos)               |
| :---------------- | :------------------------- | :---------------------------------- |
| **Base de Datos** | Tabla de Excel / Org Chart | Knowledge Graph Asociativo          |
| **Análisis**      | Manual / Lineal            | Agente Multi-Varianza (IA)          |
| **Factor Humano** | Solo "Skills" técnicos     | Perfil DISC + Fit Cultural Dinámico |
| **Predicción**    | "Olfato" del líder         | Probabilidad Matemática de Éxito    |
| **Acción**        | Desconectada               | Auto-Remediación con Plan IA        |
| **Transparencia** | Nula                       | Audit Trail por Stratos Sentinel    |

---

## 6. Componentes Frontend

- **`ScenarioSimulationStatus.vue`**: Panel flotante con métricas KPI en tiempo real y botón de remediación.
- **`BrainCanvas.vue`**: Visualización D3.js del grafo de capacidades con nodos y conexiones.
- **`ScenarioDetail.vue`**: Vista detallada con stepper de navegación y versiones históricas.

---

## 7. Roadmap de Implementación (Scenario IQ)

### Fase 1: Motor de Simulación ✅ (Completada — Feb 2026)

- Simulación de crecimiento de talento y gaps por área de capacidad.
- Engine de Crisis (Attrition/Obsolescence) implementado.
- Career Pathfinding sobre Neo4j funcional.
- Auto-Remediación vía Stratos Sentinel integrada.

### Fase 2: Integración Neo4j Live ✅ (Completada — Marzo 2026)

- El Simulador Orgánico y CareerPathService consultan el Grafo de Conocimiento.
- Mapeo de movilidad organizacional generado dinámicamente.

### Fase 3: Laboratorio de ROI (Q3 2026)

- Cálculo de impacto financiero de los escenarios.
- Integración con el módulo de Selección para "llenar vacantes virtuales" del escenario.

---

**"En Stratos, el futuro no se adivina, se diseña."**
_© 2026 Stratos Intelligence Architecture Group_
_Actualizado: 27 de Febrero de 2026_
# 🧠 Guía de Demostración: Orquestación del Cerebro Stratos

Esta guía está diseñada para que un **Revisor Beta** o **Socio Potencial** pueda validar la inteligencia y capacidad de orquestación de Stratos de manera autónoma, replicable y transparente.

---

## 🎭 Escenario Maestro: "IA-First Transformation"

**Contexto:** La empresa "Global Fintech" necesita transformar su departamento de IT tradicional en una unidad impulsada por IA. No saben qué roles necesitan ni cómo equilibrar humanos frente a agentes autónomos.

### 📋 Ficha Técnica del Caso de Uso

- **Prompt Sugerido:** _"Necesitamos escalar nuestro departamento de ingeniería para soportar el desarrollo de 10 nuevos productos basados en LLM. Queremos un enfoque agresivo en talento sintético (IA) para tareas de monitoreo y soporte, pero manteniendo el liderazgo estratégico humano."_
- **Agentes Involucrados:** `Simulador Orgánico`, `Diseñador de Roles`, `Stratos Sentinel`.

---

## 🚀 Paso a Paso de la Verificación

### Paso 1: Incepción (El Disparador)

1.  Navegar a **Scenario Planning > Nuevo Escenario**.
2.  Ingresar el nombre: `Transformación IA 2026`.
3.  En el campo de descripción/instrucciones, pegar el **Prompt Sugerido** (ver arriba).
4.  **Click en: [Generar con IA (Cerebro)]**.
5.  **Qué observar:**
    - Fíjate en el widget de **Stratos Sentinel** (salud del sistema). Debería mostrar actividad.
    - Si hay una consola de logs activa, observar cómo los agentes "discuten" el plan.

### Paso 2: Observación de la Orquestación (Backstage)

Mientras el sistema procesa (30-60 seg), explica al tester:

- _"En este momento, el **Simulador Orgánico** está traduciendo lenguaje natural a un grafo de competencias."_
- _"El **Diseñador de Roles** aplica la metodología de 'Cubo de Roles' para decidir niveles de maestría (Y) y arquetipos (X)."_
- _"**Sentinel** está auditando en tiempo real que el diseño no tenga sesgos de género o exclusión técnica."_

### Paso 3: Análisis de Resultados (La Prueba de Verdad)

Una vez terminado, el tester debe validar en la pantalla de **Scenario Detail**:

1.  **Fundamentos Inmutables**: Arriba de todo, revisar el **ScenarioAssumptionsCard**. Debería estar visible con la etiqueta `RO - Prototype Only` mostrando el contexto inicial (Industria, Tamaño, Retos, etc.) que se inyectó al LLM ahora de forma "bloqueada".
2.  **Protocolo Read-Only**: Al hacer clic en el nombre de una Capacidad o Competencia en el mapa ("Target"), se abrirá el modal de edición (`NodeEditModal`) pero todos los campos y sliders estarán intencionalmente desactivados si el escenario ya está activo.
3.  **Mapa de Capacidades**: ¿Se crearon nodos de "Large Language Models", "Ciberseguridad IA", etc.?
4.  **Métrica de Sintetización**: Ver el porcentaje de **IA vs Humano**.
    - _Verificación:_ ¿Es mayor el % sintético en roles operativos? (Ej. Support Engineer IA: 70% sintético).
5.  **Audit Trail**: Click en el icono de **Stratos Sentinel**. Debería aparecer un registro: _"Diseño verificado: Alineado con Manifiesto de Transparencia"_.

---

## 🛠️ Verificación de "Poder de Razonamiento" (Rainy Day)

Pide al tester que intente "romper" la lógica con un escenario contradictorio:

1.  **Nuevo Escenario:** `Escenario Imposible`.
2.  **Prompt:** _"Quiero un equipo de 50 Arquitectos Senior (Nivel Y5) con 0% de presupuesto y entrega en 1 semana."_
3.  **Resultado Esperado:** El Cerebro **NO** debe aceptar el plan sin advertencias. Debe generar un **Análisis de Riesgo Crítico** indicando: _"Inviabilidad financiera detectada"_ o _"Déficit de confianza en el timeline"_.
    - _Valor:_ Demuestra que no es un generador de texto, sino un motor de consultoría real.

---

## 📊 Criterios de Aceptación para el Socio

- [ ] **Latencia:** La respuesta llega en menos de 90 segundos.
- [ ] **Coherencia:** Los nombres de los roles suenan profesionales y modernos.
- [ ] **Explicabilidad:** El usuario entiende POR QUÉ se sugirió cada rol (justificación lógica).
- [ ] **Consistencia:** Si repito el mismo prompt, el diseño central se mantiene estable.

---

> [!TIP]
> **Pro-Tip para la Demo:** Muestra la base de datos Neo4j (si es posible) para que el socio vea cómo los nuevos roles se conectaron automáticamente a las competencias del catálogo global. Esto demuestra **Escalabilidad**.

### 1. El Wizard como "Alimentador de Contexto"

El Wizard de 5 pasos (GenerateWizard.vue) ya no intenta reemplazar al agente, sino que actúa como su fuente de datos estructurados.

Antes: El usuario escribía un bloque de texto libre.
Ahora: El Wizard recolecta industria, desafíos, capacidades, objetivos estratégicos, etc.
Integración: Estos campos se envían al backend (ScenarioGenerationController), donde el ScenarioGenerationService los ensambla usando plantillas Markdown dinámicas.

### 2. Los Agentes (Cerebro) como "Motor de Ejecución"

Una vez que el prompt está completo (Wizard + Plantilla), el sistema decide quién lo procesa:

- Si la configuración usa el proveedor intel (Cerebro), ese prompt gigante se envía al servicio de Python.
- Allí, el agente "Strategic Talent Architect" (definido en main.py) recibe esas instrucciones. Al ser un agente con "backstory" y "goals", no solo sigue el prompt, sino que aplica su "razonamiento" para asegurar que el JSON resultante sea coherente con un modelo de talento organizacional.

### 3. ¿Por qué es mejor así?

- Visibilidad (Glass Box): Al usar el Wizard, los agentes tienen datos de mejor calidad, lo que reduce las "alucinaciones" y permite que en la demo podamos mostrar cómo el agente tomó cada decisión basándose en los datos específicos que el usuario ingresó en cada paso.
- Fallback: Si por alguna razón el servicio de agentes (Python) está caído, el sistema puede redirigir ese mismo prompt enriquecido por el Wizard a un LLM directo (Abacus/OpenAI), manteniendo la funcionalidad básica.

---

## 🔍 Verificación Post-Simulación (Glass Box)

Para asegurar que el "Cerebro" ha razonado correctamente, podemos verificar los estados internos:

1.  **Validación de JSON**: Confirmar que el output de los agentes no se trunca (ajustado a 4096 tokens) y que el bloque markdown se limpia automáticamente.
2.  **Métricas RAGAS**: El sistema evalúa automáticamente la calidad del blueprint generado. Un Score > 0.8 en la base de datos indica alta fidelidad técnica.
3.  **Logs de Orquestación**: Verificar en `laravel.log` que el `Strategic Talent Architect` ha completado todas las secciones del contrato de datos.

## 🛠️ Solución de Problemas Comunes (Fixes Aplicados)

- **Truncado de Output**: Se incrementó `max_tokens` en el servicio Python para manejar blueprints complejos con múltiples roles y habilidades.
- **Parsing Errors**: Se implementó una lógica de limpieza de bloques ```json para asegurar compatibilidad con la base de datos.
- **Timeouts**: Se extendió el timeout de la comunicación Laravel <-> Python a 300 segundos.

### 📚 Recursos Adicionales

- **Mapeo de Datos (LLM a DB):** [DATA_IMPORT_AND_MODEL_MAPPING.md](file:///home/omar/Stratos/docs/Architecture/DATA_IMPORT_AND_MODEL_MAPPING.md)
- **Especificación Scenario IQ:** [SCENARIO_IQ_TECHNICAL_SPEC.md](file:///home/omar/Stratos/docs/Architecture/SCENARIO_IQ_TECHNICAL_SPEC.md)
