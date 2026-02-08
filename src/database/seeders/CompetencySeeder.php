<?php

namespace Database\Seeders;

use App\Models\Capability;
use App\Models\Competency;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class CompetencySeeder extends Seeder
{
    public function run(): void
    {
        $caps = Capability::all();
        if ($caps->isEmpty()) {
            $this->command->info('No capabilities found, skipping CompetencySeeder');

            return;
        }

        foreach ($caps as $cap) {
            // Check if capability already has competencies attached (via pivot)
            $existing = $cap->competencies()->count();
            if ($existing >= 1) {
                $this->command->info("Capability {$cap->id} already has {$existing} competencies, skipping");

                continue;
            }

            $count = rand(1, 3);
            // Skills are not directly scoped by capability in the current schema;
            // use organization skills as a pool for attaching to competencies.
            $skillPool = Skill::where('organization_id', $cap->organization_id)->get();

            for ($i = 1; $i <= $count; $i++) {
                $name = $cap->name.' - Competency '.$i;
                // Create competency WITHOUT capability_id (N:N model)
                $comp = Competency::create([
                    'organization_id' => $cap->organization_id,
                    'name' => $name,
                    'description' => 'Auto-seeded competency for '.$cap->name,
                ]);

                // attach a few skills (1..4) from the capability as competency skills
                if ($skillPool->isNotEmpty()) {
                    $n = min(4, max(1, intval($skillPool->count() > 0 ? rand(1, $skillPool->count()) : 1)));
                    $selected = $skillPool->shuffle()->take($n);
                    foreach ($selected as $s) {
                        $comp->skills()->attach($s->id, ['weight' => rand(10, 100)]);
                    }
                }
            }
            $this->command->info("Seeded {$count} competencies for capability {$cap->id}");
        }
    }
}
