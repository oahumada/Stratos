<?php

namespace Tests\Feature;

use App\Models\Organizations;
use App\Models\User;
use App\Models\Scenario;
use App\Models\Capability;
use App\Models\Competency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetencyCreateAndAttachTest extends TestCase
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

    public function test_create_competency_via_competencies_endpoint_and_attach_to_capability()
    {
        $scenario = Scenario::create([
            'organization_id' => $this->organization->id,
            'name' => 'Scenario Attach',
            'horizon_months' => 6,
            'fiscal_year' => 2026,
            'created_by' => $this->user->id,
        ]);

        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap For Attach']);

        // Create and attach competency in a single call via scenario-scoped endpoint
        // This is the new N:N model: competencies are not tied to a specific capability
        $payload = [
            'competency' => [
                'name' => 'API Created Comp',
                'description' => 'Created from test',
            ],
            'required_level' => 4,
            'weight' => 1,
        ];

        $createResp = $this->actingAs($this->user)
            ->postJson("/api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies", $payload);

        $createResp->assertStatus(201)->assertJson(['success' => true]);

        // verify competency created in DB
        $this->assertDatabaseHas('competencies', [
            'organization_id' => $this->organization->id,
            'name' => 'API Created Comp',
        ]);

        $comp = Competency::where('name', 'API Created Comp')->where('organization_id', $this->organization->id)->first();
        $this->assertNotNull($comp);

        // verify competency is attached to capability via pivot
        $this->assertDatabaseHas('capability_competencies', [
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'competency_id' => $comp->id,
            'required_level' => 4,
        ]);
    }
}
