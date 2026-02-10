<?php

namespace App\Services;

use App\Models\Scenario;
use App\Models\ScenarioGeneration;
use App\Models\Capability;
use App\Models\Competency;
use App\Models\Skill;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ScenarioGenerationImporter
{
    /**
     * Import capabilities/competencies/skills from a ScenarioGeneration.llm_response
     * into the given Scenario as incubating entities.
     *
     * Returns a report array with created/attached ids and names.
     */
    public function importGeneration(Scenario $scenario, ScenarioGeneration $generation, array $options = []): array
    {
        $llm = $generation->llm_response ?? null;
        if (! is_array($llm)) {
            throw new \InvalidArgumentException('Invalid LLM response');
        }

        // If the provider stored a string in content, decode it
        if (isset($llm['content']) && is_string($llm['content'])) {
            $decoded = json_decode($llm['content'], true);
            if (is_array($decoded)) {
                $llm = array_merge($llm, $decoded);
            }
        }

        $caps = $llm['capabilities'] ?? ($llm['scenario']['capabilities'] ?? []);

        $report = ['capabilities' => [], 'competencies' => [], 'skills' => [], 'errors' => []];

        DB::beginTransaction();
        try {
            // mappings to store LLM id -> DB id for audit and incremental imports
            $mappings = ['capabilities' => [], 'competencies' => [], 'skills' => []];

            foreach ($caps as $capData) {
                $capName = trim($capData['name'] ?? ($capData[0] ?? ''));
                if (empty($capName)) {
                    continue;
                }

                // external id from LLM if present
                $capExtId = $capData['id'] ?? $capData['llm_id'] ?? $capData['uuid'] ?? null;

                // prefer match by llm_id within organization
                if (! empty($capExtId)) {
                    $cap = Capability::where('organization_id', $scenario->organization_id)
                        ->where('llm_id', (string) $capExtId)
                        ->first();
                } else {
                    // match by name (case-insensitive) when no external id provided
                    $cap = Capability::where('organization_id', $scenario->organization_id)
                        ->whereRaw('LOWER(name) = ?', [mb_strtolower($capName)])
                        ->first();
                }

                if (! $cap) {
                    $cap = Capability::create([
                        'organization_id' => $scenario->organization_id,
                        'llm_id' => empty($capExtId) ? null : (string) $capExtId,
                        'name' => $capName,
                        'description' => $capData['description'] ?? null,
                        'discovered_in_scenario_id' => $scenario->id,
                    ]);
                } else {
                    // update description/discovered flag when appropriate
                    $updated = false;
                    if (empty($cap->llm_id) && ! empty($capExtId)) {
                        $cap->llm_id = (string) $capExtId;
                        $updated = true;
                    }
                    if (empty($cap->discovered_in_scenario_id)) {
                        $cap->discovered_in_scenario_id = $scenario->id;
                        $updated = true;
                    }
                    if (! empty($capData['description']) && $cap->description !== $capData['description']) {
                        $cap->description = $capData['description'];
                        $updated = true;
                    }
                    if ($updated) {
                        $cap->save();
                    }
                }

                $report['capabilities'][] = ['id' => $cap->id, 'name' => $cap->name, 'created' => $cap->wasRecentlyCreated, 'llm_id' => $cap->llm_id ?? null];
                if (! empty($capExtId)) {
                    $mappings['capabilities'][(string) $capExtId] = $cap->id;
                }
                // ensure capability is linked to the scenario via scenario_capabilities pivot
                $scExists = DB::table('scenario_capabilities')
                    ->where('scenario_id', $scenario->id)
                    ->where('capability_id', $cap->id)
                    ->exists();

                if (! $scExists) {
                    DB::table('scenario_capabilities')->insert([
                        'scenario_id' => $scenario->id,
                        'capability_id' => $cap->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                    $comps = $capData['competencies'] ?? [];
                    foreach ($comps as $compData) {
                        $compName = trim($compData['name'] ?? ($compData[0] ?? ''));
                        if (empty($compName)) {
                            continue;
                        }

                        $compExtId = $compData['id'] ?? $compData['llm_id'] ?? $compData['uuid'] ?? null;

                        if (! empty($compExtId)) {
                            $comp = Competency::where('organization_id', $scenario->organization_id)
                                ->where('llm_id', (string) $compExtId)
                                ->first();
                        } else {
                            $comp = Competency::where('organization_id', $scenario->organization_id)
                                ->whereRaw('LOWER(name) = ?', [mb_strtolower($compName)])
                                ->first();
                        }

                        if (! $comp) {
                            $comp = Competency::create([
                                'organization_id' => $scenario->organization_id,
                                'llm_id' => empty($compExtId) ? null : (string) $compExtId,
                                'name' => $compName,
                                'description' => $compData['description'] ?? null,
                            ]);
                        } else {
                            $updated = false;
                            if (empty($comp->llm_id) && ! empty($compExtId)) {
                                $comp->llm_id = (string) $compExtId;
                                $updated = true;
                            }
                            if (! empty($compData['description']) && $comp->description !== $compData['description']) {
                                $comp->description = $compData['description'];
                                $updated = true;
                            }
                            if ($updated) {
                                $comp->save();
                            }
                        }

                        // Mark competency as discovered/incubating in this scenario when column exists
                        if (Schema::hasColumn('competencies', 'discovered_in_scenario_id') && empty($comp->discovered_in_scenario_id)) {
                            $comp->discovered_in_scenario_id = $scenario->id;
                            $comp->save();
                        }

                        $report['competencies'][] = ['id' => $comp->id, 'name' => $comp->name, 'created' => $comp->wasRecentlyCreated, 'llm_id' => $comp->llm_id ?? null];
                        if (! empty($compExtId)) {
                            $mappings['competencies'][(string) $compExtId] = $comp->id;
                        }

                        // attach capability_competencies pivot (scenario scoped)
                        $requiredLevel = (int) ($compData['required_level'] ?? 3);
                        $pivotExists = DB::table('capability_competencies')
                            ->where('scenario_id', $scenario->id)
                            ->where('capability_id', $cap->id)
                            ->where('competency_id', $comp->id)
                            ->exists();

                        if (! $pivotExists) {
                            DB::table('capability_competencies')->insert([
                                'scenario_id' => $scenario->id,
                                'capability_id' => $cap->id,
                                'competency_id' => $comp->id,
                                'required_level' => $requiredLevel,
                                'priority' => $compData['priority'] ?? null,
                                'weight' => $compData['weight'] ?? null,
                                'rationale' => $compData['rationale'] ?? null,
                                'is_required' => $compData['is_required'] ?? false,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                        // skills within competency
                        $skills = $compData['skills'] ?? [];
                        foreach ($skills as $skillItem) {
                            // skill can be string or object
                            if (is_array($skillItem)) {
                                $sname = trim($skillItem['name'] ?? ($skillItem[0] ?? ''));
                                $skillExtId = $skillItem['id'] ?? $skillItem['llm_id'] ?? null;
                            } else {
                                $sname = trim((string) $skillItem);
                                $skillExtId = null;
                            }
                            if ($sname === '') {
                                continue;
                            }

                            if (! empty($skillExtId)) {
                                $skill = Skill::where('organization_id', $scenario->organization_id)
                                    ->where('llm_id', (string) $skillExtId)
                                    ->first();
                            } else {
                                $skill = Skill::where('organization_id', $scenario->organization_id)
                                    ->whereRaw('LOWER(name) = ?', [mb_strtolower($sname)])
                                    ->first();
                            }

                            if (! $skill) {
                                $skill = Skill::create([
                                    'organization_id' => $scenario->organization_id,
                                    'llm_id' => empty($skillExtId) ? null : (string) $skillExtId,
                                    'name' => $sname,
                                    'description' => null,
                                ]);
                                Log::info('Created skill from importer', ['name' => $sname, 'llm_id' => $skillExtId, 'skill_id' => $skill->id]);
                            } else {
                                $updated = false;
                                if (empty($skill->llm_id) && ! empty($skillExtId)) {
                                    $skill->llm_id = (string) $skillExtId;
                                    $updated = true;
                                }
                                if ($updated) {
                                    $skill->save();
                                    Log::info('Updated skill llm_id from importer', ['name' => $skill->name, 'llm_id' => $skill->llm_id, 'skill_id' => $skill->id]);
                                }
                            }

                            // Mark skill as discovered/incubating in this scenario when column exists
                            if (Schema::hasColumn('skills', 'discovered_in_scenario_id') && empty($skill->discovered_in_scenario_id)) {
                                $skill->discovered_in_scenario_id = $scenario->id;
                                $skill->save();
                            }

                            $report['skills'][] = ['id' => $skill->id, 'name' => $skill->name, 'created' => $skill->wasRecentlyCreated, 'llm_id' => $skill->llm_id ?? null];
                            if (! empty($skillExtId)) {
                                $mappings['skills'][(string) $skillExtId] = $skill->id;
                            }

                            // attach competency_skills pivot
                            $csExists = DB::table('competency_skills')
                                ->where('competency_id', $comp->id)
                                ->where('skill_id', $skill->id)
                                ->exists();

                            if (! $csExists) {
                                DB::table('competency_skills')->insert([
                                    'competency_id' => $comp->id,
                                    'skill_id' => $skill->id,
                                    'weight' => $compData['skill_weight'] ?? 10,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    }
            }

            // persist mappings into generation metadata for traceability and incremental imports
            try {
                $generation->metadata = array_merge($generation->metadata ?? [], ['llm_mappings' => $mappings]);
                if (empty($generation->scenario_id)) {
                    $generation->scenario_id = $scenario->id;
                }
                $generation->save();
            } catch (\Throwable $e) {
                // non-fatal: log and continue
                \Log::warning('Could not persist generation mappings: '.$e->getMessage(), ['generation_id' => $generation->id]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $report['errors'][] = $e->getMessage();
        }

        return $report;
    }
}
