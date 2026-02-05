# Versionamiento de Competencias

## Resumen ejecutivo

El versionamiento de competencias permite conservar trazabilidad de cambios en la definición de una competencia (BARS, lista de skills, nombre, descripción, metadata) y soportar flujos donde una asociación role↔competency necesita apuntar a una versión concreta.

## Tablas / modelos implicados

- `competencies` : entidad principal (meta).
- `competency_versions` : versiones históricas (id, competency_id, name, description, bars/json, metadata, created_at, created_by).
- `competency_skills` : relación many-to-many entre competencia (o versión) y skills.
- `skills` : lista de skills (pueden marcarse como `is_incubated`).

Si `competency_versions` no existe en la implementación actual, se recomienda crearla para separar la definición mutable de la entidad identidad.

## Flujo / Eventos

- Usuario abre `TransformModal` y edita la definición (BARS/skills/nombre).
- Al guardar en TransformModal se crea una nueva fila en `competency_versions` y el endpoint devuelve el payload con `id` (version id) y `created_skills` (si se generaron skills en incubación).
- Componente `RoleCompetencyStateModal` recibe ese evento (`@transformed`) y utiliza `handleTransformed` para:
  - asignar `competency_version_id` en el mapeo,
  - mostrar cualquier `created_skills` (incubation) en UI,
  - realizar un `auto-save` del mapping (llamada a `roleCompetencyStore.saveMapping`).

## API (ejemplos)

- POST `/api/competencies/{competencyId}/versions` -> crea versión
  - Request body: `{ name, description, bars, create_skills_incubated, metadata }`
  - Response: `{ success: true, data: { id: 123, created_skills: [{ id, name, is_incubated }] } }`

## Efectos en UI

- `RoleCompetencyStateModal`:
  - Si `change_type === 'transformation'` y no hay `competency_version_id`, al pulsar Guardar muestra `TransformModal` para crear versión.
  - Tras crear versión, asigna `competency_version_id`, muestra incubated skills y realiza guardado automático.

## Recomendaciones de diseño

- Mantener `competency_versions` como fuente de verdad inmutable para cada versión.
- Registrar `created_skills` con `is_incubated = true` y un workflow para promover (approve) o publicar.
- Version id debe propagarse al DTO del mapping (`competency_version_id`).

## Tests a cubrir

- Crear versión desde `TransformModal` y comprobar payload devuelto.
- `RoleCompetencyStateModal` recibe `transformed` y realiza `auto-save` (mocks de store/API).
- Comportamiento con `created_skills` (mostrar badges de incubación).

## Migraciones / Compatibilidad

- Si se añade `competency_versions` migrar datos previos creando versión inicial para cada `competency` existente.
