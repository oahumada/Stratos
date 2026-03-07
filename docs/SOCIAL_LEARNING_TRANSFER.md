# Social Learning & Knowledge Transfer Engine

## 🎯 Objetivo Estratégico

El módulo de **Social Learning** de Stratos tiene como misión mitigar el riesgo de pérdida de capital intelectual mediante la orquestación proactiva de la transferencia de conocimiento. No es solo un sistema de mentoría; es un agente de resiliencia organizacional que identifica silos críticos y fuerza la polinización cruzada de capacidades.

---

## 🏗️ Arquitectura del Módulo

### 1. El Cerebro: `SocialLearningService.php`

Encapsula la inteligencia de negocio y la integración con modelos de IA.

- **Detección de Silos**: Cruza datos de `RetentionDeepPredictorService` con la criticidad de las skills (`is_critical`). Identifica a expertos en riesgo de fuga que poseen conocimiento único.
- **Algoritmo de Matching**:
    - Prioriza la **Mentoría Cross-Departmental** (punto extra por polinización cruzada).
    - Evalúa el **Gap de Nivel** (Mentor debe estar al menos 1 nivel por encima del Mentee).
    - Considera el **HiPo Status** (mentores de alto potencial).
- **IA Blueprint Execution**: Utiliza el `AiOrchestratorService` para diseñar planes de aprendizaje estructurados en tiempo real.

### 2. Capa API: `SocialLearningController.php`

Expone las capacidades al frontend mediante endpoints REST:

- `GET /api/social-learning/dashboard`: Retorna el estado global de continuidad (Global Market Cross-Pollination) y la lista de silos detectados.
- `GET /api/social-learning/matches/{skillId}`: Sugerencias dinámicas de mentees para una skill específica.
- `POST /api/social-learning/generate-blueprint`: Dispara la generación del plan de mentoría por IA.

### 3. Interfaz: `SocialLearning.vue`

Diseñada bajo el estándar **Stratos Glass Design System**.

- **Visualización de Riesgo**: Tarjetas con gradientes de riesgo y avatares dinámicos.
- **Preview de Blueprint**: Un panel interactivo que muestra los hitos semanales generados por la IA.
- **Acceso Proactivo**: Integrado en el sidebar bajo la sección de "People Experience".

---

## 🛠️ Flujo de Operación

1. **Escaneo de Continuidad**: El sistema analiza periódicamente a los colaboradores con skills críticas.
2. **Identificación de Silos**: Si un experto tiene un `Flight Risk > 60%`, se marca como un silo en riesgo.
3. **Orquestación de Transferencia**: El administrador de HR recibe sugerencias de sucesores potenciales.
4. **Generación de Blueprint**: La IA diseña un plan de 4 semanas adaptado al perfil del mentor y aprendiz.
5. **Ejecución Viral**: Se inicia la transferencia de conocimiento, reduciendo el impacto de una eventual salida.

---

## 📈 Métricas de Impacto (KPIs)

- **Cross-Pollination Index**: Porcentaje de skills críticas que están distribuidas en más de un departamento.
- **Knowledge Redundancy Score**: Métrica que indica cuántas personas pueden cubrir una posición clave en caso de baja.
- **Social Learning Velocity**: Tiempo promedio desde la detección de un riesgo hasta la creación de un plan de transferencia.

---

## 🔗 Integraciones

- **Stratos Map**: Los riesgos de continuidad se visualizan como "Fires" o puntos críticos en el mapa de calor organizacional.
- **Retention IA**: El motor de predicción de rotación alimenta la prioridad de los planes de aprendizaje.
- **Learning Paths**: Los blueprints generados pueden alimentarse directamente al sistema de desarrollo individual (`DevelopmentPath`).

---

> [!IMPORTANT]
> Este módulo es fundamental para la **Sostenibilidad del Talento**. Un colaborador que enseña no solo transfiere conocimiento, sino que aumenta su propio engagement y sentido de pertenencia.
