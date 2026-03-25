<?php

namespace Database\Seeders;

use App\Models\CulturalBlueprint;
use App\Models\People;
use App\Models\PsychometricProfile;
use App\Models\PulseResponse;
use App\Models\PulseSurvey;
use Illuminate\Database\Seeder;

class CultureDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Asegurar que tenemos una organización y un Cultural Blueprint simulado
        $orgId = 1; // Ajustar según el entorno, usualmente 1 es la default

        CulturalBlueprint::updateOrCreate(
            ['organization_id' => $orgId],
            [
                'mission' => 'Liderar la transición hacia una IA ética y descentralizada.',
                'vision' => 'Ser el estándar de gobernanza algorítmica y talento humano para 2030.',
                'values' => [
                    'Innovación Radical (Adhocracia)',
                    'Meritocracia Basada en Datos (Mercado)',
                    'Bienestar del Colaborador (Clan)',
                    'Integridad Criptográfica (Jerarquía)',
                ],
                'principles' => [
                    'Transparencia Algorítmica',
                    'Human-in-the-loop',
                    'Privacidad por Diseño',
                ],
            ]
        );

        // 2. Obtener personas para asignarles datos
        $people = People::where('organization_id', $orgId)->limit(10)->get();
        if ($people->isEmpty()) {
            return;
        }

        // 3. Simular Encuestas Pulse (Datos de Sentimiento)
        $survey = PulseSurvey::first() ?? PulseSurvey::create([
            'title' => 'Cultura y Sentimiento Diario',
            'type' => 'climate',
            'questions' => [['id' => 1, 'text' => '¿Cómo te sientes hoy?', 'type' => 'rating']],
            'is_active' => true,
        ]);

        foreach ($people as $person) {
            // Creamos 5 respuestas por persona con una tendencia mixta
            for ($i = 0; $i < 5; $i++) {
                PulseResponse::create([
                    'pulse_survey_id' => $survey->id,
                    'people_id' => $person->id,
                    'answers' => [1 => rand(3, 5)], // Escala 1-5
                    'sentiment_score' => (float) rand(40, 95),
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }

        // 4. Simular Perfiles Psicométricos (DNA para el Radar CVF)
        $traits = [
            'Empatía', 'Cooperación', // Clan
            'Creatividad', 'Apertura', // Adhocracia
            'Ambición', 'Persistencia', // Mercado
            'Orden', 'Responsabilidad', // Jerarquía
        ];

        foreach ($people as $person) {
            foreach ($traits as $trait) {
                PsychometricProfile::updateOrCreate(
                    [
                        'people_id' => $person->id,
                        'trait_name' => $trait,
                    ],
                    [
                        'score' => rand(600, 950) / 1000,
                        'rationale' => 'Basado en simulaciones de comportamiento.',
                        'created_at' => now()->subDays(rand(10, 60)),
                    ]
                );
            }
        }
    }
}
