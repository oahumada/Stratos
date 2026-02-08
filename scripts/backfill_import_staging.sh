#!/usr/bin/env bash
set -euo pipefail

# Runbook script: migración + backfill de accepted_prompt y validación
# Uso: Ejecutar desde la raíz del repo (no dentro de src/) como usuario con acceso a la DB
# Requiere: `pg_dump`/`pg_restore` o equivalente si usas Postgres. Ajustar según DB.

ROOT_DIR=$(cd "$(dirname "$0")/.." && pwd)
SRC_DIR="$ROOT_DIR/src"

echo "Runbook: backfill/import staging"

read -p "¿Estás en el entorno staging y tienes backup/permiso para ejecutar esto? (yes/no) " confirm
if [[ "$confirm" != "yes" ]]; then
  echo "Cancelado. Responde 'yes' para continuar." && exit 1
fi

# Variables: adapta según tu entorno
read -p "DB_HOST (default: localhost): " DB_HOST; DB_HOST=${DB_HOST:-localhost}
read -p "DB_USER (default: postgres): " DB_USER; DB_USER=${DB_USER:-postgres}
read -p "DB_NAME (default: staging_db): " DB_NAME; DB_NAME=${DB_NAME:-staging_db}
read -p "BACKUP_DIR (default: /backups): " BACKUP_DIR; BACKUP_DIR=${BACKUP_DIR:-/backups}

TIMESTAMP=$(date +%Y%m%d%H%M)
BACKUP_FILE="$BACKUP_DIR/staging_db_${TIMESTAMP}.dump"

echo
echo "1) Crear backup (Postgres ejemplo)"
echo "   Si usas otro motor adapta este paso. Asegúrate de que PGPASSWORD esté definido o .pgpass configurado."
echo
read -p "Continuar con pg_dump backup a $BACKUP_FILE ? (yes/no) " do_backup
if [[ "$do_backup" == "yes" ]]; then
  mkdir -p "$BACKUP_DIR"
  echo "Ejecutando: pg_dump -Fc -h $DB_HOST -U $DB_USER -d $DB_NAME -f $BACKUP_FILE"
  pg_dump -Fc -h "$DB_HOST" -U "$DB_USER" -d "$DB_NAME" -f "$BACKUP_FILE"
  echo "Backup creado: $BACKUP_FILE"
else
  echo "Saltando backup. Asegúrate manualmente de tener uno antes de continuar." 
fi

echo
echo "2) Poner app en mantenimiento (desde src/)"
cd "$SRC_DIR"
php artisan down --message="Despliegue migración backfill"

echo
echo "3) Ejecutar migraciones"
php artisan migrate --force

echo
read -p "¿Ejecutar la migración de backfill específica ahora? (recommended) (yes/no) " run_backfill
if [[ "$run_backfill" == "yes" ]]; then
  php artisan migrate --path=/database/migrations/2026_02_07_130000_backfill_accepted_prompt_metadata.php --force
fi

echo
echo "4) Limpiar cache/config y recargar"
php artisan config:clear || true
php artisan cache:clear || true

echo
echo "5) Verificaciones básicas (sólo mostrar queries). Puedes ejecutar las queries con psql si está disponible."
SQL1="SELECT id, source_generation_id, (accepted_prompt IS NOT NULL) AS has_prompt FROM scenarios WHERE source_generation_id IS NOT NULL LIMIT 10;"
SQL2="SELECT COUNT(*) FROM scenario_generations WHERE prompt IS NOT NULL;"
SQL3="SELECT COUNT(*) FROM scenarios WHERE source_generation_id IS NOT NULL AND accepted_prompt IS NOT NULL;"

echo "Query ejemplo 1: $SQL1"
echo "Query ejemplo 2: $SQL2"
echo "Query ejemplo 3: $SQL3"

if command -v psql >/dev/null 2>&1; then
  echo
  read -p "psql detectado: ejecutar las queries de verificación ahora? (yes/no) " run_psql
  if [[ "$run_psql" == "yes" ]]; then
    PGPASSWORD=${PGPASSWORD:-${PGPASSWORD}} psql -h "$DB_HOST" -U "$DB_USER" -d "$DB_NAME" -c "$SQL1"
    PGPASSWORD=${PGPASSWORD:-${PGPASSWORD}} psql -h "$DB_HOST" -U "$DB_USER" -d "$DB_NAME" -c "$SQL2"
    PGPASSWORD=${PGPASSWORD:-${PGPASSWORD}} psql -h "$DB_HOST" -U "$DB_USER" -d "$DB_NAME" -c "$SQL3"
  else
    echo "No se ejecutaron verificaciones SQL." 
  fi
else
  echo "psql no detectado: copia las queries arriba y ejecútalas manualmente en tu consola DB." 
fi

echo
echo "6) Habilitar feature flags temporales en staging (ej. via export env)
 - Esto no modifica .env permanentemente; exporta variables en la sesión donde corras artisan o actualiza .env manualmente.
"
read -p "¿Exportar IMPORT_GENERATION=true y VALIDATE_LLM_RESPONSE=true para la sesión actual y limpiar config? (yes/no) " do_flags
if [[ "$do_flags" == "yes" ]]; then
  export IMPORT_GENERATION=true
  export VALIDATE_LLM_RESPONSE=true
  php artisan config:clear
  php artisan cache:clear
  echo "Flags aplicadas en sesión: IMPORT_GENERATION=true, VALIDATE_LLM_RESPONSE=true"
else
  echo "No se cambiaron flags; hazlo manualmente si necesitas habilitar la importación." 
fi

echo
echo "7) Probar flujo básico (ejemplo cURL). Sustituye TOKEN y generation_id"
cat <<'CURL'
curl -X POST \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  https://staging.example.com/api/strategic-planning/scenario-generations/{generation_id}/accept \
  -d '{"import": true}'
CURL

echo
echo "8) Traer app fuera de mantenimiento"
php artisan up

echo
echo "Runbook terminado. Revisa las verificaciones y, si todo OK, deshabilita IMPORT_GENERATION en producción hasta validación completa." 

exit 0
