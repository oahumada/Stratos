# Runbook: Backfill de `competency_versions`

Propósito

- Ejecutar el comando `backfill:competency-versions` en un entorno de staging para crear versiones históricas de `competency_versions` a partir de `discovered_in_scenario_id`.

Resumen rápido

1. Preparar staging: backup + dependencias.
2. Ejecutar dry-run (sin efectos) y revisar salida.
3. Ejecutar con `--apply` si todo OK.
4. Verificar datos y tests mínimos.
5. Rollback (si necesario) usando snapshot/DB dump o eliminando registros marcados por el backfill.

Prerequisitos

- Acceso SSH al servidor de staging y permisos para ejecutar `php artisan` y crear dumps.
- Variables de entorno de staging (`.env`) correctamente configuradas (DB, S3, etc.).
- Copia de seguridad reciente de la base de datos (snapshot o dump).
- Ventana de mantenimiento planificada (si es un entorno con tráfico crítico).
- Account/role con permisos para revertir (DB restore).

Comandos útiles (desde la raíz del repo)

- Obtener branch correcto y dependencias:

```bash
git fetch origin
git checkout feature/scenario-planning/paso-2
composer install --no-dev --optimize-autoloader
npm ci --silent # si fuese necesario para assets
php artisan migrate --force # si hay migraciones pendientes EN STAGING solo si es parte del plan
```

- Crear backup DB (ejemplo MySQL):

```bash
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > /tmp/db-backup-before-backfill.sql
# o usar snapshot del proveedor (recommended)
```

Dry-run (simular sin crear registros)

```bash
php artisan backfill:competency-versions
```

- El comando imprimirá un resumen por `scenario_id` y contará cuántos `competency_versions` crearían. Revisar output y muestreos.

Apply (crear registros)

```bash
php artisan backfill:competency-versions --apply
```

Verificaciones post-apply

- Comprobar conteo total esperado (comparar con dry-run):

```sql
-- Postgres
SELECT COUNT(*) FROM competency_versions WHERE (metadata->>'source') = 'backfill';
-- MySQL (JSON_EXTRACT)
SELECT COUNT(*) FROM competency_versions WHERE JSON_EXTRACT(metadata, '$.source') = '"backfill"';
```

- Revisar ejemplos:
  - seleccionar 5 filas con `metadata->>'scenario_id' = <ID>` y validar `original_competency_id`, `version_group_id` y campos `bars`/`notes`.

Rollback

- Opción A (recomendado): restaurar DB desde snapshot/dump creado antes del paso.

- Opción B (quirúrgico) — eliminar registros creados por backfill (use con precaución):

```sql
-- Postgres
DELETE FROM competency_versions WHERE (metadata->>'source') = 'backfill' AND (metadata->>'scenario_id') = '123';

-- MySQL
DELETE FROM competency_versions WHERE JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.source')) = 'backfill' AND JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.scenario_id')) = '123';
```

Notas de seguridad y precauciones

- Nunca ejecutar `--apply` sin backup y revisión del dry-run.
- Ejecutar primero en un entorno de staging que replica el tamaño/datos reales.
- Si hay jobs/consumers que dependen de `competency_versions`, detener cola temporalmente o planificar ventana.

Tips de verificación rápida

- Ejecutar tests relevantes:

```bash
# Ejecutar solo el test feature que verifica el backfill (local)
php artisan test --filter BackfillCompetencyVersionsTest
```

- Verificar que el API de versiones responde:

```bash
# ejemplo: obtener versiones de una competencia
curl -H "Authorization: Bearer $TOKEN" "https://staging.example.com/api/competencies/321/versions"
```

Contacto en caso de emergencia

- Equipo Backend: @backend-team
- DBA/Infra: @dba-oncall

Registro de ejecución

- Al ejecutar, copiar la salida del dry-run y del apply, y pegarlas en la entrada de despliegue/issue para auditoría.

---
