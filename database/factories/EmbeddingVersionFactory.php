<?php

namespace Database\Factories;

use App\Models\EmbeddingVersion;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmbeddingVersion>
 */
class EmbeddingVersionFactory extends Factory
{
    protected $model = EmbeddingVersion::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'version_tag' => 'v'.$this->faker->semver(),
            'snapshot_count' => $this->faker->numberBetween(0, 500),
            'metadata' => [
                'trigger' => $this->faker->randomElement(['scheduled', 'manual', 'feedback_threshold']),
            ],
            'created_by' => $this->faker->randomElement(['system', 'reindex_job', 'manual']),
        ];
    }
}
