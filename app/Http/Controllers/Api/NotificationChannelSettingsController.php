<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationChannelSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationChannelSettingsController extends Controller
{
    /**
     * Get organization's notification channel settings (admin only)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $orgId = $user->organization_id;

        $this->authorize('manage-org-notifications', $orgId);

        $settings = NotificationChannelSetting::where('organization_id', $orgId)->get();

        return response()->json(['settings' => $settings]);
    }

    /**
     * Update channel setting for organization
     */
    public function update(Request $request, string $channelType): JsonResponse
    {
        $user = $request->user();
        $orgId = $user->organization_id;

        $this->authorize('manage-org-notifications', $orgId);

        $validated = $request->validate([
            'is_enabled' => 'required|boolean',
            'global_config' => 'nullable|array',
        ]);

        $setting = NotificationChannelSetting::updateOrCreate(
            [
                'organization_id' => $orgId,
                'channel_type' => $channelType,
            ],
            [
                'is_enabled' => $validated['is_enabled'],
                'global_config' => $validated['global_config'] ?? null,
            ]
        );

        return response()->json($setting);
    }

    /**
     * Delete channel setting (disable it)
     */
    public function destroy(Request $request, string $channelType): JsonResponse
    {
        $user = $request->user();
        $orgId = $user->organization_id;

        $this->authorize('manage-org-notifications', $orgId);

        NotificationChannelSetting::where('organization_id', $orgId)
            ->where('channel_type', $channelType)
            ->delete();

        return response()->json(null, 204);
    }
}
