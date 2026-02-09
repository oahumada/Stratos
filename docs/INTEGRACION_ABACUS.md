## Integración Abacus LLM — Stratos

Fecha: 2026-02-08

## Resumen

Esta guía documenta cómo integrar Abacus (runtime y API) con el servicio de generación de escenarios en Stratos. Contiene instrucciones, ejemplos, fallbacks y prácticas recomendadas para llamadas streaming y no-streaming.

1. Configuración

---

- Variables de entorno (en `src/.env`):
  - `ABACUS_API_KEY` — clave privada (no subir a VCS).
  - `ABACUS_BASE_URL` — por defecto `https://api.abacus.ai`.
  - `ABACUS_STREAM_URL` — opcional. Ej: `https://routellm.abacus.ai/v1/chat/completions`.
  - `ABACUS_MODEL` — ejemplo: `gpt-5`.
  - `ABACUS_TIMEOUT` — timeout en segundos.

- Config en `config/services.php`:
  - bloque `abacus` con `base_url`, `stream_url`, `key`, `model`, `timeout`.

2. Endpoints y payloads

---

- Streaming (SSE) runtime:
  - Método recomendado: POST a `https://routellm.abacus.ai/v1/chat/completions`.
  - Payload base:

```json
{
  "model": "gpt-5",
  "messages": [{ "role": "user", "content": "..." }],
  "stream": true
}
```

- No-stream: algunos endpoints usan `/v1/generate` y esperan `prompt` (string). La forma moderna para chat es `messages`.

3. Headers y autenticación

---

- Recomendado enviar `Authorization: Bearer <API_KEY>`. Algunos ejemplos de la doc histórica usan `apiKey: <API_KEY>`.
- En `AbacusClient` se añade por defecto ambos headers para compatibilidad (`Authorization` y `apiKey`) y evitar reintentos.

4. Implementación en Stratos (resumen)

---

- `app/Services/AbacusClient.php`:
  - `generate(string $prompt, array $options = [])` para no-stream.
  - `generateStream(string $prompt, array $options = [], ?callable $onChunk = null)` para streaming SSE, con capacidad de ensamblar texto y parsear JSON final.
  - Deriva `stream_url` desde `ABACUS_STREAM_URL` o construye `https://routellm.abacus.ai/v1/chat/completions` cuando `ABACUS_BASE_URL` es `api.abacus.ai`.

- `scripts/generate_via_abacus.php`:
  - Script de ejemplo que utiliza `generateStream`, imprime chunks en tiempo real, intenta decodificar JSON final y persiste usando `ScenarioGenerationService`.

5. Persistencia y auditoría

---

- Guardar la generación en `scenario_generations` con `organization_id`, `created_by`, `prompt` (redactado), `metadata` y `status`.
- Redactar prompt antes de persistir (usar `RedactionService::redactText`).
- Registrar metadata: provider, model, instruction source, headers usados, status codes.

6. Manejo incremental (opcional)

---

- Opcional: persistir cada chunk en una tabla `generation_chunks` con `generation_id`, `offset`, `chunk`, `created_at`.
- Útil para debugging, replays y para UI de progreso en tiempo real.

7. Errores comunes y diagnóstico

---

- `400 Bad Request`: revisar payload (`messages` vs `prompt`, `model`).
- `404 Not Found`: endpoint incorrecto; configurar `ABACUS_STREAM_URL` explícito si el runtime tiene host distinto.
- `401/403`: clave inválida/permiso; probar header `apiKey:` si `Authorization` falla.
- `5xx`: retry con backoff y alertas.

8. Comandos útiles

---

```bash
cd /home/omar/Stratos/src
php scripts/generate_via_abacus.php    # prueba de streaming + persistencia
php artisan queue:work --tries=1       # procesar jobs/cola (import)
sqlite3 database/database.sqlite "SELECT id, organization_id, status, created_at FROM scenario_generations ORDER BY id DESC LIMIT 5;"
```

9. Mejoras sugeridas

---

- Añadir tabla `generation_chunks` y endpoint para leer progreso en UI.
- Tests unitarios que mockeen Guzzle y stream para `generateStream`.
- Añadir métricas y tracing (request ids, latency) a `metadata`.

## Contacto

Para dudas, comentar en el PR o abrir issue `integracion/abacus` con el error y el `generation_id`.

---

Generado y añadido por desarrollador automático el 2026-02-08.
