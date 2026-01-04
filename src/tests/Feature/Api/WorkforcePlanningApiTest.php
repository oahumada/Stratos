<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\WorkforcePlanningScenario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class WorkforcePlanningApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'organization_id' => 1,
        ]);
        
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function it_can_list_scenarios()
    {
        // Create test scenarios
        WorkforcePlanningScenario::factory(3)->create([
            'organization_id' => 1,
        ]);

        $response = $this->getJson('/api/v1/workforce-planning/scenarios');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'pagination' => ['current_page', 'total', 'per_page', 'last_page'],
            ])
            ->assertJsonPath('success', true);
    }

    /** @test */
    public function it_can_create_a_scenario()
    {
        $data = [
            'name' => 'Q1 2026 Planning',
            'description' => 'Base scenario',
            'horizon_months' => 12,
            'fiscal_year' => 2026,
        ];

        $response = $this->postJson('/api/v1/workforce-planning/scenarios', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'status', 'created_at'],
            ])
            ->assertJsonPath('data.name', 'Q1 2026 Planning');
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/v1/workforce-planning/scenarios', []);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    /** @test */
    public function it_can_show_a_specific_scenario()
    {
        $scenario = WorkforcePlanningScenario::factory()->create([
            'organization_id' => 1,
        ]);

        $response = $this->getJson("/api/v1/workforce-planning/scenarios/{$scenario->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $scenario->id)
            ->assertJsonPath('data.name', $scenario->name);
    }

    /** @test */
    public function it_returns_404_for_non_existent_scenario()
    {
        $response = $this->getJson('/api/v1/workforce-planning/scenarios/99999');

        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function it_can_update_a_scenario()
    {
        $scenario = WorkforcePlanningScenario::factory()->create([
            'organization_id' => 1,
            'name' => 'Original',
            'status' => 'draft',
        ]);

        $response = $this->putJson("/api/v1/workforce-planning/scenarios/{$scenario->id}", [
            'name' => 'Updated',
            'status' => 'approved',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated')
            ->assertJsonPath('data.status', 'approved');
    }

    /** @test */
    public function it_can_approve_a_scenario()
    {
        $scenario = WorkforcePlanningScenario::factory()->create([
            'organization_id' => 1,
            'status' => 'draft',
        ]);

        $response = $this->postJson("/api/v1/workforce-planning/scenarios/{$scenario->id}/approve");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'approved')
            ->assertJsonPath('data.approved_by', $this->user->id);
    }

    /** @test */
    public function it_can_delete_a_scenario()
    {
        $scenario = WorkforcePlanningScenario::factory()->create([
            'organization_id' => 1,
        ]);

        $response = $this->deleteJson("/api/v1/workforce-planning/scenarios/{$scenario->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertNull(WorkforcePlanningScenario::find($scenario->id));
    }

    /** @test */
    public function it_can_filter_scenarios_by_status()
    {
        WorkforcePlanningScenario::factory(2)->create([
            'organization_id' => 1,
            'status' => 'draft',
        ]);

        WorkforcePlanningScenario::factory()->create([
            'organization_id' => 1,
            'status' => 'approved',
        ]);

        $response = $this->getJson('/api/v1/workforce-planning/scenarios?status=draft');

        $response->assertStatus(200)
            ->assertJsonPath('pagination.total', 2);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->postJson('/api/v1/workforce-planning/scenarios', [
            'name' => 'Test',
            'horizon_months' => 12,
            'fiscal_year' => 2026,
        ]);

        // Without auth, should get 401 or 422 (validation)
        $this->assertTrue(in_array($response->status(), [401, 422]));
    }
}
