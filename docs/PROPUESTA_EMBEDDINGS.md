# 🎯 Propuesta: Integración de Embeddings en Importación LLM

## Situación Actual

### ✅ Lo que tenemos

- **Campos de embedding** (`vector(1536)`) en tablas:
    - `competencies.embedding`
    - `roles.embedding`
    - `skills.embedding`
    - `scenarios.embedding`
    - `scenario_roles.embedding`
    - `scenario_generations.embedding`

### ❌ Lo que NO estamos haciendo

- **NO generamos embeddings** durante la importación LLM
- **NO almacenamos** representaciones vectoriales de las entidades importadas
- **NO aprovechamos** la búsqueda semántica para:
    - Detectar duplicados similares
    - Sugerir competencias/skills relacionadas
    - Comparar roles entre scenarios

---

## 🎯 Propuesta de Implementación

### Fase 2.1: Generación de Embeddings en Importación

#### 1. Servicio de Embeddings

**Crear**: `app/Services/EmbeddingService.php`

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    protected string $provider;
    protected string $model;

    public function __construct()
    {
        $this->provider = config('services.embeddings.provider', 'openai');
        $this->model = config('services.embeddings.model', 'text-embedding-3-small');
    }

    /**
     * Generate embedding for a given text
     */
    public function generate(string $text): ?array
    {
        try {
            switch ($this->provider) {
                case 'openai':
                    return $this->generateOpenAI($text);
                case 'abacus':
                    return $this->generateAbacus($text);
                default:
                    Log::warning("Unknown embedding provider: {$this->provider}");
                    return null;
            }
        } catch (\Exception $e) {
            Log::error("Embedding generation failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate embedding using OpenAI
     */
    protected function generateOpenAI(string $text): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/embeddings', [
            'model' => $this->model,
            'input' => $text,
        ]);

        if ($response->successful()) {
            return $response->json('data.0.embedding');
        }

        return null;
    }

    /**
     * Generate embedding using Abacus
     */
    protected function generateAbacus(string $text): ?array
    {
        // Implementar cuando Abacus soporte embeddings
        return null;
    }

    /**
     * Generate embedding for a role
     */
    public function forRole(\App\Models\Roles $role): ?array
    {
        $text = implode(' | ', array_filter([
            $role->name,
            $role->description,
            $role->responsibilities,
        ]));

        return $this->generate($text);
    }

    /**
     * Generate embedding for a competency
     */
    public function forCompetency(\App\Models\Competency $competency): ?array
    {
        $text = implode(' | ', array_filter([
            $competency->name,
            $competency->description,
        ]));

        return $this->generate($text);
    }

    /**
     * Generate embedding for a skill
     */
    public function forSkill(\App\Models\Skill $skill): ?array
    {
        $text = implode(' | ', array_filter([
            $skill->name,
            $skill->description,
        ]));

        return $this->generate($text);
    }

    /**
     * Find similar items using cosine similarity
     */
    public function findSimilar(string $table, array $embedding, int $limit = 5): array
    {
        $embeddingStr = '[' . implode(',', $embedding) . ']';

        return \DB::select("
            SELECT
                id,
                name,
                1 - (embedding <=> ?) as similarity
            FROM {$table}
            WHERE embedding IS NOT NULL
            ORDER BY embedding <=> ?
            LIMIT ?
        ", [$embeddingStr, $embeddingStr, $limit]);
    }
}
```

#### 2. Actualizar `ScenarioGenerationService::finalizeScenarioImport()`

**Modificar**: `app/Services/ScenarioGenerationService.php`

```php
// Agregar al inicio del archivo
use App\Services\EmbeddingService;

// En el método finalizeScenarioImport(), después de crear cada entidad:

// Ejemplo para Roles (línea ~673)
$role = \App\Models\Roles::updateOrCreate(
    [
        'organization_id' => $orgId,
        'name' => $roleData['name'],
    ],
    [
        'description' => $roleData['description'] ?? null,
        'status' => 'in_incubation',
        'discovered_in_scenario_id' => $scenario->id,
    ]
);

// 🆕 GENERAR EMBEDDING
if (config('features.generate_embeddings', false)) {
    $embeddingService = app(EmbeddingService::class);
    $embedding = $embeddingService->forRole($role);

    if ($embedding) {
        $role->update(['embedding' => $embedding]);

        // Opcional: buscar roles similares
        $similar = $embeddingService->findSimilar('roles', $embedding, 3);
        Log::info("Similar roles found for '{$role->name}':", $similar);
    }
}
```

#### 3. Configuración

**Agregar a**: `config/features.php`

```php
return [
    // ... existing features

    'generate_embeddings' => env('FEATURE_GENERATE_EMBEDDINGS', false),
];
```

**Agregar a**: `config/services.php`

```php
return [
    // ... existing services

    'embeddings' => [
        'provider' => env('EMBEDDINGS_PROVIDER', 'openai'),
        'model' => env('EMBEDDINGS_MODEL', 'text-embedding-3-small'),
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],
];
```

**Variables de entorno**:

```env
FEATURE_GENERATE_EMBEDDINGS=true
EMBEDDINGS_PROVIDER=openai
EMBEDDINGS_MODEL=text-embedding-3-small
OPENAI_API_KEY=sk-...
```

---

## 🎁 Beneficios

### 1. Detección de Duplicados Semánticos

```php
// Antes de crear un rol, verificar si ya existe uno similar
$embedding = $embeddingService->generate($roleData['name'] . ' ' . $roleData['description']);
$similar = $embeddingService->findSimilar('roles', $embedding, 1);

if ($similar[0]->similarity > 0.95) {
    // Sugerir usar el rol existente en lugar de crear uno nuevo
    Log::info("High similarity detected with existing role: {$similar[0]->name}");
}
```

### 2. Búsqueda Semántica en UI

```javascript
// Frontend: buscar roles por descripción natural
const response = await axios.post('/api/roles/semantic-search', {
  query: "persona que gestiona equipos de desarrollo ágil"
});

// Backend endpoint
public function semanticSearch(Request $request)
{
    $embedding = $this->embeddingService->generate($request->query);
    return $this->embeddingService->findSimilar('roles', $embedding, 10);
}
```

### 3. Recomendaciones Inteligentes

```php
// Sugerir competencias relacionadas a un rol
$roleEmbedding = $role->embedding;
$suggestedCompetencies = $embeddingService->findSimilar('competencies', $roleEmbedding, 5);
```

### 4. Análisis de Evolución

```php
// Comparar un rol antes y después de un scenario
$originalRole = Roles::find($roleId);
$incubatedRole = Roles::where('discovered_in_scenario_id', $scenarioId)
                      ->where('name', 'LIKE', "%{$originalRole->name}%")
                      ->first();

$similarity = $this->cosineSimilarity($originalRole->embedding, $incubatedRole->embedding);

if ($similarity < 0.7) {
    Log::info("Role has significantly evolved in this scenario");
}
```

---

## 📋 Plan de Implementación

### Fase 2.1 (Inmediata)

1. ✅ Crear `EmbeddingService`
2. ✅ Agregar feature flag `generate_embeddings`
3. ✅ Integrar en `finalizeScenarioImport()` para roles, competencies, skills
4. ✅ Agregar logging de roles similares encontrados
5. ✅ Documentar en `openmemory.md`

### Fase 2.2 (Siguiente Sprint)

1. Crear endpoint `/api/roles/semantic-search`
2. Agregar UI para búsqueda semántica en catálogo
3. Implementar sugerencias de competencias relacionadas
4. Dashboard de duplicados detectados

### Fase 2.3 (Futuro)

1. Backfill de embeddings para entidades existentes
2. Re-generación automática cuando cambia descripción
3. Clustering de roles/competencias por similitud
4. Visualización de "mapa semántico" en el grafo

---

## ⚠️ Consideraciones

### Costos

- **OpenAI text-embedding-3-small**: ~$0.02 / 1M tokens
- **Estimación**: 100 roles × 50 tokens promedio = 5,000 tokens = **$0.0001**
- **Muy económico** comparado con generación de texto

### Performance

- Generación de embedding: ~100-200ms por llamada
- **Solución**: Generar en background job si hay muchas entidades
- Alternativa: Batch API de OpenAI (más económico y rápido)

### Privacidad

- Los embeddings **NO contienen** el texto original
- Son vectores numéricos (1536 dimensiones)
- **Seguro** para almacenar incluso con datos sensibles

---

## 🚀 Próximos Pasos

**¿Quieres que implemente esto ahora?**

Opciones:

1. **Implementar Fase 2.1 completa** (EmbeddingService + integración)
2. **Solo crear el servicio** y dejarlo listo para usar
3. **Posponer** para después de completar workflow de aprobación

**Recomendación**: Implementar Fase 2.1 ahora porque:

- Es rápido (30 minutos)
- No afecta funcionalidad existente (feature flag)
- Agrega valor inmediato (detección de duplicados en logs)
- Prepara el terreno para búsqueda semántica (Fase 2.2)
