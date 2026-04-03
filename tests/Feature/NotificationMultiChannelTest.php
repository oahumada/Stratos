<?php

use App\Models\Organization;
use App\Models\User;
use App\Models\UserNotificationChannel;
use App\Services\Notifications\NotificationDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationMultiChannelTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationDispatcher $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dispatcher = app(NotificationDispatcher::class);
    }

    public function test_user_can_get_notification_preferences(): void
    {
        $org = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id]);

        UserNotificationChannel::create([
            'user_id' => $user->id,
            'organization_id' => $org->id,
            'channel_type' => 'email',
            'channel_config' => ['email' => $user->email],
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/notification-preferences');

        $response->assertOk();
        $response->assertJsonStructure(['preferences', 'available_channels']);
        $this->assertCount(1, $response['preferences']);
    }

    public function test_user_can_add_notification_channel(): void
    {
        $org = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id]);

        $response = $this->actingAs($user)
            ->postJson('/api/notification-preferences', [
                'channel_type' => 'telegram',
                'channel_config' => ['bot_token' => 'test_token', 'chat_id' => '123'],
                'is_active' => true,
            ]);

        $response->assertCreated();
        $this->assertTrue(UserNotificationChannel::where('user_id', $user->id)
            ->where('channel_type', 'telegram')
            ->exists());
    }

    public function test_user_can_toggle_channel(): void
    {
        $org = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id]);

        $channel = UserNotificationChannel::create([
            'user_id' => $user->id,
            'organization_id' => $org->id,
            'channel_type' => 'email',
            'channel_config' => [],
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/notification-preferences/email/toggle");

        $response->assertOk();
        $this->assertFalse($response['is_active']);
    }

    public function test_user_can_delete_notification_channel(): void
    {
        $org = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id]);

        UserNotificationChannel::create([
            'user_id' => $user->id,
            'organization_id' => $org->id,
            'channel_type' => 'slack',
            'channel_config' => [],
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->deleteJson('/api/notification-preferences/slack');

        $response->assertNoContent();
        $this->assertFalse(UserNotificationChannel::where('user_id', $user->id)
            ->where('channel_type', 'slack')
            ->exists());
    }

    public function test_dispatcher_sends_to_user_active_channels(): void
    {
        $org = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id]);

        UserNotificationChannel::create([
            'user_id' => $user->id,
            'organization_id' => $org->id,
            'channel_type' => 'email',
            'channel_config' => ['email' => $user->email],
            'is_active' => true,
        ]);

        $results = $this->dispatcher->dispatchToUser(
            $user,
            'Test Title',
            'Test message'
        );

        $this->assertArrayHasKey('email', $results);
    }

    public function test_user_defaults_to_email_if_no_preferences(): void
    {
        $org = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id]);

        $results = $this->dispatcher->dispatchToUser(
            $user,
            'Test Title',
            'Test message'
        );

        $this->assertArrayHasKey('email', $results);
    }

    public function test_notification_channel_model_relationships(): void
    {
        $org = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id]);

        $channel = UserNotificationChannel::create([
            'user_id' => $user->id,
            'organization_id' => $org->id,
            'channel_type' => 'telegram',
            'channel_config' => ['bot_token' => 'test', 'chat_id' => '123'],
            'is_active' => true,
        ]);

        $this->assertTrue($channel->user->is($user));
        $this->assertTrue($channel->organization->is($org));
    }
}
