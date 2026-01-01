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

        User::create([
            'organization_id' => $org->id,
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@techcorp.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        User::create([
            'organization_id' => $org->id,
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
