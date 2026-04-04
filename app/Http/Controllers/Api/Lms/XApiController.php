<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\XApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class XApiController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id.';

    public function __construct(
        protected XApiService $xApiService,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $request->validate([
            'statements' => 'sometimes|array',
            'statements.*.verb.id' => 'required_with:statements|string',
            'statements.*.object.id' => 'required_with:statements|string',
            'verb.id' => 'required_without:statements|string',
            'object.id' => 'required_without:statements|string',
        ]);

        if ($request->has('statements')) {
            $stored = $this->xApiService->storeStatements($request->input('statements'), $organizationId);

            return response()->json(['statements' => $stored, 'count' => count($stored)], 201);
        }

        $statement = $this->xApiService->storeStatement($request->all(), $organizationId);

        return response()->json($statement, 201);
    }

    public function index(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $filters = $request->only(['actor', 'verb', 'activity', 'since', 'until', 'per_page']);

        $statements = $this->xApiService->queryStatements($organizationId, $filters);

        return response()->json($statements);
    }

    public function activityStats(Request $request, string $objectId): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $stats = $this->xApiService->getActivityStats($objectId, $organizationId);

        return response()->json($stats);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
