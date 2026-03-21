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

| Servicio | Propósito | Estado | Ubicación |
|----------|-----------|--------|-----------|
| **AiOrchestratorService** | Orquesta agentes; llama `agentThink()` | ✅ Completo | `app/Services/` |
| **RAGASEvaluator** | Evalúa outputs LLM (contexto, faithfulness, relevancia) | ✅ Completo (integra Python RAGAS) | `app/Services/` |
| **EmbeddingService** | Genera embeddings (OpenAI, ABACUS, mock) | ✅ Completo | `app/Services/` |
| **LLMClient** | Cliente HTTP agnóstico para múltiples LLM | ✅ Completo | `app/Services/` |
| **StratosGuideService** | Agente de mesa de ayuda (RAG-based) | ✅ Parcial (~50%) | `app/Services/` |
| **SentinelMonitorService** | Agente de monitoreo de calidad | ✅ Parcial (~40%) | `app/Services/` |
| **CultureSentinelService** | Análisis de cultura organizacional | ✅ Partial (~30%) | `app/Services/` |
| **TalentOrquestatorService** | Orquesta decisiones de talento híbrido | ✅ Parcial (~60%) | `app/Services/Talent/` |
| **TalentBlueprintService** | Genera blueprints de roles + skill gaps | ✅ Completo | `app/Services/Talent/` |
| **ScenarioGenerationService** | Genera escenarios automáticamente | ✅ Completo | `app/Services/Scenario/` |
| **GapAnalysisService** | Análisis de brechas de competencia | ✅ Completo | `app/Services/` |
| **MatchingService** | Matching de candidatos (human + synthetic) | ✅ Completo | `app/Services/` |

### 1.2 Infraestructura Soportada

| Componente | Implementado | Nota |
|-----------|--------------|------|
| **LLM Providers** | OpenAI, DeepSeek, ABACUS | Agnóstico; agregar modelos locales (Ollama) |
| **Embeddings** | OpenAI, ABACUS, mock | Necesita fallback más robusto |
| **Vector Storage** | ❌ No (esperado: pgvector) | **CRÍTICO para RAG** |
| **Knowledge Graph** | ❌ No | Arquitectura + schema definido, no implementado |
| **Memory (short-term)** | App context + session storage | Funcional pero básico |
| **Memory (episodic)** | LLMEvaluation records (logs) | Funcional; puede mejorar indexación |
| **Memory (semantic)** | ❌ No (será vector DB) | **CRÍTICO** |
| **Orchestrator Supervisor** | Básico (Agent model) | Necesita Planner + Verifier + Arbiter |
| **Audit & Logging** | ✅ Muy completo | AuditTrailService + ComplianceAudit |
| **Multi-tenant Scoping** | ✅ Nativo | organization_id en todo lugar |

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

---

## 4️⃣ QUICK WINS (Implementables en Días)

Antes de empezar Sprint 0, estos pueden dar valor inmediato:

### **QW-1: Mejorar Logging de Prompts (1-2 días)**
```php
// app/Traits/LogsPrompts.php
public static function logPrompt(string $prompt, string $output, ?array $metadata = null) {
    $hash = hash('sha256', $prompt); // PII-safe
    Log::channel('llm_prompts')->info('LLM Call', compact('hash', 'output', 'metadata'));
}
```
**Beneficio**: Trazabilidad sin riesgos de cumplimiento.

### **QW-2: Dashboard de Salud RAGASEvaluator (1-2 días)**
Crear Blade con gráficos de `llm_evaluations`:
- Faithfulness trend
- Hallucination detection rate
- Provider comparison
**Beneficio**: Visibilidad de calidad LLM en tiempo real.

### **QW-3: Endpoint Interno `/api/rag/ask` (2-3 días)**
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

### **QW-4: Redaction Service para PII (2-3 días)**
Polir `RedactionService` existente y usarlo en todos los logs.
```php
$redacted = RedactionService::redact($prompt, ['email', 'phone', 'ssn']);
```
**Beneficio**: GDPR-ready logging sin riesgos.

### **QW-5: Agent Interaction Metrics (1-2 días)**
Query `agent_interactions` + `llm_evaluations` para dashboard:
- Call count por agent
- Success rate (evaluación RAGAS)
- Avg latency
**Beneficio**: KPIs de agentes sin código nuevo.

**Total Quick Wins**: 7-14 días; valor inmediato + contexto para Sprint 0.

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

| KPI | Target | Timeline |
|-----|--------|----------|
| **Hallucination Rate** | < 3% | Semana 12 |
| **RAG Latency (P95)** | < 2s | Semana 4 |
| **Knowledge Index Size** | > 50K docs | Semana 2 |
| **User Acceptance Rate** | > 80% | Semana 8 |
| **Critic Accuracy** | > 90% | Semana 9 |
| **Feedback Loop Impact** | Halluc ↓ 50% | Semana 12 |
| **Zero Security Incidents** | 100% | Ongoing |
| **GDPR Compliance** | 100% | Semana 6 |

---

## 7️⃣ DEPENDENCIAS Y RIESGOS

### 7.1 Dependencias

| Item | Propietario | Est. |
|------|-------------|------|
| PostgreSQL 15+ con pgvector | DevOps | 1-2 días |
| Redis (para message bus) | DevOps | 1 día |
| Python RAGAS service (ya existe) | IA Team | ✅ Listo |
| LLM providers (OpenAI, DeepSeek, ABACUS) | DevOps + Product | ✅ Listo |
| Knowledge base docs (formato, ingesta) | Product + Content | 3-5 días |

### 7.2 Riesgos y Mitigaciones

| Riesgo | Probabilidad | Mitigación |
|--------|--------------|------------|
| Vector DB performance degrade con 100K docs | Media | Pre-tuning de índices; monitoring; sharding strategy |
| Hallucination rate no baja | Media | Robustecer Critic; feedback loop; fine-tuning |
| Latencia RAG > SLA durante peak | Media | Caching; query optimization; load testing |
| PII leak en logs | Baja | Redaction en pipeline + tests automáticos |
| Integración con agentes existentes compleja | Media | MVP con un agente; generalizar después |

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

## 🎯 RESUMEN: HOJA DE RUTA VISUAL

```
┌─────────────────────────────────────────────────────────────────────┐
│                       STRATOS INTELLIGENCE 2026                    │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  QW-1 QW-2  │  QW-3 QW-4  │  QW-5          │ 3-6 meses            │
│  Logging    │  Endpoint   │  Metrics       │ Advanced              │
│  ─────────  │  ────────   │  ───────       │ ────────              │
│  (2d)       │  (5d)       │  (3d)          │                       │
│             │             │                │                       │
│             ├─────────────┤  Sprint 0      │ Sprint 3: Critic+     │
│             │ Sprint 0    │  ─────────     │ Orchest. Supervisor   │
│             │ pgvector    │  (2w)          │ (3w) + Msg Bus        │
│             │ Indexing    │  • Indexación  │                       │
│             │ (2w)        │  • RE base     │ Sprint 4: Learning    │
│             │ • pgvector  │  • RAG svc     │ Loop (3w)             │
│             │ • HNSW idx  │  • Integrate   │ • Feedback mechanism  │
│             │ • delta idx │    GuideSvc    │ • Re-index job       │
│             │             │                │ • Versioning         │
│             │             ├────────────────┤                       │
│             │             │ Sprint 2       │                       │
│             │             │ Logging        │                       │
│             │             │ Metrics (2w)   │                       │
│             │             │ • Dashboard    │                       │
│             │             │ • KPIs          │                       │
│             │             │ • Integr RAGAS │                       │
│             ├─────────────┴────────────────┴───────────────────────┤
│             │              Sprint 1: RAG Core (2w)                 │
│             │              • RagService central                    │
│             │              • Integr StratosGuide                   │
│             │              • Endpoint /api/rag/ask                 │
│                                                                     │
│  Timeline: 7-14d (QW)  + 2w (S0) + 2w (S1) + 2w (S2) + 3w (S3)   │
│          + 3w (S4)   =  ~12-14 semanas (3 meses)                  │
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
