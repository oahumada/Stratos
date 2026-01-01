<?php

namespace Database\Seeders;

use App\Models\Organizations;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        Organizations::create([
            'name' => 'TechCorp',
            'subdomain' => 'techcorp',
            'industry' => 'Technology',
            'size' => 'large',
        ]);
    }
}
