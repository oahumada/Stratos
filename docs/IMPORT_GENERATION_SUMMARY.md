## Resumen: Importación/Incubación de generaciones LLM

Fecha: 2026-02-08

Resumen ejecutivo

- Implementado flujo seguro para persistir prompts aceptados y opcionalmente importar (incubar) entidades generadas por el LLM.
- La importación automática está controlada por feature-flag `import_generation` y puede activarse en entornos controlados.
- Se añadieron auditorías de intento de import (`import_audit`) y UI para revisar/promover incubadas antes de publicar.

Qué se implementó (archivos clave)

- Backend:
  - `src/app/Http/Controllers/Api/ScenarioGenerationController.php` — método `accept()` persiste `accepted_prompt`, metadatos y registra `import_audit`.
  - `src/app/Services/LlmResponseValidator.php` — validación JSON Schema del `llm_response`, retorna {valid, errors[]} legible para UI/ops.
  - Migraciones/backfill: `database/migrations/*backfill_accepted_prompt_metadata.php` — backfill para escenarios históricos.
  - `src/config/features.php` — contiene `import_generation` (resuelto desde env).

- Frontend:
  - `src/resources/js/components/IncubatedReviewModal.vue` — modal para revisar/incubar/promover entidades generadas.
  - `src/resources/js/pages/ScenarioPlanning/ScenarioDetail.vue` — muestra `import_audit` y abre modal de revisión.

- Scripts & Runbook:
  - `scripts/staging_backfill.sh` y `scripts/staging_automation.sh` — dry-run, backup y apply para staging (Postgres y SQLite soportados), con verificación y logs.
  - `docs/IMPORT_GENERATION_RUNBOOK.md` y `docs/IMPORT_GENERATION_CHECKLIST.md` — guía operativa para ejecución segura en staging.

Acciones realizadas

- Validación y tests unitarios agregados para `LlmResponseValidator` y flujos de `accept`.
- Modal frontend corregida (v-model sobre prop arreglado) y tests E2E/Unit actualizados.
- Backfill aplicado en ambiente local SQLite durante pruebas; scripts mejorados para fallback y permisos.

Pendientes / Siguientes pasos

1. Ejecutar migraciones y backfill en staging con backup validado (operador manual). Ver `docs/IMPORT_GENERATION_CHECKLIST.md`.
2. Abrir PR con cambios, incluir checklist de despliegue y pruebas en CI (no se subió el .env de producción).
3. Verificar `import_audit` en staging tras backfill y una ejecución de import controlada.
4. Plan de monitoreo post-enable (1–2 horas), revisar logs, métricas y entradas `import_audit`.

Notas de seguridad y privacidad

- `accepted_prompt` se guarda redacted por defecto; políticas server-side controlan visibilidad (`ScenarioPolicy::viewAcceptedPrompt`).
- Nunca subir `IMPORT_GENERATION=true` en producción sin aprobación; preferir PR controlada o feature-flag rollouts.

Contacto / responsabilidad

- Autor/integrador: equipo de Feature: `feature/workforce-planning-scenario-modeling`.
