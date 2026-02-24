# Arquitectura de Nutrición de Datos: Agente Talento 360

Este documento detalla cómo los agentes de inteligencia de Stratos, específicamente el **Orquestador 360 (The Arbiter)**, recolectan y procesan información para generar análisis precisos de talento.

---

## 1. Flujos de Información (Fuentes de Datos)

El agente no opera sobre una base de datos estática, sino que realiza una **síntesis multifuente** de tres flujos principales:

### A. La Entrevista Psicométrica (Voz del Sujeto)
*   **Origen:** Módulo de entrevistas por chat de Stratos.
*   **Tipo de dato:** No estructurado (Texto/Diálogo).
*   **Propósito:** Capturar la auto-percepción del colaborador, su estilo de comunicación y sus respuestas a incidentes críticos.

### B. Feedback Externo (Evaluación Multifocal)
*   **Origen:** Evaluaciones de pares, jefes directos y subordinados.
*   **Tipo de dato:** Estructurado (Puntajes) y No estructurado (Comentarios cualitativos).
*   **Propósito:** Proporcionar la visión externa y objetiva del comportamiento del colaborador en el día a día.

### C. Datos de Desempeño (KPIs)
*   **Origen:** Backend de Laravel (Base de datos PostgreSQL).
*   **Tipo de dato:** Cuantitativo (Métricas de cumplimiento, KPIs).
*   **Propósito:** Anclar el análisis en resultados de negocio tangibles.

---

## 2. Proceso de Síntesis (Vía de Ejecución)

La nutrición del agente sigue un flujo técnico definido entre el Core (Laravel) y el Motor de Inteligencia (Python):

1.  **Recolección Contextual:** El `StratosAssessmentService` en Laravel agrupa el historial de la entrevista, los feedbacks externos y los KPIs del usuario en una estructura JSON.
2.  **Transmisión:** Se envía la carga de datos al endpoint `/interview/analyze-360` del microservicio de inteligencia.
3.  **Orquestación con CrewAI:** El agente es configurado con una "Persona" de **Psicólogo Organizacional Experto**, recibiendo las tres fuentes como contexto directo en su memoria de corto plazo para la tarea.
4.  **Generación de Insights:** El modelo (preferentemente DeepSeek R1 por su capacidad de razonamiento) cruza los datos para identificar:
    *   **Fortalezas:** Coincidencias positivas entre auto-percepción y feedback externo.
    *   **Puntos Ciegos (Blind Spots):** Discrepancias donde el feedback externo es más bajo que la auto-percepción.
    *   **Fortalezas Ocultas:** Áreas donde el equipo valora al colaborador más de lo que él mismo lo hace.

---

## 3. Resumen de Nutrición por Agente

| Agente | Principal "Alimento" de Datos |
| :--- | :--- |
| **Orquestador 360** | Entrevistas + Feedback 360 + KPIs de Desempeño. |
| **Stratos Sentinel** | Logs de sistema (`laravel.log`) + Auditoría de integridad de BD. |
| **Stratos Guide** | Documentación técnica del proyecto + Manuales (vía RAG). |
| **Estratega (Architect)** | Análisis de Brechas (Gaps) + Datos de Mercado de Talento. |

---
**Estado:** Documentación de Arquitectura de Datos  
**Fecha:** 24 de febrero de 2026
