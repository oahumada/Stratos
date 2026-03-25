<?php

namespace Database\Seeders;

use App\Models\LmsCourse;
use Illuminate\Database\Seeder;

class LmsInternalCoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'title' => 'Protocolos de Seguridad en Planta Automotriz (SOP-01)',
                'description' => 'Inducción obligatoria sobre normas de seguridad, uso de EPP y protocolos de emergencia en líneas de producción.',
                'category' => 'Seguridad',
                'level' => 'beginner',
                'estimated_duration_minutes' => 120,
            ],
            [
                'title' => 'Mantenimiento Preventivo de Celdas Robóticas Kuka',
                'description' => 'Guía técnica avanzada para el diagnóstico y mantenimiento de brazos robóticos en la sección de soldadura.',
                'category' => 'Técnico',
                'level' => 'advanced',
                'estimated_duration_minutes' => 480,
            ],
            [
                'title' => 'Cultura de Calidad Stratos: El Estándar Six Sigma',
                'description' => 'Módulo interno sobre la aplicación de Seis Sigma en nuestros procesos de ensamblaje.',
                'category' => 'Calidad',
                'level' => 'intermediate',
                'estimated_duration_minutes' => 240,
            ],
        ];

        foreach ($courses as $course) {
            LmsCourse::updateOrCreate(['title' => $course['title']], $course);
        }
    }
}
