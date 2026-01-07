<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrganizationUseCase;
use App\Models\ScenarioTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationUseCaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $query = OrganizationUseCase::query()
            ->forOrganization($organizationId)
            ->with(['template']);

        if ($active = $request->query('is_active')) {
            $query->where('is_active', filter_var($active, FILTER_VALIDATE_BOOLEAN));
        }

        $useCases = $query->orderByDesc('activated_at')->get();

        return response()->json([
            'success' => true,
            'data' => $useCases,
        ]);
    }

    public function activate(ScenarioTemplate $template, Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $data = $request->validate([
            'custom_config' => 'nullable|array',
        ]);

        $useCase = OrganizationUseCase::updateOrCreate(
            [
                'organization_id' => $organizationId,
                'use_case_template_id' => $template->id,
            ],
            [
                'is_active' => true,
                'activated_at' => now(),
                'custom_config' => $data['custom_config'] ?? null,
            ]
        );

        // Track template usage
        $template->incrementUsage();

        return response()->json([
            'success' => true,
            'message' => 'Use case activated',
            'data' => $useCase->load('template'),
        ], 201);
    }

    public function deactivate(ScenarioTemplate $template): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $useCase = OrganizationUseCase::where('organization_id', $organizationId)
            ->where('use_case_template_id', $template->id)
            ->first();

        if (!$useCase) {
            return response()->json([
                'success' => false,
                'message' => 'Use case not found for this organization',
            ], 404);
        }

        $useCase->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Use case deactivated',
            'data' => $useCase->load('template'),
        ]);
    }
}
