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
            ->with('role')
            ->get();

        $gapService = new GapAnalysisService();
        $opportunities = $openings->map(function ($opening) use ($people, $gapService) {
            $analysis = $gapService->calculate($people, $opening->role);
            
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
        })->sortByDesc('match_percentage')->values();

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
            $people = People::where('organization_id', $user->organization_id)
                ->where('deleted_at', null)
                ->get();

            // Calcular match para cada persona
            $candidates = $people->map(function ($person) use ($opening, $gapService) {
                $analysis = $gapService->calculate($person, $opening->role);
                
                $totalGapDays = collect($analysis['gaps'])
                    ->where('gap', '>', 0)
                    ->sum(fn($gap) => $gap['gap'] * 30);

                return [
                    'id' => $person->id,
                    'name' => $person->full_name ?? ($person->first_name . ' ' . $person->last_name),
                    'current_role' => $person->role->name ?? 'Sin rol',
                    'match_percentage' => $analysis['match_percentage'],
                    'time_to_productivity' => $totalGapDays > 0 ? $totalGapDays : 15,
                    'category' => $analysis['summary']['category'],
                    'missing_skills_count' => collect($analysis['gaps'])->where('gap', '>', 0)->count(),
                ];
            })
            ->sortByDesc('match_percentage')
            ->values()
            ->take(10); // Top 10 candidatos

            return [
                'id' => $opening->id,
                'title' => $opening->title,
                'role' => $opening->role?->name,
                'department' => $opening->department,
                'deadline' => $opening->deadline,
                'status' => $opening->status,
                'candidates' => $candidates,
                'total_candidates' => $people->count(),
            ];
        })->values();

        return response()->json([
            'data' => [
                'positions' => $positionsWithCandidates,
            ],
        ]);
    }
