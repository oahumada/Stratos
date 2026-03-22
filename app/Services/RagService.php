<?php

namespace App\Services;

use App\Models\LLMEvaluation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class RagService
{
    protected EmbeddingService $embeddingService;

    protected LLMClient $llmClient;

    public function __construct(EmbeddingService $embeddingService, LLMClient $llmClient)
    {
        $this->embeddingService = $embeddingService;
        $this->llmClient = $llmClient;
    }

    /**
     * Answer a question using RAG (Retrieval Augmented Generation)
     * Phase 1 MVP: Simple text similarity without pgvector
     */
    public function ask(
        string $question,
        string $organizationId,
        string $contextType = 'evaluations',
        int $maxSources = 5
    ): array {
        $startedAt = microtime(true);

        // Step 1: Retrieve relevant documents from context
        $relevantDocs = $this->retrieve(
            $question,
            $organizationId,
            $contextType,
            $maxSources
        );

        // Step 2: Rank documents (actualmente ya vienen con relevance_score calculado)
        $relevantDocs = $this->rank($relevantDocs, $question);

        if ($relevantDocs->isEmpty()) {
            $durationMs = (int) ((microtime(true) - $startedAt) * 1000);

            Log::info('rag.metrics', [
                'organization_id' => $organizationId,
                'context_type' => $contextType,
                'context_count' => 0,
                'confidence' => 0.0,
                'duration_ms' => $durationMs,
                'has_answer' => false,
                'question_hash' => sha1($question),
            ]);

            return [
                'success' => true,
                'question' => $question,
                'answer' => 'No hay documentos relevantes en la base de conocimiento para responder tu pregunta.',
                'sources' => [],
                'confidence' => 0.0,
                'context_count' => 0,
                'message' => 'No matching documents found',
            ];
        }

        // Step 3: Prepare context for LLM
        $context = $this->assemblePrompt($question, $relevantDocs);

        // Step 4: Generate answer using LLM
        $answer = $this->generate($question, $context);

        // Step 5: Post-filter result (scoping, redacción PII, etc.)
        $answer = $this->postFilter($answer);

        // Step 6: Score confidence based on relevance
        $confidence = $this->calculateConfidence($relevantDocs);

        $contextCount = $relevantDocs->count();
        $durationMs = (int) ((microtime(true) - $startedAt) * 1000);

        Log::info('rag.metrics', [
            'organization_id' => $organizationId,
            'context_type' => $contextType,
            'context_count' => $contextCount,
            'confidence' => $confidence,
            'duration_ms' => $durationMs,
            'has_answer' => true,
            'question_hash' => sha1($question),
        ]);

        return [
            'success' => true,
            'question' => $question,
            'answer' => $answer,
            'sources' => $relevantDocs->map(fn ($doc) => [
                'id' => $doc['id'],
                'type' => $doc['type'],
                'relevance_score' => $doc['relevance_score'],
                'provider' => $doc['provider'] ?? null,
                'quality_level' => $doc['quality_level'] ?? null,
            ])->values()->toArray(),
            'confidence' => $confidence,
            'context_count' => $contextCount,
        ];
    }

    /**
     * Retrieve: obtener documentos relevantes según la pregunta.
     */
    public function retrieve(
        string $question,
        string $organizationId,
        string $contextType,
        int $maxSources
    ): Collection {
        return $this->retrieveRelevantDocuments($question, $organizationId, $contextType, $maxSources);
    }

    /**
     * Retrieve relevant documents based on question
     * MVP: Simple keyword matching + embedding similarity where available
     */
    protected function retrieveRelevantDocuments(
        string $question,
        string $organizationId,
        string $contextType,
        int $maxSources
    ): Collection {
        $docs = collect();

        // Generate question embedding for similarity matching
        $questionEmbedding = $this->embeddingService->generate($question);

        // Search in evaluations
        if (in_array($contextType, ['evaluations', 'all'])) {
            $evaluations = LLMEvaluation::forOrganization($organizationId)
                ->completed()
                ->get()
                ->map(function ($eval) use ($question, $questionEmbedding) {
                    // Calculate relevance score based on keyword matching and embedding similarity
                    $textRelevance = $this->calculateKeywordSimilarity(
                        $question,
                        $eval->context_content ?? $eval->output_content ?? ''
                    );

                    $embeddingRelevance = 0.0;
                    if ($questionEmbedding && $eval->embedding_vector) {
                        $embeddingRelevance = $this->cosineSimilarity($questionEmbedding, $eval->embedding_vector);
                    }

                    return [
                        'id' => $eval->id,
                        'type' => 'evaluation',
                        'content' => $eval->context_content ?? $eval->output_content ?? '',
                        'preview' => substr($eval->output_content ?? '', 0, 200).'...',
                        'relevance_score' => ($textRelevance * 0.6) + ($embeddingRelevance * 0.4),
                        'provider' => $eval->llm_provider,
                        'quality_level' => $eval->quality_level,
                        'created_at' => $eval->created_at,
                    ];
                })
                ->sortByDesc('relevance_score')
                ->take($maxSources);

            $docs = $docs->concat($evaluations);
        }

        // Return top documents by relevance
        return $docs->sortByDesc('relevance_score')->take($maxSources);
    }

    /**
     * Rank: reordenar documentos por relevancia (extensible a futuros criterios).
     */
    public function rank(Collection $documents, string $question): Collection
    {
        // Actualmente la relevancia ya está calculada; simplemente normalizamos el orden
        return $documents->sortByDesc('relevance_score')->values();
    }

    /**
     * Calculate simple keyword similarity (TF-IDF-like approach)
     */
    protected function calculateKeywordSimilarity(string $query, string $text): float
    {
        if (empty($text)) {
            return 0.0;
        }

        $queryTerms = array_filter(explode(' ', strtolower($query)));
        $textLower = strtolower($text);

        $matches = 0;
        foreach ($queryTerms as $term) {
            if (strlen($term) > 2 && stripos($textLower, $term) !== false) {
                $matches++;
            }
        }

        // Normalize: max 1.0, weighted by query term coverage
        return min(1.0, ($matches / max(count($queryTerms), 1)) * 0.8);
    }

    /**
     * Calculate cosine similarity between two vectors
     */
    protected function cosineSimilarity(array $vec1, array $vec2): float
    {
        if (count($vec1) !== count($vec2) || empty($vec1)) {
            return 0.0;
        }

        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        for ($i = 0; $i < count($vec1); $i++) {
            $dotProduct += $vec1[$i] * $vec2[$i];
            $normA += $vec1[$i] ** 2;
            $normB += $vec2[$i] ** 2;
        }

        $denominator = sqrt($normA) * sqrt($normB);

        if ($denominator === 0) {
            return 0.0;
        }

        return round($dotProduct / $denominator, 4);
    }

    /**
     * AssemblePrompt: construir el contexto/prompt completo para el LLM.
     */
    public function assemblePrompt(string $question, Collection $relevantDocs): string
    {
        $contextParts = [
            '## Contexto Relevante',
            '',
            'Pregunta: '.$question,
            '',
            '### Documentos Base de Conocimiento:',
            '',
        ];

        foreach ($relevantDocs as $doc) {
            $contextParts[] = "**Documento [{$doc['type']}]** (Relevancia: ".round($doc['relevance_score'] * 100, 0).'%)';
            $contextParts[] = $doc['content'] ?? $doc['preview'];
            $contextParts[] = '';
        }

        $contextParts[] = 'Por favor, responde la pregunta basándote en el contexto anterior.';

        return implode("\n", $contextParts);
    }

    /**
     * Generate: obtener respuesta del LLM dado el contexto.
     */
    public function generate(string $question, string $context): string
    {
        try {
            $prompt = <<<PROMPT
Eres un asistente experto que responde preguntas basándose en documentos de evaluación de LLM.

{$context}

Responde de manera concisa y profesional, citando específicamente los documentos que apoyan tu respuesta.
PROMPT;

            $result = $this->llmClient->generate($prompt);

            // Handle response which can be string or array
            $responseContent = $result['response'] ?? 'No se pudo generar una respuesta.';

            // If response is array, convert to JSON string
            if (is_array($responseContent)) {
                $responseContent = json_encode($responseContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }

            return (string) $responseContent;
        } catch (\Exception $e) {
            Log::error('RAG answer generation failed', ['error' => $e->getMessage()]);

            return 'Error generando respuesta: '.$e->getMessage();
        }
    }

    /**
     * PostFilter: aplicar filtros finales a la respuesta (scoping, redacción PII, etc.).
     */
    public function postFilter(string $answer): string
    {
        // Por ahora, utilizamos el RedactionService para eliminar PII potencial del texto de salida.
        return RedactionService::redactText($answer);
    }

    /**
     * Calculate overall confidence based on source relevance
     */
    protected function calculateConfidence(Collection $relevantDocs): float
    {
        if ($relevantDocs->isEmpty()) {
            return 0.0;
        }

        $avgRelevance = $relevantDocs->avg('relevance_score');

        return min(1.0, round($avgRelevance, 2));
    }
}
