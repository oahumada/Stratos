<?php

namespace Tests\Feature\Api;

use App\Models\DeviceToken;
use App\Models\MobileApproval;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);

class MobileControllerTest extends TestCase
{
    protected Organization $organization;

    protected User $user;

    protected User $manager;

    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();
        $this->user = User::factory()->for($this->organization)->create([
            'role' => 'user',
        ]);
        $this->manager = User::factory()->for($this->organization)->create([
            'role' => 'manager',
        ]);

        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    /**
     * Test: Register device token (FCM Android)
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function register_android_device(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/mobile/register-device', [
            'token' => 'dXN4UF8yeUZTaTpBUDkxd0VPYzUyN3o4ajVVM1IxdXNxWjEzQ0wtMDZpeDhFdUpBODVYbzJnYWJQUXFURHcwRjk=',
            'platform' => 'android',
            'metadata' => [
                'app_version' => '1.0.0',
                'os_version' => '14.0',
            ],
        ]);

        $response->assertSuccessful()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.platform', 'android');

        $this->assertDatabaseHas('device_tokens', [
            'user_id' => $this->user->id,
            'platform' => 'android',
            'is_active' => true,
        ]);
    }

    /**
     * Test: Register device token (APNs iOS)
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function register_ios_device(): void
    {
        $iosToken = 'a'.str_repeat('b', 63); // 64 char hex token

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/mobile/register-device', [
            'token' => $iosToken,
            'platform' => 'ios',
            'metadata' => [
                'app_version' => '1.0.0',
                'device_model' => 'iPhone 13',
            ],
        ]);

        $response->assertSuccessful()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.platform', 'ios');

        $this->assertDatabaseHas('device_tokens', [
            'user_id' => $this->user->id,
            'platform' => 'ios',
        ]);
    }

    /**
     * Test: Register device with invalid token format
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function register_device_invalid_token(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/mobile/register-device', [
            'token' => 'invalid_short_token',
            'platform' => 'android',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    /**
     * Test: Get user's active devices
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function get_active_devices(): void
    {
        // Create 2 devices
        DeviceToken::factory(2)->for($this->user)->for($this->organization)->create(['is_active' => true]);

        // Create 1 inactive device
        DeviceToken::factory()->for($this->user)->for($this->organization)->create(['is_active' => false]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/mobile/devices');

        $response->assertSuccessful()
            ->assertJsonPath('count', 2)
            ->assertJsonPath('success', true);
    }

    /**
     * Test: Deactivate device token
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function deactivate_device(): void
    {
        $device = DeviceToken::factory()->for($this->user)->for($this->organization)->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->deleteJson("/api/mobile/devices/{$device->id}");

        $response->assertSuccessful()
            ->assertJsonPath('success', true);

        $this->assertFalse($device->fresh()->is_active);
    }

    /**
     * Test: Create approval request
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function create_approval_request(): void
    {
        $approval = MobileApproval::factory()
            ->for($this->organization)
            ->for($this->manager, 'user')
            ->for($this->user, 'requester')
            ->create(['status' => 'pending']);

        $this->assertDatabaseHas('mobile_approvals', [
            'id' => $approval->id,
            'user_id' => $this->manager->id,
            'requester_id' => $this->user->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Test: Get pending approvals for user
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function get_pending_approvals(): void
    {
        // Create 3 pending approvals for manager
        MobileApproval::factory(3)
            ->for($this->organization)
            ->for($this->manager, 'user')
            ->for($this->user, 'requester')
            ->create(['status' => 'pending']);

        // Create 1 approved (should not be returned)
        MobileApproval::factory()
            ->for($this->organization)
            ->for($this->manager, 'user')
            ->for($this->user, 'requester')
            ->create(['status' => 'approved']);

        $token = $this->manager->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->getJson('/api/mobile/approvals');

        $response->assertSuccessful()
            ->assertJsonPath('count', 3);
    }

    /**
     * Test: Approve request
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function approve_request(): void
    {
        $approval = MobileApproval::factory()
            ->for($this->organization)
            ->for($this->manager, 'user')
            ->for($this->user, 'requester')
            ->create(['status' => 'pending']);

        $token = $this->manager->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->postJson("/api/mobile/approvals/{$approval->id}/approve", [
            'reason' => 'Looks good to proceed',
        ]);

        $response->assertSuccessful()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'approved');

        $this->assertDatabaseHas('mobile_approvals', [
            'id' => $approval->id,
            'status' => 'approved',
        ]);
    }

    /**
     * Test: Reject request
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function reject_request(): void
    {
        $approval = MobileApproval::factory()
            ->for($this->organization)
            ->for($this->manager, 'user')
            ->for($this->user, 'requester')
            ->create(['status' => 'pending']);

        $token = $this->manager->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->postJson("/api/mobile/approvals/{$approval->id}/reject", [
            'reason' => 'Need more information',
        ]);

        $response->assertSuccessful()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'rejected');

        $this->assertDatabaseHas('mobile_approvals', [
            'id' => $approval->id,
            'status' => 'rejected',
        ]);
    }

    /**
     * Test: Cannot approve expired request
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_approve_expired_request(): void
    {
        $approval = MobileApproval::factory()
            ->for($this->organization)
            ->for($this->manager, 'user')
            ->for($this->user, 'requester')
            ->create([
                'status' => 'pending',
                'expires_at' => now()->subDay(),
            ]);

        $token = $this->manager->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->postJson("/api/mobile/approvals/{$approval->id}/approve", [
            'reason' => 'Too late',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    /**
     * Test: Get approval history
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function get_approval_history(): void
    {
        // Create approvals with different statuses
        MobileApproval::factory(5)->for($this->organization)->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/mobile/approvals/history?per_page=10');

        $response->assertSuccessful()
            ->assertJsonPath('success', true)
            ->assertJsonPath('pagination.per_page', 10);
    }

    /**
     * Test: Cannot access other user's approval
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_access_other_users_approval(): void
    {
        $otherUser = User::factory()->for($this->organization)->create();

        $approval = MobileApproval::factory()
            ->for($this->organization)
            ->for($otherUser, 'user')
            ->for($this->user, 'requester')
            ->create(['status' => 'pending']);

        // Try to approve with wrong user
        $token = $this->manager->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->postJson("/api/mobile/approvals/{$approval->id}/approve", [
            'reason' => 'Not my approval',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test: Sync offline queue
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function sync_offline_queue(): void
    {
        // Queue some requests
        \App\Models\OfflineQueue::factory(2)
            ->for($this->user)
            ->for($this->organization)
            ->create(['status' => 'pending']);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/mobile/offline-queue/sync');

        $response->assertSuccessful()
            ->assertJsonPath('success', true);
    }

    /**
     * Test: Get queue status
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function get_queue_status(): void
    {
        \App\Models\OfflineQueue::factory()
            ->for($this->user)
            ->for($this->organization)
            ->create(['status' => 'pending']);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/mobile/offline-queue/status');

        $response->assertSuccessful()
            ->assertJsonPath('data.pending', 1);
    }

    /**
     * Test: Multi-tenant isolation - user cannot see other org's data
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function multi_tenant_isolation(): void
    {
        $otherOrg = Organization::factory()->create();
        $otherUser = User::factory()->for($otherOrg)->create();

        // Create approval in other organization
        MobileApproval::factory()
            ->for($otherOrg)
            ->for($otherUser, 'user')
            ->create(['status' => 'pending']);

        // Try to access from first org
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/mobile/approvals');

        $response->assertSuccessful()
            ->assertJsonPath('count', 0);
    }

    /**
     * Test: Unauthenticated request is rejected
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_request_rejected(): void
    {
        $response = $this->getJson('/api/mobile/devices');

        $response->assertUnauthorized();
    }

    /**
     * Test: Device stats endpoint (admin only)
     */
    #[\PHPUnit\Framework\Attributes\Test]
    public function device_stats_admin_only(): void
    {
        // Regular user should be denied
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/mobile/stats/devices');

        $response->assertStatus(403);

        // Manager should have access
        $token = $this->manager->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->getJson('/api/mobile/stats/devices');

        $response->assertSuccessful()
            ->assertJsonPath('success', true);
    }
}
