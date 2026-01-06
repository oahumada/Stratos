<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScenarioComparisonRequest;
use App\Models\ScenarioComparison;
use App\Services\WorkforcePlanningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScenarioComparisonController extends Controller
{
    public function __construct(private WorkforcePlanningService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ScenarioComparison::class);

        $organizationId = auth()->user()->organization_id;
        $query = ScenarioComparison::query()->forOrganization($organizationId);

        if ($search = $request->query('q')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $comparisons = $query->orderByDesc('created_at')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $comparisons->items(),
            'pagination' => [
                'current_page' => $comparisons->currentPage(),
                'total' => $comparisons->total(),
                'per_page' => $comparisons->perPage(),
                'last_page' => $comparisons->lastPage(),
            ],
        ]);
    }

    public function store(StoreScenarioComparisonRequest $request): JsonResponse
    {
        $this->authorize('create', ScenarioComparison::class);

        $data = $request->validated();
        $organizationId = auth()->user()->organization_id;

        $results = $this->service->compareScenarios($data['scenario_ids'], $data['comparison_criteria'] ?? []);

        $comparison = ScenarioComparison::create([
            'organization_id' => $organizationId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'scenario_ids' => $data['scenario_ids'],
            'comparison_criteria' => $data['comparison_criteria'] ?? null,
            'comparison_results' => $results,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Scenario comparison created',
            'data' => $comparison,
        ], 201);
    }

    public function show(ScenarioComparison $comparison): JsonResponse
    {
        $this->authorize('view', $comparison);

        return response()->json([
            'success' => true,
            'data' => $comparison,
        ]);
    }
}
