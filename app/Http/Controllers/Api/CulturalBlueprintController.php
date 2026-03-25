<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CulturalBlueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CulturalBlueprintController extends Controller
{
    public function show(): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $blueprint = CulturalBlueprint::firstOrCreate(
            ['organization_id' => $orgId],
            [
                'mission' => '',
                'vision' => '',
                'values' => [],
                'principles' => [],
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $blueprint,
            'is_verified' => $blueprint->isVerified(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $data = $request->validate([
            'mission' => 'nullable|string',
            'vision' => 'nullable|string',
            'values' => 'nullable|array',
            'principles' => 'nullable|array',
        ]);

        $blueprint = CulturalBlueprint::updateOrCreate(
            ['organization_id' => $orgId],
            $data
        );

        return response()->json([
            'success' => true,
            'data' => $blueprint,
            'message' => 'Blueprint actualizado correctamente',
        ]);
    }

    public function sign(Request $request): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $blueprint = CulturalBlueprint::where('organization_id', $orgId)->firstOrFail();

        $blueprint->seal();

        return response()->json([
            'success' => true,
            'data' => $blueprint,
            'message' => 'Blueprint firmado digitalmente con Sello Stratos',
        ]);
    }
}
