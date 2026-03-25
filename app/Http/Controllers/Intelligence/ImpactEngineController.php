<?php

namespace App\Http\Controllers\Intelligence;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\Intelligence\ImpactEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImpactEngineController extends Controller
{
    public function __construct(
        protected ImpactEngineService $impactEngine
    ) {}

    /**
     * Retorna un resumen de KPIs financieros asociados al capital humano.
     */
    public function getSummary(Request $request): JsonResponse
    {
        $orgId = $request->header('X-Organization-ID') ?? Organization::first()?->id;

        if (! $orgId) {
            return response()->json(['error' => 'Organization not found'], 404);
        }

        $kpis = $this->impactEngine->calculateFinancialKPIs((int) $orgId);

        return response()->json($kpis);
    }
}
