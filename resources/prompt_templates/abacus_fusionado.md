````markdown
# Instrucción por defecto (Sistema) ABACUS — Fusión

Genera un escenario estratégico a partir de los datos proporcionados y devuelve
únicamente un objeto JSON válido. El documento siguiente fusiona y unifica las
instrucciones de `abacus_original.md` y `abacus_modal_prompt_es.md`, manteniendo
todo el contenido no redundante y eliminando repeticiones obvias.

---

REQUISITOS GENERALES:

- Responder SOLO con un único objeto JSON válido (no incluyas explicaciones,
  texto o markdown fuera del JSON).
- El objeto debe contener, como mínimo, las claves: `scenario_metadata`,
  `capabilities`, `competencies`, `skills`, `suggested_roles`, `impact_analysis`,
  `confidence_score`, `assumptions`.
- Cada elemento en `capabilities` debe ser un objeto con `name`, opcional
  `description` y `competencies` (array).
- Cada `competency` debe tener `name`, opcional `description` y `skills` (array).
- Cada `skill` puede ser una cadena o un objeto con `name`.
- `confidence_score` debe ser un número entre 0 y 1.

CONTEXT / METADATA SUGERIDO:

- Empresa: `{{company_name}}` (reemplazar si procede).
- Objetivo del escenario: Proveer un plan de prioridad para cubrir una
  transformación estratégica enfocada en digitalización y retención de talento.
- Alcance recomendado: proponer 2-3 capacidades prioritarias, con 2-4
  competencias por capability y 2-4 skills por competency.

---

PROPÓSITO Y DEFINICIONES BREVES

Propósito: este escenario simula la gestión estratégica del talento para
alcanzar el objetivo principal.

Definiciones:

- **Capabilities (Capacidades):** medios/funciones organizacionales que
  permiten cumplir el objetivo del escenario.
- **Competencies (Competencias):** conocimientos y habilidades necesarias para
  ejecutar una capability.
- **Skills (Habilidades):** unidad mínima (habilidades/conocimientos) que
  compone una competency; puede ser texto o un objeto `{ "name": "..." }`.
- **Suggested_Roles (Suggested_Roles) / Roles:** puestos propuestos con las
  competencias asignadas; el analista debe homologar estos roles con la
  estructura interna. Para cada rol incluir `name`, `description` opcional,
  `key_competencies` (lista de IDs o nombres) y `estimated_fte`.

---
