Genera un escenario estratégico de talento en formato JSON únicamente.

REQUISITOS:

- Responder SOLO con un único objeto JSON válido (no incluyas explicaciones, texto o markdown fuera del JSON).
- El objeto debe contener, como mínimo, las claves: `scenario_metadata`, `capabilities`, `competencies`, `skills`, `suggested_roles`, `impact_analysis`, `confidence_score`, `assumptions`.
- Cada elemento en `capabilities` debe ser un objeto con `name`, opcional `description` y `competencies` (array).
- Cada `competency` debe tener `name`, opcional `description` y `skills` (array).
- Cada `skill` puede ser una cadena o un objeto con `name`.
- `confidence_score` debe ser un número entre 0 y 1.

CONTEXTO (breve):

- Empresa: {{company_name}} (reemplazar si procede)
- Objetivo del escenario: Proveer un plan de prioridad para cubrir una transformación estratégica enfocada en digitalización y retención de talento.
- Alcance: proponer 2-3 capacidades prioritarias, con 2-4 competencias por capability y 2-4 skills por competencia.

INSTRUCCIÓN PARA EL MODELO:

- Devuelve SOLO el JSON solicitado. No agregues explicaciones ni comentarios.
- Prioriza roles prácticos y mapeables a estructuras organizacionales típicas (ej. Analista, Líder de Producto, Ingeniero de Datos).
- Cuando propongas `suggested_roles`, incluye `name`, `estimated_fte` (número aproximado), y `key_competencies` (lista de nombres).
- En `impact_analysis` incluye 2-3 ítems con `area`, `impact` (alto/medio/bajo) y `notes`.

INSTRUCCIÓN PARA EL MODELO (ENFOQUE INGENIERÍA DE TALENTO):
- Actúa como un Ingeniero de Talento Estratégico. Tu objetivo es diseñar un plano (blueprint) de capacidades híbridas.
- Por cada rol en `suggested_roles`, DEBES incluir obligatoriamente el objeto `talent_composition`:
    - `human_percentage`: % de carga de trabajo que requiere juicio humano, empatía o liderazgo (0-100).
    - `synthetic_percentage`: % de carga de trabajo delegable a agentes IA o automatización (0-100).
    - `strategy_suggestion`: Elige la mejor estrategia de cobertura: ["Buy", "Build", "Borrow", "Synthetic"].
    - `logic_justification`: Breve explicación de por qué ese mix (ej: "Alta carga de procesamiento de datos permite 70% IA").

- En `impact_analysis`, evalúa cómo la introducción de "Talento Sintético" (IA) mejora la eficiencia de la capacidad analizada.

EJEMPLO MÍNIMO DE SALIDA ESPERADA:
{
"scenario_metadata": {
"name": "Transformación Digital - Retención 2026",
"generated_at": "2026-02-09T12:00:00Z",
"confidence_score": 0.87
},
"capabilities": [
{
"name": "Datos y Analítica",
"description": "Capacidad para recolectar, procesar y explotar datos operativos",
"competencies": [
{
"name": "Ingesta y calidad de datos",
"description": "Pipelines confiables y validaciones",
"skills": ["ETL básico", {"name": "Validación de esquemas"}]
}
]
}
],
"competencies": [],
"skills": [],
"suggested_roles": [
{"name": "Analista de Datos", "estimated_fte": 1.5, "key_competencies": ["Ingesta y calidad de datos"]}
],
"impact_analysis": [
{"area": "Operaciones", "impact": "alto", "notes": "Reducción de re-trabajo gracias a datos consolidados"}
],
"confidence_score": 0.87,
"assumptions": ["Los datos fuente están disponibles en formatos CSV/DB"]
}

USO RÁPIDO (CLI):

- Para usar en `scripts/generate_via_abacus.php` o en el modal del wizard, copia el contenido JSON de ejemplo como prompt o sustituye tokens (p. ej. `{{company_name}}`) por valores reales antes de enviar.

NOTA: Este archivo es una plantilla de prueba para integraciones; modifica `company_name` y el `OBJECTIVE` según el caso de prueba.
