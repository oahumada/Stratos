<?php

namespace Database\Seeders;

use App\Models\JobOpening;
use App\Models\Organizations;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobOpeningSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organizations::first();
        $adminUser = User::first();
        $roles = Roles::all();

        $jobTitles = [
            ['title' => 'Senior Backend Developer', 'role_name' => 'Senior Full Stack Developer'],
            ['title' => 'Frontend Developer', 'role_name' => 'Frontend Developer'],
            ['title' => 'QA Engineer', 'role_name' => 'QA Engineer'],
            ['title' => 'Product Manager', 'role_name' => 'Product Manager'],
            ['title' => 'DevOps Engineer', 'role_name' => 'DevOps Engineer'],
        ];

        foreach ($jobTitles as $jobData) {
            $role = $roles->where('name', $jobData['role_name'])->first();

            if ($role) {
                JobOpening::create([
                    'organization_id' => $org->id,
                    'title' => $jobData['title'],
                    'role_id' => $role->id,
                    'department' => $role->department,
                    'status' => 'open',
                    'deadline' => now()->addMonths(1),
                    'created_by' => $adminUser->id,
                ]);
            }
        }
    }
}
