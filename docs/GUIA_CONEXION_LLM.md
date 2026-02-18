# Guía: Conectar la generación de escenarios con un API LLM real

Objetivo

- Describir el propósito, uso y arquitectura de la generación asistida de escenarios por LLM.
- Indicar pasos prácticos para conectar un proveedor LLM real (DeepSeek) a través del microservicio de inteligencia.

Estado de la implementación

- La lógica de construcción del prompt y la encolación existe: `app/Services/ScenarioGenerationService.php`.
- El job que consume la cola y persiste la respuesta existe: `app/Jobs/GenerateScenarioFromLLMJob.php`.
- UI/Wizard y store están implementados: `resources/js/pages/ScenarioPlanning/GenerateWizard/GenerateWizard.vue` y `resources/js/stores/scenarioGenerationStore.ts`.
- Existe un microservicio dedicado en Python (`python_services/`) que actua como orquestador de agentes (CrewAI).
- Hay redacción automática para prompts/respuestas: `app/Services/RedactionService.php`.

Arquitectura Actual (Stratos Intel Service)

- **Flow**:
    1. Operador completa wizard en UI → `scenarioGenerationStore.preview()` para ver prompt.
    2. Operador confirma → `scenarioGenerationStore.generate()` que hace `POST /api/strategic-planning/scenarios/generate`.
    3. Backend: `ScenarioGenerationController` identifica el proveedor activo (vía `config/services.php`).
    4. Si el proveedor es `intel`, se usa `StratosIntelService` para enviar la petición al **Microservicio Python** (FastAPI).
    5. El Microservicio Python emplea **CrewAI** (con DeepSeek/GPT) para generar el blueprint organizacional.
    6. Job: `GenerateScenarioFromLLMJob` recibe la respuesta, la redacciona y la persiste en `scenario_generations`.
    7. UI/polling consulta `fetchStatus()` hasta `complete`.

Componentes clave

- Service (Laravel): `app/Services/Intelligence/StratosIntelService.php`
- Microservicio (Python): `python_services/app/main.py`
- Job: `app/Jobs/GenerateScenarioFromLLMJob.php`
- Redaction: `app/Services/RedactionService.php`

Variables de entorno (Root .env)

```bash
INTEL_DEFAULT_PROVIDER=intel
PYTHON_INTEL_URL=http://localhost:8000
PYTHON_INTEL_TIMEOUT=30
```

Variables de entorno (Python Service .env)

```bash
OPENAI_API_KEY=sk-your-deepseek-key
OPENAI_API_BASE=https://api.deepseek.com
OPENAI_MODEL_NAME=deepseek-chat
STRATOS_MOCK_IA=false # Set to true for development without API cost
```

Pruebas y despliegue controlado

- **Entorno de pruebas (local/CI)**: usar `INTEL_DEFAULT_PROVIDER=mock` (o mockeando el microservicio en Python).
- **Staging**: habilitar `STRATOS_MOCK_IA=false` y configurar la API Key real. Revisar logs y coste por organización.
- **Run manual de verificación**:
    1. En staging, crear un escenario en UI, previsualizar prompt, autorizar.
    2. Verificar que `scenario_generations` cambia a `complete` y revisar `llm_response` redacted.
    3. Revisar `confidence_score`, `assumptions` y asegurar que el JSON cumple el esquema.

Seguridad y costes

- **Redactar tokens/PII**: `RedactionService` se aplica antes de persistir. Valida que `scenario_generations.prompt` se guarde redacted.
- **Monitoreo**: instrumentar métricas (conteo por org, latencia, tokens) y alertas de coste desde el dashboard de DeepSeek.

Checklist mínimo antes de habilitar en producción

- [x] Configurar `OPENAI_API_KEY` en el microservicio Python.
- [x] Establecer `STRATOS_MOCK_IA=false`.
- [x] Verificar conectividad entre Laravel y el microservicio (vía `curl` o logs).
- [x] Ejecutar la suite de tests de integración `ScenarioGenerationIntelTest.php`.

Contacto y referencias

- Código clave: `app/Services/Intelligence/StratosIntelService.php`, `python_services/app/main.py`, `app/Jobs/GenerateScenarioFromLLMJob.php`.
