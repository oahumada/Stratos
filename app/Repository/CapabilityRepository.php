<?php

// app/Repositories/CapabilityRepository.php

namespace App\Repository;

use App\Models\Capability;

class CapabilityRepository
{
    public function getForScenario(int $scenarioId)
    {
        return Capability::whereHas('scenarios', function ($query) use ($scenarioId) {
            $query->where('scenario_id', $scenarioId);
        })->with(['skills.barsLevels'])->get();
    }

    public function create(array $data)
    {
        return Capability::create($data);
    }
}
