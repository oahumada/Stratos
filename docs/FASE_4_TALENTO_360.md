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

- **`assessment_sessions`**: Registro de la sesión de entrevista (tipo, estado, metadatos de potencial).
- **`assessment_messages`**: Registro histórico (log) de la conversación entre el humano y el agente de IA.
- **`psychometric_profiles`**: Resultados estructurados del análisis (Rasgo, Puntaje, Justificación).
- **`assessment_requests`**: Gestión de solicitudes de feedback a terceros (evaluador, sujeto, relación, token).
- **`assessment_feedback`**: Almacenamiento de respuestas cualitativas de los colaboradores.

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

---

## 💻 Interfaz de Usuario (Vue 3 + Vuetify)

### 💬 Chat de Evaluación (`AssessmentChat.vue`)

Componente interactivo que permite a cualquier colaborador realizar su entrevista. Incluye:

- Feedback visual de "pensamiento" de la IA.
- Gestión de estados (Inicio, En progreso, Analizando).
- Scroll automático y diseño premium.

### 👤 Ficha de Talento (`People/Index.vue`)

Integración de una nueva pestaña **"Potencial AI"** que:

- Muestra el chat si no hay evaluación previa.
- Presenta un dashboard de resultados (Radar de rasgos y reporte ejecutivo) si ya fue analizado.

### 📊 Dashboard Talento 360° (`Talento360/Dashboard.vue`)

Vista gerencial (C-Level) que consolida:

- **Índice de Potencial Organizacional**: Promedio global de la compañía.
- **Mapa de Rasgos**: Gráfico de radar con el promedio de rasgos detectados.
- **High Potentials**: Identificación automática de colaboradores con potencial > 80%.
- **Actividad Reciente**: Log de las últimas evaluaciones concluidas.

### 🛡️ Gestión de Feedback (`PendingFeedback.vue`)

Integrado en el Dashboard principal, este componente alerta proactivamente al colaborador sobre solicitudes de feedback pendientes, permitiendo responder preguntas clave en una interfaz limpia y rápida.

---

## ✅ Validación y Calidad

- **Tests Unitarios/Feature**: `tests/Feature/Api/AssessmentApiTest.php` cubre el flujo completo de API, incluyendo el caso de **Análisis 360**.
- **Agnosticismo**: Totalmente compatible con DeepSeek, Abacus o OpenAI configurado vía `.env`.

---

## 🛠️ Cómo Iniciar una Evaluación

1. Navegar a **People**.
2. Seleccionar un colaborador.
3. Ir a la pestaña **Potencial AI**.
4. Hacer clic en **"Comenzar Entrevista"**.
5. Al finalizar, hacer clic en **"Finalizar y Analizar"**.
