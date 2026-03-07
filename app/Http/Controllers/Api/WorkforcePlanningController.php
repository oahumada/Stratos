<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkforceScenario;
use App\Services\WorkforcePlanningService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkforcePlanningController extends Controller
{
    protected $planningService;

    public function __construct(WorkforcePlanningService $planningService)
    {
        $this->planningService = $planningService;
    }

    public function createScenario(Request $request)
    {
        // Simple logic for the MVP
        $data = $request->validate([
            'name' => 'required|string',
            'growth_percentage' => 'nullable|integer',
            'timeframe_start' => 'nullable|date',
            'timeframe_end' => 'nullable|date',
        ]);

        $scenario = new WorkforceScenario;
        $scenario->id = Str::uuid()->toString();
        $scenario->organization_id = $request->user()?->organization_id ?? Str::uuid()->toString(); // Fallback if no user
        $scenario->name = $data['name'];
        $scenario->timeframe_start = $data['timeframe_start'] ?? now();
        $scenario->timeframe_end = $data['timeframe_end'] ?? now()->addMonths(12);
        // Using "description" as a way to store the growth
        $scenario->description = 'Expected Growth: '.($data['growth_percentage'] ?? 0).'%';
        $scenario->save();

        return response()->json([
            'status' => 'success',
            'scenario' => $scenario,
        ]);
    }

    public function getRecommendations(Request $request, $id)
    {
        $scenario = WorkforceScenario::find($id);

        if (! $scenario) {
            return response()->json(['message' => 'Scenario not found'], 404);
        }

        $recommendations = $this->planningService->getRecommendations($scenario);

        return response()->json([
            'status' => 'success',
            'recommendations' => $recommendations,
            'kpis' => [
                'gaps_closed_percent' => 60,
                'active_strategies' => 3,
                'risk_alerts' => 1,
            ],
        ]);
    }

    public function getScenarios()
    {
        $scenarios = WorkforceScenario::orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'scenarios' => $scenarios,
        ]);
    }
}
