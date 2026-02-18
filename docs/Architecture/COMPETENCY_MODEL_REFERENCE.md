# Modelo de Competencias y Selección Estratégica para Stratos

**Fecha:** 17 de Febrero, 2026
**Objetivo:** Definir el marco teórico-científico que guiará los algoritmos de IA y la estructura del Grafo de Conocimiento en Stratos.

## Documentación Relacionada

- **Implementación Técnica:** [Roadmap de Arquitectura Inteligente](./ARCHITECTURE_ROADMAP_v1.md) - _Cómo se traduce este modelo en código e infraestructura._

---

## 1. Revisión de Literatura: La Evolución de los Modelos

Para que Stratos sea una herramienta robusta, no debemos reinventar la rueda, sino integrar lo mejor de la psicología organizacional clásica con la analítica de datos moderna.

### A. El Fundamento: David McClelland & "Testing for Competence" (1973)

- **Premisa:** Los tests de inteligencia (IQ) y los títulos académicos _no_ predicen el éxito laboral. Lo que predice el éxito son las **Competencias**: características subyacentes de la persona que resultan en un desempeño efectivo o superior.
- **Aporte a Stratos:** Nuestro análisis de brechas no debe medir solo "sabe Python", sino "usa Python para resolver problemas complejos bajo presión".

### B. El Modelo del Iceberg (Spencer & Spencer, 1993)

Posiblemente el modelo más sólido para estructurar nuestra IA. Divide las competencias en dos niveles:

1.  **Visible (La Punta):** Destrezas (Skills) y Conocimientos. Son fáciles de medir y entrenar.
2.  **Invisible (Sumergido):** Auto-concepto, Rasgos (Traits) y Motivos. Son difíciles de entrenar pero determinan el comportamiento a largo plazo.

### C. Entrevista de Incidentes Críticos (BEI - Behavioral Event Interview)

- **Metodología:** En lugar de preguntas hipotéticas ("¿Qué harías si...?"), se piden evidencias concretas del pasado ("Cuéntame de un momento específico en que...").
- **La Regla de Oro:** _El comportamiento pasado es el mejor predictor del comportamiento futuro._

### D. La "Organización Basada en Habilidades" (Josh Bersin / Deloitte, 2020s)

- **Tendencia Actual:** Moverse de "Cargos" estáticos a "Clusters de Habilidades" dinámicos.
- **Aporte a Stratos:** La fluidez. Un rol no es una caja fija, es una colección de skills que evoluciona.

---

## 2. El "Modelo Stratos": Integración Híbrida

Proponemos un modelo propietario para Stratos que combina la **Granularidad** de los Skills (para el Grafo) con la **Profundidad** del Iceberg (para los Agentes).

### Estructura de 3 Capas

#### Capa 1: Hard Skills & Herramientas (El "Qué") -> _Manejado por Neo4j_

- **Definición:** Conocimientos técnicos, herramientas, lenguajes, certificaciones.
- **Evaluación:** Binaria o por niveles de dominio (1-5).
- **Ejemplo:** "React.js", "Contabilidad IFRS", "Operación de Grúa Horquilla".
- **Cierre de Brecha:** Fácil. Estrategia "Build" (Capacitación).

#### Capa 2: Competencias Conductuales (El "Cómo") -> _Manejado por Agentes (BEI)_

- **Definición:** Comportamientos observables transferibles.
- **Evaluación:** A través de evidencias de resultados e incidentes críticos.
- **Ejemplo:** "Resolución de Problemas", "Liderazgo de Cambio", "Comunicación Asertiva".
- **Cierre de Brecha:** Medio. Requiere Coaching o Mentoring.

#### Capa 3: Meta-Competencias & Agilidad (El "Por Qué") -> _Inferido por Psicometría_

- **Definición:** Capacidad de aprendizaje (Learning Agility), Motivación, Rasgos de Personalidad (Big Five).
- **Evaluación:** Inferencia de patrones, tests psicométricos invisibles.
- **Ejemplo:** "Curiosidad Intelectual", "Resiliencia", "Apertura a la Experiencia".
- **Cierre de Brecha:** Difícil. Estrategia "Buy" (Selección por Cultural Fit).

---

## 3. Implementación en la Tecnología Stratos

### A. Para el Análisis de Brechas (Step 3)

El sistema no dirá simplemente _"Falta conocimiento en Python"_. Dirá:

> _"Detectamos una brecha crítica en la competencia **'Innovación Técnica'** (Capa 2). Aunque el equipo conoce las herramientas (Capa 1), carecen de evidencia de haberlas aplicado en escenarios de alta incertidumbre. Recomendamos contratar un Tech Lead con alta 'Apertura a la Experiencia' (Capa 3) para catalizar el cambio."_

### B. Para la Selección (Chatbot Interviewer)

El Agente Entrevistador utilizará la técnica **STAR** (Situación, Tarea, Acción, Resultado) para validar la Capa 2:

1.  **Prompt del Agente:** _"El candidato dice saber 'Liderazgo'. Pídele que describa un incidente crítico donde tuvo que liderar a un equipo desmotivado. Indaga específicamente en QUÉ hizo él (Acción) y CUÁL fue el impacto medible (Resultado)."_
2.  **Validación de Evidencia:** El agente buscará en la respuesta indicadores de conducta específicos (ej: uso de "Yo" vs "Nosotros", mención de métricas concretas).

### C. Para Evaluación 360° Viva (Feedback Continuo)

En lugar de encuestas anuales sesgadas, Stratos utiliza el Grafo de Conocimiento para identificar quiénes son los verdaderos pares de trabajo.

- **El Problema:** El organigrama dice que reportas a Juan, pero trabajas día a día con María (Producto) y Pedro (QA). Juan no ve tu trabajo real.
- **La Solución Stratos:** El Agente de Feedback 360° rastrea la "Huella Digital de Colaboración" (Commits conjuntos, reuniones, tickets de Jira compartidos) para solicitar feedback a las personas relevantes.
- **Prompt del Agente:** _"Vi que colaboraste intensamente con Ana en el 'Proyecto Migración' las últimas 2 semanas. ¿Cómo calificarías su competencia de 'Resolución de Problemas' en ese contexto específico?"_

### D. Para Evaluación de Clima y Satisfacción (Termómetro Organizacional)

- **El Problema:** Las encuestas de clima son fotografías estáticas que llegan tarde.
- **La Solución Stratos:** Análisis de Sentimiento Pasivo y Agentes de "Pulso".
- **Mecanismo:**
    1.  **Detección de Patrones:** El sistema alerta si un equipo de alto rendimiento repentinamente baja su velocidad o aumenta el tono negativo en sus comunicaciones (respetando privacidad, analizando tendencias agregadas).
    2.  **Agente de Bienestar:** Un bot confidencial que contacta proactivamente: _"Noto que el equipo ha estado trabajando horas extra esta semana. ¿Cómo te sientes con la carga actual?"_.
    3.  **Inferencia de Burnout:** Cruzando horas de actividad + tono de lenguaje + estancamiento en skills (falta de tiempo para aprender) = Alerta temprana de fuga de talento.

---

## 4. Conclusión

Elegimos el **Modelo del Iceberg Dinámico**.

- **Dinámico:** Porque las Skills (Punta del Iceberg) cambian rápido y se mapean en el Grafo.
- **Profundo:** Porque los Agentes buscan la base sumergida (Rasgos/Motivos) mediante inferencia y incidentes críticos.

Esta combinación nos da la precisión técnica de una ATS moderna y la profundidad psicológica de un consultor experto.
