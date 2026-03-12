<?php

namespace App\Services;

use App\Models\LLMEvaluation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RAGASEvaluator
{
    /**
     * Python RAGAS service endpoint
     */
    protected string $ragas_service_url;

    /**
     * Configuration
     */
    protected array $config;

    public function __construct()
    {
        $this->ragas_service_url = config('services.ragas.url', 'http://localhost:8001');
        $this->config = config('ragas');
    }

    /**
     * Evaluate LLM-generated content using RAGAS framework
     *
     * Provider-agnostic: Works with any LLM provider (DeepSeek, ABACUS, OpenAI, etc.)
     */
    public function evaluate(
        string $inputPrompt,
        string $outputContent,
        string $organizationId,
        ?string $context = null,
        ?string $provider = null,
        ?string $modelVersion = null,
    ): LLMEvaluation {
        // Detect provider if not specified
        if (! $provider) {
            $provider = $this->detectProvider($outputContent) ?? config('ragas.default_provider', 'mock');
        }

        // Create evaluation record
        $evaluation = new LLMEvaluation([
            'organization_id' => $organizationId,
            'llm_provider' => $provider,
            'llm_model' => $modelVersion,
            'input_content' => $inputPrompt,
            'output_content' => $outputContent,
            'context_content' => $context,
            'status' => 'pending',
            'evaluation_config' => $this->config,
            'created_by' => auth()->check() ? auth()->id() : null,
        ]);

        $evaluation->save();

        // Queue evaluation job
        try {
            dispatch(new \App\Jobs\EvaluateLLMGeneration($evaluation->id));
        } catch (\Exception $e) {
            Log::error('Failed to dispatch evaluation job', [
                'evaluation_id' => $evaluation->id,
                'error' => $e->getMessage(),
            ]);

            $evaluation->update([
                'status' => 'failed',
                'error_message' => 'Failed to queue evaluation: '.$e->getMessage(),
            ]);
        }

        return $evaluation;
    }

    /**
     * Perform the actual RAGAS evaluation
     * Called by the evaluation job
     */
    public function performEvaluation(LLMEvaluation $evaluation): array
    {
        $evaluation->update([
            'status' => 'evaluating',
            'started_at' => now(),
        ]);

        try {
            $startTime = microtime(true);

            // Call RAGAS Python service (provider-agnostic)
            $result = $this->callRagasService(
                $evaluation->input_content,
                $evaluation->output_content,
                $evaluation->context_content,
                $evaluation->llm_provider,
            );

            $processingMs = (int) ((microtime(true) - $startTime) * 1000);

            // Extract metrics from result
            $metrics = $this->extractMetrics($result);

            // Assign individual scores to the model BEFORE calculating composite
            $evaluation->faithfulness_score = $metrics['faithfulness'] ?? null;
            $evaluation->relevance_score = $metrics['relevance'] ?? null;
            $evaluation->context_alignment_score = $metrics['context_alignment'] ?? null;
            $evaluation->coherence_score = $metrics['coherence'] ?? null;
            $evaluation->hallucination_rate = $metrics['hallucination'] ?? null;

            // Calculate composite score and normalize
            $compositeScore = $evaluation->calculateCompositeScore();
            $normalizedScore = $evaluation->normalizeScore();
            $qualityLevel = $evaluation->determineQualityLevel((float) $compositeScore);

            // Update evaluation with results
            $evaluation->update([
                'faithfulness_score' => $evaluation->faithfulness_score,
                'relevance_score' => $evaluation->relevance_score,
                'context_alignment_score' => $evaluation->context_alignment_score,
                'coherence_score' => $evaluation->coherence_score,
                'hallucination_rate' => $evaluation->hallucination_rate,
                'composite_score' => $compositeScore,
                'normalized_score' => $normalizedScore,
                'quality_level' => $qualityLevel,
                'metric_details' => $metrics['details'] ?? [],
                'issues_detected' => $metrics['issues'] ?? [],
                'recommendations' => $metrics['recommendations'] ?? [],
                'status' => 'completed',
                'completed_at' => now(),
                'processing_ms' => $processingMs,
                'tokens_used' => $result['tokens_used'] ?? null,
            ]);

            $evaluation->seal();

            Log::info('RAGAS evaluation completed', [
                'evaluation_id' => $evaluation->id,
                'provider' => $evaluation->llm_provider,
                'composite_score' => $compositeScore,
                'quality_level' => $qualityLevel,
                'processing_ms' => $processingMs,
            ]);

            return [
                'success' => true,
                'evaluation' => $evaluation->fresh(),
            ];
        } catch (\Exception $e) {
            $evaluation->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'retry_count' => $evaluation->retry_count + 1,
            ]);

            Log::error('RAGAS evaluation failed', [
                'evaluation_id' => $evaluation->id,
                'provider' => $evaluation->llm_provider,
                'error' => $e->getMessage(),
                'retry_count' => $evaluation->retry_count,
            ]);

            throw $e;
        }
    }

    /**
     * Call RAGAS Python service (provider-agnostic)
     * Returns metrics regardless of LLM provider
     */
    protected function callRagasService(
        string $inputPrompt,
        string $outputContent,
        ?string $context,
        string $provider,
    ): array {
        $timeout = $this->config['providers'][$provider]['timeout_seconds'] ?? 60;

        $payload = [
            'input_prompt' => $inputPrompt,
            'output_content' => $outputContent,
            'context' => $context,
            'provider' => $provider,  // Send provider for awareness/logging
            'evaluation_mode' => $this->config['evaluation_mode'] ?? 'balanced',
        ];

        try {
            $response = Http::timeout($timeout)
                ->post("{$this->ragas_service_url}/evaluate", $payload);

            if (! $response->successful()) {
                throw new \Exception(
                    "RAGAS service returned {$response->status()}: {$response->body()}"
                );
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('RAGAS service call failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'url' => $this->ragas_service_url,
            ]);

            throw new \Exception("Failed to evaluate with RAGAS: {$e->getMessage()}");
        }
    }

    /**
     * Extract and normalize RAGAS metrics from service response
     * Provider-agnostic: Same structure regardless of which LLM generated content
     */
    protected function extractMetrics(array $result): array
    {
        return [
            'faithfulness' => $this->clampScore($result['faithfulness'] ?? 0.0),
            'relevance' => $this->clampScore($result['relevance'] ?? 0.0),
            'context_alignment' => $this->clampScore($result['context_alignment'] ?? 0.0),
            'coherence' => $this->clampScore($result['coherence'] ?? 0.0),
            'hallucination' => $this->clampScore($result['hallucination'] ?? 0.0),
            'details' => $result['metric_details'] ?? [],
            'issues' => $result['issues'] ?? [],
            'recommendations' => $result['recommendations'] ?? [],
        ];
    }

    /**
     * Ensure score is in valid 0-1 range
     */
    protected function clampScore(float $score): float
    {
        return round(max(0.0, min(1.0, $score)), 2);
    }

    /**
     * Auto-detect LLM provider from output characteristics
     * Heuristic detection when provider not explicitly provided
     */
    protected function detectProvider(?string $content): ?string
    {
        if (! $content) {
            return null;
        }

        // Heuristic patterns (can be enhanced)
        if (stripos($content, 'deepseek') !== false) {
            return 'deepseek';
        }
        if (stripos($content, 'openai') !== false || stripos($content, 'gpt-') !== false) {
            return 'openai';
        }
        if (stripos($content, 'abacus') !== false) {
            return 'abacus';
        }

        return null;
    }

    /**
     * Get provider baseline score
     */
    public function getProviderBaseline(string $provider): float
    {
        return $this->config['providers'][$provider]['baseline_score']
            ?? $this->config['providers']['mock']['baseline_score'];
    }

    /**
     * Get evaluation metrics summary for organization
     */
    public function getOrganizationMetrics(string $organizationId, ?string $provider = null): array
    {
        $query = LLMEvaluation::forOrganization($organizationId)
            ->completed()
            ->latestOnly();

        if ($provider) {
            $query->byProvider($provider);
        }

        $evaluations = $query->get();

        if ($evaluations->isEmpty()) {
            return [
                'total_evaluations' => 0,
                'avg_composite_score' => 0.0,
                'quality_distribution' => [],
                'provider_distribution' => [],
            ];
        }

        $counts = $evaluations->countBy('quality_level');

        return [
            'total_evaluations' => $evaluations->count(),
            'avg_composite_score' => round($evaluations->avg('composite_score'), 2),
            'max_composite_score' => round($evaluations->max('composite_score'), 2),
            'min_composite_score' => round($evaluations->min('composite_score'), 2),
            'quality_distribution' => [
                'excellent' => $counts->get('excellent', 0),
                'good' => $counts->get('good', 0),
                'acceptable' => $counts->get('acceptable', 0),
                'poor' => $counts->get('poor', 0),
                'critical' => $counts->get('critical', 0),
            ],
            'provider_distribution' => $evaluations->countBy('llm_provider')->toArray(),
            'last_evaluation_at' => $evaluations->max('completed_at'),
        ];
    }
}
