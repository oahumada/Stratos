<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RagAskRequest;
use App\Services\RagService;
use Illuminate\Http\JsonResponse;

class RagController extends Controller
{
    protected RagService $ragService;

    public function __construct(RagService $ragService)
    {
        $this->ragService = $ragService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Ask a question using RAG (Retrieval Augmented Generation)
     *
     * POST /api/rag/ask
     */
    public function ask(RagAskRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->ragService->ask(
            question: $validated['question'],
            organizationId: auth()->user()->organization_id,
            contextType: $validated['context_type'],
            maxSources: $validated['max_sources'],
        );

        return response()->json($result);
    }
}
