<?php

namespace App\Jobs;

use App\Services\AgentMessageBus;
use App\Services\Agents\ArbiterAgent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ExecuteAgentTask implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 5;

    public function __construct(
        protected string $executionId,
        protected string $taskId,
        protected string $agentName,
        protected array $payload,
        protected int $organizationId,
    ) {}

    public function handle(ArbiterAgent $arbiter, AgentMessageBus $bus): void
    {
        Log::channel('agents')->info('ExecuteAgentTask: processing', [
            'execution_id' => $this->executionId,
            'task_id' => $this->taskId,
            'agent' => $this->agentName,
            'organization_id' => $this->organizationId,
        ]);

        $task = [
            'id' => $this->taskId,
            'description' => $this->payload['description'] ?? 'Execute task',
            'agent' => $this->agentName,
            'priority' => $this->payload['priority'] ?? 1,
            'dependencies' => $this->payload['dependencies'] ?? [],
            'estimated_complexity' => $this->payload['estimated_complexity'] ?? 'medium',
        ];

        $result = $arbiter->executeTask($task, $this->organizationId);

        $bus->recordResult($this->taskId, array_merge($result, [
            'execution_id' => $this->executionId,
            'organization_id' => $this->organizationId,
        ]));
    }
}
