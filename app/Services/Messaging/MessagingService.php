<?php

namespace App\Services\Messaging;

use App\Enums\MessageState;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessagingService
{
    public function __construct(
        protected ParticipantManager $participantManager
    ) {}

    /**
     * Send a message within a conversation.
     *
     * @param  string  $conversationId  (UUID)
     * @param  int  $organizationId  (for multi-tenant validation)
     * @param  string|null  $replyToMessageId  (UUID, optional)
     *
     * @throws \Exception if conversation not found, sender not participant, or lacks permission
     */
    public function sendMessage(
        string $conversationId,
        int $organizationId,
        int $senderPeopleId,
        string $body,
        ?string $replyToMessageId = null,
    ): Message {
        // Validate conversation exists
        $conversation = Conversation::where('id', $conversationId)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        // Validate sender is participant and can send
        $this->validateSenderAccess($conversationId, $organizationId, $senderPeopleId);

        // Validate reply message if provided
        $replyTo = null;
        if ($replyToMessageId) {
            $replyTo = Message::where('id', $replyToMessageId)
                ->where('conversation_id', $conversationId)
                ->where('organization_id', $organizationId)
                ->firstOrFail();
        }

        // Create message
        $message = Message::create([
            'conversation_id' => $conversationId,
            'organization_id' => $organizationId,
            'people_id' => $senderPeopleId,
            'body' => $body,
            'state' => MessageState::Sent,
            'reply_to_message_id' => $replyToMessageId,
        ]);

        // Update conversation's last_message_at and last_message_id
        $conversation->update([
            'last_message_at' => now(),
            'last_message_id' => $message->id,
        ]);

        // Increment unread count for all OTHER active participants
        ConversationParticipant::where('conversation_id', $conversationId)
            ->where('people_id', '!=', $senderPeopleId)
            ->whereNull('left_at')
            ->increment('unread_count');

        Log::info('Message sent', [
            'message_id' => $message->id,
            'conversation_id' => $conversationId,
            'sender_id' => $senderPeopleId,
            'organization_id' => $organizationId,
        ]);

        return $message;
    }

    /**
     * Mark all messages in a conversation as read for a participant.
     *
     * @param  string  $conversationId  (UUID)
     * @param  int  $organizationId  (for multi-tenant validation)
     *
     * @throws \Exception if conversation or participant not found
     */
    public function markAsRead(
        string $conversationId,
        int $organizationId,
        int $readerPeopleId,
    ): void {
        // Validate participant exists
        $participant = ConversationParticipant::where('conversation_id', $conversationId)
            ->where('organization_id', $organizationId)
            ->where('people_id', $readerPeopleId)
            ->firstOrFail();

        // Start transaction
        DB::transaction(function () use ($participant) {
            // Update all unread messages to READ state for this participant
            // (This assumes we track read status per-participant; if not, adjust)
            // For now, we just reset unread_count and update last_read_at

            $participant->update([
                'unread_count' => 0,
                'last_read_at' => now(),
            ]);

            // Note: If you need per-message read tracking, you'd add a new table
            // like message_reads(message_id, people_id, read_at)
            // For MVP, we track unread_count at the participant level
        });

        Log::info('Conversation marked as read', [
            'conversation_id' => $conversationId,
            'people_id' => $readerPeopleId,
            'organization_id' => $organizationId,
        ]);
    }

    /**
     * Get total unread message count for a person across all conversations.
     */
    public function getUnreadTotalCount(int $organizationId, int $peopleId): int
    {
        return ConversationParticipant::where('organization_id', $organizationId)
            ->where('people_id', $peopleId)
            ->whereNull('left_at')
            ->sum('unread_count');
    }

    /**
     * Validate that a person is a participant in a conversation and can send messages.
     * Throws exception if not allowed.
     *
     * @param  string  $conversationId  (UUID)
     * @param  int  $organizationId  (for multi-tenant validation)
     *
     * @throws \Exception if not a participant or lacks send permission
     */
    public function validateSenderAccess(
        string $conversationId,
        int $organizationId,
        int $peoplePeopleId,
    ): void {
        $participant = ConversationParticipant::where('conversation_id', $conversationId)
            ->where('organization_id', $organizationId)
            ->where('people_id', $peoplePeopleId)
            ->first();

        if (! $participant) {
            throw new \Exception('Unauthorized: not a participant in this conversation');
        }

        if (! $participant->can_send) {
            throw new \Exception('Unauthorized: cannot send messages in this conversation');
        }

        if ($participant->left_at) {
            throw new \Exception('Unauthorized: no longer a participant (left at '.$participant->left_at.')');
        }
    }
}
