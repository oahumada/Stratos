<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentPath;
use App\Models\People;
use App\Models\Roles;
use App\Services\DevelopmentPathService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DevelopmentPathController extends Controller
{
    /**
     * Lista todas las rutas de desarrollo
     */
    public function index(): JsonResponse
    {
        $paths = DevelopmentPath::with(['people', 'targetRole'])
            ->latest()
            ->get()
            ->map(function ($path) {
                return [
                    'id' => $path->id,
                    'people_id' => $path->people_id,
                    'people_name' => $path->people->full_name ?? 
                                    ($path->people->first_name . ' ' . $path->people->last_name),
                    'target_role_id' => $path->target_role_id,
                    'target_role_name' => $path->targetRole->name ?? 'N/A',
                    'status' => $path->status,
                    'estimated_duration_months' => $path->estimated_duration_months,
                    'steps' => $path->steps,
                    'created_at' => $path->created_at->toIso8601String(),
                ];
            });

        return response()->json(['data' => $paths]);
    }

    /**
     * Genera una nueva ruta de desarrollo
     */
    public function generate(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'people_id' => ['required', 'integer'],
                'role_id' => ['nullable', 'integer'],
                'role_name' => ['nullable', 'string'],
            ]);

            $people = People::find($data['people_id']);
            if (! $people) {
                return response()->json(['error' => 'Persona no encontrada'], 404);
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
                'data' => [
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
                ]
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Development path generation error: ' . $e->getMessage(), [
                'people_id' => $request->input('people_id'),
                'role_id' => $request->input('role_id'),
            ]);
            
            return response()->json([
                'error' => 'Error al generar la ruta de aprendizaje',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina una ruta de desarrollo (soft delete)
     */
    public function destroy($id): JsonResponse
    {
        $path = DevelopmentPath::find($id);
        
        if (!$path) {
            return response()->json(['error' => 'Ruta no encontrada'], 404);
        }

        $path->delete();

        return response()->json([
            'message' => 'Ruta de aprendizaje eliminada correctamente',
            'id' => $id
        ], 200);
    }
}
