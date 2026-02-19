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
        return "enroll_" . uniqid() . "_{$courseId}";
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
}
