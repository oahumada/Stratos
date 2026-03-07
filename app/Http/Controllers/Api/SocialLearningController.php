<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Services\Talent\SocialLearningService;
use Illuminate\Http\Request;

class SocialLearningController extends Controller
{
    public function __construct(
        protected SocialLearningService $socialLearning
    ) {}

    /**
     * Dashboard de "Knowledge Transfer" y Riesgos de Continuidad.
     */
    public function dashboard()
    {
        $risks = $this->socialLearning->identifyContinuityRisks();

        // Sugerencias para mitigar cada riesgo
        $mitigations = $risks->map(function ($risk) {
            $suggestions = $this->socialLearning->suggestMatches($risk['skill']->id);

            return [
                'person_at_risk' => $risk['person'],
                'skill_critical' => $risk['skill'],
                'risk_score' => $risk['risk_score'],
                'potential_mentees' => $suggestions->map(fn ($s) => [
                    'mentee' => $s['mentee'],
                    'match_score' => $s['match_score'],
                    'type' => $s['type'],
                ]),
            ];
        });

        return response()->json([
            'ContinuityRisks' => $mitigations,
            'GlobalMarketCrossPollination' => rand(60, 95), // Mock
        ]);
    }

    /**
     * Propuesta de emparejamiento para una skill específica.
     */
    public function matches(int $skillId)
    {
        $matches = $this->socialLearning->suggestMatches($skillId);

        return response()->json($matches);
    }

    /**
     * Generar Blueprint AI para una relación mentor-mentee.
     */
    public function generateBlueprint(Request $request)
    {
        $data = $request->validate([
            'mentor_id' => 'required|exists:people,id',
            'mentee_id' => 'required|exists:people,id',
            'skill_id' => 'required|exists:skills,id',
        ]);

        $blueprint = $this->socialLearning->createMentorshipBlueprint(
            $data['mentor_id'],
            $data['mentee_id'],
            $data['skill_id']
        );

        return response()->json($blueprint);
    }
}
