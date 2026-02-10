# Guía: Conectar la generación de escenarios con un API LLM real

Objetivo

- Describir el propósito, uso y arquitectura de la generación asistida de escenarios por LLM.
- Indicar pasos prácticos para conectar un proveedor LLM real (ej. OpenAI) de forma segura y controlada.

Estado de la implementación

- La lógica de construcción del prompt y la encolación existe: [src/app/Services/ScenarioGenerationService.php](src/app/Services/ScenarioGenerationService.php).
- El job que consume la cola y persiste la respuesta existe: [src/app/Jobs/GenerateScenarioFromLLMJob.php](src/app/Jobs/GenerateScenarioFromLLMJob.php).
- UI/Wizard y store están implementados: [src/resources/js/pages/ScenarioPlanning/GenerateWizard/GenerateWizard.vue](src/resources/js/pages/ScenarioPlanning/GenerateWizard/GenerateWizard.vue) y [src/resources/js/stores/scenarioGenerationStore.ts](src/resources/js/stores/scenarioGenerationStore.ts).
- Hay redacción automática para prompts/respuestas: [src/app/Services/RedactionService.php](src/app/Services/RedactionService.php).
- Tests: unitarios añadidos para `preparePrompt` y E2E Playwright para el wizard (mock LLM): [src/tests/Unit/ScenarioGenerationServiceTest.php](src/tests/Unit/ScenarioGenerationServiceTest.php) y [src/tests/e2e/generate-wizard.spec.ts](src/tests/e2e/generate-wizard.spec.ts).

Arquitectura (resumen)

- Flow:
  1. Operador completa wizard en UI → `scenarioGenerationStore.preview()` para ver prompt.
  2. Operador confirma → `scenarioGenerationStore.generate()` que hace `POST /api/strategic-planning/scenarios/generate`.
  3. Backend: `ScenarioGenerationService::enqueueGeneration()` crea `scenario_generations` (status=queued) y dispatcha `GenerateScenarioFromLLMJob`.
  4. Job llama al `LLMProvider` configurable (mock o real), guarda `llm_response` y actualiza `status`.
  5. UI/polling consulta `fetchStatus()` hasta `complete` y muestra resultados; operador puede crear un `scenario` o `ChangeSet`.
  6. Opcional: si el operador acepta la propuesta, se puede invocar `POST /api/strategic-planning/scenarios/generate/{id}/accept` para crear un `scenario` draft a partir de la `llm_response` validada. El backend copia el `prompt` (redacted) y registra `source_generation_id` en el `scenario` para trazabilidad.

Componentes clave

- Service: [src/app/Services/ScenarioGenerationService.php](src/app/Services/ScenarioGenerationService.php)
- Job: [src/app/Jobs/GenerateScenarioFromLLMJob.php](src/app/Jobs/GenerateScenarioFromLLMJob.php)
- Store: [src/resources/js/stores/scenarioGenerationStore.ts](src/resources/js/stores/scenarioGenerationStore.ts)
- UI Wizard: [src/resources/js/pages/ScenarioPlanning/GenerateWizard/GenerateWizard.vue](src/resources/js/pages/ScenarioPlanning/GenerateWizard/GenerateWizard.vue)
- Redaction: [src/app/Services/RedactionService.php](src/app/Services/RedactionService.php)

Requisitos previos para conectar un proveedor real

- Claves/secretos: variable `OPENAI_API_KEY` (u otra según proveedor) en el entorno seguro del servidor/CI.
- Feature flag: `LLM_ENABLED=true` o similar para controlar habilitación en staging/producción.
- Config: `LLM_PROVIDER=openai` (valores soportados: `mock`, `openai`, `azure`, etc.)
- Rate-limits y límites de coste: definir `LLM_RATE_LIMIT` y `LLM_MAX_TOKENS` en `.env`.
- Aprobación humana: UI requiere confirmación antes de invocar la API.

Variables de entorno sugeridas

```
LLM_PROVIDER=mock
LLM_ENABLED=false
OPENAI_API_KEY=
OPENAI_MODEL=gpt-4o-mini
LLM_TIMEOUT=60
LLM_RATE_LIMIT=5 # llamadas por minuto por org (ejemplo)
LLM_MOCK_SIMULATE_429=false
```

Implementación mínima de proveedor (ejemplo PHP)

Agrega una clase en `src/app/Services/Providers/OpenAIProvider.php` que implemente una interfaz `LLMProviderInterface`.

Ejemplo esquemático:

```php
<?php
namespace App\Services\Providers;

use Illuminate\Support\Facades\Http;

class OpenAIProvider implements LLMProviderInterface
{
    public function call(string $prompt, array $opts = []) : array
    {
        $resp = Http::withToken(config('services.openai.key'))
            ->timeout(config('services.openai.timeout', 60))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.openai.model', 'gpt-4o-mini'),
                'messages' => [[ 'role' => 'user', 'content' => $prompt ]],
                'max_tokens' => $opts['max_tokens'] ?? 1500,
            ]);

        if (! $resp->successful()) {
            // lanzar excepción personalizada para reintentos
            throw new \Exception('LLM error: ' . $resp->status());
        }

        $body = $resp->json();
        return $body;
    }
}
```

Notas de integración:

- Registrar en `config/services.php` y un binding en un ServiceProvider que devuelva la implementación según `LLM_PROVIDER`.
- Manejar errores: lanzar `LLMRateLimitException` en 429 y `LLMServerException` en 5xx; `GenerateScenarioFromLLMJob` ya implementa reintentos exponenciales.
- No persistir la respuesta cruda si contiene PII; usar `RedactionService` previo a persistir.

Pruebas y despliegue controlado

- Entorno de pruebas (local/CI): usar `LLM_PROVIDER=mock` para tests automáticos y E2E Playwright para el wizard (mock LLM): [src/tests/e2e/generate-wizard.spec.ts](src/tests/e2e/generate-wizard.spec.ts).
- Staging: habilitar `LLM_ENABLED=true` y `LLM_PROVIDER=openai` pero con límites: `LLM_RATE_LIMIT=1` y `LLM_MAX_TOKENS` bajo; revisar logs y coste por organización.
- Run manual de verificación:
  1. En staging, crear un escenario en UI, previsualizar prompt, autorizar.
  2. Verificar que `scenario_generations` cambia a `complete` y revisar `llm_response` redacted.
  3. Revisar `confidence_score`, `assumptions` y asegurar que el JSON cumple el esquema mínimo.

Comandos útiles (local)

```bash
cd src
cp .env.example .env
export LLM_PROVIDER=mock
export LLM_ENABLED=true
composer install
npm ci && npm run build
php artisan migrate:fresh --seed
php artisan queue:work --sleep=3 --tries=3
php artisan serve --host=127.0.0.1 --port=8000
```

Seguridad y costes

- Redactar tokens/PII: `RedactionService` se aplica antes de persistir. Valida que `scenario_generations.prompt` se guarde redacted.
- Monitoreo: instrumentar métricas (conteo por org, latencia, tokens) y alertas de coste.
- Aislar producción: usar feature flags y límites por organización.

Checklist mínimo antes de habilitar en producción

- [ ] Configurar `OPENAI_API_KEY` en vault/secret-manager.
- [ ] Añadir `LLM_PROVIDER=openai` binding y pruebas de integración.
- [ ] Ejecutar pruebas de carga/rate-limit en staging (simular 429).
- [ ] Revisar y aprobar prompts/resultados con stakeholders.
- [ ] Poner alertas de coste/latencia.

Contacto y referencias

- Código clave: [src/app/Services/ScenarioGenerationService.php](src/app/Services/ScenarioGenerationService.php), [src/app/Jobs/GenerateScenarioFromLLMJob.php](src/app/Jobs/GenerateScenarioFromLLMJob.php), [src/app/Services/RedactionService.php](src/app/Services/RedactionService.php)
- E2E y fixtures: [src/tests/e2e/generate-wizard.spec.ts](src/tests/e2e/generate-wizard.spec.ts), [src/tests/fixtures/llm/mock_generation_response.json](src/tests/fixtures/llm/mock_generation_response.json)
