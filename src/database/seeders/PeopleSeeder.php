<?php

namespace Database\Seeders;

use App\Models\Organizations;
use App\Models\People;
use App\Models\Roles;
use App\Models\Skills;
use Illuminate\Database\Seeder;

class PeopleSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organizations::first();
        $roles = Roles::all();
        $skills = Skills::all();

        $firstNames = ['Carlos', 'María', 'Juan', 'Ana', 'Pedro', 'Laura', 'Miguel', 'Sandra', 'Roberto', 'Elena', 'Fernando', 'Patricia', 'Diego', 'Beatriz', 'Andrés', 'Gabriela', 'Javier', 'Claudia', 'Ricardo', 'Verónica'];
        $lastNames = ['García', 'López', 'Martínez', 'Rodríguez', 'Pérez', 'Sánchez', 'González', 'Fernández', 'Torres', 'Ramírez'];

        for ($i = 0; $i < 20; $i++) {
            $firstName = $firstNames[$i % count($firstNames)];
            $lastName = $lastNames[rand(0, count($lastNames) - 1)];
            $email = strtolower($firstName . '.' . $lastName . '@techcorp.com');

            $currentRole = $roles[rand(0, count($roles) - 1)];

            $people = People::create([
                'organization_id' => $org->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'role_id' => $currentRole->id,
                'department_id' => null,
                'hire_date' => now()->subMonths(rand(3, 60)),
                'photo_url' => "https://api.dicebear.com/7.x/avataaars/svg?seed={$email}",
            ]);

            // Asignar skills a la persona
            $skillsForPeople = [];

            // Skills del rol actual
            $roleSkills = $currentRole->skills()->take(4)->get();
            foreach ($roleSkills as $skill) {
                $skillsForPeople[$skill->id] = [
                    'level' => rand(2, 5),
                    'last_evaluated_at' => now()->subMonths(rand(1, 12)),
                ];
            }

            // Skills adicionales aleatorias
            $additionalSkills = $skills->random(3);
            foreach ($additionalSkills as $skill) {
                if (!isset($skillsForPeople[$skill->id])) {
                    $skillsForPeople[$skill->id] = [
                        'level' => rand(1, 3),
                        'last_evaluated_at' => now()->subMonths(rand(1, 12)),
                    ];
                }
            }

            $people->skills()->attach($skillsForPeople);
        }
    }
}
