<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departments;
use App\Models\Skill;
use App\Models\People;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    /**
     * Get the organizational tree of departments.
     */
    public function tree(Request $request)
    {
        // Obtener todos los departamentos con su relación manager
        $departments = Departments::with('manager:id,first_name,last_name,email,avatar_url')
            ->orderBy('name')
            ->get();

        // Convertirlos en árbol
        $tree = $this->buildTree($departments);

        return response()->json($tree);
    }

    /**
     * Update hierarchy (Drag and Drop in Org Chart)
     */
    public function updateHierarchy(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:departments,id',
            'parent_id' => 'nullable|exists:departments,id',
        ]);

        // Evitar ciclos infinitos básicos
        if ($data['id'] == $data['parent_id']) {
            return response()->json(['error' => 'Un departamento no puede ser padre de sí mismo.'], 422);
        }

        $department = Departments::findOrFail($data['id']);
        $department->parent_id = $data['parent_id'];
        $department->save();

        return response()->json(['message' => 'Jerarquía actualizada exitosamente']);
    }

    /**
     * Update department parent via PUT (from hierarchy editor modal)
     */
    public function updateDepartmentParent(Request $request, Departments $department)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:departments,id|different:id',
        ]);

        // Evitar ciclos infinitos: check if parent_id is descendant of this department
        $hasCircularReference = $this->hasDescendant($department->id, $data['parent_id']);
        if ($hasCircularReference) {
            return response()->json(
                ['error' => 'No se puede establecer esta relación: crearía un ciclo circular.'],
                422
            );
        }

        $department->parent_id = $data['parent_id'];
        $department->save();

        // Retornar el departamento actualizado
        return response()->json([
            'message' => 'Jerarquía actualizada exitosamente',
            'department' => $department->load('manager', 'parent', 'children'),
        ]);
    }

    /**
     * Helper: Check if targetId is a descendant of departmentId
     */
    private function hasDescendant($departmentId, $targetId): bool
    {
        if (! $targetId) {
            return false;
        } // null parent is always valid

        $queue = [$targetId];
        $visited = [];

        while (! empty($queue)) {
            $current = array_pop($queue);
            if ($current == $departmentId) {
                return true; // Found circular reference
            }
            if (in_array($current, $visited)) {
                continue;
            }
            $visited[] = $current;

            $children = Departments::where('parent_id', $current)->pluck('id')->toArray();
            $queue = array_merge($queue, $children);
        }

        return false;
    }

    /**
     * Set a manager to a department
     */
    public function setManager(Request $request, int $id)
    {
        $data = $request->validate([
            'manager_id' => 'nullable|exists:people,id',
        ]);

        $department = Departments::findOrFail($id);
        $department->manager_id = $data['manager_id'];
        $department->save();

        return response()->json(['message' => 'Manager actualizado correctamente', 'department' => $department->load('manager')]);
    }

    /**
     * Heatmap Data para Echarts (Stratos Map)
     * Retorna [ [x, y, value], ... ] x=Departamento, y=Competencia/Skill
     */
    public function heatmapData(Request $request)
    {
        // Cargar departamentos y skills desde la BD (una sola vez)
        $departments = Departments::orderBy('name')->get();
        $skills = Skill::where('is_critical', true)->orderBy('name')->get();

        // Fallbacks en caso de datos ausentes (mantener comportamiento previo)
        if ($departments->isEmpty()) {
            $departments = collect(['Ventas', 'Ingeniería', 'Producto', 'Marketing', 'Data', 'RRHH', 'Operaciones'])
                ->map(fn($n) => (object)['id' => null, 'name' => $n]);
        }
        if ($skills->isEmpty()) {
            $skills = collect(['Python', 'SQL', 'Liderazgo', 'AI/ML', 'UX/UI', 'Finanzas'])
                ->map(fn($n) => (object)['id' => null, 'name' => $n]);
        }

        $deptNames = $departments->pluck('name')->values()->all();
        $skillNames = $skills->pluck('name')->values()->all();

        // Mapear por nombre para búsquedas rápidas
        $deptByName = $departments->keyBy('name');
        $skillByName = $skills->keyBy('name');

        $data = [];
        $criticalRisks = [];

        // Si no hay ids reales, mantenemos el comportamiento aleatorio simple
        $realDeptIds = $departments->pluck('id')->filter()->values()->all();
        $realSkillIds = $skills->pluck('id')->filter()->values()->all();

        if (empty($realDeptIds) || empty($realSkillIds)) {
            // Generar datos random (legacy fallback)
            foreach ($deptNames as $x => $depName) {
                foreach ($skillNames as $y => $skillName) {
                    $data[] = [$x, $y, rand(30, 95)];
                }
            }

            return response()->json([
                'x_axis' => $deptNames,
                'y_axis' => $skillNames,
                'data' => $data,
                'critical_risks' => $criticalRisks,
            ]);
        }

        // Cargar personas una sola vez con sus activeSkills filtradas
        $people = People::with(['activeSkills' => function ($q) use ($realSkillIds) {
            $q->whereIn('skill_id', $realSkillIds);
        }])
            ->whereIn('department_id', $realDeptIds)
            ->get(['id', 'first_name', 'last_name', 'department_id', 'is_high_potential']);

        // Headcount por departamento (solo para los departamentos reales)
        $headcounts = DB::table('people')
            ->whereIn('department_id', $realDeptIds)
            ->select('department_id', DB::raw('count(*) as cnt'))
            ->groupBy('department_id')
            ->get()
            ->pluck('cnt', 'department_id')
            ->toArray();

        // Mapear índices para eje X/Y
        $deptIndex = array_flip($deptNames);
        $skillIndex = array_flip($skillNames);

        // Contadores por pair (deptIndex, skillIndex)
        $counters = [];

        foreach ($people as $p) {
            foreach ($p->activeSkills as $ps) {
                $skill = $ps->skill; // relación de PeopleRoleSkills
                if (! $skill) {
                    continue;
                }
                $dName = $departments->firstWhere('id', $p->department_id)->name ?? null;
                $sName = $skill->name;
                if ($dName === null || $sName === null) {
                    continue;
                }
                $x = $deptIndex[$dName] ?? null;
                $y = $skillIndex[$sName] ?? null;
                if ($x === null || $y === null) {
                    continue;
                }
                $key = "{$x}:{$y}";
                $counters[$key]['count'] = ($counters[$key]['count'] ?? 0) + 1;
                if (! empty($p->is_high_potential) && rand(0, 10) > 8) {
                    $counters[$key]['risk_person'] = ($counters[$key]['risk_person'] ?? []) + [trim($p->first_name . ' ' . $p->last_name)];
                }
            }
        }

        // Construir la matriz de salida
        foreach ($deptNames as $x => $dName) {
            foreach ($skillNames as $y => $sName) {
                $key = "{$x}:{$y}";
                $count = $counters[$key]['count'] ?? 0;
                $head = $headcounts[$departments->firstWhere('name', $dName)->id] ?? 0;
                $coverage = $head > 0 ? (int) round(($count / $head) * 100) : rand(30, 95);

                if (! empty($counters[$key]['risk_person'])) {
                    foreach ($counters[$key]['risk_person'] as $personName) {
                        $criticalRisks[] = [
                            'coord' => [$x, $y],
                            'reason' => "Continuity Alert: {$personName} (Clave) en riesgo de fuga.",
                        ];
                        // solo un riesgo por celda es suficiente para la visual
                        break;
                    }
                }

                $data[] = [$x, $y, $coverage];
            }
        }

        return response()->json([
            'x_axis' => $deptNames,
            'y_axis' => $skillNames,
            'data' => $data,
            'critical_risks' => $criticalRisks,
        ]);
    }

    /**
     * Función recursiva helper para construir el árbol.
     */
    private function buildTree($elements, $parentId = null)
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTree($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                } else {
                    $element->children = [];
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
