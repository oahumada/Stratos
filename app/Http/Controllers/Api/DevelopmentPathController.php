<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentPath;
use App\Models\People;
use App\Models\Roles;
use App\Services\DevelopmentPathService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DevelopmentPathController extends Controller
{
    protected $smartGenerator;

    public function __construct(\App\Services\Talent\SmartPathGeneratorService $smartGenerator)
    {
        $this->smartGenerator = $smartGenerator;
    }

    /**
     * Lista todas las rutas de desarrollo
     */
    public function index(): JsonResponse
    {
        $paths = DevelopmentPath::with(['people', 'targetRole', 'actions'])
            ->latest()
            ->get()
            ->map(function ($path) {
                return [
                    'id' => $path->id,
                    'people_id' => $path->people_id,
                    'people_name' => $path->people->full_name ??
                                    ($path->people->first_name.' '.$path->people->last_name),
                    'action_title' => $path->action_title ?? $path->title ?? 'Plan de Desarrollo',
                    'current_role_id' => $path->people->role_id,
                    'current_role_name' => $path->people->role->name ?? null,
                    'target_role_id' => $path->target_role_id,
                    'target_role_name' => $path->targetRole->name ?? 'N/A',
                    'status' => $path->status,
                    'estimated_duration_months' => $path->estimated_duration_months,
                    'progress' => $path->progress ?? 0,
                    'actions' => $path->actions, // Include the detailed actions
                    'created_at' => $path->created_at->toIso8601String(),
                ];
            });

        return response()->json(['data' => $paths]);
    }

    /**
     * Genera una nueva ruta de desarrollo
     * Soporta dos modos:
     * 1. Plan de Carrera (basado en Roles)
     * 2. Cierre de Brechas (basado en Skills)
     */
    public function generate(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'people_id' => ['required', 'integer'],
                'role_id' => ['nullable', 'integer'], // Modo Carrera
                'skill_id' => ['nullable', 'integer'], // Modo Gap
                'current_level' => ['nullable', 'integer'],
                'target_level' => ['nullable', 'integer'],
            ]);

            $people = People::find($data['people_id']);
            if (! $people) {
                return response()->json(['error' => 'Persona no encontrada'], 404);
            }

            // Modo 2: Skill Gap Closure (Smart Path)
            if (!empty($data['skill_id'])) {
                $currentLevel = $data['current_level'] ?? 1;
                $targetLevel = $data['target_level'] ?? 3;
                
                $path = $this->smartGenerator->generatePath(
                    $people->id,
                    $data['skill_id'],
                    $currentLevel,
                    $targetLevel
                );

                return response()->json(['data' => $path->load('actions')], 201);
            }

            // Modo 1: Career Path (Legacy/Role based)
            $role = null;
            if (! empty($data['role_id'])) {
                $role = Roles::find($data['role_id']);
            }

            if (! $role) {
                return response()->json(['error' => 'Debe especificar un Rol o una Skill'], 422);
            }

            $service = new DevelopmentPathService;
            $path = $service->generate($people, $role);

            return response()->json([
                'data' => [
                    'id' => $path->id,
                    'status' => $path->status,
                    'estimated_duration_months' => $path->estimated_duration_months,
                    'steps' => $path->steps,
                    'people' => [
                        'id' => $people->id,
                        'name' => $people->full_name ?? ($people->first_name.' '.$people->last_name),
                    ],
                    'target_role' => [
                        'id' => $role->id,
                        'name' => $role->name,
                    ],
                ],
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Development path generation error: '.$e->getMessage(), [
                'people_id' => $request->input('people_id'),
                'role_id' => $request->input('role_id'),
            ]);

            return response()->json([
                'error' => 'Error al generar la ruta de aprendizaje',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Elimina una ruta de desarrollo (soft delete)
     */
    public function destroy($id): JsonResponse
    {
        $path = DevelopmentPath::find($id);

        if (! $path) {
            return response()->json(['error' => 'Ruta no encontrada'], 404);
        }

        $path->delete();

        return response()->json([
            'message' => 'Ruta de aprendizaje eliminada correctamente',
            'id' => $id,
        ], 200);
    }
}
