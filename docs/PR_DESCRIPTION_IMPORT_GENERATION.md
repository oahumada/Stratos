# PR: Auto-import de generación LLM (incubación) + Migración/backfill

Resumen

- Persiste la proveniencia de generaciones aceptadas en `scenarios` (`source_generation_id`, `accepted_prompt`, `accepted_prompt_redacted`, `accepted_prompt_metadata`).
- Añade un importer opcional `ScenarioGenerationImporter` que crea capacidades/competencias/skills en modo incubación desde la respuesta JSON del LLM.
- Añade flags de feature en `config/features.php`: `import_generation`, `validate_llm_response`.
- Incluye migración para añadir campos y una migración backfill (no ejecutada automáticamente en producción).

Archivos clave

- `database/migrations/2026_02_07_120000_add_generation_fields_to_scenarios_table.php`
- `database/migrations/2026_02_07_130000_backfill_accepted_prompt_metadata.php` (backfill)
- `src/app/Services/ScenarioGenerationImporter.php`
- `src/app/Services/LlmResponseValidator.php`
- `src/app/Http/Controllers/Api/ScenarioGenerationController.php` (accept() integrado con import opcional)
- `src/resources/js/pages/ScenarioPlanning/ScenarioDetail.vue` (UX para ver incubadas y promover)
- `src/config/features.php`

Motivación y seguridad

- La importación automática está DESHABILITADA por defecto; se activa con `IMPORT_GENERATION=true` en staging para validación.
- Antes de habilitar en producción se debe: ejecutar backfill en staging, validar comportamiento con múltiples escenarios y añadir auditoría para operaciones de import/promote.

Cómo probar en staging (resumen rápido)

1. Backup DB.
2. Aplicar migraciones y backfill.
3. Habilitar `IMPORT_GENERATION=true` y `VALIDATE_LLM_RESPONSE=true` localmente o en staging.
4. Generar una respuesta LLM válida (usar GenerateWizard o fixture).
5. Llamar `accept` con `import=true` y verificar filas nuevas en `capabilities/competencies/skills` y la UI `ScenarioDetail`.

Checklist para merge (marcar antes de aceptar PR)

- [ ] Tests unitarios/feature añadidos y ejecutados localmente (feature tests pasaron).
- [ ] Runbook creado: `docs/IMPORT_GENERATION_RUNBOOK.md`.
- [ ] Backup & staging plan aprobado.
- [ ] Auditoría prevista para promote/import (si no, incluir en follow-up).

Notas para reviewer

- Revisar idempotencia del `ScenarioGenerationImporter` (usa `firstOrCreate` y comprobaciones de pivots, pero hay que validar casos límite).
- Revisar `LlmResponseValidator`: actualmente ligero; recomendamos transformar en JSON Schema en próxima iteración.
