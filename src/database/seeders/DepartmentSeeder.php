<?php

namespace Database\Seeders;

use App\Models\Departments;
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
            ['name' => 'Engineering', 'description' => 'Software and product development', 'organization_id' => 1],
            ['name' => 'Sales', 'description' => 'Sales and business development', 'organization_id' => 1],
            ['name' => 'Marketing', 'description' => 'Marketing and communications', 'organization_id' => 1],
            ['name' => 'Human Resources', 'description' => 'HR and recruitment', 'organization_id' => 1],
            ['name' => 'Finance', 'description' => 'Finance and accounting', 'organization_id' => 1],
            ['name' => 'Operations', 'description' => 'Operations and logistics', 'organization_id' => 1],
            ['name' => 'Support', 'description' => 'Customer support and success', 'organization_id' => 1],
            ['name' => 'Product', 'description' => 'Product management and design', 'organization_id' => 1],
        ];

        foreach ($departments as $department) {
            Departments::firstOrCreate(
                ['name' => $department['name']
            ],
                $department
            );
        }
    }
}
