<?php

namespace Tests\Feature\Integrations;

use App\Services\Intelligence\StratosIntelService;
use Tests\TestCase;

class DeepSeekLiveTest extends TestCase
{
    /**
     * @group live
     */
    public function test_it_gets_real_recommendation_from_deepseek()
    {
        $service = new StratosIntelService();
        
        // Skip if service is not reachable
        try {
            \Illuminate\Support\Facades\Http::get(config('services.python_intel.base_url') . '/');
        } catch (\Exception $e) {
            $this->markTestSkipped('Python Intelligence Service is not reachable at ' . config('services.python_intel.base_url'));
        }
        
        $gapData = [
            'role_context' => [
                'role_id' => 101,
                'role_name' => 'Data Scientist',
                'description' => 'Expert in machine learning and data analysis'
            ],
            'competency_context' => [
                'competency_name' => 'PyTorch Deep Learning',
                'required_level' => 4,
                'current_level' => 1,
                'gap_size' => 3
            ],
            'talent_context' => [
                'current_headcount' => 1,
                'talent_status' => 'Scarcity in market'
            ],
            'market_context' => null
        ];

        $result = $service->analyzeGap($gapData);

        expect($result)->toBeArray();
        expect($result)->toHaveKey('strategy');
        expect($result)->toHaveKey('confidence_score');
        expect($result)->toHaveKey('reasoning_summary');
        expect($result)->toHaveKey('action_plan');
        
        echo "\nDeepSeek Recommendation:\n";
        echo "Strategy: " . $result['strategy'] . "\n";
        echo "Confidence: " . $result['confidence_score'] . "\n";
        echo "Reasoning: " . $result['reasoning_summary'] . "\n";
    }
}
