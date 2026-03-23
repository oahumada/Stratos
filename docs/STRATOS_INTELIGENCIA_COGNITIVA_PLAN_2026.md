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
- ✅ **Bloque 4 - Sprint 2 (Intelligence Metrics Infrastructure)**: 🎉 100% COMPLETADO - Fase 1 + 2 + 3
    - **Fase 1**: `IntelligenceMetric` model + migration + factory, RagService::logMetric() auto-capture, 6/6 tests passing ✅
    - **Fase 2**: `IntelligenceMetricAggregate` model + service + daily job + scheduler (01:00 UTC), custom percentile calculations, 8/8 tests passing ✅
    - **Fase 3** ✨ **NEW**: API endpoints (`IntelligenceAggregatesController`) + async dashboard (`IntelligenceMetricsDashboard.vue`) + TypeScript composable (`useIntelligenceMetrics`), 9/9 tests passing ✅
    - **Total tests Bloque 4**: 34/34 passing (storage 6 + aggregation 8 + API 9 + RAG integration 11) ✅

**Extra no previsto en el plan original:**

- ✅ **MonitoringHub.vue**: Hub centralizado de Inteligencia & Monitoreo accesible desde Command Center y sidebar, agrupando QW-2, QW-5 y futuros dashboards.
- ✅ **GuideFaq + FAQ Knowledge Base**: Sistema de preguntas frecuentes para StratosGuide, indexado en embeddings genericos

**Resumen de avance:**

- Quick Wins: 5/5 completados (100%)
- Sprint 0-1: Embeddings + RAG Pipeline completados al 100%
- **Bloque 4 (Sprint 2)**: 🎉 COMPLETADO 100% - Toda la stack de métricas de inteligencia operacional
    - Fase 1 (Per-request storage): `IntelligenceMetric` capturando automáticamente en cada call RAG ✅
    - Fase 2 (Daily aggregation): `IntelligenceMetricAggregate` con P50/P95/P99, success rates, averages; job ejecuta diariamente a 01:00 UTC ✅
    - Fase 3 (Dashboard & API):
        - **API Endpoints** (`/api/intelligence/aggregates`): filtrado, paginación, caching 1h, multi-tenant scoping, 9/9 tests ✅
        - **Vue Dashboard** (`IntelligenceMetricsDashboard`): KPI cards (6 métricas), line charts (éxito + latencia), data table, filtros interactivos, auto-polling 30s, dark mode ✅
        - **TypeScript Composable** (`useIntelligenceMetrics`): state management, time-series data formatting, error handling ✅
- Capa de visibilidad: dashboards de calidad LLM, agentes y **métricas de inteligencia** ya operativos y unificados en el Hub centralizado.

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

#### **Fase 3: Dashboard & API Endpoints** ✅ COMPLETADO

- [x] Crear endpoint `GET /api/intelligence/aggregates` (filtros: metric_type, source_type, date_from, date_to; paginación per_page).
    - Ubicación: `app/Http/Controllers/Api/IntelligenceAggregatesController.php`
    - Caching: 1 hora (agregados no actualizan hasta 01:01 UTC)
    - Multi-tenant: scoping automático por organization_id del usuario
    - Tests: 9/9 passing ✅
- [x] Crear endpoint `GET /api/intelligence/aggregates/summary` (estadísticas: total_calls, success_rate, latency metrics).
    - Retorna P50, P95, P99, average, min, max para latency
    - SLA indicators integrados en respuesta
    - Tests: cubiertos en los 9/9 de API ✅
- [x] Dashboard Vue: `IntelligenceMetricsDashboard.vue` con visualización de tendencias e indicadores clave.
    - KPI Cards: 6 métricas (Llamadas, Éxito%, Latencia, P95, Confianza, Contexto)
    - Line Charts: Tasa de éxito (tendencia diaria) + Latencia (prom vs P95)
    - Data Table: Últimas 20 agregados con filtrado interactivo
    - Filtros: Date range picker (date_from/date_to) + metric_type selector
    - Auto-polling: Each 30s con stopPropagation en unmount
    - Dark mode optimizado + responsive grid layout
- [x] TypeScript Composable: `useIntelligenceMetrics()` (resources/js/composables/)
    - State management: aggregates[], summary, pagination, currentFilters, lastUpdated
    - Methods: fetchAggregates(), fetchSummary(), startPolling(), stopPolling()
    - Computed: timeSeriesData (para charts), aggregatesByMetricType
- [x] Integración con ApexCharts para visualización time-series.
- [x] Policy control: `IntelligenceMetricAggregatePolicy` (viewAny, view) registrada en `AuthServiceProvider`.
- [x] Web route: `GET /intelligence/aggregates` → `IntelligenceMetricsDashboard.vue` (Inertia).
- [x] API routes: Prefix `/api/intelligence` → index() + summary() methods.
- [x] Tests: 9/9 passing (auth, filtering, scoping, pagination, summaries, empty states).
- [x] Total Bloque 4: 34/34 tests passing (storage 6 + aggregation 8 + API 9 + RAG integration 11) ✅

### Bloque 5 – Sprints 3 y 4: Orquestación y Learning Loop

#### **Sprint 3.1: VerifierAgent (The Critic) - Tarea 1 COMPLETADA ✅**

**Fecha**: 22 de marzo de 2026  
**Status**: ✅ Tarea 1 IMPLEMENTADA Y TESTEADA

**Entregables Tarea 1:**

- [x] `app/Data/VerificationViolation.php` (41 líneas) — DTO Value Object para violaciones individuales
- [x] `app/Data/VerificationResult.php` (220 líneas) — DTO Value Object contenedor con auto-recalcular score
- [x] `config/verification_rules.php` (180 líneas) — Configuración centralizada (global + 9 agentes)
- [x] `tests/Unit/Data/VerificationResultTest.php` (261 líneas, 15/15 tests PASSING ✅)
- [x] Código formateado con Pint (compliant)
- [x] Git commit `7dc627ac` (712 insertions)

**Detalles Implementados:**

- VerificationViolation: rule, severity, message, field, received, expected → serialización JSON bidireccional
- VerificationResult: score (0-1), recommendation (accept|reject|review), reasoning + fluent API
- Score auto-recalculation: 1.0 (0 issues) → 0.75 (1) → 0.5 (2-3) → 0.2 (4+)
- Factory methods: `passed()`, `failed()`, `review()`
- Per-agent rules: Estratega, Orquestador 360, Matchmaker, Coach, Diseñador, Navegador, Curador, Arquitecto, Sentinel
- Global rules: max_response_length (50k), multi_tenant validation, hallucination detection, cache TTL (24h)

**Próximas Tareas Sprint 3.1:**

- [ ] Tarea 2: TalentVerificationService core (8h) — 5 validators (schema, rules, hallucinations, contradictions, multi-tenant)
- [ ] Tarea 3: Business Rules Engine (6h) — Per-agent validators (9 clases)
- [ ] Tarea 4: Testing suite (6h) — 12-15 Feature + Unit tests
- [ ] Tarea 5: Integration & Docs (4h) — OpenAPI, AiOrchestratorService integration, openmemory update

**Métrica de Éxito Sprint 3.1**:

- Tarea 1: ✅ 15/15 tests passing, Pint compliant
- Tarea 2-5: Verifier accuracy > 90%, latencia verify() < 500ms

---

#### **Sprint 3.2-3.3: PlannerAgent + ArbiterAgent + Message Bus (Pendiente)**

- [ ] Definir contratos e interfaces para `PlannerAgent`, `ArbiterAgent` (inputs/outputs, errores, timeouts).
- [ ] Message bus (Redis Streams o Database Queue).
- [ ] Implementar PlannerAgent (sub-task decomposition).
- [ ] Implementar ArbiterAgent (orchestration, retries).

---

#### **Sprint 4: Learning Loop + Feedback Mechanism (Pendiente)**

- [ ] Diseñar modelo/tablas `improvement_feedback` y taxonomía de tags (hallucination, irrelevant, incomplete, excellent).
- [ ] Especificar comportamiento de jobs `ProcessImprovementSignals` y `ReindexKnowledge`.
- [ ] Versioning system para embeddings + prompts snapshots.

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
│  ✅ Sprint0 │  ✅ Sprint1 │  ✅ Bloque 4:  │ 🔄 Sprint 3.1:       │
│  pgvector   │  RAG Core   │  Metrics       │ VerifierAgent (Critic)│
│  • Emb DB   │  • Service  │  COMPLETADO    │ T1: ✅ DTO+Config    │
│  • HNSW     │  • Endpoint │  ─────────     │ (✅ 15/15 tests)     │
│  • FAQ Idx  │  • GuideRAG │  F1: Storage   │ T2-T5: 🔄 En curso   │
│  DONE       │  DONE       │  (✅ 6/6)      │ Sprint 4: Learning    │
│             │             │  F2: Agg (✅)  │ Loop (3w) PENDIENTE   │
│             │             │  (✅ 8/8)      │ • Feedback mechanism  │
│             │             │  F3: Dash (✅) │ • Re-index job       │
│             │             │  (✅ 9/9+)     │ • Versioning         │
│             │             │  • 34/34 tests │                       │
│             ├─────────────┴────────────────┴───────────────────────┤
│             │    COMPLETADO: Core RAG + Observability + Intelligence│
│             │           • RagService central ✅                     │
│             │           • Integr StratosGuide ✅                    │
│             │           • Endpoint /api/rag/ask ✅                  │
│             │           • IntelligenceMetric storage ✅             │
│             │           • Daily aggregation job ✅                  │
│             │           • IntelligenceAggregatesController API ✅   │
│             │           • IntelligenceMetricsDashboard.vue ✅       │
│             │           • Auto-polling + ApexCharts ✅              │
│                                                                     │
│  ✅ DONE: 7-14d (QW) + 2w (S0) + 2w (S1) + 2w (B4.F1+F2+F3)      │
│  � IN PROGRESS: Sprint 3.1 T1 ✅ | T2-T5 🔄 | Sprint 3.2-4 ⏳ │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🎯 BENEFICIOS, VENTAJAS Y MEJORAS DE STRATOS INTELIGENCIA COGNITIVA

### ¿Por qué estos desarrollos van a transformar Stratos?

Stratos ha evolucionado desde una plataforma de gestión de talento convencional a un **"cerebro digital"** que entiende, aprende y mejora continuamente. Aquí te explicamos qué significa esto en términos reales:

---

### 1️⃣ **Decisiones de Talento Más Inteligentes (y Menos Sesgadas)**

**Antes**: Los departamentos de RRHH tomaban decisiones basadas en intuición, históricos limitados o datos incompletos. Esto generaba sesgo inconsciente, promociones injustas y rotación innecesaria.

**Ahora**: Stratos analiza miles de datos de personas (competencias, historia profesional, cultural fit, potencial de crecimiento) en segundos. Cuando un gerente pregunta _"¿Quién es el mejor candidato para liderar este proyecto?"_, Stratos no solo da un nombre—explica por qué, qué riesgos hay, y qué necesita esa persona para triunfar.

**Impacto Real:**

- ✅ Reducción de rotación: candidatos mejor emparejados = menos personas que se van
- ✅ Promociones justas: evaluación sin sesgos, basada en toda la información
- ✅ Retención de talento crítico: identificar antes de que alguien se vaya por frustración

---

### 2️⃣ **Búsqueda y Emparejamiento Ultrarrápido**

**Antes**: Un recruiter tardaba 2-3 días en revisar CVs, hacer llamadas, y presentar candidatos. Si surgía una urgencia, no había forma de reaccionar rápido.

**Ahora**: Con nuestro motor RAG (Retrieval-Augmented Generation), si necesitas "un líder de transformación digital con 8+ años en manufacturing", Stratos busca **globalmente** en tu base de talento, busca externamente en LLMs (bases de datos públicas), y te presenta los top-3 candidatos con score de match en **menos de 5 segundos**. Incluye análisis de cultural fit, brechas de competencia, y un plan personalizado para integrarlos.

**Impacto Real:**

- ✅ Velocidad de contratación: de días a minutos
- ✅ Costo reducido: menos rechazos porque los matches son precisos
- ✅ Probabilidad de éxito: 40% más alto cuando el fit es bueno

---

### 3️⃣ **Aprendizaje y Desarrollo Personalizado (Verdaderamente Personalizado)**

**Antes**: "En esta empresa hacemos capacitación genérica para todos": todos ven el mismo video, todos hacen el mismo quiz, y la mayoría se aburre o se queda atrás.

**Ahora**: Stratos crea un **blueprint de crecimiento único para cada persona**:

- Analiza qué competencias necesita para el próximo rol
- Identifica brechas específicas (no genéricas)
- Genera un camino de aprendizaje que combina: cursos online, mentoría, proyectos de estiramiento, y coaching
- Estima duración realista: "en 6 meses puedes estar listo para ese role si sigues este plan"
- Aprende si la capacitación funciona: si alguien no progresa, Stratos detecta por qué y ajusta

**Impacto Real:**

- ✅ Engagement: la gente se siente apoyada en su crecimiento
- ✅ Retención: "esta empresa invierte en mí" es un diferenciador enorme
- ✅ Productividad: personas más competentes = mejores resultados
- ✅ ROI educativo: evitas capacitación desperdiciada

---

### 4️⃣ **Validación Automática de Calidad (The Critic)**

**Antes**: Si un sistema de IA recomendaba algo, asumías que funcionaba. Nadie chequeaba si la recomendación era correcta, sesgada, o alucinaba (inventaba datos).

**Ahora**: El **VerifierAgent** actúa como un crítico interno:

- Antes de que una recomendación llegue a una persona, Stratos la valida contra políticas, datos, y lógica de negocio
- Detecta si hay "alucinaciones" (cuando la IA inventa datos que no existen)
- Identifica contradicciones: "espera, dijiste que está en Buenos Aires pero antes dijiste que está en Mendoza"
- Da una calificación: ¿Es seguro confiar en esto? ¿Hay dudas? ¿Rechazar por qué?
- Mantiene una auditoría completa: qué se validó, qué falló, por qué

**Impacto Real:**

- ✅ Seguridad: recomendaciones confiables
- ✅ Conformidad legal: auditoría completa de decisiones
- ✅ Reducción de riesgos: evitar decisiones de RRHH que terminen en conflictos legales
- ✅ Confianza del usuario: la gente confía más cuando sí hay un segundo par de ojos

---

### 5️⃣ **Memoria Que Aprende de Sus Propios Errores**

**Antes**: Si Stratos cometía un error en una recomendación, lo volvía a hacer porque no "memorizaba" los errores. Era como un amnésico laboral.

**Ahora**: Hay un **loop de aprendizaje continuo**:

- Cuando un usuario rechaza una recomendación, Stratos pregunta por qué
- Si detecta un patrón ("Stratos siempre me sugiere ingenieros que no saben hablar español"), lo nota y lo corrige
- Re-indexa su memoria con ejemplos exitosos y evita los patrones de error
- En 2 semanas empiezas a notar que las recomendaciones mejoran

**Impacto Real:**

- ✅ Sistema que mejora mes a mes (no queda estancado)
- ✅ Personalización que evoluciona: "Stratos me conoce cada vez mejor"
- ✅ Menor overhead manual: menos "tuning" de la plataforma

---

### 6️⃣ **Visibilidad Completa en Decisiones de Talento**

**Antes**: Los sistemas de RRHH eran cajas negras. Sabías el output pero no por qué. ¿Por qué Stratos rechazó este candidato? Nadie sabe.

**Ahora**: Hay un **dashboard de inteligencia** que muestra:

- Cuántas recomendaciones hizo el sistema hoy (y cuán confiables fueron)
- Métricas de hallucination: ¿Qué % de recomendaciones pueden tener problemas?
- Tendencias de cultural fit por área
- Brechas de competencia más comunes (información para estrategia de L&D)
- Tiempo que tarda cada decisión (latencia)
- Equidad: ¿Se recomienda a hombres y mujeres equitativamente?

**Impacto Real:**

- ✅ Transparencia: puedes explicar decisiones a gerentes y a personas
- ✅ Governance: datos para auditoría, conformidad, reportes regulatorios
- ✅ Insights estratégicos: entiende dónde están los gaps de talento
- ✅ Confianza: "veo que el sistema está funcionando bien"

---

### 7️⃣ **Escalabilidad Sin Burnout del Equipo**

**Antes**: A medida que la empresa crecía, el equipo de RRHH crecía proporcionalmente. Más personas = más costo y aún no daban abasto en decisiones críticas.

**Ahora**: Stratos maneja la **escala cognitiva**:

- 100 personas = procesa datos de todos en segundos
- 10.000 personas = sigue siendo segundos
- Tu equipo se enfoca en estrategia y relaciones, no en buscar en hojas de cálculo

**Impacto Real:**

- ✅ Costo de RRHH por empleado baja
- ✅ Equipo menos estresado = decisiones mejor pensadas
- ✅ Capacidad de manejar crisisde talento sin pánico

---

### 8️⃣ **Predicción de Rendimiento Futuro (El Factor Diferenciador)**

**Antes**: Contrataban a alguien porque parecía calificado en el CV o porque "cayó bien en la entrevista". 6 meses después descubrías que no rendía, o peor: se iba. Nunca sabías qué predictor real de éxito hubiera funcionado.

**Ahora**: Stratos analiza **patrones históricos de éxito** en tu organización:

- Estudia: qué personas han sido las más productivas, las que han crecido más rápido, las que se han quedado y han sido felices
- Extrae factores comunes: "en nuestro case, los ingenieros que triunfan en proyectos digitales tienen: mentalidad de aprendizaje, experience en metodologías ágiles, network de mentores, y necesidad de autonomía"
- Crea un "perfil predictivo de éxito" específico para cada tipo de rol en TU empresa (no genérico)
- Al recomendar un nuevo candidato, Stratos compara: "este candidato encaja el 92% con el perfil de quienes han triunfado aqui"
- Valida después: cuando la persona lleva un año, Stratos compara su performance real con la predicción. ¿Era correcta? Si no, actualiza el modelo

**Ejemplo Real:**

```
Rol: Líder de Innovación (Manufacturing)

Histórico de éxito en tu empresa:
✅ Juan (2020): Predic=89% → Performance real=92% → ✅ Acertó
✅ María (2021): Predic=85% → Performance real=88% → ✅ Acertó
❌ Carlos (2021): Predic=72% → Performance real=45% → Bajo (validación útil)

Nuevo Candidato (Ahmed):
Predicción: 91% de probabilidad de alto rendimiento
Explicación:
  • Como Juan → mentalidad de experimentación
  • Como María → comunicación clara (crítica en manufacturing)
  • Evita patrón Carlos → no tiene red interna (pero Stratos puede mitigarlo con mentoring)

Recomendación: "Contratar, pero asignar mentor para primeros 3 meses"
```

**¿Por qué es diferente?**

- Otros sistemas te dan un score (ej: "Candidate Score: 7.5/10")
- Stratos te da **un score basado en TUS datos**, **explicado**, **y mejora continuamente**
- No es "mejor en el papel" sino "mejor en tu contexto específico"

**Impacto Real:**

- ✅ Menos bad hires: Stratos filtra candidatos que parecen buenos pero no lo van a ser en tu contexto
- ✅ Retención mejorada: si Stratos predice bien, las personas contratadas tienden a quedarse más tiempo
- ✅ Time-to-productivity: personas que encajan bien producen valor en semanas, no meses
- ✅ ROI de contratación: menos inverso en training para gente que se va
- ✅ Data-driven decisioning: "Hire this person" no es intuición, es análisis de 5 años de tus propios datos

**El Ciclo Predictivo:**

1. **Análisis histórico**: ¿Qué hace que alguien triunfe aquí?
2. **Predicción**: Nuevo candidato = score de probabilidad de éxito
3. **Contratación**: Decides basado en datos + juicio
4. **Onboarding inteligente**: Stratos asigna mentores/recursos basado en dónde puede fallar
5. **Validación**: Después de 6-12 meses, Stratos verifica si la predicción fue correcta
6. **Mejora del modelo**: Si falló, ¿por qué? Stratos aprende y próxima vez será más preciso

---

### 9️⃣ **Monitoreo del Ambiente Laboral (Stratos Experience)**

**Antes**: El ejecutivo o gerente general se enteraba de problemas en el clima laboral cuando era demasiado tarde: renuncias en cascada, baja productividad, o conflictos públicos. "Nadie me contar qué estaba pasando" era la queja común.

**Ahora**: Stratos continuamente monitorea el "pulso cultural" y la salud del ambiente a través de múltiples señales:

**¿Qué monitorea Stratos?**

1. **Clima Laboral & Engagement**
    - Análisis de encuestas de pulso (satisfaction, belongingness, purpose)
    - Tendencias mes a mes: ¿mejoró o empeoró el clima?
    - Segmentación: ¿Qué áreas tienen buen clima? ¿Cuáles tienen problemas?
2. **Señales de Liderazgo**
    - Feedback directo: "¿Cómo es trabajar con tu gerente?" (encuestas de 360 grados)
    - Detecta patrones: gerentes con altos reportados que se van, que reportan ansiedad, falta de dirección
    - Identifica líderes excepcionales: los que retienen talento, generan innovación, mantienen equipos motivados
3. **Conflictos Tempranos**
    - Análisis de comunicación interna (emails, chats, tickets): ¿Hay tensión?
    - Detecta conflictos interpersonales antes de que exploten: "hay fricción entre Juan y el equipo de ventas"
    - Identifica tensiones estructurales: "el equipo X se siente dejado de lado por la dirección"
    - Señal de bullying/acoso: anomalías en comunicación, aislamiento de una persona
4. **Retención & Burnout**
    - Predicción de renuncias: Stratos nota patrones en gente que se va ("siempre se van después de 2 proyectos rechazados")
    - Detección de burnout: personas trabajando excesivas horas, disminución en interacción social, comentarios pesimistas
    - Riesgos de desenganche: alguien que antes era vocal y social se vuelve silencioso = potencial problema

5. **Productividad vs. Bienestar**
    - Correlación: ¿Qué equipos que tienen buen clima son productivos? ¿Cuáles están estresadas pero productivas (insostenible)?
    - Análisis: "El equipo X trabajo 60 horas/semana pero su satisfacción bajó 30%. Esto no es sostenible."
    - Identifica equipos en riesgo: alta productividad + bajo bienestar = burnout inminente

**El Ciclo de Mejora de Ambiente:**

```
1. DETECT (Continuo)
   ↓
   Stratos detecta: "Climate score bajó 15% en Q1"
   Causa detectada: "Cambio de gerente en Área X hace 3 meses"

2. DIAGNOSE (Profundo)
   ↓
   Stratos analiza:
   • Encuestas: ¿Qué específicamente cambió? (confianza, dirección, equidad, reconocimiento)
   • Comunicación: ¿Hay menos interacción o más conflictiva?
   • Rotación: ¿Se fueron personas clave? ¿Quién es el riesgo siguiente?

3. RECOMMEND (Actionable)
   ↓
   Stratos sugiere:
   • "Gerente necesita coaching en comunicación" (específico, no genérico)
   • "Reunión de alineamiento con el equipo: clarificar dirección"
   • "Reconocer públicamente 3 logros del equipo que se sienten invisibles"
   • "Ajustar car de compensación: perciben desigualdad vs. otras áreas"

4. IMPLEMENT (Tu equipo actúa)
   ↓
   HR/Leadership ejecuta recomendaciones

5. MONITOR (Seguimiento)
   ↓
   Stratos rastrea:
   • ¿Se implementó realmente?
   • ¿Mejoró el clima? (medición en 2, 4, 8 semanas)
   • ¿Bajó la rotación? ¿Mejoró el engagement?
   • ¿Qué funcionó? ¿Qué no?

6. LEARN (Evolución)
   ↓
   Stratos documenta: "En problemas de clima X, la solución que funciona es Y"
   Próxima vez, sabrá cómo actuar más rápido
```

**Ejemplo Real: Crisis de Liderazgo Evitada**

```
SEMANA 1: Detección
• Stratos nota: clima score en Área de Tecnología -22% vs. mes anterior
• Patrón: 3 senior engineers muy productivos están siendo silenciosos en meetings
• Red flags: 1 solicitó traslado interno, 1 está entrevistándose afuera (por chats)

SEMANA 2: Diagnóstico
• Encuesta rápida: equipo dice "No sabemos para dónde vamos", "No se escuchan nuestras ideas"
• Análisis de comunicación: Nuevo gerente centraliza decisiones, no consulta
• Comparación: Otros equipos bajo su gerencia tienen clima similar

RESULTADO SIN STRATOS:
• En 3 meses, 2 senior engineers se van (costo: 6-9 meses de búsqueda + training)
• Equipo pierde productividad: nuevos proyectos se retrasan
• Reputación: Tecnología se considera "lugar donde no es buen trabajar"

RESULTADO CON STRATOS:
SEMANA 2: Recomendaciones
• "Gerente necesita coaching en liderazgo participativo" (asignado)
• "Llevar a cabo retrospectiva con el equipo" (facilitado por HR)
• "Comunicar roadmap y cómo cada persona contribuye" (específico)

SEMANA 4: Validación
• Climate score: +12% (no completamente recuperado, pero en buen camino)
• Engineers hablando de nuevo en meetings
• Engineer que pedía traslado: "Siento que me escuchan ahora", cancela solicitud

SEMANA 12: Resultado Final
• Climate score vuelve a baseline histórico
• Equipo de Tecnología es ahora ejemplo de "liderazgo efectivo"
• Rotación: 0 (comparado vs. 2-3 esperados sin intervención)
• Costo ahorrado: ~$500k (2 senior engineers que se hubieran ido)
```

**Impacto Real:**

- ✅ Problemas de clima detectados **en semanas**, no en meses o después de que alguien se vaya
- ✅ Gerentes problemáticos identificados y desarrollados (o reubicados si no cambian)
- ✅ Líderes excepcionales reconocidos y promocionados
- ✅ Prevención de burnout: identificas gente en riesgo antes de que se queme
- ✅ Retención mejorada: trabajar en un ambiente sano es la razón #1 para quedarse
- ✅ Productividad sostenible: no trabajas 60 horas/semana insosteniblemente, trabajas 40-45 con mejor resultado
- ✅ Conflictos resueltos rápido: intervención temprana evita toxicidad
- ✅ Data para decisiones: "¿Por qué promover a X como líder?" porque sus equipos tienen los mejores climas y resultados

**El Diferenciador: No es solo "satisfacción"**

Otros sistemas preguntan: "¿Qué tal tu trabajo? 1-5 estrellas"  
Stratos integra: encuestas + comunicación + rotación + productividad + feedback de pares = **panorama completo**

---

### 📊 **En Resumen: La Transformación**

| Aspecto                         | Antes                        | Después                                     |
| ------------------------------- | ---------------------------- | ------------------------------------------- |
| **Velocidad de decisión**       | Días                         | Minutos                                     |
| **Precisión de emparejamiento** | 65% acierto                  | 90%+ acierto                                |
| **Capacidad predictiva**        | Ninguna (adivinar)           | Basada en datos históricos (>85% precisión) |
| **Monitoreo de clima**          | Anual (encuesta una vez/año) | Continuo (señales diarias)                  |
| **Detección de problemas**      | Cuando explotan              | Semanas antes (predictivo)                  |
| **Sesgo en decisiones**         | Alto                         | Bajo (auditado)                             |
| **Confianza en datos**          | Media (~70%)                 | Alta (~95%)                                 |
| **Capacidad de escala**         | Limitada                     | Ilimitada                                   |
| **Aprendizaje del sistema**     | Ninguno                      | Continuo (mejora mensual)                   |
| **Transparencia**               | Baja (caja negra)            | Alta (auditable)                            |
| **ROI educativo**               | Bajo (L&D genérica)          | Alto (L&D personalizada)                    |

---

### 🔟 **Cultura Declarada vs. Real: La Brecha Que Hace Fracasar Estrategias**

**Antes**: Tu empresa dice ser "innovadora, ágil, inclusiva, data-driven". Pero cuando cambias estrategia o introduces cambios, la gente se resiste. ¿Por qué? Porque la cultura real—lo que la gente **realmente** vive día a día—es diferente de la que proclamas. Esto se llamaba "brecha cultural" y los ejecutivos la descubrían **demasiado tarde**, cuando la iniciativa ya había fracasado.

**Ahora**: Stratos mide **ambas culturas** continuamente:

**1. Cultura Declarada** (Lo que la empresa dice ser):

- Misión, valores, visión publicados en la web y en documentos internos
- Comportamientos esperados según el código de conducta
- Promesas de marca: "Somos un equipo inclusivo" o "Premios a innovación"

**2. Cultura Real** (Lo que la gente realmente vive):

- Cómo se toman decisiones (¿Consulta real o es solo teatro?)
- Quién tiene poder de verdad (¿Es meritocrático o político?)
- Cómo se tratan los errores (¿Aprendizaje o castigo?)
- Inclusión real: ¿Se escuchan todas las voces o solo las "cómodas"?
- Velocidad: ¿Se mueven rápido o hay burocracia oculta?
- Si eres diferente (edad, género, origen, neurodiverso), ¿Eres aceptado o marginalizado?

**El Análisis de Fricción (Único de Stratos):**

```
EJEMPLO: Empresa Tech que quiere transformarse en "Data-Driven"

Cultura Declarada:
"Somos data-driven, ágiles, experimentos rápidos"

Cultura Real (detectada por Stratos):
• 60% de las personas dicen "las decisiones se toman por opinión del executive"
• Cuando presentas data que contradice al jefe, hay silencio incómodo
• Los errores experimentales resultan en castigos (menos oportunidades después)
• "Data-driven" significa "data que confirme lo que ya decidimos"

FRICCIÓN DETECTADA: 85% de employees perciben contradicción

Cuando anuntias la estrategia "Data-Driven 2026":
SIN STRATOS:
• Equipo aparentemente acepta ("ok, vamos a usar más data")
• En realidad confúnden "data-driven" con "decisiones del jefe confirmadas con datos"
• Resistencia silenciosa: toman más tiempo, no aceptan recomendaciones
• Initiative fracasa: después de 6 meses, vuelven a "hablemos con el jefe"
• Ejecutivo frustrado: "¿Por qué no adoptan esta cultura?"

CON STRATOS:
• Anticipa el problema: "Hay una enorme fricción entre la cultura que quieres y la que existe"
• Recomienda: Antes de cambiar estrategia, alinea la cultura
  - "Necesitas mostrar que los experimentos fallidos NO son castigos"
  - "Necesitas tomar 3 decisiones importantes basadas únicamente en data, públicamente"
  - "Necesitas que X ejecutivo (quien tiene poder real) lo lidere"
• Ejecuta: Cambios de estructura + decisiones visible + comunicación clara
• Valida: 2 meses después, fricción baja a 20%
• Then: Cuando lanzas "Data-Driven 2026", adoptación real es 90% (vs. 30% sin preparación)
```

**¿Por Qué Esto Importa para Cambios Estratégicos?**

Toda estrategia requiere que la gente:

- Cambie comportamientos
- Adopte nuevas mentalidades
- Tome decisiones diferente

Si la cultura real no alinea con la nueva estrategia, dos cosas pasan:

1. **La gente se adapta superficialmente** (dice que lo hace, pero no lo hace)
2. **El cambio fracasa** (costo: millones en iniciativas que caen)

Stratos detecta la fricción **antes** de que gastes dinero e identifica qué exactamente necesita cambiar en la cultura para que la estrategia funcione.

**Ejemplo Real de Uso:**

**Situación**: CEO quiere "transformación digital" pero la cultura es "confiamos en procesos manuales de siempre"

**Stratos analiza**:

- Fricción: 78% (muy alta)
- Causa: Gente tiene miedo a lo digital (falta de capacitación), desconfía de "habilidades computacionales"
- Patrón: Personas mayores = más resistencia (correlación clara)

**Recomendación**: No es tecnología, es cultura + capacitación + inclusión

**Plan Stratos**:

1. L&D dirigida: Capacitación digital para 40+ años (sin juzgar)
2. Históricos de éxito: Muestra personas grandes que adoptaron exitosamente
3. Change management: Líderes moderan "¿Cómo imaginamos transformación en 3 años?"
4. Monitoreo: Fricción debería bajar mes a mes

**Resultado**: Después de 8 semanas, fricción baja a 35% (manejable), y cuando lanzas iniciativa digital, adopción es real.

**Impacto Real:**

- ✅ Cambios estratégicos que funcionan de verdad (no solo en papel)
- ✅ Evitas millones desperdiciados en iniciativas que fracasan
- ✅ Gente entiende "por qué" el cambio (alineación claridad)
- ✅ Menos resistencia = menos malestar, menos rotación
- ✅ Velocidad: cambio capturado en semanas, no en años de fricción

---

### 1️⃣1️⃣ **Psicometría & Evaluaciones de Potencial: De Predictores Estáticos a Dinámicos**

**Antes**: Las evaluaciones psicométricas eran snap-shots: hiciste test en la entrevista, te clasificaron (ej: "Eres de tipo MBTI INTJ, liderazgo 7/10, potencial medio") y eso era tu "predictor" para siempre. Pero las personas no son estáticas; crecen, cambian contexto, desarrollan nuevas habilidades.

**Ahora**: Stratos integra psicometría con **datos de comportamiento real** para crear evaluaciones dinámicas:

**1. Psicometría Mejorada (Baseline + Context)**

Tradicional:

```
Test psicométrico → "Extraversión: 6/10" → Fin
```

Con Stratos:

```
Test psicométrico → "Extraversión: 6/10"
       ↓
Observación en contexto histórico:
• En proyectos creativos: participación = 8/10 (muy extrovertida)
• En reuniones 1-a-1: comunicación = 5/10 (introvertida)
• Cuando hay conflicto: se retrae (4/10)
       ↓
Análisis: "No es introvertida. Es selectiva con sus espacios.
En contexto adecuado, es altamente comunicativa.
Necesita: ambientes de confianza + tiempo de procesamiento"
       ↓
Recomendación de rol: Investigación + facilitar small groups, no grandes presentaciones (a menos que el grupo sea de confianza)
```

**2. Evaluaciones de Potencial que Evolucionan**

Tradicional:

```
Performance actual 7/10 + Test potencial → "Potencial: Alto/Bajo"
(Y eso es lo que asumen para siempre)
```

Con Stratos:

```
Performance actual 7/10 + Test potencial + Histórico de crecimiento
       ↓
Stratos analiza:
• Velocidad de aprendizaje (¿Cómo rápido adoptó nuevas skills?)
• Receptividad a feedback (¿Escucha? ¿Ajusta?)
• Resiliencia ante fallos (¿Se reinventa o se desmorona?)
• Impacto en otros (¿Levanta a su equipo o los agota?)
       ↓
Resultado: No es un número estático. Es una evaluación dinámica:
"Potencial ALTO para roles técnicos en 2-3 años. Potencial MEDIO para liderazgo
de equipos grandes hoy, pero se proyecta ALTO si trabaja en 360-feedback y EI.
Necesita: mentorship, proyectos de estiramiento, coaching en comunicación"
       ↓
Seguimiento: Cada 6 meses, Stratos valida:
¿Se está moviendo hacia el potencial? ¿Qué falta?
```

**3. Diferencias de Personalidad en Contexto (El Cambio Transformacional)**

Stratos entiende:

- Una persona "perfeccionista" es FORTALEZA en QA/auditoría, PROBLEMA en startups (demasiado lenta)
- Una persona "impulsiva" es DEBILIDAD en planning, FORTALEZA en crisis (actúa rápido)
- Una persona "conflictiva" es PROBLEMA en equipos tímidos, VALOR en equipos disfuncionales (dice verdades incomodas)

**Antes**: La evaluaba como "problemas de personalidad: baja tolerancia, impulsiva"

**Ahora**:

```
"Tiene tolerancia baja para ineficiencia → Fortaleza en QA /
Debilidad en ambientes políticos (donde la inefficiency es política).
Recomendación: Rol donde pueda impactar efficiency sin frustración política"
```

**4. Potencial de Liderazgo: Multidimensional (No Solo "Es Líder o No")**

Tradicional:

```
Evaluación liderazgo → Score → "Apto / No apto para liderazgo"
(Ignora: ¿Cuántas personas? ¿Qué contexto? ¿Qué tipo?)
```

Con Stratos:

```
Multidimensional Assessment:
• Liderazgo técnico: 9/10 (personas confían en su expertise, la siguen)
• Liderazgo emocional: 5/10 (no procesa bien emociones de otros)
• Liderazgo político: 3/10 (terrible navegando dinámicas de poder)
• Liderazgo de visión: 8/10 (inspira con dirección clara)

Recomendación:
"CTO o Tech Lead IDEAL. Director general RIESGOSO (necesitaría Co-líder o coaching).
Plan: Coaching de inteligencia emocional. En 18 meses podría estar listo para Director"

En lugar de: "Potencial de liderazgo: SÍ / NO"
```

**5. Detalles Finos Que Predicen Éxito**

Stratos identifica:

- Gente que trabaja bien bajo presión vs. gente que se quiebra
- Quien es creativa en ambientes estructurados (oxymoron para muchos: son POCAS)
- Quien es buena en equipos remotos vs. híbridos vs. presenciales
- Quien se motiva con dinero, con reconocimiento, con propósito, con autonomía
- Quien aprende haciendo, leyendo, conversando, enseñando a otro

**El Beneficio Psicométrico:**

De:

```
Evaluación psicométrica estática →
"Este candidato es INTJ, liderazgo 6, potencial bajo"
(Y eso te sigue toda la vida en la empresa)
```

A:

```
Evaluación psicométrica + comportamiento continuo →
"Este candidato tiene X fortalezas en Y contextos. En Z entorno podría brillar.
Necesita este desarrollo. En 18 meses podría estar listo para X rol.
Recomendación: Asignar mentor Z, proyecto de estiramiento en Q2, coaching de EI"
(Dinámico, accionable, esperanzador)
```

**Impacto Real:**

- ✅ Evaluaciones justas: consideras contexto, no solo personality type
- ✅ Talento no se pierde: "Introvertida" no significaba "mala para liderazgo", solo "necesita contexto diferente"
- ✅ Planes de desarrollo específicos: basados en fortalezas + debilidades reales, no etiquetas
- ✅ Predicción mejorada de éxito: combinas psicometría + datos reales
- ✅ Potencial que crece: personas ven que "Potencial MEDIO hoy, ALTO en 2 años" (esperanza = motivación)
- ✅ Toma de decisiones: "¿Promover a X?" No es intuición + CV. Es datos psicométricos + performance + evaluación potencial dinámica
- ✅ Menos burnout: asignas roles donde alguien puede cómodamente ser eficiente (no poniéndola en contexto donde sufre)

---

### 1️⃣2️⃣ **Evaluaciones 360° Sin Sesgo: La Triangulación de la Verdad**

**Antes**: Las evaluaciones 360° tradicionales eran un desastre de subjetividad. Tu jefe te daba buena calificación porque "te cae bien" (o mala porque tuvo un mal día). Tus compañeros opinaban influidos por "quién es simpático en los almuerzos" en lugar de quién realmente entrega. El resultado: promociones políticas, gente talentosa ignorada, decisiones injustas.

**Ahora**: Stratos ha revolucionado las evaluaciones 360° mediante **triangulación de cuatro fuentes independientes**:

**1. Evidencia Verificable (Datos Duros de Terceros)**

Stratos conecta con sistemas de terceros (ERP, sistemas de facturación, repos de código, etc.) para extraer datos duros que no pueden ser opiniones:

- **Entregas cumplidas**: ¿Cuántos proyectos terminaron a tiempo? ¿Dentro de presupuesto?
- **Calidad**: Defectos encontrados (en código, en procesos). Tasa de error. Re-trabajo.
- **Productividad**: Líneas de código, documentos generados, transacciones procesadas
- **Cumplimiento financiero**: Presupuestos respetados, gastos no autorizados, austeridad
- **Conformidad**: Auditorías aprobadas, compliance issues, violaciones de políticas

**Ejemplo:**

```
Persona A:
- Mi jefe dice: "Excelente desempeño" (opinión)
- Datos ERP muestran: 2 proyectos retrasados, 1 sobre presupuesto (evidencia)

Persona B:
- Mi jefe dice: "Desempeño medio" (opinión)
- Datos ERP muestran: 5 proyectos on-time, presupuesto 3% bajo (evidencia)

Conclusión: La opinión del jefe es sesgada. Los datos hablan claro.
```

**2. KPIs Cuantitativos (Métricas Objetivas)**

Stratos calcula KPIs específicos del rol (no genéricos) que miden:

- **Eficiencia**: Ej: tokens entregados por hora, clientes servidos por día, tickets resueltos por semana
- **Calidad**: % de trabajo que pasó QA a la primera, satisfacción de cliente, NPS
- **Fiabilidad**: % de attendance, cumplimiento de deadlines, on-time delivery
- **Impacto**: Dinero ahorrado, ingresos generados, riesgos mitigados
- **Innovación**: Ideas implementadas, mejoras de proceso, patentes

**Lo clave**: Estos KPIs se comparan **contra el estándar histórico de excelencia de tu empresa**, no contra el promedio.

```
Vendedor A: "Vendió $500k"
vs.
Vendedor B: "Vendió $400k"

Contexto Stratos:
- Vendedor A: territorio es premium ($1M potencial), capturó 50%
- Vendedor B: territorio es difícil ($300k potencial), capturó 133%

KPI ajustado por contexto:
- Vendedor A: 50% eficiencia
- Vendedor B: 133% eficiencia (MEJOR)

Conclusión: Sin contexto, A parecía mejor. Con datos, B es superior.
```

**3. Consenso Social (Mapa Cerebro)**

Stratos recolecta feedback de pares usando el "Mapa Cerebro" — una red social interna donde:

- Compañeros responden: "¿Esta persona colabora?" "¿Levanta el nivel?" "¿Es confiable?"
- Las respuestas se procesan con inteligencia: identifica **quién opina** (¿Tiene credibilidad? ¿Trabalaja cerca?) y **qué opinan**
- Filtra ruido: opiniones de personas que no trabajan juntas, opiniones extremas, tendencias políticas

**Lo diferente de Stratos**:

```
Tradicional:
"¿Qué tal tu compañero? 1-5 estrellas"
→ Resultado: Persona popular = calificación alta (sesgo de simpatía)

Stratos:
"¿Qué tal tu compañero en ESTOS contextos específicos?
  - Cuando la gente necesita ayuda, ¿responde?
  - Cuando hay conflicto, ¿ayuda a resolver o lo agrava?
  - ¿Admite cuando se equivoca?
  - ¿Si tuvieras que confiarle un proyecto crítico, lo harías?"
→ Resultado: Datos sobre comportamiento real, no simpatía
```

**4. Motor Vanguard (Análisis Contextual & Correlación)**

El Motor Vanguard de Stratos es donde ocurre toda la magia: toma las 3 fuentes anteriores y:

- **Cruza correlaciones**: Si la evidencia y KPIs dicen "excelente" pero el Mapa Cerebro dice "poco colaborador", Stratos lo detecta y pregunta "¿por qué?"
- **Ajusta por contexto**: "Trabajo bajo presión" vs. "Ambiente relajado". "Equipo experado" vs. "Equipo junior". El mismo comportamiento se evalúa diferente según contexto
- **Identifica patrones ocultos**: "Esta persona tiene buen KPI pero el Mapa Cerebro la describe como 'difícil'. Probablemente genera resultado a costa de relaciones" (insostenible a largo plazo)
- **Genera recomendaciones**: "Persona A: 360 Score Alto Y Confiable. Recomendado para promover. Persona B: 360 Score Alto PERO potencialmente insostenible. Recomendado coaching en colaboración antes de promover."

**El Triángulo de la Verdad:**

```
         EVIDENCIA VERIFICABLE
                ▲
               / \
              /   \
             /     \
            /       \
           /         \
          /           \
         /             \
        /               \
       /                 \
      /                   \
     /                     \
    /                       \
   /                         \
  /                           \
 /                             \
/                               \
──────────────────────────────────
          KPIs           CONSENSO
                         SOCIAL

Si los tres puntos se alinean →  EVALUACIÓN CONFIABLE
Si hay triángulos contradictorios → ANOMALÍA A INVESTIGAR
```

**Ejemplos de Triangulaciones:**

```
CASO 1: Persona asciende justificadamente
Evidence: ✅ Proyectos on-time, presupuesto controlado
KPIs: ✅ Eficiencia 95%, calidad 99%
Mapa: ✅ Compañeros dicen "excelente, confiable, levanta nivel"
→ RESULTADO: Promoción segura. Será exitosa.

CASO 2: Persona que parecía buena pero necesita coaching
Evidence: ✅ Entregas completas
KPIs: ✅ Alta productividad (145% vs. promedio)
Mapa: ⚠️ Compañeros dicen "difícil", "poco receptivo a feedback"
→ RESULTADO: "Persona talentosa pero con debilidades de liderazgo.
Requiere coaching en EI y colaboración. En 12 meses, potencial de promover."

CASO 3: Persona con baja evaluación previa que merece segunda chance
Evidence: ⚠️ 1 proyecto retrasado hace 2 años (bajo antiguo jefe)
KPIs: ✅ Últimos 18 meses: on-time, calidad alta
Mapa: ✅ Nuevo equipo dice "confiable, mejora constante"
→ RESULTADO: "Persona fue mal clasificada años atrás por jefe tóxico.
Datos actuales muestran mejora sostenible. Potencial subestimado."

CASO 4: Alerta de riesgo - Personas sobre-evaluadas
Evidence: ✅ Entregas (pero fecha límites siempre justas, margen 0)
KPIs: ✅ Productividad (pero solo en actividades que elige)
Mapa: ❌ Compañeros dicen "no colabora", "invisible cuando hay crisis", "difícil de contactar"
→ RESULTADO: "RIESGO: Persona con resultados individuales altos pero débil
en crisis y colaboración. NO recomendado para liderazgo. Riesgo de rotación."
```

**¿Por Qué Esto Cambia Todo?**

**Antes (Sin Triangulación):**

- Jefe subjetivo: "Te doy 4/5 porque me cae bien"
- Resultado: Promoción injusta, conflicto con compañeros, person se quiebra en nuevo rol

**Ahora (Con Triangulación):**

- Datos objetivos + contexto + relaciones = visión 360° real
- Resultado: Promoción justificada, persona preparada, baja rotación, confianza

**Impacto Real:**

- ✅ Fin del amiguismo: promociones basadas en evidencia, no en política
- ✅ Talento invisible descubierto: gente talentosa que era mal evaluada por jefes sesgados
- ✅ Evaluaciones justas: cualquier persona entiende "por qué" se evaluó así
- ✅ Aprendizaje sostenible: si hay brecha ("bueno en proyecto pero débil en team"), recomendaciones claras para mejorar
- ✅ Confianza en resultados: puedes usar estas evaluaciones para promociones, bonos, asignaciones sin temor a demandas legales
- ✅ Reducción de rotación: gente se queda cuando se siente evaluada justamente
- ✅ Cultura de transparencia: todos ven que el sistema es justo, no misterioso

**Resultado Final: Evaluaciones que Cumplen su Rol**

Las evaluaciones 360 dejan de ser "opiniones compiladas" para convertirse en:

- **Herramienta de aprendizaje**: "Aquí es donde creces"
- **Herramienta de justicia**: "Aquí es por qué la decisión fue X"
- **Herramienta de mejora sostenible**: "Aquí es cómo llegamos a tu siguiente nivel"

---

### 🎬 **¿Cómo Esto Afecta A Mi Día A Día?**

**Si eres un gerente:**

- Menos tiempo en decisiones de talento, más en liderazgo estratégico
- Recomendaciones que puedes confiar = decisiones más veloces
- Visibilidad clara: quién necesita qué para crecer
- Entiende diferencias: "Esta introvertida no es mala para liderazgo, solo necesita equipo de confianza"
- Evaluaciones que puedes defender: "No es mi opinión, es triangulación de datos + feedback"

**Si eres una persona con talento:**

- Tu oportunidad de crecimiento es identificada **antes** de que la puestos
- Tu plan de desarrollo es hecho **solo para ti**, no genérico
- Tu progreso es monitoreado y Stratos ajusta el plan si no funciona
- Eres evaluado sin sesgos
- Tu personalidad no te etiqueta para siempre: "Introvertida hoy, pero con mentor X puedes crecer a liderazgo"
- Confías en tu evaluación: "No es opinión del jefe, es datos + consenso"

**Si eres un ejecutivo:**

- Menos riesgos legales (auditoría completa de decisiones)
- Estrategia de talento basada en datos, no en gut-feel
- ROI educativo medible: puedes demostrar que el aprendizaje funciona
- Escalabilidad: crece sin que los costos de RRHH explotan
- Cambios estratégicos que realmente prendén (porque alineas la cultura primero)
- Ves evolución clara: promociones basadas en potencial demostrado + plan de acción realista
- Confianza en evaluaciones: sistema auditable, transparente, inmune a sesgo de jefe individual

---

## � TABLA RESUMEN: 12 BENEFICIOS DE STRATOS INTELIGENCIA COGNITIVA

| # | Beneficio | Cómo Stratos lo Realiza |
|---|-----------|------------------------|
| 1️⃣ | **Decisiones de Talento Inteligentes (Menos Sesgadas)** | Analiza miles de datapoints (competencias, historia, cultural fit, potencial). Detecta sesgos inconscientes. Proporciona explicaciones de recomendaciones basadas en datos. |
| 2️⃣ | **Búsqueda & Emparejamiento Ultrarrápido** | Motor RAG busca globalmente en base de talento + externamente en LLMs públicos. Genera match score en <5 segundos. Incluye análisis de cultural fit, brechas, plan de integración. |
| 3️⃣ | **Aprendizaje Verdaderamente Personalizado** | Analiza competencias actuales vs. requeridas para próximo rol. Genera blueprint único de crecimiento (cursos + mentoría + proyectos). Monitorea progreso y ajusta si no funciona. |
| 4️⃣ | **Validación Automática de Calidad (The Critic)** | VerifierAgent valida outputs contra políticas, datos, lógica business. Detecta alucinaciones, contradicciones. Mantiene auditoría completa de validaciones. Score 0-1 confiabilidad. |
| 5️⃣ | **Memoria Que Aprende** | Feedback loop: cuando usuario rechaza recomendación, Stratos pregunta por qué. Identifica patrones de error. Re-indexa memoria con ejemplos exitosos. Evita errores similares. |
| 6️⃣ | **Visibilidad Completa en Decisiones** | Dashboard de inteligencia muestra: métricas hallucination, cultural fit trends, brechas competencia, tiempo decisión, equidad de recomendaciones. KPIs auditables. |
| 7️⃣ | **Escalabilidad Sin Burnout** | Procesa 100-10,000 personas en segundos. Equipo RRHH se enfoca en estrategia/relaciones, no búsqueda manual. Costo/empleado baja. |
| 8️⃣ | **Predicción de Rendimiento Futuro** | Analiza histórico: qué personas triunfaron en tu empresa. Extrae perfil de éxito específico. Compara nuevo candidato contra perfil. Valida predicción después de 6-12 meses. Modelo mejora continuamente. |
| 9️⃣ | **Monitoreo del Ambiente Laboral (Experience)** | Monitorea continuamente: encuestas de pulso, comunicación interna, rotación, burnout, conflictos. Detecta problemas en SEMANAS. Genera recomendaciones específicas para mejorar clima. Rastrea si mejoran. |
| 🔟 | **Cultura Declarada vs. Real (Fricción Estratégica)** | Mide brecha entre misión/valores proclamados vs. cómo vive la gente. Identifica fricción % con cambios estratégicos. Recomienda alineación previa antes de gastar en iniciativas que fracasarán. |
| 1️⃣1️⃣ | **Psicometría Dinámica & Potencial Evolutivo** | Integra test psicométrico + comportamiento real histórico. Genera evaluación contextualizada, no etiqueta estática. Potencial se actualiza cada 6 meses basado en progreso real. Planes de desarrollo accionables. |
| 1️⃣2️⃣ | **Evaluaciones 360° Sin Sesgo (Triangulación)** | TRIANGULA 4 fuentes: Evidencia verificable (ERP, repos) + KPIs cuantitativos ajustados + Consenso social (Mapa Cerebro) + Motor Vanguard (correlación). Fin del amiguismo. Justicia auditable. |

---

## �📝 Documentos de Referencia

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
