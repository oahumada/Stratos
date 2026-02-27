# 💎 Ecosistema de Evaluación de Potencial AI & 360° (Cerbero)

Esta documentación detalla la arquitectura, el flujo de datos y la inteligencia detrás del módulo de **Evaluación de Potencial** de Stratos, una pieza fundamental de la Ingeniería de Talento de la plataforma.

---

## 1. Visión General

A diferencia de las evaluaciones tradicionales que son estáticas y retrospectivas, Stratos utiliza **IA Generativa y Razonamiento Multivariante** para predecir la capacidad futura de un colaborador. El sistema combina autopercepción, feedback social (360°) y datos de rendimiento real (KPIs).

## 2. Componentes del Ecosistema

### A. Stratos AI Interviewer (El Agente)

Es el frontend de la evaluación. Utiliza el agente **"Especialista en Psicometría DISC & Learning Agility"**.

- **Tecnología:** Impulsado por el `StratosAssessmentService` conectado al microservicio de Python Intel.
- **Metodología:** El agente utiliza marcos de **DISC (Dominancia, Influencia, Estabilidad, Cumplimiento)** y los 4 pilares de **Learning Agility (Mental, People, Change, Results)**.
- **Dinamismo:** El agente no sigue un guion; genera preguntas situacionales basadas en el historial del chat para identificar el perfil conductual y el potencial de aprendizaje del colaborador.

### B. El Mapa de Relaciones "Cerbero"

El motor 360° identifica automáticamente las relaciones de la persona evaluada:

- **Managers:** Perspectiva de alineación estratégica.
- **Peers (Pares):** Perspectiva de colaboración y cultura.
- **Subordinates:** Perspectiva de liderazgo y clima.

### C. El Banco de Preguntas Inteligente (BARS)

Para el feedback externo, el sistema utiliza `SkillQuestionBank`:

- Selecciona preguntas específicas basadas en las habilidades activas de la persona.
- Adapta el lenguaje según la relación (ej. una pregunta sobre "Liderazgo" se redacta distinto para un jefe que para un subordinado).

---

## 3. Arquitectura Técnica y Flujo de Datos

### I. Inicio e Interacción (`AssessmentController@startSession`)

1. Se crea una `AssessmentSession` vinculada a una organización y persona.
2. El colaborador chatea con la IA. Los mensajes se guardan en `AssessmentMessage`.

### II. Disparo del Ciclo 360 (`AssessmentController@triggerThreeSixty`)

1. El sistema identifica managers, pares y subordinados.
2. Genera un `AssessmentRequest` único para cada evaluador externo con un **Token de Acceso Seguro**.
3. Se envían notificaciones para que los terceros califiquen evidencias y otorguen puntajes de confianza.

### III. El Motor de Análisis (`StratosAssessmentService@analyzeThreeSixty`)

Cuando el usuario pulsa "Finalizar y Analizar", ocurre la magia:

1. **Recolección:** Se agrupan los mensajes del chat, el feedback de todos los externos y los KPIs (del `PerformanceDataService`).
2. **Razonamiento IA:** Se envía este "Mega-Contexto" al microservicio de Python.
3. **Triangulación:** La IA busca inconsistencias (ej. la persona dice ser gran comunicadora, pero sus pares le dan puntaje bajo).

### IV. Resultados y Persistencia

Los resultados se desglosan en:

- **`PsychometricProfile`**: Registra cada rasgo (`trait`) con su puntaje (1-100) y el razonamiento del agente (`rationale`).
- **Predictive Analytics**: Se guarda el `success_probability` (probabilidad de éxito en el rol), el `overall_potential` y el `team_synergy_preview` (análisis de encaje en equipo).
- **Metadata de Sesión**: Se guardan los `blind_spots` (puntos ciegos), la `cultural_analysis` y el flujo de razonamiento de la IA.

---

## 4. Robustez y Performance

Dada la complejidad del análisis, se han implementado medidas de alta disponibilidad:

- **Timeouts Extendidos:** El análisis 360° tiene una ventana de **180 segundos** para permitir el razonamiento profundo de modelos de lenguaje grandes (LLMs).
- **Graceful Error Handling:** Si algún rasgo no puede ser calculado, el sistema guarda el resto de la evaluación evitando errores 500 y permitiendo la visualización parcial de resultados.

## 5. Integración con el Ecosistema Stratos

La joya no termina en el análisis. El `PotentialScore` y los `Blind Spots` se inyectan automáticamente en:

1. **Talent Intelligence Dashboard:** Para comparativas de HiPos (High Potentials).
2. **Learning Path Generator:** Para que la ruta de aprendizaje no sea genérica, sino que se enfoque en corregir los puntos ciegos detectados.

---

_Documento de Ingeniería de Talento - Stratos v2.1_
_Actualizado: 26 de Febrero de 2026_
