# 🐕 Cerbero Engine: El Orquestador de Relaciones y Flujos Sociales

El **Cerbero Engine** es el motor de gobernanza social de Stratos. Su función principal es mapear, proteger y activar la red de relaciones humanas de la organización, eliminando la ambigüedad en procesos de evaluación y toma de decisiones.

---

## 1. Características Principales

Cerbero no es un organigrama estático; es un **Grafo de Relaciones Dinámico** con las siguientes características:

- **Multi-dimensionalidad:** Soporta estructuras matriciales donde un colaborador tiene múltiples jefes (Funcional, Proyecto, Mentor).
- **Reciprocidad 360°:** Entiende que las relaciones tienen direcciones y pesos distintos (un Jefe tiene más peso en resultados, un Par en colaboración).
- **Activación por Eventos:** Reacciona a cambios en la estructura (ej: una promoción dispara automáticamente la actualización de su red de pares).
- **Auditoría de Acceso:** Controla quién tiene derecho a ver qué información basándose en la cercanía social y jerárquica.

---

## 2. Funciones Core

### A. Mapeo del ADN Social (`Social Graph`)

Cerbero gestiona la tabla `people_relationships`, creando un mapa de "Quién es Quién" que sirve como fuente de verdad para toda la plataforma.

### B. Identificación Inteligente de Actores

Cuando se inicia un proceso (como una Evaluación 360), Cerbero identifica instantáneamente:

- **Managers:** Para feedback de desempeño y kpis.
- **Peers:** Para feedback de cultura y equipo.
- **Subordinates:** Para feedback de liderazgo.
- **Detección de Vacíos:** Si un colaborador no tiene suficientes pares o un jefe asignado, Cerbero lanza una alerta de "Gobernanza Débil".

### C. Orquestación de Feedback

Selecciona el banco de preguntas adecuado (`SkillQuestionBank`) basándose en el tipo de relación detectada, asegurando que cada evaluador responda sobre lo que realmente observa.

---

## 3. Usos en Stratos

1.  **Evaluación 360:** El uso más crítico. Cerbero elimina la necesidad de configurar manualmente quién evalúa a quién.
2.  **Detección de Silos:** Analiza el grafo social para encontrar departamentos con nula conexión entre sí, permitiendo acciones preventivas del CEO.
3.  **Sucesión Automática:** Identifica la "Unidad de Comando" afectada cuando un líder sale de la empresa, sugiriendo candidatos internos basados en la cercanía de la red.
4.  **IA Mentor:** El agente de IA consulta a Cerbero para saber a qué expertos internos puede sugerir como mentores para un colaborador específico.

---

_Cerbero Engine: Protegiendo la integridad de la red humana de Stratos._
