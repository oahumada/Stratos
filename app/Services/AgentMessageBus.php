<?php

namespace App\Services;

use App\Jobs\ExecuteAgentTask;
use App\Models\AgentMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AgentMessageBus
{
    /** @var array<string, callable[]> */
    protected array $subscribers = [];

    /**
     * Publish a message to a channel with tenant scoping.
     */
    public function publish(string $channel, array $message, int $organizationId): string
    {
        $messageId = (string) Str::uuid();

        $agentMessage = AgentMessage::withoutGlobalScopes()->create([
            'execution_id' => $message['execution_id'] ?? $messageId,
            'task_id' => $message['task_id'] ?? null,
            'channel' => $channel,
            'agent_name' => $message['agent'] ?? null,
            'payload' => $message,
            'result' => null,
            'status' => 'queued',
            'organization_id' => $organizationId,
            'attempts' => 0,
        ]);

        Log::channel('agents')->debug('MessageBus: published', [
            'channel' => $channel,
            'message_id' => $agentMessage->id,
            'organization_id' => $organizationId,
        ]);

        // Notify subscribers
        foreach ($this->subscribers[$channel] ?? [] as $handler) {
            try {
                $handler($message, $organizationId);
            } catch (\Throwable $e) {
                Log::channel('agents')->warning('MessageBus: subscriber error', [
                    'channel' => $channel,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $messageId;
    }

    /**
     * Register a handler for a channel.
     */
    public function subscribe(string $channel, callable $handler): void
    {
        $this->subscribers[$channel][] = $handler;
    }

    /**
     * Queue a job for agent task execution.
     */
    public function dispatch(string $taskId, string $agentName, array $payload, int $organizationId): void
    {
        $executionId = $payload['execution_id'] ?? (string) Str::uuid();

        AgentMessage::withoutGlobalScopes()->create([
            'execution_id' => $executionId,
            'task_id' => $taskId,
            'channel' => 'task.dispatched',
            'agent_name' => $agentName,
            'payload' => $payload,
            'result' => null,
            'status' => 'queued',
            'organization_id' => $organizationId,
            'attempts' => 0,
        ]);

        ExecuteAgentTask::dispatch(
            $executionId,
            $taskId,
            $agentName,
            $payload,
            $organizationId,
        );

        Log::channel('agents')->info('MessageBus: dispatched task', [
            'task_id' => $taskId,
            'agent' => $agentName,
            'organization_id' => $organizationId,
        ]);
    }

    /**
     * Get execution status from stored messages.
     */
    public function getStatus(string $executionId): array
    {
        $messages = AgentMessage::withoutGlobalScopes()
            ->where('execution_id', $executionId)
            ->orderBy('created_at')
            ->get();

        if ($messages->isEmpty()) {
            return [
                'execution_id' => $executionId,
                'status' => 'not_found',
                'messages' => [],
            ];
        }

        $statuses = $messages->pluck('status')->unique()->values()->toArray();
        $overallStatus = 'processing';

        if (in_array('failed', $statuses, true)) {
            $overallStatus = 'failed';
        } elseif (! in_array('processing', $statuses, true) && ! in_array('queued', $statuses, true)) {
            $overallStatus = 'completed';
        }

        return [
            'execution_id' => $executionId,
            'status' => $overallStatus,
            'total_messages' => $messages->count(),
            'messages' => $messages->map(fn (AgentMessage $m) => [
                'task_id' => $m->task_id,
                'channel' => $m->channel,
                'status' => $m->status,
                'agent' => $m->agent_name,
                'attempts' => $m->attempts,
            ])->toArray(),
        ];
    }

    /**
     * Store result for a completed task.
     */
    public function recordResult(string $taskId, array $result): void
    {
        DB::transaction(function () use ($taskId, $result) {
            $message = AgentMessage::withoutGlobalScopes()
                ->where('task_id', $taskId)
                ->latest()
                ->first();

            if ($message) {
                $message->update([
                    'result' => $result,
                    'status' => $result['status'] ?? 'completed',
                    'attempts' => $message->attempts + 1,
                ]);
            } else {
                AgentMessage::withoutGlobalScopes()->create([
                    'execution_id' => $result['execution_id'] ?? (string) Str::uuid(),
                    'task_id' => $taskId,
                    'channel' => 'task.result',
                    'agent_name' => $result['agent'] ?? null,
                    'payload' => [],
                    'result' => $result,
                    'status' => $result['status'] ?? 'completed',
                    'organization_id' => $result['organization_id'] ?? 0,
                    'attempts' => 1,
                ]);
            }
        });

        Log::channel('agents')->debug('MessageBus: recorded result', [
            'task_id' => $taskId,
            'status' => $result['status'] ?? 'completed',
        ]);
    }
}
