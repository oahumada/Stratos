<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScenarioTemplateRequest;
use App\Http\Requests\UpdateScenarioTemplateRequest;
use App\Models\ScenarioTemplate;
use App\Services\ScenarioPlanning\ScenarioTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScenarioTemplateController extends Controller
{
    public function __construct(
        private ScenarioTemplateService $templateService,
    ) {}

    /**
     * List all templates with filtering and pagination
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ScenarioTemplate::class);

        $templates = $this->templateService->getTemplates([
            'scenario_type' => $request->query('scenario_type'),
            'industry' => $request->query('industry'),
            'is_active' => $request->query('is_active'),
            'search' => $request->query('search'),
        ], $request->query('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $templates->items(),
            'pagination' => [
                'current_page' => $templates->currentPage(),
                'total' => $templates->total(),
                'per_page' => $templates->perPage(),
                'last_page' => $templates->lastPage(),
            ],
        ]);
    }

    /**
     * Get a single template with usage tracking
     */
    public function show(ScenarioTemplate $template): JsonResponse
    {
        $this->authorize('view', $template);

        $template = $this->templateService->getTemplate($template->id);

        return response()->json([
            'success' => true,
            'data' => $template,
        ]);
    }

    /**
     * Create a new template
     */
    public function store(StoreScenarioTemplateRequest $request): JsonResponse
    {
        $this->authorize('create', ScenarioTemplate::class);

        $template = $this->templateService->createTemplate($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Template created successfully',
            'data' => $template,
        ], 201);
    }

    /**
     * Update an existing template
     */
    public function update(UpdateScenarioTemplateRequest $request, ScenarioTemplate $template): JsonResponse
    {
        $this->authorize('update', $template);

        $updated = $this->templateService->updateTemplate($template->id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Template updated successfully',
            'data' => $updated,
        ]);
    }

    /**
     * Delete a template
     */
    public function destroy(ScenarioTemplate $template): JsonResponse
    {
        $this->authorize('delete', $template);

        $this->templateService->deleteTemplate($template->id);

        return response()->json([
            'success' => true,
            'message' => 'Template deleted successfully',
        ]);
    }

    /**
     * Convert an existing scenario to a reusable template
     *
     * POST /api/scenario-templates/save-as-template
     */
    public function saveAsTemplate(Request $request): JsonResponse
    {
        $request->validate([
            'scenario_id' => 'required|integer|exists:scenarios,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'scenario_type' => 'nullable|string|in:transformation,growth,optimization,crisis',
            'industry' => 'nullable|string|in:technology,finance,healthcare,manufacturing,retail,general',
            'icon' => 'nullable|string|max:100',
            'config' => 'nullable|array',
        ]);

        $template = $this->templateService->saveScenarioAsTemplate(
            $request->input('scenario_id'),
            $request->only(['name', 'description', 'scenario_type', 'industry', 'icon', 'config']),
        );

        return response()->json([
            'success' => true,
            'message' => 'Scenario saved as template successfully',
            'data' => $template,
        ], 201);
    }

    /**
     * Create a scenario from a template with customizations
     *
     * POST /api/scenario-templates/{template}/instantiate
     */
    public function instantiate(Request $request, ScenarioTemplate $template): JsonResponse
    {
        $this->authorize('instantiate', $template);

        $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'scenario_type' => 'nullable|string',
            'budget' => 'nullable|numeric',
            'timeline_weeks' => 'nullable|integer|min:1',
            'expected_retention' => 'nullable|numeric|min:0|max:100',
            'config' => 'nullable|array',
        ]);

        $scenario = $this->templateService->instantiateScenarioFromTemplate(
            $template->id,
            $request->only(['name', 'description', 'scenario_type', 'budget', 'timeline_weeks', 'expected_retention', 'config']),
            auth()->user()->current_organization_id ?? null,
        );

        return response()->json([
            'success' => true,
            'message' => 'Scenario created from template successfully',
            'data' => $scenario,
        ], 201);
    }

    /**
     * Clone a template
     *
     * POST /api/scenario-templates/{template}/clone
     */
    public function clone(Request $request, ScenarioTemplate $template): JsonResponse
    {
        $this->authorize('create', ScenarioTemplate::class);

        $request->validate([
            'name' => 'nullable|string|max:255',
        ]);

        $cloned = $this->templateService->cloneTemplate(
            $template->id,
            $request->input('name'),
        );

        return response()->json([
            'success' => true,
            'message' => 'Template cloned successfully',
            'data' => $cloned,
        ], 201);
    }

    /**
     * Get recommended templates for user's organization
     *
     * GET /api/scenario-templates/recommendations
     */
    public function recommendations(Request $request): JsonResponse
    {
        $limit = $request->query('limit', 5);

        $recommendations = $this->templateService->getRecommendedTemplates(
            auth()->user()->current_organization_id ?? null,
            (int) $limit,
        );

        return response()->json([
            'success' => true,
            'data' => $recommendations,
        ]);
    }

    /**
     * Get template statistics and usage analytics
     *
     * GET /api/scenario-templates/statistics
     */
    public function statistics(): JsonResponse
    {
        $this->authorize('viewAny', ScenarioTemplate::class);

        $stats = $this->templateService->getTemplateStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
