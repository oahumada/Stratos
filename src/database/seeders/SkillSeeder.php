<?php

namespace Database\Seeders;

use App\Models\Organizations;
use App\Models\Skills;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organizations::first();

        $skillsData = [
            // Technical Skills (12)
            ['name' => 'PHP', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'Laravel', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'Vue.js', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'React', 'category' => 'technical', 'is_critical' => false],
            ['name' => 'TypeScript', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'Database Design', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'PostgreSQL', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'REST APIs', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'Docker', 'category' => 'technical', 'is_critical' => false],
            ['name' => 'Git', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'CSS/Tailwind', 'category' => 'technical', 'is_critical' => false],
            ['name' => 'Testing (PHPUnit)', 'category' => 'technical', 'is_critical' => false],

            // Soft Skills (9)
            ['name' => 'Communication', 'category' => 'soft', 'is_critical' => true],
            ['name' => 'Leadership', 'category' => 'soft', 'is_critical' => false],
            ['name' => 'Problem Solving', 'category' => 'soft', 'is_critical' => true],
            ['name' => 'Team Work', 'category' => 'soft', 'is_critical' => true],
            ['name' => 'Critical Thinking', 'category' => 'soft', 'is_critical' => true],
            ['name' => 'Adaptability', 'category' => 'soft', 'is_critical' => false],
            ['name' => 'Project Management', 'category' => 'soft', 'is_critical' => false],
            ['name' => 'Mentoring', 'category' => 'soft', 'is_critical' => false],
            ['name' => 'Agile Methodology', 'category' => 'soft', 'is_critical' => false],

            // Business Skills (9)
            ['name' => 'Business Analysis', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Product Strategy', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Requirements Gathering', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Client Management', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Budget Planning', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Market Research', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Stakeholder Management', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Sales Skills', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Data Analysis', 'category' => 'business', 'is_critical' => false],
        ];

        foreach ($skillsData as $skill) {
            Skills::create([
                'organization_id' => $org->id,
                ...$skill,
            ]);
        }
    }
}
