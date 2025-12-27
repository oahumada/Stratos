<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\Role;
use App\Models\Skill;
use App\Services\GapAnalysisService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function metrics(): JsonResponse
    {
        $totalPeople = Person::count();
        $totalSkills = Skill::count();
        $totalRoles = Role::count();

        // Brechas promedio por persona
        $gapService = new GapAnalysisService();
        $people = Person::with('skills')->get();
        $gaps = [];
        $matchPercentages = [];

        foreach ($people as $person) {
            $roles = Role::get();
            foreach ($roles as $role) {
                $analysis = $gapService->calculate($person, $role);
                $gaps[] = $analysis['gaps'];
                $matchPercentages[] = $analysis['match_percentage'];
            }
        }

        $avgMatch = count($matchPercentages) > 0 ? array_sum($matchPercentages) / count($matchPercentages) : 0;

        return response()->json([
            'summary' => [
                'total_people' => $totalPeople,
                'total_skills' => $totalSkills,
                'total_roles' => $totalRoles,
                'average_match_percentage' => round($avgMatch, 2),
            ],
        ]);
    }
}
