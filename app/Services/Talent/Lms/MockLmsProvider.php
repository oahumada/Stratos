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
        // Mock a set of courses based on the query or random if empty
        $count = empty($query) ? 5 : rand(2, 8);
        $courses = [];

        for ($i = 1; $i <= $count; $i++) {
            $courses[] = [
                'id' => "CRS-{$i}-".strtoupper(substr(md5($query.$i), 0, 8)),
                'title' => (empty($query) ? 'General Training ' : 'Especialización en '.ucfirst($query))." {$i}",
                'provider' => 'mock_lms',
                'duration_hours' => rand(5, 40),
                'level' => ['beginner', 'intermediate', 'advanced'][rand(0, 2)],
                'rating' => 4.0 + (rand(0, 10) / 10),
            ];
        }

        return $courses;
    }
}
