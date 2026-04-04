<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\Cmi5Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Cmi5Controller extends Controller
{
    public function __construct(
        protected Cmi5Service $service,
    ) {}

    public function launch(Request $request, int $package): JsonResponse
    {
        $request->validate([
            'launch_mode' => 'nullable|in:Normal,Browse,Review',
        ]);

        $orgId = $this->resolveOrganizationId($request);
        $userId = $request->user()->id;

        $session = $this->service->createSession(
            $package,
            $userId,
            $orgId,
            $request->input('launch_mode', 'Normal'),
        );

        $launchUrl = $this->service->buildLaunchUrl($session);

        return response()->json([
            'success' => true,
            'data' => [
                'session' => $session,
                'launch_url' => $launchUrl,
            ],
        ]);
    }

    public function fetchUrl(Request $request, int $session): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $sessionModel = \App\Models\LmsCmi5Session::forOrganization($orgId)->findOrFail($session);

        return response()->json([
            'auth-token' => $this->service->getAuthToken($sessionModel->id, $orgId),
            'endpoint' => url('/api/lms/xapi'),
            'actor' => $sessionModel->actor_json,
            'registration' => $sessionModel->registration_id,
            'activityId' => $sessionModel->package->identifier ?? 'activity-'.$sessionModel->package_id,
            'returnURL' => $sessionModel->return_url ?? url('/'),
            'moveOn' => $sessionModel->move_on,
            'masteryScore' => $sessionModel->mastery_score,
            'launchMode' => $sessionModel->launch_mode,
            'entitlementKey' => ['courseStructure' => []],
        ]);
    }

    public function statement(Request $request, int $session): JsonResponse
    {
        $request->validate([
            'verb' => 'required|array',
            'verb.id' => 'required|string',
        ]);

        $orgId = $this->resolveOrganizationId($request);

        $updatedSession = $this->service->handleStatement(
            $session,
            $request->all(),
            $orgId,
        );

        return response()->json(['success' => true, 'data' => $updatedSession]);
    }

    public function authToken(Request $request, int $session): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $token = $this->service->getAuthToken($session, $orgId);

        return response()->json(['auth-token' => $token]);
    }

    public function sessions(Request $request, int $package): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $sessions = \App\Models\LmsCmi5Session::forOrganization($orgId)
            ->where('package_id', $package)
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $sessions]);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
