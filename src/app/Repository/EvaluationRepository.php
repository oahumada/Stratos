<?php

namespace App\Repository;

use App\Models\Evaluation;
    class EvaluationRepository
{
    public function findByUserAndScenario(int $userId, int $scenarioId)
    {
        return Evaluation::where('user_id', $userId)
                         ->where('scenario_id', $scenarioId)
                         ->with(['skill.capability', 'responses.barsLevel', 'evidences'])
                         ->get();
    }

    public function create(array $data)
    {
        return Evaluation::create($data);
    }

    public function updateLevel(Evaluation $evaluation, float $currentLevel, float $gap, int $confidence)
    {
        $evaluation->update([
            'current_level' => $currentLevel,
            'gap' => $gap,
            'confidence_score' => $confidence,
            'evaluated_at' => now()
        ]);
    }
}