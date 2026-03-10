# 🧠 Stratos AI Psychometric Evaluation System

## Visión General

El motor de evaluación de talento de Stratos (Cerbero AI) ha sido diseñado como un sistema operativo de Inteligencia Artificial que trasciende los tradicionales test estáticos de opción múltiple. Para evaluar el potencial, la afinidad cultural y las capacidades innatas de un individuo, hemos implementado un sistema interactivo de **Sicometría Inferencial**.

Este documento explica exhaustivamente el diseño técnico y los fundamentos psicológicos de dicha funcionalidad.

## 1. El Proceso Interactivo (Frontend a Backend)

### Interfaz del Usuario (`AssessmentChat`)

El talento ingresa a de **Mi Stratos -> Mi ADN** e inicia una sesión en vivo con un agente conversacional. Este flujo rompe los sesgos de respuesta comunes mediante una interacción de ida y vuelta que simula una entrevista profesional profunda con un equipo humano.

### Motor de Entrevista (Backend AI)

Detrás de escena, un agente autónomo de **CrewAI** configurado con bajas tasas de alucinación (baja _temperature_) y orquestado a través de modelos robustos (preferentemente **DeepSeek / GPT-4o**), conduce la entrevista utilizando metodologías especializadas.

- **Interviewer Agent**:
    - _Rol:_ `Expert Behavioral Event Interviewer (BEI & STAR Specialist)`
    - Su objetivo es evitar respuestas vagas y teóricas. Para ello, insta constantemente al candidato a recordar y detallar incidentes pasados precisos usando el **Método STAR**:
        - **S**ituación (Contexto)
        - **T**area (Desafío o responsabilidad)
        - **A**cción (Lo que _hizo_ exactamente la persona, no el equipo)
        - **R**esultado (Métricas o impacto final)

---

## 2. Modelos Psicométricos Incorporados

La evaluación cruzada aplica tres modelos en la síntesis de respuestas. Esta "red" garantiza que se capturen desde estilos operacionales de comunicación hasta estructuras profundas de la personalidad y adaptabilidad a largo plazo.

### A. Modelo DISC (Comportamiento y Comunicación)

DISC examina el comportamiento observable y el estilo de trabajo.

- **Dominance (Dominancia):** Se focaliza en resultados, desafíos y resolución de problemas.
- **Influence (Influencia):** Orientado a personas, comunicación persuasiva, optimismo.
- **Steadiness (Estabilidad):** Paciencia, confiabilidad, ser buen integrador de equipos.
- **Conscientiousness (Cumplimiento):** Precisión, análisis, respeto a las reglas y calidad.

### B. El "Big 5" o Modelo OCEAN (Personalidad Estructural)

_Dato vital: Recientemente integrado a la malla de evaluación de Stratos._
A diferencia de DISC (que evalúa _cómo reacciona_ alguien), los **Cinco Grandes (Big 5)** evalúan _quién es_ esa persona fundamentalmente.

- **Openness (Apertura a la Experiencia):** Creatividad, preferencia por la novedad sobre la rutina, curiosidad intelectual.
- **Conscientiousness (Responsabilidad):** Organización, autodisciplina, orientación a la planificación y metas.
- **Extraversion (Extraversión):** Sociabilidad, búsqueda de estimulación en interacciones.
- **Agreeableness (Amabilidad):** Empatía, cooperación, altruismo.
- **Neuroticism (Neuroticismo / Estabilidad Emocional):** Control del estrés, reactividad a estímulos negativos, resiliencia emocional.

### C. Learning Agility (Agilidad de Aprendizaje)

Predecir el éxito futuro no solo demanda conocer el presente del candidato. La Agilidad de Aprendizaje es el indicador de su **potencial** a través de:

- Mentalidad de Crecimiento (_Growth Mindset_).
- Disposición a aprender de los errores y aplicarlos a nuevos problemas (Agilidad Mental y Agilidad de Resultados).

---

## 3. Sicometría Inferencial ("Inferential Psychometry")

El corazón del análisis de Stratos no es una regla de puntuación basada en opciones múltiples, sino un enfoque inferencial basado en evidencia (`Inferential Psychometry Expert`).

1. Una vez cerrada la entrevista y cruzada con evaluaciones de pares (`Triangulación 360°`), el sistema toma **la transcripción total**.
2. Los Agentes Analistas mapean patrones conversacionales contra las dimensiones de **DISC**, **OCEAN**, y **Learning Agility**.
3. **Restricción de Alucinación:** Por regla arquitectónica estricta del prompt, el Agente tiene prohibido emitir un rasgo sin citar una parte empírica del texto. Para asignar una puntuación de _Resiliencia_ en el dashboard al candidato, el modelo debe capturar y exponer en su justificación (Rationale/Evidence) exactamente qué historia de resolución de conflicto contó el talento que demuestra dicha competencia.

## 4. Orquestación Multi-Agente Avanzada (Evaluación 360)

Cuando el talento o la empresa activan una revisión profunda que interconecta la evaluación 360 con la psicométrica, la arquitectura es aún más intrincada, invocando tres agentes concurrentes:

1. **Senior Inferential Psychometrist:** Analiza cruces entre lo que dice la persona en la entrevista y los resultados BARS obtenidos de colegas. Procesa el perfil de rasgos combinados DISC + Big 5.
2. **Guardian of Organizational Culture:** Extrae del "CULTURE MANIFESTO" de la organización sus valores. Mide la **afinidad cultural (Cultural Fit)** confrontando dicho manifiesto con los valores mostrados en la entrevista y testimonios.
3. **Strategic Success Predictor (ROI Analyst):** Consolida la brecha entre el rol, el perfil OCEAN/DISC y el Cultural Fit para emitir una probabilidad de éxito y un diagnóstico sinérgico sobre cómo impactará al equipo existente.

_Nota técnica: Puede encontrar toda la implementación de estas estructuras de prompting en el sistema Python central ubicado en `./python_services/app/main.py`._
