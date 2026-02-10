# Runbook: Migraciones y Backfill para `accepted_prompt` + Importación automática

Fecha: 2026-02-07

Objetivo

- Ejecutar las migraciones y el backfill que persisten `accepted_prompt`/metadatos en `scenarios`.
- Habilitar (en staging) la importación automática de capacidades/competencias/habilidades desde la respuesta JSON del LLM (incubación) y validar el flujo end-to-end.

Resumen de cambios

- Nueva migración: `2026_02_07_120000_add_generation_fields_to_scenarios_table.php` (añade campos de procedencia en `scenarios`).
- Backfill: `2026_02_07_130000_backfill_accepted_prompt_metadata.php` (popula los campos desde `scenario_generations`).
- Nuevo servicio: `ScenarioGenerationImporter` (importa capacidades/competencias/skills en modo incubación).
- Feature flags en `config/features.php`: `import_generation`, `validate_llm_response`.

Precauciones previas

1. Hacer snapshot/backup completo de la DB de staging. No avanzar sin backup verificable.
2. Confirmar que `staging` usa las mismas estructuras de tablas (migraciones previas aplicadas).
3. Revisar que `config/features.php` en staging tenga `import_generation` = false por defecto.

Pre-flight checklist (must be completed before applying):

- Tomar backup y guardar en almacenamiento duradero. Ejemplo Postgres:
  ```bash
  pg_dump -Fc -h $DB_HOST -U $DB_USER $DB_DATABASE > /backups/staging_db_$(date +%F).dump
  ```
- Confirmar ventana de mantenimiento y notificar stakeholders.
- Verificar variables de entorno de staging: `APP_ENV=staging`, `DB_*` apuntan a staging, `IMPORT_GENERATION`/`VALIDATE_LLM_RESPONSE` configuradas según plan.

Ejecución (dry-run por defecto)

- Usar el script helper `scripts/staging_backfill.sh` en la raíz del repo. Dry-run:
  ```bash
  ./scripts/staging_backfill.sh
  ```
- Ejecutar (interactivo, requiere backup):
  ```bash
  ./scripts/staging_backfill.sh --apply --backup /path/to/staging_db.dump
  ```

Post-run checks

- Verificar que `scenarios` que referencian `source_generation_id` tienen `accepted_prompt` poblado.
- Revisar `import_audit` en `scenario_generations` para confirmar import attempts:
  ```bash
  php artisan tinker --env=staging
  >>> \App\Models\ScenarioGeneration::find(123)->metadata
  ```
- Monitorizar logs y endpoints de salud.

Comandos (en `src/`)

1. Obtener backup (ejemplo PostgreSQL):

```bash
# desde la carpeta raíz del proyecto
export TIMESTAMP=$(date +%Y%m%d%H%M)
pg_dump -Fc -h $DB_HOST -U $DB_USER $DB_DATABASE > /backups/staging_db_${TIMESTAMP}.dump
```

2. Poner el sistema en modo mantenimiento (opcional pero recomendado):

```bash
cd src
php artisan down --message="Despliegue migración backfill"
```

3. Ejecutar migraciones (incluye la migración que añade campos):

```bash
cd src
php artisan migrate --force
```

4. Ejecutar el backfill (si la migración no lo aplica automáticamente, ejecutar la migración específica o el seeder):

```bash
# Si la backfill está implementada como migración separada
php artisan migrate --path=/database/migrations/2026_02_07_130000_backfill_accepted_prompt_metadata.php --force

# Alternativa: si es una tarea/artisan command custom
php artisan scenarios:backfill-accepted-prompt --env=staging
```

5. Verificaciones básicas post-migración

- Asegurarse de que hay filas en `scenarios` con `source_generation_id` y `accepted_prompt` cuando corresponde:

```sql
SELECT id, source_generation_id, accepted_prompt IS NOT NULL AS has_prompt
FROM scenarios
WHERE source_generation_id IS NOT NULL
LIMIT 10;
```

- Verificar conteo comparativo con `scenario_generations`:

```sql
SELECT COUNT(*) FROM scenario_generations WHERE accepted_at IS NOT NULL;
SELECT COUNT(*) FROM scenarios WHERE source_generation_id IS NOT NULL;
```

6. Habilitar importación en staging (temporalmente) y flag de validación

Editar `src/config/features.php` en staging o exportar variables de entorno según la estrategia del proyecto.

```php
'import_generation' => env('IMPORT_GENERATION', false),
'validate_llm_response' => env('VALIDATE_LLM_RESPONSE', true),
```

En staging:

```bash
export IMPORT_GENERATION=true
export VALIDATE_LLM_RESPONSE=true
php artisan config:clear
php artisan cache:clear
```

7. Prueba funcional: generar → aceptar con import

- Simular una generación (o usar el GenerateWizard de staging).
- Ejecutar la petición `accept` con el flag `import=true`.

Ejemplo cURL (autenticar con un token válido):

```bash
curl -X POST \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  https://staging.example.com/api/strategic-planning/scenario-generations/{generation_id}/accept \
  -d '{"import": true}'
```

8. Verificar resultados en DB

- Consultar nuevas filas en `capabilities`, `competencies`, `skills` asociadas al `scenario_id` creado.

```sql
SELECT * FROM capabilities WHERE scenario_id = <SCENARIO_ID> LIMIT 20;
SELECT * FROM competencies WHERE capability_id IN (SELECT id FROM capabilities WHERE scenario_id = <SCENARIO_ID>);
SELECT * FROM skills WHERE competency_id IN (SELECT id FROM competencies WHERE capability_id IN (SELECT id FROM capabilities WHERE scenario_id = <SCENARIO_ID>));
```

9. Revisar interfaz: abrir la página `ScenarioDetail` y confirmar que el bloque "Incubadas" muestra las entidades.

10. Promover y auditoría: usar el endpoint `approve()` para promover y confirmar eventos de auditoría (si existen logs/eventos).

Rollback (si algo falla)

- Restaurar la snapshot/backup:

```bash
pg_restore -d $DB_DATABASE -h $DB_HOST -U $DB_USER /backups/staging_db_${TIMESTAMP}.dump
```

- Alternativa de rollback rápido: deshabilitar `import_generation` y revertir migraciones (solo si seguro):

```bash
php artisan migrate:rollback --step=1 --force
```

Checklist post-despliegue (marcar items):

- [ ] Backup verificado y accesible.
- [ ] Migraciones aplicadas sin errores.
- [ ] Backfill ejecutado y conteos validados.
- [ ] Importación automática probada con 3 escenarios de ejemplo.
- [ ] Interfaz `ScenarioDetail` muestra incubadas correctamente.
- [ ] Promover entidades funciona y audit logs registrados.
- [ ] `IMPORT_GENERATION` desactivado por defecto en producción hasta validación completa.

Notas

- Mantener `VALIDATE_LLM_RESPONSE=true` hasta mejorar el validador a un JSON Schema robusto.
- No ejecutar en producción sin pasar staging y sin auditoría implementada.
