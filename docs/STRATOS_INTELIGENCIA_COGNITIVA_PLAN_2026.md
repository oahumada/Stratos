# 🧠 Stratos: Plan de Mejora de Capacidades Cognitivas y Razonamiento (2026)

**Versión**: 1.0  
**Fecha**: 21 de marzo de 2026  
**Objetivo**: Reforzar las capacidades cognitivas de Stratos como "cerebro orquestador del talento" mediante memoria multi-nivel, RAG avanzado, orquestación de agentes y ciclos de aprendizaje.  
**Estado**: Documento de Planeamiento y Decisión

---

## 📊 RESUMEN EJECUTIVO

Stratos **ya tiene cimientos sólidos** en IA/ML:

- ✅ **AiOrchestratorService** — orquestación de agentes con múltiples proveedores (DeepSeek, OpenAI, ABACUS)
- ✅ **RAGASEvaluator** — evaluación de calidad LLM (provider-agnóstico)
- ✅ **EmbeddingService** — generación de embeddings (OpenAI, ABACUS, mock)
- ✅ **StratosGuideService** + **SentinelMonitorService** — agentes especializados
- ✅ **TalentOrquestatorService** + **TalentBlueprintService** — motor de talento híbrido
- ✅ **ScenarioGenerationService** + **SimuladorService** — generación de escenarios y simulación
- ✅ **Multi-tenant + Policies + Auditoría** — gobernanza integrada

**Lo que falta**: conectar y potenciar estos componentes con:

1. **Vector DB robusto** (pgvector + indexación automática)
2. **RAG pipeline cohesivo** (retrieval → ranking → prompt assembly)
3. **Memoria semántica y episódica** (grafo de conocimiento, caché inteligente)
4. **Loop de aprendizaje** (feedback loop, re-indexado, métricas de evolución)
5. **Orquestador supervisor** (planner + verifier + arbiter)
6. **Dashboard de inteligencia** (KPIs, hallucinations, latencias, fairness)

---

## 1️⃣ ESTADO ACTUAL DE STRATOS IA/ML

### 1.1 Servicios de IA Implementados

| Servicio                      | Propósito                                               | Estado                             | Ubicación                |
| ----------------------------- | ------------------------------------------------------- | ---------------------------------- | ------------------------ |
| **AiOrchestratorService**     | Orquesta agentes; llama `agentThink()`                  | ✅ Completo                        | `app/Services/`          |
| **RAGASEvaluator**            | Evalúa outputs LLM (contexto, faithfulness, relevancia) | ✅ Completo (integra Python RAGAS) | `app/Services/`          |
| **EmbeddingService**          | Genera embeddings (OpenAI, ABACUS, mock)                | ✅ Completo                        | `app/Services/`          |
| **LLMClient**                 | Cliente HTTP agnóstico para múltiples LLM               | ✅ Completo                        | `app/Services/`          |
| **StratosGuideService**       | Agente de mesa de ayuda (RAG-based)                     | ✅ Parcial (~50%)                  | `app/Services/`          |
| **SentinelMonitorService**    | Agente de monitoreo de calidad                          | ✅ Parcial (~40%)                  | `app/Services/`          |
| **CultureSentinelService**    | Análisis de cultura organizacional                      | ✅ Partial (~30%)                  | `app/Services/`          |
| **TalentOrquestatorService**  | Orquesta decisiones de talento híbrido                  | ✅ Parcial (~60%)                  | `app/Services/Talent/`   |
| **TalentBlueprintService**    | Genera blueprints de roles + skill gaps                 | ✅ Completo                        | `app/Services/Talent/`   |
| **ScenarioGenerationService** | Genera escenarios automáticamente                       | ✅ Completo                        | `app/Services/Scenario/` |
| **GapAnalysisService**        | Análisis de brechas de competencia                      | ✅ Completo                        | `app/Services/`          |
| **MatchingService**           | Matching de candidatos (human + synthetic)              | ✅ Completo                        | `app/Services/`          |

### 1.2 Infraestructura Soportada

| Componente                  | Implementado                   | Nota                                                                       |
| --------------------------- | ------------------------------ | -------------------------------------------------------------------------- |
| **LLM Providers**           | OpenAI, DeepSeek, ABACUS       | Agnóstico; agregar modelos locales (Ollama)                                |
| **Embeddings**              | OpenAI, ABACUS, mock           | Necesita fallback más robusto                                              |
| **Vector Storage**          | 🔄 Parcial (pgvector opcional) | Migraciones de extensión + columnas embedding; falta tabla genérica + jobs |
| **Knowledge Graph**         | ❌ No                          | Arquitectura + schema definido, no implementado                            |
| **Memory (short-term)**     | App context + session storage  | Funcional pero básico                                                      |
| **Memory (episodic)**       | LLMEvaluation records (logs)   | Funcional; puede mejorar indexación                                        |
| **Memory (semantic)**       | 🔄 En diseño (hacia vector DB) | Depende de despliegue completo de pgvector/RAG                             |
| **Orchestrator Supervisor** | Básico (Agent model)           | Necesita Planner + Verifier + Arbiter                                      |
| **Audit & Logging**         | ✅ Muy completo                | AuditTrailService + ComplianceAudit                                        |
| **Multi-tenant Scoping**    | ✅ Nativo                      | organization_id en todo lugar                                              |

### 1.3 Modelos y Tablas de Soporte

```sql
-- Agentes
✅ agents (name, persona, role_description, expertise_areas, provider, model, capabilities_config)

-- Evaluaciones LLM
✅ llm_evaluations (input_content, output_content, context_content, status, metrics: faithfulness, relevancy, coherence, etc.)

-- Vectores
❌ embeddings (text, embedding_vector, embedding_model, organization_id, resource_type, resource_id, metadata)

-- Knowledge entries
❌ knowledge_entries (text, embedding_id, source, category, organization_id, created_at)

-- Agent interactions (episodic memory)
✅ agent_interactions (agent_id, user_id, input, output, context, feedback, created_at) [puede ser AuditTrail]

-- Eventos de mejora (feedback loop)
❌ improvement_events (type: 'user_feedback', 'hallucination', 'success', user_rating, feedback_text, org_id)
```

---

## 2️⃣ PROPUESTA: MEJORAR CAPACIDADES COGNITIVAS

### 2.1 Pilares de Mejora

#### **Pilar 1: Memoria Multi-Nivel**

- **Memoria de Trabajo** (session-based): Contexto actual de interacción → ya existe (session storage)
- **Memoria Episódica** (histórica): Decisiones, acciones, resultados → mejorar `agent_interactions` + indexación
- **Memoria Semántica** (conceptual): Facts, documentos, perfiles, blueprints en embeddings → **NUEVA** (pgvector)
- **Memoria Simbólica** (relaciones): Grafo persona→rol→skill→scenario → **NUEVA** (schema + queries)

#### **Pilar 2: RAG Pipeline Cohesivo**

```
Retrieval (vector search) → Ranking (relevancy + recency) → Prompt Assembly → LLM → Post-Filtering → Output
```

- **Indexación automática**: perfiles, documentos, escenarios, blueprints → embeddings diarios
- **Retrieval robusto**: similaridad coseno + metadatos (org, tipo, recencia)
- **Ranking inteligente**: penalizar hallucinations conocidas, favorecer fuentes auditadas
- **Prompt assembly**: combinar contexto + ejemplos + guardrails
- **Post-filtering**: validar pertenencia a org, políticas, redaction de PII

#### **Pilar 3: Orquestación de Agentes Mejorada**

```
Plan (descompone objetivo) → Assign (executor pool) → Execute → Verify (critic) → Retry/Compensate
```

- **Planner**: devuelve sub-tareas con prioridad, dependencias
- **Ejecutores**: especializados (skill-matcher, evaluador psicométrico, simulador, guide)
- **Verifier/Critic**: valida outputs vs. reglas business, detecta contradicciones
- **Message Bus**: coordinación asíncrona (Redis Streams o RabbitMQ)

#### **Pilar 4: Loop de Aprendizaje Continuo**

```
Señales (feedback, métricas) → Análisis (paterns, drifts) → Mejora (re-indexado, fine-tuning) → Redeployment
```

- **Recolección**: feedback humano (👍👎), outcomes (aceptación, ROI), métricas de calidad
- **Replay**: ejemplos de éxito / fracaso para entrenamiento
- **Adaptación**: re-indexación de knowledge, LoRA en modelos locales
- **Monitoreo**: detectar degradación, drift de concepto, sesgo

---

## 3️⃣ ROADMAP PRIORIZADO (2-6 Meses)

### **Sprint 0: Fundación Vector DB + Indexación (Semana 1-2)**

**Objetivo**: Activar pgvector y crear pipeline automático de indexación.

**Tareas**:

1. Habilitar extensión `pgvector` en PostgreSQL
2. Crear tabla `embeddings` con schema + índices HNSW
3. Crear `EmbeddingIndexJob` que procese:
    - Perfiles (People)
    - Documentos (roles, blueprints, escenarios)
    - FAQ / Knowledge base (para StratosGuide)
4. Configurar cron para re-indexación diaria (delta)
5. Tests de índice + performance

**Entregables**:

- PR con migration pgvector
- `EmbeddingIndexJob` funcional
- Queries de retrieval (cosine similarity + filters)
- Documentation: architecture diagram + setup guide

**Métrica de Éxito**: Indexación de 10K+ documentos < 5 min; query tiempo < 100ms

**Estado actual (marzo 2026)**:

- 🔄 Parcial: existe soporte pgvector (migraciones de extensión y columnas de embeddings en tablas core/escenarios/capabilities/blueprints) y `EmbeddingService` con lógica de similitud.
- ⏳ Pendiente: tabla genérica `embeddings`, `EmbeddingIndexJob`, cron de reindexado y queries optimizadas sobre pgvector.

---

### **Sprint 1: RAG Pipeline Básico (Semana 3-4)**

**Objetivo**: Crear servicio RAG reutilizable e integrar en `StratosGuideService`.

**Tareas**:

1. Crear `RagService`:
    - `retrieve(query, org_id, filters)` → top-K documentos + scores
    - `rank(documents, query)` → rerank por relevancia/recencia
    - `assemble_prompt(query, docs, context)` → prompt final
    - `generate(query)` → LLM call con contexto
    - `post_filter(result)` → validar scoping, redact PII
2. Integrar en `StratosGuideService` para Q&A sobre:
    - Metodología de Cubo
    - Workflow de Scenarios
    - Uso de Blueprints
3. Crear endpoint `POST /api/rag/ask` (internal)
4. Tests (unit + integration) + logging
5. Documentación de prompts + ejemplos

**Entregables**:

- PR con `RagService`
- Mejorado `StratosGuideService` (RAG-based)
- Internal endpoint `/api/rag/ask`
- Dashboard: query latency, retrieval quality

**Métrica de Éxito**:

- Latencia P95: < 2s
- Relevancia percibida (manual test): > 80%
- Hallucination rate: < 5%

**Estado actual (marzo 2026)**:

- ✅ `RagService` implementado (MVP sobre `LLMEvaluation` + embeddings) y `RagController` exponiendo `POST /api/rag/ask`.
- ✅ Wayfinder generado para `/api/rag/ask` (routes TS en `resources/js/routes/rag`).
- 🔄 Pendiente: integración profunda en `StratosGuideService`, funciones separadas de ranking/assembly/post-filter y dashboard específico de calidad RAG.

---

### **Sprint 2: Logging, Evaluación y Métricas (Semana 5-6)**

**Objetivo**: Habilitar observabilidad completa y dashboard de calidad.

**Tareas**:

1. Mejorar logging en RAG/LLM:
    - Registrar prompts, inputs, outputs (sin secretos)
    - Hashes para auditoría + redaction de PII
    - Timestamped chains-of-thought (cuando sea seguro)
2. Crear tabla `intelligence_metrics`:
    - LLM latency, quality scores (faithfulness, relevance, coherence)
    - Tasa de hallucinations (detectadas por Critic)
    - User acceptance rate (feedback)
    - Fairness metrics (por demografía, si aplica)
3. Dashboard Blade/Vue:
    - Time-series de métricas
    - Alertas por SLA breach
    - Incident history
4. Integrar `RAGASEvaluator` para post-validation de outputs
5. Tests automáticos de calidad

**Entregables**:

- Mejorada tabla de logs (redaction integrada)
- `intelligence_metrics` table + jobs de cálculo
- Dashboard de KPIs
- Alerting on Slack / email

**Métrica de Éxito**:

- 100% de LLM calls evaluados (RAGAS)
- Dashboard actualizado cada 1h
- Ningún prompt sensible almacenado en texto plano

**Estado actual (marzo 2026)**:

- ✅ Logging PII-safe de prompts vía `LogsPrompts` trait (`LLMClient`, `AiOrchestratorService` + tests unitarios).
- ✅ Dashboards operativos: `QualityDashboard.vue` (QW-2) e `AgentMetricsDashboard.vue` (QW-5), ahora centralizados en `MonitoringHub.vue`.
- 🔄 Pendiente: tabla `intelligence_metrics`, jobs de agregación/alerting y time-series completas de KPIs.

---

### **Sprint 3: Critic/Verifier + Arbiter (Semana 7-9)**

**Objetivo**: Ampliar `AiOrchestratorService` con Planner, Executor pool, Verifier y Arbiter.

**Tareas**:

1. Crear `PlannerAgent`:
    - Input: objetivo alto-nivel
    - Output: árbol de sub-tareas (prioridad, deps)
    - Ejemplo: "Sugiere 3 candidatos para CTO" → [buscar skills, eval psicometric, score ROI]
2. Crear `VerifierAgent` / Critic:
    - Input: resultado de executor
    - Output: score (0-1), validaciones, contradicciones detectadas
    - Valida: scoping multi-tenant, políticas, hallucinations
3. Crear `ArbiterAgent` / Supervisor:
    - Asigna trabajo a ejecutores
    - Maneja retries, compensaciones
    - Decide: aceptar, rechazar, delegarIteración extra
4. Message bus (Redis Streams):
    - Pub/sub para coordinación
    - Dead-letter queue para errores
5. Tests: orquestación multi-agente, timeouts, retries
6. Documentación de arquitectura

**Entregables**:

- PR con Planner + Verifier + Arbiter agents
- Redis Streams setup
- Message bus patterns (saga, choreography)
- E2E tests de orquestación
- Architecture diagram

**Métrica de Éxito**:

- Latencia Planner: < 1s
- Verifier accuracy: > 90%
- Retry success rate: > 95%
- Multi-agent tasks en paralelo (no bloqueantes)

---

### **Sprint 4: Learning Loop + Conocimiento Evoluciona (Semana 10-12)**

**Objetivo**: Crear feedback loop y re-entrenamiento automático.

**Tareas**:

1. Crear tabla `improvement_feedback`:
    - user_id, agent_id, evaluation_id, rating (1-5), feedback text
    - Tags: "hallucination", "irrelevant", "incomplete", "excellent"
2. Job `ProcessImprovementSignals`:
    - Agregar feedback por tipo/período
    - Detectar patrones de error
    - Marcar prompts para revisión
3. Job `ReindexKnowledge`:
    - Incluir ejemplos exitosos como embeddings
    - Downrank documentos con errores conocidos
    - Actualizar metadatos
4. Opcional: LoRA fine-tuning en modelos locales (Ollama)
5. Versioning: guardar snapshots de embeddings + prompts
6. Tests: validar que feedback loop mejora métricas

**Entregables**:

- `improvement_feedback` table + UI para rating
- Jobs: ProcessImprovementSignals, ReindexKnowledge
- Versioning system (embeddings snapshots)
- Metrics: hallucination reduction post-loop
- Documentation

**Métrica de Éxito**:

- Hallucination rate ↓ 50% en 2 semanas
- User acceptance rate ↑ 10%
- Knowledge base re-indexado sin downtime

**Estado actual (marzo 2026)**:

- ⏳ No iniciado: tabla de feedback, jobs de señal y reindexado y versionado aún pendientes.

---

## 4️⃣ QUICK WINS (Implementables en Días)

Antes de empezar Sprint 0, estos pueden dar valor inmediato:

### **QW-1: Mejorar Logging de Prompts (1-2 días)** ✅ IMPLEMENTADO

```php
// app/Traits/LogsPrompts.php
public static function logPrompt(string $prompt, string $output, ?array $metadata = null) {
    $hash = hash('sha256', $prompt); // PII-safe
    Log::channel('llm_prompts')->info('LLM Call', compact('hash', 'output', 'metadata'));
}
```

**Beneficio**: Trazabilidad sin riesgos de cumplimiento.

### **QW-2: Dashboard de Salud RAGASEvaluator (1-2 días)** ✅ IMPLEMENTADO

Crear Blade/Vue con gráficos de `llm_evaluations`:

- Faithfulness trend ✅ (tendencias de calidad en `QualityDashboard.vue`)
- Hallucination detection rate ✅ (KPI de **Tasa de Alucinación**)
- Provider comparison ✅ (gráfico "Evaluaciones por Proveedor")
- **Beneficio**: Visibilidad de calidad LLM en tiempo real. ✅ LOGRADO

### **QW-3: Endpoint Interno `/api/rag/ask` (2-3 días)** ✅ IMPLEMENTADO

Usar `EmbeddingService` + existing LLM providers para un Q&A básico.

```php
POST /api/rag/ask
{
    "question": "¿Cómo funciona el matching de candidatos?",
    "organization_id": 1
}
→ {
    "answer": "...",
    "sources": [{ "type": "doc", "id": 123, "relevance": 0.95 }],
    "confidence": 0.87
}
```

**Beneficio**: PoC de RAG antes de infra completa.

### **QW-4: Redaction Service para PII (2-3 días)** ✅ IMPLEMENTADO

Polir `RedactionService` existente y usarlo en todos los logs.

```php
$redacted = RedactionService::redact($prompt, ['email', 'phone', 'ssn']);
```

**Beneficio**: GDPR-ready logging sin riesgos.

### **QW-5: Agent Interaction Metrics (1-2 días)** ✅ IMPLEMENTADO

Query `agent_interactions` + `llm_evaluations` para dashboard:

- Call count por agent ✅ (`AgentMetricsDashboard.vue` – gráfico "Interacciones por Agente")
- Success rate (evaluación RAGAS) ✅ (KPI **Tasa de Éxito %**)
- Avg latency ✅ (KPI **Latencia Promedio** + bloque de percentiles P50/P95/P99)
- **Beneficio**: KPIs de agentes sin código nuevo. ✅ LOGRADO

**Total Quick Wins**: 7-14 días; valor inmediato + contexto para Sprint 0.

---

### 📊 ESTADO DE IMPLEMENTACIÓN (22 de marzo de 2026)

**Estado Quick Wins:**

- ✅ **QW-1**: Logging de prompts implementado (`LogsPrompts` + canal `llm_prompts` + tests)
- ✅ **QW-2**: Dashboard de Salud RAGASEvaluator completo (QualityDashboard.vue)
- ✅ **QW-3**: Endpoint interno `/api/rag/ask` operativo (RagService + RagController)
- ✅ **QW-4**: Redaction Service PII estandarizado en logs clave (`RedactionService` + canal `redaction` + tests)
- ✅ **QW-5**: Agent Interaction Metrics completo (AgentMetricsDashboard.vue)

**Sprint Progress:**

- ✅ **Sprint 0 (Embeddings)**: COMPLETADO - Tabla genérica `embeddings`, `EmbeddingService`, FAQ indexing, delta reindex command
- ✅ **Sprint 1 (RAG Pipeline)**: COMPLETADO - `RagService` con 5 métodos, `StratosGuideService` integration, FAQ-based retrieval, RAG metrics logging
- ✅ **Bloque 4 - Sprint 2 (Intelligence Metrics Infrastructure)**: COMPLETADO - Fase 1 (storage) + Fase 2 (daily aggregation)
  - **Fase 1**: `IntelligenceMetric` model + migration + factory, RagService::logMetric() auto-capture, 6/6 tests passing ✅
  - **Fase 2**: `IntelligenceMetricAggregate` model + service + daily job + scheduler (01:00 UTC), custom percentile calculations, 8/8 tests passing ✅
  - **Total tests Bloque 4**: 25/25 passing (includes integration tests with RagAskTest) ✅

**Extra no previsto en el plan original:**

- ✅ **MonitoringHub.vue**: Hub centralizado de Inteligencia & Monitoreo accesible desde Command Center y sidebar, agrupando QW-2, QW-5 y futuros dashboards.
- ✅ **GuideFaq + FAQ Knowledge Base**: Sistema de preguntas frecuentes para StratosGuide, indexado en embeddings genericos

**Resumen de avance:**

- Quick Wins: 5/5 completados (100%)
- Sprint 0-1: Embeddings + RAG Pipeline completados al 100%
- **Bloque 4 (Sprint 2)**: Infrastructure de metrics 100% operacional
  - Fase 1 (Per-request storage): `IntelligenceMetric` capturando automáticamente en cada call RAG ✅
  - Fase 2 (Daily aggregation): `IntelligenceMetricAggregate` con P50/P95/P99, success rates, averages; job ejecuta diariamente a 01:00 UTC ✅
- Capa de visibilidad: dashboards de calidad LLM y de agentes ya operativos y unificados en el nuevo hub.

---

## 5️⃣ GOBERNANZA, SEGURIDAD Y PRIVACIDAD

### 5.1 Principios

1. **Multi-tenant by Design**
    - Todas las queries vectoriales filtran por `organization_id`
    - Policies + Gates validadas en cada endpoint
    - Tests automáticos de isolation

2. **Redaction & Auditable**
    - Nunca almacenar PII en embeddings o prompts
    - Hash-based linkage a datos reales
    - Chain-of-thought solo si auditado

3. **Explicabilidad**
    - Cada decisión vinculada a fuente (documento, agente, modelo)
    - Scores de confianza publicados
    - "Why" requests: mostrar retrieval docs

4. **Cumplimiento**
    - GDPR: derecho al olvido → borrar embeddings + references
    - ISO 30414: métricas de HR mesurado + auditable
    - SOC 2: logging, alerting, incident response

### 5.2 Checklist de Implementación

- [ ] pgvector encryption en tránsito (SSL)
- [ ] Redaction automática de prompts antes de persistir
- [ ] Índices en `organization_id` para queries rápidas
- [ ] Policies en RagService + RAGController
- [ ] Tests de multi-tenant isolation (fixture-based)
- [ ] Audit trail de cambios en embeddings
- [ ] SLA monitoring y alerting
- [ ] Incident response protocol (hallucination detected → alert, disable, investigate)

---

## 6️⃣ INDICADORES DE ÉXITO (KPIs)

| KPI                         | Target       | Timeline  |
| --------------------------- | ------------ | --------- |
| **Hallucination Rate**      | < 3%         | Semana 12 |
| **RAG Latency (P95)**       | < 2s         | Semana 4  |
| **Knowledge Index Size**    | > 50K docs   | Semana 2  |
| **User Acceptance Rate**    | > 80%        | Semana 8  |
| **Critic Accuracy**         | > 90%        | Semana 9  |
| **Feedback Loop Impact**    | Halluc ↓ 50% | Semana 12 |
| **Zero Security Incidents** | 100%         | Ongoing   |
| **GDPR Compliance**         | 100%         | Semana 6  |

---

## 7️⃣ DEPENDENCIAS Y RIESGOS

### 7.1 Dependencias

| Item                                     | Propietario       | Est.     |
| ---------------------------------------- | ----------------- | -------- |
| PostgreSQL 15+ con pgvector              | DevOps            | 1-2 días |
| Redis (para message bus)                 | DevOps            | 1 día    |
| Python RAGAS service (ya existe)         | IA Team           | ✅ Listo |
| LLM providers (OpenAI, DeepSeek, ABACUS) | DevOps + Product  | ✅ Listo |
| Knowledge base docs (formato, ingesta)   | Product + Content | 3-5 días |

### 7.2 Riesgos y Mitigaciones

| Riesgo                                      | Probabilidad | Mitigación                                           |
| ------------------------------------------- | ------------ | ---------------------------------------------------- |
| Vector DB performance degrade con 100K docs | Media        | Pre-tuning de índices; monitoring; sharding strategy |
| Hallucination rate no baja                  | Media        | Robustecer Critic; feedback loop; fine-tuning        |
| Latencia RAG > SLA durante peak             | Media        | Caching; query optimization; load testing            |
| PII leak en logs                            | Baja         | Redaction en pipeline + tests automáticos            |
| Integración con agentes existentes compleja | Media        | MVP con un agente; generalizar después               |

---

## 8️⃣ PRÓXIMOS PASOS (Decisión Requerida)

### Opción A: Acelerar (Recomendado)

- **Semana 1**: Completar QW-1 a QW-5 + planificar Sprint 0.
- **Semana 2-3**: Sprint 0 (pgvector + indexación).
- **Semana 4**: Sprint 1 (RAG pipeline).
- Paralelo: documentación, tests, knowledge base prep.

**Palanca**: 1 engineer full-time (~40h/sem); soporte DevOps (~10h/sem).

### Opción B: Iterativo (Default)

- **Semana 1-2**: QW-1 a QW-5 + análisis detallado.
- **Semana 3-4**: Sprint 0 piloto (pgvector local only).
- **Semana 5-8**: Sprint 1 (RAG) después de validar pgvector.
- Paralelo: feedback loops en sprints anteriores.

**Palanca**: 1 engineer full-time; puede variar según prioridades.

### Opción C: Incremental (Bajo Riesgo)

- Semana 1-2: QW-1, QW-2, QW-5 (dashboards, logging).
- Semana 3-6: Sprint 0 + 1 (pgvector + RAG) en paralelo con feature development.
- Semana 7+: Iteration basada en feedback real.

**Palanca**: 1 engineer 50% + soporte ad-hoc.

**Recomendación**: **Opción A** — el costo de complejidad futura (mantenimiento de múltiples patrones) supera la inversión ahora. Además, el valor de negocio (reducción de hallucinations, recomendaciones precisas) justifica la prioridad.

---

## 9️⃣ Backlog de Implementación Detallado

### Bloque 1 – Quick Win pendiente

- [x] **QW-4 – Redaction Service PII**
    - [x] Revisar todos los puntos donde se loguean prompts/outputs (LLMClient, AiOrchestratorService, servicios de agentes).
    - [x] Estandarizar uso de `RedactionService::redact()` antes de escribir en `llm_prompts` y otros canales.
    - [x] Añadir tests que validen que emails/teléfonos/IDs sensibles nunca aparecen en logs.

### Bloque 2 – Sprint 0: Vector DB + Indexación

- [x] Diseñar y crear tabla genérica `embeddings` (organization_id, resource_type, resource_id, metadata, embedding).
- [x] Implementar `EmbeddingIndexJob` para indexar:
    - [x] Personas (People) – indexación básica con nombre/email/rol.
    - [x] Roles – apoyado en `EmbeddingService::forRole()`.
    - [x] Escenarios – apoyado en `EmbeddingService::forScenario()`.
    - [x] FAQ / knowledge base de StratosGuide.
        - Modelo `GuideFaq` + indexación en `embeddings`.
        - `RagService` soporta ahora `contextType = 'guide_faq'` (además de `evaluations` / `all`).
        - `StratosGuideService::askGuide()` invoca `RagService->ask()` con contexto `guide_faq` para responder dudas funcionales.
- [ ] FAQ / knowledge base de StratosGuide.
- [x] Añadir comando/cron para reindexado delta (solo cambios recientes).
- [x] Ajustar `EmbeddingService` para leer/escribir en `embeddings` cuando pgvector esté disponible (lectura vía `findSimilar` apuntando a tabla genérica con fallback legacy).

### Bloque 3 – Sprint 1: RAG Pipeline "bien hecho"

- [x] Refactor de `RagService` en métodos explícitos:
    - [x] `retrieve(query, org_id, filters)`.
    - [x] `rank(documents, query)`.
    - [x] `assemblePrompt(query, docs, context)`.
    - [x] `generate(query)`.
    - [x] `postFilter(result)` (scoping, redacción PII).
- [x] Integrar `RagService` en `StratosGuideService` para FAQs de metodología, escenarios y blueprints.
- [x] Alinear `/api/rag/ask` con el frontend usando rutas Wayfinder (TS) donde aplique.
- [x] Añadir primeras métricas de latencia/éxito de RAG (logs estructurados + counters básicos).

### Bloque 4 – Sprint 2: Métricas de Inteligencia

#### **Fase 1: Per-Request Metrics Storage** ✅ COMPLETADO

- [x] Diseñar y crear tabla `intelligence_metrics` (organization_id, metric_type, source_type, latency, context_count, confidence, success, metadata).
- [x] Integrar captura automática en `RagService::logMetric()` — se ejecuta en paths vacío/éxito.
- [x] Factory `IntelligenceMetricFactory` para tests con datos realistas.
- [x] Tests: 6/6 passing (multi-tenant isolation, casting JSON/float/int, aggregation queries).

#### **Fase 2: Daily Aggregation Infrastructure** ✅ COMPLETADO

- [x] Diseñar y crear tabla `intelligence_metric_aggregates` (22 fields: date_key, totals, success_rate, P50/P95/P99 percentiles, averages).
- [x] Implementar `IntelligenceMetricsAggregator` service (169 lines):
  - Custom percentile calculation (array sort + index-based positioning) sin dependencias externas.
  - Grouping by metric_type | source_type.
  - Upsert with unique constraint para idempotencia.
  - Manejo de null organization_id para métricas globales.
- [x] Crear `AggregateIntelligenceMetricsDaily` job (ShouldQueue, constructor con date param para backfill).
- [x] Registrar en scheduler: `$schedule->job(...)->dailyAt('01:00')` (UTC).
- [x] Tests: 8/8 passing (percentile accuracy, multi-type, all-orgs, upsert, date defaulting, null org scoping).
- [x] Pint formatting: PASS.

#### **Fase 3: Dashboard & API Endpoints** 🚀 PRÓXIMO

- [ ] Crear endpoint `GET /api/intelligence/aggregates` (filtros: metric_type, date_from, date_to, org_id).
- [ ] Dashboard Vue: time-series charts (latency trends, success rate), SLA indicators, incident history.
- [ ] Caching (1h) en agregados (datos no actualizan hasta 01:01 UTC).
- [ ] Integration con ApexCharts/ECharts para visualización de tendencias.

### Bloque 5 – Sprints 3 y 4: Orquestación y Learning Loop

- [ ] Definir contratos e interfaces para `PlannerAgent`, `VerifierAgent` y `ArbiterAgent` (inputs/outputs, errores, timeouts).
- [ ] Diseñar modelo/tablas `improvement_feedback` y taxonomía de tags (hallucination, irrelevant, incomplete, excellent).
- [ ] Especificar comportamiento de jobs `ProcessImprovementSignals` y `ReindexKnowledge` (sin implementarlos aún).

---

## 🎯 RESUMEN: HOJA DE RUTA VISUAL

```
┌─────────────────────────────────────────────────────────────────────┐
│                       STRATOS INTELLIGENCE 2026                    │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  QW-1 QW-2  │  QW-3 QW-4  │  QW-5          │ Sprint 3/4            │
│  ✅ Logging │  ✅ RAG     │  ✅ Metrics    │ Advanced              │
│  ─────────  │  ────────   │  Lite          │ ────────              │
│  DONE       │  DONE       │  (3d)          │                       │
│             │             │                │                       │
│  ✅ Sprint0 │  ✅ Sprint1 │  Bloque 4:     │ Sprint 3: Critic+     │
│  pgvector   │  RAG Core   │  Metrics       │ Orchest. Supervisor   │
│  • Emb DB   │  • Service  │  DONE!         │ (3w) + Msg Bus        │
│  • HNSW     │  • Endpoint │  ─────────     │                       │
│  • FAQ Idx  │  • GuideRAG │  F1: Storage   │ Sprint 4: Learning    │
│  DONE       │  DONE       │  F2: Agg (✅)   │ Loop (3w)             │
│             │             │  F3: Dash (→pb)│ • Feedback mechanism  │
│             │             │  • 25/25 tests │ • Re-index job       │
│             │             │  • Sched 01UTC │ • Versioning         │
│             ├─────────────┴────────────────┴───────────────────────┤
│             │           COMPLETADO: Core RAG + Observability        │
│             │           • RagService central ✅                     │
│             │           • Integr StratosGuide ✅                    │
│             │           • Endpoint /api/rag/ask ✅                  │
│             │           • IntelligenceMetric storage ✅             │
│             │           • Daily aggregation job ✅                  │
│                                                                     │
│  ✅ DONE: 7-14d (QW) + 2w (S0) + 2w (S1) + 1w (B4.F1+F2)          │
│  🚀 NEXT: B4.F3 Dashboard (1w) → Critic+Learning (6w)             │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 📝 Documentos de Referencia

- `docs/AI_AGENTS_STRATEGY.md` — Estrategia general de agentes
- `docs/ROADMAP_ESTRATEGICO_2026.md` — Visión 2026
- `app/Services/AiOrchestratorService.php` — Implementación actual
- `app/Services/RAGASEvaluator.php` — Evaluador de calidad
- `app/Services/EmbeddingService.php` — Generador de embeddings
- `docs/SESION_2026_03_20_RESUMEN.md` — últimas decisiones

---

## 👤 Próximos Responsables

- **Product/Strategy**: Validar KPIs y prioridades
- **Engineering**: Asignar recurso full-time para Quick Wins + Sprint 0
- **DevOps**: Preparar pgvector + Redis infrastructure
- **QA/Security**: Plan de testing + auditoría

---

**Fin del documento.** Recomendación: revisar con equipo técnico + product, validar opción elegida, y comenzar Quick Wins en la próxima sprint.
