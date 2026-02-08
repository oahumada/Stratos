# Borrador de PR: Importación segura de generaciones (persistencia de `accepted_prompt` + backfill)

Resumen
- Este PR introduce la persistencia de la prompt aceptada (`accepted_prompt` y metadatos asociados) cuando un operador acepta una generación, añade auditoría de intentos de importación (`import_audit`), un importador opcional y validación del `llm_response`. También incluye UI para revisar/incubar/promover entidades generadas, tests unitarios/feature y un script + runbook para backfill en staging.

Cambios principales
- Backend:
  - `ScenarioGenerationController::accept()` persiste `accepted_prompt`, `accepted_prompt_redacted` y `accepted_prompt_metadata` en la creación de `scenarios`.
  - `LlmResponseValidator` devuelve errores estructurados y es usado condicionalmente por la importación.
  - `ScenarioGeneration.metadata.import_audit` se actualiza con entradas cronológicas que describen intentos, fallos y reportes del importador.
  - Nuevas rutas de API para promover incubadas y registrar vistas de `accepted_prompt`.
- Frontend:
  - `IncubatedReviewModal.vue` (nuevo) para revisar y promover incubadas.
  - `ScenarioDetail.vue` muestra `import_audit` y permite ver el `accepted_prompt` (y registra la vista via API).
  - Toggle y flag en el flujo de aceptación para controlar `import=true`.
- Operaciones:
  - `scripts/staging_backfill.sh` (dry-run por defecto) y `docs/IMPORT_GENERATION_RUNBOOK.md` y `docs/IMPORT_GENERATION_CHECKLIST.md` con instrucciones y checklist operativo.

Pruebas
- PHPUnit/Pest: tests unitarios y feature relevantes añadidos/actualizados (validador y audit import).  
- Vitest: tests frontend; la modal fue corregida y la suite de unit pasó.

Cómo revisar (lista rápida para reviewers)
- Revisar que `ScenarioGenerationController::accept()` persista los campos de provenance correctamente.
- Revisar la forma y contenido de `import_audit` (estructura y metadatos incluídos: attempted_by, attempted_at, result, report/error).
- Validación: `LlmResponseValidator` devuelve paths y mensajes legibles; comprobar tests unitarios.
- Frontend: abrir `ScenarioDetail` de un escenario con generación incubada y verificar modal de revisión y botón de ver `accepted_prompt`.
- Ejecutar scripts en dry-run y revisar la salida.

Pasos para desplegar / checklist mínimo (para incluir en la PR como referencia)
1. Crear backup completo del staging DB y tag del release.  
2. En entorno de staging: ejecutar dry-run:
```bash
./scripts/staging_backfill.sh
```
3. Revisar resultados y muestras (consultas SQL en runbook/checklist).  
4. Si OK, ejecutar con `--apply` y pasar ruta de backup:
```bash
./scripts/staging_backfill.sh --apply --backup /ruta/a/backup_staging_YYYY-MM-DD.dump
```
5. Verificar conteos y `import_audit` recientes; validar UI en staging (abrir escenarios sample).  
6. Solo tras verificación completa, habilitar feature flag `import_generation`.

Notas de seguridad
- No habilitar `import_generation` hasta haber ejecutado el backfill y verificado que `accepted_prompt` no contiene secretos.  
- En caso de detectar datos sensibles, seguir el apartado de rollback y redactado en el runbook.

Archivos clave incluidos
- `src/app/Http/Controllers/Api/ScenarioGenerationController.php`  
- `src/app/Services/LlmResponseValidator.php`  
- `src/resources/js/components/IncubatedReviewModal.vue`  
- `src/resources/js/pages/ScenarioPlanning/ScenarioDetail.vue`  
- `scripts/staging_backfill.sh`  
- `docs/IMPORT_GENERATION_RUNBOOK.md`  
- `docs/IMPORT_GENERATION_CHECKLIST.md`  

Comandos útiles (para abrir PR desde tu máquina)
```bash
git checkout -b feat/import-generation-provenance
git add .
git commit -m "feat(import): persist accepted_prompt + controlled import, audit and backfill runbook"
git push --set-upstream origin feat/import-generation-provenance
# Luego abrir PR en GitHub con el título y el body de este archivo
```

Solicito reviewers: @team-backend, @team-frontend, @ops

-- Fin del borrador de PR --
