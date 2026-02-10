## Arquitectura y proceso de generación de escenarios

Resumen conciso

- Objetivo: recibir respuestas LLM en streaming desde el proveedor ABACUS, persistir deltas (chunks), exponer progreso, ensamblar y compactar el resultado final para consumo por UI/CLI.

Componentes principales

- Proveedor LLM: ABACUS (cliente `AbacusClient` / `App\Services\LLMClient`).
- Job orquestador: `GenerateScenarioFromLLMJob` — maneja streaming, persistencia y compaction.
- Buffer temporal: `GenerationRedisBuffer` — lista Redis por organización/scenario/generation y hash meta (`:meta`).
- Persistencia chunks: tabla `generation_chunks` (modelo `GenerationChunk`) cuando se usa DB o como respaldo.
- Generación final: tabla `scenario_generations` (modelo `ScenarioGeneration`) con columnas relevantes:
  - `llm_response` (JSON parseado del ensamblado, resultado final)
  - `compacted` (longText) — blob compactado (base64) del ensamblado
  - `metadata` (json) — meta ligera: `provider`, `model`, `progress`, `received_chunks`, `received_bytes`, `first_chunk_at`, `last_chunk_at`, etc.
  - columnas dedicadas: `chunk_count`, `compacted_at`, `compacted_by` para consultas y auditoría.

Flujo de datos (paso a paso)

1. Se inicia una generación (API/CLI) y se crea un registro en `scenario_generations` con `metadata` inicial (p. ej. `used_instruction`, `initiator`).
2. `GenerateScenarioFromLLMJob` envía la petición al proveedor ABACUS y abre streaming.
3. A medida que llegan deltas/chunks:
   - Se almacenan en Redis por key: `app:scenario_planning:org:{org}:generation:{gen}:chunks` (o con `:scenario:{id}` si aplica).
   - Se actualiza el hash meta `{key}:meta` con `received_chunks`, `received_bytes`, `first_chunk_at`, `last_chunk_at`, y `total_chunks` si el proveedor lo reporta.
   - Alternativa: si `GENERATION_CHUNK_STORAGE` = `db` (o `both`) se insertan filas en `generation_chunks`.
4. El job expone progreso (endpoints `/progress` o similar) basándose en `metadata.progress` y datos en Redis/meta.
5. Cuando se detecta finalización (idle timeout, `total_chunks` alcanzado o cierre de stream):
   - Se ensamblan los chunks (concatenación) desde Redis o desde `generation_chunks`.
   - Se intenta `json_decode` del ensamblado; si es válido, se escribe en `llm_response` (campo JSON). Si no, `llm_response` se mantiene o se registra el fallback.
   - Se crea el blob compactado (base64) y se guarda en la columna `compacted`.
   - Se actualizan `metadata` (sin incluir el blob), y las columnas `chunk_count`, `compacted_at`, `compacted_by`.
   - Opcional: borrar la key Redis si `deleteAfter=true`.

Operaciones / comandos útiles

- `php artisan test:redis-buffer {generation_id} [--delete]` — (helper) pobla Redis desde `generation_chunks` y ejecuta `assembleAndPersist`.
- `php artisan compact:generation {org} {generation_id} [--delete]` — assemble desde Redis y persiste.
- `php artisan redis:inspect-generation {org} {gen} [--delete]` — inspecciona/borra keys Redis.
- `php artisan migrate:compacted-from-metadata [--preview]` — migración idempotente que movió blobs antiguos de `metadata.compacted` a la columna `compacted`.

Configuración y variables de entorno

- `GENERATION_CHUNK_STORAGE=redis|db|both` — modo de almacenamiento de chunks.
- `GENERATION_CHUNK_TTL` — TTL por defecto de buffer Redis (p. ej. 86400s).

Decisiones y recomendaciones

- Canonical: considerar la columna `compacted` y `llm_response` como artefactos canónicos consumidos por la UI y tests; `metadata` queda para estado y trazabilidad.
- Purga: añadir job periódico para limpiar `generation_chunks` antiguos tras ventana de QA (configurable).
- Indexes: indexar `chunk_count`, `compacted_at` y `organization_id` para consultas rápidas.
- Backups: compacted puede ser grande; asegúrate de incluir `compacted` en backups o mover a object storage (S3) si escala.

Testing

- Test manual: `tests/Feature/RedisBufferManualTest.php` (usa generación id=59 como fixture para poblar Redis y ejecutar compaction).
- Considerar tests E2E con Redis real para validar TTL, concurrencia e idle-timeout.

Documentos y scripts relacionados

- `docs/GENERATION_BUFFER.md` — documentación operativa del buffer (TTL, pattern de keys).
- `src/scripts/compact_from_llm_59.php` — script PDO para compactar sin autoload (útil en entornos sin vendor).

Notas finales

- El patrón aplicado en la generación `id=59` es el patrón recomendado: `llm_response` = JSON parseado; `compacted` = blob base64; `metadata` = estado ligero. Mantener este patrón para coherencia y performance.

## Esquema Canónico (`metadata`) - JSON Schema

El siguiente JSON Schema define la forma recomendada y canónica para el campo `metadata` en `scenario_generations`.

```json
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "title": "ScenarioGeneration.metadata",
  "type": "object",
  "properties": {
    "provider": { "type": "string" },
    "model": { "type": "string" },
    "progress": {
      "type": "object",
      "properties": {
        "received_chunks": { "type": "integer" },
        "total_chunks": { "type": ["integer", "null"] },
        "received_bytes": { "type": "integer" },
        "expected_total_bytes": { "type": ["integer", "null"] },
        "percent": { "type": ["number", "null"] }
      },
      "required": ["received_chunks", "received_bytes"]
    },
    "chunk_count": { "type": "integer" },
    "compacted_at": { "type": "string", "format": "date-time" },
    "received_chunks": { "type": "integer" },
    "received_bytes": { "type": "integer" },
    "first_chunk_at": { "type": "string", "format": "date-time" },
    "last_chunk_at": { "type": "string", "format": "date-time" },
    "initiator": { "type": ["integer", "null"] },
    "used_instruction": {
      "type": "object",
      "properties": {
        "content": { "type": "string" },
        "source": { "type": "string" },
        "language": { "type": "string" }
      }
    }
  },
  "required": ["provider", "model"],
  "additionalProperties": true
}
```

Notas:

- No incluir el blob `compacted` dentro de `metadata`; `compacted` debe almacenarse en la columna dedicada.
- `metadata` queda para estado y trazabilidad ligera (provider, progress, counts, timestamps, origen del prompt).

-- Fin
