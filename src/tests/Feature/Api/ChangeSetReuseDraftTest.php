<?php

namespace Tests\Feature\Api;

use App\Models\ChangeSet;
use App\Models\StrategicPlanningScenarios;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChangeSetReuseDraftTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_reuses_existing_draft_for_same_scenario_and_org()
    {
        // Arrange: create user, organization and a draft ChangeSet for scenario 1
        $user = User::factory()->create();
        // Ensure an organization exists and assign to user to satisfy FK constraints
        $org = \App\Models\Organizations::factory()->create();
        $user->organization_id = $org->id;
        $user->save();
        $this->actingAs($user);

        $orgId = $org->id;

        // Create a scenario for this organization and use its id
        $scenario = StrategicPlanningScenarios::factory()->create(['organization_id' => $orgId]);

        $existing = ChangeSet::create([
            'organization_id' => $orgId,
            'scenario_id' => $scenario->id,
            'title' => 'Existing Draft',
            'diff' => ['ops' => []],
            'status' => 'draft',
            'created_by' => $user->id,
        ]);

        // Act: call the store endpoint with empty payload
        $response = $this->postJson("/api/strategic-planning/scenarios/{$scenario->id}/change-sets", []);

        // Assert: response is successful and returns the existing ChangeSet id
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals($existing->id, $data['id']);

        // Also assert only one ChangeSet exists
        $this->assertDatabaseCount('change_sets', 1);
    }
}
