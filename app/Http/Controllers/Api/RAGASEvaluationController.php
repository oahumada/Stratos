<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LLMEvaluation;
use App\Services\RAGASEvaluator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RAGASEvaluationController extends Controller
{
    protected RAGASEvaluator $evaluator;

    public function __construct(RAGASEvaluator $evaluator)
    {
        $this->evaluator = $evaluator;
        $this->middleware('auth:sanctum');
    }

    /**
     * Evaluate LLM-generated content
     *
     * POST /api/qa/llm-evaluations
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', LLMEvaluation::class);

        $validated = $request->validate([
            'input_prompt' => 'required|string|max:10000',
            'output_content' => 'required|string|max:50000',
            'context' => 'nullable|string|max:50000',
            'llm_provider' => 'nullable|string|in:deepseek,abacus,openai,intel,mock',
            'llm_model' => 'nullable|string|max:255',
        ]);

        $evaluation = $this->evaluator->evaluate(
            inputPrompt: $validated['input_prompt'],
            outputContent: $validated['output_content'],
            organizationId: auth()->user()->organization_id,
            context: $validated['context'] ?? null,
            provider: $validated['llm_provider'] ?? null,
            modelVersion: $validated['llm_model'] ?? null,
        );

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $evaluation->id,
                'status' => $evaluation->status,
                'created_at' => $evaluation->created_at,
            ],
            'message' => 'Evaluation queued for processing',
        ], 202);
    }

    /**
     * Get evaluation status and results
     *
     * GET /api/qa/llm-evaluations/{id}
     */
    public function show(string $id): JsonResponse
    {
        $evaluation = LLMEvaluation::findOrFail($id);

        $this->authorize('view', $evaluation);

        return response()->json([
            'success' => true,
            'data' => $this->formatEvaluation($evaluation),
        ]);
    }

    /**
     * List evaluations for organization
     *
     * GET /api/qa/llm-evaluations
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', LLMEvaluation::class);

        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        $provider = $request->get('provider');
        $status = $request->get('status');
        $qualityLevel = $request->get('quality_level');

        $query = LLMEvaluation::forOrganization(auth()->user()->organization_id)
            ->latest('created_at');

        if ($provider) {
            $query->byProvider($provider);
        }

        if ($status) {
            $query->byStatus($status);
        }

        if ($qualityLevel) {
            $query->byQualityLevel($qualityLevel);
        }

        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $paginated->map(fn ($eval) => $this->formatEvaluation($eval)),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'last_page' => $paginated->lastPage(),
            ],
        ]);
    }

    /**
     * Get organization-wide RAGAS metrics
     *
     * GET /api/qa/llm-evaluations/metrics/summary
     */
    public function metrics(Request $request): JsonResponse
    {
        $this->authorize('viewAny', LLMEvaluation::class);

        $provider = $request->get('provider');

        $metrics = $this->evaluator->getOrganizationMetrics(
            organizationId: auth()->user()->organization_id,
            provider: $provider,
        );

        return response()->json([
            'success' => true,
            'data' => $metrics,
        ]);
    }

    /**
     * Format evaluation for API response
     */
    protected function formatEvaluation(LLMEvaluation $evaluation): array
    {
        $data = [
            'id' => $evaluation->id,
            'status' => $evaluation->status,
            'llm_provider' => $evaluation->llm_provider,
            'llm_model' => $evaluation->llm_model,
            'created_at' => $evaluation->created_at->toIso8601String(),
        ];

        // Include metrics only if evaluation is completed
        if ($evaluation->status === 'completed') {
            $data['metrics'] = [
                'composite_score' => (float) $evaluation->composite_score,
                'normalized_score' => (float) $evaluation->normalized_score,
                'quality_level' => $evaluation->quality_level,
                'individual_scores' => [
                    'faithfulness' => (float) $evaluation->faithfulness_score,
                    'relevance' => (float) $evaluation->relevance_score,
                    'context_alignment' => (float) $evaluation->context_alignment_score,
                    'coherence' => (float) $evaluation->coherence_score,
                    'hallucination_rate' => (float) $evaluation->hallucination_rate,
                ],
                'issues' => $evaluation->issues_detected ?? [],
                'recommendations' => $evaluation->recommendations ?? [],
            ];
            $data['completed_at'] = $evaluation->completed_at?->toIso8601String();
            $data['processing_ms'] = $evaluation->processing_ms;
        }

        // Include error info if failed
        if ($evaluation->status === 'failed') {
            $data['error'] = $evaluation->error_message;
            $data['retry_count'] = $evaluation->retry_count;
        }

        return $data;
    }
}
