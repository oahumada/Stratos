<?php

namespace App\Services\Talent\Lms;

class MockLmsProvider implements LmsProviderInterface
{
    public function getLaunchUrl(string $courseId, ?string $userId = null): string
    {
        return "https://lms-mock.example.com/learn/course/{$courseId}?user={$userId}";
    }

    public function enrollUser(string $courseId, string $userId): string
    {
        return 'enroll_'.uniqid()."_{$courseId}";
    }

    public function getProgress(string $enrollmentId): float
    {
        // Mock progress based on randomness or time
        return (float) rand(0, 100);
    }

    public function isCompleted(string $enrollmentId): bool
    {
        return rand(0, 10) > 8; // 20% chance of being completed in mock
    }

    public function searchCourses(string $query): array
    {
        $mockCatalog = [
            ['id' => 'MOCK-001', 'title' => 'Fundamentos de Lean Manufacturing', 'level' => 'beginner', 'provider' => 'mock'],
            ['id' => 'MOCK-002', 'title' => 'Gestión de Calidad Automotriz (IATF 16949)', 'level' => 'advanced', 'provider' => 'mock'],
            ['id' => 'MOCK-003', 'title' => 'Liderazgo en Procesos de Cambio', 'level' => 'intermediate', 'provider' => 'mock'],
            ['id' => 'MOCK-004', 'title' => 'Análisis de Datos para Producto', 'level' => 'intermediate', 'provider' => 'mock'],
            ['id' => 'MOCK-005', 'title' => 'Introducción a la Inteligencia de Talento', 'level' => 'beginner', 'provider' => 'mock'],
        ];

        if (empty($query)) {
            return $mockCatalog;
        }

        return array_values(array_filter($mockCatalog, function ($c) use ($query) {
            return stripos($c['title'], $query) !== false;
        }));
    }
}
