<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\People;
use App\Models\Roles;
use App\Services\DevelopmentPathService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DevelopmentPathController extends Controller
{
    public function generate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'people_id' => ['required', 'integer'],
            'role_id' => ['nullable', 'integer'],
            'role_name' => ['nullable', 'string'],
        ]);

        $people = People::find($data['people_id']);
        if (! $people) {
            return response()->json(['error' => 'Peoplea no encontrada'], 404);
        }

        $role = null;
        if (!empty($data['role_id'])) {
            $role = Roles::find($data['role_id']);
        } elseif (!empty($data['role_name'])) {
            $role = Roles::where('name', $data['role_name'])->first();
        }

        if (! $role) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }

        $service = new DevelopmentPathService();
        $path = $service->generate($people, $role);

        return response()->json([
            'id' => $path->id,
            'status' => $path->status,
            'estimated_duration_months' => $path->estimated_duration_months,
            'steps' => $path->steps,
            'people' => [
                'id' => $people->id,
                'name' => $people->full_name ?? ($people->first_name . ' ' . $people->last_name),
            ],
            'target_role' => [
                'id' => $role->id,
                'name' => $role->name,
            ],
        ], 201);
    }
}
