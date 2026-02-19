<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PulseSurvey;

class PulseSurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PulseSurvey::create([
            'title' => 'Clima Organizacional - Q1 2026',
            'type' => 'climate',
            'questions' => [
                ['id' => 1, 'text' => '¿Qué tan satisfecho estás con tu equilibrio vida-trabajo?', 'type' => 'rating', 'options' => [1,2,3,4,5]],
                ['id' => 2, 'text' => '¿Sientes que tienes las herramientas necesarias para tu rol?', 'type' => 'rating', 'options' => [1,2,3,4,5]],
                ['id' => 3, 'text' => '¿Qué podríamos mejorar en tu día a día?', 'type' => 'text']
            ],
            'is_active' => true
        ]);

        PulseSurvey::create([
            'title' => 'Nivel de Engagement Semanal',
            'type' => 'engagement',
            'questions' => [
                ['id' => 1, 'text' => '¿Qué tan motivado te sentiste esta semana?', 'type' => 'rating', 'options' => [1,2,3,4,5]],
                ['id' => 2, 'text' => '¿Recibiste feedback útil de tu líder?', 'type' => 'rating', 'options' => [1,2,3,4,5]]
            ],
            'is_active' => true
        ]);
    }
}
