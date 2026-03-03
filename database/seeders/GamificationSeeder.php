<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GamificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'name' => 'Culture Champion',
                'slug' => 'culture-champion',
                'description' => 'Alineamiento excepcional con los valores de la organización.',
                'icon' => 'mdi-heart-plus',
                'color' => 'red-accent-3'
            ],
            [
                'name' => 'AI Visionary',
                'slug' => 'ai-visionary',
                'description' => 'Dominio avanzado en el uso de Copilotos y Agentes de IA.',
                'icon' => 'mdi-brain',
                'color' => 'deep-purple'
            ],
            [
                'name' => 'Quick Learner',
                'slug' => 'quick-learner',
                'description' => 'Completó un Learning Plan en tiempo record.',
                'icon' => 'mdi-lightning-bolt',
                'color' => 'amber'
            ],
            [
                'name' => 'Strategic Architect',
                'slug' => 'strategic-architect',
                'description' => 'Diseñó exitosamente un escenario de crisis de alta fidelidad.',
                'icon' => 'mdi-castle',
                'color' => 'indigo'
            ]
        ];

        foreach ($badges as $badge) {
            \Illuminate\Support\Facades\DB::table('badges')->updateOrInsert(
                ['slug' => $badge['slug']],
                $badge
            );
        }
    }
}
