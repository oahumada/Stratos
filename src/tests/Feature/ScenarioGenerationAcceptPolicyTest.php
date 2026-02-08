<?php

namespace Tests\Feature;

use App\Models\ScenarioGeneration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScenarioGenerationAcceptPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_accept_forbidden_for_user_from_different_organization()
    {
        $org1 = \App\Models\Organizations::factory()->create();
        $org2 = \App\Models\Organizations::factory()->create();

        $user = User::factory()->create(['organization_id' => $org1->id]);
        $this->actingAs($user);

        $generation = ScenarioGeneration::create([
            'organization_id' => $org2->id,
            'created_by' => $user->id,
            'prompt' => 'p',
            'llm_response' => ['scenario_metadata' => ['name' => 'X']],
            'status' => 'complete',
        ]);

        $res = $this->postJson("/api/strategic-planning/scenarios/generate/{$generation->id}/accept");
        $res->assertStatus(403);
    }

    public function test_accept_forbidden_for_non_creator_and_non_privileged_user()
    {
        $org = \App\Models\Organizations::factory()->create();
        $creator = User::factory()->create(['organization_id' => $org->id]);
        $other = User::factory()->create(['organization_id' => $org->id]);

        $generation = ScenarioGeneration::create([
            'organization_id' => $org->id,
            'created_by' => $creator->id,
            'prompt' => 'p',
            'llm_response' => ['scenario_metadata' => ['name' => 'Y']],
            'status' => 'complete',
        ]);

        $this->actingAs($other);
        // Sanity check: Gate should disallow accept for this user
        $this->assertFalse(\Gate::forUser($other)->allows('accept', $generation));
        // Direct policy invocation should also be false
        $policy = new \App\Policies\ScenarioGenerationPolicy();
        $this->assertFalse($policy->accept($other, $generation));

        $res = $this->postJson("/api/strategic-planning/scenarios/generate/{$generation->id}/accept");
        $res->assertStatus(403);
    }

    public function test_accept_allowed_for_admin_role()
    {
        $org = \App\Models\Organizations::factory()->create();
        $admin = User::factory()->create(['organization_id' => $org->id, 'role' => 'admin']);

        $generation = ScenarioGeneration::create([
            'organization_id' => $org->id,
            'created_by' => $admin->id,
            'prompt' => 'p',
            'llm_response' => ['scenario_metadata' => ['name' => 'ACME Admin']],
            'status' => 'complete',
        ]);

        $this->actingAs($admin);
        $res = $this->postJson("/api/strategic-planning/scenarios/generate/{$generation->id}/accept");
        $res->assertStatus(201);
    }
}
