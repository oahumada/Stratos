<?php

namespace App\Services\Messaging;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\People;
use Illuminate\Support\Facades\DB;

class ParticipantManager
{
    /**
     * Add multiple participants to a conversation in one transaction.
     *
     * @param Conversation $conversation
     * @param array $peopleIds
     * @param bool $canSend
     * @param bool $canRead
     * @return Collection of ConversationParticipant
     */
    public function addMultiple(
        Conversation $conversation,
        array $peopleIds,
        bool $canSend = true,
        bool $canRead = true,
    ) {
        // Remove duplicates
        $uniqueIds = array_unique($peopleIds);

        return DB::transaction(function () use ($conversation, $uniqueIds, $canSend, $canRead) {
            $participants = [];

            foreach ($uniqueIds as $peopleId) {
                // Check if already exists
                $existing = ConversationParticipant::where('conversation_id', $conversation->id)
                    ->where('people_id', $peopleId)
                    ->first();

                if ($existing) {
                    if ($existing->left_at) {
                        $existing->update([
                            'left_at' => null,
                            'can_send' => $canSend,
                            'can_read' => $canRead,
                        ]);
                    }
                    $participants[] = $existing;
                    continue;
                }

                // Create new
                $participant = ConversationParticipant::create([
                    'conversation_id' => $conversation->id,
                    'people_id' => $peopleId,
                    'organization_id' => $conversation->organization_id,
                    'can_send' => $canSend,
                    'can_read' => $canRead,
                    'joined_at' => now(),
                    'unread_count' => 0,
                ]);

                $participants[] = $participant;
            }

            return $participants;
        });
    }

    /**
     * Validate that all people IDs exist and belong to the given organization.
     *
     * @param int $organizationId
     * @param array $peopleIds
     * @return void
     *
     * @throws \Exception if any people not found or not in organization
     */
    public function validatePeopleInOrganization(int $organizationId, array $peopleIds): void
    {
        $uniqueIds = array_unique($peopleIds);

        $found = People::where('organization_id', $organizationId)
            ->whereIn('id', $uniqueIds)
            ->count();

        if ($found !== count($uniqueIds)) {
            throw new \Exception('Not all people exist in this organization');
        }
    }

    /**
     * Get count of active (non-removed) participants in a conversation.
     *
     * @param string $conversationId   (UUID)
     * @return int
     */
    public function getActiveCount(string $conversationId): int
    {
        return ConversationParticipant::where('conversation_id', $conversationId)
            ->whereNull('left_at')
            ->count();
    }
}
