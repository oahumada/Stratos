<?php

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\Organization;
use App\Models\People;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('Message API', function () {
    beforeEach(function () {
        $this->org = Organization::factory()->create();

        $this->sender = User::factory()->for($this->org)->create();
        $this->senderPeople = People::factory()
            ->for($this->org)
            ->for($this->sender, 'user')
            ->create();

        $this->recipient = User::factory()->for($this->org)->create();
        $this->recipientPeople = People::factory()
            ->for($this->org)
            ->for($this->recipient, 'user')
            ->create();

        $this->conversation = Conversation::factory()->for($this->org)->create();

        ConversationParticipant::factory()
            ->for($this->conversation)
            ->for($this->org)
            ->for($this->senderPeople)
            ->create(['can_send' => true]);

        ConversationParticipant::factory()
            ->for($this->conversation)
            ->for($this->org)
            ->for($this->recipientPeople)
            ->create(['can_send' => true, 'unread_count' => 0]);

        Sanctum::actingAs($this->sender);
    });

    it('lists messages in conversation', function () {
        Message::factory()
            ->for($this->conversation)
            ->for($this->org)
            ->for($this->senderPeople)
            ->times(3)
            ->create();

        $response = $this->getJson("/api/messaging/conversations/{$this->conversation->id}/messages");

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'body', 'state', 'sender', 'created_at'],
            ],
            'meta' => ['total', 'per_page'],
        ]);
    });

    it('sends message to conversation', function () {
        $response = $this->postJson(
            "/api/messaging/conversations/{$this->conversation->id}/messages",
            ['body' => 'Hello team!']
        );

        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => ['id', 'body', 'state', 'people_id', 'conversation_id', 'created_at'],
        ]);

        $message = Message::where('conversation_id', $this->conversation->id)->first();
        expect($message)->not->toBeNull();
        expect($message->body)->toBe('Hello team!');
        expect($message->people_id)->toBe($this->senderPeople->id);

        // Verify unread count incremented for recipient
        $recipientParticipant = ConversationParticipant::where('people_id', $this->recipientPeople->id)
            ->where('conversation_id', $this->conversation->id)
            ->first();
        expect($recipientParticipant->unread_count)->toBe(1);
    });

    it('sends message with reply_to', function () {
        $originalMessage = Message::factory()
            ->for($this->conversation)
            ->for($this->org)
            ->for($this->senderPeople)
            ->create();

        $response = $this->postJson(
            "/api/messaging/conversations/{$this->conversation->id}/messages",
            [
                'body' => 'Replying to this',
                'reply_to_message_id' => $originalMessage->id,
            ]
        );

        $response->assertCreated();

        $replyMessage = Message::where('reply_to_message_id', $originalMessage->id)->first();
        expect($replyMessage)->not->toBeNull();
        expect($replyMessage->body)->toBe('Replying to this');
    });

    it('marks conversation as read', function () {
        $message = Message::factory()
            ->for($this->conversation)
            ->for($this->org)
            ->for($this->senderPeople)
            ->create();

        // Switch to recipient
        Sanctum::actingAs($this->recipient);

        // First, increase unread count
        $participant = ConversationParticipant::where('people_id', $this->recipientPeople->id)
            ->where('conversation_id', $this->conversation->id)
            ->first();
        $participant->update(['unread_count' => 3]);

        $response = $this->postJson("/api/messaging/messages/{$message->id}/read");

        $response->assertNoContent();

        $participant->refresh();
        expect($participant->unread_count)->toBe(0);
        expect($participant->last_read_at)->not->toBeNull();
    });

    it('prevents non-participant from sending message', function () {
        $nonParticipant = User::factory()->for($this->org)->create();
        $nonParticipantPeople = People::factory()
            ->for($this->org)
            ->for($nonParticipant, 'user')
            ->create();

        Sanctum::actingAs($nonParticipant);

        $response = $this->postJson(
            "/api/messaging/conversations/{$this->conversation->id}/messages",
            ['body' => 'Should fail']
        );

        $response->assertForbidden();
    });

    it('prevents sending empty message', function () {
        $response = $this->postJson(
            "/api/messaging/conversations/{$this->conversation->id}/messages",
            ['body' => '']
        );

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('body');
    });

    it('prevents message exceeding max length', function () {
        $longBody = str_repeat('a', 5001);

        $response = $this->postJson(
            "/api/messaging/conversations/{$this->conversation->id}/messages",
            ['body' => $longBody]
        );

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('body');
    });

    it('enforces multi-tenant isolation', function () {
        $otherOrg = Organization::factory()->create();
        $otherConversation = Conversation::factory()->for($otherOrg)->create();

        $response = $this->postJson(
            "/api/messaging/conversations/{$otherConversation->id}/messages",
            ['body' => 'Attempt cross-org']
        );

        $response->assertForbidden();
    });
});
