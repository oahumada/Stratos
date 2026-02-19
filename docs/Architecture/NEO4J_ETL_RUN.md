# Guía de despliegue y ejecución: ETL Postgres → Neo4j (PoC)

Resumen

- Esta guía muestra pasos mínimos para poner en marcha el PoC ETL (`python_services/neo4j_etl.py`) y verificar su ejecución con `python_services/verify_etl.py`.

Requisitos

- Postgres accesible con datos (users, competencies, person_skill).
- Neo4j accesible (driver 5.x compatible).
- Variables de entorno configuradas (ver más abajo).

Variables de entorno necesarias

- `PG_DSN` (ej: `postgres://user:pass@host:5432/db`)
- `NEO4J_URI` (ej: `neo4j://neo4j-host:7687`)
- `NEO4J_USER`, `NEO4J_PASSWORD`
- Opcional: `LAST_SYNC` (ISO timestamp) — para depuración

Instalación rápida (local/development)

```bash
cd python_services
python3 -m venv venv
. venv/bin/activate
pip install -r requirements.txt
```

Ejecutar migración para `sync_checkpoints` (Laravel)

```bash
# Desde la raíz del proyecto
php artisan migrate --path=database/migrations/2026_02_19_120000_create_sync_checkpoints_table.php
```

Ejecución del ETL (PoC)

```bash
export PG_DSN='postgres://user:pass@host:5432/db'
export NEO4J_URI='neo4j://neo4j-host:7687'
export NEO4J_USER='neo4j'
export NEO4J_PASSWORD='supersecret'
python neo4j_etl.py
```

Verificación post-run

```bash
# Ejecutar el script de verificación
python verify_etl.py

# Salida 0 -> OK; 2 -> fallo
```

Automatización y despliegue

- Opción A (cron): crear un cronjob que ejecute `python neo4j_etl.py` periódicamente.
- Opción B (Laravel Job): crear un `Job` en Laravel (`AnalyzeTalentGap` o `RunNeo4jSync`) que llame al script (por ejemplo, con `Process::fromShellCommandline('python3 ...')`) y programarlo con `schedule()`.

- Opción C (recomendada): desplegar el microservicio FastAPI incluido en `python_services/app` y exponer endpoints HTTP para control y observabilidad.
    - Iniciar con `uvicorn python_services.app.main:app --host 0.0.0.0 --port 8001 --workers 1`.
    - Endpoints disponibles:
        - `GET /health` — healthcheck.
        - `POST /sync` — dispara el ETL en background (ACK inmediato).
        - `GET /status` — muestra `sync_checkpoints`.

Artisan Command & Job (Laravel)
- Se agregó el comando Artisan `neo4j:sync` para disparar el ETL desde el backend Laravel.
    - Uso: `php artisan neo4j:sync --via=script` (ejecuta `python_services/neo4j_etl.py` localmente)
    - Uso: `php artisan neo4j:sync --via=fastapi` (llama a la URL configurada en `NEO4J_ETL_SERVICE_URL`)
- También se añadió el `App\Jobs\RunNeo4jSyncJob` que implementa `ShouldQueue` y puede encolarse con `dispatch(new RunNeo4jSyncJob('fastapi'))`.

Recomendación operativa:
- Preferir `--via=fastapi` en entornos donde el microservicio Python esté desplegado (mayor observabilidad y separación de responsabilidades).
- En entornos donde no exista el servicio, usar `--via=script` o encolar el `RunNeo4jSyncJob` para ejecución asíncrona por `queue:work`.

Pautas operativas

- Ejecutar inicialmente con `LAST_SYNC` temprano (ej: `1970-01-01T00:00:00Z`) para poblar el grafo, luego dejar que el ETL actualice checkpoints.
- Revisar índices en Neo4j; crear constraints antes de cargas masivas.
- Monitoreo: enviar métricas de duración/errores a tu stack de observabilidad (Prometheus/Datadog).

Próximos pasos sugeridos

- Convertir PoC en servicio FastAPI para permitir control y observabilidad.
- Añadir tests automatizados y un job de integración que ejecute `verify_etl.py` tras cada despliegue de la pipeline.
