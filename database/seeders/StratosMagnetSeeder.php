<?php

namespace Database\Seeders;

use App\Models\JobOpening;
use App\Models\Organization;
use App\Models\Roles;
use Illuminate\Database\Seeder;

class StratosMagnetSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::where('subdomain', 'techcorp')->first();
        if (!$org) return;

        $roleIA = Roles::where('name', 'like', '%AI%')->first() ?? Roles::first();
        $roleLead = Roles::where('name', 'like', '%Lead%')->first() ?? Roles::first();

        JobOpening::create([
            'organization_id' => $org->id,
            'title' => 'Senior AI Research Engineer',
            'role_id' => $roleIA->id,
            'department' => 'R&D',
            'description' => 'Buscamos un experto en Modelos de Lenguaje para liderar nuestra próxima generación de agentes autónomos.',
            'requirements' => 'Experiencia en PyTorch, LLMs y optimización de inferencia.',
            'benefits' => 'Trabajo 100% remoto, bonos por patentes y presupuesto para conferencias.',
            'is_external' => true,
            'status' => 'open',
            'deadline' => now()->addMonths(2),
            'created_by' => 1
        ]);

        JobOpening::create([
            'organization_id' => $org->id,
            'title' => 'Strategic Talent Lead',
            'role_id' => $roleLead->id,
            'department' => 'People Experience',
            'description' => 'Buscamos transformar la gestión del talento usando Stratos. Si crees en el poder de la IA para potenciar a las personas, este es tu lugar.',
            'requirements' => '5+ años en HR Tech o consultoría estratégica.',
            'benefits' => 'Seguro médico premium, stock options y cultura radicalmente transparente.',
            'is_external' => true,
            'status' => 'open',
            'deadline' => now()->addMonths(1),
            'created_by' => 1
        ]);
    }
}
