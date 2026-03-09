<?php

namespace App\Http\Controllers\Api\Automation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class N8nController extends Controller
{
    /**
     * Handle incoming webhooks from n8n.
     */
    public function handleWebhook(Request $request)
    {
        // Simple security check
        $secret = config('services.n8n.secret');
        if ($secret && $request->header('X-N8n-Secret') !== $secret) {
            Log::warning('Unauthorized n8n webhook attempt', ['header' => $request->header('X-N8n-Secret')]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $event = $request->input('event');
        $payload = $request->input('payload', []);

        Log::info("Received n8n webhook: $event", ['payload' => $payload]);

        // Process different event types
        return match ($event) {
            'workflow_completed' => $this->handleWorkflowCompleted($payload),
            'action_required' => $this->handleActionRequired($payload),
            default => $this->handleUnknownEvent($event, $payload),
        };
    }

    protected function handleWorkflowCompleted(array $payload)
    {
        // Logic to update scenario, role, or talent state
        return response()->json(['message' => 'Workflow completion processed']);
    }

    protected function handleActionRequired(array $payload)
    {
        // Logic to notify user or create an audit trail entry
        return response()->json(['message' => 'Action requirement logged']);
    }

    protected function handleUnknownEvent(string $event, array $payload)
    {
        Log::info("Unhandled n8n event: $event");
        return response()->json(['message' => 'Event received but not processed'], 202);
    }
}
