# Plan de prueba: Flujo completo - Escenarios con asistencia LLM

Fecha: 2026-02-08

Objetivo

- Ver en navegador el flujo completo de generación de escenarios (Paso 1): crear prompt via GenerateWizard, previsualizar, aceptar (`accept()`), y validar que `accepted_prompt`, `accepted_prompt_redacted`, `accepted_prompt_metadata` y `import_audit` se persistan correctamente.
- Si la importación/incubación se activa (casilla `import=true`), validar que las entidades propuestas (roles, competencias, mappings) queden incubadas y sean visibles en la UI de revisión; probar promoverlas a entidades reales.
- Intentar ajustar Paso 2 para que acepte propuestas de roles y relaciones generadas por el LLM y verificar la matriz Paso 2 con propuestas pre-pobladas.

Precondiciones

- Ejecutar migraciones en `src/` y levantar servidor de desarrollo:

```bash
cd src
composer install
npm install
php artisan migrate --seed
npm run dev
php artisan serve --host=127.0.0.1 --port=8001
```

- Asegurar `IMPORT_GENERATION` en `src/.env` según lo desees para pruebas (preferible: `false` si pruebas manuales, `true` sólo para pruebas controladas).
- Usar un navegador con la cuenta admin de E2E (ver `src/database/seeders/E2ESeeder.php`) o crear un usuario con permisos para `accept`.

Pasos: Paso 1 (flujo LLM)

1. Abrir la página del escenario y lanzar `GenerateWizard` (cabecera `ScenarioDetail` → `mdi-robot`).
2. Completar pasos del wizard, clicar `Preview` y verificar el contenido del prompt en el cuadro `PreviewConfirm`.
3. Marcar la casilla `Aceptar e importar automáticamente` según quieras probar `import=true`.
4. Ejecutar `Accept` y observar:
   - En la respuesta API: `201` y payload con `scenario_id` o estado `accepted`.
   - En UI: redirección al nuevo `ScenarioDetail` draft.
   - En DB: `scenarios.accepted_prompt` contiene el prompt; `scenario_generations.metadata` actualizado con `accepted_by`, `accepted_at`.
   - En `scenario_generations.metadata.import_audit` aparece un nuevo intento con `attempted_by`, `attempted_at`, `result`.

Verificaciones rápidas (devtools / DB)

- Usar inspector de red (Network) para revisar `POST /api/strategic-planning/scenarios/generate/{id}/accept`.
- Desde consola SQL (SQLite o Postgres) revisar:

```sql
SELECT accepted_prompt IS NOT NULL AS has_prompt FROM scenarios WHERE id = <created_id>;
SELECT json_extract(metadata, '$.import_audit') FROM scenario_generations WHERE id = <gen_id>;
```

Pasos: Paso 1 → Incubación UI

1. Si marcaste `import=true`, abrir `ScenarioDetail` y lanzar `IncubatedReviewModal`.
2. Revisar entidades propuestas (roles, competencias, relaciones). Verificar que cada item tenga botón `Promote` / `Editar`.
3. Promover una propuesta y validar:
   - Se crea la entidad real (`roles`, `competencies`, `role_versions`, mapping entries).
   - `import_audit` se actualiza con `import_status: succeeded` o `failed` y `report` detallado.

Ajustes: Paso 2 (aceptar propuestas automáticamente)

- Objetivo: habilitar una opción para que la matriz Paso 2 se pre-popule con `roles` y `mappings` propuestos desde la importación.
- Trabajo mínimo sugerido:
  1. Backend: crear endpoint `POST /api/scenarios/{id}/imports/commit` que aplique incubadas a `scenario_roles` y `scenario_role_skills`.
  2. Frontend: en `ScenarioDetail` añadir botón `Aplicar propuestas Paso 2` que haga llamada al endpoint y muestre resultados (toasts/errors).
  3. E2E: añadir un test Playwright que genere, acepte con `import=true`, abra `ScenarioDetail`, aplique propuestas y compruebe que la matriz Paso 2 contiene las filas pre-pobladas.

Comandos útiles para diagnóstico

```bash
# ver últimas entradas import_audit
cd src
php artisan tinker --execute="\DB::table('scenario_generations')->orderBy('id','desc')->limit(5)->get()->pluck('metadata')"

# verificar scenarios con accepted_prompt
php -r "require 'src/bootstrap/autoload.php'; print_r(\DB::table('scenarios')->whereNotNull('accepted_prompt')->take(5)->get());"
```

Resultados esperados y criterios de éxito

- El flujo de generación → preview → accept crea un `scenario` draft con `accepted_prompt` y enlaza metadata de aceptación en `scenario_generations`.
- Si se pidió importación automática y la feature-flag lo permite, las entidades propuestas quedan en `metadata.incubated` y accesibles en la UI de revisión.
- Al aplicar/promover una propuesta, las entidades reales aparecen en Paso 2 y la matriz muestra filas pre-pobladas.

Notas y riesgos

- No habilites `IMPORT_GENERATION=true` en prod sin revisión de auditoría y plan de rollback.
- Para cambios masivos en Paso 2, prepara backups y pruebas en staging antes de aplicar en producción.

\*\*\* Fin del plan de prueba
