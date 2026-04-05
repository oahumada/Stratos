<?php

namespace App\Services\Agents;

use App\Contracts\AgentInterface;
use App\Services\AiOrchestratorService;
use App\Services\TalentVerificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PlannerAgent implements AgentInterface
{
    public function __construct(
        protected AiOrchestratorService $orchestrator,
        protected TalentVerificationService $verifier,
    ) {}

    public function getName(): string
    {
        return 'Planificador Cognitivo';
    }

    public function getCapabilities(): array
    {
        return [
            'task_decomposition',
            'execution_planning',
            'dependency_resolution',
            'complexity_estimation',
        ];
    }

    /**
     * Execute via AgentInterface contract.
     */
    public function execute(array $input, array $context = []): array
    {
        $organizationId = $context['organization_id'] ?? $input['organization_id'] ?? 0;

        return $this->decompose(
            $input['objective'] ?? '',
            $input['constraints'] ?? [],
            $organizationId,
            $input['available_agents'] ?? [],
        );
    }

    /**
     * Decompose a high-level objective into a tree of sub-tasks.
     */
    public function decompose(string $objective, array $constraints, int $organizationId, array $availableAgents = []): array
    {
        Log::channel('agents')->info('PlannerAgent: decomposing objective', [
            'objective' => Str::limit($objective, 100),
            'organization_id' => $organizationId,
        ]);

        $prompt = $this->buildDecompositionPrompt($objective, $constraints, $availableAgents);

        $llmResponse = $this->orchestrator->agentThink(
            $this->getName(),
            $prompt,
            $this->getSystemPrompt(),
        );

        $tasks = $this->buildTaskTree($llmResponse);
        $executionOrder = $this->resolveExecutionOrder($tasks);

        $plan = [
            'plan_id' => (string) Str::uuid(),
            'objective' => $objective,
            'organization_id' => $organizationId,
            'tasks' => $tasks,
            'execution_order' => $executionOrder,
            'constraints' => $constraints,
            'created_at' => now()->toISOString(),
        ];

        $verification = $this->verifier->verify(
            $this->getName(),
            $plan,
            ['organization_id' => $organizationId],
        );

        $plan['verification'] = [
            'score' => $verification->score,
            'recommendation' => $verification->recommendation,
            'passed' => $verification->isPassed(),
        ];

        Log::channel('agents')->info('PlannerAgent: plan created', [
            'plan_id' => $plan['plan_id'],
            'task_count' => count($tasks),
            'verification_score' => $verification->score,
            'organization_id' => $organizationId,
        ]);

        return $plan;
    }

    /**
     * Structure LLM output into a normalized task tree.
     */
    public function buildTaskTree(array $llmResponse): array
    {
        $rawTasks = $llmResponse['tasks']
            ?? $llmResponse['subtasks']
            ?? $llmResponse['steps']
            ?? [];

        $tasks = [];
        foreach ($rawTasks as $index => $raw) {
            $taskId = $raw['id'] ?? 'task_'.($index + 1);

            $tasks[] = [
                'id' => $taskId,
                'description' => $raw['description'] ?? $raw['task'] ?? 'Undefined task',
                'agent' => $raw['agent'] ?? $raw['assigned_to'] ?? null,
                'priority' => (int) ($raw['priority'] ?? ($index + 1)),
                'dependencies' => $raw['dependencies'] ?? $raw['depends_on'] ?? [],
                'estimated_complexity' => $this->estimateComplexity($raw),
                'status' => 'pending',
            ];
        }

        // If LLM returned no structured tasks, create a single fallback task
        if (empty($tasks) && ! empty($llmResponse)) {
            $tasks[] = [
                'id' => 'task_1',
                'description' => $llmResponse['content'] ?? $llmResponse['response'] ?? 'Process objective',
                'agent' => null,
                'priority' => 1,
                'dependencies' => [],
                'estimated_complexity' => 'medium',
                'status' => 'pending',
            ];
        }

        return $tasks;
    }

    /**
     * Topological sort of tasks respecting dependency order.
     */
    public function resolveExecutionOrder(array $tasks): array
    {
        $graph = [];
        $inDegree = [];

        foreach ($tasks as $task) {
            $id = $task['id'];
            $graph[$id] = $task['dependencies'] ?? [];
            $inDegree[$id] = count($graph[$id]);
        }

        // Kahn's algorithm
        $queue = [];
        foreach ($inDegree as $id => $degree) {
            if ($degree === 0) {
                $queue[] = $id;
            }
        }

        $order = [];
        while (! empty($queue)) {
            // Sort by priority within the same level
            usort($queue, function ($a, $b) use ($tasks) {
                $priorityA = collect($tasks)->firstWhere('id', $a)['priority'] ?? 999;
                $priorityB = collect($tasks)->firstWhere('id', $b)['priority'] ?? 999;

                return $priorityA - $priorityB;
            });

            $current = array_shift($queue);
            $order[] = $current;

            foreach ($inDegree as $id => &$degree) {
                if (in_array($current, $graph[$id] ?? [], true)) {
                    $degree--;
                    if ($degree === 0) {
                        $queue[] = $id;
                    }
                }
            }
            unset($degree);
        }

        // Append any tasks not reached (circular dependency safety)
        foreach ($tasks as $task) {
            if (! in_array($task['id'], $order, true)) {
                $order[] = $task['id'];
            }
        }

        return $order;
    }

    /**
     * Estimate task complexity based on description and metadata.
     */
    public function estimateComplexity(array $task): string
    {
        if (isset($task['complexity']) || isset($task['estimated_complexity'])) {
            $value = $task['complexity'] ?? $task['estimated_complexity'];
            if (in_array($value, ['low', 'medium', 'high', 'critical'], true)) {
                return $value;
            }
        }

        $description = strtolower($task['description'] ?? $task['task'] ?? '');
        $deps = count($task['dependencies'] ?? $task['depends_on'] ?? []);

        if ($deps >= 3 || str_contains($description, 'integra') || str_contains($description, 'complex')) {
            return 'critical';
        }
        if ($deps >= 2 || str_contains($description, 'analy') || str_contains($description, 'evalua')) {
            return 'high';
        }
        if ($deps >= 1 || strlen($description) > 100) {
            return 'medium';
        }

        return 'low';
    }

    protected function buildDecompositionPrompt(string $objective, array $constraints, array $availableAgents): string
    {
        $constraintText = ! empty($constraints)
            ? 'Constraints: '.implode(', ', $constraints)
            : 'No specific constraints.';

        $agentText = ! empty($availableAgents)
            ? 'Available agents: '.implode(', ', $availableAgents)
            : 'Use any appropriate agents.';

        return <<<PROMPT
        Decompose the following objective into a structured plan of sub-tasks.

        OBJECTIVE: {$objective}

        {$constraintText}
        {$agentText}

        Respond with a JSON object containing a "tasks" array. Each task should have:
        - "id": unique identifier (e.g., "task_1")
        - "description": clear description of what to do
        - "agent": which agent should handle this (or null)
        - "priority": integer (1 = highest)
        - "dependencies": array of task IDs this depends on
        - "complexity": one of "low", "medium", "high", "critical"
        PROMPT;
    }

    protected function getSystemPrompt(): string
    {
        return 'You are a strategic planning agent. You decompose complex HR/talent objectives '
            .'into actionable sub-tasks. Always respond with valid JSON containing a "tasks" array. '
            .'Each task must specify dependencies, priority, and which agent should handle it.';
    }
}
