# Guía: Generación Asistida de Escenarios (LLM-driven)

Resumen

- Propósito: plantilla y guía operativa para generar escenarios de planificación de talento mediante un LLM, integrable como un wizard en la UI.
- Alcance: cuestionario operador → prompt estructurado → llamada LLM (cola) → revisión humana → persistencia como `scenario` draft o `scenario_generation` record.

!!! warning
**Precaución importante:** No ejecutar tests automáticos (unit/feature/E2E) ni pipelines que lancen E2E hasta haber completado e verificado la implementación de la generación asistida por LLM. Requisitos mínimos antes de ejecutar tests:

- Seeds/fixtures para los escenarios usados por E2E y datos de prueba reproducibles.
- Helpers E2E (login/intercepts) validados y estables.
- `LLMClient` adaptador con modo `mock` y configuración `LLM_PROVIDER` disponible; en CI forzar `LLM_PROVIDER=mock`.
- Redacción/redaction de PII en prompts/responses y validación de no persistir secretos.
- Tests de edge-cases: redaction, rate-limits (429), errores 5xx/timeouts, reintentos/idempotencia y validación de payloads LLM.

Hasta completar los puntos anteriores, el workflow Playwright E2E está desactivado por defecto y requiere `RUN_E2E=true` para correr.

1. Plantilla del Prompt Estructurado

# CONTEXTO ORGANIZACIONAL

Nombre: {{company_name}}
Industria: {{industry}} - {{sub_industry}}
Tamaño: {{company_size}} personas
Alcance: {{geographic_scope}}
Ciclo: {{organizational_cycle}}

# SITUACIÓN ACTUAL

Desafíos principales:
{{current_challenges}}

Capacidades existentes:
{{current_capabilities}}

Brechas identificadas:
{{current_gaps}}

Roles formalizados actuales: {{current_roles_count}}
Modelo de competencias formal: {{has_formal_competency_model}}

# INTENCIÓN ESTRATÉGICA

Objetivo principal:
{{strategic_goal}}

Mercados objetivo:
{{target_markets}}

Crecimiento esperado: {{expected_growth}}
Tipo de transformación: {{transformation_type}}

Iniciativas clave:
{{key_initiatives}}

# RECURSOS Y RESTRICCIONES

Nivel de inversión: {{budget_level}}
Disponibilidad de talento: {{talent_availability}}
Capacidad de capacitación: {{training_capacity}}
Madurez tecnológica: {{technology_maturity}}

Restricciones críticas:
{{critical_constraints}}

# HORIZONTE TEMPORAL

Plazo: {{time_horizon}}
Urgencia: {{urgency_level}}

Hitos:
{{milestones}}

---

2. Cuestionario de Contexto Estratégico (para el operador)

- SECCIÓN 1: IDENTIDAD ORGANIZACIONAL
  - `company_name` (texto)
  - `industry` (dropdown)
  - `sub_industry` (texto)
  - `company_size` (número)
  - `geographic_scope` (dropdown)
  - `organizational_cycle` (dropdown)

- SECCIÓN 2: SITUACIÓN ACTUAL
  - `current_challenges` (textarea)
  - `current_capabilities` (textarea)
  - `current_gaps` (textarea)
  - `current_roles_count` (número)
  - `has_formal_competency_model` (boolean)

- SECCIÓN 3: INTENCIÓN ESTRATÉGICA
  - `strategic_goal` (textarea)
  - `target_markets` (textarea)
  - `expected_growth` (dropdown)
  - `transformation_type` (checkboxes)
  - `key_initiatives` (textarea)

- SECCIÓN 4: RESTRICCIONES Y RECURSOS
  - `budget_level` (dropdown)
  - `talent_availability` (dropdown)
  - `training_capacity` (dropdown)
  - `technology_maturity` (dropdown)
  - `critical_constraints` (textarea)

- SECCIÓN 5: HORIZONTE TEMPORAL
  - `time_horizon` (dropdown)
  - `urgency_level` (dropdown)
  - `milestones` (textarea)

3. Instrucciones operativas para la IA

- Objetivo: generar un "Escenario de Planificación de Talento" con estructura JSON.
- Incluir siempre: `scenario_metadata`, `capacities`, `competencies`, `skills`, `suggested_roles`, `impact_analysis`.
- Restringir lenguaje: usar terminología del `industry` dado.
- No incluir datos personales sensibles; si aparecen, redáctalos.
- Añadir `confidence_score` (0.0–1.0) y lista de `assumptions`.

4. Esquema de salida (JSON)

- `scenario_metadata`: { generated_at, confidence_score, assumptions[] }
- `capacities`: [{ name, description, criticality, time_horizon, justification }...]
- `competencies`: [{ name, description, archetype, domain, linked_capacity, bars, development_state, priority }...]
- `skills`: [{ name, description, skill_type, linked_competencies[], proficiency_required }...]
- `suggested_roles`: [{ name, description, archetype, complexity_level, process_domain, required_competencies[], role_type, implementation_priority, strategic_justification }...]
- `impact_analysis`: { transformation_index, transformation_justification, main_risks[], critical_dependencies[], implementation_recommendations[] }

5. Diseño Backend (sugerido)

- Nueva tabla `scenario_generations` (migration):
  - id, organization_id, created_by, prompt (text), llm_response (json), generated_at, confidence_score (decimal), status (queued/complete/failed), metadata (json), model_version, redacted boolean, timestamps.
- Servicio: `ScenarioGenerationService`
  - Método `preparePrompt(data, user, org)` → construye prompt estructurado + añade contexto desde `openmemory.md` u otros recursos del repo.
  - Método `enqueueGeneration(prompt, meta)` → crea `scenario_generations` con status `queued` y dispatch de Job `GenerateScenarioFromLLMJob`.
- Job `GenerateScenarioFromLLMJob`
  - Llama al LLM (cliente configurable), guarda respuesta en `llm_response`, extrae `confidence_score` y setea `status=complete`.
  - En caso de error setea `status=failed` y registra intentos.
- Endpoint API (`POST /api/strategic-planning/scenarios/generate`)
  - Requiere autenticación y `organization_id`; valida campos mínimos; retorna `generation_id` y `status`.
- Endpoint de consulta (`GET /api/strategic-planning/scenarios/generate/{id}`)
  - Devuelve `llm_response`, metadata, estado.

6. Diseño Frontend (sugerido)

- Wizard 5 pantallas como componentes en `resources/js/pages/ScenarioPlanning/GenerateWizard/`:
  - `StepIdentity.vue`, `StepSituation.vue`, `StepIntent.vue`, `StepResources.vue`, `StepHorizon.vue`.
- Estado temporal con Pinia `useScenarioGenerationStore` (almacena campos, validaciones, progreso, preview).
- Botón `Generar` que llama `POST /api/.../generate` y muestra loader y barra de progreso.
- Revisión: usar un modal tipo `ChangeSetModal.vue` para mostrar salida friendly + JSON toggle, permitir `Guardar como borrador` o `Crear Scenario`.
- UX: permitir editar campos del JSON resultante antes de aceptar; mostrar `confidence_score` y `assumptions`.

7. Integración con modelos existentes

- Multi-tenant: forzar `organization_id` en calls y persistencia.
- Guardar como `scenario` draft o generar un `ChangeSet` que proponga cambios (dependiendo del flujo): si el operador quiere aplicar cambios sobre un escenario existente, crear `ChangeSet` con ops.
- Reusar patrones: `ChangeSetModal` para revisión, `ScenarioDetail.vue` para editar/aceptar.

8. Seguridad, privacidad y costos

- Nunca enviar secretos o PII al LLM. Redactar antes de persistir prompts/responses.
- Implementar rate-limits por organización y colas para controlar costos.
- Registrar prompts y semántica de redacción (audit trail) con opción de redacción total del contenido antes de almacenamiento.

9. Testing y calidad

- Unit tests: `ScenarioGenerationServiceTest` (prompt builder, meta extraction).
- Feature tests: API `generate` en diferentes escenarios (mínimos, completos, permisos multi-tenant).
- Integration: Job `GenerateScenarioFromLLMJob` mocked LLM client.
- E2E Playwright: wizard happy path → generar → revisar → aceptar.

10. Trazabilidad y observabilidad

- Guardar `model_version` usado, `request_tokens`, `response_tokens` (opcional, atento a privacidad), `duration`.
- Métricas: counts generados por org, latencia, tasa de fallos.

11. Checklist mínimo de implementación

- [ ] Migration `scenario_generations` creada
- [ ] `ScenarioGenerationService` implementado
- [ ] Job y LLM client adaptador implementados y probados (mock)
- [ ] API endpoints `generate`/`status` implementados y documentados
- [ ] Wizard UI implementado + Pinia store
- [ ] Revisión modal integrado y opción guardar/crear scenario
- [ ] Tests unit/feature/e2e añadidos
- [ ] Documentación actualizada (`docs/GUIA_GENERACION_ESCENARIOS.md`) — este archivo

---

14. Playwright E2E — Guía práctica (añadido)

- Ejecutar localmente (desde `src/`):

````bash
# Instalar dependencias (por primera vez)
cd src
npm ci
# Instalar navegadores Playwright
npx playwright install --with-deps
# Ejecutar E2E (headless)
npm run test:e2e
# Ejecutar en modo headed (local)
npm run test:e2e:headed
```markdown
## Estado actual y pasos para pruebas de aceptación

- **Estado de la implementación:**
  - Flujo completo implementado: armado de prompt (`ScenarioGenerationService`), endpoint de preview, endpoint para encolar (`generate`) y job `GenerateScenarioFromLLMJob` que consume el `LLMClient` y persiste `llm_response` en `scenario_generations`.
  - Redacción (PII) y manejo de rate-limits/backoff integrados en el job y en los providers.
  - Patrón `LLMClient` con providers `mock` (por defecto para tests) y `openai` disponible; `LLMClient` está registrado en el contenedor via `LLMServiceProvider` para inyección y sobrescritura en tests.

- **Pruebas existentes (ubicación):**
  - Unit: `src/tests/Unit/ScenarioGenerationServiceTest.php` (prompt builder).
  - Feature: `src/tests/Feature/ScenarioGenerationEndpointsTest.php` (preview + enqueue).
  - Integration: `src/tests/Feature/ScenarioGenerationIntegrationTest.php` (LLMClient + MockProvider).
  - E2E Playwright: `src/tests/e2e/generate-wizard.spec.ts` + helpers en `src/tests/e2e/helpers/` y fixtures `src/tests/fixtures/llm/mock_generation_response.json`.

- **Endpoints clave para pruebas manuales desde navegador:**
  - `POST /api/strategic-planning/scenarios/generate/preview` → muestra prompt generado.
  - `POST /api/strategic-planning/scenarios/generate` → encola la generación, devuelve `generation_id` y `status` (202).
  - `GET /api/strategic-planning/scenarios/generate/{id}` → consulta estado y `llm_response`.

  12. Aceptación y persistencia del prompt (provenance)

  - Nuevo endpoint: `POST /api/strategic-planning/scenarios/generate/{id}/accept`
    - Requiere autenticación y que la `generation` esté en `status=complete`.
    - Crea un nuevo `scenario` (draft) a partir del JSON validado presente en `scenario_generations.llm_response`.
    - Persiste metadatos de procedencia en el nuevo registro de `scenario` y en el registro de `scenario_generations`.

  - Campos añadidos (migration):
    - `scenarios.source_generation_id` (FK -> `scenario_generations.id`) para trazabilidad.
    - `scenarios.accepted_prompt` (text) — copia del prompt guardado (redacted) usado para la generación.
    - `scenarios.accepted_prompt_redacted` (boolean) — indica si el prompt guardado ya está redactado.
    - `scenarios.accepted_prompt_metadata` (json) — metadata asociada a la generación (por ejemplo: `accepted_by`, `accepted_at`, `created_scenario_id`).

  - Comportamiento:
    - El endpoint valida tenant (`organization_id`) y que la generación sea `complete`.
    - Toma `llm_response.scenario_metadata` para poblar campos estándar del `scenario` (`name`, `description`, `horizon_months`, `start_date`, `end_date`, `owner_user_id`).
    - Actualiza `scenario_generations.metadata` con `accepted_by`, `accepted_at` y `created_scenario_id`.
    - El prompt persistido y la metadata están redactados según `RedactionService` y no contienen PII/secretos.


- **Archivos importantes (revisión rápida):**
  - `src/app/Services/ScenarioGenerationService.php` — preparación de prompt + enqueue.
  - `src/app/Jobs/GenerateScenarioFromLLMJob.php` — job que llama al LLM, redacciona y persiste.
  - `src/app/Services/LLMClient.php`, `src/app/Services/LLMProviders/MockProvider.php`, `src/app/Services/LLMProviders/OpenAIProvider.php` — adapters.
  - `src/app/Services/RedactionService.php` — redacción de PII.
  - `src/app/Providers/LLMServiceProvider.php` — binding en contenedor.
  - E2E: `src/tests/e2e/*` y fixtures en `src/tests/fixtures/llm/`.

- **Cómo ejecutar pruebas localmente (rápido):**
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
    - Asegúrate de levantar el servidor (`php artisan serve` o equivalente) y que `LLM_PROVIDER=mock` en el entorno de E2E.

- **Variables de entorno y cómo habilitar provider real (con precaución):**
  - **Provider en producción: ABACUS (NO OpenAI)**
  - Variables ABACUS requeridas:
    - `ABACUS_API_KEY` — clave de API de ABACUS (obligatoria)
    - `ABACUS_BASE_URL` — default: `https://api.abacus.ai`
    - `ABACUS_STREAM_URL` — default: `https://routellm.abacus.ai/v1/chat/completions`
    - `ABACUS_MODEL` — default: `abacus-default`
    - `ABACUS_TIMEOUT` — timeout en segundos (default: 60)
    - `ABACUS_CHUNKS_TTL_DAYS` — días de retención de chunks (default: 30)
  - Para ejecutar una generación de prueba con ABACUS:
    ```bash
    cd src
    php scripts/generate_via_abacus.php
    ```
  - Recomendación: en CI y en PRs usar `LLM_PROVIDER=mock` (MockProvider).
  - Nota: El código incluye `OpenAIProvider` pero NO es el proveedor activo del proyecto.

- **Comportamiento del job y manejo de errores:**
  - Marca `status=processing` al iniciar; guarda `llm_response` redacted al completar.
  - Reintenta automáticamente en `LLMRateLimitException` con backoff exponencial (máx 5 intentos).
  - En fallos 5xx o excepciones marca `status=failed` y persiste metadata con mensaje.

- **Notas para pruebas de aceptación manual desde navegador:**
  - Usar GenerateWizard: rellenar los 5 pasos → `Preview` → `Generate` → comprobar en UI el polling del `generation_id` hasta `complete` y revisar `llm_response` en el modal.
  - Para probar errores/latencias, usar `MockProvider` que puede simular 429/5xx (activar `LLM_MOCK_SIMULATE_429=true`).
  - Para pruebas con LLM real, coordinar claves y activar `LLM_ENABLED`/feature-flag en staging; limitar el número de llamadas y monitorizar coste.

---

Si deseas, puedo:
- añadir un checklist de aceptación paso a paso dentro de esta guía (formato imprimible), o
- crear un pequeño script de prueba que lance el wizard E2E en modo headful para demostración manual.

---

````

- Variables de entorno: copie `.env.playwright.example` a `.env.playwright` o exporte en CI las siguientes variables mínimas:
  - `BASE_URL` — URL donde corre la app de pruebas (ej. `http://localhost:8000`).
  - `E2E_ADMIN_EMAIL`, `E2E_ADMIN_PASSWORD` — credenciales de usuario E2E.
  - `LLM_PROVIDER` — en CI usar `mock` para evitar llamadas reales.

- Helpers recomendados (ubicación): `src/tests/e2e/helpers/`
  - `login.ts` — helper para login por UI o API.
  - `intercepts.ts` — helpers para interceptar endpoints LLM/preview/generate/status y servir fixtures.

- Fixtures LLM: colocar respuestas controladas en `src/tests/fixtures/llm/` y usarlas en `intercepts.ts`.

15. Playwright en CI — ejemplo de GitHub Actions (añadido)

- Archivo ejemplo: `.github/workflows/playwright-e2e.yml` — debe ejecutarse _después_ de tener el backend disponible (service / deploy de revisión) o usar un job que levante la app. El job mínimo se encarga de instalar Node, dependencias y navegadores, y fuerza `LLM_PROVIDER=mock`:

```yaml
name: Playwright E2E

on:
  pull_request:
    branches: [main]

jobs:
  e2e:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup Node
        uses: actions/setup-node@v4
        with:
          node-version: "18"
      - name: Install dependencies (frontend)
        working-directory: src
        run: npm ci
      - name: Install Playwright browsers
        working-directory: src
        run: npx playwright install --with-deps
      - name: Run Playwright E2E
        working-directory: src
        env:
          BASE_URL: ${{ secrets.E2E_BASE_URL }}
          LLM_PROVIDER: mock
          E2E_ADMIN_EMAIL: ${{ secrets.E2E_ADMIN_EMAIL }}
          E2E_ADMIN_PASSWORD: ${{ secrets.E2E_ADMIN_PASSWORD }}
        run: npm run test:e2e
```

16. LLM en CI / producción — configuración y recomendaciones (añadido)

- Variables y adapter:
  - `LLM_PROVIDER` — `mock|openai|anthropic|internal`.
  - `LLM_API_KEY` — sólo en entornos controlados; NO en logs ni reportes.
  - `LLM_MODE` — `production|test` (forzar `test` en CI).

- Recomendación de infraestructura:
  - Implementar un `LLMClient` adaptador con una opción `mock` que lea fixtures desde `tests/fixtures/llm/` cuando `LLM_PROVIDER=mock`.
  - En CI siempre exportar `LLM_PROVIDER=mock` para evitar llamadas externas. Añadir comprobación en startup/tests para fallar si `LLM_PROVIDER` es `production` y no se ha autorizado.

17. Edge-cases y tests adicionales (añadido)

- Redacción / redaction:
  - Test que envíe un prompt que contenga PII en preview y comprobar que el backend redactor elimina PII antes de persistir.
- Rate limits / errores LLM:
  - Mockear respuestas `429` y `5xx` en `GenerateScenarioFromLLMJob` y comprobar reintentos/backoff y marcado `failed` tras N intentos.
- Reintentos / idempotencia:
  - Tests de job que aseguren idempotencia (no duplicar `scenario` al reintentar).
- Validación del payload LLM:
  - Tests que comprueben la validación del JSON devuelto por LLM y rechazo cuando no cumple schema.

18. Seguridad / secretos (añadido)

- Nunca almacenar claves en memorias ni logs. Implementar función `redactPrompt()` antes de persistir o enviar a observabilidad.

Ejemplo (PHP pseudo):

```php
function redactPrompt(string $prompt): string {
  // simple redaction: emails, SSNs, long tokens
  $prompt = preg_replace('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', '[REDACTED_EMAIL]', $prompt);
  $prompt = preg_replace('/\b\d{3}[- ]?\d{2}[- ]?\d{4}\b/', '[REDACTED_SSN]', $prompt);
  return $prompt;
}
```

- En CI, sustituir LLM real por `mock` y asegurarse de que `openmemory.md` y otros índices no contengan secretos.

19. Artefactos y reporting (añadido)

- Configurar Playwright reporter (`html` + `junit`) y guardar en `test-results/` como artefactos en CI.
- Guardar capturas/videos de fallos (config en `playwright.config.ts` ya establece `video: 'retain-on-failure'`).

---

Actualiza la checklist arriba y marca los ítems E2E/CI/LLM/seguridad como implementados cuando añadas los helpers y workflow. 12. Ejemplo rápido de uso (operador)

- Completar cuestionario en wizard.
- Pulsar `Generar` → aparece `generation_id` y barra de progreso.
- Revisar resultado en modal → editar si se desea → `Guardar como borrador` o `Crear scenario`.

13. Cómo actualizar esta guía

- Editar `docs/GUIA_GENERACION_ESCENARIOS.md` y añadir notas de versiones en `openmemory.md`.

---

Archivo creado: [docs/GUIA_GENERACION_ESCENARIOS.md](docs/GUIA_GENERACION_ESCENARIOS.md)
