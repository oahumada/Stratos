# QW-3: `/api/rag/ask` Endpoint

## 📊 Descripción General

QW-3 implementa un endpoint REST básico para **Retrieval Augmented Generation (RAG)** que permite formular preguntas sobre la base de conocimiento de evaluaciones LLM (RAGAS).

**Duración Realizada**: 2.5 horas  
**Status**: ✅ Completado

---

## 🎯 Objetivos Logrados

✅ Crear Form Request `RagAskRequest` con validación  
✅ Implementar servicio `RagService` con retrieval + LLM generation  
✅ Crear controlador `RagController` para manejar requests  
✅ Integrar con `EmbeddingService` y `LLMClient`  
✅ Búsqueda híbrida: keyword matching + embedding similarity  
✅ Multi-tenant isolation por `organization_id`  
✅ Tests Feature: 10 casos de prueba  
✅ Ruta registrada en `routes/api.php`

---

## 📁 Estructura de Archivos

```
app/
├── Http/
│   ├── Controllers/Api/
│   │   └── RagController.php (NEW - 30 líneas)
│   └── Requests/
│       └── RagAskRequest.php (NEW - 40 líneas)
├── Services/
│   └── RagService.php (NEW - 240 líneas)
tests/
└── Feature/Api/
    └── RagAskTest.php (NEW - 220 líneas)
routes/
└── api.php (MODIFIED +6 líneas - nueva ruta /api/rag/ask)
```

---

## 🚀 Como Usar

### 1. Request

```bash
POST /api/rag/ask

Headers:
- Authorization: Bearer {sanctum_token}
- Content-Type: application/json

Body:
{
  "question": "¿Cuál es la calidad promedio de las evaluaciones?",
  "context_type": "evaluations",  // optional: evaluations|capabilities|competencies|all
  "max_sources": 5,                // optional: 1-10, default 5
  "include_metadata": true         // optional: boolean
}
```

### 2. Response

```json
{
    "success": true,
    "question": "¿Cuál es la calidad promedio de las evaluaciones?",
    "answer": "Basándose en los documentos analizados, la calidad promedio es de 0.87...",
    "sources": [
        {
            "id": "uuid-123",
            "type": "evaluation",
            "relevance_score": 0.92,
            "provider": "deepseek",
            "quality_level": "excellent"
        },
        {
            "id": "uuid-456",
            "type": "evaluation",
            "relevance_score": 0.78,
            "provider": "openai",
            "quality_level": "good"
        }
    ],
    "confidence": 0.85,
    "context_count": 2
}
```

---

## 🔧 Componentes Técnicos

### Form Request: `RagAskRequest`

**Validaciones:**

- `question` → required, string, 5-1000 caracteres
- `context_type` → nullable, one of: evaluations|capabilities|competencies|all
- `max_sources` → nullable, integer 1-10
- `include_metadata` → nullable, boolean

**Defaults (autoset en `validated()`):**

- `max_sources`: 5
- `context_type`: "evaluations"
- `include_metadata`: true

### Service: `RagService`

**Método Principal:**

```php
public function ask(
    string $question,
    string $organizationId,
    string $contextType = 'evaluations',
    int $maxSources = 5
): array
```

**Flujo Interno:**

1. **retrieveRelevantDocuments()**
    - Busca LLMEvaluation records completados
    - Calcula relevancia por:
        - Keyword matching (TF-IDF simple): 60% peso
        - Embedding similarity (cosine): 40% peso
    - Retorna top N con scores

2. **prepareContext()**
    - Construye prompt de contexto con documentos relevantes
    - Formatea para legibilidad LLM

3. **generateAnswer()**
    - Llama a `LLMClient.generate()` con contexto
    - Max tokens: 500
    - Temperature: 0.7

4. **calculateConfidence()**
    - Promedio de relevancia scores de fuentes
    - Rango: 0.0 - 1.0

### Controller: `RagController`

**Single Method:**

- `ask(RagAskRequest $request)` → POST /api/rag/ask
- Valida automáticamente vía Form Request
- Inyecta RagService automáticamente
- Retorna JSON response

---

## 🧪 Tests (10 casos)

Ubicación: `tests/Feature/Api/RagAskTest.php`

✅ **Autenticación**

- Requiere sanctum auth (401 sin token)

✅ **Validación**

- Question es requerida
- Question mínimo 5 caracteres

✅ **Búsqueda**

- Sin documentos relevantes
- Con documentos relevantes
- Retorna estructura esperada

✅ **Multi-tenancy**

- Aislamiento por organization_id
- Usuario no puede ver datos de otra org

✅ **Filtrado**

- Respeta context_type
- Respeta max_sources limit

✅ **Metadata**

- Incluye provider, quality_level cuando disponible

✅ **Confidence**

- Score en rango [0.0, 1.0]

---

## 📊 Algoritmo de Relevancia

### Keyword Matching (60% peso)

```
relevance_keyword = (matches / total_terms) * 0.8
```

- Busca términos de pregunta en documento
- Solo términos > 2 caracteres
- Normalizado a máximo 0.8

### Embedding Similarity (40% peso)

```
relevance_embedding = cosine_similarity(question_embedding, doc_embedding)
```

- Genera embedding de pregunta
- Compara con embedding del documento
- Cosine similarity en rango [0.0, 1.0]

### Score Final

```
final_score = (keyword * 0.6) + (embedding * 0.4)
```

---

## 🔄 Data Flow

```
┌─────────────────────────────────────┐
│   POST /api/rag/ask                 │
│   {question, context_type, ...}     │
└──────────────┬──────────────────────┘
               │
        RagAskRequest
        (validate)
               │
     ┌─────────▼──────────────┐
     │   RagController::ask   │
     │   (auth:sanctum)       │
     └─────────┬──────────────┘
               │
        ┌──────▼──────────────────────┐
        │   RagService::ask           │
        │   (orchestrate RAG flow)    │
        └──────┬─────────────────────┘
               │
    ┌──────────┴──────────┬──────────────┐
    │                     │              │
    ▼                     ▼              ▼
retrieveRelevant    prepareContext  generateAnswer
Documents           (format docs)   (call LLM)
    │                     │              │
    ├─ keyword match      │              │
    ├─ embedding sim      │              │
    └─ multi-tenant       │              │
       isolation          │              │
                    LLMClient
                    generate()
               │
               └──────────┬─────────────┐
                          │             │
                    calculateConfidence
                          │
               ┌──────────▼──────────────┐
               │ JsonResponse            │
               │ {answer, sources, ...}  │
               └─────────────────────────┘
```

---

## 🛡️ Seguridad & Multi-tenancy

✅ **Autenticación:** Requiere Sanctum auth  
✅ **Scoping:** `forOrganization(auth()->user()->organization_id)`  
✅ **Solo completados:** `completed()` status filter  
✅ **Input validation:** Form Request + max lengths  
✅ **Error handling:** Try/catch con logging

---

## 📈 Características MVP

**Incluidas:**

- ✅ Keyword + embedding hybrid search
- ✅ Multi-tenant enforcement
- ✅ Confidence scoring
- ✅ Source attribution
- ✅ Context type filtering
- ✅ Max sources limiting
- ✅ Comprehensive testing

**NO incluidas (para futuras fases):**

- ❌ pgvector similarity search (sin BD - en memoria solamente)
- ❌ Document storage/indexing (solo busca en LLMEvaluation)
- ❌ Streaming responses
- ❌ Follow-up context tracking
- ❌ Refinement loop

---

## 🚀 Próximos Pasos (Post-MVP)

**Sprint 0**: Implementar pgvector en BD para búsqueda más precisaatura  
**QW-4**: Mejoras a Redaction Service  
**QW-5**: Agent Interaction Metrics

---

## 🧪 Ejecutar Tests

```bash
# Specific test
php artisan test tests/Feature/Api/RagAskTest.php

# Specific test method
php artisan test --filter="requires_authentication" tests/Feature/Api/RagAskTest.php

# All tests
composer test
```

---

## 📝 Cambios Realizados

### Archivos Creados

- ✅ `app/Http/Requests/RagAskRequest.php`
- ✅ `app/Services/RagService.php`
- ✅ `app/Http/Controllers/Api/RagController.php`
- ✅ `tests/Feature/Api/RagAskTest.php`

### Archivos Modificados

- ✅ `routes/api.php` - Agregada ruta `/api/rag/ask`

### Dependencias Usadas (ya presentes)

- ✅ `LLMClient` - LLM generation
- ✅ `EmbeddingService` - Text embeddings
- ✅ `LLMEvaluation` - Knowledge base
- ✅ Laravel FormRequest - Validation

---

## 🔗 Endpoint Details

| Método | Ruta         | Auth       | Multi-tenant  |
| ------ | ------------ | ---------- | ------------- |
| POST   | /api/rag/ask | Sanctum ✅ | Org-scoped ✅ |

**Status Code Responses:**

- 200 → Success (even with no sources)
- 401 → Unauthenticated
- 422 → Validation error

---

## 💡 Ejemplos de Uso

### Ejemplo 1: Pregunta Genérica

```bash
curl -X POST http://localhost:8000/api/rag/ask \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "question": "¿Cuál es la tasa de alucinación promedio?"
  }'
```

### Ejemplo 2: Pregunta Específica con Filtros

```bash
curl -X POST http://localhost:8000/api/rag/ask \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "question": "¿Cómo es la calidad de Deepseek?",
    "context_type": "evaluations",
    "max_sources": 3,
    "include_metadata": true
  }'
```

---

## 🎓 Lecciones Aprendidas

1. **Híbrido > Puro**: Keyword + embedding mejor que solo embedding
2. **Confidence Critical**: Usuarios necesitan saber confiabilidad de respuestas
3. **Source Attribution**: Mejor UX con fuentes citadas
4. **Progressive Enhancement**: MVP sin pgvector, upgrade en Sprint 0

---

## 📊 Commit Message

```
feat: QW-3 - RAG endpoint /api/rag/ask con búsqueda híbrida

- Crear Form Request RagAskRequest con validación
- Implementar RagService con retrieval + LLM generation
- Soporte keyword matching (60%) + embedding similarity (40%)
- Multi-tenant scoping automático
- Crear RagController::ask para orquestar flujo
- Registrar ruta POST /api/rag/ask en routes/api.php
- 10 tests Feature Coverage (auth, validation, multi-tenant, filtering)
- Calcular confidence score basado en relevancia de fuentes
- Retornar answer + sources + confidence + context_count
```
