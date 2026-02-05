# Versionamiento de Roles (visión general)

## Propósito

Documentar cómo se debe gestionar el versionamiento de roles y su relación con competencias/versiones de competencias para conservar trazabilidad en mappings y cambios de responsabilidad.

## Relaciones y tablas sugeridas

- `roles` — identidad del rol.
- `role_versions` — (opcional) si se requiere guardar snapshots de una definición de rol (nombre, responsabilidades, band, perfil). Contendrá `role_id`, `version_number`, `payload`, `created_at`.
- `role_competency_mappings` — tabla que guarda asociaciones entre roles y competencias; puede apuntar a `competency_version_id` para precisión histórica.

## Flujo recomendado

- Cambios menores en rol (nombre, descripción) → actualizar `roles` (no necesariamente crear versión).
- Cambios estructurales o que afecten responsabilidades/skills del rol → crear `role_versions` y, cuando aplique, actualizar mapeos para apuntar a versiones de competencias concretas.

## Integración con competency versioning

- Cuando una transformación de competencia crea una nueva `competency_version`, los `role_competency_mappings` deben poder referenciar `competency_version_id`.
- Guardar `role_version_id` en eventos de deploy/major-change para poder reproducir estado histórico.

## Tests y auditoría

- Pruebas para asegurar que cuando se crea una nuevaCompetencyVersion, los mappings existentes no pierden referencia histórica.
- Auditoría: log de cambios cuando se actualizan mappings o se crea `role_versions`.
