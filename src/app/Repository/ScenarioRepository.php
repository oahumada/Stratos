<?php

namespace App\Repository;

use App\Models\Scenario;
    class ScenarioRepository
{
    public function findWithCapabilities(int $id)
    {
        return Scenario::with([
            'capabilities.skills.barsLevels',
            'owner'
        ])->findOrFail($id);
    }

    public function getActiveScenarios()
    {
        return Scenario::where('status', 'active')
                       ->with('owner')
                       ->get();
    }

    public function create(array $data)
    {
        return Scenario::create($data);
    }

    public function attachCapability(Scenario $scenario, int $capabilityId, array $pivotData)
    {
        $scenario->capabilities()->attach($capabilityId, $pivotData);
    }
}