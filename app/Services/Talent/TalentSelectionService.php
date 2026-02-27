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
            $analysis = $result['response'];

            // Extraer match_score si viene en el JSON
            $matchScore = $analysis['match_score'] ?? $analysis['match'] ?? null;
            if (is_string($matchScore)) {
                $matchScore = (int) filter_var($matchScore, FILTER_SANITIZE_NUMBER_INT);
            }
            
            // Guardar el análisis en la aplicación
            $application->update([
                'ai_analysis' => $analysis,
                'match_score' => $matchScore
            ]);

            Log::info("Análisis de Selector de Talento persistido para APP #{$applicationId}");
            
            return [
                'status' => 'analyzed',
                'candidate' => $candidateName,
                'analysis' => $analysis
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

    /**
     * Extrae el 'Blueprint de Éxito' (DNA) de una persona de alto rendimiento.
     */
    public function extractHighPerformerDNA(int $personId): array
    {
        $person = \App\Models\People::with(['activeSkills.skill', 'psychometricProfiles'])->findOrFail($personId);
        
        $skills = $person->activeSkills->map(fn($s) => $s->skill->name)->implode(', ');
        $traits = $person->psychometricProfiles->map(fn($p) => "{$p->trait_name}: {$p->score}")->implode(', ');
        
        $prompt = "Actúa como el Matchmaker de Resonancia. Estoy analizando a un 'High Performer' de la organización para decodificar su DNA de éxito.
        
        Datos del Perfil Exitoso:
        - Nombre: {$person->full_name}
        - Skills Dominantes: {$skills}
        - Rasgos Psicofisiológicos (DISC): {$traits}
        
        Por favor:
        1. Define el 'Persona de Éxito' para este rol.
        2. Identifica el 'Gen Dominante' (la característica más crítica que lo hace exitoso).
        3. Crea un perfil de búsqueda optimizado para encontrar clones de este talento.
        
        Responde en formato JSON con: 'success_persona', 'dominant_gene', 'search_profile' (string).";

        try {
            $result = $this->orchestrator->agentThink('Matchmaker de Resonancia', $prompt);
            return $result['response'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
