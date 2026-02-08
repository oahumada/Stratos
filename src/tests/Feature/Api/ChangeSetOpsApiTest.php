<?php

namespace Tests\Feature\Api;

use App\Models\Organizations;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChangeSetOpsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_op_and_apply_creates_role_sunset_mapping_via_api()
    {
        $org = Organizations::create(['name' => 'API Org', 'subdomain' => 'api-'.uniqid()]);
        $user = User::factory()->create(['organization_id' => $org->id]);

        $role = Roles::create([
            'organization_id' => $org->id,
            'name' => 'API Role '.uniqid(),
            'level' => 'mid',
        ]);

        $this->actingAs($user, 'sanctum');

        $scenario = Scenario::create([
            'organization_id' => $org->id,
            'name' => 'Test Scenario',
            'horizon_months' => 6,
            'fiscal_year' => now()->year,
            'created_by' => $user->id,
            'owner_user_id' => $user->id,
        ]);

        $res = $this->postJson("/api/strategic-planning/scenarios/{$scenario->id}/change-sets", [
            'title' => 'API ChangeSet',
            'description' => 'test',
        ]);
        $res->assertStatus(201);
        $csId = $res->json('data.id');

        $op = [
            'type' => 'create_role_sunset_mapping',
            'payload' => [
                'scenario_id' => null,
                'role_id' => $role->id,
                'mapped_role_id' => null,
                'sunset_reason' => 'api-test',
                'metadata' => ['via' => 'api'],
            ],
        ];

        $add = $this->postJson("/api/strategic-planning/change-sets/{$csId}/ops", $op);
        $add->assertStatus(200)->assertJson(['success' => true]);

        $apply = $this->postJson("/api/strategic-planning/change-sets/{$csId}/apply");
        $apply->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('role_sunset_mappings', [
            'role_id' => $role->id,
            'sunset_reason' => 'api-test',
        ]);
    }
}
