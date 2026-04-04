<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\LtiProviderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LtiController extends Controller
{
    public function __construct(
        protected LtiProviderService $ltiService,
    ) {}

    public function platforms(Request $request): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        return response()->json($this->ltiService->getPlatforms($orgId));
    }

    public function registerPlatform(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|string|max:255',
            'deployment_id' => 'required|string|max:255',
            'platform_url' => 'required|url|max:500',
            'auth_url' => 'required|url|max:500',
            'token_url' => 'required|url|max:500',
            'jwks_url' => 'required|url|max:500',
        ]);

        $orgId = $this->resolveOrganizationId($request);
        $platform = $this->ltiService->registerPlatform($orgId, $request->all());

        return response()->json($platform, 201);
    }

    public function launch(Request $request): JsonResponse
    {
        $request->validate([
            'client_id' => 'required|string',
            'course_id' => 'required|integer',
        ]);

        $platform = $this->ltiService->validateLaunch($request);

        if (! $platform) {
            return response()->json(['error' => 'Invalid LTI launch: platform not found'], 401);
        }

        $response = $this->ltiService->buildLaunchResponse($platform, $request->integer('course_id'));

        return response()->json($response);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
