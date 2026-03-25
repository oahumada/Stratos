<?php

namespace App\Services\Talent;

use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\PersonMovement;
use App\Models\Roles;
use App\Models\SuccessionPlan;
use App\Models\User;
use App\Services\Scenario\CareerPathService;
use Illuminate\Support\Collection;

/**
 * SuccessionPlanningService
 *
 * Calcula predicciones de sucesión basadas en:
 * - Trayectoria histórica (PersonMovement)
 * - Velocidad de aprendizaje (Cierre de Gaps en PeopleRoleSkills)
 * - Estabilidad (Frecuencia de cambios)
 * - Fit de Competencias (CareerPathService)
 */
class SuccessionPlanningService
{
    protected CareerPathService $careerService;

    protected MobilitySimulationService $mobilityService;

    protected \App\Services\Talent\Lms\LmsService $lmsService;

    public function __construct(
        CareerPathService $careerService, MobilitySimulationService $mobilityService,
        \App\Services\Talent\Lms\LmsService $lmsService
    ) {
        $this->careerService = $careerService;
        $this->mobilityService = $mobilityService;
        $this->lmsService = $lmsService;
    }

    /**
     * Obtiene los mejores sucesores potenciales para un rol.
     */
    public function getSuccessorsForRole(int $roleId, int $limit = 5): Collection
    {
        $role = Roles::with('skills')->findOrFail($roleId);
        $orgId = $role->organization_id;

        // Buscamos candidatos en toda la organización excepto los que ya tienen ese rol
        $candidates = People::where('organization_id', $orgId)
            ->where('role_id', '!=', $roleId)
            ->get();

        return $candidates->map(function ($person) use ($role) {
            /** @var People $person */
            return $this->analyzeSuccessionReadiness($person, $role);
        })
            ->sortByDesc('readiness_score')
            ->take($limit)
            ->values();
    }

    /**
     * Analiza profundamente a una persona para un rol de sucesión.
     */
    public function analyzeSuccessionReadiness(People $person, Roles $targetRole): array
    {
        // 1. Skill Match Score (40% de peso)
        $careerEval = $this->careerService->predictTransitionSuccess($person->id, $targetRole->id);
        $matchScore = $careerEval['match_score'];

        // 2. Stability Score (20% de peso) - Basado en PersonMovement
        $stabilityScore = $this->calculateIndividualStability($person->id);

        // 3. Learning Velocity (25% de peso) - Basado en cierre de gaps histórico
        $velocityScore = $this->calculateLearningVelocity($person->id);

        // 4. Trajectory Match (15% de peso) - Alineación de carrera previa
        $trajectoryScore = $this->calculateTrajectoryFit($person, $targetRole);

        // 5. Legacy Risk - Riesgo para el equipo de origen si esta persona se mueve
        $legacyRisk = $this->mobilityService->calculateLegacyRisk($person);

        // Score Final Ponderado
        $finalScore = ($matchScore * 0.40)
                     + ($stabilityScore * 0.20)
                     + ($velocityScore * 0.25)
                     + ($trajectoryScore * 0.15);

        // Ajuste por High Potential (Bono estratégico)
        if ($person->is_high_potential) {
            $finalScore = min(100, $finalScore + 10);
        }

        return [
            'person' => [
                'id' => $person->id,
                'name' => $person->full_name,
                'email' => $person->email,
                'current_role' => $person->role->name ?? 'N/A',
                'photo_url' => $person->photo_url,
                'is_high_potential' => $person->is_high_potential,
            ],
            'readiness_score' => round($finalScore, 2),
            'readiness_level' => $this->mapReadinessToLevel($finalScore),
            'estimated_months' => $careerEval['estimated_preparation_months'],
            'metrics' => [
                'skill_match' => $matchScore,
                'stability' => $stabilityScore,
                'learning_velocity' => $velocityScore,
                'trajectory_fit' => $trajectoryScore,
                'legacy_risk' => round($legacyRisk * 100, 2),
            ],
            'gaps' => $careerEval['gaps_to_close'],
            'trajectory_summary' => $this->summarizeTrajectory($person->id),
            'potential_replacements' => $this->getReplacementsForPerson($person),
            'recommended_courses' => $this->getRecommendedCoursesForGaps($careerEval['gaps_to_close']),
        ];
    }

    /**
     * Busca cursos en el LMS para cerrar los gaps detectados.
     */
    protected function getRecommendedCoursesForGaps(array $gaps): array
    {
        $recommendations = [];

        // Tomamos los 2-3 gaps más críticos
        $criticalGaps = array_slice($gaps, 0, 3);

        foreach ($criticalGaps as $gap) {
            $courses = $this->lmsService->searchCourses($gap['skill_name']);
            if (! empty($courses)) {
                // Tomar el primer mejor resultado de cada skill
                $recommendations[] = [
                    'skill' => $gap['skill_name'],
                    'course' => $courses[0],
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Identifica quién podría cubrir el puesto de una persona si esta es promocionada.
     */
    protected function getReplacementsForPerson(People $person): Collection
    {
        if (! $person->role_id) {
            return collect();
        }

        // Buscamos los mejores candidatos para el rol actual de la persona
        return $this->getSuccessorsForRole($person->role_id, 3)
            ->map(function ($candidate) {
                return [
                    'id' => $candidate['person']['id'],
                    'name' => $candidate['person']['name'],
                    'photo_url' => $candidate['person']['photo_url'],
                    'readiness_score' => $candidate['readiness_score'],
                    'readiness_level' => $candidate['readiness_level'],
                ];
            });
    }

    /**
     * Calcula qué tan estable ha sido el empleado en el último año.
     */
    protected function calculateIndividualStability(int $personId): int
    {
        $movementsCount = PersonMovement::where('person_id', $personId)
            ->where('movement_date', '>=', now()->subYear())
            ->whereIn('type', ['transfer', 'lateral_move', 'exit'])
            ->count();

        $score = 40;
        if ($movementsCount === 0) {
            $score = 100;
        } elseif ($movementsCount === 1) {
            $score = 90;
        } elseif ($movementsCount === 2) {
            $score = 70;
        }

        return $score;
    }

    /**
     * Calcula la velocidad de aprendizaje basada en skills subidas de nivel recientemente.
     */
    protected function calculateLearningVelocity(int $personId): int
    {
        // En un sistema real, miraríamos el historial de evaluaciones.        // Como proxy, usamos el promedio de progreso en PeopleRoleSkills activos.
        $skills = PeopleRoleSkills::where('people_id', $personId)
            ->where('is_active', true)
            ->get();

        if ($skills->isEmpty()) {
            return 50;
        }

        $progressCount = 0;
        foreach ($skills as $s) {
            if ($s->current_level >= $s->required_level) {
                $progressCount++;
            }
        }

        $baseVelocity = ($progressCount / $skills->count()) * 100;

        // Si ha tenido promociones recientes, su velocidad percibida es mayor
        $promotions = PersonMovement::where('person_id', $personId)
            ->where('type', 'promotion')
            ->where('movement_date', '>=', now()->subMonths(18))
            ->count();

        return (int) min(100, $baseVelocity + ($promotions * 20));
    }

    /**
     * Calcula si su trayectoria previa lo acerca naturalmente al rol objetivo.
     */
    protected function calculateTrajectoryFit(People $person, Roles $targetRole): int
    {
        $score = 0;

        // Mismo departamento: +40
        if ($person->department_id === $targetRole->department_id) {
            $score += 40;
        }

        // Si ya ha pasado por roles que son "stepping stones" para este rol
        // Para este MVP, chequeamos si ha tenido al menos una promoción en la empresa
        $hasPromotions = PersonMovement::where('person_id', $person->id)
            ->where('type', 'promotion')
            ->exists();

        if ($hasPromotions) {
            $score += 30;
        }

        // Si tiene antigüedad mayor a 2 años (Experiencia acumulada)
        if ($person->hire_date && $person->hire_date->diffInYears(now()) >= 2) {
            $score += 30;
        }

        return min(100, $score);
    }

    protected function summarizeTrajectory(int $personId): array
    {
        $movements = PersonMovement::where('person_id', $personId)
            ->orderBy('movement_date', 'desc')
            ->get();

        return [
            'total_movements' => $movements->count(),
            'last_movement' => $movements->first()->type ?? 'none',
            'years_in_org' => People::find($personId)->hire_date?->diffInYears(now()) ?? 0,
        ];
    }

    protected function mapReadinessToLevel(float $score): string
    {
        $level = 'Potencial Emergente (2y+)';

        if ($score >= 85) {
            $level = 'Listo Ahora (Successor A)';
        } elseif ($score >= 70) {
            $level = 'Listo Corto Plazo (6-12m)';
        } elseif ($score >= 50) {
            $level = 'Desarrollo Necesario (1-2y)';
        }

        return $level;
    }

    /**
     * Persiste un plan de sucesión.
     */
    public function saveSuccessionPlan(array $data, User $actor): SuccessionPlan
    {
        return SuccessionPlan::create([
            'organization_id' => $actor->organization_id,
            'person_id' => $data['person_id'],
            'target_role_id' => $data['target_role_id'],
            'readiness_score' => $data['readiness_score'],
            'readiness_level' => $data['readiness_level'],
            'estimated_months' => $data['estimated_months'],
            'metrics' => $data['metrics'] ?? [],
            'gaps' => $data['gaps'] ?? [],
            'suggested_courses' => $data['suggested_courses'] ?? [],
            'created_by' => $actor->id,
            'status' => 'active',
        ]);
    }
}
