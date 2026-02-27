<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssessmentSession;
use App\Models\DevelopmentPath;
use App\Models\People;
use App\Services\GapAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MiStratosController extends Controller
{
    protected GapAnalysisService $gapService;

    public function __construct(GapAnalysisService $gapService)
    {
        $this->gapService = $gapService;
    }

    /**
     * Dashboard personal — "Mi Stratos"
     * Retorna toda la data consolidada de la persona autenticada.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $person = People::with([
            'role.skills.competencies',
            'department',
            'skills',
            'psychometricProfiles',
            'developmentPaths.actions',
            'relations.relatedPerson',
        ])->where('user_id', $user->id)->first();

        if (!$person) {
            return response()->json([
                'success' => true,
                'data' => [
                    'person' => null,
                    'message' => 'No hay un perfil de colaborador asociado a este usuario.',
                ],
            ]);
        }

        // Gap Analysis
        $gapAnalysis = $person->role
            ? $this->gapService->calculate($person, $person->role)
            : null;

        // Competencies agrupadas
        $competencies = $this->groupSkillsByCompetency($person);

        // Learning progress
        $learningPaths = DevelopmentPath::with('actions')
            ->where('people_id', $person->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($path) => [
                'id' => $path->id,
                'title' => $path->title ?? $path->name ?? 'Ruta de Desarrollo',
                'status' => $path->status ?? 'active',
                'total_actions' => $path->actions->count(),
                'completed_actions' => $path->actions->where('status', 'completed')->count(),
                'progress' => $path->actions->count() > 0
                    ? round(($path->actions->where('status', 'completed')->count() / $path->actions->count()) * 100)
                    : 0,
                'created_at' => $path->created_at,
            ]);

        // Active assessment sessions (Conversaciones)
        $conversations = AssessmentSession::where('people_id', $person->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($session) => [
                'id' => $session->id,
                'type' => $session->type ?? 'evaluation',
                'status' => $session->status ?? 'pending',
                'created_at' => $session->created_at,
                'completed_at' => $session->completed_at ?? null,
            ]);

        // KPI summary
        $kpis = $this->calculateKPIs($person, $gapAnalysis, $learningPaths);

        // Psychometric snapshot
        $psychometricSnapshot = $person->psychometricProfiles->isNotEmpty()
            ? $person->psychometricProfiles->last()
            : null;

        return response()->json([
            'success' => true,
            'data' => [
                'person' => [
                    'id' => $person->id,
                    'first_name' => $person->first_name,
                    'last_name' => $person->last_name,
                    'full_name' => $person->full_name,
                    'email' => $person->email,
                    'photo_url' => $person->photo_url,
                    'hire_date' => $person->hire_date,
                    'is_high_potential' => $person->is_high_potential,
                    'department' => $person->department ? [
                        'id' => $person->department->id,
                        'name' => $person->department->name,
                    ] : null,
                    'role' => $person->role ? [
                        'id' => $person->role->id,
                        'name' => $person->role->name,
                        'archetype' => $person->role->archetype ?? null,
                        'mastery_level' => $person->role->mastery_level ?? null,
                    ] : null,
                ],
                'kpis' => $kpis,
                'gap_analysis' => $gapAnalysis,
                'competencies' => $competencies,
                'learning_paths' => $learningPaths,
                'conversations' => $conversations,
                'psychometric' => $psychometricSnapshot,
            ],
        ]);
    }

    /**
     * Calcula KPIs personales para el dashboard.
     */
    private function calculateKPIs(People $person, ?array $gapAnalysis, $learningPaths): array
    {
        // Potencial: basado en gap analysis match score
        $potentialScore = 0;
        if ($gapAnalysis && isset($gapAnalysis['summary']['match_percentage'])) {
            $potentialScore = (int) $gapAnalysis['summary']['match_percentage'];
        }

        // Readiness: habilidades cubiertas vs requeridas
        $readinessScore = 0;
        if ($person->role && $person->role->skills->count() > 0) {
            $totalRequired = $person->role->skills->count();
            $covered = $person->skills->count();
            $readinessScore = min(100, round(($covered / max(1, $totalRequired)) * 100));
        }

        // Learning progress: promedio de avance en rutas de aprendizaje
        $learningScore = 0;
        if ($learningPaths->count() > 0) {
            $learningScore = (int) round($learningPaths->avg('progress'));
        }

        // Skills count
        $skillsCount = $person->skills->count();

        // Gap count (skills con brecha)
        $gapCount = 0;
        if ($gapAnalysis && isset($gapAnalysis['gaps'])) {
            $gapCount = count(array_filter($gapAnalysis['gaps'], fn ($g) => ($g['gap'] ?? 0) > 0));
        }

        return [
            'potential' => $potentialScore,
            'readiness' => $readinessScore,
            'learning' => $learningScore,
            'skills_count' => $skillsCount,
            'gap_count' => $gapCount,
            'is_high_potential' => $person->is_high_potential,
        ];
    }

    /**
     * Agrupa skills por competencia para el perfil.
     */
    private function groupSkillsByCompetency(People $person): array
    {
        if (!$person->role) {
            return [];
        }

        $competencies = [];
        foreach ($person->role->skills as $skill) {
            foreach ($skill->competencies as $comp) {
                if (!isset($competencies[$comp->id])) {
                    $competencies[$comp->id] = [
                        'id' => $comp->id,
                        'name' => $comp->name,
                        'skills' => [],
                    ];
                }

                $pSkill = $person->skills->firstWhere('id', $skill->id);
                $competencies[$comp->id]['skills'][] = [
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'required_level' => $skill->pivot->required_level ?? 3,
                    'current_level' => $pSkill ? ($pSkill->pivot->current_level ?? 0) : 0,
                    'is_critical' => $skill->pivot->is_critical ?? false,
                ];
            }
        }

        return array_values($competencies);
    }
}
