<?php

namespace App\Services\Talent\Lms;

interface LmsProviderInterface
{
    /**
     * Obtiene la URL de lanzamiento para un curso.
     */
    public function getLaunchUrl(string $courseId, ?string $userId = null): string;

    /**
     * Inscribe a un usuario en un curso.
     */
    public function enrollUser(string $courseId, string $userId): string;

    /**
     * Obtiene el progreso de un usuario en un curso.
     */
    public function getProgress(string $enrollmentId): float;

    /**
     * Verifica si el curso se ha completado.
     */
    public function isCompleted(string $enrollmentId): bool;
}
