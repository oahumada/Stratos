# 🧠 Documentación Fase 4: Talento 360° (Gestión del Potencial con IA)

## 📋 Descripción General

La Fase 4 de Stratos introduce la capacidad de evaluar no solo las habilidades actuales de los colaboradores, sino también su **potencial latente y rasgos psicométricos** mediante el uso de Agentes de IA conversacionales y un robusto modelo de feedback multi-fuente. Esta fase cierra el círculo entre la planificación estratégica y la gestión individual del talento.

---

## 🧬 Metodología: Modelo de Evaluación 360° en Stratos

### 1. Introducción

El modelo de evaluación 360° desarrollado para Stratos tiene como objetivo estimar el **Nivel Actual (N)** de dominio de competencias de una persona en su rol, comparándolo con un **Nivel Requerido (R)** para identificar brechas y orientar planes de desarrollo personalizados. Este enfoque integral recoge percepciones desde múltiples fuentes para reducir sesgos y obtener una visión completa del desempeño.

### 2. Fundamentos del Modelo

- **2.1 Enfoque 360°**: La evaluación se realiza desde diversas perspectivas: autoevaluación, jefes, pares y subordinados. Esto permite contrastar percepciones y obtener un diagnóstico más robusto.
- **2.2 Uso de BARS (Behaviorally Anchored Rating Scales)**: Cada competencia se define mediante una escala de 5 niveles basada en comportamientos observables y específicos. Esto facilita que los evaluadores seleccionen el nivel que mejor describe al evaluado, asegurando claridad y objetividad.
- **2.3 Estructura de la Evaluación**: Se utilizan 3-4 preguntas clave por competencia, cada una con 5 opciones que representan los niveles BARS. Las preguntas son consistentes para todos los evaluadores, con ajustes menores según el rol del evaluador. Además, se recogen comentarios y evidencias cualitativas para enriquecer el análisis.

### 3. Cálculo del Nivel Actual (N)

Las respuestas se ponderan según el rol y confiabilidad del evaluador. Se calcula un nivel representativo para cada competencia mediante medianas ponderadas, considerando la evidencia aportada y la calibración de evaluadores. Se analizan también la dispersión y consistencia para detectar posibles sesgos o inconsistencias.

### 4. Identificación de Brechas (Gap)

La brecha se determina comparando el Nivel Actual (N) con el Nivel Requerido (R) definido para el rol. Esta diferencia indica áreas de mejora y sirve para diseñar rutas de desarrollo personalizadas.

### 5. Tres Fuentes de la Verdad en la Evaluación

Para garantizar una evaluación sólida y confiable, Stratos integra tres fuentes de la verdad que se complementan:

1.  **Validación Social**: El cruce y comparación de las evaluaciones 360° desde diferentes roles (auto, jefe, pares, subordinados), identificando consensos y discrepancias.
2.  **Evidencia**: Documentación o ejemplos concretos que respaldan las respuestas, ajustando el peso y confiabilidad de cada evaluación.
3.  **KPIs (Indicadores Clave de Desempeño)**: Datos cuantitativos relacionados con el desempeño real que validan o contrastan la percepción subjetiva.

### 6. Modelo Híbrido de Generación de Preguntas

Para garantizar relevancia y comparabilidad, Stratos utiliza un modelo híbrido para la generación de preguntas:

- Existe un banco maestro de preguntas BARS predefinidas y validadas para cada competencia y arquetipo.
- La IA actúa como consultor senior que adapta estas preguntas al contexto específico del escenario y proceso de negocio.
- Las preguntas generadas por IA son validadas por administradores antes de su uso en evaluaciones, asegurando consistencia y calidad.

### 7. Recomendaciones Prácticas

- Diseñar preguntas que cubran subdimensiones clave de cada competencia y una pregunta global para validación.
- Exigir evidencia para cada respuesta y ajustar pesos según la calidad de la misma.
- Utilizar al menos 3 evaluadores con visibilidad directa para asegurar confiabilidad.
- Aplicar métodos robustos de agregación (mediana ponderada) y análisis de consistencia (ICC, dispersión).
- Implementar calibración periódica de evaluadores para minimizar sesgos.
- Complementar con pruebas objetivas para competencias técnicas críticas.
- Mostrar indicadores de confianza y recomendaciones automáticas para seguimiento.

### 8. Conclusión

El modelo 360° de Stratos ofrece una metodología estructurada, flexible y robusta para evaluar competencias y detectar brechas de manera precisa. Su enfoque híbrido en la generación de preguntas garantiza relevancia contextual sin sacrificar la comparabilidad, facilitando la toma de decisiones informadas para el desarrollo del talento y la planificación estratégica.

---

## 🏗️ Componentes Técnicos

### 1. Modelado de Datos (Laravel)

Se han implementado tres tablas principales para gestionar el ciclo de vida de las evaluaciones:

- **`competency_levels_bars`**: Definiciones de los 5 niveles de comportamiento para cada habilidad (BARS).
- **`skill_question_bank`**: Banco de preguntas maestrías por habilidad y arquetipo.
- **`assessment_sessions`**: Registro de la sesión de entrevista (tipo, estado, metadatos de potencial).
- **`assessment_messages`**: Registro histórico (log) de la conversación entre el humano y el agente de IA.
- **`psychometric_profiles`**: Resultados estructurados del análisis (Rasgo, Puntaje, Justificación).
- **`assessment_requests`**: Gestión de solicitudes de feedback a terceros (evaluador, sujeto, relación, token).
- **`assessment_feedback`**: Almacenamiento de respuestas cualitativas y puntajes BARS (score, evidence, confidence).

**Modelos:**

- `AssessmentSession`, `AssessmentMessage`, `PsychometricProfile`.

### 2. Microservicio de Inteligencia (Python / FastAPI)

Se han añadido dos agentes especializados utilizando **CrewAI** y **DeepSeek**:

- **Expert Psychometric Interviewer**: Conduce la entrevista de forma dinámica, realizando preguntas de seguimiento basadas en las respuestas del usuario para profundizar en rasgos de personalidad.
- **Talent Assessment Analyst**: Procesa la transcripción completa para extraer un perfil JSON con puntajes de 0 a 1 y un reporte sumario.

**Endpoints:**

- `POST /interview/chat`: Genera la siguiente respuesta del entrevistador AI.
- `POST /interview/analyze`: Realiza el cierre y análisis psicométrico binario (Sujeto/IA).
- `POST /interview/analyze-360`: Orquestador de la **Triangulación de la Verdad**, analizando la entrevista vs. feedback externo para detectar discrepancias.

### 3. Servicios e Integración (Laravel)

- **`StratosAssessmentService`**: Orquestador de la comunicación entre PHP y el microservicio de IA. Soporta ahora `analyzeThreeSixty`.
- **`AssessmentController`**: Expone la API para el frontend, gestionando la persistencia de mensajes, la solicitud de feedback a terceros y la sumisión de respuestas.
- **`Talento360Controller`**: Genera métricas agregadas para el dashboard organizacional.

---

## 🔍 Triangulación y Puntos Ciegos (Blind Spots)

El sistema ya no depende únicamente de lo que el colaborador dice. Stratos ahora implementa un modelo de **Triangulación de la Verdad**:

1.  **Auto-percepción**: Obtenida mediante la entrevista psicométrica AI.
2.  **Percepción Externa**: Feedback cualitativo de pares, supervisores y subordinados.
3.  **Análisis de IA**: El agente "Expert Talent Analyst" cruza ambas fuentes para identificar:
    - **Fortalezas Validadas**: Donde ambas percepciones coinciden.
    - **Puntos Ciegos**: Rasgos positivos vistos por otros pero no por el sujeto, o debilidades no reconocidas.
    - **Gaps de Credibilidad**: Discrepancias significativas en el nivel de maestría técnica o conductual.

## 🔮 Metodología BARS (Behaviorally Anchored Rating Scales)

La gestión del feedback se ha profesionalizado mediante un modelo de "Escalas de Comportamiento":

### 1. Estructura de Captura

- **Feedback Estructurado**: Ya no son solo opiniones abiertas. Utilizamos el modelo BARS para calificar habilidades en una escala del 1 al 5, donde cada nivel tiene una descripción conductual precisa.
- **Evidencia Obligatoria**: Para puntajes extremos (1 o 5), el sistema exige un link o justificación de evidencia (URL, Jira, Documento), garantizando objetividad.
- **Nivel de Confianza (Confidence Score)**: Cada evaluador indica qué tan seguro está de su calificación (0-100%).

### 2. Motor de Cálculo (`CompetencyAssessmentService`)

El cálculo del "Nivel Actual" de una competencia no es un promedio simple. Stratos aplica:

- **Ponderación por Rol**: Jefe (40%) > Pares (30%) > Subordinados (20%) > Auto (10%).
- ** Ajuste de Confianza**: Las calificaciones con baja confianza (investigador incierto) pesan menos en el resultado final.
- **Análisis de Dispersión (SD)**: Si la Desviación Estándar entre evaluadores supera `1.5`, el sistema marca la habilidad como **"Requiere Calibración"** (verified = false).

### 3. Integración de KPIs de Negocio (`PerformanceDataService`)

Para aterrizar el "Potencial" a la "Realidad", el sistema inyecta datos duros de desempeño en el análisis de IA:

- **Ventas / Objetivos**: Cumplimiento % trimestral.
- **NPS / Calidad**: Métricas de satisfacción del cliente.
- **Velocidad**: Métricas de entrega de proyectos.

Esta triangulación (Psicometría + Feedback 360° + KPIs) permite detectar:

- **High Potentials Reales**: Alto potencial + Alto desempeño.
- **Underachievers**: Alto potencial + Bajo desempeño (Problema motivacional o de entorno).
- **Overachievers**: Bajo potencial + Alto desempeño (Riesgo de burnout o techo técnico).

---

## 💻 Interfaz de Usuario (Vue 3 + Vuetify)

### 💬 Chat de Evaluación (`AssessmentChat.vue`)

... (contenido previo) ...

### 🛡️ Gestión de Feedback (`PendingFeedback.vue`)

Integrado en el Dashboard principal, este componente alerta proactivamente al colaborador sobre solicitudes de feedback pendientes.

- **Selección Inteligente**: Al abrir un request, el sistema ya pre-seleccionó las preguntas BARS relevantes para las habilidades del sujeto y la relación con el evaluador.
- **`FeedbackFormBARS.vue`**: Componente visual interactivo para calificación conductual.

---

## ✅ Validación y Calidad

- **Tests Unitarios/Feature**: `tests/Feature/Api/AssessmentApiTest.php` valida:
    - Flujo de entrevista.
    - Captura de feedback 360°.
    - **Cálculo automático de niveles de competencia** tras el análisis.
- **Agnosticismo**: Compatible con DeepSeek, Abacus o OpenAI.

---

## 🛠️ Cómo Iniciar una Evaluación

1. Navegar a **People**.
2. Seleccionar un colaborador.
3. Ir a la pestaña **Potencial AI**.
4. Hacer clic en **"Comenzar Entrevista"**.
5. Al finalizar, hacer clic en **"Finalizar y Analizar"**.
6. (Automático) El sistema solicita feedback 360 a pares predefinidos.
7. Al completarse el feedback, se actualiza el perfil de competencias.
