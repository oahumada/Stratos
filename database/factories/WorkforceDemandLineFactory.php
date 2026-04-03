<?php

namespace Database\Factories;

use App\Models\WorkforceDemandLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkforceDemandLine>
 */
class WorkforceDemandLineFactory extends Factory
{
    protected $model = WorkforceDemandLine::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $year = 2026;
        $month = str_pad((string) $this->faker->numberBetween(1, 12), 2, '0', STR_PAD_LEFT);

        return [
            'unit' => $this->faker->randomElement(['Sales', 'Operations', 'Finance', 'HR', 'Tech']),
            'role_name' => $this->faker->jobTitle(),
            'period' => "{$year}-{$month}",
            'volume_expected' => $this->faker->numberBetween(100, 5000),
            'time_standard_minutes' => $this->faker->randomElement([15, 30, 45, 60, 90, 120]),
            'productivity_factor' => round($this->faker->numberBetween(70, 120) / 100, 2),
            'coverage_target_pct' => round($this->faker->numberBetween(80, 100), 2),
            'attrition_pct' => round($this->faker->numberBetween(0, 20), 2),
            'ramp_factor' => round($this->faker->numberBetween(80, 100) / 100, 2),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
