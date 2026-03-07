<?php

namespace Database\Factories;

use App\Models\LLMEvaluation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LLMEvaluation>
 */
class LLMEvaluationFactory extends Factory
{
    protected $model = LLMEvaluation::class;

    public function definition(): array
    {
        $providers = ['deepseek', 'abacus', 'openai', 'intel', 'mock'];

        return [
            'organization_id' => Organization::factory(),
            'evaluable_type' => 'ScenarioGeneration',
            'evaluable_id' => $this->faker->numberBetween(1, 100),
            'llm_provider' => $this->faker->randomElement($providers),
            'llm_model' => $this->faker->word(),
            'llm_version' => $this->faker->semver(),
            'input_content' => $this->faker->paragraph(5),
            'output_content' => $this->faker->paragraph(10),
            'context_content' => $this->faker->paragraph(3),
            'faithfulness_score' => $this->faker->randomFloat(2, 0.70, 1.00),
            'relevance_score' => $this->faker->randomFloat(2, 0.70, 1.00),
            'context_alignment_score' => $this->faker->randomFloat(2, 0.70, 1.00),
            'coherence_score' => $this->faker->randomFloat(2, 0.70, 1.00),
            'hallucination_rate' => $this->faker->randomFloat(2, 0.00, 0.30),
            'composite_score' => $this->faker->randomFloat(2, 0.70, 0.95),
            'quality_level' => $this->faker->randomElement(['excellent', 'good', 'acceptable', 'poor', 'critical']),
            'normalized_score' => $this->faker->randomFloat(2, 0.70, 1.00),
            'metric_details' => [],
            'issues_detected' => [],
            'recommendations' => [],
            'status' => 'completed',
            'created_by' => User::factory(),
            'evaluation_config' => config('ragas'),
            'processing_ms' => $this->faker->numberBetween(100, 5000),
            'tokens_used' => $this->faker->numberBetween(100, 2000),
            'is_latest' => true,
            'started_at' => now()->subMinutes(5),
            'completed_at' => now(),
        ];
    }

    /**
     * Mark evaluation as pending (not yet processed)
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'faithfulness_score' => null,
            'relevance_score' => null,
            'context_alignment_score' => null,
            'coherence_score' => null,
            'hallucination_rate' => null,
            'composite_score' => null,
            'quality_level' => null,
            'normalized_score' => null,
            'started_at' => null,
            'completed_at' => null,
            'processing_ms' => null,
        ]);
    }

    /**
     * Mark evaluation as evaluating (in progress)
     */
    public function evaluating(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'evaluating',
            'started_at' => now(),
            'completed_at' => null,
        ]);
    }

    /**
     * Mark evaluation as failed
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'error_message' => 'RAGAS service timeout',
            'retry_count' => 3,
            'faithfulness_score' => null,
            'composite_score' => null,
            'completed_at' => now(),
        ]);
    }

    /**
     * Set specific provider
     */
    public function provider(string $provider): static
    {
        return $this->state(fn (array $attributes) => [
            'llm_provider' => $provider,
        ]);
    }

    /**
     * Set quality level to excellent
     */
    public function excellent(): static
    {
        return $this->state(fn (array $attributes) => [
            'quality_level' => 'excellent',
            'composite_score' => 0.92,
            'faithfulness_score' => 0.93,
            'relevance_score' => 0.91,
            'context_alignment_score' => 0.93,
            'coherence_score' => 0.90,
            'hallucination_rate' => 0.05,
        ]);
    }

    /**
     * Set quality level to good
     */
    public function good(): static
    {
        return $this->state(fn (array $attributes) => [
            'quality_level' => 'good',
            'composite_score' => 0.85,
            'faithfulness_score' => 0.86,
            'relevance_score' => 0.84,
            'context_alignment_score' => 0.85,
            'coherence_score' => 0.82,
            'hallucination_rate' => 0.08,
        ]);
    }

    /**
     * Set quality level to acceptable
     */
    public function acceptable(): static
    {
        return $this->state(fn (array $attributes) => [
            'quality_level' => 'acceptable',
            'composite_score' => 0.75,
            'faithfulness_score' => 0.75,
            'relevance_score' => 0.73,
            'context_alignment_score' => 0.76,
            'coherence_score' => 0.70,
            'hallucination_rate' => 0.12,
        ]);
    }

    /**
     * Set quality level to poor
     */
    public function poor(): static
    {
        return $this->state(fn (array $attributes) => [
            'quality_level' => 'poor',
            'composite_score' => 0.60,
            'faithfulness_score' => 0.59,
            'relevance_score' => 0.58,
            'context_alignment_score' => 0.61,
            'coherence_score' => 0.55,
            'hallucination_rate' => 0.20,
        ]);
    }

    /**
     * Set quality level to critical
     */
    public function critical(): static
    {
        return $this->state(fn (array $attributes) => [
            'quality_level' => 'critical',
            'composite_score' => 0.35,
            'faithfulness_score' => 0.30,
            'relevance_score' => 0.32,
            'context_alignment_score' => 0.35,
            'coherence_score' => 0.25,
            'hallucination_rate' => 0.35,
        ]);
    }
}
