<?php

namespace Database\Seeders;

use App\Models\Scenario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScenarioCapabilitiesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $scenarios = Scenario::all();
        if ($scenarios->isEmpty()) {
            $this->command->info('No scenarios found, skipping ScenarioCapabilitiesSeeder');

            return;
        }

        foreach ($scenarios as $scenario) {
            $existing = DB::table('scenario_capabilities')->where('scenario_id', $scenario->id)->count();
            if ($existing >= 3) {
                $this->command->info("Scenario {$scenario->id} already has {$existing} capabilities, skipping");

                continue;
            }

            $names = [
                'AI Platform',
                'Data Infrastructure',
                'ML Ops',
                'Prompt Engineering',
                'User Experience',
                'Analytics & Instrumentation',
            ];

            // create between 3 and 6 capabilities per scenario
            $count = rand(3, 6);
            $createdIds = [];
            for ($i = 0; $i < $count; $i++) {
                $name = $names[$i % count($names)].' ('.$scenario->id.')';

                $capId = DB::table('capabilities')->insertGetId([
                    'organization_id' => $scenario->organization_id,
                    'name' => $name,
                    'description' => "Auto-seeded capability for scenario {$scenario->id}",
                    'position_x' => 100 + ($i * 120),
                    'position_y' => 100 + (($i % 2) * 140),
                    'importance' => 3,
                    'type' => 'technical',
                    'category' => 'technical',
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('scenario_capabilities')->insert([
                    'scenario_id' => $scenario->id,
                    'capability_id' => $capId,
                    'strategic_role' => 'target',
                    'strategic_weight' => 50,
                    'priority' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $createdIds[] = $capId;
            }

            // ensure capability_links table exists and create simple sequential links
            DB::statement('CREATE TABLE IF NOT EXISTS capability_links (id INTEGER PRIMARY KEY, source_id INTEGER, target_id INTEGER, is_critical INTEGER DEFAULT 0)');
            for ($j = 0; $j < count($createdIds) - 1; $j++) {
                DB::table('capability_links')->insert([
                    'source_id' => $createdIds[$j],
                    'target_id' => $createdIds[$j + 1],
                    'is_critical' => $j % 2 === 0 ? 1 : 0,
                ]);
            }

            $this->command->info("Seeded {$count} capabilities for scenario {$scenario->id}");
        }
    }
}
