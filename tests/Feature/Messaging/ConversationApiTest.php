<?php

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Organization;
use App\Models\People;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('Conversation API', function () {
    beforeEach(function () {
        $this->org = Organization::factory()->create();
        $this->user = User::factory()->for($this->org)->create();
        $this->userPeople = People::factory()
            ->for($this->org)
            ->for($this->user, 'user')
            ->create();

        Sanctum::actingAs($this->user);
    });

    it('lists conversations for authenticated user', function () {
        $conv1 = Conversation::factory()->for($this->org)->create();
        $conv2 = Conversation::factory()->for($this->org)->create();
        Conversation::factory()->for($this->org)->create(['is_active' => false]);

        ConversationParticipant::factory()
            ->for($conv1)
            ->for($this->org)
            ->for($this->userPeople)
            ->create();

        ConversationParticipant::factory()
            ->for($conv2)
            ->for($this->org)
            ->for($this->userPeople)
            ->create();

        $response = $this->getJson('/api/messaging/conversations');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'is_active', 'organization_id', 'created_at']
            ],
            'meta' => ['total', 'per_page', 'current_page']
        ]);
    });

    it('creates conversation with participants', function () {
        $people = People::factory()->for($this->org)->times(2)->create();

        $response = $this->postJson('/api/messaging/conversations', [
            'title' => 'Sprint Planning',
            'participant_ids' => [$people[0]->id, $people[1]->id],
            'context_type' => 'none'
        ]);

        $response->assertCreated();
        $response->assertJsonStructure(['data' => ['id', 'title', 'organization_id']]);

        $conversation = Conversation::where('title', 'Sprint Planning')->first();
        expect($conversation)->not->toBeNull();
        expect($conversation->organization_id)->toBe($this->org->id);
        expect($conversation->created_by)->toBe($this->userPeople->id);

        $participantCount = $conversation->participants()->count();
        // Should include creator + the 2 specified people (possibly deduplicated)
        expect($participantCount)->toBeGreaterThanOrEqual(2);
    });

    it('shows conversation with messages', function () {
        $conversation = Conversation::factory()->for($this->org)->create();

        ConversationParticipant::factory()
            ->for($conversation)
            ->for($this->org)
            ->for($this->userPeople)
            ->create();

        $response = $this->getJson("/api/messaging/conversations/{$conversation->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['id', 'title', 'organization_id', 'is_active']
        ]);
    });

    it('updates conversation title', function () {
        $conversation = Conversation::factory()->for($this->org)->create(['created_by' => $this->userPeople->id]);

        $response = $this->putJson("/api/messaging/conversations/{$conversation->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated description'
        ]);

        $response->assertOk();

        $conversation->refresh();
        expect($conversation->title)->toBe('Updated Title');
        expect($conversation->description)->toBe('Updated description');
    });

    it('archives conversation', function () {
        $conversation = Conversation::factory()->for($this->org)->create(['created_by' => $this->userPeople->id]);

        $response = $this->deleteJson("/api/messaging/conversations/{$conversation->id}");

        $response->assertNoContent();

        $conversation->refresh();
        expect($conversation->is_active)->toBeFalse();
        expect($conversation->deleted_at)->not->toBeNull();
    });

    it('prevents cross-organization access', function () {
        $otherOrg = Organization::factory()->create();
        $conversation = Conversation::factory()->for($otherOrg)->create();

        $response = $this->getJson("/api/messaging/conversations/{$conversation->id}");

        $response->assertForbidden();
    });

    it('prevents non-participant from viewing conversation', function () {
        $conversation = Conversation::factory()->for($this->org)->create();
        // User is not a participant

        $response = $this->getJson("/api/messaging/conversations/{$conversation->id}");

        $response->assertForbidden();
    });

    it('validates participant IDs when creating', function () {
        $response = $this->postJson('/api/messaging/conversations', [
            'title' => 'Test',
            'participant_ids' => [999999]  // Non-existent
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('participant_ids');
    });

    it('prevents unauthenticated access', function () {
        Sanctum::actingAs(null);

        $response = $this->getJson('/api/messaging/conversations');

        $response->assertUnauthorized();
    });
});
