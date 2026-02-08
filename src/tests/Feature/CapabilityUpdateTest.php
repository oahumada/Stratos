<?php

namespace Tests\Feature;

use App\Models\Capability;
use App\Models\Organizations;
use App\Models\Scenario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CapabilityUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected Organizations $organization;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organization = Organizations::factory()->create();
        $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
    }

    public function test_update_capability_entity_via_api()
    {
        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Old Name', 'description' => 'Old desc']);

        $payload = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)
            ->patchJson("/api/capabilities/{$cap->id}", $payload);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('capabilities', [
            'id' => $cap->id,
            'organization_id' => $this->organization->id,
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ]);
    }

    public function test_update_scenario_capability_pivot_via_api()
    {
        $scenario = Scenario::create([
            'organization_id' => $this->organization->id,
            'name' => 'S-pivot',
            'horizon_months' => 6,
            'fiscal_year' => 2026,
            'created_by' => $this->user->id,
        ]);

        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Pivot Cap']);

        // Note: the PATCH endpoint supports upsert behavior and will create the pivot if missing.

        $pivotPayload = [
            'strategic_weight' => 7,
            'priority' => 'high',
            'rationale' => 'Updated rationale via test',
            'required_level' => 3,
            'is_critical' => 1,
        ];

        $response = $this->actingAs($this->user)
            ->patchJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}", $pivotPayload);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('scenario_capabilities', [
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'strategic_weight' => 7,
            'priority' => 'high',
            'required_level' => 3,
            'is_critical' => 1,
        ]);
    }

    public function test_importance_reflected_in_capability_tree()
    {
        $scenario = Scenario::create(['organization_id' => $this->organization->id, 'name' => 'S-tree', 'horizon_months' => 6, 'fiscal_year' => 2026, 'created_by' => $this->user->id]);
        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Tree Cap', 'importance' => 4]);

        // Create capability via API under the scenario so it appears in capability-tree
        $createRes = $this->actingAs($this->user)
            ->postJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities", [
                'name' => 'Tree Cap',
                'description' => 'Created via API',
                'importance' => 4,
                'strategic_weight' => 5,
                'priority' => 1,
            ])
            ->assertStatus(201)
            ->decodeResponseJson();

        $createdCap = $createRes['data'] ?? null;
        $capId = $createdCap['id'] ?? $cap->id;

        // Update importance on capability entity
        $this->actingAs($this->user)
            ->patchJson("/api/capabilities/{$capId}", ['importance' => 9])
            ->assertStatus(200)->assertJson(['success' => true]);

        // Fetch capability-tree for scenario and assert importance updated
        $res = $this->actingAs($this->user)
            ->getJson("/api/strategic-planning/scenarios/{$scenario->id}/capability-tree")
            ->assertStatus(200)
            ->json();

        $found = false;
        foreach ($res as $item) {
            if (isset($item['id']) && $item['id'] == $capId) {
                $found = true;
                $this->assertEquals(9, $item['importance']);
                break;
            }
        }
        $this->assertTrue($found, 'Capability not present in capability-tree');
    }

    public function test_pivot_reflected_in_capability_tree()
    {
        $scenario = Scenario::create(['organization_id' => $this->organization->id, 'name' => 'S-pivot-tree', 'horizon_months' => 6, 'fiscal_year' => 2026, 'created_by' => $this->user->id]);
        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Pivot Tree Cap']);

        // Ensure pivot exists via PATCH upsert
        $this->actingAs($this->user)
            ->patchJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}", [
                'strategic_weight' => 8,
                'priority' => 2,
                'rationale' => 'Pivot test',
                'required_level' => 4,
                'is_critical' => 0,
            ])->assertStatus(200)->assertJson(['success' => true]);

        // Fetch capability-tree for scenario and assert pivot values present
        $res = $this->actingAs($this->user)
            ->getJson("/api/strategic-planning/scenarios/{$scenario->id}/capability-tree")
            ->assertStatus(200)
            ->json();

        $found = false;
        foreach ($res as $item) {
            if (isset($item['id']) && $item['id'] == $cap->id) {
                $found = true;
                $this->assertEquals(8, $item['strategic_weight']);
                $this->assertEquals(2, $item['priority']);
                break;
            }
        }
        $this->assertTrue($found, 'Capability not present in capability-tree');
    }
}
