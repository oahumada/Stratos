<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelemetryController extends Controller
{
    /**
     * Receive a simple analytics event from the frontend and log it.
     * This endpoint is intentionally lightweight: it logs the event and
     * returns success. In future it can persist to DB or forward to an
     * external analytics provider.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $payload = $request->validate([
            'event' => 'required|string|max:191',
            'properties' => 'sometimes|array',
        ]);

        $event = $payload['event'];
        $props = $payload['properties'] ?? [];

        $log = [
            'event' => $event,
            'user_id' => $user ? $user->id : null,
            'ip' => $request->ip(),
            'properties' => $props,
            'url' => $request->headers->get('referer') ?? $request->fullUrl(),
            'ts' => now()->toDateTimeString(),
        ];

        Log::channel('stack')->info('ui-telemetry', $log);

        return response()->json(['success' => true]);
    }
}
