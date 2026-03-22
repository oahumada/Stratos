<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
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
        if (empty(trim($text))) {
            return null;
        }

        try {
            switch ($this->provider) {
                case 'openai':
                    return $this->generateOpenAI($text);
                case 'abacus':
                    return $this->generateAbacus($text);
                case 'mock':
                    return $this->generateMock($text);
                default:
                    Log::warning("Unknown embedding provider: {$this->provider}");

                    return null;
            }
        } catch (\Exception $e) {
            Log::error('Embedding generation failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Generate embedding using OpenAI
     */
    protected function generateOpenAI(string $text): ?array
    {
        $apiKey = config('services.openai.key');

        if (empty($apiKey)) {
            Log::warning('OpenAI API key not configured');

            return null;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/embeddings', [
            'model' => $this->model,
            'input' => $text,
        ]);

        if ($response->successful()) {
            return $response->json('data.0.embedding');
        }

        Log::error('OpenAI embedding failed', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return null;
    }

    /**
     * Generate embedding using Abacus (placeholder for future implementation)
     */
    protected function generateAbacus(string $text): ?array
    {
        Log::info('Abacus embeddings not yet implemented, falling back to mock');

        return $this->generateMock($text);
    }

    /**
     * Generate mock embedding for testing (deterministic based on text hash)
     */
    protected function generateMock(string $text): array
    {
        // Generate deterministic embedding based on text hash
        $hash = md5($text);
        $embedding = [];

        // Generate 1536 dimensions (OpenAI text-embedding-3-small size)
        for ($i = 0; $i < 1536; $i++) {
            $seed = hexdec(substr($hash, $i % 32, 2)) + $i;
            $embedding[] = (sin($seed) + cos($seed * 2)) / 2;
        }

        return $embedding;
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
     * Generate embedding for a capability
     */
    public function forCapability(\App\Models\Capability $capability): ?array
    {
        $text = implode(' | ', array_filter([
            $capability->name,
            $capability->description,
        ]));

        return $this->generate($text);
    }

    /**
     * Generate embedding for a scenario
     */
    public function forScenario(\App\Models\Scenario $scenario): ?array
    {
        $text = implode(' | ', array_filter([
            $scenario->name,
            $scenario->description,
            $scenario->assumptions,
        ]));

        return $this->generate($text);
    }

    /**
     * Find similar items using cosine similarity (pgvector <=> operator).
     *
     * Cuando `features.generate_embeddings` está activo y existe la tabla genérica
     * `embeddings`, se usa como fuente primaria usando `resource_type = $table`
     * y `metadata->>'name'` como nombre lógico. Si algo falla, se hace fallback
     * a la estrategia legacy basada en columnas `embedding` propias de cada tabla.
     */
    public function findSimilar(string $table, array $embedding, int $limit = 5, ?int $organizationId = null): array
    {
        if (empty($embedding)) {
            return [];
        }

        $embeddingStr = '['.implode(',', $embedding).']';

        // Intento 1: usar tabla genérica embeddings cuando el feature está activo
        if (config('features.generate_embeddings', false)) {
            try {
                $query = '
                    SELECT 
                        resource_id AS id,
                        (metadata->>\'name\') AS name,
                        1 - (embedding <=> ?) AS similarity
                    FROM embeddings
                    WHERE resource_type = ?
                        AND embedding IS NOT NULL
                ';

                $params = [$embeddingStr, $table];

                if ($organizationId !== null) {
                    $query .= ' AND organization_id = ?';
                    $params[] = $organizationId;
                }

                $query .= '
                    ORDER BY embedding <=> ?
                    LIMIT ?
                ';

                $params[] = $embeddingStr;
                $params[] = $limit;

                return DB::select($query, $params);
            } catch (\Exception $e) {
                Log::warning('Generic embeddings similarity search failed, falling back to legacy', [
                    'error' => $e->getMessage(),
                    'table' => $table,
                ]);
                // continúa hacia el fallback legacy
            }
        }

        // Fallback legacy: consulta directa sobre la tabla con columna embedding propia
        $query = "
            SELECT 
                id, 
                name,
                1 - (embedding <=> ?) as similarity
            FROM {$table}
            WHERE embedding IS NOT NULL
        ";

        $params = [$embeddingStr];

        if ($organizationId !== null) {
            $query .= ' AND organization_id = ?';
            $params[] = $organizationId;
        }

        $query .= '
            ORDER BY embedding <=> ?
            LIMIT ?
        ';

        $params[] = $embeddingStr;
        $params[] = $limit;

        try {
            return DB::select($query, $params);
        } catch (\Exception $e) {
            Log::error('Similarity search failed (legacy)', ['error' => $e->getMessage(), 'table' => $table]);

            return [];
        }
    }

    /**
     * Convert embedding array to PostgreSQL vector format
     */
    public function toVectorString(array $embedding): string
    {
        return '['.implode(',', $embedding).']';
    }

    /**
     * Batch generate embeddings for multiple texts
     */
    public function batchGenerate(array $texts): array
    {
        $embeddings = [];

        foreach ($texts as $key => $text) {
            $embeddings[$key] = $this->generate($text);
        }

        return $embeddings;
    }
}
