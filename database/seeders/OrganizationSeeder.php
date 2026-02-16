<?php

namespace Database\Seeders;

use App\Models\Organizations;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        Organizations::updateOrCreate([
            'subdomain' => 'techcorp',
        ], [
            'name' => 'TechCorp',
            'industry' => 'Technology',
            'size' => 'large',
        ]);
    }
}
