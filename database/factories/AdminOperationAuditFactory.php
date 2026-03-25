<?php

namespace Database\Factories;

use App\Models\AdminOperationAudit;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminOperationAuditFactory extends Factory
{
    protected $model = AdminOperationAudit::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'operation_type' => $this->faker->randomElement(['backfill', 'generate', 'import', 'cleanup', 'rebuild']),
            'operation_name' => $this->faker->sentence(2),
            'status' => 'pending',
            'parameters' => [],
            'dry_run_preview' => null,
            'result' => null,
            'error_message' => null,
            'records_processed' => 0,
            'records_affected' => 0,
            'duration_seconds' => null,
        ];
    }

    public function success(): self
    {
        return $this->state([
            'status' => 'success',
            'result' => ['message' => 'Operation completed successfully'],
            'duration_seconds' => $this->faker->numberBetween(1, 300),
            'records_affected' => $this->faker->numberBetween(1, 1000),
        ]);
    }

    public function failed(): self
    {
        return $this->state([
            'status' => 'failed',
            'error_message' => 'Operation failed',
            'duration_seconds' => $this->faker->numberBetween(1, 60),
        ]);
    }

    public function dryRun(): self
    {
        return $this->state([
            'status' => 'dry_run',
            'dry_run_preview' => [
                'estimated_records' => $this->faker->numberBetween(100, 5000),
                'affected_areas' => ['intelligence_aggregates'],
            ],
        ]);
    }
}
