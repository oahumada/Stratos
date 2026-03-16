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

        $query = People::with(['department', 'role', 'supervisor'])
            ->where('organization_id', $orgId);

        if ($centerId) {
            $query->where(function($q) use ($centerId) {
                $q->where('id', $centerId)
                  ->orWhere('supervised_by', $centerId);
            });
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
                'color' => '#10b981'
            ];
        });

        $links = [];
        foreach ($people as $p) {
            if ($p->supervised_by) {
                $links[] = [
                    'source' => 'p_' . $p->supervised_by,
                    'target' => 'p_' . $p->id,
                    'value' => 2
                ];
            }
        }

        return response()->json([
            'nodes' => $nodes,
            'links' => $links
        ]);
    }
}
