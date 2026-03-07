<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;

    protected User $admin;

    protected User $collaborator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->org = Organization::create([
            'name' => 'Stratos Corp',
            'subdomain' => 'stratos',
        ]);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@stratos.com',
            'password' => bcrypt('password'),
            'organization_id' => $this->org->id,
            'role' => 'admin',
        ]);

        $this->collaborator = User::create([
            'name' => 'John Doe',
            'email' => 'john@stratos.com',
            'password' => bcrypt('password'),
            'organization_id' => $this->org->id,
            'role' => 'collaborator',
        ]);
    }

    public function test_admin_can_access_investor_dashboard()
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/investor/dashboard');

        $response->assertStatus(200);
    }

    public function test_collaborator_is_forbidden_from_investor_dashboard()
    {
        $response = $this->actingAs($this->collaborator, 'sanctum')
            ->getJson('/api/investor/dashboard');

        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'No tienes permiso para acceder a este recurso.']);
    }

    public function test_unauthenticated_user_is_unauthorized()
    {
        $response = $this->getJson('/api/investor/dashboard');

        $response->assertStatus(401);
    }

    public function test_multi_tenancy_on_support_tickets()
    {
        $org2 = Organization::create([
            'name' => 'Other Corp',
            'subdomain' => 'other',
        ]);
        $userFromOrg2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@other.com',
            'password' => bcrypt('password'),
            'organization_id' => $org2->id,
            'role' => 'collaborator',
        ]);

        // Create a ticket in Org 1
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/support-tickets', [
                'title' => 'Bug in Org 1',
                'description' => 'Help!',
                'type' => 'bug',
                'priority' => 'high',
            ]);

        // User from Org 2 should NOT see it
        $response = $this->actingAs($userFromOrg2, 'sanctum')
            ->getJson('/api/support-tickets');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');

        // User from Org 1 SHOULD see it
        $response = $this->actingAs($this->collaborator, 'sanctum')
            ->getJson('/api/support-tickets');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
