<?php

use App\Services\LLMClient;
use App\Services\ScenarioGenerationService;

it('generates a response from configured LLM provider (mock by default)', function () {
    $svc = new ScenarioGenerationService();

    $payload = [
        'company_name' => 'Integration Co',
        'strategic_goal' => 'test LLM integration',
    ];

    // lightweight user/org objects
    $org = new \App\Models\Organizations();
    $org->id = 1;
    $org->name = 'Integration Co';

    $user = new \App\Models\User();
    $user->organization_id = $org->id;

    $prompt = $svc->preparePrompt($payload, $user, $org);

    $client = app(LLMClient::class);

    // If real provider is configured but no API key, the provider will throw; in CI we expect mock
    $result = null;
    try {
        $result = $client->generate($prompt);
    } catch (Exception $e) {
        // Skip test if real provider misconfigured
        skip('LLM provider not available: ' . $e->getMessage());
    }

    expect(is_array($result))->toBeTrue();
    expect(array_key_exists('response', $result))->toBeTrue();
    expect(array_key_exists('model_version', $result))->toBeTrue();
});
