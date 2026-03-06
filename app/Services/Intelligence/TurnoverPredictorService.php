<?php

namespace App\Services\Intelligence;

use App\Models\EmployeePulse;
use App\Models\People;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Facades\Log;

class TurnoverPredictorService
{
    public function __construct(
        protected AiOrchestratorService $orchestrator
    ) {}

    /**
     * Predice el riesgo de rotación de un empleado en función de su nuevo pulso y su historial.
     */
    public function analyzeRiskForPulse(EmployeePulse $pulse): void
    {
        try {
            $person = $pulse->person;
            
            // Recopilar contexto: pulsos anteriores.
            $previousPulses = EmployeePulse::where('people_id', $person->id)
                ->where('id', '!=', $pulse->id)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
            
            $history = $previousPulses->map(function ($p) {
                return [
                    'date' => $p->created_at->format('Y-m-d'),
                    'e_nps' => $p->e_nps,
                    'stress_level' => $p->stress_level,
                    'engagement_level' => $p->engagement_level,
                ];
            })->toArray();

            $currentData = [
                'date' => $pulse->created_at->format('Y-m-d'),
                'e_nps' => $pulse->e_nps,
                'stress_level' => $pulse->stress_level,
                'engagement_level' => $pulse->engagement_level,
                'comments' => $pulse->comments,
            ];

            $profile = [
                'role' => $person->role_name ?? 'Desconocido',
                'hire_date' => clone $person->created_at, // Mocking tenure, idealmente tendríamos hire_date
            ];

            $prompt = "Actúa como Stratos AI, un científico de datos especializado en People Analytics y Retención de Talento.\n\n";
            $prompt .= "OBJETIVO: Analizar el pulso más reciente de un empleado, compararlo con su historial y predecir el riesgo de rotación (fuga de talento).\n\n";
            $prompt .= "CONTEXTO DEL COLABORADOR:\n";
            $prompt .= "Rol: {$profile['role']}\n";
            $prompt .= "Historial de Pulsos Anteriores (Más reciente primero): " . json_encode($history) . "\n";
            $prompt .= "Pulso Actual (Recién respondido): " . json_encode($currentData) . "\n\n";

            $prompt .= "REGLAS DE ANÁLISIS:\n";
            $prompt .= "1. Una caída drástica en 'e_nps' o 'engagement_level' es una bandera roja.\n";
            $prompt .= "2. Un 'stress_level' alto sostenido aumenta el riesgo de Burnout (y renuncia).\n";
            $prompt .= "3. Los 'comments' pueden revelar frustración o aburrimiento.\n";
            $prompt .= "4. Clasifica el riesgo estrictamente como: 'low', 'medium', o 'high'.\n";
            $prompt .= "5. Brinda un 'ai_turnover_reason' breve (máximo 2 oraciones) indicando el porqué de la predicción y si requiere intervención rápida de RRHH.\n\n";

            $prompt .= "RESPUESTA:\n";
            $prompt .= "Devuelve EXCLUSIVAMENTE un objeto JSON válido con este esquema:\n";
            $prompt .= '{
  "ai_turnover_risk": "low",
  "ai_turnover_reason": "El nivel de engagegement se mantiene estable y el estrés es manejable."
}';

            $agentResponse = $this->orchestrator->agentThink('Orquestador de Rotación', $prompt);
            
            $analysisString = $agentResponse['response'] ?? '{}';
            $analysisString = str_replace('```json', '', $analysisString);
            $analysisString = str_replace('```', '', $analysisString);
            $analysis = json_decode(trim($analysisString), true);

            if ($analysis && isset($analysis['ai_turnover_risk'])) {
                $pulse->ai_turnover_risk = $analysis['ai_turnover_risk'];
                $pulse->ai_turnover_reason = $analysis['ai_turnover_reason'] ?? 'Detectado por IA';
                $pulse->save();
                
                Log::info("Turnover Prediction for {$person->first_name}: Risk - {$pulse->ai_turnover_risk}");
            } else {
                Log::warning("No se pudo parsear el análisis de rotación para Pulse ID: {$pulse->id}. Response: $analysisString");
            }
        } catch (\Exception $e) {
            Log::error('TurnoverPredictorService Error: ' . $e->getMessage());
        }
    }
}
