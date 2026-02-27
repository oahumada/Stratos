<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PulseSurvey;
use App\Models\PulseResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PulseController extends Controller
{
    /**
     * Lista encuestas activas.
     */
    public function index(Request $request): JsonResponse
    {
        $surveys = PulseSurvey::where('is_active', true)
            ->withCount('responses')
            ->latest()
            ->get();

        return response()->json(['data' => $surveys]);
    }

    /**
     * Obtiene el detalle de una encuesta.
     */
    public function show($id): JsonResponse
    {
        $survey = PulseSurvey::with(['responses.people'])->findOrFail($id);
        return response()->json(['data' => $survey]);
    }

    /**
     * Guarda una respuesta.
     */
    public function storeResponse(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pulse_survey_id' => ['required', 'exists:pulse_surveys,id'],
            'people_id' => ['required', 'exists:people,id'],
            'answers' => ['required', 'array'],
        ]);

        // Simulación simple de sentiment score (podría ser un servicio de IA luego)
        $score = $this->calculateSentiment($data['answers']);

        $response = PulseResponse::create([
            'pulse_survey_id' => $data['pulse_survey_id'],
            'people_id' => $data['people_id'],
            'answers' => $data['answers'],
            'sentiment_score' => $score
        ]);

        return response()->json([
            'message' => 'Respuesta guardada correctamente',
            'data' => $response
        ], 201);
    }

    /**
     * Calcula un score de sentimiento básico basado en ratings.
     */
    protected function calculateSentiment(array $answers): float
    {
        $total = 0;
        $count = 0;

        foreach ($answers as $answer) {
            if (is_numeric($answer)) {
                $total += $answer;
                $count++;
            }
        }

        if ($count === 0) {
            return 50.0;
        }

        // Asumiendo escala 1-5, normalizar a 0-100
        return ($total / ($count * 5)) * 100;
    }

    /**
     * Ejecuta un escaneo de salud organizacional con el Culture Sentinel.
     * GET /api/pulse/health-scan
     */
    public function healthScan(Request $request, \App\Services\CultureSentinelService $sentinel): JsonResponse
    {
        $orgId = auth()->user()->organization_id;

        try {
            $result = $sentinel->runHealthScan($orgId);
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en escaneo de salud: ' . $e->getMessage()
            ], 500);
        }
    }
}
