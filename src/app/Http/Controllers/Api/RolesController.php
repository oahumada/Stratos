<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RolesController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::with('skills')->get()->map(fn($r) => [
            'id' => $r->id,
            'name' => $r->name,
            'department' => $r->department,
            'level' => $r->level,
            'skills_count' => $r->skills()->count(),
        ]);

        return response()->json(['data' => $roles]);
    }

    public function show(int $id): JsonResponse
    {
        $role = Role::with('skills')->find($id);
        if (! $role) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }

        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'department' => $role->department,
            'level' => $role->level,
            'skills' => $role->skills->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'category' => $s->category,
                'required_level' => $s->pivot->required_level ?? 0,
                'is_critical' => (bool) ($s->pivot->is_critical ?? false),
            ]),
        ]);
    }
}
