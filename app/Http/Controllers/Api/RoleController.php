<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Store a newly created role with its cube dimensions and competencies.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'cube_dimensions' => 'nullable|array',
            'competencies' => 'nullable|array',
            'ai_archetype_config' => 'nullable|array'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $role = Roles::create([
                    'organization_id' => auth()->user()->organization_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'cube_dimensions' => $request->cube_dimensions,
                    'ai_archetype_config' => $request->ai_archetype_config,
                    'status' => 'active'
                ]);

                // Attach competencies/skills
                if ($request->has('competencies') && is_array($request->competencies)) {
                    foreach ($request->competencies as $compData) {
                        // Find or create the skill
                        $skill = Skill::firstOrCreate(
                            [
                                'name' => $compData['name'],
                                'organization_id' => auth()->user()->organization_id
                            ],
                            [
                                'category' => 'Competency',
                                'description' => $compData['rationale'] ?? null
                            ]
                        );

                        // Attach to role with level
                        $role->skills()->attach($skill->id, [
                            'required_level' => $compData['level'] ?? 3,
                            'is_critical' => true
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Rol creado exitosamente con el Modelo de Cubo.',
                    'role' => $role
                ], 201);
            });
        } catch (\Exception $e) {
            Log::error('Error creating role with Cube: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el rol: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specific role.
     */
    public function show($id)
    {
        $role = Roles::with(['skills', 'agent', 'blueprint'])->findOrFail($id);
        return response()->json($role);
    }
}
