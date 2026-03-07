# 🎮 Documentación del Motor de Gamificación y Aprendizaje - Stratos

Como experto en aprendizaje y gamificación, presento la arquitectura y el diseño pedagógico del motor de incentivos de Stratos. Este sistema no es puramente lúdico; es un **ecosistema de refuerzo positivo** diseñado para alinear las metas individuales de desarrollo con las necesidades estratégicas de la organización.

---

## 🏛️ 1. Filosofía del Diseño: El Ciclo de Aprendizaje Gamificado

El modelo de Stratos se basa en la teoría de la **Autodeterminación (SDT)**, fomentando la _Competencia_, la _Autonomía_ y la _Relación_ a través de un ciclo de retroalimentación continua:

1.  **Desafío (Quest):** Se establecen metas claras y alcanzables (Autonomía).
2.  **Acción (Progreso):** El usuario realiza actividades de aprendizaje (Competencia).
3.  **Refuerzo (XP/Badges):** Reconocimiento inmediato del esfuerzo realizado (Relación/Estatus).
4.  **Evolución (Stratos IQ):** Integración de los logros en el perfil de talento global.

---

## 🛠️ 2. Arquitectura de Datos y Modelos

El motor está construido sobre cuatro pilares fundamentales en la base de datos:

### A. Misiones (`Quest`)

Es el contenedor de objetivos.

- **Campos Clave:** `requirements` (JSON), `points_reward`, `badge_id`.
- **Tipo de Logro:** Puede ser por hitos (_Milestone-based_) o por acumulación de experiencia (_Experience-based_).

### B. Instancia de Usuario (`PersonQuest`)

Rastrea la jornada individual.

- **Estado Dinámico:** El campo `progress` permite guardar estados parciales, permitiendo que el aprendizaje sea asíncrono y persistente.
- **Persistencia:** Almacena el `completed_at` para análisis de velocidad de aprendizaje (_Learning Velocity_).

### C. Recompensas de Experiencia (`PeoplePoint`)

El motor de la economía interna.

- **Atribución:** Cada punto tiene un `reason` y `meta` data, permitiendo auditar por qué un colaborador está destacando (ej. "Liderazgo en Mentoría", "Dominio Técnico IA").

### D. Reconocimiento Social (`Badge`)

La representación visual de la maestría.

- **Estandarización:** Basado en slugs únicos para facilitar la integración con estándares como W3C Verifiable Credentials en el futuro.

---

## 🚀 3. El Motor de Incentivos al Aprendizaje

La integración con el aprendizaje no es accidental; es técnica y metodológica:

### 🔄 Flujo de Incentivo (Loop de Hook)

1.  **Activación:** El sistema detecta un "Skill Gap" en el **Stratos Map** y sugiere una Quest específica.
2.  **Inversión:** El usuario inicia la misión a través del `GamificationController@startQuest`.
3.  **Feedback Prematuro:** Conforme el usuario progresa en el LMS o recibe feedback, el `GamificationService@progressQuest` actualiza su estado.
4.  **Celebración de Maestría:** Al completar el 100%, se dispara una `DB::transaction` que:
    - Marca la misión como completada.
    - Acumula puntos de XP.
    - Otorga la insignia correspondiente.

### 🍱 El "Micro-Learning" Gamificado

Al fragmentar grandes metas de desarrollo en requisitos dentro de una Quest, Stratos reduce la fricción cognitiva del aprendizaje. El usuario no "estudia para un rol", sino que "completa pasos de una misión".

---

## 📊 4. Impacto en el Negocio (ROI de Aprendizaje)

El motor de gamificación convierte datos subjetivos de "capacitación" en métricas objetivas de talento:

- **Learning Velocity:** Velocidad con la que los colaboradores completan Quests.
- **Skill Liquidity:** Qué tan rápido se mueven los puntos de XP en diferentes clústeres de habilidades.
- **Engagement Rate:** Participación activa en misiones voluntarias vs. mandatorias.

---

## 📝 Conclusión para Q&A

El sistema de gamificación de Stratos está **listo para la fase de producción**. Su arquitectura desacoplada a través del `GamificationService` permite que cualquier nuevo módulo (ej. un nuevo simulador de IA o un portal de cultura) pueda "premiar" al usuario simplemente inyectando puntos o disparando misiones, manteniendo la integridad del ADN de talento del colaborador.
