<?php

namespace Database\Seeders;

use App\Models\Organizations;
use App\Models\Roles;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organizations::first();

        $rolesData = [
            [
                'name' => 'Backend Developer',
                'department' => 'Engineering',
                'level' => 'mid',
                'description' => 'Desarrollador de backend con experiencia en Laravel y bases de datos',
            ],
            [
                'name' => 'Frontend Developer',
                'department' => 'Engineering',
                'level' => 'mid',
                'description' => 'Desarrollador frontend con experiencia en Vue.js y TypeScript',
            ],
            [
                'name' => 'Senior Full Stack Developer',
                'department' => 'Engineering',
                'level' => 'senior',
                'description' => 'Desarrollador full stack senior con experiencia integral',
            ],
            [
                'name' => 'QA Engineer',
                'department' => 'Quality Assurance',
                'level' => 'mid',
                'description' => 'Ingeniero QA enfocado en testing y calidad',
            ],
            [
                'name' => 'Product Manager',
                'department' => 'Product',
                'level' => 'senior',
                'description' => 'Gestor de productos encargado de estrategia y roadmap',
            ],
            [
                'name' => 'DevOps Engineer',
                'department' => 'Infrastructure',
                'level' => 'senior',
                'description' => 'Ingeniero DevOps con experiencia en deployment e infraestructura',
            ],
            [
                'name' => 'Technical Lead',
                'department' => 'Engineering',
                'level' => 'lead',
                'description' => 'Líder técnico responsable de arquitectura y equipo',
                'purpose' => 'Asegurar la excelencia técnica y el crecimiento del equipo mediante la definición de arquitecturas escalables y la mentoría continua.',
                'expected_results' => "1. Disponibilidad del sistema del 99.9%.\n2. Reducción de la deuda técnica en un 20% anual.\n3. Incremento en el nivel de maestría del equipo Engineering.",
            ],
            [
                'name' => 'Business Analyst',
                'department' => 'Business',
                'level' => 'mid',
                'description' => 'Analista de negocio enfocado en requerimientos y análisis',
            ],
        ];

        foreach ($rolesData as $roleData) {
            Roles::create([
                'organization_id' => $org->id,
                ...$roleData,
            ]);
        }
    }
}
