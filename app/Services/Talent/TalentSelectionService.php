<?php

namespace App\Services\Talent;

use App\Models\Application;
use App\Models\JobOpening;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Facades\Log;

class TalentSelectionService
{
    protected AiOrchestratorService $orchestrator;

    public function __construct(AiOrchestratorService $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * Analiza una aplicación específica usando el Selector de Talento.
     */
    public function analyzeApplication(int $applicationId): array
    {
        $application = Application::with(['people', 'jobOpening.role'])->findOrFail($applicationId);
        
        $candidateName = $application->people->name;
        $roleName = $application->jobOpening->role->name ?? 'Posición Especialista';
        
        $prompt = "Necesito que analices la aplicación de {$candidateName} para el cargo de {$roleName}.
        
        Datos del candidato:
        - Bio/Experiencia: {$application->people->bio}
        - Mensaje de Aplicación: {$application->message}
        
        Requerimientos del Puesto:
        - Rol: {$roleName}
        
        Por favor:
        1. Evalúa el 'Match' técnico y cultural (0-100%).
        2. Identifica 3 fortalezas clave.
        3. Identifica 1 riesgo o brecha importante.
        4. Recomienda si debe pasar a la siguiente fase (terna).
        
        Responde en un tono profesional de reclutador experto.";

        try {
            $result = $this->orchestrator->agentThink('Selector de Talento', $prompt);
            
            // Guardar el análisis en la aplicación (suponiendo que tenemos un campo para ello o en logs)
            Log::info("Análisis de Selector de Talento completado para APP #{$applicationId}");
            
            return [
                'status' => 'analyzed',
                'candidate' => $candidateName,
                'analysis' => $result['response']
            ];
        } catch (\Exception $e) {
            Log::error("Error en análisis de talento: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Propone una terna para una vacante buscando entre todos los aplicantes.
     */
    public function proposeShortlist(int $jobOpeningId): array
    {
        $opening = JobOpening::with(['applications.people'])->findOrFail($jobOpeningId);
        $applications = $opening->applications;

        if ($applications->isEmpty()) {
            return ['status' => 'error', 'message' => 'No hay candidatos para analizar.'];
        }

        $prompt = "Actúa como Selector de Talento de Stratos. Tengo los siguientes candidatos para la vacante '{$opening->title}':\n\n";
        
        foreach ($applications as $app) {
            $prompt .= "- {$app->people->name}: {$app->people->bio}\n";
        }

        $prompt .= "\nPor favor, selecciona a los mejores 3 (terna) y justifica por qué son los finalistas ideales. Si hay menos de 3, selecciona los que califiquen.";

        try {
            $result = $this->orchestrator->agentThink('Selector de Talento', $prompt);
            return ['status' => 'shortlist_proposed', 'result' => $result['response']];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
