<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Engineering', 'description' => 'Software and product development'],
            ['name' => 'Sales', 'description' => 'Sales and business development'],
            ['name' => 'Marketing', 'description' => 'Marketing and communications'],
            ['name' => 'Human Resources', 'description' => 'HR and recruitment'],
            ['name' => 'Finance', 'description' => 'Finance and accounting'],
            ['name' => 'Operations', 'description' => 'Operations and logistics'],
            ['name' => 'Support', 'description' => 'Customer support and success'],
            ['name' => 'Product', 'description' => 'Product management and design'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['name' => $department['name']],
                $department
            );
        }
    }
}
