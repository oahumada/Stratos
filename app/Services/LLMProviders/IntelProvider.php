<?php

namespace App\Services\LLMProviders;

use App\Services\Intelligence\StratosIntelService;
use Illuminate\Support\Arr;

class IntelProvider implements LLMProviderInterface
{
    protected StratosIntelService $intel;

    public function __construct(array $config = [])
    {
        $this->intel = app(StratosIntelService::class);
    }

    /**
     * Generate a response from the Python Intelligence service.
     */
    public function generate(string $prompt): array
    {
        // Extract basic data from prompt or assume defaults for scenario generation
        // Note: IntelProvider is primarily intended for Scenario Blueprint generation via Python.
        $companyName = 'Stratos Org'; 
        if (preg_match('/DESIGN A TALENT ENGINEERING BLUEPRINT for (.*?)\./', $prompt, $matches)) {
            $companyName = $matches[1];
        }

        $res = $this->intel->generateScenario($companyName, $prompt);

        if (!$res) {
            throw new \App\Services\LLMProviders\Exceptions\LLMServerException('Intel service failed to return a response.');
        }

        return [
            'response' => $res,
            'confidence' => (float)Arr::get($res, 'confidence_score', 0.8),
            'model_version' => 'intel-python-v1',
        ];
    }
}
