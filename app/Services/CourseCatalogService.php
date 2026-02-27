<?php

namespace App\Services;

class CourseCatalogService
{
    /**
     * Simula un catálogo de cursos reales basado en la skill.
     * En una fase futura, esto se conectará con APIs de Udemy, Coursera o LMS interno.
     */
    public function findCoursesBySkill(string $skillName): array
    {
        $catalog = [
            'Liderazgo' => [
                ['title' => 'Comunicación Asertiva para Líderes', 'provider' => 'InternalAcademy', 'duration' => '10h'],
                ['title' => 'Gestión de Equipos de Alto Rendimiento', 'provider' => 'Coursera', 'duration' => '40h'],
            ],
            'Python' => [
                ['title' => 'Python for Data Science', 'provider' => 'EdX', 'duration' => '60h'],
                ['title' => 'Arquitecturas Limpias en Python', 'provider' => 'Udemy', 'duration' => '20h'],
            ],
            'Adaptabilidad' => [
                ['title' => 'Mindset de Crecimiento en Entornos Ágiles', 'provider' => 'LinkedIn Learning', 'duration' => '5h'],
            ]
        ];

        return $catalog[$skillName] ?? [
            ['title' => "Especialización en {$skillName}", 'provider' => 'Google Learning', 'duration' => '15h']
        ];
    }
}
