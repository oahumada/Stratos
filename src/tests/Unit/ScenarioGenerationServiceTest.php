<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Organizations;
use App\Models\User;

uses(RefreshDatabase::class);

it('builds a prompt that includes operator input and organization data', function () {
    $org = Organizations::create(['name' => 'ACME Corp', 'subdomain' => 'acme', 'industry' => 'tech', 'size' => 'small']);
    $user = User::factory()->create(['organization_id' => $org->id]);

    $svc = new \App\Services\ScenarioGenerationService();

    $payload = ['company_name' => 'ACME Corp', 'strategic_goal' => 'expand into EU'];

    $prompt = $svc->preparePrompt($payload, $user, $org);

    expect($prompt)->toBeString();
    expect(strpos($prompt, 'OPERATOR_INPUT') !== false)->toBeTrue();
    expect(strpos($prompt, 'ACME Corp') !== false)->toBeTrue();
    expect(strpos($prompt, 'expand into EU') !== false)->toBeTrue();
});
