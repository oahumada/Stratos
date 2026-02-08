<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\WorkforcePlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WorkforcePlanApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['organization_id' => 1]);
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function it_can_create_a_workforce_plan()
    {
        $data = [
            'name' => 'Plan Q1 2026',
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addMonths(6)->toDateString(),
            'planning_horizon_months' => 12,
            'scope_type' => 'organization_wide',
            'owner_user_id' => $this->user->id,
        ];

        $response = $this->postJson('//api/workforce-planning/workforce-plans', $data);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'code', 'status']);
    }

    /** @test */
    public function it_can_list_workforce_plans()
    {
        WorkforcePlan::factory(3)->create(['organization_id' => 1]);

        $response = $this->getJson('//api/workforce-planning/workforce-plans');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    /** @test */
    public function it_can_show_a_workforce_plan()
    {
        $plan = WorkforcePlan::factory()->create(['organization_id' => 1]);

        $response = $this->getJson("//api/workforce-planning/workforce-plans/{$plan->id}");

        $response->assertStatus(200)
            ->assertJsonPath('plan.id', $plan->id);
    }

    /** @test */
    public function only_owner_can_delete_draft()
    {
        $plan = WorkforcePlan::factory()->create([
            'organization_id' => 1,
            'owner_user_id' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->deleteJson("//api/workforce-planning/workforce-plans/{$plan->id}");

        $response->assertStatus(200);
        $this->assertNull(WorkforcePlan::find($plan->id));
    }
}
