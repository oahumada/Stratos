# Documentación: `discovered_in_scenario_id` y flujo de guardado desde nodo

Resumen breve

- `discovered_in_scenario_id` en la tabla `capabilities` es la FK que indica que la capability fue creada/descubierta dentro de un `Scenario` concreto (modo "incubating").
- La relación entre `Scenario` y `Capability` se materializa en la tabla pivot `scenario_capabilities`, que contiene atributos propios de la relación (p. ej. `strategic_role`, `priority`, `required_level`, `is_critical`).

Entidades y responsabilidades

- `capabilities` (entidad): guarda atributos inherentes a la capability: `name`, `description`, `position_x`, `position_y`, `importance`, `type`, `category`, `status` y opcionalmente `discovered_in_scenario_id`.
- `scenario_capabilities` (relación/pivot): guarda atributos de la relación escenario–capability: `scenario_id`, `capability_id`, `strategic_role`, `strategic_weight`, `priority`, `rationale`, `required_level`, `is_critical`, timestamps.

Semántica práctica

- Si una capability se crea desde el canvas/nodo de un `Scenario`, la UI debe realizar dos acciones atomizadas al pulsar `Guardar`:
  1. Crear o actualizar la fila en `capabilities` (incluyendo `discovered_in_scenario_id = <scenario_id>` si es nueva).
  2. Crear o actualizar la fila en `scenario_capabilities` con los campos de relación capturados en la UI.
- La FK `discovered_in_scenario_id` es informativa y permite distinguir capacidades incubadas; la persistencia de la relación concreta (y sus metadatos) reside en el pivot.

Comportamiento en el backend del repositorio

- Ruta API de ejemplo que guarda desde el nodo: `POST /strategic-planning/scenarios/{id}/capabilities` (archivo: `src/routes/api.php`).
  - Crea la fila en `capabilities` y establece `discovered_in_scenario_id`.
  - Inserta (si no existe) la fila correspondiente en `scenario_capabilities` con los campos de relación recibidos o con valores por defecto.
- Modelo `Capability` (archivo: `src/app/Models/Capability.php`) contiene un listener `created` que, como seguridad secundaria, añade el registro en `scenario_capabilities` cuando `discovered_in_scenario_id` está presente. La inserción en el API es la fuente de verdad del flujo Save desde UI.

Ejemplo de payload recomendado desde la UI (JSON)
{
"name": "Nueva Capability",
"description": "Descripción...",
"position_x": 42.5,
"position_y": 13.0,
"importance": 3,
"discovered_in_scenario_id": 123,
// Campos de relación (opcionalmente en la misma petición)
"strategic_role": "target",
"strategic_weight": 50,
"priority": 3,
"rationale": "Razón...",
"required_level": 3,
"is_critical": true
}

Comandos útiles para verificar localmente (Tinker)

```bash
cd src
php artisan tinker
# Crear capability desde tinker (simula la creación desde UI con discovered_in_scenario_id)
$cap = \App\Models\Capability::create(["organization_id"=>1,"name"=>"Test Cap","type"=>"technical","discovered_in_scenario_id"=>1]);
\DB::table('scenario_capabilities')->where('capability_id', $cap->id)->first();
```

Decisiones recomendadas

- Mantener la inserción explícita en la API (ruta) como flujo primario — el listener en `Capability::booted()` puede permanecer como backup, pero no sustituye la lógica del endpoint.
- No mover atributos de relación al registro `capabilities`; mantener la separación entidad vs relación evita duplicidad y ambigüedades.

Referencias en el código

- Modelo `Capability`: src/app/Models/Capability.php
- Pivot/migración: src/database/migrations/2026_01_12_193106_create_scenario_capabilities_table.php
- Ruta de creación desde nodo (dev API): src/routes/api.php

Notas finales

- Si la UI necesita crear la relación en una llamada distinta (por ejemplo, crear la capability primero y luego abrir un modal para los metadatos de relación), implementar endpoint `PUT/POST /scenarios/{id}/capabilities/{capability_id}` para crear/actualizar el pivot.
