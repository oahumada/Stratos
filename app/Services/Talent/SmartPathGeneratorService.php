<?php

namespace App\Services\Talent;

use App\Models\DevelopmentPath;
use App\Models\DevelopmentAction;
use App\Models\People;
use App\Models\Skill;
use Illuminate\Support\Facades\DB;

class SmartPathGeneratorService
{
    protected $mentorService;

    public function __construct(MentorMatchingService $mentorService)
    {
        $this->mentorService = $mentorService;
    }

    /**
     * Genera un plan de desarrollo automático basado en la brecha detectada.
     * 
     * @param int $peopleId
     * @param int $skillId
     * @param int $currentLevel
     * @param int $targetLevel
     * @param int|null $targetRoleId
     * @return DevelopmentPath
     */
    public function generatePath(int $peopleId, int $skillId, int $currentLevel, int $targetLevel, ?int $targetRoleId = null): DevelopmentPath
    {
        $gap = $targetLevel - $currentLevel;
        $person = People::findOrFail($peopleId);
        $skill = Skill::findOrFail($skillId);
        
        return DB::transaction(function () use ($person, $skill, $gap, $targetRoleId) {
            
            // 1. Crear el contenedor del Path
            $path = DevelopmentPath::create([
                'organization_id' => $person->organization_id,
                'people_id' => $person->id,
                // Si no hay rol objetivo, asumimos que es para mejorar en el rol actual
                'target_role_id' => $targetRoleId ?? $person->role_id,
                'action_title' => "Plan de Cierre de Brechas: {$skill->name}",
                'status' => 'draft',
                'estimated_duration_months' => $this->estimateDuration($gap),
                'started_at' => now(),
                'steps' => [] // JSON field fallback
            ]);

            // 2. Generar Acciones Específicas (70-20-10 Model)
            
            // A. Education (10%) - Cursos / Recursos
            $this->createEducationAction($path, $skill, $gap);

            // B. Exposure (20%) - Mentoria / Social
            $this->createExposureAction($path, $skill);

            // C. Experience (70%) - Proyectos / On-the-job
            $this->createExperienceAction($path, $skill, $gap);

            return $path->load('actions');
        });
    }

    private function estimateDuration(int $gap): int
    {
        return $gap > 1 ? 6 : 3; // 6 meses para gaps grandes, 3 para pequeños
    }

    private function createEducationAction(DevelopmentPath $path, Skill $skill, int $gap)
    {
        $intensity = $gap > 1 ? 'Intensivo' : 'Fundamentos';
        
        DevelopmentAction::create([
            'development_path_id' => $path->id,
            'title' => "Curso: {$skill->name} - Nivel {$intensity}",
            'description' => "Completar módulo de aprendizaje formal para adquirir bases teóricas de nivel {$intensity}.",
            'type' => 'training',
            'strategy' => 'build',
            'order' => 1,
            'status' => 'pending',
            'estimated_hours' => $gap > 1 ? 40 : 10,
            'impact_weight' => 0.10
        ]);
    }

    private function createExposureAction(DevelopmentPath $path, Skill $skill)
    {
        // Buscar mentor
        $mentors = $this->mentorService->findMentors($skill->id, 4, 1);
        $mentorName = $mentors->isNotEmpty() ? $mentors->first()->full_name : 'Experto Externo';

        DevelopmentAction::create([
            'development_path_id' => $path->id,
            'title' => "Mentoría con {$mentorName}",
            'description' => "Sesiones quincenales para revisar progreso y discutir casos prácticos con {$mentorName}.",
            'type' => 'mentoring',
            'strategy' => 'borrow',
            'order' => 2,
            'status' => 'pending',
            'estimated_hours' => 12, // 1 hora x 12 sesiones (6 meses)
            'impact_weight' => 0.20
        ]);
    }

    private function createExperienceAction(DevelopmentPath $path, Skill $skill, int $gap)
    {
        DevelopmentAction::create([
            'development_path_id' => $path->id,
            'title' => "Proyecto Práctico: Aplicación de {$skill->name}",
            'description' => "Asumir responsabilidad de un entregable clave que requiera el uso activo de {$skill->name} bajo supervisión.",
            'type' => 'project',
            'strategy' => 'build', // 'Apply' isn't an option, use 'build' (internal development) or 'practice' if available?
            // Migration has 'practice'. Let's use 'practice'.
            // Wait, schema says: enum('type', ['training', 'practice', 'project', 'mentoring'])
            // Strategy says: enum('strategy', ['build', 'buy', 'borrow', 'bot'])
            // So Type='project', Strategy='build' is valid.
            // Or Type='practice', Strategy='build'.
            // Project fits better here. Strategy 'build' fits "Internal development".
            'order' => 3,
            'status' => 'pending',
            'estimated_hours' => $gap > 1 ? 120 : 40,
            'impact_weight' => 0.70
        ]);
    }
}
