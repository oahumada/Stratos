<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\Role;
use App\Services\GapAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GapAnalysisController extends Controller
{
    public function analyze(Request $request): JsonResponse
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

        $service = new GapAnalysisService();
        $analysis = $service->calculate($person, $role);

        return response()->json([
            'person' => [
                'id' => $person->id,
                'name' => $person->full_name ?? ($person->first_name . ' ' . $person->last_name),
            ],
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
            ],
            'analysis' => $analysis,
        ]);
    }
}
