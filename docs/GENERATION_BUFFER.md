# Buffer de generación (Redis)

Resumen rápido

- Se usa Redis como buffer temporal para almacenar los `chunks` generados por el LLM durante la transmisión.
- El artefacto canónico para la UI/tests es `metadata.compacted` (base64) almacenado en `scenario_generations`.

Key pattern

Clave Redis (recomendada): `app:scenario_planning:org:{organization_id}:scenario:{scenario_id}:generation:{generation_id}:chunks`
Si `scenario_id` no está disponible la key cae a: `app:scenario_planning:org:{organization_id}:generation:{generation_id}:chunks`

TTL

- Se puede configurar mediante la variable de entorno `GENERATION_CHUNK_TTL`.

Operación de ingestión (recomendado)

1. Al recibir un `chunk` del LLM, hacer `RPUSH` en la key anterior.
2. Si la key es nueva, fijar `EXPIRE key GENERATION_CHUNK_TTL`.
3. Mantener LARGO límite y alertas si `LLEN` excede un umbral configurable.

Ensamblado y persistencia

- Worker/command debe:
  - Leer `LRANGE key 0 -1` y concatenar/decodificar los `chunks`.
  - Calcular `chunk_count = LLEN key`.
  - Persistir en BD: `scenario_generations.llm_response` (si aplica), `metadata.compacted` (base64), `metadata.chunk_count` y `metadata.compacted_at`.
  - Opcional: `DEL key` si se desea liberar memoria inmediatamente.

Comando disponible (PoC)

- `php artisan compact:generation {organization_id} {generation_id} {--delete}`
  - Ensambla desde Redis y persiste `metadata.compacted`.
  - `--delete` borra la key Redis tras el guardado.

Buenas prácticas

- Usar Redis solo como buffer temporal; para almacenamiento final usar S3/NDJSON o `metadata.compacted` en BD.
- Para alto volumen, considerar Redis Streams + consumer groups para exactamente-once/ack.
- Comprimir chunks si son grandes y monitorear la memoria de Redis.

Ejemplo de env

```
GENERATION_CHUNK_TTL=86400
```

Comandos Artisan útiles

- `php artisan compact:generation {organization_id} {generation_id} {--delete}`
  - Ensambla los `chunks` desde Redis (según key pattern), calcula `chunk_count`, crea/actualiza `metadata.compacted` en `scenario_generations` y, si se pasa `--delete`, borra la key Redis.
  - Ejemplo:
    ```bash
    php artisan compact:generation 12 59
    php artisan compact:generation 12 59 --delete
    ```

- `php artisan redis:inspect-generation {organization_id} {generation_id} {--scenario_id=} {--delete}`
  - Lista keys Redis que coincidan con el patrón de buffer (muestra `LLEN` para cada key). Si se pasa `--delete` elimina las keys listadas.
  - Ejemplo:
    ```bash
    php artisan redis:inspect-generation 12 59 --scenario_id=345
    php artisan redis:inspect-generation 12 59 --scenario_id=345 --delete
    ```

Notas

- Ambos comandos se descubren automáticamente en Artisan (están bajo `app/Console/Commands`).
- Útiles para QA/operaciones: `inspect` permite auditar y liberar memoria, `compact` persiste el artefacto canónico `metadata.compacted`.
