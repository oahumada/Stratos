<?php

namespace Database\Seeders;

use App\Models\Roles;
use App\Models\Skills;
use Illuminate\Database\Seeder;

class RoleSkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = Skills::all();

        $rolesSkillsData = [
            'Backend Developer' => [
                ['name' => 'PHP', 'required_level' => 4, 'is_critical' => true],
                ['name' => 'Laravel', 'required_level' => 4, 'is_critical' => true],
                ['name' => 'Database Design', 'required_level' => 3, 'is_critical' => true],
                ['name' => 'REST APIs', 'required_level' => 3, 'is_critical' => true],
                ['name' => 'Git', 'required_level' => 3, 'is_critical' => true],
                ['name' => 'Problem Solving', 'required_level' => 4, 'is_critical' => true],
            ],
            'Frontend Developer' => [
                ['name' => 'Vue.js', 'required_level' => 4, 'is_critical' => true],
                ['name' => 'TypeScript', 'required_level' => 4, 'is_critical' => true],
                ['name' => 'CSS/Tailwind', 'required_level' => 3, 'is_critical' => false],
                ['name' => 'Git', 'required_level' => 3, 'is_critical' => true],
                ['name' => 'Communication', 'required_level' => 3, 'is_critical' => true],
                ['name' => 'Team Work', 'required_level' => 3, 'is_critical' => true],
            ],
            'Senior Full Stack Developer' => [
                ['name' => 'Laravel', 'required_level' => 5, 'is_critical' => true],
                ['name' => 'Vue.js', 'required_level' => 5, 'is_critical' => true],
                ['name' => 'TypeScript', 'required_level' => 4, 'is_critical' => true],
                ['name' => 'Database Design', 'required_level' => 4, 'is_critical' => true],
                ['name' => 'Leadership', 'required_level' => 4, 'is_critical' => false],
                ['name' => 'Problem Solving', 'required_level' => 5, 'is_critical' => true],
            ],
            'QA Engineer' => [
                ['name' => 'Testing (PHPUnit)', 'required_level' => 4, 'is_critical' => false],
                ['name' => 'Problem Solving', 'required_level' => 4, 'is_critical' => true],
                ['name' => 'Communication', 'required_level' => 3, 'is_critical' => true],
                ['name' => 'Agile Methodology', 'required_level' => 3, 'is_critical' => false],
                ['name' => 'Critical Thinking', 'required_level' => 4, 'is_critical' => true],
                ['name' => 'Team Work', 'required_level' => 3, 'is_critical' => true],
            ],
            'Product Manager' => [
                ['name' => 'Product Strategy', 'required_level' => 5, 'is_critical' => false],
                ['name' => 'Communication', 'required_level' => 5, 'is_critical' => true],
                ['name' => 'Leadership', 'required_level' => 4, 'is_critical' => false],
                ['name' => 'Requirements Gathering', 'required_level' => 4, 'is_critical' => false],
                ['name' => 'Stakeholder Management', 'required_level' => 4, 'is_critical' => false],
                ['name' => 'Data Analysis', 'required_level' => 3, 'is_critical' => false],
            ],
            'DevOps Engineer' => [
                ['name' => 'Docker', 'required_level' => 5, 'is_critical' => false],
                ['name' => 'PostgreSQL', 'required_level' => 4, 'is_critical' => true],
                ['name' => 'Git', 'required_level' => 4, 'is_critical' => true],
                ['name' => 'Problem Solving', 'required_level' => 4, 'is_critical' => true],
                ['name' => 'Team Work', 'required_level' => 3, 'is_critical' => true],
                ['name' => 'Communication', 'required_level' => 3, 'is_critical' => true],
            ],
            'Technical Lead' => [
                ['name' => 'Laravel', 'required_level' => 5, 'is_critical' => true],
                ['name' => 'Database Design', 'required_level' => 5, 'is_critical' => true],
                ['name' => 'Leadership', 'required_level' => 5, 'is_critical' => false],
                ['name' => 'Mentoring', 'required_level' => 4, 'is_critical' => false],
                ['name' => 'Communication', 'required_level' => 5, 'is_critical' => true],
                ['name' => 'Problem Solving', 'required_level' => 5, 'is_critical' => true],
            ],
            'Business Analyst' => [
                ['name' => 'Business Analysis', 'required_level' => 4, 'is_critical' => false],
                ['name' => 'Requirements Gathering', 'required_level' => 4, 'is_critical' => false],
                ['name' => 'Communication', 'required_level' => 4, 'is_critical' => true],
                ['name' => 'Problem Solving', 'required_level' => 3, 'is_critical' => true],
                ['name' => 'Data Analysis', 'required_level' => 3, 'is_critical' => false],
                ['name' => 'Team Work', 'required_level' => 3, 'is_critical' => true],
            ],
        ];

        foreach ($rolesSkillsData as $roleName => $skillsData) {
            $role = Roles::where('name', $roleName)->first();
            
            if (!$role) {
                continue;
            }

            $skillsToAttach = [];
            foreach ($skillsData as $skillData) {
                $skill = $skills->firstWhere('name', $skillData['name']);
                if ($skill) {
                    $skillsToAttach[$skill->id] = [
                        'required_level' => $skillData['required_level'],
                        'is_critical' => $skillData['is_critical'],
                    ];
                }
            }

            $role->skills()->attach($skillsToAttach);
        }
    }
}
