<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IntelligenceMetricAggregate>
 */
class IntelligenceMetricAggregateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $metrics = ['rag', 'llm_call', 'agent'];
        $sources = ['evaluations', 'guide_faq', 'roles', null];
        $totalCount = rand(10, 500);
        $successCount = (int) ($totalCount * rand(85, 99) / 100);

        return [
            'organization_id' => null,
            'metric_type' => $metrics[array_rand($metrics)],
            'source_type' => $sources[array_rand($sources)],
            'date_key' => now()->subDays(rand(0, 30))->toDateString(),
            'total_count' => $totalCount,
            'success_count' => $successCount,
            'success_rate' => round($successCount / $totalCount, 4),
            'avg_duration_ms' => rand(100, 2000),
            'p50_duration_ms' => rand(100, 1500),
            'p95_duration_ms' => rand(500, 3000),
            'p99_duration_ms' => rand(1000, 5000),
            'avg_confidence' => $this->faker->randomFloat(4, 0.7, 1.0),
            'avg_context_count' => rand(1, 8),
            'metadata' => [],
        ];
    }
}
