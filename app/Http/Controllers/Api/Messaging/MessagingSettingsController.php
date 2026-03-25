<?php

namespace App\Http\Controllers\Api\Messaging;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMessagingSettingsRequest;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessagingSettingsController extends Controller
{
    /**
     * Get messaging settings for organization
     */
    public function getSettings(Request $request): JsonResponse
    {
        $orgId = $request->user()->people?->organization_id;

        $settings = [
            'organization_id' => $orgId,
            'retention_days' => 90,
            'max_participants' => 500,
            'enable_read_receipts' => true,
            'enable_typing_indicators' => true,
            'allowed_context_types' => [
                'scenario', 'learning_path', 'project', 'evaluation', 'alert', 'general',
            ],
        ];

        return response()->json([
            'data' => $settings,
        ]);
    }

    /**
     * Update messaging settings for organization
     */
    public function updateSettings(UpdateMessagingSettingsRequest $request): JsonResponse
    {
        $orgId = $request->user()->people?->organization_id;

        // In real implementation, these would be stored in a settings table
        $settings = [
            'organization_id' => $orgId,
            'retention_days' => $request->input('retention_days', 90),
            'max_participants' => $request->input('max_participants', 500),
            'enable_read_receipts' => $request->boolean('enable_read_receipts', true),
            'enable_typing_indicators' => $request->boolean('enable_typing_indicators', true),
            'allowed_context_types' => $request->input('allowed_context_types', [
                'scenario', 'learning_path', 'project', 'evaluation', 'alert', 'general',
            ]),
            'updated_at' => now()->toIso8601String(),
        ];

        return response()->json([
            'data' => $settings,
            'message' => 'Settings updated successfully',
        ]);
    }

    /**
     * Get messaging metrics summary
     */
    public function getMetrics(Request $request): JsonResponse
    {
        $orgId = $request->user()->people?->organization_id;

        $totalConversations = Conversation::where('organization_id', $orgId)->count();
        $activeConversations = Conversation::where('organization_id', $orgId)->where('is_active', true)->count();
        $totalMessages = Message::whereHas('conversation', function ($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })->count();

        $unreadMessages = Message::whereHas('conversation', function ($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })->where('state', '!=', 'read')->count();

        $metrics = [
            'organization_id' => $orgId,
            'total_conversations' => $totalConversations,
            'active_conversations' => $activeConversations,
            'archived_conversations' => $totalConversations - $activeConversations,
            'total_messages' => $totalMessages,
            'unread_messages' => $unreadMessages,
            'average_response_time' => '2h 15m', // placeholder
            'delivery_success_rate' => 98.5,
            'read_rate' => 76.2,
        ];

        return response()->json([
            'data' => $metrics,
        ]);
    }
}
