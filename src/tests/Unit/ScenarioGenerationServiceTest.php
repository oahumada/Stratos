<?php

use App\Services\ScenarioGenerationService;
use App\Models\Organizations;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('builds prompt including operator inputs and organization data', function () {
    $org = Organizations::create(['name' => 'ACME Corp', 'subdomain' => 'acme', 'industry' => 'tech', 'size' => 'small']);
    $user = User::factory()->create(['organization_id' => $org->id]);

    $svc = new ScenarioGenerationService();

    $data = [
        'company_name' => 'TestCo',
        'current_challenges' => 'low sales',
    ];

    $prompt = $svc->preparePrompt($data, $user, $org);

    expect($prompt)->toContain('OPERATOR_INPUT');
    expect($prompt)->toContain('low sales');
    expect($prompt)->toContain('ACME Corp');
});
