<?php

namespace App\Services;

use App\Models\Scenario;
use App\Models\ScenarioGeneration;
use App\Models\Capability;
use App\Models\Competency;
use App\Models\Skill;
use Illuminate\Support\Facades\DB;

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

        $caps = $llm['capabilities'] ?? ($llm['scenario']['capabilities'] ?? []);

        $report = ['capabilities' => [], 'competencies' => [], 'skills' => [], 'errors' => []];

        DB::beginTransaction();
        try {
            foreach ($caps as $capData) {
                $capName = trim($capData['name'] ?? ($capData[0] ?? ''));
                if (empty($capName)) {
                    continue;
                }

                $cap = Capability::firstOrCreate([
                    'organization_id' => $scenario->organization_id,
                    'name' => $capName,
                ], [
                    'description' => $capData['description'] ?? null,
                    'discovered_in_scenario_id' => $scenario->id,
                ]);

                $report['capabilities'][] = ['id' => $cap->id, 'name' => $cap->name, 'created' => $cap->wasRecentlyCreated];

                $comps = $capData['competencies'] ?? [];
                foreach ($comps as $compData) {
                    $compName = trim($compData['name'] ?? ($compData[0] ?? ''));
                    if (empty($compName)) {
                        continue;
                    }

                    $comp = Competency::firstOrCreate([
                        'organization_id' => $scenario->organization_id,
                        'name' => $compName,
                    ], [
                        'description' => $compData['description'] ?? null,
                    ]);

                    $report['competencies'][] = ['id' => $comp->id, 'name' => $comp->name, 'created' => $comp->wasRecentlyCreated];

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
                    foreach ($skills as $skillName) {
                        $sname = trim((string) $skillName);
                        if ($sname === '') {
                            continue;
                        }

                        $skill = Skill::firstOrCreate([
                            'organization_id' => $scenario->organization_id,
                            'name' => $sname,
                        ], [
                            'description' => null,
                        ]);

                        $report['skills'][] = ['id' => $skill->id, 'name' => $skill->name, 'created' => $skill->wasRecentlyCreated];

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

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $report['errors'][] = $e->getMessage();
        }

        return $report;
    }
}
