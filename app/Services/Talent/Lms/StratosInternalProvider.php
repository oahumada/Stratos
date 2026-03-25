<?php

namespace App\Services\Talent\Lms;

use App\Models\LmsCourse;
use App\Models\LmsEnrollment;

class StratosInternalProvider implements LmsProviderInterface
{
    public function getLaunchUrl(string $courseId, ?string $userId = null): string
    {
        // En el LMS interno, el lanzamiento es simplemente una ruta SPA de Stratos
        return '/talento-360/learning/course/'.$courseId;
    }

    public function enrollUser(string $courseId, string $userId): string
    {
        $enrollment = LmsEnrollment::firstOrCreate([
            'lms_course_id' => $courseId,
            'user_id' => $userId,
        ], [
            'status' => 'enrolled',
            'progress_percentage' => 0,
            'started_at' => now(),
        ]);

        return (string) $enrollment->id;
    }

    public function getProgress(string $enrollmentId): float
    {
        $enrollment = LmsEnrollment::find($enrollmentId);

        return $enrollment ? (float) $enrollment->progress_percentage : 0.0;
    }

    public function isCompleted(string $enrollmentId): bool
    {
        $enrollment = LmsEnrollment::find($enrollmentId);

        return $enrollment && $enrollment->status === 'completed';
    }

    public function searchCourses(string $query): array
    {
        return LmsCourse::where('title', 'like', "%{$query}%")
            ->where('is_active', true)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'description' => $c->description,
                'provider' => 'internal',
            ])
            ->toArray();
    }
}
