<?php

use App\Models\Organizations;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organizations::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
});

it('fetches the investor dashboard metrics', function () {
    $response = $this->actingAs($this->user)
        ->withoutMiddleware()
        ->getJson('/api/investor/dashboard');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data' => [
            'summary' => [
                'headcount',
                'active_scenarios',
                'org_readiness',
                'talent_roi_usd',
                'critical_gap_rate',
                'ai_augmentation_index',
            ],
            'charts' => [
                'skill_levels',
                'department_readiness',
            ],
            'forecast' => [
                'next_quarter_readiness',
                'projected_savings_usd',
            ],
            'timestamp',
        ],
    ]);

    expect($response->json('success'))->toBeTrue();
});
