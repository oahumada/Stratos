<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departments;
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
     * Función recursiva helper para construir el árbol.
     */
    private function buildTree($elements, $parentId = null)
    {
        $branch = array();

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
