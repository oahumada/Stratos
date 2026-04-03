<?php

namespace Database\Factories;

use App\Models\ChangeSet;
use App\Models\Organization;
use App\Models\Scenario;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChangeSetFactory extends Factory
{
    protected $model = ChangeSet::class;

    public function definition(): array
    {
        return [
            'scenario_id' => Scenario::factory(),
            'organization_id' => Organization::factory(),
            'title' => 'ChangeSet '.uniqid(),
            'description' => 'Test changeset for planning',
            'status' => 'draft',
            'diff' => ['ops' => []],
            'metadata' => [],
            'created_by' => null,
            'approved_by' => null,
        ];
    }
}
