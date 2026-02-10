# Guía rápida: Pruebas con Abacus + harness CLI

Objetivo: instrucciones para probar integraciones Abacus usando el harness CLI `scripts/generate_via_abacus.php` y el script de ejecución extendida usado durante debugging (`/tmp/run_abacus_override.php`). Incluye pasos para simular prompts, ejecutar en foreground/background, y validar desde la API y la DB.

1. Preparar entorno

- Asegúrate de tener variables de entorno necesarias en el entorno donde corras PHP (ej. `.env` o export):
  - `ABACUS_API_KEY` (o `services.abacus.key` en config)
  - `ABACUS_BASE_URL` (o `services.abacus.base_url` en config)
  - `ABACUS_MODEL` (opcional, `gpt-5` recomendado)

2. Ejecutar harness CLI (prueba local rápida)

- Ejecuta el script de test CLI que ya existe:

```bash
php scripts/generate_via_abacus.php --prompt "Genera un escenario de prueba en JSON con keys scenario_metadata y capabilities"
```

- Parámetros útiles:
  - `--prompt` : texto del prompt (si no se pasa, el script usa un prompt por defecto).
  - `--model` : sobrescribir modelo, por ejemplo `--model=gpt-5`.
  - `--timeout` : segundos de timeout para la petición.

- Salida: el script persiste `ScenarioGeneration` y `GenerationChunk` en la DB; revisa consola para logs y el ID de la generación.

3. Ejecutar versión con timeouts extendidos (debug / background)

- Copia/usa el script temporal que se usó durante depuración o crea uno similar en `scripts/` (ejemplo):

```bash
php /tmp/run_abacus_override.php --prompt "Genera JSON ..." --timeout 300 --stream_idle_timeout 240 > /tmp/abacus_run.log 2>&1 &
# Ver PID si es necesario: ps aux | grep run_abacus_override.php
```

- Monitorea el log:

```bash
tail -f /tmp/abacus_run.log
```

4. Validar vía API (curl)

- Encolar generación vía endpoint Abacus (usa token válido):

```bash
curl -X POST 'http://localhost/api/strategic-planning/scenarios/generate/abacus' \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"organization_id":1, "company_name":"ACME", "instruction":"Genera un escenario de prueba en JSON", "timeout":300, "stream_idle_timeout":240 }'
```

- Respuesta esperada: `202` y JSON con `id` de la generación.

5. Inspección de la base de datos y logs

Comandos rápidos para revisar el estado de una generación, contar chunks y ver el progreso almacenado.

- Usando Tinker (Artisan):

```bash
php artisan tinker
>>> $g = \App\Models\ScenarioGeneration::find({id});
>>> $g->status; // queued|processing|complete|failed
>>> $g->metadata['progress'] ?? null; // progreso incremental si existe
>>> \App\Models\GenerationChunk::where('scenario_generation_id', {id})->count();
>>> \App\Models\GenerationChunk::where('scenario_generation_id', {id})->orderBy('sequence')->limit(10)->get();
```

- Usando SQL (MySQL / MariaDB):

```sql
SELECT id, status, JSON_EXTRACT(metadata, '$.progress') as progress FROM scenario_generations WHERE id = {id};
SELECT COUNT(*) FROM generation_chunks WHERE scenario_generation_id = {id};
SELECT sequence, chunk FROM generation_chunks WHERE scenario_generation_id = {id} ORDER BY sequence LIMIT 50;
```

- Logs de Laravel (errores y trazas relacionadas con Abacus):

````bash
Ejemplos curl útiles:

1) Encolar generación Abacus (devuelve `id`):

```bash
curl -X POST 'http://localhost/api/strategic-planning/scenarios/generate/abacus' \
  -H "Authorization: Bearer $TOKEN" \

- Inspección rápida desde shell PHP (sin Tinker):

```bash
  -H "Content-Type: application/json" \
  -d '{"organization_id":1, "company_name":"ACME", "instruction":"Genera un escenario de prueba en JSON", "timeout":300, "stream_idle_timeout":240 }'

Consejos:
- Si tu entorno usa SQLite para tests locales, los comandos SQL cambian ligeramente (no JSON_EXTRACT). Usa `SELECT metadata FROM scenario_generations WHERE id={id};` y parsea JSON localmente.
- Verifica `organization_id` al inspeccionar filas para evitar confundir generaciones de otros tenants.

````

2. Consultar estado y progreso (incluye `metadata.progress` si está presente):

```bash
curl -H "Authorization: Bearer $TOKEN" 'http://localhost/api/strategic-planning/scenarios/generate/{id}'
```

3. Endpoint rápido `/progress` — devuelve status, `metadata.progress` y últimos N chunks (por defecto 5). Parámetros: `?last=10&assemble=true`:

```bash
curl -H "Authorization: Bearer $TOKEN" 'http://localhost/api/strategic-planning/scenarios/generate/{id}/progress?last=10&assemble=true'
```

4. Obtener chunks completos (fallback para ensamblado client-side):

```bash
curl -H "Authorization: Bearer $TOKEN" 'http://localhost/api/strategic-planning/scenarios/generate/{id}/chunks'
```

5. Polling / inspección (UI o curl)

- Consultar estado y metadata (incluye `metadata.progress` cuando el job va persistiendo):

```bash
curl -H "Authorization: Bearer $TOKEN" 'http://localhost/api/strategic-planning/scenarios/generate/{id}'
```

- Obtener chunks (si el ensamblado client-side es necesario):

```bash
curl -H "Authorization: Bearer $TOKEN" 'http://localhost/api/strategic-planning/scenarios/generate/{id}/chunks'
```

- Obtener versión compactada (si ya está creada):

```bash
curl -H "Authorization: Bearer $TOKEN" 'http://localhost/api/strategic-planning/scenarios/generate/{id}/compacted'
```

6. Inspección directa en DB (Artisan Tinker o consulta SQL)

- Usando Tinker:

```bash
php artisan tinker
>>> \App\Models\ScenarioGeneration::find({id});
>>> \App\Models\GenerationChunk::where('scenario_generation_id', {id})->orderBy('sequence')->get();
```

- Campos de interés en `ScenarioGeneration`: `status`, `metadata.progress` (received_chunks, received_bytes, percent), `llm_response`.

7. Logs

- Revisa `storage/logs/laravel.log` para errores y trazas relacionadas con AbacusClient.

```bash
tail -n 200 storage/logs/laravel.log | grep Abacus
```

8. Simular prompt complejo (archivo)

- Puedes preparar un prompt en archivo y pasarlo al script CLI:

```bash
cat > /tmp/prompt.json <<'JSON'
{
  "instruction":"Genera un escenario con 2 capacidades y 3 competencias cada una, output JSON sólo",
  "meta":{"test_case":"integration"}
}
JSON

php scripts/generate_via_abacus.php --prompt "$(jq -r '.instruction' /tmp/prompt.json)" --model=gpt-5
```

9. Limpieza y recomendaciones

- El harness CLI (`scripts/generate_via_abacus.php`) se deja intacto para pruebas repetibles.
- Los scripts temporales en `/tmp` pueden eliminarse tras validación.
- Para UI: usa el endpoint `generate/abacus` para encolar y el polling existente en GenerateWizard que solicita `/compacted` y `/chunks`.

Archivos de referencia:

- [scripts/generate_via_abacus.php](scripts/generate_via_abacus.php)
- [src/app/Http/Controllers/Api/ScenarioGenerationAbacusController.php](src/app/Http/Controllers/Api/ScenarioGenerationAbacusController.php)
- [src/app/Jobs/GenerateScenarioFromLLMJob.php](src/app/Jobs/GenerateScenarioFromLLMJob.php)

Si quieres, puedo:

- Añadir un script helper en `scripts/` para ejecutar la versión extendida (wrapping de `/tmp/run_abacus_override.php`).
- Añadir un endpoint `/progress` que devuelva `metadata.progress` + últimos N chunks para polling más eficiente.

---

Guía creada automáticamente. Si deseas que añada el script helper en `scripts/run_abacus_override.php`, dime y lo creo ahora.
