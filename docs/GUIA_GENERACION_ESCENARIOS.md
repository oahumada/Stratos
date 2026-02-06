# Guía: Generación Asistida de Escenarios (LLM-driven)

Resumen

- Propósito: plantilla y guía operativa para generar escenarios de planificación de talento mediante un LLM, integrable como un wizard en la UI.
- Alcance: cuestionario operador → prompt estructurado → llamada LLM (cola) → revisión humana → persistencia como `scenario` draft o `scenario_generation` record.

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

12. Ejemplo rápido de uso (operador)

- Completar cuestionario en wizard.
- Pulsar `Generar` → aparece `generation_id` y barra de progreso.
- Revisar resultado en modal → editar si se desea → `Guardar como borrador` o `Crear scenario`.

13. Cómo actualizar esta guía

- Editar `docs/GUIA_GENERACION_ESCENARIOS.md` y añadir notas de versiones en `openmemory.md`.

---

Archivo creado: [docs/GUIA_GENERACION_ESCENARIOS.md](docs/GUIA_GENERACION_ESCENARIOS.md)
