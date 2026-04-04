<?php

namespace App\Services\Lms;

use App\Models\LmsLtiPlatform;
use Illuminate\Http\Request;

class LtiProviderService
{
    public function registerPlatform(int $orgId, array $data): LmsLtiPlatform
    {
        return LmsLtiPlatform::create([
            'organization_id' => $orgId,
            'name' => $data['name'],
            'client_id' => $data['client_id'],
            'deployment_id' => $data['deployment_id'],
            'platform_url' => $data['platform_url'],
            'auth_url' => $data['auth_url'],
            'token_url' => $data['token_url'],
            'jwks_url' => $data['jwks_url'],
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Validate an LTI 1.3 launch request (simplified stub).
     */
    public function validateLaunch(Request $request): ?LmsLtiPlatform
    {
        $clientId = $request->input('client_id');

        if (! $clientId) {
            return null;
        }

        return LmsLtiPlatform::where('client_id', $clientId)
            ->where('is_active', true)
            ->first();
    }

    public function buildLaunchResponse(LmsLtiPlatform $platform, int $courseId): array
    {
        return [
            'platform' => $platform->name,
            'course_url' => url("/lms/catalog/{$courseId}"),
            'launch_context' => [
                'platform_id' => $platform->id,
                'client_id' => $platform->client_id,
                'deployment_id' => $platform->deployment_id,
                'course_id' => $courseId,
            ],
        ];
    }

    public function getPlatforms(int $orgId)
    {
        return LmsLtiPlatform::forOrganization($orgId)->latest()->get();
    }
}
