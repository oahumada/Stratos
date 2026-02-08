#!/usr/bin/env bash
set -euo pipefail

# staging_backfill.sh
# Dry-run by default. Use --apply --backup /path/to/backup.sql to execute.

APPLY=false
BACKUP_FILE=""
FORCE=false

print_help() {
  cat <<EOF
Usage: $0 [--apply] --backup /path/to/backup.sql [--force]

--apply    Actually run migrations and backfill. Without --apply script prints planned commands.
--backup   Path to DB backup file (required when --apply).
--force    Skip interactive confirmation (use with care).

This script is intended to be run on the staging host with the application code checked out
and environment configured for staging (APP_ENV=staging, correct DB access, etc.).

Steps when --apply:
  1) Verify provided backup exists
  2) (Optional) Upload backup to safe storage if desired
  3) Run `php artisan migrate --force --env=staging`
  4) Run artisan backfill commands:
       php artisan backfill:competency-versions --apply
       php artisan backfill:role-versions --apply
  5) Confirm success, and monitor logs for anomalies

EOF
}

# parse args
while [[ $# -gt 0 ]]; do
  case "$1" in
    --apply) APPLY=true; shift ;;
    --backup) BACKUP_FILE="$2"; shift 2 ;;
    --force) FORCE=true; shift ;;
    -h|--help) print_help; exit 0 ;;
    *) echo "Unknown arg: $1"; print_help; exit 1 ;;
  esac
done

if [ "$APPLY" = false ]; then
  echo "DRY RUN: the following commands would be executed on staging if run with --apply and --backup specified:\n"
  echo "# 1. Ensure you have a DB backup (example for postgres):"
  echo "pg_dump -U \$PGUSER -h \$PGHOST \$PGDATABASE > /tmp/db-backup-"]
  echo "# 2. Run migrations"
  echo "php artisan migrate --force --env=staging"
  echo "# 3. Backfill competency/role versions"
  echo "php artisan backfill:competency-versions --apply"
  echo "php artisan backfill:role-versions --apply"
  echo "# 4. Optional: run custom migration backfill (if any)"
  echo "php artisan migrate --force --env=staging"
  echo "\nTo execute, re-run with: $0 --apply --backup /path/to/backup.sql"
  exit 0
fi

if [ -z "$BACKUP_FILE" ]; then
  echo "ERROR: --backup /path/to/backup.sql is required when --apply is set"
  exit 2
fi

if [ ! -f "$BACKUP_FILE" ]; then
  echo "ERROR: Backup file not found: $BACKUP_FILE"
  exit 2
fi

if [ "$FORCE" = false ]; then
  echo "About to apply migrations and backfill on STAGING. This is destructive if misused."
  echo "Backup file: $BACKUP_FILE"
  read -p "Type the word 'I_CONFIRM' to proceed: " CONFIRM
  if [ "$CONFIRM" != "I_CONFIRM" ]; then
    echo "Aborting. Confirmation not provided."
    exit 3
  fi
fi

set -x

# 1) verify backup exists (already checked). Optionally copy to a safe folder (left to operator)
# 2) (optional) restore or keep backup in safe storage

# 3) migrate
php artisan migrate --force --env=staging

# 4) backfill commands
php artisan backfill:competency-versions --apply
php artisan backfill:role-versions --apply

# 5) additional migration (backfill migration file will run during migrate)
# (already run above)

set +x

echo "Staging migrations & backfill completed. Monitor logs and check key data points (sample scenarios, import_audit entries)."
