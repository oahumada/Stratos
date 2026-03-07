<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departments;
use App\Models\Skill;
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
        // En producción cargaríamos deps y skills de la DB
        $departments = Departments::orderBy('name')->pluck('name')->toArray();
        if (empty($departments)) {
            $departments = ['Ventas', 'Ingeniería', 'Producto', 'Marketing', 'Data', 'RRHH', 'Operaciones'];
        }

        $skills = Skill::where('is_critical', true)->orderBy('name')->pluck('name')->toArray();
        if (empty($skills)) {
            $skills = ['Python', 'SQL', 'Liderazgo', 'AI/ML', 'UX/UI', 'Finanzas'];
        }

        $data = [];
        $criticalRisks = [];

        foreach ($departments as $x => $depName) {
            $dept = Departments::where('name', $depName)->first();
            foreach ($skills as $y => $skillName) {
                $skill = Skill::where('name', $skillName)->first();

                // Calculamos cobertura real or random if not exists
                $coverage = rand(30, 95);

                // LÓGICA DE RIESGO DE CONTINUIDAD
                // Si la cobertura es < 50% y la skill es crítica, o si hay personas clave en riesgo de fuga
                $hasRetentionRisk = false;
                $riskReason = '';

                if ($dept && $skill) {
                    // Buscamos personas en este depto con esta skill crítica
                    $peopleWithSkill = $dept->People()->whereHas('activeSkills', function ($q) use ($skill) {
                        $q->where('skill_id', $skill->id);
                    })->get();

                    foreach ($peopleWithSkill as $p) {
                        // Mock de detección de riesgo de fuga (en prod vendría del predictor service)
                        if ($p->is_high_potential && rand(0, 10) > 8) {
                            $hasRetentionRisk = true;
                            $riskReason = "Continuity Alert: {$p->full_name} (Clave) en riesgo de fuga.";
                            break;
                        }
                    }
                }

                $value = $coverage;
                // Si hay riesgo de fuga, forzamos un estado visual de "Alerta" (ej. > 100 para lógica de color)
                if ($hasRetentionRisk) {
                    $criticalRisks[] = [
                        'coord' => [$x, $y],
                        'reason' => $riskReason,
                    ];
                }

                $data[] = [$x, $y, $value];
            }
        }

        return response()->json([
            'x_axis' => $departments,
            'y_axis' => $skills,
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
