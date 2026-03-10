<?php

namespace App\Services\Talent\Lms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoodleProvider implements LmsProviderInterface
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.moodle.url', '');
        $this->token = config('services.moodle.token', '');
    }

    public function getLaunchUrl(string $courseId, ?string $userId = null): string
    {
        // Moodle suele usar una URL base + el ID del curso
        return $this->baseUrl . "/course/view.php?id=" . $courseId;
    }

    public function enrollUser(string $courseId, string $userId): string
    {
        if (empty($this->token)) {
            Log::warning("Moodle Token no configurado. Simulando inscripción.");
            return "moodle_mock_enroll_" . uniqid();
        }

        // Llamada a enrol_manual_enrol_users de Moodle
        try {
            Http::get($this->baseUrl . "/webservice/rest/server.php", [
                'wstoken' => $this->token,
                'wsfunction' => 'enrol_manual_enrol_users',
                'moodlewsrestformat' => 'json',
                'enrolments[0][roleid]' => 5, // Student
                'enrolments[0][userid]' => $userId,
                'enrolments[0][courseid]' => $courseId,
            ]);

            return "moodle_" . $courseId . "_" . $userId;
        } catch (\Exception $e) {
            Log::error("Error enrolling in Moodle: " . $e->getMessage());
            return "error_moodle";
        }
    }

    public function getProgress(string $enrollmentId): float
    {
        // En Moodle esto suele requerir core_completion_get_course_completion_status
        return 0.0;
    }

    public function isCompleted(string $enrollmentId): bool
    {
        return false;
    }

    public function searchCourses(string $query): array
    {
        if (empty($this->token)) {
            return [];
        }

        try {
            $response = Http::get($this->baseUrl . "/webservice/rest/server.php", [
                'wstoken' => $this->token,
                'wsfunction' => 'core_course_search_courses',
                'moodlewsrestformat' => 'json',
                'criterianame' => 'search',
                'criteriavalue' => $query,
            ]);

            $data = $response->json();
            $courses = $data['courses'] ?? [];

            return array_map(fn($c) => [
                'id' => $c['id'],
                'title' => $c['fullname'],
                'description' => $c['summary'],
                'provider' => 'moodle'
            ], $courses);
            
        } catch (\Exception $e) {
            Log::error("Error searching Moodle courses: " . $e->getMessage());
            return [];
        }
    }
}
