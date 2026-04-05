<?php

namespace App\Services\Agents;

use App\Contracts\AgentInterface;
use App\Services\AgentMessageBus;
use App\Services\AiOrchestratorService;
use App\Services\TalentVerificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ArbiterAgent implements AgentInterface
{
    protected int $maxRetries = 3;

    protected int $timeoutSeconds = 30;

    public function __construct(
        protected AiOrchestratorService $orchestrator,
        protected TalentVerificationService $verifier,
        protected AgentMessageBus $messageBus,
    ) {}

    public function getName(): string
    {
        return 'Árbitro de Agentes';
    }

    public function getCapabilities(): array
    {
        return [
            'multi_agent_orchestration',
            'retry_management',
            'compensation_handling',
            'quality_assessment',
        ];
    }

    /**
     * Execute via AgentInterface contract.
     */
    public function execute(array $input, array $context = []): array
    {
        $organizationId = $context['organization_id'] ?? $input['organization_id'] ?? 0;

        return $this->orchestrate($input['plan'] ?? $input, $organizationId);
    }

    /**
     * Orchestrate sequential execution of a plan's tasks.
     */
    public function orchestrate(array $plan, int $organizationId): array
    {
        $executionId = (string) Str::uuid();
        $tasks = $plan['tasks'] ?? [];
        $executionOrder = $plan['execution_order'] ?? array_column($tasks, 'id');

        Log::channel('agents')->info('ArbiterAgent: starting orchestration', [
            'execution_id' => $executionId,
            'plan_id' => $plan['plan_id'] ?? 'unknown',
            'task_count' => count($tasks),
            'organization_id' => $organizationId,
        ]);

        $this->messageBus->publish('orchestration.started', [
            'execution_id' => $executionId,
            'plan_id' => $plan['plan_id'] ?? null,
            'task_count' => count($tasks),
        ], $organizationId);

        $taskIndex = collect($tasks)->keyBy('id');
        $completedTasks = [];
        $taskResults = [];
        $overallStatus = 'completed';

        foreach ($executionOrder as $taskId) {
            $task = $taskIndex->get($taskId);
            if (! $task) {
                Log::channel('agents')->warning('ArbiterAgent: task not found in plan', [
                    'task_id' => $taskId,
                    'execution_id' => $executionId,
                ]);

                continue;
            }

            $result = $this->executeTaskWithRetries($task, $organizationId, $executionId);
            $taskResults[$taskId] = $result;

            if ($result['status'] === 'completed') {
                $completedTasks[] = $task;
            } else {
                Log::channel('agents')->error('ArbiterAgent: task failed after retries', [
                    'task_id' => $taskId,
                    'execution_id' => $executionId,
                ]);

                $isCritical = ($task['estimated_complexity'] ?? 'medium') === 'critical';
                if ($isCritical) {
                    $this->compensate($completedTasks, $task);
                    $overallStatus = 'failed';
                    break;
                }

                $overallStatus = 'partial';
            }
        }

        $aggregated = $this->aggregateResults($taskResults);
        $qualityScore = $this->assessQuality($taskResults, $organizationId);

        $result = [
            'execution_id' => $executionId,
            'plan_id' => $plan['plan_id'] ?? null,
            'results' => $aggregated,
            'status' => $overallStatus,
            'quality_score' => $qualityScore,
            'task_summary' => [
                'total' => count($tasks),
                'completed' => count($completedTasks),
                'failed' => count($tasks) - count($completedTasks),
            ],
            'organization_id' => $organizationId,
            'completed_at' => now()->toISOString(),
        ];

        $this->messageBus->publish('orchestration.completed', [
            'execution_id' => $executionId,
            'status' => $overallStatus,
            'quality_score' => $qualityScore,
        ], $organizationId);

        Log::channel('agents')->info('ArbiterAgent: orchestration finished', [
            'execution_id' => $executionId,
            'status' => $overallStatus,
            'quality_score' => $qualityScore,
            'organization_id' => $organizationId,
        ]);

        return $result;
    }

    /**
     * Execute a single task, dispatching to the appropriate agent via AiOrchestratorService.
     */
    public function executeTask(array $task, int $organizationId): array
    {
        $agentName = $task['agent'] ?? $this->getName();
        $taskId = $task['id'] ?? (string) Str::uuid();

        Log::channel('agents')->info('ArbiterAgent: executing task', [
            'task_id' => $taskId,
            'agent' => $agentName,
            'organization_id' => $organizationId,
        ]);

        $this->messageBus->publish('task.started', [
            'task_id' => $taskId,
            'agent' => $agentName,
        ], $organizationId);

        try {
            $prompt = $this->buildTaskPrompt($task);
            $response = $this->orchestrator->agentThink($agentName, $prompt);

            $this->messageBus->recordResult($taskId, [
                'status' => 'completed',
                'output' => $response,
            ]);

            return [
                'task_id' => $taskId,
                'status' => 'completed',
                'output' => $response,
                'agent' => $agentName,
            ];
        } catch (\Throwable $e) {
            Log::channel('agents')->warning('ArbiterAgent: task execution failed', [
                'task_id' => $taskId,
                'error' => $e->getMessage(),
            ]);

            return [
                'task_id' => $taskId,
                'status' => 'failed',
                'error' => $e->getMessage(),
                'agent' => $agentName,
            ];
        }
    }

    /**
     * Retry a failed task with a modified prompt.
     */
    public function handleRetry(array $task, array $failedResult, int $attempt): array
    {
        Log::channel('agents')->info('ArbiterAgent: retrying task', [
            'task_id' => $task['id'] ?? 'unknown',
            'attempt' => $attempt,
        ]);

        $task['description'] = sprintf(
            '[Retry %d/%d] %s (Previous error: %s)',
            $attempt,
            $this->maxRetries,
            $task['description'] ?? '',
            Str::limit($failedResult['error'] ?? 'Unknown error', 100),
        );

        return $task;
    }

    /**
     * Compensate completed tasks when a critical task fails.
     */
    public function compensate(array $completedTasks, array $failedTask): void
    {
        Log::channel('agents')->warning('ArbiterAgent: compensating due to critical failure', [
            'failed_task' => $failedTask['id'] ?? 'unknown',
            'tasks_to_compensate' => count($completedTasks),
        ]);

        foreach (array_reverse($completedTasks) as $task) {
            try {
                $taskId = $task['id'] ?? 'unknown';
                $this->messageBus->recordResult($taskId, [
                    'status' => 'compensated',
                    'reason' => 'Critical task failed: '.($failedTask['id'] ?? 'unknown'),
                ]);

                Log::channel('agents')->info('ArbiterAgent: compensated task', [
                    'task_id' => $taskId,
                ]);
            } catch (\Throwable $e) {
                Log::channel('agents')->error('ArbiterAgent: compensation failed', [
                    'task_id' => $task['id'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Aggregate all task results into a summary.
     */
    public function aggregateResults(array $taskResults): array
    {
        $aggregated = [];

        foreach ($taskResults as $taskId => $result) {
            $aggregated[] = [
                'task_id' => $taskId,
                'status' => $result['status'] ?? 'unknown',
                'agent' => $result['agent'] ?? null,
                'has_output' => ! empty($result['output']),
            ];
        }

        return $aggregated;
    }

    /**
     * Assess overall quality score using TalentVerificationService.
     */
    public function assessQuality(array $results, int $organizationId): float
    {
        $completedResults = array_filter($results, fn ($r) => ($r['status'] ?? '') === 'completed');

        if (empty($completedResults)) {
            return 0.0;
        }

        $totalScore = 0.0;
        $count = 0;

        foreach ($completedResults as $result) {
            $output = $result['output'] ?? [];
            if (empty($output)) {
                continue;
            }

            try {
                $verification = $this->verifier->verify(
                    $result['agent'] ?? $this->getName(),
                    $output,
                    ['organization_id' => $organizationId],
                );
                $totalScore += $verification->score;
                $count++;
            } catch (\Throwable $e) {
                Log::channel('agents')->warning('ArbiterAgent: quality assessment failed for task', [
                    'task_id' => $result['task_id'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $count > 0 ? round($totalScore / $count, 2) : 0.0;
    }

    protected function executeTaskWithRetries(array $task, int $organizationId, string $executionId): array
    {
        $result = $this->executeTask($task, $organizationId);

        $attempt = 1;
        while ($result['status'] === 'failed' && $attempt < $this->maxRetries) {
            $attempt++;
            $task = $this->handleRetry($task, $result, $attempt);
            $result = $this->executeTask($task, $organizationId);
        }

        return $result;
    }

    protected function buildTaskPrompt(array $task): string
    {
        $description = $task['description'] ?? 'Execute task';
        $complexity = $task['estimated_complexity'] ?? 'medium';

        return "Execute the following task (complexity: {$complexity}):\n\n{$description}";
    }
}
