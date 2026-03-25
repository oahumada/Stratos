<?php

namespace App\Services\Messaging;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Organization;
use App\Models\People;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConversationService
{
    public function __construct(
        protected ParticipantManager $participantManager
    ) {}

    /**
     * Create a new conversation within an organization.
     *
     * @param  array  $participantPeopleIds  (including creator if not already present)
     * @param  string  $contextType  (none, learning_assignment, performance_review, etc.)
     *
     * @throws \Exception if organization or people don't exist
     */
    public function createConversation(
        int $organizationId,
        int $createdByPeopleId,
        array $participantPeopleIds,
        ?string $title = null,
        ?string $description = null,
        string $contextType = 'none',
        ?string $contextId = null,
    ): Conversation {
        // Validate organization exists
        $org = Organization::findOrFail($organizationId);

        // Validate creator exists in organization
        $creator = People::where('id', $createdByPeopleId)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        // Validate all participants belong to the organization
        $this->participantManager->validatePeopleInOrganization($organizationId, $participantPeopleIds);

        // Start transaction for conversation + participants
        return DB::transaction(function () use (
            $organizationId,
            $createdByPeopleId,
            $participantPeopleIds,
            $title,
            $description,
            $contextType,
            $contextId,
        ) {
            // Create conversation
            $conversation = Conversation::create([
                'organization_id' => $organizationId,
                'created_by' => $createdByPeopleId,
                'title' => $title,
                'description' => $description,
                'context_type' => $contextType,
                'context_id' => $contextId,
                'is_active' => true,
            ]);

            // Add all participants (including creator)
            $uniqueParticipants = array_unique($participantPeopleIds);
            $this->participantManager->addMultiple($conversation, $uniqueParticipants);

            Log::info('Conversation created', [
                'conversation_id' => $conversation->id,
                'organization_id' => $organizationId,
                'created_by' => $createdByPeopleId,
                'participant_count' => count($uniqueParticipants),
            ]);

            return $conversation;
        });
    }

    /**
     * Get conversation with paginated messages and participant details.
     *
     * @param  string  $conversationId  (UUID)
     * @param  int  $organizationId  (for multi-tenant validation)
     * @return array with keys: conversation, messages, participants, meta
     *
     * @throws \Exception if conversation not found or org_id mismatch
     */
    public function getConversationWithMessages(
        string $conversationId,
        int $organizationId,
        int $page = 1,
        int $perPage = 50,
    ): array {
        // Get conversation with org check
        $conversation = Conversation::where('id', $conversationId)
            ->where('organization_id', $organizationId)
            ->with([
                'organization:id,name',
                'createdByUser:id,name,email',
                'participants' => function ($query) {
                    $query->select('id', 'conversation_id', 'people_id', 'can_send', 'can_read', 'joined_at', 'unread_count');
                    $query->with('people:id,name,email');
                },
            ])
            ->firstOrFail();

        // Get paginated messages
        $messagesQuery = $conversation->messages()
            ->select('id', 'conversation_id', 'people_id', 'body', 'state', 'reply_to_message_id', 'created_at')
            ->with('sender:id,name,email')
            ->with('replyTo:id,body,people_id')
            ->orderBy('created_at', 'desc');

        $messages = $messagesQuery->paginate($perPage, ['*'], 'page', $page);

        return [
            'conversation' => $conversation,
            'messages' => $messages,
            'participants' => $conversation->participants,
            'meta' => [
                'total_messages' => $messagesQuery->count(),
                'total_participants' => $conversation->participants->count(),
                'is_active' => $conversation->is_active,
                'archived_at' => $conversation->archived_at,
            ],
        ];
    }

    /**
     * Archive a conversation (soft delete behavior).
     *
     * @param  string  $conversationId  (UUID)
     * @param  int  $organizationId  (for multi-tenant validation)
     *
     * @throws \Exception if conversation not found or org_id mismatch
     */
    public function archiveConversation(string $conversationId, int $organizationId): bool
    {
        $conversation = Conversation::where('id', $conversationId)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $result = $conversation->update([
            'is_active' => false,
        ]);

        // Soft delete for archival
        $conversation->delete();

        Log::info('Conversation archived', [
            'conversation_id' => $conversationId,
            'organization_id' => $organizationId,
        ]);

        return (bool) $result;
    }

    /**
     * Add a participant to an existing conversation.
     *
     * @param  string  $conversationId  (UUID)
     * @param  int  $organizationId  (for multi-tenant validation)
     *
     * @throws \Exception if conversation or people not found
     */
    public function addParticipant(
        string $conversationId,
        int $organizationId,
        int $peopleId,
        bool $canSend = true,
        bool $canRead = true,
    ): ConversationParticipant {
        // Validate conversation
        $conversation = Conversation::where('id', $conversationId)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        // Validate people exists in organization
        People::where('id', $peopleId)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        // Check if already participant
        $existing = ConversationParticipant::where('conversation_id', $conversationId)
            ->where('people_id', $peopleId)
            ->first();

        if ($existing) {
            // Reactivate if was left
            if ($existing->left_at) {
                $existing->update([
                    'left_at' => null,
                    'can_send' => $canSend,
                    'can_read' => $canRead,
                ]);
            }

            return $existing;
        }

        // Create new participant
        $participant = ConversationParticipant::create([
            'conversation_id' => $conversationId,
            'people_id' => $peopleId,
            'organization_id' => $organizationId,
            'can_send' => $canSend,
            'can_read' => $canRead,
            'joined_at' => now(),
            'unread_count' => 0,
        ]);

        Log::info('Participant added to conversation', [
            'conversation_id' => $conversationId,
            'people_id' => $peopleId,
            'organization_id' => $organizationId,
        ]);

        return $participant;
    }

    /**
     * Remove a participant from a conversation (soft-remove via left_at).
     *
     * @param  string  $conversationId  (UUID)
     * @param  int  $organizationId  (for multi-tenant validation)
     *
     * @throws \Exception if participant not found
     */
    public function removeParticipant(
        string $conversationId,
        int $organizationId,
        int $peopleId,
    ): bool {
        $participant = ConversationParticipant::where('conversation_id', $conversationId)
            ->where('organization_id', $organizationId)
            ->where('people_id', $peopleId)
            ->firstOrFail();

        $result = $participant->update(['left_at' => now()]);

        Log::info('Participant removed from conversation', [
            'conversation_id' => $conversationId,
            'people_id' => $peopleId,
            'organization_id' => $organizationId,
        ]);

        return (bool) $result;
    }

    /**
     * Get total unread message count for a participant across all conversations.
     */
    public function getUnreadCount(int $organizationId, int $peopleId): int
    {
        return ConversationParticipant::where('organization_id', $organizationId)
            ->where('people_id', $peopleId)
            ->whereNull('left_at')
            ->sum('unread_count');
    }
}
