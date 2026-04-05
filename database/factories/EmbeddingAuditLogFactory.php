<?php

namespace Database\Factories;

use App\Models\EmbeddingAuditLog;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmbeddingAuditLog>
 */
class EmbeddingAuditLogFactory extends Factory
{
    protected $model = EmbeddingAuditLog::class;

    public function definition(): array
    {
        $actions = ['created', 'updated', 'deleted', 'flagged', 'unflagged'];
        $triggers = ['system', 'reindex_job', 'user', 'feedback_loop'];

        return [
            'organization_id' => Organization::factory(),
            'embedding_id' => null,
            'action' => $this->faker->randomElement($actions),
            'changes' => ['field' => 'metadata', 'old' => null, 'new' => ['flagged' => true]],
            'triggered_by' => $this->faker->randomElement($triggers),
        ];
    }
}
