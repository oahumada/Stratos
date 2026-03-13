# 🧠 Especificación Técnica: Motor Scenario IQ (Talent Blueprinting)

## 1. Visión

Scenario IQ transforma la planificación de personal de una hoja de cálculo estática en un **Laboratorio de Simulación Organizacional**. Permite a los líderes crear "Gemelos Digitales" de sus equipos y predecir el impacto de cambios estratégicos antes de que ocurran.

---

## 2. Arquitectura del Motor

El motor opera sobre tres capas de inteligencia:

### A. La Capa de Datos (Digital Twin / Semantic Graph)

- **Knowledge Graph (Neo4j):** Mapea no solo quién reporta a quién, sino quién _influye_ en quién. El `DigitalTwinService` captura este estado permitiendo simulaciones desacopladas con **Zero DB Overhead**.
- **Vector State (Postgres + pgvector):** Almacena el perfil semántico de cada colaborador (DISC, skills, aspiraciones).
- **Cascading Risk Logic:** Identifica dependencias críticas basadas en la topología del grafo (ej: impacto de la salida de un manager en la estabilidad del equipo).

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
| `POST` | `/scenarios/{id}/simulate-merge`      | `AgenticScenarioController@simulateTeamMerge`    | Fusión de departamentos vía Digital Twin        |

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
_Actualizado: 12 de Marzo de 2026_
