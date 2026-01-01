<?php

namespace Database\Seeders;

use App\Models\People;
use App\Models\Skills;
use App\Models\PeopleSkill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeopleSkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all people and skills
        $peoples = People::all();
        $skills = Skills::all();

        if ($peoples->isEmpty() || $skills->isEmpty()) {
            $this->command->warn('No people or skills found. Skipping PeopleSkills seeding.');
            return;
        }

        // For each person, assign 5-6 random skills with random levels
        foreach ($peoples as $person) {
            // Get 5-6 random skills
            $randomSkills = $skills->random(rand(5, min(6, $skills->count())));

            foreach ($randomSkills as $skill) {
                PeopleSkill::create([
                    'people_id' => $person->id,
                    'skill_id' => $skill->id,
                    'level' => rand(1, 5), // Level between 1 and 5
                    'last_evaluated_at' => now()->subDays(rand(0, 30)),
                    'evaluated_by' => null, // Can be updated later
                ]);
            }

            $this->command->info("Assigned skills to: {$person->first_name} {$person->last_name}");
        }

        $this->command->info('PeopleSkills seeding completed successfully!');
    }
}
