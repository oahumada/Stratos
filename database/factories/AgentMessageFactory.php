<?php

namespace Database\Factories;

use App\Models\AgentMessage;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AgentMessage>
 */
class AgentMessageFactory extends Factory
{
    protected $model = AgentMessage::class;

    public function definition(): array
    {
        return [
            'execution_id' => (string) Str::uuid(),
            'task_id' => 'task_'.$this->faker->numberBetween(1, 100),
            'channel' => $this->faker->randomElement(['task.dispatched', 'task.started', 'task.result']),
            'agent_name' => $this->faker->randomElement(['Planificador Cognitivo', 'Árbitro de Agentes']),
            'payload' => ['description' => $this->faker->sentence()],
            'result' => null,
            'status' => 'queued',
            'organization_id' => Organization::factory(),
            'attempts' => 0,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => 'completed',
            'result' => ['output' => 'Task completed successfully'],
            'attempts' => 1,
        ]);
    }
}
