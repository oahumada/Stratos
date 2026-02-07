<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organizations;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class E2ESeeder extends Seeder
{
    /**
     * Seed the application's database with reproducible E2E data.
     */
    public function run(): void
    {
        // Ensure organization with id 1 exists
        $orgData = ['name' => 'E2E Test Org'];
        if (Schema::hasColumn('organizations', 'subdomain')) {
            $orgData['subdomain'] = env('E2E_ORG_SUBDOMAIN', 'e2e');
        }
        if (Schema::hasColumn('organizations', 'industry')) {
            $orgData['industry'] = 'testing';
        }
        if (Schema::hasColumn('organizations', 'size')) {
            $orgData['size'] = 'small';
        }
        if (Schema::hasColumn('organizations', 'slug')) {
            $orgData['slug'] = 'e2e-test-org';
        }

        $org = Organizations::firstOrCreate(
            ['id' => 1],
            $orgData
        );

        // Create admin user with known credentials
        $admin = User::firstOrCreate(
            ['email' => env('E2E_ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => 'E2E Admin',
                'organization_id' => $org->id,
                'password' => Hash::make(env('E2E_ADMIN_PASSWORD', 'password')),
                'role' => 'admin',
            ]
        );

        // Ensure at least one scenario exists for id 1 via ScenarioSeeder
        $this->call(ScenarioSeeder::class);

        // Optionally call other demo seeders to provide consistent baseline
        if (class_exists(DemoSeeder::class)) {
            $this->call(DemoSeeder::class);
        }
    }
}
