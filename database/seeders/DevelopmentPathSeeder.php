<?php

namespace Database\Seeders;

use App\Models\DevelopmentPath;
use App\Models\Organizations;
use App\Models\People;
use App\Models\Roles;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class DevelopmentPathSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organizations::first();
        $people = People::first();
        $targetRole = Roles::where('name', 'Senior Full Stack Developer')->first();
        $skills = Skill::all();

        if (! $people || ! $targetRole) {
            return;
        }

        DevelopmentPath::create([
            'action_title' => 'Personal development plan',
            'organization_id' => $org->id,
            'people_id' => $people->id,
            'target_role_id' => $targetRole->id,
            'status' => 'draft',
            'estimated_duration_months' => 6,
            'steps' => [
                [
                    'skill_id' => $skills->firstWhere('name', 'Vue.js')->id,
                    'skill_name' => 'Vue.js',
                    'action_type' => 'course',
                    'resource_name' => 'Vue.js - The Complete Guide',
                    'resource_url' => 'https://www.udemy.com/course/vuejs-2-the-complete-guide/',
                    'duration_hours' => 40,
                    'completed' => false,
                ],
                [
                    'skill_id' => $skills->firstWhere('name', 'TypeScript')->id,
                    'skill_name' => 'TypeScript',
                    'action_type' => 'course',
                    'resource_name' => 'TypeScript Pro Course',
                    'resource_url' => 'https://www.udemy.com/course/typescript-the-complete-developers-guide/',
                    'duration_hours' => 30,
                    'completed' => false,
                ],
                [
                    'skill_id' => $skills->firstWhere('name', 'Docker')->id,
                    'skill_name' => 'Docker',
                    'action_type' => 'mentoring',
                    'resource_name' => 'Work with DevOps team',
                    'resource_url' => null,
                    'duration_hours' => 20,
                    'completed' => false,
                ],
            ],
        ]);
    }
}
