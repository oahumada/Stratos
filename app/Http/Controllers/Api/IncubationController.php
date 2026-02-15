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
            'roles' => $this->getIncubatedItems(Roles::class, $scenarioId),
        ];

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    private function getIncubatedItems($modelClass, $scenarioId)
    {
        if ($modelClass === \App\Models\TalentBlueprint::class) {
             $query = $modelClass::where('scenario_id', $scenarioId)
                ->where('status', 'in_incubation');
             $items = $query->get();
             // Blueprints don't have embeddings yet, skip check
             return $items;
        }

        $query = $modelClass::where('discovered_in_scenario_id', $scenarioId)
            ->where('status', 'in_incubation');
            
        $items = $query->get();

        // Bonus: Check for duplicates/similar items if embeddings are enabled
        if (config('features.generate_embeddings', false)) {
            $items->transform(function ($item) use ($modelClass) {
                // If the item has an embedding, check for similar items in active status
                if (!empty($item->embedding)) {
                    $tableName = (new $modelClass)->getTable();
                    try {
                        // We search against the same table
                        // But we want to find items that are ACTIVE (not incubation) ideally,
                        // or at least warn about duplicates.
                        // Since `findSimilar` is generic, we might get the item itself or other incubation items.
                        // We'll filter in PHP for now.
                        
                        $vector = $this->vectorToArray($item->embedding);
                        
                        // Search for similar items (limit 5)
                        $similar = $this->embeddingService->findSimilar(
                            $tableName, 
                            $vector, 
                            5, 
                            $item->organization_id
                        );
                        
                        // Filter out:
                        // 1. The item itself
                        // 2. Items from the same scenario import (status=in_incubation with same scenario_id)
                        // We only want to warn about similarity to EXISTING, ACTIVE items.
                        
                        $warnings = [];
                        foreach ($similar as $match) {
                            if ($match->id == $item->id) continue;
                            
                            // Check status of match (we need to fetch it or join, but findSimilar returns minimal data)
                            // For performance, let's just show the match. If it's another incubation item, it's still good to know (internal duplicate).
                            
                            if ($match->similarity > 0.85) {
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
                         // robustly ignore embedding failures
                    }
                }
                
                // Hide embedding from JSON response
                $item->makeHidden('embedding');
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
                            // This part is simplified. In real Stratos, we would follow strict Role creation rules.
                            $realRole = \App\Models\Roles::firstOrCreate([
                                'organization_id' => $scenario->organization_id, // Assuming scenario has org_id or user
                                'name' => $entity->role_name,
                            ], [
                                'description' => $entity->role_description,
                                'status' => 'active',
                                'discovered_in_scenario_id' => $scenario->id,
                                'llm_id' => 'gen_' . $entity->id
                            ]);
                            
                            // Link Role to Scenario if not linked via ScenarioRoles table? 
                            // ScenarioRoles might be handled separately or here.
                            
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

        return response()->json(['success' => true, 'approved_count' => $approvedCount]);
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
