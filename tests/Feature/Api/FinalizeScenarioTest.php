<?php

use App\Models\Organization;
use App\Models\Scenario;
use App\Models\User;

beforeEach(function () {
    $org = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $org->id]);
    $this->actingAs($this->user);

    $this->scenario = Scenario::factory()->create([
        'organization_id' => $org->id,
        'owner_user_id' => $this->user->id,
        'created_by' => $this->user->id,
        'horizon_months' => 6,
        'fiscal_year' => 2026,
    ]);
});

it('finalizes a scenario and sets decision_status to approved', function () {
    $res = $this->postJson("/api/strategic-planning/scenarios/{$this->scenario->id}/finalize");

    $res->assertStatus(200)->assertJson(['success' => true]);

    $this->assertDatabaseHas('scenarios', [
        'id' => $this->scenario->id,
        'decision_status' => 'approved',
    ]);
});
