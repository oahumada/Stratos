<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Capability;
use App\Models\Competency;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\Skill;
use App\Services\EmbeddingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncubationController extends Controller
{
    protected $embeddingService;

    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
    }

    /**
     * List all items in 'in_incubation' status for a scenario.
     */
    public function index(Request $request, $scenarioId)
    {
        // ... (implementation follows)
        // I will use `write_to_file` correctly in separate step or better yet, build it out properly now.
        $user = auth()->user();
        $scenario = Scenario::withoutGlobalScopes()->find($scenarioId);

        if (!$scenario || $scenario->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['message' => 'Scenario not found or forbidden'], 404);
        }

        $response = [
            'capabilities' => $this->getIncubatedItems(Capability::class, $scenarioId),
            'competencies' => $this->getIncubatedItems(Competency::class, $scenarioId),
            'skills' => $this->getIncubatedItems(Skill::class, $scenarioId),
            'roles' => $this->getIncubatedItems(\App\Models\TalentBlueprint::class, $scenarioId),
        ];

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    private function getIncubatedItems($modelClass, $scenarioId)
    {
        $items = collect();
        if ($modelClass === \App\Models\TalentBlueprint::class) {
             $items = $modelClass::where('scenario_id', $scenarioId)
                ->where('status', 'in_incubation')
                ->get();
        } else {
            $items = $modelClass::where('discovered_in_scenario_id', $scenarioId)
                ->where('status', 'in_incubation')
                ->get();
        }

        // Bonus: Check for duplicates/similar items if embeddings are enabled
        if (config('features.generate_embeddings', false)) {
            $items->transform(function ($item) use ($modelClass) {
                // If the item has an embedding, check for similar items in active status
                if (!empty($item->embedding)) {
                    // Critical: if we are checking a Blueprint, we compare against 'roles' table
                    $targetTable = ($modelClass === \App\Models\TalentBlueprint::class) ? 'roles' : (new $modelClass)->getTable();
                    
                    try {
                        $vector = $this->vectorToArray($item->embedding);
                        
                        // Search for similar items (limit 5)
                        $similar = $this->embeddingService->findSimilar(
                            $targetTable,
                            $vector,
                            5,
                            $item->organization_id
                        );
                        
                        $warnings = [];
                        foreach ($similar as $match) {
                            // Filter out exact same ID ONLY if we are in the same table
                            if ($targetTable === $item->getTable() && $match->id == $item->id) continue;
                            
                            // For roles, we are often comparing blueprint vs catalog, so similarities are very useful
                            if ($match->similarity > 0.75) { // Lower threshold for roles to show "Partial" matches
                                $warnings[] = [
                                    'id' => $match->id,
                                    'name' => $match->name,
                                    'score' => round($match->similarity, 3)
                                ];
                            }
                        }

                        if (!empty($warnings)) {
                            $item->similarity_warnings = $warnings;
                        }

                    } catch (\Exception $e) {
                         Log::error("Similarity check failed: " . $e->getMessage());
                    }
                }
                
                // Hide embedding from JSON response
                if (method_exists($item, 'makeHidden')) {
                    $item->makeHidden('embedding');
                }
                return $item;
            });
        }

        return $items;
    }

    private function vectorToArray($vector) {
        if (is_string($vector)) {
            // Postgres vector output might be string "[0.1,0.2...]"
            return json_decode($vector);
        }
        return $vector;
    }

    /**
     * Approve incubated items (bulk).
     */
    public function approve(Request $request, $scenarioId)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.type' => 'required|in:capability,competency,skill,role',
            'items.*.id' => 'required|integer'
        ]);

        $user = auth()->user();
        $scenario = Scenario::withoutGlobalScopes()->find($scenarioId);
        
        if (!$scenario || $scenario->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['message' => 'Scenario not found or forbidden'], 404);
        }

        $approvedCount = 0;

        DB::transaction(function () use ($request, $scenario, &$approvedCount) {
            foreach ($request->items as $item) {
                $modelClass = $this->getModelClass($item['type']);
                
                if ($modelClass) {
                    $entity = null;
                    
                    if ($modelClass === \App\Models\TalentBlueprint::class) {
                        $entity = $modelClass::where('id', $item['id'])
                            ->where('scenario_id', $scenario->id)
                            ->where('status', 'in_incubation')
                            ->first();

                        if ($entity) {
                            $entity->status = 'active';
                            $entity->save();
                            
                            // Create actual Role
                            $realRole = \App\Models\Roles::firstOrCreate([
                                'organization_id' => $scenario->organization_id,
                                'name' => $entity->role_name,
                            ], [
                                'description' => $entity->role_description,
                                'status' => 'active',
                                'discovered_in_scenario_id' => $scenario->id,
                                'llm_id' => 'gen_' . $entity->id
                            ]);
                            
                            // Determine Archetype
                            $arch = 'O';
                            if ($entity->human_leverage > 70) $arch = 'E';
                            elseif ($entity->human_leverage > 40) $arch = 'T';

                            // Link Role to Scenario via scenario_roles table
                            \App\Models\ScenarioRole::updateOrCreate([
                                'scenario_id' => $scenario->id,
                                'role_id' => $realRole->id,
                            ], [
                                'fte' => $entity->total_fte_required ?? 1,
                                'role_change' => 'modify',
                                'evolution_type' => 'transformation',
                                'impact_level' => 'high',
                                'human_leverage' => $entity->human_leverage,
                                'archetype' => $arch,
                            ]);
                            
                            $approvedCount++;
                        }
                    } else {
                        $entity = $modelClass::where('id', $item['id'])
                            ->where('discovered_in_scenario_id', $scenario->id)
                            ->where('status', 'in_incubation')
                            ->first();
                            
                        if ($entity) {
                            $entity->status = 'active';
                            $entity->save();
                            $approvedCount++;
                        }
                    }
                }
            }
        });

        // After transaction, check remaining items
        $remaining = Capability::where('discovered_in_scenario_id', $scenarioId)->where('status', 'in_incubation')->count() +
                     Competency::where('discovered_in_scenario_id', $scenarioId)->where('status', 'in_incubation')->count() +
                     Skill::where('discovered_in_scenario_id', $scenarioId)->where('status', 'in_incubation')->count() +
                     \App\Models\TalentBlueprint::where('scenario_id', $scenarioId)->where('status', 'in_incubation')->count();

        // Even if zero, we don't move scenario to 'active' yet, as per user requirement.
        // It stays in 'incubating' or moves to a 'design_consolidated' state.
        if ($remaining === 0 && $scenario->status === 'incubating') {
             $scenario->status = 'incubated'; // A technical state meaning "Design Ready but not global"
             $scenario->save();
        }

        return response()->json([
            'success' => true, 
            'approved_count' => $approvedCount,
            'scenario_status' => $scenario->status,
            'remaining_incubated' => $remaining
        ]);
    }

    /**
     * Reject incubated items (bulk).
     * Rejection means DELETION or setting to inactive. 
     * Since these are new items that we don't want, deletion is preferred to avoid clutter.
     */
    public function reject(Request $request, $scenarioId)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.type' => 'required|in:capability,competency,skill,role',
            'items.*.id' => 'required|integer'
        ]);

        $user = auth()->user();
        $scenario = Scenario::withoutGlobalScopes()->find($scenarioId);

        if (!$scenario || $scenario->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['message' => 'Scenario not found or forbidden'], 404);
        }

        $rejectedCount = 0;

        DB::transaction(function () use ($request, $scenario, &$rejectedCount) {
            foreach ($request->items as $item) {
                $modelClass = $this->getModelClass($item['type']);
                if ($modelClass) {
                   $entity = null;
                   
                    if ($modelClass === \App\Models\TalentBlueprint::class) {
                        $entity = $modelClass::where('id', $item['id'])
                            ->where('scenario_id', $scenario->id)
                            ->where('status', 'in_incubation')
                            ->first();
                    } else {
                        $entity = $modelClass::where('id', $item['id'])
                            ->where('discovered_in_scenario_id', $scenario->id)
                            ->where('status', 'in_incubation')
                            ->first();
                    }
                    
                    if ($entity) {
                        // Hard delete attempt
                        try {
                            $entity->delete();
                            $rejectedCount++;
                        } catch (\Exception $e) {
                             // Fallback to inactive if constraints prevent deletion
                             Log::warning("Could not delete incubated item {$item['type']} {$item['id']}: " . $e->getMessage());
                             $entity->status = 'inactive';
                             $entity->save();
                             $rejectedCount++;
                        }
                    }
                }
            }
        });

        return response()->json(['success' => true, 'rejected_count' => $rejectedCount]);
    }

    private function getModelClass($type)
    {
        return match ($type) {
            'capability' => Capability::class,
            'competency' => Competency::class,
            'skill' => Skill::class,
            'role' => \App\Models\TalentBlueprint::class, // Use Blueprint for 'role' type in incubation
            default => null,
        };
    }
}
