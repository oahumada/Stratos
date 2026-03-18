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
            'ai_archetype_config' => 'nullable|array',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $role = Roles::create([
                    'organization_id' => auth()->user()->organization_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'cube_dimensions' => $request->cube_dimensions,
                    'ai_archetype_config' => $request->ai_archetype_config,
                    'status' => 'active',
                ]);

                // Attach competencies/skills
                if ($request->has('competencies') && is_array($request->competencies)) {
                    foreach ($request->competencies as $compData) {
                        // Find or create the skill
                        $skill = Skill::firstOrCreate(
                            [
                                'name' => $compData['name'],
                                'organization_id' => auth()->user()->organization_id,
                            ],
                            [
                                'category' => 'Competency',
                                'description' => $compData['rationale'] ?? null,
                            ]
                        );

                        // Attach to role with level
                        $role->skills()->attach($skill->id, [
                            'required_level' => $compData['level'] ?? 3,
                            'is_critical' => true,
                        ]);
                    }
                }

                // Trigger Strategic Domain Event
                \App\Events\RoleRequirementsUpdated::dispatch(
                    $role->id,
                    $role->organization_id,
                    ['action' => 'created_from_cube_wizard', 'name' => $role->name]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Rol creado exitosamente con el Modelo de Cubo.',
                    'role' => $role,
                ], 201);
            });
        } catch (\Exception $e) {
            Log::error('Error creating role with Cube: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el rol: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing role with wizard data.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'cube_dimensions' => 'nullable|array',
            'competencies' => 'nullable|array',
            'ai_archetype_config' => 'nullable|array',
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {
                $role = Roles::findOrFail($id);
                
                $role->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'cube_dimensions' => $request->cube_dimensions,
                    'ai_archetype_config' => $request->ai_archetype_config,
                ]);

                // Sync competencies/skills (simplification: detach all and re-attach)
                if ($request->has('competencies') && is_array($request->competencies)) {
                    $role->skills()->detach(); // Clean existing
                    
                    foreach ($request->competencies as $compData) {
                        $skill = Skill::firstOrCreate(
                            [
                                'name' => $compData['name'],
                                'organization_id' => auth()->user()->organization_id,
                            ],
                            [
                                'category' => 'Competency',
                                'description' => $compData['rationale'] ?? null,
                            ]
                        );

                        $role->skills()->attach($skill->id, [
                            'required_level' => $compData['level'] ?? 3,
                            'is_critical' => true,
                        ]);
                    }
                }

                \App\Events\RoleRequirementsUpdated::dispatch(
                    $role->id,
                    $role->organization_id,
                    ['action' => 'updated_from_cube_wizard', 'name' => $role->name]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Rol actualizado exitosamente con el Refinamiento de Cubo.',
                    'role' => $role,
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Error updating role with Cube: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el rol: '.$e->getMessage(),
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
