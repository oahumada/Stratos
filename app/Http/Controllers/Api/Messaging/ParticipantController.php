<?php

namespace App\Http\Controllers\Api\Messaging;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Services\Messaging\ConversationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function __construct(
        private ConversationService $conversationService
    ) {}

    /**
     * POST /api/messaging/conversations/{conversation}/participants
     * Add a participant to a conversation.
     */
    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorize('update', $conversation);

        $validated = $request->validate([
            'people_id' => 'required|integer|exists:people,id',
            'can_send' => 'sometimes|boolean',
            'can_read' => 'sometimes|boolean',
        ]);

        try {
            $participant = $this->conversationService->addParticipant(
                conversationId: $conversation->id,
                organizationId: $request->user()->organization_id,
                peopleId: $validated['people_id'],
                canSend: $validated['can_send'] ?? true,
                canRead: $validated['can_read'] ?? true,
            );

            $participant->load('people');

            return response()->json(['data' => $participant], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * DELETE /api/messaging/conversations/{conversation}/participants/{participant}
     * Remove a participant from a conversation.
     */
    public function destroy(
        Request $request,
        Conversation $conversation,
        ConversationParticipant $participant
    ): JsonResponse {
        $this->authorize('update', $conversation);

        // Extra validation: participant must belong to this conversation
        if ($participant->conversation_id !== $conversation->id) {
            return response()->json(['message' => 'Participant not found in this conversation'], 404);
        }

        try {
            $this->conversationService->removeParticipant(
                conversationId: $conversation->id,
                organizationId: $request->user()->organization_id,
                peopleId: $participant->people_id,
            );

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
