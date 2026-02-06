# ChangeSet Process (Resumen)

Propósito: centralizar cambios planeados sobre escenarios/roles/competencies en un objeto `ChangeSet` que puede previsualizarse, revisarse y aplicarse de forma transaccional.

Flujo general

- Crear: API `POST /api/strategic-planning/scenarios/{scenarioId}/change-sets` -> guarda `title`, `diff` y metadata.
- Editar ops: API `POST /api/strategic-planning/change-sets/{id}/ops` -> añade una op al `diff.ops`.
- Preview: `GET /api/strategic-planning/change-sets/{id}/preview` -> retorna el diff almacenado.
- Approve/Apply: `POST /api/strategic-planning/change-sets/{id}/apply` -> valida permisos y ejecuta `ChangeSetService::apply`.

Representación del diff

- Un `ChangeSet.diff` contiene `ops` (array). Cada `op` tiene al menos `type` y opcional `payload`.
- Tipos soportados actualmente:
  - `create_competency_version`: crea una `CompetencyVersion` (auto-completa `name`, `description`, `version_group_id` y `evolution_state`).
  - `create_role_version`: crea una `RoleVersion` (con `version_group_id` y metadata por defecto si faltan).
  - `update_pivot`: actualiza o inserta filas en tablas pivot (genérico).
  - `update_scenario_role_skill`: actualiza/inserta en `scenario_role_skills`.
  - `create_role_sunset_mapping`: crea registro en `role_sunset_mappings` y asegura que exista un `RoleVersion` (auto-backfill) si no existe.

Reglas importantes

- Auto-backfill: durante `apply`, si una op requiere una versión (role/competency) y no existe, el servicio crea una versión siguiendo las convenciones del repo (`version_group_id = UUID`, `metadata.source = 'backfill'` o `'changeset'`).
- Idempotencia: antes de crear `CompetencyVersion`, `RoleVersion` o `RoleSunsetMapping` se comprueba existencia para evitar duplicados.
- Transaccional: la ejecución completa de `apply` es atómica mediante transacción DB. Si falla, todo revierte.

Extensiones y recomendaciones

- Policies: implementar `ChangeSetPolicy` para controlar quién puede `apply` (approvers). Actualmente `apply` requiere autorización.
- Auditoría: `ChangeSet` guarda `created_by` y `approved_by` y marca `applied_at` cuando se aplica.
- CI: ejecutar migraciones y seeders antes de pruebas para asegurar esquemas y constraints.

Notas de implementación

- Archivo principal del servicio: `src/app/Services/ChangeSetService.php`.
- Tabla de mappings de sunset: `role_sunset_mappings` (modelo `RoleSunsetMapping`).
- Backfill CLI existente para competencias: `backfill:competency-versions`. Nuevo comando agregado: `backfill:role-versions`.

Próximos pasos

- Implementar `ChangeSetPolicy` y flujo de aprobación en UI.
- Añadir tests E2E que cubran rollback en errores.
- Expandir ops soportados y mejorar viewer de diffs en frontend.
