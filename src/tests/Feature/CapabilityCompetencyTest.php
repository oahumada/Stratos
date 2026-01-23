<?php

namespace Tests\Feature;

use App\Models\Organizations;
use App\Models\User;
use App\Models\Scenario;
use App\Models\Capability;
use App\Models\Competency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CapabilityCompetencyTest extends TestCase
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

    public function test_attach_existing_competency_creates_pivot()
    {
        $scenario = Scenario::create(['organization_id' => $this->organization->id, 'name' => 'S1', 'horizon_months' => 6, 'fiscal_year' => 2026, 'created_by' => $this->user->id]);
        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap A']);
        $competency = Competency::create(['organization_id' => $this->organization->id, 'capability_id' => $cap->id, 'name' => 'Existing Comp']);

        $response = $this->actingAs($this->user)
            ->postJson("//api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies", [
                'competency_id' => $competency->id,
                'required_level' => 4,
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('capability_competencies', [
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'competency_id' => $competency->id,
            'required_level' => 4,
        ]);
    }

    public function test_create_new_competency_and_pivot_in_transaction()
    {
        $scenario = Scenario::create(['organization_id' => $this->organization->id, 'name' => 'S2', 'horizon_months' => 6, 'fiscal_year' => 2026, 'created_by' => $this->user->id]);
        $cap = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap B']);

        $payload = [
            'competency' => ['name' => 'New Comp', 'description' => 'Created via API'],
            'required_level' => 2,
        ];

        $response = $this->actingAs($this->user)
            ->postJson("//api/strategic-planning/scenarios/{$scenario->id}/capabilities/{$cap->id}/competencies", $payload);

        $response->assertStatus(201)->assertJson(['success' => true]);

        $this->assertDatabaseHas('competencies', [
            'organization_id' => $this->organization->id,
            'name' => 'New Comp',
        ]);

        $comp = Competency::where('name', 'New Comp')->where('organization_id', $this->organization->id)->first();
        $this->assertNotNull($comp);

        $this->assertDatabaseHas('capability_competencies', [
            'scenario_id' => $scenario->id,
            'capability_id' => $cap->id,
            'competency_id' => $comp->id,
            'required_level' => 2,
        ]);
    }
}
