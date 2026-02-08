<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScenarioTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScenarioTemplateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ScenarioTemplate::class);

        $query = ScenarioTemplate::query();

        if ($type = $request->query('scenario_type')) {
            $query->where('scenario_type', $type);
        }

        if ($industry = $request->query('industry')) {
            $query->where(function ($q) use ($industry) {
                $q->where('industry', $industry)
                    ->orWhere('industry', 'general');
            });
        }

        if (! is_null($request->query('is_active'))) {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $templates = $query->orderBy('usage_count', 'desc')->paginate(15);

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

    public function show(ScenarioTemplate $template): JsonResponse
    {
        $this->authorize('view', $template);

        $template->incrementUsage();

        return response()->json([
            'success' => true,
            'data' => $template,
        ]);
    }
}
