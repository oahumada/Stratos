# Estado actual: Generación Asistida de Escenarios (LLM)

Resumen operativo corto

- Estado de la implementación:
  - Flujo completo operativo: `preparePrompt`, preview API, enqueue (POST `/generate`) y job `GenerateScenarioFromLLMJob` que llama al `LLMClient` y persiste `llm_response`.
  - Redacción (PII) y manejo de rate-limits/backoff integrados.
  - `LLMClient` con providers `mock` (por defecto) y `openai`; registrado en el contenedor (`LLMServiceProvider`) para DI en jobs/tests.

- Pruebas existentes (ubicación):
  - Unit: `src/tests/Unit/ScenarioGenerationServiceTest.php`.
  - Feature: `src/tests/Feature/ScenarioGenerationEndpointsTest.php`.
  - Integration: `src/tests/Feature/ScenarioGenerationIntegrationTest.php`.
  - E2E: `src/tests/e2e/generate-wizard.spec.ts` + helpers en `src/tests/e2e/helpers/` y fixtures `src/tests/fixtures/llm/mock_generation_response.json`.

- Endpoints clave para pruebas manuales:
  - `POST /api/strategic-planning/scenarios/generate/preview` → mostrar prompt.
  - `POST /api/strategic-planning/scenarios/generate` → encolar (devuelve `generation_id`).
  - `GET /api/strategic-planning/scenarios/generate/{id}` → consultar estado y `llm_response`.

- Archivos clave:
  - `src/app/Services/ScenarioGenerationService.php`
  - `src/app/Jobs/GenerateScenarioFromLLMJob.php`
  - `src/app/Services/LLMClient.php` y providers en `src/app/Services/LLMProviders/`
  - `src/app/Services/RedactionService.php`
  - `src/app/Providers/LLMServiceProvider.php`
  - E2E: `src/tests/e2e/*` y `src/tests/fixtures/llm/`

- Cómo ejecutar pruebas localmente (rápido):
  - Unit/Feature:
    ```bash
    cd src
    php artisan test
    ```
  - E2E Playwright (local):
    ```bash
    cd src
    export BASE_URL=http://127.0.0.1:8000
    export E2E_ADMIN_EMAIL=admin@example.com
    export E2E_ADMIN_PASSWORD=secret
    npx playwright test src/tests/e2e/generate-wizard.spec.ts
    ```

    - Levantar servidor (`php artisan serve`) y usar `LLM_PROVIDER=mock`.

- Variables de entorno para provider real (precaución):
  - `LLM_PROVIDER=openai`
  - `LLM_API_KEY` / `LLM_OPENAI_API_KEY`
  - `LLM_ENABLED=true`
  - Recomendación: en CI usar `LLM_PROVIDER=mock`.

- Comportamiento del job:
  - Marca `status=processing`, persiste `llm_response` redacted y `confidence_score`.
  - Reintentos en 429 (backoff), marca `failed` en errores persistentes.

- Notas para aceptación manual desde navegador:
  - Abrir GenerateWizard en `ScenarioDetail.vue`, completar pasos, `Preview`, `Generate` → revisar modal con resultado y `confidence_score`.
  - Para probar errores, activar `LLM_MOCK_SIMULATE_429=true` en `MockProvider`.

---

Este archivo añade un resumen accionable sobre el estado actual y comandos rápidos para pruebas de aceptación. Si quieres que lo integre directamente dentro de `docs/GUIA_GENERACION_ESCENARIOS.md`, lo pego y hago commit en el mismo cambio.
