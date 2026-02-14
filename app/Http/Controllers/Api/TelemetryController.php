<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelemetryController extends Controller
{
    /**
     * Almacena un evento de telemetría ligero para análisis de uso.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event' => 'required|string',
            'properties' => 'nullable|array',
            'user_id' => 'nullable|integer',
            'session_id' => 'nullable|string',
        ]);

        // En una implementación real, esto podría ir a Mixpanel, Datadog o una tabla interna.
        // Por ahora lo registramos en los logs para trazabilidad.
        Log::info('Stratos Telemetry Event', $validated);

        return response()->json([
            'success' => true,
            'message' => 'Telemetry event recorded'
        ]);
    }
}
