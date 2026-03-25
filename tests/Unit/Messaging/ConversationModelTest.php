<?php

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Organization;
use App\Models\People;

describe('Conversation Model', function () {
    beforeEach(function () {
        $this->org = Organization::factory()->create();
        $this->people = People::factory()->for($this->org)->create();
    });

    it('determines if user is participant', function () {
        $conversation = Conversation::factory()->for($this->org)->create();

        ConversationParticipant::factory()
            ->for($conversation)
            ->for($this->org)
            ->for($this->people)
            ->create();

        expect($conversation->isParticipant($this->people->id))->toBeTrue();

        $other_people = People::factory()->for($this->org)->create();
        expect($conversation->isParticipant($other_people->id))->toBeFalse();
    });

    it('adds participant correctly', function () {
        $conversation = Conversation::factory()->for($this->org)->create();

        $participant = $conversation->addParticipant($this->people->id);

        expect($participant)->toBeInstanceOf(ConversationParticipant::class);
        expect($participant->conversation_id)->toBe($conversation->id);
        expect($participant->people_id)->toBe($this->people->id);
        expect($participant->can_send)->toBeTrue();
        expect($participant->can_read)->toBeTrue();
        expect($participant->left_at)->toBeNull();
    });

    it('marks conversation as read for participant', function () {
        $conversation = Conversation::factory()->for($this->org)->create();

        $participant = ConversationParticipant::factory()
            ->for($conversation)
            ->for($this->org)
            ->for($this->people)
            ->create(['unread_count' => 5]);

        $conversation->markAsRead($this->people->id);

        $participant->refresh();
        expect($participant->unread_count)->toBe(0);
        expect($participant->last_read_at)->not->toBeNull();
    });

    it('scope active filters correctly', function () {
        $activeConv = Conversation::factory()->for($this->org)->create(['is_active' => true]);
        $inactiveConv = Conversation::factory()->for($this->org)->create(['is_active' => false]);
        $archivedConv = Conversation::factory()
            ->for($this->org)
            ->create();
        $archivedConv->delete();  // Soft delete

        $results = Conversation::where('organization_id', $this->org->id)->active()->pluck('id');

        expect($results)->toContain($activeConv->id);
        expect($results)->not->toContain($inactiveConv->id);
        expect($results)->not->toContain($archivedConv->id);
    });

    it('scope for organization filters correctly', function () {
        $otherOrg = Organization::factory()->create();

        $convInOrg = Conversation::factory()->for($this->org)->create();
        $convInOtherOrg = Conversation::factory()->for($otherOrg)->create();

        $results = Conversation::forOrganization($this->org->id)->pluck('id');

        expect($results)->toContain($convInOrg->id);
        expect($results)->not->toContain($convInOtherOrg->id);
    });
});
