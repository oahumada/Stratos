<?php

use App\Contracts\AgentInterface;
use App\Data\VerificationResult;
use App\Jobs\ExecuteAgentTask;
use App\Models\Agent;
use App\Models\AgentMessage;
use App\Models\Organization;
use App\Services\AgentMessageBus;
use App\Services\Agents\ArbiterAgent;
use App\Services\Agents\PlannerAgent;
use App\Services\AiOrchestratorService;
use App\Services\TalentVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->otherOrganization = Organization::factory()->create();

    // Seed the planner and arbiter agents
    Agent::factory()->create([
        'name' => 'Planificador Cognitivo',
        'type' => 'analyst',
        'provider' => 'deepseek',
        'model' => 'deepseek-chat',
        'is_active' => true,
        'organization_id' => null,
    ]);
    Agent::factory()->create([
        'name' => 'Árbitro de Agentes',
        'type' => 'analyst',
        'provider' => 'deepseek',
        'model' => 'deepseek-reasoner',
        'is_active' => true,
        'organization_id' => null,
    ]);
    Agent::factory()->create([
        'name' => 'Estratega de Talento',
        'type' => 'analyst',
        'provider' => 'deepseek',
        'model' => 'deepseek-chat',
        'is_active' => true,
        'organization_id' => null,
    ]);

    $this->mockOrchestrator = Mockery::mock(AiOrchestratorService::class);
    $this->mockVerifier = Mockery::mock(TalentVerificationService::class);

    $this->mockVerifier
        ->shouldReceive('verify')
        ->andReturn(VerificationResult::passed('Test verification'))
        ->byDefault();

    $this->app->instance(AiOrchestratorService::class, $this->mockOrchestrator);
    $this->app->instance(TalentVerificationService::class, $this->mockVerifier);
});

// ============ AgentInterface Contract ============

it('PlannerAgent implements AgentInterface', function () {
    $planner = app(PlannerAgent::class);
    expect($planner)->toBeInstanceOf(AgentInterface::class);
    expect($planner->getName())->toBe('Planificador Cognitivo');
    expect($planner->getCapabilities())->toContain('task_decomposition');
});

it('ArbiterAgent implements AgentInterface', function () {
    $arbiter = app(ArbiterAgent::class);
    expect($arbiter)->toBeInstanceOf(AgentInterface::class);
    expect($arbiter->getName())->toBe('Árbitro de Agentes');
    expect($arbiter->getCapabilities())->toContain('multi_agent_orchestration');
});

// ============ PlannerAgent Tests ============

it('PlannerAgent decomposes objective into tasks', function () {
    $this->mockOrchestrator
        ->shouldReceive('agentThink')
        ->once()
        ->andReturn([
            'tasks' => [
                ['id' => 'task_1', 'description' => 'Analyze skills gap', 'agent' => 'Estratega de Talento', 'priority' => 1, 'dependencies' => []],
                ['id' => 'task_2', 'description' => 'Create learning paths', 'agent' => 'Coach de Crecimiento', 'priority' => 2, 'dependencies' => ['task_1']],
            ],
        ]);

    $planner = app(PlannerAgent::class);
    $result = $planner->decompose(
        'Improve engineering team skills',
        ['budget' => 'limited'],
        $this->organization->id,
    );

    expect($result)->toHaveKeys(['plan_id', 'tasks', 'execution_order', 'verification']);
    expect($result['tasks'])->toHaveCount(2);
    expect($result['tasks'][0]['id'])->toBe('task_1');
    expect($result['tasks'][1]['dependencies'])->toContain('task_1');
    expect($result['organization_id'])->toBe($this->organization->id);
});

it('PlannerAgent resolves execution order via topological sort', function () {
    $planner = app(PlannerAgent::class);

    $tasks = [
        ['id' => 'task_c', 'description' => 'C', 'priority' => 3, 'dependencies' => ['task_a', 'task_b']],
        ['id' => 'task_a', 'description' => 'A', 'priority' => 1, 'dependencies' => []],
        ['id' => 'task_b', 'description' => 'B', 'priority' => 2, 'dependencies' => ['task_a']],
    ];

    $order = $planner->resolveExecutionOrder($tasks);

    expect($order)->toBe(['task_a', 'task_b', 'task_c']);
});

it('PlannerAgent handles tasks with no dependencies', function () {
    $planner = app(PlannerAgent::class);

    $tasks = [
        ['id' => 'task_1', 'description' => 'First', 'priority' => 2, 'dependencies' => []],
        ['id' => 'task_2', 'description' => 'Second', 'priority' => 1, 'dependencies' => []],
    ];

    $order = $planner->resolveExecutionOrder($tasks);

    // task_2 has higher priority (lower number)
    expect($order[0])->toBe('task_2');
    expect($order[1])->toBe('task_1');
});

it('PlannerAgent builds task tree from various LLM formats', function () {
    $planner = app(PlannerAgent::class);

    // Test with "steps" key
    $result = $planner->buildTaskTree([
        'steps' => [
            ['description' => 'Step 1'],
            ['description' => 'Step 2'],
        ],
    ]);

    expect($result)->toHaveCount(2);
    expect($result[0]['id'])->toBe('task_1');
    expect($result[0]['status'])->toBe('pending');
});

it('PlannerAgent estimates complexity correctly', function () {
    $planner = app(PlannerAgent::class);

    expect($planner->estimateComplexity(['description' => 'simple task']))->toBe('low');
    expect($planner->estimateComplexity(['description' => 'evaluate team performance', 'dependencies' => ['a', 'b']]))->toBe('high');
    expect($planner->estimateComplexity(['description' => 'integrate systems', 'dependencies' => ['a', 'b', 'c']]))->toBe('critical');
    expect($planner->estimateComplexity(['complexity' => 'high']))->toBe('high');
});

it('PlannerAgent validates plan via verifier', function () {
    $this->mockOrchestrator
        ->shouldReceive('agentThink')
        ->once()
        ->andReturn([
            'tasks' => [
                ['id' => 'task_1', 'description' => 'Test task', 'priority' => 1, 'dependencies' => []],
            ],
        ]);

    $this->mockVerifier
        ->shouldReceive('verify')
        ->once()
        ->withArgs(function ($agentId, $output, $context) {
            return $agentId === 'Planificador Cognitivo'
                && isset($context['organization_id']);
        })
        ->andReturn(VerificationResult::passed('Valid plan'));

    $planner = app(PlannerAgent::class);
    $result = $planner->decompose('Test objective', [], $this->organization->id);

    expect($result['verification']['passed'])->toBeTrue();
    expect($result['verification']['score'])->toBe(1.0);
});

// ============ ArbiterAgent Tests ============

it('ArbiterAgent orchestrates sequential task execution', function () {
    $this->mockOrchestrator
        ->shouldReceive('agentThink')
        ->twice()
        ->andReturn(['result' => 'success', 'data' => 'processed']);

    $arbiter = app(ArbiterAgent::class);

    $plan = [
        'plan_id' => 'test-plan-001',
        'tasks' => [
            ['id' => 'task_1', 'description' => 'First task', 'agent' => 'Estratega de Talento', 'priority' => 1, 'dependencies' => [], 'estimated_complexity' => 'low'],
            ['id' => 'task_2', 'description' => 'Second task', 'agent' => 'Estratega de Talento', 'priority' => 2, 'dependencies' => ['task_1'], 'estimated_complexity' => 'medium'],
        ],
        'execution_order' => ['task_1', 'task_2'],
    ];

    $result = $arbiter->orchestrate($plan, $this->organization->id);

    expect($result['status'])->toBe('completed');
    expect($result['execution_id'])->not->toBeEmpty();
    expect($result['results'])->toHaveCount(2);
    expect($result['task_summary']['completed'])->toBe(2);
    expect($result['quality_score'])->toBeGreaterThanOrEqual(0);
});

it('ArbiterAgent handles retry on failure', function () {
    $callCount = 0;
    $this->mockOrchestrator
        ->shouldReceive('agentThink')
        ->andReturnUsing(function () use (&$callCount) {
            $callCount++;
            if ($callCount <= 2) {
                throw new \RuntimeException('Temporary failure');
            }

            return ['result' => 'success after retry'];
        });

    $arbiter = app(ArbiterAgent::class);

    $plan = [
        'plan_id' => 'retry-test',
        'tasks' => [
            ['id' => 'task_1', 'description' => 'Flaky task', 'agent' => 'Estratega de Talento', 'priority' => 1, 'dependencies' => [], 'estimated_complexity' => 'medium'],
        ],
        'execution_order' => ['task_1'],
    ];

    $result = $arbiter->orchestrate($plan, $this->organization->id);

    expect($result['status'])->toBe('completed');
    expect($callCount)->toBe(3);
});

it('ArbiterAgent compensates on critical failure', function () {
    $callCount = 0;
    $this->mockOrchestrator
        ->shouldReceive('agentThink')
        ->andReturnUsing(function () use (&$callCount) {
            $callCount++;
            if ($callCount > 1) {
                throw new \RuntimeException('Critical failure');
            }

            return ['result' => 'done'];
        });

    $arbiter = app(ArbiterAgent::class);

    $plan = [
        'plan_id' => 'compensate-test',
        'tasks' => [
            ['id' => 'task_1', 'description' => 'Succeeds', 'agent' => 'Estratega de Talento', 'priority' => 1, 'dependencies' => [], 'estimated_complexity' => 'low'],
            ['id' => 'task_2', 'description' => 'Fails critically', 'agent' => 'Estratega de Talento', 'priority' => 2, 'dependencies' => ['task_1'], 'estimated_complexity' => 'critical'],
        ],
        'execution_order' => ['task_1', 'task_2'],
    ];

    $result = $arbiter->orchestrate($plan, $this->organization->id);

    expect($result['status'])->toBe('failed');

    // Check compensation was recorded
    $compensated = AgentMessage::withoutGlobalScopes()
        ->where('task_id', 'task_1')
        ->where('status', 'compensated')
        ->exists();
    expect($compensated)->toBeTrue();
});

it('ArbiterAgent sets partial status for non-critical failures', function () {
    $callCount = 0;
    $this->mockOrchestrator
        ->shouldReceive('agentThink')
        ->andReturnUsing(function () use (&$callCount) {
            $callCount++;
            if ($callCount >= 2 && $callCount <= 4) {
                throw new \RuntimeException('Non-critical failure');
            }

            return ['result' => 'ok'];
        });

    $arbiter = app(ArbiterAgent::class);

    $plan = [
        'plan_id' => 'partial-test',
        'tasks' => [
            ['id' => 'task_1', 'description' => 'Succeeds', 'agent' => 'Estratega de Talento', 'priority' => 1, 'dependencies' => [], 'estimated_complexity' => 'low'],
            ['id' => 'task_2', 'description' => 'Fails (not critical)', 'agent' => 'Estratega de Talento', 'priority' => 2, 'dependencies' => [], 'estimated_complexity' => 'medium'],
        ],
        'execution_order' => ['task_1', 'task_2'],
    ];

    $result = $arbiter->orchestrate($plan, $this->organization->id);

    expect($result['status'])->toBe('partial');
    expect($result['task_summary']['completed'])->toBe(1);
    expect($result['task_summary']['failed'])->toBe(1);
});

// ============ AgentMessageBus Tests ============

it('MessageBus publishes and tracks messages', function () {
    $bus = app(AgentMessageBus::class);

    $messageId = $bus->publish('test.channel', [
        'execution_id' => 'exec-001',
        'data' => 'hello',
    ], $this->organization->id);

    expect($messageId)->not->toBeEmpty();

    $stored = AgentMessage::withoutGlobalScopes()
        ->where('channel', 'test.channel')
        ->where('organization_id', $this->organization->id)
        ->first();

    expect($stored)->not->toBeNull();
    expect($stored->status)->toBe('queued');
    expect($stored->payload['data'])->toBe('hello');
});

it('MessageBus records results', function () {
    $bus = app(AgentMessageBus::class);

    // First publish a message with task_id
    $bus->publish('task.started', [
        'execution_id' => 'exec-002',
        'task_id' => 'task_99',
    ], $this->organization->id);

    // Record result
    $bus->recordResult('task_99', [
        'status' => 'completed',
        'output' => ['data' => 'result'],
    ]);

    $message = AgentMessage::withoutGlobalScopes()
        ->where('task_id', 'task_99')
        ->first();

    expect($message->status)->toBe('completed');
    expect($message->result['output']['data'])->toBe('result');
    expect($message->attempts)->toBe(1);
});

it('MessageBus returns execution status', function () {
    $bus = app(AgentMessageBus::class);

    $bus->publish('task.started', [
        'execution_id' => 'exec-003',
        'task_id' => 'task_a',
    ], $this->organization->id);
    $bus->publish('task.started', [
        'execution_id' => 'exec-003',
        'task_id' => 'task_b',
    ], $this->organization->id);

    $status = $bus->getStatus('exec-003');

    expect($status['execution_id'])->toBe('exec-003');
    expect($status['total_messages'])->toBe(2);
});

it('MessageBus notifies subscribers', function () {
    $bus = app(AgentMessageBus::class);
    $received = [];

    $bus->subscribe('test.events', function ($message, $orgId) use (&$received) {
        $received[] = ['message' => $message, 'org' => $orgId];
    });

    $bus->publish('test.events', ['data' => 'test'], $this->organization->id);

    expect($received)->toHaveCount(1);
    expect($received[0]['message']['data'])->toBe('test');
    expect($received[0]['org'])->toBe($this->organization->id);
});

// ============ Multi-Tenant Isolation ============

it('messages from org 1 are not visible to org 2', function () {
    $bus = app(AgentMessageBus::class);

    $bus->publish('org.test', [
        'execution_id' => 'exec-org1',
        'data' => 'org1-data',
    ], $this->organization->id);

    $bus->publish('org.test', [
        'execution_id' => 'exec-org2',
        'data' => 'org2-data',
    ], $this->otherOrganization->id);

    $org1Messages = AgentMessage::withoutGlobalScopes()
        ->where('organization_id', $this->organization->id)
        ->where('channel', 'org.test')
        ->get();

    $org2Messages = AgentMessage::withoutGlobalScopes()
        ->where('organization_id', $this->otherOrganization->id)
        ->where('channel', 'org.test')
        ->get();

    expect($org1Messages)->toHaveCount(1);
    expect($org1Messages->first()->payload['data'])->toBe('org1-data');
    expect($org2Messages)->toHaveCount(1);
    expect($org2Messages->first()->payload['data'])->toBe('org2-data');
});

// ============ ExecuteAgentTask Job ============

it('ExecuteAgentTask job dispatches correctly', function () {
    Queue::fake();

    $bus = app(AgentMessageBus::class);
    $bus->dispatch('task_dispatch_1', 'Estratega de Talento', [
        'description' => 'Test dispatch',
        'execution_id' => 'exec-dispatch',
    ], $this->organization->id);

    Queue::assertPushed(ExecuteAgentTask::class, function ($job) {
        return true;
    });

    // Verify message was stored
    $message = AgentMessage::withoutGlobalScopes()
        ->where('task_id', 'task_dispatch_1')
        ->first();

    expect($message)->not->toBeNull();
    expect($message->agent_name)->toBe('Estratega de Talento');
    expect($message->status)->toBe('queued');
});

// ============ End-to-End ============

it('end-to-end: plan → orchestrate → results', function () {
    // Mock LLM to return a plan
    $this->mockOrchestrator
        ->shouldReceive('agentThink')
        ->andReturn([
            'tasks' => [
                ['id' => 'task_1', 'description' => 'Analyze current state', 'agent' => 'Estratega de Talento', 'priority' => 1, 'dependencies' => []],
                ['id' => 'task_2', 'description' => 'Propose improvements', 'agent' => 'Estratega de Talento', 'priority' => 2, 'dependencies' => ['task_1']],
            ],
            'result' => 'analysis complete',
        ]);

    $planner = app(PlannerAgent::class);
    $plan = $planner->decompose(
        'Improve talent retention',
        [],
        $this->organization->id,
    );

    expect($plan['tasks'])->toHaveCount(2);
    expect($plan['execution_order'])->toBe(['task_1', 'task_2']);
    expect($plan['verification']['passed'])->toBeTrue();

    // Now orchestrate the plan
    $arbiter = app(ArbiterAgent::class);
    $result = $arbiter->orchestrate($plan, $this->organization->id);

    expect($result['status'])->toBe('completed');
    expect($result['results'])->toHaveCount(2);
    expect($result['quality_score'])->toBeGreaterThanOrEqual(0);
    expect($result['organization_id'])->toBe($this->organization->id);
});
