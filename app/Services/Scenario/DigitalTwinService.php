<?php

namespace App\Services\Scenario;

use App\Models\Organization;
use App\Models\Roles;
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
        $people = $this->capturePeople($org);

        return [
            'org_metadata' => [
                'id' => $org->id,
                'total_headcount' => count($people),
                'sectors' => $org->departments()->pluck('name')->toArray(),
            ],
            'nodes' => [
                'people' => $people,
                'roles' => $this->captureRoles($org),
            ],
            'edges' => [
                'hierarchies' => $this->captureHierarchies($org),
                'skill_mesh' => $this->captureSkillMesh($org, $people),
            ],
        ];
    }

    private function capturePeople(Organization $org): array
    {
        return $org->people()
            ->with(['role', 'skills'])
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'department_id' => $p->department_id,
                'role' => $p->role?->name,
                'performance_score' => $p->metadata['last_performance_score'] ?? 0.8,
                'potential_level' => $p->metadata['overall_potential'] ?? 'B',
                'skills' => $p->skills->pluck('name')->toArray(),
                'skill_ids' => $p->skills->pluck('id')->toArray(),
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
        // Relación Jefe-Subordinado vía tabla de relaciones
        return \DB::table('people_relationships')
            ->join('people', 'people.id', '=', 'people_relationships.person_id')
            ->where('people.organization_id', $org->id)
            ->where('people_relationships.relationship_type', 'manager')
            ->select('person_id as source', 'related_person_id as target', \DB::raw("'manages' as relation"))
            ->get()
            ->toArray();
    }

    private function captureSkillMesh(Organization $org, array $people): array
    {
        // Reutiliza la información ya cargada en capturePeople para evitar otra consulta masiva
        $edges = [];

        foreach ($people as $p) {
            $personId = $p['id'];
            $skillIds = $p['skill_ids'] ?? [];

            foreach ($skillIds as $skillId) {
                $edges[] = [
                    'source' => $personId,
                    'target' => $skillId,
                    'strength' => 1, // valor por defecto; detalle fino requiere pivot
                ];
            }
        }

        return $edges;
    }
}
