<?php

namespace Database\Seeders;

use App\Models\Organizations;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\RoleSkill;
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
                'department_id' =>  $currentRole->id,
                'hire_date' => now()->subMonths(rand(3, 60)),
                'photo_url' => "https://api.dicebear.com/7.x/avataaars/svg?seed={$email}",
            ]);

            // Asignar skills a la persona usando people_role_skills
            $expiresAt = now()->addMonths(6);

            // PARTE 1: Skills obligatorias del rol actual (desde RoleSkill)
            // Todas las personas con el mismo rol tendrán estas skills (6 por rol)
            // Estas son las skills que DEBE tener para cumplir su rol
            $roleSkills = RoleSkill::where('role_id', $currentRole->id)->get();
            foreach ($roleSkills as $roleSkill) {
                PeopleRoleSkills::create([
                    'people_id' => $people->id,
                    'role_id' => $currentRole->id,
                    'skill_id' => $roleSkill->skill_id,
                    'current_level' => rand(2, 5),
                    'required_level' => $roleSkill->required_level,
                    'is_active' => true,
                    'evaluated_at' => now()->subMonths(rand(1, 12)),
                    'expires_at' => $expiresAt,
                    'notes' => 'Demo seeder - skill del rol actual',
                ]);
            }

            // PARTE 2: Skills adicionales aleatorias (2-4 por persona)
            // IMPORTANTE: Esto causa que personas con el MISMO rol tengan DIFERENTE cantidad de skills
            // Ejemplo: Backend Developer puede tener 8, 9 o 10 skills total (6 del rol + 2-4 adicionales)
            // 
            // RAZÓN: Representa skills que la persona tiene por experiencia previa/intereses
            // pero que NO son obligatorias del rol actual
            // 
            // IMPACTO FUTURO:
            // - Gap Analysis: Considerar solo skills del rol (6) o incluir adicionales?
            // - Learning Paths: Las adicionales son "nice to have" o ignorarlas?
            // - Scoring: ¿Dan ventaja las skills extra para promociones?
            // - Reportes: Denominador variable (8/8 vs 9/9) puede confundir métricas
            // 
            // DECISIÓN: Dejado así para MVP (realismo), revisar cuando se implemente
            // Gap Analysis o Learning Paths
            $additionalSkills = $skills->random(rand(2, 4));
            foreach ($additionalSkills as $skill) {
                // Verificar que no esté duplicada (evita que skill del rol aparezca 2 veces)
                $exists = PeopleRoleSkills::where('people_id', $people->id)
                    ->where('skill_id', $skill->id)
                    ->where('is_active', true)
                    ->exists();
                
                if (!$exists) {
                    PeopleRoleSkills::create([
                        'people_id' => $people->id,
                        'role_id' => $currentRole->id,
                        'skill_id' => $skill->id,
                        'current_level' => rand(1, 3),
                        'required_level' => 2,
                        'is_active' => true,
                        'evaluated_at' => now()->subMonths(rand(1, 12)),
                        'expires_at' => $expiresAt,
                        'notes' => 'Demo seeder - skill adicional',
                    ]);
                }
            }
        }
    }
}
