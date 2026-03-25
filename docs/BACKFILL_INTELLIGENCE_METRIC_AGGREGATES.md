# Backfill de Intelligence Metric Aggregates

Guía operativa del comando de backfill histórico de agregados de métricas de inteligencia.

## ¿Qué hace este backfill?

El comando `backfill:intelligence-metric-aggregates` reconstruye (o recalcula) los registros de la tabla `intelligence_metric_aggregates` a partir de los datos crudos de `intelligence_metrics`.

- Fuente: `intelligence_metrics` (eventos/métricas crudas).
- Destino: `intelligence_metric_aggregates` (resumen diario por tipo/origen/org).
- Base técnica: `App\Services\IntelligenceMetricsAggregator`.
- Persistencia: `updateOrCreate` (idempotente por llave única).

### Idempotencia

El proceso es idempotente: ejecutar el mismo rango más de una vez no duplica filas.

La unicidad del agregado está dada por:

- `organization_id`
- `metric_type`
- `source_type`
- `date_key`

## Firma del comando

```bash
php artisan backfill:intelligence-metric-aggregates \
  [--from=YYYY-MM-DD] \
  [--to=YYYY-MM-DD] \
  [--organization_id=ID] \
  [--apply]
```

## Parámetros

- `--from`: fecha inicial (inclusive), formato `Y-m-d`.
- `--to`: fecha final (inclusive), formato `Y-m-d`.
- `--organization_id`: limita el cálculo a una organización específica.
- `--apply`: aplica persistencia en DB.

### Modo por defecto (seguro)

Si no se incluye `--apply`, el comando corre en **dry-run** (no escribe datos).

## Comportamiento cuando faltan fechas

Si `--from` o `--to` no se envían, el comando toma límites automáticamente desde `intelligence_metrics`:

- `--from` ausente: `MIN(created_at)`
- `--to` ausente: `MAX(created_at)`

Si no existen métricas de origen, el comando finaliza con error de validación.

## Ejemplos de uso

### 1) Simulación segura (dry-run)

```bash
php artisan backfill:intelligence-metric-aggregates --from=2026-03-01 --to=2026-03-25
```

### 2) Aplicar para todas las organizaciones

```bash
php artisan backfill:intelligence-metric-aggregates --from=2026-03-01 --to=2026-03-25 --apply
```

### 3) Aplicar para una sola organización

```bash
php artisan backfill:intelligence-metric-aggregates --from=2026-03-10 --to=2026-03-15 --organization_id=12 --apply
```

### 4) Auto-rango completo (según datos disponibles)

```bash
php artisan backfill:intelligence-metric-aggregates --apply
```

## Flujo recomendado en staging/producción

1. Ejecutar dry-run para validar rango/volumen.
2. Revisar salida (`Source metrics in range`).
3. Ejecutar con `--apply` en ventana controlada.
4. Repetir el mismo comando para comprobar idempotencia.

## Validación post-ejecución

### Conteo rápido de agregados por rango

```sql
SELECT date_key, metric_type, COUNT(*) AS rows
FROM intelligence_metric_aggregates
WHERE date_key BETWEEN '2026-03-01' AND '2026-03-25'
GROUP BY date_key, metric_type
ORDER BY date_key, metric_type;
```

### Verificar una organización puntual

```sql
SELECT date_key, metric_type, source_type, total_count, success_count, success_rate
FROM intelligence_metric_aggregates
WHERE organization_id = 12
  AND date_key BETWEEN '2026-03-10' AND '2026-03-15'
ORDER BY date_key, metric_type, source_type;
```

## Errores comunes

- `Invalid date format`: la fecha debe venir en `Y-m-d`.
- `Invalid range`: `--from` no puede ser mayor que `--to`.
- `Invalid --organization_id`: debe ser entero.
- `No intelligence metrics found`: no hay datos crudos para calcular.

## Archivos relacionados

- Comando: `app/Console/Commands/BackfillIntelligenceMetricAggregates.php`
- Servicio agregador: `app/Services/IntelligenceMetricsAggregator.php`
- Modelo agregado: `app/Models/IntelligenceMetricAggregate.php`
- Test del comando: `tests/Feature/Console/BackfillIntelligenceMetricAggregatesCommandTest.php`
