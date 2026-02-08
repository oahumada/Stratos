<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Organizations;
use App\Models\User;

uses(RefreshDatabase::class);

it('builds a prompt that includes operator input and organization data', function () {
    // instantiate models in memory to avoid DB dependency
    $org = new Organizations();
    $org->name = 'ACME Corp';
    $org->subdomain = 'acme';
    $org->industry = 'tech';
    $org->size = 'small';
    $org->id = 1;

    $user = new User();
    $user->organization_id = $org->id;

    $svc = new \App\Services\ScenarioGenerationService();

    $payload = ['company_name' => 'ACME Corp', 'strategic_goal' => 'expand into EU'];

    $prompt = $svc->preparePrompt($payload, $user, $org);

    expect($prompt)->toBeString();
    expect(strpos($prompt, 'OPERATOR_INPUT') !== false)->toBeTrue();
    expect(strpos($prompt, 'ACME Corp') !== false)->toBeTrue();
    expect(strpos($prompt, 'expand into EU') !== false)->toBeTrue();
});

it('encodes array fields as JSON and includes them in the prompt', function () {
    $org = new Organizations();
    $org->name = 'Beta LLC';
    $org->subdomain = 'beta';
    $org->industry = 'finance';
    $org->size = 'medium';
    $org->id = 2;

    $user = new User();
    $user->organization_id = $org->id;

    $svc = new \App\Services\ScenarioGenerationService();

    $payload = ['key_initiatives' => ['hire 10 devs', 'open EU office'], 'strategic_goal' => 'scale platform'];

    $prompt = $svc->preparePrompt($payload, $user, $org);

    expect($prompt)->toBeString();
    // the array should be JSON encoded somewhere in the prompt
    expect(strpos($prompt, '"hire 10 devs"') !== false)->toBeTrue();
    expect(strpos($prompt, '"open EU office"') !== false)->toBeTrue();
    expect(strpos($prompt, 'OPERATOR_INPUT') !== false)->toBeTrue();
});

it('uses organization name when available instead of payload company_name', function () {
    $org = new Organizations();
    $org->name = 'Gamma Industries';
    $org->subdomain = 'gamma';
    $org->industry = 'manufacturing';
    $org->size = 'large';
    $org->id = 3;

    $user = new User();
    $user->organization_id = $org->id;

    $svc = new \App\Services\ScenarioGenerationService();

    $payload = ['company_name' => 'ShouldNotAppear Inc', 'strategic_goal' => 'optimize supply chain'];

    $prompt = $svc->preparePrompt($payload, $user, $org);

    expect($prompt)->toBeString();
    expect(strpos($prompt, 'Gamma Industries') !== false)->toBeTrue();
    expect(strpos($prompt, 'ShouldNotAppear Inc') === false)->toBeTrue();
});
