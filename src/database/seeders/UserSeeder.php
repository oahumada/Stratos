<?php

namespace Database\Seeders;

use App\Models\Organizations;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organizations::first();
        if (! $org) {
            $org = Organizations::create([
                'name' => 'Demo Org',
                'subdomain' => 'demo',
                'industry' => 'software',
                'size' => 'small',
            ]);
        }

        User::updateOrCreate([
            'email' => 'juan.perez@techcorp.com',
        ], [
            'organization_id' => $org->id,
            'name' => 'Juan Pérez',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'organization_id' => $org->id,
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
