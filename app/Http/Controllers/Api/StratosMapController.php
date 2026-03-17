<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departments;
use App\Models\People;
use Illuminate\Http\Request;

class StratosMapController extends Controller
{
    /**
     * Obtiene los datos para los Nodos Gravitacionales (Departamentos)
     */
    public function getGravitationalData()
    {
        $orgId = auth()->user()->organization_id;

        $departments = Departments::withCount('People')
            ->where('organization_id', $orgId)
            ->get();

        $nodes = $departments->map(function ($dept) {
            return [
                'id' => 'dept_' . $dept->id,
                'name' => $dept->name,
                'type' => 'department',
                'mass' => $dept->people_count ?: 1, // Headcount
                'value' => $dept->payroll_total,    // Payroll
                'parentId' => $dept->parent_id ? 'dept_' . $dept->parent_id : null,
                'color' => '#6366f1'
            ];
        });

        $links = [];
        foreach ($departments as $dept) {
            if ($dept->parent_id) {
                $links[] = [
                    'source' => 'dept_' . $dept->parent_id,
                    'target' => 'dept_' . $dept->id,
                    'value' => 1
                ];
            }
        }

        return response()->json([
            'nodes' => $nodes,
            'links' => $links
        ]);
    }

    /**
     * Obtiene los datos para el Mapa Cerberos (Jerarquía de Personas)
     */
    public function getCerberosData(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $centerId = $request->query('person_id');

        $query = People::with(['department', 'role', 'supervisor', 'relations.relatedPerson'])
            ->where('organization_id', $orgId);

        if ($centerId) {
            $focalPerson = People::find($centerId);
            if ($focalPerson) {
                $query->where(function($q) use ($centerId, $focalPerson) {
                    $q->where('id', $centerId) // Ella misma
                      ->orWhere('supervised_by', $centerId) // Sus subordinados
                      ->orWhere('id', $focalPerson->supervised_by) // Su jefe
                      ->orWhereHas('relations', function($sq) use ($centerId) {
                          $sq->where('related_person_id', $centerId) // Gente que la evalúa a ella
                            ->orWhere('person_id', $centerId); // Gente que ella evalúa
                      });
                });
            }
        }

        $people = $query->get();

        $nodes = $people->map(function ($p) {
            return [
                'id' => 'p_' . $p->id,
                'name' => $p->full_name,
                'type' => 'person',
                'mass' => $p->is_high_potential ? 1.5 : 1,
                'value' => $p->salary,
                'role' => $p->role->name ?? 'N/A',
                'dept' => $p->department->name ?? 'N/A',
                'parentId' => $p->supervised_by ? 'p_' . $p->supervised_by : null,
                'color' => $p->is_high_potential ? '#10b981' : '#3b82f6'
            ];
        });

        $links = [];
        $addedLinks = []; // To avoid duplicates

        foreach ($people as $p) {
            // Reporting line (Core Hierarchy)
            if ($p->supervised_by) {
                $source = 'p_' . $p->supervised_by;
                $target = 'p_' . $p->id;
                $links[] = [
                    'source' => $source,
                    'target' => $target,
                    'value' => 3, // Strong link for hierarchy
                    'type' => 'supervisor'
                ];
                $addedLinks["$source-$target"] = true;
            }

            // 360 Evaluation Relationships
            foreach ($p->relations as $rel) {
                $source = 'p_' . $p->id;
                $target = 'p_' . $rel->related_person_id;
                
                // Only add if target is also in our current result set
                if ($people->contains('id', $rel->related_person_id) && !isset($addedLinks["$source-$target"])) {
                    $links[] = [
                        'source' => $source,
                        'target' => $target,
                        'value' => $rel->relationship_type === 'peer' ? 1 : 2,
                        'type' => $rel->relationship_type
                    ];
                    $addedLinks["$source-$target"] = true;
                }
            }
        }

        return response()->json([
            'nodes' => $nodes,
            'links' => $links
        ]);
    }

    /**
     * Busca personas para el buscador del mapa
     */
    public function searchPeople(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $query = $request->query('query');

        if (empty($query)) {
            return response()->json([]);
        }

        $people = People::where('organization_id', $orgId)
            ->where(function($q) use ($query) {
                $q->where('first_name', 'like', "%$query%")
                  ->orWhere('last_name', 'like', "%$query%")
                  ->orWhere(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%$query%");
            })
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'department_id']);

        return response()->json($people);
    }
}
