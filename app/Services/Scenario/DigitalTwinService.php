<?php

namespace App\Services\Scenario;

use App\Models\Organization;
use App\Models\People;
use App\Models\Roles;
use App\Models\Skill;
use Illuminate\Support\Facades\Log;

class DigitalTwinService
{
    /**
     * Exporta el estado actual de la organización como un Grafo Semántico comprimido.
     * Este JSON es lo que consumen los Agentes de Scenario IQ para sus simulaciones.
     */
    public function captureState(Organization $org): array
    {
        Log::info("Capturando Digital Twin para la organización: {$org->id}");

        return [
            'org_metadata' => [
                'id' => $org->id,
                'total_headcount' => $org->people()->count(),
                'sectors' => $org->departments()->pluck('name')->toArray(),
            ],
            'nodes' => [
                'people' => $this->capturePeople($org),
                'roles' => $this->captureRoles($org),
            ],
            'edges' => [
                'hierarchies' => $this->captureHierarchies($org),
                'skill_mesh' => $this->captureSkillMesh($org),
            ]
        ];
    }

    private function capturePeople(Organization $org): array
    {
        return $org->people()
            ->with(['role', 'skills'])
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'role' => $p->role?->name,
                'performance_score' => $p->metadata['last_performance_score'] ?? 0.8,
                'potential_level' => $p->metadata['overall_potential'] ?? 'B',
                'skills' => $p->skills->pluck('name')->toArray(),
            ])
            ->toArray();
    }

    private function captureRoles(Organization $org): array
    {
        return Roles::where('organization_id', $org->id)
            ->with('skills')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'criticality' => $r->metadata['is_critical'] ?? false,
                'required_skills' => $r->skills->pluck('name')->toArray(),
            ])
            ->toArray();
    }

    private function captureHierarchies(Organization $org): array
    {
        // Simplificado: Relación Jefe-Subordinado
        return \DB::table('people')
            ->where('organization_id', $org->id)
            ->whereNotNull('manager_id')
            ->select('id as source', 'manager_id as target', \DB::raw("'manages' as relation"))
            ->get()
            ->toArray();
    }

    private function captureSkillMesh(Organization $org): array
    {
        // Conecta personas con sus top skills
        return \DB::table('people_role_skills')
            ->join('people', 'people.id', '=', 'people_role_skills.people_id')
            ->where('people.organization_id', $org->id)
            ->select('people_id as source', 'skill_id as target', 'level as strength')
            ->get()
            ->toArray();
    }
}
