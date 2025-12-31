<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Roles;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Senior Developer',
                'description' => 'Lead development team with 5+ years of experience',
                'organization_id' => 1,
            ],
            [
                'name' => 'Junior Developer',
                'description' => 'Entry-level developer with mentorship',
                'organization_id' => 1,
            ],
            [
                'name' => 'Full Stack Developer',
                'description' => 'Developer proficient in frontend and backend',
                'organization_id' => 1,
            ],
            [
                'name' => 'Frontend Developer',
                'description' => 'Specialized in user interface development',
                'organization_id' => 1,
            ],
            [
                'name' => 'Backend Developer',
                'description' => 'Specialized in server-side development',
                'organization_id' => 1,
            ],
            [
                'name' => 'DevOps Engineer',
                'description' => 'Infrastructure and deployment specialist',
                'organization_id' => 1,
            ],
            [
                'name' => 'QA Engineer',
                'description' => 'Quality assurance and testing specialist',
                'organization_id' => 1,
            ],
            [
                'name' => 'Product Manager',
                'description' => 'Product strategy and roadmap management',
                'organization_id' => 1,
            ],
        ];

        foreach ($roles as $role) {
            Roles::create($role);
        }
    }
}
