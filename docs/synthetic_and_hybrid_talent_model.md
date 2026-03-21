# 🧬 Modelo de Talento Sintético e Híbrido (Stratos Hybrid DNA)

## 0. Introducción: De Capital Humano a Orquestación de Capacidades

Tradicionalmente, la gestión de talento se ha centrado exclusivamente en el ser humano como único ejecutor de habilidades. En la era de la IA Agéntica, Stratos evoluciona para gestionar **Capacidades**, independientemente de si son ejecutadas por una persona, un agente de IA autónomo o una simbiosis de ambos.

Este documento detalla el **Cubo de la Skill**, el marco arquitectónico que permite a las organizaciones diseñar y gobernar una fuerza de trabajo híbrida.

> [!NOTE]
> Para una explicación técnica y teórica más profunda de los marcos inyectados (4D, PAI, STARA), consulte el documento **[Conceptual Models Deep Dive](conceptual_models_deep_dive.md)**.

---

## 1. El Cubo de la Skill (XYZ)

Cada unidad atómica de capacidad en Stratos (la Skill) se define tridimensionalmente para determinar su estrategia de ejecución.

### Eje X: Arquetipo de Ejecución (Talent Mode)
Define el "dueño" del proceso y el grado de autonomía tecnológica.

*   **Human-Centric (H):** Tareas que requieren alta inteligencia emocional, juicio ético complejo, empatía o presencia física. El humano es el único ejecutor.
*   **Hybrid Augmented (A):** El humano es el piloto y la IA es el "exoesqueleto". La tarea se realiza más rápido o mejor gracias a herramientas de IA (Copilots, ChatGPT, etc.).
*   **Human-in-the-Loop (HITL):** La IA realiza el grueso del trabajo operativo (ej: un flujo de n8n), pero el humano actúa como interruptor de seguridad o validador del resultado final.
*   **Synthetic Autonomous (S):** Un agente digital o bot realiza el 100% de la tarea. No requiere intervención humana directa más allá del monitoreo de métricas.

### Eje Y: Niveles de Maestría (Dominio BARS/SFIA)
Define la profundidad y calidad del resultado (1 al 5).
*   **Nivel 1-2:** Ejecución operativa simple o asistida.
*   **Nivel 3:** Ejecución táctica autónoma.
*   **Nivel 4-5:** Ejecución estratégica, liderazgo y optimización del dominio.

### Eje Z: Fluidez IA (Madurez 4D)
Mide la capacidad de la organización (o del colaborador) para trabajar en simbiosis con la tecnología. Se basa en el **Framework 4D**.

---

## 2. El Framework 4D de Fluidez en IA

Para que una habilidad pueda ser movida hacia el espectro **Híbrido** o **Sintético**, debe ser evaluada bajo estas cuatro dimensiones:

| Dimensión | Descripción | Aplicación en Stratos |
| :--- | :--- | :--- |
| **D1: Delegación** | Capacidad de identificar qué partes de la tarea son "maquinizables". | El Curador IA sugiere el porcentaje de automatización. |
| **D2: Descripción** | Habilidad para estructurar la tarea (Prompt Engineering) o definir workflows. | Facilidad para crear un trigger en **n8n**. |
| **D3: Discernimiento** | Capacidad crítica para validar resultados y detectar alucinaciones o errores. | Define los puntos de control humano (HITL). |
| **D4: Diligencia** | Responsabilidad ética y legal sobre el output, sin importar quién lo ejecutó. | Sello digital de cumplimiento y autoría. |

---

## 3. Implementación en el Agente Curador (Wizard)

Cuando el **Agente Curador (IA)** materializa una competencia, realiza un análisis de "Genética Híbrida":

1.  **Análisis de Potencial Sintético:** La IA evalúa la descripción de la competencia.
2.  **Propuesta de Arquetipo:** Basado en la complejidad, asigna `Human`, `Hybrid` o `Synthetic`.
3.  **Generación de Blueprint:** Provee los puntajes 4D para cada habilidad.
4.  **Conector n8n:** Si la skill es `Synthetic`, el sistema queda preparado para recibir un `workflow_id` que ejecute la lógica en tiempo real.

---

## 4. Impacto en el Workforce Planning

Este modelo cambia la forma en que el C-Level ve la organización:

*   **Optimización de Costos:** Las habilidades `Synthetic` se miden por costo de cómputo/API, no por salario.
*   **Reskilling Continuo:** El enfoque del entrenamiento humano se desplaza hacia las habilidades de **Discernimiento (D3)** y **Diligencia (D4)**.
*   **Arquitectura de Roles:** Un rol ya no es solo una lista de tareas humanas; es una composición de **Humanos + Agentes Sintéticos** orquestados por Stratos.

## Repaso

Deep Dive conceptual de los modelos que hemos inyectado hoy en la arquitectura:

### 1. El Framework 4D (Fluidez en IA)

#### Referencia: Basado en marcos de alfabetización digital avanzada para equipos híbridos.

Este modelo no mide "cuánta IA usas", sino "qué tan bien colaboras con ella". Es el Eje Z de nuestro Cubo de Skill.

*   **D1: Delegación (Delegation):** Es la capacidad estratégica de trocear un proceso y saber qué partes debe hacer un humano y cuáles un agente (ej. n8n). Si delegas mal, el costo de supervisión supera al de ejecución.
*   **D2: Descripción (Description):** Se refiere a la Arquitectura de Instrucciones. En un modo sintético, es la capacidad de convertir una visión en un prompt o en un workflow estructurado. Sin una buena descripción, la IA genera "ruido".
*   **D3: Discernimiento (Discernment):** Es el filtro crítico. Un experto humano en este nivel detecta "alucinaciones", sesgos o errores lógicos en lo que la IA produce. Es el punto de control de calidad.
*   **D4: Diligencia (Diligence):** Es la responsabilidad última. Aunque la IA cometa el error, el humano (o el proceso certificado en Stratos) asume la diligencia. Es el componente legal y ético de la ejecución.

### 2. El Modelo PAI (Partnership on AI - Collaboration Framework)

#### Referencia: Un marco líder en el diseño de sistemas de interacción Humano-IA.

Este modelo define nuestro Eje X (Arquetipos de Ejecución). Nos permite dejar de usar el término genérico "IA" y empezar a hablar de niveles de agencia:

- AI as Observer: La IA monitorea la skill humana (analítica).
- AI as Tool: La IA es pasiva (ej. Excel con Copilot).
- AI as Partner (Hybrid): Trabajo en pareja. La IA sugiere y el humano decide en tiempo real.
- AI as Actor (Synthetic): La IA ejecuta de forma autónoma. Stratos gestiona este arquetipo como un "Colaborador Sintético".

### 3. El Modelo STARA (Smart Technology, AI, Robotics, Algorithms)

#### Referencia: Estudios sobre la susceptibilidad de los empleos a la automatización.

Este modelo se encarga de calcular el "Índice de Potencial Sintético" que ves en el Wizard de Stratos. Analiza:

*   **Repetibilidad:** ¿Se repite el patrón de la tarea?
*   **Structured Data:** ¿Usa datos limpios o juicio ambiguo?
Predictibilidad: ¿El resultado deseado es constante?

### 4. La Síntesis en Stratos: El Cubo de la Skill

Al cruzar estos modelos, Stratos genera una "Firma de Capacidad" única. Por ejemplo, una habilidad de "Análisis de Riesgos Crediticios" puede ser:

*   **X (Arquetipo):** HITL (Human-in-the-Loop).
*   **Y (Maestría):** L4 (Experto).
*   **Z (Fluidez 4D):** Requiere un nivel D3 (Discernimiento) máximo para validar los datos del bot de n8n.

¿Por qué esto es una revolución operativa?
Porque ahora, cuando diseñas un rol (o un cubo de rol), puedes decidir el "Mix de Talento". Antes solo podías elegir "Personas". Ahora puedes diseñar un rol que sea:

*   **60% Capacidad Humana (H).**
*   **30% Capacidad Aumentada (A).**
*   **10% Capacidad Sintética (S).**

Esto permite que la organización escale exponencialmente. Contratas humanos para las áreas donde el Eje X es Humano-Céntrico y despliegas agentes de n8n donde el Eje X es Sintético.

---

> [!IMPORTANT]
> **Stratos DNA** asegura que la organización no solo use IA de forma ad-hoc, sino que la IA esté **embebida en la arquitectura del talento** de forma gobernada y certificada.
