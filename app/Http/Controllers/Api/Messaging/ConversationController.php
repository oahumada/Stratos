<?php

namespace App\Http\Controllers\Api\Messaging;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\UpdateConversationRequest;
use App\Models\Conversation;
use App\Services\Messaging\ConversationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ConversationController extends Controller
{
    public function __construct(
        private ConversationService $conversationService
    ) {}

    /**
     * GET /api/messaging/conversations
     * List conversations for authenticated user's organization.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Conversation::class);

        $user = $request->user();
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);

        // Get conversations for organization (active only)
        $conversations = Conversation::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->with([
                'createdByUser:id,name,email',
                'participants' => function ($query) {
                    $query->select('id', 'conversation_id', 'people_id', 'unread_count')
                        ->whereNull('left_at');
                },
            ])
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $conversations->items(),
            'links' => [
                'first' => $conversations->url(1),
                'last' => $conversations->url($conversations->lastPage()),
                'next' => $conversations->nextPageUrl(),
                'prev' => $conversations->previousPageUrl(),
            ],
            'meta' => [
                'current_page' => $conversations->currentPage(),
                'from' => $conversations->firstItem(),
                'to' => $conversations->lastItem(),
                'total' => $conversations->total(),
                'per_page' => $conversations->perPage(),
                'last_page' => $conversations->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/messaging/conversations
     * Create a new conversation.
     */
    public function store(StoreConversationRequest $request): JsonResponse
    {
        $this->authorize('create', Conversation::class);

        $user = $request->user();

        // Ensure creator is included in participants
        $participantIds = array_unique(
            array_merge([$user->people_id], $request->validated()['participant_ids'] ?? [])
        );

        try {
            $conversation = $this->conversationService->createConversation(
                organizationId: $user->organization_id,
                createdByPeopleId: $user->people_id,
                participantPeopleIds: $participantIds,
                title: $request->validated()['title'] ?? null,
                description: $request->validated()['description'] ?? null,
                contextType: $request->validated()['context_type'] ?? 'none',
                contextId: $request->validated()['context_id'] ?? null,
            );

            $conversation->load('participants', 'createdByUser');

            return response()->json(['data' => $conversation], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * GET /api/messaging/conversations/{conversation}
     * Get conversation with messages for authenticated user.
     */
    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorize('view', $conversation);

        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 50);

        try {
            $data = $this->conversationService->getConversationWithMessages(
                conversationId: $conversation->id,
                organizationId: $request->user()->organization_id,
                page: $page,
                perPage: $perPage,
            );

            return response()->json([
                'data' => [
                    'conversation' => $data['conversation'],
                    'messages' => $data['messages'],
                    'participants' => $data['participants'],
                    'meta' => $data['meta'],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    /**
     * PUT /api/messaging/conversations/{conversation}
     * Update conversation details.
     */
    public function update(UpdateConversationRequest $request, Conversation $conversation): JsonResponse
    {
        $this->authorize('update', $conversation);

        try {
            $conversation->update($request->validated());

            return response()->json(['data' => $conversation]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * DELETE /api/messaging/conversations/{conversation}
     * Archive a conversation (soft delete).
     */
    public function destroy(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorize('delete', $conversation);

        try {
            $this->conversationService->archiveConversation(
                conversationId: $conversation->id,
                organizationId: $request->user()->organization_id,
            );

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
