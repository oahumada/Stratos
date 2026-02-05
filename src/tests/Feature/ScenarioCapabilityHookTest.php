<?php

namespace Tests\Feature;

use App\Models\Organizations;
use App\Models\User;
use App\Models\Scenario;
use App\Models\Capability;
use App\Models\Competency;
use App\Models\ScenarioCapability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScenarioCapabilityHookTest extends TestCase
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

    public function test_creating_pivot_generates_competency_versions()
    {
        $scenario = Scenario::create([
            'organization_id' => $this->organization->id,
            'name' => 'Hook Scenario',
            'horizon_months' => 6,
            'fiscal_year' => now()->year,
            'created_by' => $this->user->id,
        ]);

        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap Hook']);
        $comp = Competency::create(['organization_id' => $this->organization->id, 'name' => 'Comp Hook']);
        $cap->competencies()->attach($comp->id, ['scenario_id' => $scenario->id]);

        // Create pivot — this should trigger the hook in ScenarioCapability::booted
        ScenarioCapability::create([
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'strategic_role' => 'target',
            'strategic_weight' => 10,
            'priority' => 1,
        ]);

        $this->assertDatabaseHas('competency_versions', [
            'competency_id' => $comp->id,
            'evolution_state' => 'new_embryo',
        ]);
    }
}
