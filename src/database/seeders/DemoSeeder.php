<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\DevelopmentPath;
use App\Models\JobOpening;
use App\Models\Organization;
use App\Models\People;
use App\Models\Roles;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear organización TechCorp
        $org = Organization::create([
            'name' => 'TechCorp',
            'subdomain' => 'techcorp',
            'industry' => 'Technology',
            'size' => 'large',
        ]);

        // 2. Crear usuario administrador
        $adminUser = User::create([
            'organization_id' => $org->id,
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@techcorp.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 3. Crear 30 skills (técnicas, soft, business)
        $skillsData = [
            // Technical Skills (12)
            ['name' => 'PHP', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'Laravel', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'Vue.js', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'React', 'category' => 'technical', 'is_critical' => false],
            ['name' => 'TypeScript', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'Database Design', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'PostgreSQL', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'REST APIs', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'Docker', 'category' => 'technical', 'is_critical' => false],
            ['name' => 'Git', 'category' => 'technical', 'is_critical' => true],
            ['name' => 'CSS/Tailwind', 'category' => 'technical', 'is_critical' => false],
            ['name' => 'Testing (PHPUnit)', 'category' => 'technical', 'is_critical' => false],

            // Soft Skills (9)
            ['name' => 'Communication', 'category' => 'soft', 'is_critical' => true],
            ['name' => 'Leadership', 'category' => 'soft', 'is_critical' => false],
            ['name' => 'Problem Solving', 'category' => 'soft', 'is_critical' => true],
            ['name' => 'Team Work', 'category' => 'soft', 'is_critical' => true],
            ['name' => 'Critical Thinking', 'category' => 'soft', 'is_critical' => true],
            ['name' => 'Adaptability', 'category' => 'soft', 'is_critical' => false],
            ['name' => 'Project Management', 'category' => 'soft', 'is_critical' => false],
            ['name' => 'Mentoring', 'category' => 'soft', 'is_critical' => false],
            ['name' => 'Agile Methodology', 'category' => 'soft', 'is_critical' => false],

            // Business Skills (9)
            ['name' => 'Business Analysis', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Product Strategy', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Requirements Gathering', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Client Management', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Budget Planning', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Market Research', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Stakeholder Management', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Sales Skills', 'category' => 'business', 'is_critical' => false],
            ['name' => 'Data Analysis', 'category' => 'business', 'is_critical' => false],
        ];

        $skills = collect($skillsData)->map(function ($skill) use ($org) {
            return Skill::create([
                'organization_id' => $org->id,
                ...$skill,
            ]);
        });

        // 4. Crear 8 roles con sus skills requeridas
        $rolesData = [
            [
                'name' => 'Backend Developer',
                'department' => 'Engineering',
                'level' => 'mid',
                'description' => 'Desarrollador de backend con experiencia en Laravel y bases de datos',
                'required_skills' => ['PHP', 'Laravel', 'Database Design', 'PostgreSQL', 'REST APIs', 'Git', 'Communication', 'Problem Solving', 'Team Work'],
            ],
            [
                'name' => 'Frontend Developer',
                'department' => 'Engineering',
                'level' => 'mid',
                'description' => 'Desarrollador frontend con experiencia en Vue.js y TypeScript',
                'required_skills' => ['Vue.js', 'TypeScript', 'CSS/Tailwind', 'Git', 'Problem Solving', 'Communication', 'Team Work'],
            ],
            [
                'name' => 'Senior Full Stack Developer',
                'department' => 'Engineering',
                'level' => 'senior',
                'description' => 'Desarrollador full stack senior con experiencia integral',
                'required_skills' => ['PHP', 'Laravel', 'Vue.js', 'TypeScript', 'Database Design', 'PostgreSQL', 'REST APIs', 'Git', 'Docker', 'Leadership', 'Problem Solving', 'Communication', 'Mentoring'],
            ],
            [
                'name' => 'QA Engineer',
                'department' => 'Quality Assurance',
                'level' => 'mid',
                'description' => 'Ingeniero QA enfocado en testing y calidad',
                'required_skills' => ['Testing (PHPUnit)', 'Problem Solving', 'Communication', 'Agile Methodology', 'Attention to Detail'],
            ],
            [
                'name' => 'Product Manager',
                'department' => 'Product',
                'level' => 'senior',
                'description' => 'Gestor de productos encargado de estrategia y roadmap',
                'required_skills' => ['Product Strategy', 'Communication', 'Leadership', 'Requirements Gathering', 'Market Research', 'Data Analysis', 'Stakeholder Management'],
            ],
            [
                'name' => 'DevOps Engineer',
                'department' => 'Infrastructure',
                'level' => 'senior',
                'description' => 'Ingeniero DevOps con experiencia en deployment e infraestructura',
                'required_skills' => ['Docker', 'PostgreSQL', 'Git', 'Problem Solving', 'Linux', 'CI/CD'],
            ],
            [
                'name' => 'Technical Lead',
                'department' => 'Engineering',
                'level' => 'lead',
                'description' => 'Líder técnico responsable de arquitectura y equipo',
                'required_skills' => ['Laravel', 'Vue.js', 'Database Design', 'Leadership', 'Architecture Design', 'Mentoring', 'Communication'],
            ],
            [
                'name' => 'Business Analyst',
                'department' => 'Business',
                'level' => 'mid',
                'description' => 'Analista de negocio enfocado en requerimientos y análisis',
                'required_skills' => ['Business Analysis', 'Requirements Gathering', 'Communication', 'Problem Solving', 'Data Analysis'],
            ],
        ];

        $roles = collect($rolesData)->map(function ($roleData) use ($org, $skills) {
            $role = Roles::create([
                'organization_id' => $org->id,
                'name' => $roleData['name'],
                'department' => $roleData['department'],
                'level' => $roleData['level'],
                'description' => $roleData['description'],
            ]);

            // Asociar skills requeridas al rol
            $skillsToAttach = [];
            foreach ($roleData['required_skills'] as $skillName) {
                $skill = $skills->firstWhere('name', $skillName);
                if ($skill) {
                    $skillsToAttach[$skill->id] = [
                        'required_level' => rand(2, 5),
                        'is_critical' => $skill->is_critical,
                    ];
                }
            }

            $role->skills()->attach($skillsToAttach);
            return $role;
        });

        // 5. Crear 20 peopleas (empleados) con sus skills
        $firstNames = ['Carlos', 'María', 'Juan', 'Ana', 'Pedro', 'Laura', 'Miguel', 'Sandra', 'Roberto', 'Elena', 'Fernando', 'Patricia', 'Diego', 'Beatriz', 'Andrés', 'Gabriela', 'Javier', 'Claudia', 'Ricardo', 'Verónica'];
        $lastNames = ['García', 'López', 'Martínez', 'Rodríguez', 'Pérez', 'Sánchez', 'González', 'Fernández', 'Torres', 'Ramírez'];

        $People = [];
        $departments = ['Engineering', 'Quality Assurance', 'Product', 'Infrastructure', 'Business'];

        for ($i = 0; $i < 20; $i++) {
            $firstName = $firstNames[$i % count($firstNames)];
            $lastName = $lastNames[rand(0, count($lastNames) - 1)];
            $email = strtolower($firstName . '.' . $lastName . '@techcorp.com');

            // Cada peoplea tiene un rol actual
            $currentRole = $roles[rand(0, count($roles) - 1)];

            $people = People::create([
                'organization_id' => $org->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'current_role_id' => $currentRole->id,
                'department' => $currentRole->department,
                'hire_date' => now()->subMonths(rand(3, 60)),
                'photo_url' => "https://api.dicebear.com/7.x/avataaars/svg?seed={$email}",
            ]);

            // Asignar skills a la peoplea (subconjunto del rol + algunas adicionales)
            $skillsForPeople = [];

            // Primero agregar algunas skills del rol actual con niveles realistas
            $roleSkills = $currentRole->skills()->take(4)->get();
            foreach ($roleSkills as $skill) {
                $skillsForPeople[$skill->id] = [
                    'level' => rand(2, 5),
                    'last_evaluated_at' => now()->subMonths(rand(1, 12)),
                ];
            }

            // Agregar algunas skills adicionales aleatorias (para crear brechas interesantes)
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
            $People[] = $people;
        }

        // 6. Crear 5 vacantes internas
        $jobOpenings = [];
        $jobTitles = [
            ['title' => 'Senior Backend Developer', 'role_id' => $roles->where('name', 'Senior Full Stack Developer')->first()->id],
            ['title' => 'Frontend Developer', 'role_id' => $roles->where('name', 'Frontend Developer')->first()->id],
            ['title' => 'QA Engineer', 'role_id' => $roles->where('name', 'QA Engineer')->first()->id],
            ['title' => 'Product Manager', 'role_id' => $roles->where('name', 'Product Manager')->first()->id],
            ['title' => 'DevOps Engineer', 'role_id' => $roles->where('name', 'DevOps Engineer')->first()->id],
        ];

        foreach ($jobTitles as $jobData) {
            $jobOpening = JobOpening::create([
                'organization_id' => $org->id,
                'title' => $jobData['title'],
                'role_id' => $jobData['role_id'],
                'department' => $roles->firstWhere('id', $jobData['role_id'])->department,
                'status' => 'open',
                'deadline' => now()->addMonths(1),
                'created_by' => $adminUser->id,
            ]);
            $jobOpenings[] = $jobOpening;
        }

        // 7. Crear 10 postulaciones (applications)
        $applicationStatuses = ['pending', 'under_review', 'accepted'];
        $applicationsCreated = 0;

        foreach ($jobOpenings as $jobOpening) {
            // Cada vacante tiene 2 postulantes
            $candidates = collect($People)->random(2);

            foreach ($candidates as $candidate) {
                if ($applicationsCreated < 10) {
                    Application::create([
                        'job_opening_id' => $jobOpening->id,
                        'people_id' => $candidate->id,
                        'status' => $applicationStatuses[rand(0, count($applicationStatuses) - 1)],
                        'message' => "Interested in this opportunity. I believe my skills align well with the role requirements.",
                        'applied_at' => now()->subDays(rand(1, 30)),
                    ]);
                    $applicationsCreated++;
                }
            }
        }

        // 8. Crear una ruta de desarrollo de ejemplo
        $people = $People[0];
        $targetRole = $roles->where('name', 'Senior Full Stack Developer')->first();

        DevelopmentPath::create([
            'organization_id' => $org->id,
            'people_id' => $people->id,
            'target_role_id' => $targetRole->id,
            'status' => 'draft',
            'estimated_duration_months' => 6,
            'steps' => [
                [
                    'skill_id' => $skills->firstWhere('name', 'Vue.js')->id,
                    'skill_name' => 'Vue.js',
                    'action_type' => 'course',
                    'resource_name' => 'Vue.js - The Complete Guide',
                    'resource_url' => 'https://www.udemy.com/course/vuejs-2-the-complete-guide/',
                    'duration_hours' => 40,
                    'completed' => false,
                ],
                [
                    'skill_id' => $skills->firstWhere('name', 'TypeScript')->id,
                    'skill_name' => 'TypeScript',
                    'action_type' => 'course',
                    'resource_name' => 'TypeScript Pro Course',
                    'resource_url' => 'https://www.udemy.com/course/typescript-the-complete-developers-guide/',
                    'duration_hours' => 30,
                    'completed' => false,
                ],
                [
                    'skill_id' => $skills->firstWhere('name', 'Docker')->id,
                    'skill_name' => 'Docker',
                    'action_type' => 'mentoring',
                    'resource_name' => 'Work with DevOps team',
                    'resource_url' => null,
                    'duration_hours' => 20,
                    'completed' => false,
                ],
            ],
        ]);

        echo "✅ Demo seeder completado:\n";
        echo "   • 1 Organización (TechCorp)\n";
        echo "   • 1 Usuario admin\n";
        echo "   • 30 Skills\n";
        echo "   • 8 Roles\n";
        echo "   • 20 Peopleas\n";
        echo "   • 5 Vacantes\n";
        echo "   • 10 Postulaciones\n";
        echo "   • 1 Ruta de desarrollo\n";
    }
}
