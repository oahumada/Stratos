<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IntelligenceMetric>
 */
class IntelligenceMetricFactory extends Factory
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

        return [
            'organization_id' => null,
            'metric_type' => $metrics[array_rand($metrics)],
            'source_type' => $sources[array_rand($sources)],
            'context_count' => $this->faker->numberBetween(0, 10),
            'confidence' => $this->faker->randomFloat(4, 0.0, 1.0),
            'duration_ms' => $this->faker->numberBetween(50, 2000),
            'success' => rand(0, 100) > 10 ? true : false,
            'metadata' => [],
        ];
    }
}
