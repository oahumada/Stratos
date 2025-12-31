<?php

namespace Database\Seeders;

use App\Models\Skills;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            // Programación (technical)
            ['name' => 'PHP', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'JavaScript', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'Python', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'Java', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'C#', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'TypeScript', 'category' => 'technical', 'organization_id' => 1],
            
            // Frontend (technical)
            ['name' => 'Vue.js', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'React', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'Angular', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'HTML5', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'CSS3', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'Tailwind CSS', 'category' => 'technical', 'organization_id' => 1],
            
            // Backend (technical)
            ['name' => 'Laravel', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'Node.js', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'Express.js', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'Django', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'Spring Boot', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'REST API', 'category' => 'technical', 'organization_id' => 1],
            
            // Base de Datos (technical)
            ['name' => 'MySQL', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'PostgreSQL', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'MongoDB', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'Redis', 'category' => 'technical', 'organization_id' => 1],
            
            // DevOps (technical)
            ['name' => 'Docker', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'Kubernetes', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'Git', 'category' => 'technical', 'organization_id' => 1],
            ['name' => 'CI/CD', 'category' => 'technical', 'organization_id' => 1],
            
            // Blandas (soft)
            ['name' => 'Liderazgo', 'category' => 'soft', 'organization_id' => 1],
            ['name' => 'Comunicación', 'category' => 'soft', 'organization_id' => 1],
            ['name' => 'Trabajo en Equipo', 'category' => 'soft', 'organization_id' => 1],
            ['name' => 'Resolución de Problemas', 'category' => 'soft', 'organization_id' => 1],
            ['name' => 'Pensamiento Crítico', 'category' => 'soft', 'organization_id' => 1],
        ];

        foreach ($skills as $skill) {
            Skills::create($skill);
        }
    }
}
