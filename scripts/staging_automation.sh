#!/usr/bin/env bash
set -euo pipefail

# staging_automation.sh
# Wrapper interactivo para: backup, dry-run, apply, verificación y opcional parche para habilitar import_generation
# Uso: desde la raíz del repo: ./scripts/staging_automation.sh

ROOT_DIR=$(cd "$(dirname "$0")/.." && pwd)
# Respect BACKUP_DIR env var; default to repo-local ./backups to avoid permission issues
BACKUP_DIR="${BACKUP_DIR:-$ROOT_DIR/backups}"
DEFAULT_SQLITE_PATH="$ROOT_DIR/src/database/database.sqlite"
TIMESTAMP=$(date +%F_%H%M%S)

info(){ echo "[INFO] $*" >&2; }
err(){ echo "[ERROR] $*" >&2; }
prompt(){ read -r -p "$* "; }

ensure_executable(){
  if ! command -v "$1" >/dev/null 2>&1; then
    err "Required command '$1' not found. Install it or run the steps manually."
    exit 1
  fi
}

run_backup_postgres(){
  ensure_executable pg_dump
  mkdir -p "$BACKUP_DIR"
  DUMP_PATH="$BACKUP_DIR/staging_${TIMESTAMP}.dump"
  info "Creando backup Postgres en: $DUMP_PATH"
  pg_dump -Fc -h "${DB_HOST:-localhost}" -U "${DB_USER:-$(whoami)}" "${DB_NAME:-app}" -f "$DUMP_PATH"
  info "Backup creado: $DUMP_PATH"
}

run_backup_sqlite(){
  mkdir -p "$BACKUP_DIR"
  if [ -z "${SQLITE_PATH:-}" ]; then
    prompt "Ruta a la base de datos sqlite (ej. database/database.sqlite):"
    SQLITE_PATH=${REPLY}
  fi
  DUMP_PATH="$BACKUP_DIR/staging_${TIMESTAMP}.db"
  info "Creando backup SQLite en: $DUMP_PATH"
  if command -v sqlite3 >/dev/null 2>&1; then
    sqlite3 "$SQLITE_PATH" ".backup '$DUMP_PATH'"
    info "Backup creado usando sqlite3: $DUMP_PATH"
  else
    info "sqlite3 no disponible: usando copia de fichero como fallback"
    cp "$SQLITE_PATH" "$DUMP_PATH"
    info "Backup (cp) creado: $DUMP_PATH"
  fi
}

dry_run(){
  info "Ejecutando dry-run del backfill (no aplica cambios)..."
  bash "$ROOT_DIR/scripts/staging_backfill.sh" | tee "/tmp/backfill_dryrun_${TIMESTAMP}.log"
  info "Dry-run guardado en /tmp/backfill_dryrun_${TIMESTAMP}.log"
}

apply_run(){
  info "Aplicando backfill (esta operación modifica la DB)..."
  bash "$ROOT_DIR/scripts/staging_backfill.sh" --apply --backup "$DUMP_PATH" | tee "/tmp/backfill_apply_${TIMESTAMP}.log"
  info "Apply guardado en /tmp/backfill_apply_${TIMESTAMP}.log"
}

verify_checks(){
  info "Ejecutando verificaciones SQL básicas..."
  if [ "${DB_CONNECTION:-postgres}" = "sqlite" ]; then
    verify_checks_sqlite
    return
  fi

  if command -v psql >/dev/null 2>&1; then
    info "Conteo de scenarios con accepted_prompt no-null (ult. 1h):"
    psql -h "${DB_HOST:-localhost}" -U "${DB_USER:-$(whoami)}" -d "${DB_NAME:-app}" -c "SELECT COUNT(*) FROM scenarios WHERE accepted_prompt IS NOT NULL AND updated_at > NOW() - INTERVAL '1 hour';"

    info "Últimas entradas de import_audit (top 20):"
    psql -h "${DB_HOST:-localhost}" -U "${DB_USER:-$(whoami)}" -d "${DB_NAME:-app}" -c "SELECT id, metadata->'import_audit' AS import_audit FROM scenario_generations WHERE metadata->'import_audit' IS NOT NULL ORDER BY updated_at DESC LIMIT 20;"
  else
    err "psql no disponible: ejecutar manualmente las queries listadas en docs/IMPORT_GENERATION_CHECKLIST.md"
  fi
}

verify_checks_sqlite(){
  if [ -z "${SQLITE_PATH:-}" ]; then
    prompt "Ruta a la base de datos sqlite (ej. database/database.sqlite):"
    SQLITE_PATH=${REPLY}
  fi
  info "Conteo de scenarios con accepted_prompt no-null (ult. 1h):"
  if command -v sqlite3 >/dev/null 2>&1; then
    sqlite3 -header -column "$SQLITE_PATH" "SELECT COUNT(*) FROM scenarios WHERE accepted_prompt IS NOT NULL AND datetime(updated_at) > datetime('now','-1 hour');"

    info "Últimas entradas de import_audit (top 20):"
    sqlite3 -header -column "$SQLITE_PATH" "SELECT id, json_extract(metadata, '$.import_audit') AS import_audit FROM scenario_generations WHERE json_extract(metadata, '$.import_audit') IS NOT NULL ORDER BY updated_at DESC LIMIT 20;"
  else
    err "sqlite3 no disponible: no puedo ejecutar queries automáticas."
    info "Puedes ejecutar manualmente las siguientes comprobaciones con sqlite3 una vez instalado:"
    echo "  sqlite3 -header -column $SQLITE_PATH \"SELECT COUNT(*) FROM scenarios WHERE accepted_prompt IS NOT NULL AND datetime(updated_at) > datetime('now','-1 hour');\""
    echo "  sqlite3 -header -column $SQLITE_PATH \"SELECT id, json_extract(metadata, '$.import_audit') AS import_audit FROM scenario_generations WHERE json_extract(metadata, '$.import_audit') IS NOT NULL ORDER BY updated_at DESC LIMIT 20;\""
    info "Mientras tanto, verificación básica por fichero:"
    ls -lh "$SQLITE_PATH" || true
  fi
}

prepare_enable_flag_patch(){
  FEATURES_FILE="$ROOT_DIR/src/config/features.php"
  if [ ! -f "$FEATURES_FILE" ]; then
    err "No se encontró $FEATURES_FILE; no puedo preparar el parche para habilitar import_generation."
    return 1
  fi

  PATCH_FILE="/tmp/enable_import_generation_${TIMESTAMP}.patch"
  info "Generando parche para habilitar 'import_generation' en $FEATURES_FILE -> $PATCH_FILE"

  # crear copia temporal con sustitución segura (no sobreescribe aún)
  TMP_FILE="/tmp/features_${TIMESTAMP}.php"
  cp "$FEATURES_FILE" "$TMP_FILE"

  # intentar reemplazar 'import_generation' => false => true
  perl -0777 -pe "s/'import_generation'\s*=>\s*false\s*,/'import_generation' => true,/s" "$TMP_FILE" > "$TMP_FILE.tmp" && mv "$TMP_FILE.tmp" "$TMP_FILE"

  # crear diff
  diff -u "$FEATURES_FILE" "$TMP_FILE" > "$PATCH_FILE" || true
  if [ -s "$PATCH_FILE" ]; then
    info "Parche creado en $PATCH_FILE"
    echo "$PATCH_FILE"
  else
    err "No se detectó cambio (posiblemente ya está habilitado)."; rm -f "$PATCH_FILE" "$TMP_FILE"; return 1
  fi
}

apply_patch_to_repo(){
  PATCH_PATH="$1"
  info "Aplicando parche localmente (git apply) -> $PATCH_PATH"
  git apply --index "$PATCH_PATH"
  info "Parche aplicado y staged. Revisa y commit/ push según política."
}

restore_backup(){
  if [ "${DB_CONNECTION:-postgres}" = "sqlite" ]; then
    restore_backup_sqlite
    return
  fi

  if prompt "¿Deseas restaurar desde backup $DUMP_PATH ahora? (y/N):"; then
    info "Restaurando desde: $DUMP_PATH"
    if command -v pg_restore >/dev/null 2>&1; then
      pg_restore -h "${DB_HOST:-localhost}" -U "${DB_USER:-$(whoami)}" -d "${DB_NAME:-app}" --clean "$DUMP_PATH"
      info "Restauración completada"
    else
      err "pg_restore no disponible. Ejecuta la restauración manualmente."
    fi
  else
    info "Restauración cancelada por el usuario."
  fi
}

restore_backup_sqlite(){
  if [ -z "${SQLITE_PATH:-}" ]; then
    prompt "Ruta a la base de datos sqlite (ej. database/database.sqlite):"
    SQLITE_PATH=${REPLY}
  fi
  if prompt "¿Deseas restaurar la base sqlite desde $DUMP_PATH ahora? (y/N):"; then
    info "Deteniendo la aplicación (asegúrate de hacerlo manualmente si corresponde)."
    info "Restaurando: copiando $DUMP_PATH -> $SQLITE_PATH"
    cp "$DUMP_PATH" "$SQLITE_PATH"
    info "Restauración completada (asegúrate de reiniciar la app)."
  else
    info "Restauración cancelada por el usuario."
  fi
}

main(){
  info "INICIANDO: wrapper interactivo de staging backfill"

  # pedir variables DB si no provistas
  prompt "DB_CONNECTION (postgres/sqlite) (enter para postgres):"
  DB_CONNECTION=${REPLY:-postgres}
  if [ "$DB_CONNECTION" = "sqlite" ]; then
    prompt "Ruta a la base de datos sqlite (ej. database/database.sqlite) (enter para usar $DEFAULT_SQLITE_PATH):"
    SQLITE_PATH=${REPLY:-$DEFAULT_SQLITE_PATH}
  else
    prompt "DB_HOST (enter para localhost):"
    DB_HOST=${REPLY:-localhost}
    prompt "DB_USER (enter para $(whoami)) :"
    DB_USER=${REPLY:-$(whoami)}
    prompt "DB_NAME (enter para app):"
    DB_NAME=${REPLY:-app}
  fi

  # backup
  info "Se va a crear un backup antes de aplicar."
  if prompt "¿Crear backup ahora? (y/N):" && [[ "$REPLY" =~ ^[Yy]$ ]]; then
    if [ "${DB_CONNECTION:-postgres}" = "sqlite" ]; then
      run_backup_sqlite
    else
      run_backup_postgres
    fi
  else
    err "Backup obligatorio. Cancelando."; exit 1
  fi

  # dry-run
  dry_run

  if prompt "¿Revisada la salida del dry-run y deseas aplicar los cambios? (y/N):" && [[ "$REPLY" =~ ^[Yy]$ ]]; then
    apply_run
  else
    info "Apply cancelado por el usuario. Saliendo."; exit 0
  fi

  # verificaciones
  verify_checks

  if prompt "¿Está conforme con los resultados y desea habilitar la importación automática (generar -> importar)? (y/N):" && [[ "$REPLY" =~ ^[Yy]$ ]]; then
    # preparar parche para habilitar flag
    PATCH_PATH=$(prepare_enable_flag_patch || true) || PATCH_PATH=""
    if [ -n "$PATCH_PATH" ]; then
      if prompt "¿Aplicar el parche localmente y stagear el cambio? (recomendado: no hacer push automático) (y/N):" && [[ "$REPLY" =~ ^[Yy]$ ]]; then
        apply_patch_to_repo "$PATCH_PATH"
        info "Parche aplicado. Revisar 'git status' y hacer commit/push cuando corresponda."
      else
        info "Parche creado en: $PATCH_PATH. Revísalo y aplícalo manualmente si procede."
      fi
    else
      info "No se creó parche; quizá ya estaba habilitado o ocurrió un error."
    fi
  else
    info "No se habilitó la importación automática por decisión del usuario."
  fi

  info "Proceso terminado. Si necesitas restaurar, usa la opción de restauración." 
  if prompt "¿Deseas ejecutar restauración desde el backup creado anteriormente? (y/N):" && [[ "$REPLY" =~ ^[Yy]$ ]]; then
    restore_backup
  fi
}

main
