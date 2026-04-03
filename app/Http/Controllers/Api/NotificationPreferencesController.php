<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserNotificationChannel;
use App\Services\Notifications\NotificationDispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationPreferencesController extends Controller
{
    public function __construct(protected NotificationDispatcher $dispatcher) {}

    /**
     * Get user's notification preferences
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $channels = UserNotificationChannel::where('user_id', $user->id)
            ->where('organization_id', $user->organization_id)
            ->get(['channel_type', 'is_active', 'created_at']);

        return response()->json([
            'preferences' => $channels,
            'available_channels' => $this->dispatcher->getAvailableChannels(),
        ]);
    }

    /**
     * Update or create user channel preference
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'channel_type' => 'required|string|in:slack,telegram,email',
            'channel_config' => 'required|array',
            'is_active' => 'boolean',
        ]);

        $channel = UserNotificationChannel::updateOrCreate(
            [
                'user_id' => $user->id,
                'organization_id' => $user->organization_id,
                'channel_type' => $validated['channel_type'],
            ],
            [
                'channel_config' => $validated['channel_config'],
                'is_active' => $validated['is_active'] ?? true,
            ]
        );

        return response()->json($channel, 201);
    }

    /**
     * Toggle channel on/off
     */
    public function toggle(Request $request, string $channelType): JsonResponse
    {
        $user = $request->user();
        $channel = UserNotificationChannel::where('user_id', $user->id)
            ->where('organization_id', $user->organization_id)
            ->where('channel_type', $channelType)
            ->firstOrFail();

        $channel->update(['is_active' => ! $channel->is_active]);

        return response()->json($channel);
    }

    /**
     * Delete channel preference
     */
    public function destroy(Request $request, string $channelType): JsonResponse
    {
        $user = $request->user();
        UserNotificationChannel::where('user_id', $user->id)
            ->where('organization_id', $user->organization_id)
            ->where('channel_type', $channelType)
            ->delete();

        return response()->json(null, 204);
    }
}
