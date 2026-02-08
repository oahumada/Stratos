<?php

namespace Tests\Feature;

use App\Models\Scenario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScenarioAcceptedPromptPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepted_prompt_hidden_for_non_privileged_user()
    {
        $org = \App\Models\Organizations::factory()->create();
        $creator = User::factory()->create(['organization_id' => $org->id]);
        $other = User::factory()->create(['organization_id' => $org->id]);

        $scenario = Scenario::create([
            'organization_id' => $org->id,
            'created_by' => $creator->id,
            'name' => 'Test',
            'code' => 'SCN-T1',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(6)->toDateString(),
            'horizon_months' => 6,
            'fiscal_year' => now()->year,
            'scope_type' => 'organization_wide',
            'owner_user_id' => $creator->id,
            'accepted_prompt' => 'sensitive data',
            'accepted_prompt_metadata' => ['accepted_by' => $creator->id],
        ]);

        $this->actingAs($other);
        $res = $this->getJson("/api/strategic-planning/scenarios/{$scenario->id}");
        $res->assertStatus(200);
        $json = $res->json('data');
        $this->assertArrayNotHasKey('accepted_prompt', $json);
        $this->assertArrayNotHasKey('accepted_prompt_metadata', $json);
    }

    public function test_accepted_prompt_visible_for_admin()
    {
        $org = \App\Models\Organizations::factory()->create();
        $admin = User::factory()->create(['organization_id' => $org->id, 'role' => 'admin']);

        $scenario = Scenario::create([
            'organization_id' => $org->id,
            'created_by' => $admin->id,
            'name' => 'Test Admin',
            'code' => 'SCN-A1',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(6)->toDateString(),
            'horizon_months' => 6,
            'fiscal_year' => now()->year,
            'scope_type' => 'organization_wide',
            'owner_user_id' => $admin->id,
            'accepted_prompt' => 'sensitive data',
            'accepted_prompt_metadata' => ['accepted_by' => $admin->id],
        ]);

        $this->actingAs($admin);
        $res = $this->getJson("/api/strategic-planning/scenarios/{$scenario->id}");
        $res->assertStatus(200);
        $json = $res->json('data');
        $this->assertArrayHasKey('accepted_prompt', $json);
        $this->assertEquals('sensitive data', $json['accepted_prompt']);
    }
}
