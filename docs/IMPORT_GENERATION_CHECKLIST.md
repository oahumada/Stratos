# Lista de comprobación: Migración y Backfill de Importación de Generaciones

Este documento es una checklist operativa para ejecutar el backfill/migración de datos relacionados con `scenario_generations` → poblado de `accepted_prompt`, `accepted_prompt_metadata` e `import_audit` en staging.

USO: seguir cada paso en orden. Si no hay operador disponible, ejecutar desde una sesión con privilegios y registrar cada resultado.

1. Preflight (obligatorio)

- Confirmar el commit/tag desplegado en staging y registrar SHA.
- Confirmar espacio en disco en el servidor de staging: `df -h`.
- Confirmar que las variables de entorno de staging son correctas (DB_HOST, DB_NAME, APP_ENV, etc.).
- Hacer backup completo de la base de datos de staging y guardar ruta del dump.
  - Ejemplo (Postgres): `pg_dump -Fc -h $HOST -U $USER $DB > /backups/staging_$(date +%F).dump`
- Hacer snapshot o tag del release (código) desplegado.

2. Dry-run (verificación sin aplicar)

- Ejecutar el script en modo dry-run (por defecto el script muestra y no aplica):
  ```bash
  ./scripts/staging_backfill.sh
  ```
- Revisar la salida y confirmar qué tablas/registros serían afectados.
- Revisar logs locales del script (si aparecen rutas temporales) y anotar cualquier error.

3. Validación previa a aplicar

- Tomar una muestra de `scenario_generations` que deberían traer `accepted_prompt` y revisar la estructura JSON.
  - Consulta ejemplo:
    ```sql
    SELECT id, metadata->'llm_response' AS llm_response
    FROM scenario_generations
    WHERE metadata->'llm_response' IS NOT NULL
    LIMIT 5;
    ```
- Confirmar que `llm_response` cumple el esquema mínimo (campos de capabilities/competencies/skills) o que el validador tolera pequeñas variaciones.

4. Ejecutar (aplicar cambios)

- Ejecutar con backup y bandera `--apply` en staging:
  ```bash
  ./scripts/staging_backfill.sh --apply --backup /ruta/a/backup_staging_YYYY-MM-DD.dump
  ```
- El script debe:
  - Ejecutar migraciones pendientes necesarias.
  - Iterar registros antiguos y poblar campos en `scenarios` desde `scenario_generations` (accepted_prompt y metadata).
  - Insertar/actualizar `import_audit` en `scenario_generations` con entradas `backfill` y resultado.

5. Verificaciones post-aplicación (inmediatas)

- Comprobaciones básicas:
  - Contar registros con `accepted_prompt` recién poblado:
    ```sql
    SELECT COUNT(*) FROM scenarios WHERE accepted_prompt IS NOT NULL AND updated_at > NOW() - INTERVAL '1 hour';
    ```
  - Revisar `import_audit` recientes:
    ```sql
    SELECT id, metadata->'import_audit' AS import_audit
    FROM scenario_generations
    WHERE metadata->'import_audit' IS NOT NULL
    ORDER BY updated_at DESC
    LIMIT 20;
    ```
- Validar integridad funcional en la app (en staging):
  - Abrir 3 escenarios sample y verificar que el `accepted_prompt` aparece en la UI y que la modal de incubadas muestra datos.
  - Ejecutar las pruebas unitarias rápidas si aplica.

6. Rollback (si hay fallos)

- Restaurar la base de datos desde el dump creado antes de aplicar:
  - Ejemplo Postgres:
    ```bash
    pg_restore -h $HOST -U $USER -d $DB /backups/staging_YYYY-MM-DD.dump --clean
    ```
- Validar que la restauración devolvió el estado previo (repetir verificaciones básicas de conteo y muestreo).

7. Habilitar feature flag (opcional, sólo si backfill correcto)

- La importación automática está protegida por la feature flag `import_generation`.
- Activar la flag en el sistema de flags que uses (archivo `src/config/features.php` o el control remoto que tengan).
- Alternativamente, habilitar progresivamente por tenant/organización si la plataforma lo soporta.

8. Monitorización y comunicación

- Revisar logs de la aplicación durante las siguientes 2 horas por errores relacionados (`importer`, `ScenarioGenerationImporter`, `LlmResponseValidator`).
- Enviar notificación al canal de equipo (ej. Slack) con resumen: backup path, commit SHA, número de registros modificados, y cualquier incidencia.

9. Notas de seguridad y privacidad

- Revisar que `accepted_prompt` no exponga secretos. Si se detectan cadenas sensibles, redactarlas y reportarlo inmediatamente.

10. Tiempo estimado

- Preparación y backup: 10–30 minutos (depende del DB).
- Dry-run y revisión de salida: 20–45 minutos.
- Ejecución y verificación: 15–60 minutos (según volumen).

-- Fin de la checklist --

Referencia: `docs/IMPORT_GENERATION_RUNBOOK.md` y el script `scripts/staging_backfill.sh` para detalles de implementación y opciones.
