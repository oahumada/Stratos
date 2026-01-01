<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\People;
use App\Models\Departments;
use App\Models\Roles;
use App\Models\Skills;
use App\Models\Organizations;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Limpiar tablas involucradas
        People::truncate();
        Departments::truncate();
        Roles::truncate();
        Skills::truncate();
        User::truncate();
        Organizations::truncate();

        // Crear organización por defecto
        $org = Organizations::create([
            'name' => 'Default Organization',
            'subdomain' => 'default',
        ]);

        // User::factory(10)->create();

        

        $this->call([
            DepartmentSeeder::class,
            RoleSeeder::class,
            SkillSeeder::class,
        ]);

        User::create([
            'name' => 'Admin',
            //'lastname' => 'User',
          //  'rut' => RutGenerator::generate(),
            'organization_id' => 1,
            'email' => 'admin@example.com',
            'password' => Hash::make('clave123'),
            'role' => 'admin',
        ]);
        
        // Crear 5 registros de People después de las tablas relacionadas
        People::factory(5)->create();

        // Assign skills to people
        $this->call([
            PeopleSkillsSeeder::class,
        ]);
    }
}