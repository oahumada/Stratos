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
                    'status' => $request->status ?? 'pending_approval',
                ]);

                // Sync competencies/skills and update their catalog data with detailed levels (blueprint)
                if ($request->has('competencies') && is_array($request->competencies)) {
                    $this->syncRoleSkills($role, $request);
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
                    'status' => $request->status ?? $role->status,
                ]);

                // Sync competencies/skills and update their catalog data with detailed levels
                if ($request->has('competencies') && is_array($request->competencies)) {
                    $this->syncRoleSkills($role, $request);
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
     * Synchronizes role competency/skill hierarchy and populates catalog data.
     * Note: Learning units and performance criteria are stored at the Skill level (SFIA standard).
     */
    private function syncRoleSkills(Roles $role, Request $request)
    {
        $competenciesData = $request->input('competencies', []);
        $blueprint = $request->input('ai_archetype_config.skill_blueprint', []);
        
        $roleCompetencyIds = [];
        $roleSkillIds = [];
        
        foreach ($competenciesData as $compData) {
            $compName = $compData['name'] ?? $compData['competency_name'] ?? 'Nueva Competencia';
            
            // 1. Create or Find the high-level Competency in the catalog (SFIA Category)
            $competency = \App\Models\Competency::updateOrCreate(
                [
                    'name' => $compName,
                    'organization_id' => $role->organization_id,
                ],
                [
                    'description' => $compData['rationale'] ?? $compData['description'] ?? "Competencia estratégica",
                    'status' => 'pending_approval'
                ]
            );

            $roleCompetencyIds[$competency->id] = [
                'required_level' => $compData['level'] ?? 3,
                'is_core' => true,
                'rationale' => $compData['rationale'] ?? null,
            ];

            // 2. Identify and Save nested Skills for this competency from the blueprint (SFIA Levels)
            if (!empty($blueprint) && is_array($blueprint)) {
                $roleSkillIds = array_merge(
                    $roleSkillIds, 
                    $this->processSfiaSkills($role, $competency, $compName, $blueprint, $compData['level'] ?? 3)
                );
            }
        }

        // Sync associations to the role
        $role->competencies()->sync($roleCompetencyIds);
        $role->skills()->sync($roleSkillIds);
    }

    /**
     * Navigates the blueprint to find skills belonging to a competency and populates their SFIA data.
     */
    private function processSfiaSkills(Roles $role, \App\Models\Competency $competency, string $compName, array $blueprint, int $requiredLevel): array
    {
        $skillIds = [];

        foreach ($blueprint as $item) {
            if (($item['competency_name'] ?? '') === $compName && isset($item['skills']) && is_array($item['skills'])) {
                foreach ($item['skills'] as $sBlue) {
                    // Create or Update the Skill record
                    $skill = Skill::updateOrCreate(
                        [
                            'name' => $sBlue['name'],
                            'organization_id' => $role->organization_id,
                        ],
                        [
                            'category' => 'Skill',
                            'description' => $sBlue['description'] ?? null,
                            'status' => 'pending_approval'
                        ]
                    );

                    // Associate Skill with Competency if not already linked
                    $competency->skills()->syncWithoutDetaching([$skill->id => ['weight' => 10]]);

                    // Populate Skill-specific SFIA levels (Note: stored in bars_levels table for technical compatibility)
                    if (isset($sBlue['levels']) && is_array($sBlue['levels'])) {
                        $this->updateSfiaLevels($skill, $sBlue['levels']);
                    }

                    $skillIds[$skill->id] = [
                        'required_level' => $requiredLevel,
                        'is_critical' => true,
                    ];
                }
            }
        }

        return $skillIds;
    }

    /**
     * Updates or creates SFIA level records for a skill with units and criteria.
     * Note: While the table/model is named BarsLevel, in this context it represents the 1-5 SFIA hierarchy.
     */
    private function updateSfiaLevels(Skill $skill, array $levels)
    {
        foreach ($levels as $lvl) {
            $skill->barsLevels()->updateOrCreate(
                ['level' => $lvl['level'] ?? 1],
                [
                    'level_name' => $lvl['level_name'] ?? "Nivel {$lvl['level']}",
                    'behavioral_description' => $lvl['description'] ?? $lvl['behavioral_description'] ?? null,
                    'learning_content' => $lvl['learning_unit'] ?? null, // Specifically for Skills
                    'performance_indicator' => $lvl['performance_criterion'] ?? null, // Specifically for Skills
                ]
            );
        }
    }

    /**
     * Display the specific role.
     */
    public function show($id)
    {
        $role = Roles::with(['skills', 'agent', 'blueprint', 'people'])->findOrFail($id);

        return response()->json($role);
    }
}
