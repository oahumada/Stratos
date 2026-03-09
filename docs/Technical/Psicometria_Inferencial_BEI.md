# 🧠 Motores de Inteligencia: BEI y Psicometría Inferencial

## 📌 Resumen

Stratos ha evolucionado sus capacidades de evaluación mediante la implementación de dos motores cognitivos avanzados:

1.  **Chatbot BEI (Behavioral Event Interview):** Un entrevistador especializado que utiliza el método STAR para recolectar evidencia conductual de alta fidelidad.
2.  **Psicometría Inferencial:** Un motor de análisis que proporciona evidencia explícita por cada rasgo diagnosticado, permitiendo una trazabilidad total del diagnóstico.

---

## 🤖 Chatbot BEI (Entrevista por Incidentes Críticos)

A diferencia de los chatbots estándar, el Chatbot BEI de Stratos está diseñado para descubrir evidencia específica y real del pasado profesional del colaborador.

### 🔑 Características Clave

- **Metodología STAR:** El agente indaga por Situación, Tarea, Acción y Resultado.
- **Foco en Incidentes Críticos:** Ignora respuestas genéricas y aspiracionales, solicitando ejemplos específicos donde el rasgo fue puesto a prueba.
- **Indagación Dinámica:** Si el usuario entrega una respuesta vaga, la IA detecta qué componente de la metodología STAR falta y realiza una pregunta de seguimiento dirigida.

### 🧩 El Método STAR en Stratos

El método STAR es el eje central de la lógica de interrogación. Fuerza al sujeto a proporcionar una narrativa estructurada que Stratos deconstruye en puntos de datos:

1.  **S - Situación:** La IA identifica el contexto. _¿Cuál fue el desafío específico?_
2.  **T - Tarea:** La IA confirma el rol. _¿Cuál fue tu responsabilidad exacta?_
3.  **A - Acción:** La fase más crítica. _¿Qué pasos ESPECÍFICOS tomaste?_ (La IA filtra "Nosotros" vs "Yo" para asegurar la rendición de cuentas individual).
4.  **R - Resultado:** El desenlace. _¿Qué pasó tras tu intervención?_

### 🔌 Implementación Técnica

- **Rol del Agente:** `Expert Behavioral Event Interviewer`
- **Modelo:** Optimizado para razonamiento situacional (DeepSeek-Chat / GPT-4o).

---

## 🔍 Psicometría Inferencial

El objetivo de la Psicometría Inferencial es pasar del "La IA lo dice" al "La IA lo prueba".

### 🔎 Seguimiento Dinámico de Evidencia

Cada rasgo psicométrico guardado en la base de datos incluye un campo de **Evidencia**. Este campo contiene una cita directa o parafraseada de la entrevista que justifica el puntaje asignado.

**Ejemplo de Insight:**

- **Rasgo:** _Resiliencia bajo Presión_
- **Puntaje:** 0.92
- **Evidencia:** "En la crisis de Oct 2025, asumí personalmente la migración del servidor cuando el líder no estaba, trabajando 24 horas para asegurar el 99% de disponibilidad."

### 🏗️ Análisis Multi-Agente (Visión 360°)

El motor de análisis utiliza tres agentes especializados para procesar la información:

1.  **Psicometrista Inferencial Senior:** Extrae rasgos conductuales y evidencia.
2.  **Guardián de la Cultura Organizacional:** Evalúa el alineamiento con el Manifiesto de Cultura.
3.  **Predictor de Éxito Estratégico:** Proyecta el ROI y la sinergia con el equipo.

---

## 📊 Actualización de Esquema de Datos

La tabla `psychometric_profiles` ha sido extendida para soportar esta trazabilidad:

```sql
ALTER TABLE psychometric_profiles ADD COLUMN evidence TEXT;
```

---

## 🚀 Impacto en la Explicabilidad

Estos motores alimentan directamente la **UI de Explicabilidad**, permitiendo a los líderes ver el segmento exacto de la conversación que llevó a cada conclusión diagnóstica. Esto aumenta la confianza y permite una verificación humana (Human-in-the-loop) de las decisiones de la IA.
