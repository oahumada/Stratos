<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CapabilitySeeder::class,
            DemoSeeder::class,
            // Seed demo data first, then scenario-specific seeders
            ScenarioSeeder::class,
            // ScenarioTemplateSeeder::class,
            // WorkforcePlanningSeeder::class,
            \Database\Seeders\PromptInstructionsSeeder::class,
        ]);
    }
}
