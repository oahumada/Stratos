# Release & QA Checklist

Breve checklist para preparar un release de la feature `scenario-planning`.

1. Código
   - [ ] Todos los cambios están en una rama feature clara (`feature/scenario-planning/paso-2`).
   - [ ] Commits son semánticos (`feat:`, `fix:`, `chore:`).

2. Tests
   - [ ] Unit tests locales pasan: `npm run test:unit` y `php artisan test`.
   - [ ] Feature tests relevantes (Backfill, Transform) pasan en CI.
   - [ ] Añadir E2E/integ si aplica (Playwright/Cypress) para flujo Transform.

3. Runbook / Backfill
   - [ ] `docs/RUNBOOK_backfill.md` revisado y aprobado por DBA.
   - [ ] Backup creado antes de ejecutar `--apply`.

4. Seguridad
   - [ ] No se han incluido secrets en commits.
   - [ ] Revisar uso de `env()` en runtime y validar configuración Sanctum.

5. Performance
   - [ ] Revisar queries nuevos; añadir índices si detectado slow queries.
   - [ ] Ejecutar smoke tests en staging con dataset representativo.

6. Docs & ChangeLog
   - [ ] Actualizar `openmemory.md` y files de docs relevantes.
   - [ ] Añadir entrada en `CHANGELOG.md` (si aplica) describiendo backfill y UI BARS.

7. Deployment
   - [ ] Plan de rollback disponible (DB dump o scripts de eliminación por `metadata.source='backfill'`).
   - [ ] Ventana de mantenimiento programada si es necesario.

8. Post-deploy
   - [ ] Validar conteo de `competency_versions` y muestreos.
   - [ ] Monitoreo de errores y alertas (Sentry/Logs).

---

Usar este checklist para PRs y despliegues. Añadir pasos específicos si el entorno los requiere.
