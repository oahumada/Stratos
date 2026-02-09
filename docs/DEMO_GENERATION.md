## Demo: Generación de escenario - "Adopción de IA generativa para la empresa"

Propósito: Proveer un flujo de demostración rápido que prellena el formulario del `GenerateWizard` y permite probar el flujo completo de generación asistida por IA (composición de prompt, envío a Abacus, streaming y persistencia).

Uso desde la UI
- Abre la página de detalle de escenario y lanza el `GenerateWizard`.
- Pulsa el botón **Cargar demo: Adopción IA generativa** (ubicado en la cabecera del asistente).
- Revisa los campos prellenados y la instrucción recomendada (se colocará como instrucción `client`).
- Ve al paso final y pulsa **Generar**. Esto encolará una generación y retornará un `id` que puedes consultar en la propia UI.

Rutas y API
- Endpoint demo (crea y encola la generación demo):

  `POST /api/strategic-planning/scenarios/generate/demo`

  Ejemplo con `curl` (requiere token válido):

  ```bash
  curl -X POST \
    -H "Accept: application/json" \
    -H "Authorization: Bearer <YOUR_TOKEN>" \
    https://your-host/api/strategic-planning/scenarios/generate/demo
  ```

- Consultar estado/resultado:
  - `GET /api/strategic-planning/scenarios/generate/{id}` — estado y `llm_response` cuando esté `complete`.
  - `GET /api/strategic-planning/scenarios/generate/{id}/chunks` — lista de chunks (progreso streaming).
  - `GET /api/strategic-planning/scenarios/generate/{id}/compacted` — devuelve el blob compactado (decodificado) si existe.

Script de consola (útil para demos locales)
- `scripts/trigger_demo_generation.php` — script que crea y encola la generación demo con el primer usuario en la BD y espera hasta completion (útil si la cola está en modo `sync` o si hay un worker en ejecución). Ejecución:

  ```bash
  php scripts/trigger_demo_generation.php
  ```

Requisitos y notas operativas
- Asegúrate de tener configurada la clave de Abacus en `.env` (`ABACUS_API_KEY`) y que los workers/cola estén corriendo si la app usa `queue:work`.
- Si tras generar no ves actividad en la generación:
  - Comprueba que `php artisan queue:work` está activo o que la cola está en modo `sync` en `config/queue.php`.
  - Revisa `storage/logs/laravel.log` para errores del job `GenerateScenarioFromLLMJob` o del `AbacusClient`.
  - Consulta `scenario_generations` y `generation_chunks` en la BD para ver si la generación fue encolada y si hay chunks.

Compatibilidad de la instrucción
- El demo prefiere una instrucción que exija salida JSON única (la UI preconfigura una instrucción cliente). Mantén esa instrucción para garantizar que el `ScenarioGenerationImporter` pueda procesar el resultado.

¿Quieres que añada una pequeña guía visual en la UI (tooltip) o un atajo de teclado para el demo? 
