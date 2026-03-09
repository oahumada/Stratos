# 📊 Visualización de Impacto y Explicablemente (IA)

## 📌 Overview

Este módulo es el puente entre la inteligencia artificial y el liderazgo organizacional. Su objetivo es evitar que Stratos sea una "caja negra", proporcionando transparencia sobre cómo la IA tomó sus decisiones y cuantificando el retorno financiero de las estrategias sugeridas.

---

## 🖥️ Componente: `AssessmentResults.vue`

Es el dashboard principal de resultados tras una evaluación de talento (BEI o Psicometría).

### 1. 🔍 Capa de Explicabilidad (Human-in-the-Loop)

Para cada rasgo psicométrico detectado, el componente ofrece:

- **Análisis IA:** Una descripción detallada de por qué se asignó ese score.
- **Evidencia STAR:** Una cita textual extraída de la entrevista que respalda el hallazgo.
    - _Ejemplo:_ "S: El proyecto estaba retrasado... A: Tomé el liderazgo de las reuniones diarias... R: Entregamos 2 días antes."
- **Flujo de Razonamiento:** Un stepper que visualiza los pasos cognitivos del agente (ej. "Analizando sentimientos", "Contrastando con KPIs", "Generando diagnóstico final").

### 2. 📡 Dashboard de Impacto Financiero (Individual ROI)

Visualiza el valor monetario del talento evaluado mediante el `RoiCalculatorService`.

- **Costo de Reemplazo:** Estimación del costo de contratar y onboarding un sucesor si la persona actual se retira.
- **Riesgo de Fuga:** Score predictivo de deserción.
- **Ahorro Anual Proyectado:** Cuantifica cuánto dinero ahorra la empresa si implementa las sugerencias de la IA para mitigar el riesgo detectado.

---

## 🧠 Servicios de Soporte

### `RoiCalculatorService.php`

- **`calculateIndividualRoi`**: Toma los datos de retención, sueldo y complejidad del rol para emitir un informe financiero individual.
- **`calculateScenarioRoi`**: Agrega el impacto de múltiples personas en un escenario de cambio organizacional.

### `Stratos360TriangulationService.php`

- **Objetivo:** Cruzar la auto-percepción del empleado (entrevista) con el feedback de terceros (360) y sus KPIs.
- **Output:** Identificación de "Puntos Ciegos" (Blind Spots) y fortalezas ocultas.

---

## 🎨 UI & UX (Diseño Premium)

- **Glassmorphism:** Uso de tarjetas translúcidas con blur de fondo para una estética moderna y futurista.
- **Micro-animaciones:** Transiciones suaves al explorar diferentes rasgos para mantener el foco del usuario.
- **Arquitectura Cognitiva (Radar):** Gráfico interactivo que muestra el balance del "Iceberg Dinámico" del colaborador.

---

## 📊 Valor para el C-Level

1.  **Auditoría:** Los diagnósticos de la IA se vuelven auditables por humanos gracias a la evidencia textual.
2.  **Decisiones basadas en datos:** Convierte "pálpitos" de talento en métricas financieras claras.
3.  **Reducción de Sesgo:** Al contrastar múltiples fuentes de datos, el impacto es una visión 360 real y objetiva.
