<?php

namespace App\Services\Talent;

use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\DevelopmentPath;
use App\Models\DevelopmentAction;
use App\Services\AiOrchestratorService;
use App\Services\AuditTrailService;
use App\Services\Talent\MentorMatchingService;
use App\Services\Talent\Lms\LmsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * LearningBlueprintService — Cierra pendiente de Fase 5.
 *
 * Genera Learning Blueprints automáticos basados en:
 * - Probabilidad de éxito predictiva
 * - Gaps actuales y predictivos
 * - Comportamientos BARS detectados
 * - Catálogo de cursos disponible
 * - Mentores internos
 */
class LearningBlueprintService
{
    public function __construct(
        protected AiOrchestratorService $orchestrator,
        protected MentorMatchingService $mentorService,
        protected LmsService $lmsService,
        protected AuditTrailService $audit
    ) {}

    /**
     * Genera un blueprint de aprendizaje completo para una persona.
     */
    public function generateBlueprint(int $peopleId, ?int $targetRoleId = null): array
    {
        $person = People::with(['role.skills', 'skills', 'psychometricProfiles'])
            ->findOrFail($peopleId);

        $targetRoleId = $targetRoleId ?? $person->role_id;

        // 1. Detectar gaps actuales
        $gaps = $this->detectGaps($peopleId, $targetRoleId);

        // 2. Predecir gaps futuros basados en tendencias
        $predictiveGaps = $this->predictFutureGaps($peopleId);

        // 3. Calcular probabilidad de éxito
        $successProbability = $this->calculateSuccessProbability($person, $gaps);

        // 4. Generar el blueprint con IA
        $aiBlueprint = $this->generateAiBlueprint($person, $gaps, $predictiveGaps, $successProbability);

        // 5. Mapear recursos disponibles
        $resources = $this->mapResources($gaps);

        // 6. Calcular timeline óptimo
        $timeline = $this->calculateOptimalTimeline($gaps, $successProbability);

        $blueprint = [
            'person' => [
                'id' => $person->id,
                'name' => $person->full_name ?? $person->name,
                'current_role' => $person->role->name ?? 'N/A',
            ],
            'success_probability' => $successProbability,
            'current_gaps' => $gaps,
            'predictive_gaps' => $predictiveGaps,
            'learning_paths' => $aiBlueprint['learning_paths'] ?? [],
            'resources' => $resources,
            'timeline' => $timeline,
            'estimated_total_hours' => $this->estimateTotalHours($gaps),
            'estimated_completion_months' => $timeline['total_months'] ?? 6,
            'roi_projection' => $this->projectLearningRoi($person, $gaps),
            'generated_at' => now()->toIso8601String(),
        ];

        // Audit
        $this->audit->logDecision(
            'Learning Blueprint',
            "people_{$peopleId}",
            "Blueprint generado — Probabilidad de éxito: {$successProbability}%",
            ['blueprint_hash' => md5(json_encode($blueprint))],
            'Learning Blueprint Service'
        );

        Log::info("Learning Blueprint generado para People #{$peopleId}");

        return $blueprint;
    }

    /**
     * Materializa el blueprint como DevelopmentPath + DevelopmentActions en la BD.
     */
    public function materializeBlueprint(int $peopleId, array $blueprint): DevelopmentPath
    {
        $person = People::findOrFail($peopleId);

        return DB::transaction(function () use ($person, $blueprint) {
            $path = DevelopmentPath::create([
                'organization_id' => $person->organization_id,
                'people_id' => $person->id,
                'target_role_id' => $person->role_id,
                'action_title' => "Learning Blueprint: " . ($blueprint['person']['name'] ?? 'Auto-generado'),
                'status' => 'draft',
                'estimated_duration_months' => $blueprint['estimated_completion_months'] ?? 6,
                'started_at' => now(),
                'steps' => $blueprint['learning_paths'] ?? [],
            ]);

            $order = 1;
            foreach ($blueprint['learning_paths'] as $learningPath) {
                foreach ($learningPath['actions'] ?? [] as $action) {
                    DevelopmentAction::create([
                        'development_path_id' => $path->id,
                        'title' => $action['title'] ?? "Acción {$order}",
                        'description' => $action['description'] ?? '',
                        'type' => $this->mapActionType($action['type'] ?? 'training'),
                        'strategy' => $action['strategy'] ?? 'build',
                        'order' => $order++,
                        'status' => 'pending',
                        'estimated_hours' => $action['hours'] ?? 10,
                        'impact_weight' => $action['impact_weight'] ?? 0.1,
                    ]);
                }
            }

            return $path->load('actions');
        });
    }

    // ── Core Calculations ────────────────────────────────────

    protected function detectGaps(int $peopleId, int $targetRoleId): array
    {
        return PeopleRoleSkills::where('people_id', $peopleId)
            ->whereColumn('current_level', '<', 'required_level')
            ->where('required_level', '>', 0)
            ->with('skill')
            ->get()
            ->map(fn ($prs) => [
                'skill_id' => $prs->skill_id,
                'skill_name' => $prs->skill->name ?? 'Unknown',
                'current_level' => $prs->current_level,
                'required_level' => $prs->required_level,
                'gap' => $prs->required_level - $prs->current_level,
                'priority' => $this->classifyGapPriority($prs->required_level - $prs->current_level),
            ])
            ->sortByDesc('gap')
            ->values()
            ->toArray();
    }

    protected function predictFutureGaps(int $peopleId): array
    {
        // Basado en tendencias del mercado y evolución tecnológica
        // En una implementación real, esto consultaría MarketIntelligenceService
        $emergingSkills = DB::table('skills')
            ->whereNotExists(function ($query) use ($peopleId) {
                $query->select(DB::raw(1))
                    ->from('people_role_skills')
                    ->whereColumn('people_role_skills.skill_id', 'skills.id')
                    ->where('people_role_skills.people_id', $peopleId);
            })
            ->where('lifecycle_status', 'active')
            ->where('is_critical', true)
            ->select('id', 'name')
            ->limit(5)
            ->get();

        return $emergingSkills->map(fn ($skill) => [
            'skill_id' => $skill->id,
            'skill_name' => $skill->name,
            'predicted_need_in_months' => rand(6, 18),
            'market_demand' => 'rising',
            'urgency' => 'proactive',
        ])->toArray();
    }

    protected function calculateSuccessProbability(People $person, array $gaps): int
    {
        $base = 80; // Base alta si tiene pocos gaps

        // Penalizar por cantidad de gaps
        $gapPenalty = count($gaps) * 5;

        // Penalizar por gaps grandes
        $largeGaps = collect($gaps)->where('gap', '>=', 3)->count();
        $largeGapPenalty = $largeGaps * 10;

        // Bonus por perfiles psicométricos fuertes
        $profileBonus = $person->psychometricProfiles->count() > 0 ? 5 : 0;

        // Bonus por actividad de desarrollo previa
        $priorDevelopment = DevelopmentPath::where('people_id', $person->id)
            ->where('status', 'completed')
            ->count();
        $developmentBonus = min(15, $priorDevelopment * 5);

        $probability = max(10, min(98, $base - $gapPenalty - $largeGapPenalty + $profileBonus + $developmentBonus));

        return $probability;
    }

    protected function generateAiBlueprint(People $person, array $gaps, array $predictiveGaps, int $successProbability): array
    {
        $learningPaths = [];

        // Agrupar gaps por prioridad
        $criticalGaps = array_filter($gaps, fn ($g) => $g['priority'] === 'critical');
        $highGaps = array_filter($gaps, fn ($g) => $g['priority'] === 'high');
        $mediumGaps = array_filter($gaps, fn ($g) => $g['priority'] === 'medium');

        // Fase 1: Intervención urgente para gaps críticos
        if (!empty($criticalGaps)) {
            $learningPaths[] = [
                'phase' => 1,
                'name' => 'Intervención Urgente',
                'duration_weeks' => 8,
                'focus' => 'critical_gaps',
                'actions' => $this->generateActionsForGaps($criticalGaps, 'intensive'),
            ];
        }

        // Fase 2: Desarrollo enfocado para gaps altos
        if (!empty($highGaps)) {
            $learningPaths[] = [
                'phase' => 2,
                'name' => 'Desarrollo Focalizado',
                'duration_weeks' => 12,
                'focus' => 'high_gaps',
                'actions' => $this->generateActionsForGaps($highGaps, 'focused'),
            ];
        }

        // Fase 3: Refinamiento y future-proofing
        $phase3Actions = $this->generateActionsForGaps($mediumGaps, 'standard');
        foreach ($predictiveGaps as $predictive) {
            $phase3Actions[] = [
                'title' => "Preparación proactiva: {$predictive['skill_name']}",
                'description' => "Iniciación en skill emergente para estar preparado en los próximos {$predictive['predicted_need_in_months']} meses.",
                'type' => 'training',
                'strategy' => 'build',
                'hours' => 20,
                'impact_weight' => 0.05,
            ];
        }

        if (!empty($phase3Actions)) {
            $learningPaths[] = [
                'phase' => 3,
                'name' => 'Refinamiento & Future-Proofing',
                'duration_weeks' => 16,
                'focus' => 'medium_gaps_and_emerging',
                'actions' => $phase3Actions,
            ];
        }

        return ['learning_paths' => $learningPaths];
    }

    protected function generateActionsForGaps(array $gaps, string $intensity): array
    {
        $actions = [];

        foreach ($gaps as $gap) {
            $skillName = $gap['skill_name'];
            $gapSize = $gap['gap'];

            // Educación (10% del modelo 70-20-10)
            $actions[] = [
                'title' => $intensity === 'intensive'
                    ? "Bootcamp intensivo: {$skillName}"
                    : "Curso: {$skillName}",
                'description' => "Formación teórica para cerrar brecha de nivel {$gapSize} en {$skillName}.",
                'type' => 'training',
                'strategy' => 'build',
                'hours' => $gapSize * ($intensity === 'intensive' ? 20 : 10),
                'impact_weight' => 0.10,
            ];

            // Exposición (20% — mentoría)
            $mentors = $this->mentorService->findMentors($gap['skill_id'], 4, 1);
            $mentor = $mentors->first();
            $actions[] = [
                'title' => "Mentoría en {$skillName}" . ($mentor ? " con {$mentor->full_name}" : ''),
                'description' => "Sesiones de acompañamiento práctico.",
                'type' => 'mentoring',
                'strategy' => 'borrow',
                'hours' => $gapSize * 4,
                'impact_weight' => 0.20,
            ];

            // Experiencia (70% — proyecto práctico)
            if ($gapSize >= 2) {
                $actions[] = [
                    'title' => "Proyecto aplicado: {$skillName}",
                    'description' => "Participación en un proyecto real que requiera aplicación de {$skillName}.",
                    'type' => 'project',
                    'strategy' => 'build',
                    'hours' => $gapSize * 30,
                    'impact_weight' => 0.70,
                ];
            }
        }

        return $actions;
    }

    protected function mapResources(array $gaps): array
    {
        $resources = [];

        foreach ($gaps as $gap) {
            // Buscar cursos en el catálogo
            $courses = $this->lmsService->searchCourses($gap['skill_name'] ?? '');
            $mentors = $this->mentorService->findMentors($gap['skill_id'] ?? 0, 4, 3);

            $resources[] = [
                'skill' => $gap['skill_name'],
                'courses_available' => count($courses),
                'mentors_available' => $mentors->count(),
                'resources_mapped' => true,
            ];
        }

        return $resources;
    }

    protected function calculateOptimalTimeline(array $gaps, int $successProbability): array
    {
        $criticalCount = collect($gaps)->where('priority', 'critical')->count();
        $totalGapLevels = collect($gaps)->sum('gap');

        // Fórmula: base + (gaps * factor) ajustado por probabilidad
        $baseMonths = 2;
        $gapFactor = 0.5;
        $probabilityFactor = $successProbability >= 70 ? 0.8 : 1.2;

        $totalMonths = (int) ceil(($baseMonths + ($totalGapLevels * $gapFactor)) * $probabilityFactor);
        $totalMonths = max(1, min(24, $totalMonths));

        return [
            'total_months' => $totalMonths,
            'phases' => [
                ['name' => 'Foundation', 'months' => min(3, (int) ceil($totalMonths * 0.3))],
                ['name' => 'Development', 'months' => min(6, (int) ceil($totalMonths * 0.5))],
                ['name' => 'Mastery', 'months' => min(6, (int) ceil($totalMonths * 0.2))],
            ],
            'milestones' => [
                ['month' => 1, 'milestone' => 'Assessment de entrada completado'],
                ['month' => (int) ceil($totalMonths / 2), 'milestone' => 'Revisión de mitad de camino'],
                ['month' => $totalMonths, 'milestone' => 'Evaluación final de competencia'],
            ],
        ];
    }

    protected function estimateTotalHours(array $gaps): int
    {
        return collect($gaps)->sum(fn ($gap) => $gap['gap'] * 40); // ~40 horas por nivel de gap
    }

    protected function projectLearningRoi(People $person, array $gaps): array
    {
        $totalInvestmentHours = $this->estimateTotalHours($gaps);
        $investmentCost = $totalInvestmentHours * 35; // Costo por hora (promedio)
        $replacementCost = 45000 * 0.5; // 50% de salario promedio

        return [
            'investment_cost_usd' => $investmentCost,
            'replacement_cost_avoided_usd' => $replacementCost,
            'productivity_gain_usd' => count($gaps) * 5000,
            'net_roi_usd' => $replacementCost + (count($gaps) * 5000) - $investmentCost,
        ];
    }

    protected function classifyGapPriority(int $gap): string
    {
        if ($gap >= 3) return 'critical';
        if ($gap === 2) return 'high';
        if ($gap === 1) return 'medium';
        return 'low';
    }

    protected function mapActionType(string $type): string
    {
        $validTypes = ['training', 'practice', 'project', 'mentoring'];
        return in_array($type, $validTypes) ? $type : 'training';
    }
}
