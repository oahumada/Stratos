<?php

namespace App\Repository;

use App\Models\Scenario;

class ScenarioRepository
{
    public function findWithCapabilities(int $id)
    {
        return Scenario::with([
            'capabilities.competencies.skills',
            'owner',
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

    // Backwards-compatible methods expected by ScenarioController
    public function getScenarioById(int $id)
    {
        return Scenario::with([
            'owner',
            'capabilities.competencies.skills',
            'statusEvents',
            'parent',
        ])->find($id);
    }

    public function getScenariosByOrganization(int $organizationId, array $filters = [])
    {
        $query = Scenario::where('organization_id', $organizationId);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['fiscal_year'])) {
            $query->where('fiscal_year', $filters['fiscal_year']);
        }

        return $query->with(['owner', 'statusEvents'])->paginate(15);
    }

    public function createScenario(array $data)
    {
        return $this->create($data);
    }

    public function updateScenario(int $id, array $data)
    {
        $scenario = Scenario::findOrFail($id);
        $scenario->update($data);

        return $scenario;
    }
}
