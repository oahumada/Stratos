<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\People;
use App\Models\ScenarioRole;
use App\Models\SuccessionPlan;
use App\Services\GapAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PeopleProfileController extends Controller
{
    protected $gapService;

    public function __construct(GapAnalysisService $gapService)
    {
        $this->gapService = $gapService;
    }

    public function show($id): JsonResponse
    {
        $person = People::with([
            'role.skills.competencies',
            'department',
            'skills',
            'psychometricProfiles',
            'developmentPaths.actions',
            'relations.relatedPerson'
        ])->findOrFail($id);

        $gapAnalysis = $person->role ? $this->gapService->calculate($person, $person->role) : null;
        $scenarios = $this->getStrategicScenarios($person);
        $competencies = $this->groupSkillsByCompetency($person);

        return response()->json([
            'success' => true,
            'data' => [
                'person' => $person,
                'gap_analysis' => $gapAnalysis,
                'competencies' => $competencies,
                'scenarios' => $scenarios,
                'succession_status' => null,
            ]
        ]);
    }

    private function getStrategicScenarios(People $person): array
    {
        if (!$person->role_id) {
            return [];
        }

        return ScenarioRole::with('scenario')
            ->where('role_id', $person->role_id)
            ->get()
            ->map(fn($sr) => [
                'id' => $sr->scenario->id,
                'name' => $sr->scenario->name,
                'status' => $sr->scenario->status,
                'impact_level' => $sr->impact_level,
                'evolution_type' => $sr->evolution_type,
            ])->toArray();
    }

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
                        'skills' => []
                    ];
                }
                
                $pSkill = $person->skills->firstWhere('id', $skill->id);
                $competencies[$comp->id]['skills'][] = [
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'required_level' => $skill->pivot->required_level,
                    'current_level' => $pSkill ? ($pSkill->pivot->current_level ?? 0) : 0,
                    'is_critical' => $skill->pivot->is_critical
                ];
            }
        }
        return array_values($competencies);
    }
}
