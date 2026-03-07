<?php

namespace App\Services\Talent;

use App\Models\DevelopmentAction;
use App\Models\DevelopmentPath;
use App\Models\People;
use App\Models\Skill;
use App\Services\AiOrchestratorService;
use App\Services\GapAnalysisService;
use Illuminate\Support\Facades\DB;

class AiDevelopmentNavigatorService
{
    public function __construct(
        protected AiOrchestratorService $orchestrator,
        protected MentorMatchingService $mentorService,
        protected GapAnalysisService $gapService
    ) {}

    /**
     * Genera un plan de desarrollo de aprendizaje profundo (Navigator) usando GenAI.
     */
    public function generateAiPath(int $peopleId, int $skillId, int $targetLevel): DevelopmentPath
    {
        $person = People::findOrFail($peopleId);
        $skill = Skill::findOrFail($skillId);

        // 1. Obtener contexto de Mentores Internos
        $mentors = $this->mentorService->findMentors($skillId, 4, 3);
        $mentorsContext = $mentors->map(function ($m) {
            return "ID: {$m->id}, Nombre: {$m->full_name}, Rol: {$m->role_name}, Experticia: {$m->expertise_level}/5";
        })->implode("\n");

        // 2. Construir el Prompt para el Navigator
        $prompt = "Actúa como Stratos Navigator, un AI Career Coach de élite.\n\n";
        $prompt .= "OBJETIVO: Diseñar una ruta de aprendizaje personalizada y de alto impacto para cerrar una brecha de habilidades.\n\n";
        $prompt .= "DATOS DEL COLABORADOR:\n";
        $prompt .= "Nombre: {$person->full_name}\n";
        $prompt .= 'Rol Actual: '.($person->role->name ?? 'N/A')."\n";
        $prompt .= "Habilidad a Desarrollar: {$skill->name}\n";
        $prompt .= "Nivel Objetivo: {$targetLevel}/5\n\n";

        $prompt .= "MENTORES INTERNOS DISPONIBLES (Prioriza conectar personas si hay expertos):\n";
        $prompt .= $mentorsContext ?: 'No hay expertos internos certificados aún. Sugiere tutoría externa o IA Bot.'."\n\n";

        $prompt .= "REGLAS DEL PLAN (Modelo 70-20-10):\n";
        $prompt .= "1. 'training' (10%): 1 acción de curso o lectura técnica.\n";
        $prompt .= "2. 'mentoring' (20%): 1 acción vinculada a un mentor interno (si existe) o a revisión de pares.\n";
        $prompt .= "3. 'project' o 'practice' (70%): 1 o 2 acciones de aplicación real en el trabajo diario.\n";
        $prompt .= "4. Sé específico: En lugar de 'Aprende Vue', di 'Refactorizar el módulo de facturación usando Composition API para mejorar reactividad'.\n\n";

        $prompt .= "FORMATO DE RESPUESTA (JSON STRICTO):\n";
        $prompt .= '{
    "path_title": "Plan Maestro: Dominio de [Skill]",
    "estimated_duration_months": 3,
    "actions": [
        {
            "title": "...",
            "description": "...",
            "type": "training|mentoring|project|practice",
            "strategy": "build|borrow|buy|bot",
            "estimated_hours": 20,
            "impact_weight": 0.10,
            "mentor_id": null
        }
    ]
}';

        $agentResponse = $this->orchestrator->agentThink('Stratos Navigator', $prompt);
        $json = $this->parseJson($agentResponse['response'] ?? '{}');

        // 3. Persistencia en Base de Datos
        return DB::transaction(function () use ($person, $skill, $json) {
            $path = DevelopmentPath::create([
                'organization_id' => $person->organization_id,
                'people_id' => $person->id,
                'target_role_id' => $person->role_id,
                'action_title' => $json['path_title'] ?? "Ruta de Desarrollo: {$skill->name}",
                'status' => 'active',
                'estimated_duration_months' => $json['estimated_duration_months'] ?? 3,
                'started_at' => now(),
            ]);

            if (isset($json['actions']) && is_array($json['actions'])) {
                foreach ($json['actions'] as $index => $act) {
                    DevelopmentAction::create([
                        'development_path_id' => $path->id,
                        'title' => $act['title'],
                        'description' => $act['description'],
                        'type' => $act['type'],
                        'strategy' => $act['strategy'],
                        'order' => $index + 1,
                        'status' => 'pending',
                        'estimated_hours' => $act['estimated_hours'] ?? 0,
                        'impact_weight' => $act['impact_weight'] ?? 0,
                        'mentor_id' => $act['mentor_id'] ?? null,
                    ]);
                }
            }

            return $path->load('actions');
        });
    }

    protected function parseJson(string $content): array
    {
        $content = str_replace(['```json', '```'], '', $content);
        $decoded = json_decode(trim($content), true);

        return is_array($decoded) ? $decoded : [];
    }
}
