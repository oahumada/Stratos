<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use App\Models\People;
use App\Services\GapAnalysisService;
use Illuminate\Http\JsonResponse;

class MarketplaceController extends Controller
{
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
}
