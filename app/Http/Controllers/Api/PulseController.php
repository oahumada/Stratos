<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\People;
use App\Models\PulseResponse;
use App\Models\PulseSurvey;
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
            'sentiment_score' => $score,
        ]);

        return response()->json([
            'message' => 'Respuesta guardada correctamente',
            'data' => $response,
        ], 201);
    }

    /**
     * Guarda el pulso recurrente de 10 segundos (EmployeePulse) y pide IA Análisis.
     */
    public function storeEmployeePulse(Request $request, \App\Services\Intelligence\TurnoverPredictorService $predictor): JsonResponse
    {
        $data = $request->validate([
            'people_id' => ['required', 'exists:people,id'],
            'e_nps' => ['nullable', 'integer', 'min:0', 'max:10'],
            'stress_level' => ['nullable', 'integer', 'min:1', 'max:5'],
            'engagement_level' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comments' => ['nullable', 'string'],
        ]);

        $pulse = \App\Models\EmployeePulse::create($data);

        // Desencadenar la IA predictiva de forma síncrona/asíncrona
        // Lo dejamos síncrono para demostración ràpida en Frontend
        $predictor->analyzeRiskForPulse($pulse);

        return response()->json([
            'status' => 'success',
            'message' => 'Pulso registrado. El Córtex de Talento ha ajustado su análisis predictivo.',
            'data' => $pulse->refresh(),
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
                'message' => 'Error en escaneo de salud: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lista los últimos pulsos con su análisis de riesgo de la IA.
     */
    public function listEmployeePulses(Request $request): JsonResponse
    {
        $pulses = \App\Models\EmployeePulse::with('person')
            ->latest()
            ->limit(50)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $pulses,
        ]);
    }

    /**
     * Genera la data para el Heatmap de Rotación Profundo.
     */
    public function listTurnoverHeatmap(Request $request, \App\Services\Intelligence\RetentionDeepPredictorService $predictor): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $people = People::where('organization_id', $orgId)
            ->whereNull('termination_date')
            ->get();

        $heatmap = $people->map(function ($person) use ($predictor) {
            $prediction = $predictor->predict($person->id);

            return [
                'id' => $person->id,
                'name' => $person->full_name ?? ($person->first_name.' '.$person->last_name),
                'role' => $person->role->name ?? 'N/A',
                'department' => $person->department->name ?? 'N/A',
                'risk_score' => $prediction['flight_risk_score'] ?? 0,
                'risk_level' => $prediction['risk_level'] ?? 'low',
                'primary_driver' => $prediction['primary_driver'] ?? 'None',
                'financial_impact' => $prediction['financial_impact']['replacement_cost_usd'] ?? 0,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $heatmap,
        ]);
    }
}
