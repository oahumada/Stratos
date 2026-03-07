# 🔍 Opción C: Evaluación de Fidelidad de IA (RAGAS) - Agnóstica de Proveedor

**Fecha:** 7 de Marzo de 2026  
**Estado:** Planificado  
**Ámbito:** Agnóstico (DeepSeek, ABACUS, OpenAI, Mock)

---

## 📋 Resumen Ejecutivo

La Opción C implementará **RAGAS (Retrieval-Augmented Generation Assessment)** para validar la **fidelidad** y **relevancia** de respuestas generadas por CUALQUIER proveedor LLM integrado en Stratos.

### Objetivo Clave

✅ Validar que el LLM genera contenido **preciso, coherente y libre de alucinaciones**  
✅ Funcionar independientemente del proveedor (DeepSeek vs ABACUS vs OpenAI vs Mock)  
✅ Detectar sesgos en recomendaciones de talento

---

## 🏗️ Arquitectura Agnóstica

```
┌─────────────────────────────────────────────────┐
│        LLMClient (Router Agnóstico)             │
│  ┌─────────────┬─────────────┬──────────────┐   │
│  │  DeepSeek   │   ABACUS    │   OpenAI     │   │
│  └─────────────┴─────────────┴──────────────┘   │
└─────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────┐
│   RAGASEvaluator (Provider-Independent)         │
│  ├── Faithfulness Check                         │
│  ├── Relevance Score                            │
│  ├── Context Alignment                          │
│  └── Hallucination Detection                    │
└─────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────┐
│   QA Dashboard & Metrics                        │
│  ├── Provider Performance Comparison            │
│  ├── Trend Analysis                             │
│  └── Alert Thresholds                           │
└─────────────────────────────────────────────────┘
```

---

## 🎯 Componentes de Implementación

### 1. **RAGASEvaluator Service** (agnóstico)

Ubicación: `app/Services/RAGASEvaluator.php`

```php
class RAGASEvaluator
{
    // Evalúa: respuesta + contexto original
    public function evaluate(
        string $generatedContent,
        string $sourceContext,
        array $expectedEntities = []
    ): RAGASScore {
        return new RAGASScore(
            faithfulness: $this->checkFaithfulness(...),
            relevance: $this->checkRelevance(...),
            contextAlignment: $this->checkContextAlignment(...),
            hallucinations: $this->detectHallucinations(...)
        );
    }
}
```

**Métricas:**

- **Faithfulness (0-1):** ¿La respuesta es fiel al contexto?
- **Relevance (0-1):** ¿La respuesta responde la pregunta?
- **Context Alignment (0-1):** ¿Se alinea con datos Stratos?
- **Hallucination Risk (0-1):** Probabilidad de alucinación

---

### 2. **LLMEvaluationJob** (agnóstico)

Ubicación: `app/Jobs/EvaluateLLMGeneration.php`

```php
class EvaluateLLMGeneration implements ShouldQueue
{
    public function handle(
        ScenarioGeneration $generation,
        RAGASEvaluator $evaluator
    ) {
        // 1. Obtener generación (agnóstico del proveedor)
        $content = $generation->llm_response;

        // 2. Evaluar con RAGAS
        $score = $evaluator->evaluate(
            $content,
            $generation->source_prompt,
            $generation->expected_entities
        );

        // 3. Persistir scores
        $generation->update([
            'ragas_faithfulness' => $score->faithfulness,
            'ragas_relevance' => $score->relevance,
            'quality_status' => $score->overall >= 0.85 ? 'approved' : 'review'
        ]);
    }
}
```

---

### 3. **Configuración agnóstica** (`config/ragas.php`)

```php
return [
    'enabled' => env('FEATURE_RAGAS_EVALUATION', false),

    'thresholds' => [
        'faithfulness_min' => 0.85,
        'relevance_min' => 0.80,
        'context_alignment_min' => 0.75,
        'hallucination_max' => 0.15, // 15% riesgo máximo
    ],

    'providers' => [
        'deepseek' => ['weight' => 1.0, 'baseline' => 0.82],
        'abacus' => ['weight' => 1.0, 'baseline' => 0.88],
        'openai' => ['weight' => 1.0, 'baseline' => 0.90],
        'intel' => ['weight' => 1.0, 'baseline' => 0.75],
        'mock' => ['weight' => 0.0, 'baseline' => 0.95], // No evaluar mocks
    ],

    'context_sources' => [
        'scenario_context',
        'organization_data',
        'historical_patterns',
    ],
];
```

**Nota:** Cada proveedor puede tener baselines diferentes, pero la **lógica de evaluación es idéntica**.

---

### 4. **Métricas & Dashboard**

Endpoint: `GET /api/qa/llm-evaluation/report`

```json
{
    "period": "2026-03-01 to 2026-03-07",
    "provider_comparison": {
        "deepseek": {
            "avg_faithfulness": 0.84,
            "avg_relevance": 0.81,
            "evaluations": 127,
            "hallucination_rate": 0.12,
            "status": "within_threshold"
        },
        "abacus": {
            "avg_faithfulness": 0.88,
            "avg_relevance": 0.86,
            "evaluations": 203,
            "hallucination_rate": 0.08,
            "status": "optimal"
        }
    },
    "trend": "↑ Mejora del 3% en fidelidad semana a semana"
}
```

---

## 🔄 Integración en Workflows

### Paso 1: Generación LLM ✅

```
ScenarioGenerationController::generate()
  → LLMClient (cualquier proveedor)
  → ScenarioGeneration creado
```

### Paso 2: Evaluación RAGAS (NEW)

```
GenerateScenarioFromLLMJob::handle()
  → dispatch(EvaluateLLMGeneration)
  → RAGASEvaluator::evaluate() [AGNÓSTICO]
  → Persistir scores de fidelidad
```

### Paso 3: QA Gate

```
ChangeSetController::approve()
  → if (ragas_score < threshold) → Review required
  → else → Auto-approve OK
```

---

## 🧪 Casos de Prueba (Provider-Independent)

```gherkin
# Feature: RAGAS Evaluation (Multi-Provider)

Scenario: DeepSeek genera contenido con hallucinations
  Given: DeepSeek como LLM activo
  When: Se genera escenario con contexto limitado
  Then: RAGAS detecta hallucination_risk > 0.15
  And: Status = "review_required"

Scenario: ABACUS mantiene calidad de fidelidad
  Given: ABACUS como LLM activo
  When: Se generan roles con competencias
  Then: RAGAS faithfulness >= 0.85
  And: Context alignment >= 0.75

Scenario: Comparativa Multi-Provider
  Given: Mismo prompt ejecutado en DeepSeek, ABACUS, OpenAI
  When: Se evalúan todos con RAGAS
  Then: Scores son comparables (después de normalización)
```

---

## ⚙️ Instalación & Configuración

### 1. Instalar RAGAS Python

```bash
pip install ragas langchain-core langchain-community
```

### 2. Integrar en LLMClient

```php
// En LLMClient::__construct()
$this->evaluator = app(RAGASEvaluator::class);
```

### 3. Habilitar en .env

```env
FEATURE_RAGAS_EVALUATION=true
RAGAS_ENDPOINT=http://localhost:8001  # Python service
RAGAS_EVALUATION_MODE=async            # async|sync
```

### 4. Crear Job Schedule

```php
// En bootstrap/app.php
$schedule->job(new EvaluateLLMGeneration::class)
    ->everyFifteenMinutes()
    ->withoutOverlapping();
```

---

## 📊 Métricas de Éxito

| Métrica                  | Target         | Tool                 |
| ------------------------ | -------------- | -------------------- |
| **IA Faithfulness**      | >= 0.85        | RAGAS                |
| **Hallucination Rate**   | < 15%          | RAGAS                |
| **Provider Consistency** | Variance < 10% | Custom Analyzer      |
| **Bias Detection**       | 0 High Risk    | RAGAS + Custom Rules |

---

## 🚀 Roadmap de Implementación

### Fase 1 (1 semana): Baseline

- [ ] Desarrollar RAGASEvaluator core
- [ ] Integrar RAGAS Python CLI
- [ ] Test con Mock data

### Fase 2 (2 semanas): Multi-Provider

- [ ] Validar con DeepSeek
- [ ] Validar con ABACUS
- [ ] Validar con OpenAI (referencia)

### Fase 3 (1 semana): Dashboard & Alerts

- [ ] Crear endpoint de reportes
- [ ] Alerts para degradación
- [ ] Comparativa provider en UI

---

## 💡 Decisiones de Diseño

### Why RAGAS?

- ✅ Open source (no vendor lock-in)
- ✅ Agnóstico de modelo/proveedor
- ✅ Métricas estándar de industry
- ✅ Fácil integración con LLMs

### Why Agnóstico?

- ✅ Stratos soporta múltiples proveedores (DeepSeek, ABACUS, OpenAI)
- ✅ Evaluación debe ser consistente entre todos
- ✅ Permite comparativas justas
- ✅ Facilita migration entre proveedores sin rehacer lógica

### Why Post-Generation (no Real-Time)?

- ✅ RAGAS eval es CPU-intensive
- ✅ Mejor experiencia: dejar que usuario vea resultado rápido, evaluar en background
- ✅ Permite batch evaluation en off-peak hours

---

## ⚠️ Limitaciones Conocidas

1. **RAGAS requiere contexto claro:** Si el prompt es ambiguo, la evaluación lo reflejará
2. **Hallucination detection no es 100%:** Posibles falsos neg/positivos
3. **Training bias:** RAGAS se entrena con ciertos tipos de LLMs (necesita validation en DeepSeek)

---

## 🔗 Referencias

- [RAGAS Documentation](https://docs.ragas.io)
- [Stratos LLMClient Architecture](./app/Services/LLMClient.php)
- [RAGAS Metrics Explained](https://github.com/explodinggradients/ragas)
