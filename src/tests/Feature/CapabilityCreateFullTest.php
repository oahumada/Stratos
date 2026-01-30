<?php

namespace Tests\Feature;

use App\Models\Organizations;
use App\Models\User;
use App\Models\Scenario;
use App\Models\Capability;
use App\Models\CapabilityCompetency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CapabilityCreateFullTest extends TestCase
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

    public function test_create_capability_with_all_fields_and_pivot()
    {
        $scenario = Scenario::create([
            'organization_id' => $this->organization->id,
            'name' => 'Create Full S1',
            'horizon_months' => 6,
            'fiscal_year' => 2026,
            'created_by' => $this->user->id,
        ]);

        $payload = [
            'name' => 'Full Capability',
            'description' => 'Full description',
            'importance' => 2,
            'type' => 'technical',
            'category' => 'technical',
            // pivot attributes expected by the endpoint
            'strategic_role' => 'target',
            'strategic_weight' => 8,
            'priority' => 2,
            'required_level' => 4,
            'rationale' => 'Needed for roadmap',
            'is_critical' => true,
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities", $payload);

        $response->assertStatus(201);

        // Verify capability record
        $this->assertDatabaseHas('capabilities', [
            'organization_id' => $this->organization->id,
            'name' => 'Full Capability',
            'description' => 'Full description',
            'importance' => 2,
            'type' => 'technical',
            'category' => 'technical',
        ]);

        $cap = Capability::where('name', 'Full Capability')->where('organization_id', $this->organization->id)->first();
        $this->assertNotNull($cap);

        // Verify scenario pivot was created with provided pivot fields
        $this->assertDatabaseHas('scenario_capabilities', [
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'strategic_weight' => 8,
            'priority' => 2,
            'required_level' => 4,
            'rationale' => 'Needed for roadmap',
            'is_critical' => 1,
        ]);
    }
}
