<?php

namespace App\Services;

use App\Models\JobOpening;
use App\Models\People;
use Illuminate\Support\Collection;

class MatchingService
{
    /**
     * Rankea candidatos internos para una vacante.
     * Retorna una colección ordenada por `match_percentage` DESC.
     */
    public function rankCandidatesForOpening(JobOpening $jobOpening): Collection
    {
        $role = $jobOpening->role; // Relación a Role
        $gapService = new GapAnalysisService();

        // Obtener peopleas de la misma organización
        $People = People::where('organization_id', $jobOpening->organization_id)->get();

        $results = collect();

        foreach ($People as $people) {
            $analysis = $gapService->calculate($people, $role);

            $gaps = collect($analysis['gaps']);
            $gapCount = (int) $gaps->where('gap', '>', 0)->count();
            $criticalCount = (int) $gaps->where('gap', '>', 2)->count();
            $mediumCount = (int) $gaps->filter(fn($g) => ($g['gap'] ?? 0) >= 1 && ($g['gap'] ?? 0) <= 2)->count();

            $missingSkills = $gaps->where('gap', '>', 0)->pluck('skill_name')->values()->all();

            // Métricas
            $match = (float) ($analysis['match_percentage'] ?? 0.0);
            $timeToProductivity = 1.0 + ($gapCount * 0.5); // meses

            // risk_factor: ponderar gaps críticos y medios
            $risk = min(100, ($criticalCount * 25) + ($mediumCount * 10));

            $results->push([
                'people_id' => $people->id,
                'name' => $people->full_name ?? ($people->first_name . ' ' . $people->last_name),
                'current_role_id' => $people->current_role_id,
                'match_percentage' => round($match, 2),
                'missing_skills' => $missingSkills,
                'time_to_productivity_months' => round($timeToProductivity, 1),
                'risk_factor' => $risk,
            ]);
        }

        return $results->sortByDesc('match_percentage')->values();
    }
}
