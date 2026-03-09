# Arquitectura de Visualización de Impacto - Stratos

## 📌 Resumen

La funcionalidad de **Visualización de Impacto** en Stratos ofrece a los stakeholders una visión multi-capa de cómo las decisiones estratégicas afectan el ecosistema de talento. Abarca desde brechas detalladas de habilidades hasta el ROI financiero de alto nivel y simulaciones "What-If" basadas en agentes.

---

## 🏗️ Capas Arquitectónicas

### 1. Agregación de Datos y Analítica (Backend)

La lógica central reside en varios servicios interconectados:

- **`ScenarioAnalyticsService`**:
    - Calcula el **Scenario IQ** (Índice de Preparación Neural).
    - Mide el cierre de brechas de habilidades y el índice de productividad.
- **`ImpactReportService`**:
    - Genera reportes ejecutivos consolidados.
- **`RoiCalculatorService`**:
    - Calcula métricas financieras (Ahorros por contratación, ROI de capacitación).
    - Computa los **KPIs para el CEO**.
- **`CrisisSimulatorService`**:
    - Realiza simulaciones de **War-Gaming**: Rotación masiva, obsolescencia tecnológica y reestructuración.
- **`NudgeOrchestratorService`**:
    - Genera recomendaciones proactivas (Nudges) basadas en los riesgos detectados.

### 2. Estrategia de API

- `GET /api/reports/scenario/{id}/impact`: Métricas detalladas de impacto por escenario.
- `GET /api/reports/roi`: Análisis de retorno de inversión de talento a nivel organizacional.

### 3. Componentes de Visualización (Frontend)

Stratos utiliza **Vue 3**, **Vuetify** y **ApexCharts** para presentar datos con una estética premium "Neural/Glassmorphic".

#### Módulos Clave:

- **`AssessmentResults.vue`**:
    - Visualiza el flujo de razonamiento de la IA.
    - Muestra la evidencia conductual (STAR).
    - Despliega el impacto financiero individual.
- **Dashboard Final**:
    - **Órbita de Ejecución**: Visualiza la distribución 4B (Build, Buy, Borrow, Bot).
    - **Scenario IQ**: El puntaje definitivo de preparación para el plan propuesto.
- **Radar Stratos**:
    - Interfaz de simulación avanzada para análisis de riesgos en tiempo real.

---

## 🧠 Filosofía de Diseño

1.  **Estética Neural**: Uso de gradientes vibrantes y elementos de cristal (glassmorphism) para transmitir la idea de un "cerebro organizacional" vivo.
2.  **Explicabilidad**: Cada métrica de impacto está respaldada por una narrativa estratégica, asegurando que los líderes entiendan el _porqué_ de los números.
3.  **Proactividad**: El sistema no solo muestra datos, sino que sugiere acciones de mitigación automáticas.

---

## 🚀 Indicadores Clave de Desempeño (KPIs)

- **Scenario IQ**: Porcentaje de alineación con las capacidades futuras requeridas.
- **Capa Táctica (4B)**: Inversión total requerida para ejecutar el escenario.
- **Índice de Sintetización**: Grado de adopción de IA/Automatización dentro del plan.
- **Probabilidad de Éxito**: Verosimilitad de cumplir los objetivos horizon dado las restricciones actuales.
