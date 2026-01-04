<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use App\Models\People;
use App\Services\GapAnalysisService;
use Illuminate\Http\JsonResponse;

class MarketplaceController extends Controller
{
    /**
     * Umbral mínimo de match para considerar a alguien como candidato viable
     * Candidatos con match < MINIMUM_MATCH_THRESHOLD no aparecen en el marketplace
     * 
     * TODO: Mover esto a configuración de organización cuando se implemente settings
     */
    private const MINIMUM_MATCH_THRESHOLD = 40;

    /**
     * Vista de candidato: Oportunidades para una persona específica
     */
    public function opportunities(int $peopleId): JsonResponse
    {
        $people = People::find($peopleId);
        if (! $people) {
            return response()->json(['error' => 'Peoplea no encontrada'], 404);
        }

        $openings = JobOpening::where('organization_id', $people->organization_id)
            ->where('status', 'open')
            ->where('role_id', '!=', $people->role_id) // Excluir vacantes del mismo rol actual
            ->with('role')
            ->get();

        $gapService = new GapAnalysisService();
        $opportunities = $openings->map(function ($opening) use ($people, $gapService) {
            $analysis = $gapService->calculate($people, $opening->role);
            
            // Filtrar oportunidades con match muy bajo (no viables)
            if ($analysis['match_percentage'] < self::MINIMUM_MATCH_THRESHOLD) {
                return null; // Se filtrará después
            }
            
            // Calcular tiempo hasta productividad (30 días por nivel de gap en promedio)
            $totalGapDays = collect($analysis['gaps'])
                ->where('gap', '>', 0)
                ->sum(fn($gap) => $gap['gap'] * 30);
            
            // Skills requeridas con niveles
            $requiredSkills = $opening->role->skills()
                ->withPivot(['required_level', 'is_critical'])
                ->get()
                ->map(fn($skill) => [
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'required_level' => $skill->pivot->required_level ?? 0,
                ]);

            return [
                'id' => $opening->id,
                'title' => $opening->title,
                'description' => $opening->role?->name . ' - ' . $opening->department,
                'role' => $opening->role?->name,
                'department' => $opening->department,
                'deadline' => $opening->deadline,
                'match_percentage' => $analysis['match_percentage'],
                'time_to_productivity' => $totalGapDays > 0 ? $totalGapDays : 15, // Mínimo 15 días de onboarding
                'category' => $analysis['summary']['category'],
                'missing_skills_count' => collect($analysis['gaps'])->where('gap', '>', 0)->count(),
                'required_skills' => $requiredSkills,
            ];
        })->filter()->sortByDesc('match_percentage')->values(); // filter() elimina nulls

        return response()->json([
            'data' => [
                'people' => [
                    'id' => $people->id,
                    'name' => $people->full_name ?? ($people->first_name . ' ' . $people->last_name),
                ],
                'opportunities' => $opportunities,
            ],
        ]);
    }

    /**
     * Vista de reclutador: Candidatos rankeados por posición abierta
     */
    public function recruiterView(): JsonResponse
    {
        $user = auth()->user();
        if (!$user || !$user->organization_id) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $openings = JobOpening::where('organization_id', $user->organization_id)
            ->where('status', 'open')
            ->with('role')
            ->get();

        $gapService = new GapAnalysisService();
        
        $positionsWithCandidates = $openings->map(function ($opening) use ($user, $gapService) {
            // Obtener todas las personas de la organización
            // EXCLUIR personas que ya ocupan el mismo rol que la vacante
            $people = People::where('organization_id', $user->organization_id)
                ->where('deleted_at', null)
                ->where('role_id', '!=', $opening->role_id) // Filtrar mismo rol
                ->get();

            // Calcular match para cada persona
            $candidates = $people->map(function ($person) use ($opening, $gapService) {
                $analysis = $gapService->calculate($person, $opening->role);
                
                // Excluir candidatos con match muy bajo (no viables)
                if ($analysis['match_percentage'] < self::MINIMUM_MATCH_THRESHOLD) {
                    return null; // Se filtrará después
                }
                
                $totalGapDays = collect($analysis['gaps'])
                    ->where('gap', '>', 0)
                    ->sum(fn($gap) => $gap['gap'] * 30);

                $matchPct = $analysis['match_percentage'];
                
                // Determinar nivel de match para categorización
                $matchLevel = 'very_low'; // <30%
                if ($matchPct >= 80) {
                    $matchLevel = 'excellent';
                } elseif ($matchPct >= 70) {
                    $matchLevel = 'high';
                } elseif ($matchPct >= 50) {
                    $matchLevel = 'medium';
                } elseif ($matchPct >= 30) {
                    $matchLevel = 'low';
                }

                return [
                    'id' => $person->id,
                    'name' => $person->full_name ?? ($person->first_name . ' ' . $person->last_name),
                    'current_role' => $person->role->name ?? 'Sin rol',
                    'match_percentage' => $matchPct,
                    'match_level' => $matchLevel,
                    'time_to_productivity' => $totalGapDays > 0 ? $totalGapDays : 15,
                    'category' => $analysis['summary']['category'],
                    'missing_skills_count' => collect($analysis['gaps'])->where('gap', '>', 0)->count(),
                    'critical_skills_missing' => collect($analysis['gaps'])
                        ->where('is_critical', true)
                        ->where('gap', '>', 0)
                        ->count(),
                ];
            })
            ->filter() // Eliminar nulls (candidatos bajo umbral)
            ->sortByDesc('match_percentage')
            ->values(); // Solo candidatos viables (≥40% match)

            // Agrupar candidatos por nivel de match para insights
            $candidatesByLevel = [
                'excellent' => $candidates->where('match_level', 'excellent')->count(),
                'high' => $candidates->where('match_level', 'high')->count(),
                'medium' => $candidates->where('match_level', 'medium')->count(),
                'low' => $candidates->where('match_level', 'low')->count(),
                'very_low' => $candidates->where('match_level', 'very_low')->count(),
            ];
            
            $topCandidate = $candidates->first();
            $recommendExternal = !$topCandidate || $topCandidate['match_percentage'] < 70;
            $urgentExternal = !$topCandidate || $topCandidate['match_percentage'] < 30;

            return [
                'id' => $opening->id,
                'title' => $opening->title,
                'role' => $opening->role?->name,
                'department' => $opening->department,
                'deadline' => $opening->deadline,
                'status' => $opening->status,
                'candidates' => $candidates,
                'total_candidates' => $candidates->count(), // Solo candidatos viables
                'total_people_evaluated' => $people->count(), // Total evaluado (incluye bajo umbral)
                'minimum_match_threshold' => self::MINIMUM_MATCH_THRESHOLD,
                'candidates_by_level' => $candidatesByLevel,
                'recommendation' => [
                    'search_external' => $recommendExternal,
                    'urgent_external' => $urgentExternal,
                    'best_match_pct' => $topCandidate ? $topCandidate['match_percentage'] : 0,
                ],
            ];
        })->values();

        return response()->json([
            'data' => [
                'positions' => $positionsWithCandidates,
            ],
        ]);
    }}