<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\Role;
use App\Services\DevelopmentPathService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DevelopmentPathController extends Controller
{
    public function generate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'person_id' => ['required', 'integer'],
            'role_id' => ['nullable', 'integer'],
            'role_name' => ['nullable', 'string'],
        ]);

        $person = Person::find($data['person_id']);
        if (! $person) {
            return response()->json(['error' => 'Persona no encontrada'], 404);
        }

        $role = null;
        if (!empty($data['role_id'])) {
            $role = Role::find($data['role_id']);
        } elseif (!empty($data['role_name'])) {
            $role = Role::where('name', $data['role_name'])->first();
        }

        if (! $role) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }

        $service = new DevelopmentPathService();
        $path = $service->generate($person, $role);

        return response()->json([
            'id' => $path->id,
            'status' => $path->status,
            'estimated_duration_months' => $path->estimated_duration_months,
            'steps' => $path->steps,
            'person' => [
                'id' => $person->id,
                'name' => $person->full_name ?? ($person->first_name . ' ' . $person->last_name),
            ],
            'target_role' => [
                'id' => $role->id,
                'name' => $role->name,
            ],
        ], 201);
    }
}
