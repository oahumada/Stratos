<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\People;
use App\Models\Roles;
use App\Models\Skill;
use App\Services\GapAnalysisService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function metrics(): JsonResponse
    {
        $totalPeople = People::count();
        $totalSkills = Skill::count();
        $totalRoles = Roles::count();

        // Brechas promedio por peoplea
        $gapService = new GapAnalysisService();
        $People = People::with('skills')->get();
        $gaps = [];
        $matchPercentages = [];

        foreach ($People as $people) {
            $roles = Roles::get();
            foreach ($roles as $role) {
                $analysis = $gapService->calculate($people, $role);
                $gaps[] = $analysis['gaps'];
                $matchPercentages[] = $analysis['match_percentage'];
            }
        }

        $avgMatch = count($matchPercentages) > 0 ? array_sum($matchPercentages) / count($matchPercentages) : 0;

        return response()->json([
            'summary' => [
                'total_People' => $totalPeople,
                'total_skills' => $totalSkills,
                'total_roles' => $totalRoles,
                'average_match_percentage' => round($avgMatch, 2),
            ],
        ]);
    }
}
