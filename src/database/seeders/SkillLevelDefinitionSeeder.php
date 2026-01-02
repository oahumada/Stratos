<?php

namespace Database\Seeders;

use App\Models\SkillLevelDefinition;
use Illuminate\Database\Seeder;

class SkillLevelDefinitionSeeder extends Seeder
{
    /**
     * Seed the skill level definitions table.
     * 
     * Sistema de niveles genéricos aplicables a todas las skills.
     * Progresión: Autonomía funcional, Complejidad, Responsabilidad
     */
    public function run(): void
    {
        $levels = [
            [
                'level' => 1,
                'name' => 'Básico',
                'description' => 'Conocimiento teórico fundamental. Requiere supervisión constante. Ejecuta tareas simples siguiendo instrucciones detalladas. Mínima autonomía.',
                'points' => 10,
            ],
            [
                'level' => 2,
                'name' => 'Intermedio',
                'description' => 'Puede ejecutar tareas con supervisión ocasional. Comprende conceptos intermedios. Resuelve problemas conocidos. Requiere validación periódica.',
                'points' => 25,
            ],
            [
                'level' => 3,
                'name' => 'Avanzado',
                'description' => 'Ejecuta de forma autónoma. Resuelve problemas complejos sin supervisión. Toma decisiones técnicas con criterio. Dominio práctico consolidado.',
                'points' => 50,
            ],
            [
                'level' => 4,
                'name' => 'Experto',
                'description' => 'Referente interno en la materia. Mentorea a otros. Diseña soluciones complejas. Lidera iniciativas técnicas. Alta responsabilidad en decisiones críticas.',
                'points' => 100,
            ],
            [
                'level' => 5,
                'name' => 'Maestro',
                'description' => 'Autoridad reconocida. Innova y define estándares organizacionales. Influencia estratégica. Máximo nivel de autonomía, complejidad y responsabilidad.',
                'points' => 200,
            ],
        ];

        foreach ($levels as $levelData) {
            SkillLevelDefinition::create($levelData);
        }

        $this->command->info('✅ 5 skill level definitions creados');
    }
}
