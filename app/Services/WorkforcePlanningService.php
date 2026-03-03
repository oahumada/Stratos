<?php

namespace App\Services;

use App\Models\WorkforceScenario;
use App\Models\TalentStrategy;
use Illuminate\Support\Collection;

class WorkforcePlanningService
{
    public function getRecommendations(WorkforceScenario $scenario): Collection
    {
        // Dummy implementation for the sake of MVP (as guided in the documentation)
        return collect([
            [
                'role' => 'Analista de Datos',
                'demand' => 5,
                'internal_supply' => 3,
                'strategy_type' => 'BUILD',
                'action' => 'Movilidad interna (2 FTE)',
            ],
            [
                'role' => 'Analista de Datos',
                'demand' => 5,
                'internal_supply' => 3,
                'strategy_type' => 'BORROW',
                'action' => 'Contratación externa (1 FTE)',
            ],
            [
                'role' => 'Analista de Datos',
                'demand' => 5,
                'internal_supply' => 3,
                'strategy_type' => 'BUY',
                'action' => 'Reclutamiento directo (2 FTE)',
            ]
        ]);
    }
}
