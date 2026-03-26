<?php

namespace App\Http\Controllers\Api\Messaging;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\Messaging\MessagingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        private MessagingService $messagingService
    ) {}

    /**
     * GET /api/messaging/conversations/{conversation}/messages
     * List messages in a conversation (paginated).
     */
    public function index(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorize('view', $conversation);

        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 50);

        $messages = $conversation->messages()
            ->select('id', 'conversation_id', 'people_id', 'body', 'state', 'reply_to_message_id', 'created_at', 'deleted_at')
            ->with([
                'sender:id,name,email',
                'replyTo:id,body,people_id',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $messages->items(),
            'links' => [
                'first' => $messages->url(1),
                'last' => $messages->url($messages->lastPage()),
                'next' => $messages->nextPageUrl(),
                'prev' => $messages->previousPageUrl(),
            ],
            'meta' => [
                'current_page' => $messages->currentPage(),
                'total' => $messages->total(),
                'per_page' => $messages->perPage(),
            ],
        ]);
    }

    /**
     * POST /api/messaging/conversations/{conversation}/messages
     * Send a message to a conversation.
     */
    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorize('sendMessage', $conversation);

        // Validate manually to get better error messages
        $validated = $request->validate([
            'body' => 'required|string|min:1|max:5000',
            'reply_to_message_id' => 'nullable|string|exists:messages,id',
        ]);

        try {
            $user = $request->user();
            
            // Ensure people relationship is loaded
            $user->load('people');
            $people = $user->people;

            if (! $people) {
                return response()->json(['message' => 'User profile not found'], 422);
            }

            $message = $this->messagingService->sendMessage(
                conversationId: $conversation->id,
                organizationId: $user->organization_id,
                senderPeopleId: $people->id,
                body: $validated['body'],
                replyToMessageId: $validated['reply_to_message_id'] ?? null,
            );

            $message->load('sender', 'replyTo');

            return response()->json(['data' => $message], 201);
        } catch (\AuthorizationException $e) {
            // Re-throw auth exceptions to be handled by middleware
            throw $e;
        } catch (\Exception $e) {
            \Log::error('MessageController::store error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * POST /api/messaging/messages/{message}/read
     * Mark a message (and all prior) as read for the authenticated user.
     */
    public function markRead(Request $request, Message $message): JsonResponse
    {
        $this->authorize('view', $message);

        try {
            $user = $request->user();
            
            // Ensure people relationship is loaded
            $user->load('people');

            // Mark the entire conversation as read for the user
            $this->messagingService->markAsRead(
                conversationId: $message->conversation_id,
                organizationId: $user->organization_id,
                readerPeopleId: $user->people->id,
            );

            return response()->json(null, 204);
        } catch (\AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('MessageController::markRead error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
