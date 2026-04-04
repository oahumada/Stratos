<?php

namespace App\Services\Lms;

use App\Models\LmsCourse;
use App\Models\LmsCourseRating;
use App\Models\LmsEnrollment;
use App\Services\AiOrchestratorService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CourseCatalogService
{
    public function __construct(
        protected AiOrchestratorService $orchestrator,
    ) {}

    public function search(int $organizationId, array $filters = [], string $sort = 'newest', int $perPage = 12): LengthAwarePaginator
    {
        $query = LmsCourse::query()
            ->where('organization_id', $organizationId)
            ->where('is_active', true)
            ->withCount('enrollments')
            ->withAvg('ratings', 'rating')
            ->withCount('ratings as review_count');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (! empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (! empty($filters['tags'])) {
            $tags = is_array($filters['tags']) ? $filters['tags'] : [$filters['tags']];
            foreach ($tags as $tag) {
                $query->whereJsonContains('tags', $tag);
            }
        }

        if (! empty($filters['duration_min'])) {
            $query->where('estimated_duration_minutes', '>=', (int) $filters['duration_min']);
        }

        if (! empty($filters['duration_max'])) {
            $query->where('estimated_duration_minutes', '<=', (int) $filters['duration_max']);
        }

        if (isset($filters['featured'])) {
            $query->where('featured', (bool) $filters['featured']);
        }

        if (! empty($filters['enrollment_type'])) {
            $query->where('enrollment_type', $filters['enrollment_type']);
        }

        $query = match ($sort) {
            'popularity' => $query->orderByDesc('enrollments_count'),
            'rating' => $query->orderByDesc('ratings_avg_rating'),
            'title' => $query->orderBy('title'),
            default => $query->orderByDesc('created_at'),
        };

        return $query->paginate($perPage);
    }

    public function getCourseDetail(int $courseId, int $organizationId): array
    {
        $course = LmsCourse::query()
            ->where('organization_id', $organizationId)
            ->where('is_active', true)
            ->withCount('enrollments')
            ->withAvg('ratings', 'rating')
            ->withCount('ratings as review_count')
            ->with(['modules' => function ($q) {
                $q->withCount('lessons');
            }])
            ->findOrFail($courseId);

        $reviews = LmsCourseRating::where('lms_course_id', $courseId)
            ->where('organization_id', $organizationId)
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return [
            'course' => $course,
            'reviews' => $reviews,
            'modules_count' => $course->modules->count(),
            'lessons_count' => $course->modules->sum('lessons_count'),
        ];
    }

    public function rateCourse(int $courseId, int $userId, int $organizationId, int $rating, ?string $review = null): LmsCourseRating
    {
        return LmsCourseRating::updateOrCreate(
            [
                'lms_course_id' => $courseId,
                'user_id' => $userId,
            ],
            [
                'organization_id' => $organizationId,
                'rating' => $rating,
                'review' => $review,
            ]
        );
    }

    public function selfEnroll(int $courseId, int $userId, int $organizationId): LmsEnrollment
    {
        $course = LmsCourse::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->findOrFail($courseId);

        if ($course->enrollment_type !== 'open') {
            throw new \InvalidArgumentException('Este curso no permite inscripción abierta.');
        }

        $existing = LmsEnrollment::where('lms_course_id', $courseId)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            throw new \InvalidArgumentException('Ya estás inscrito en este curso.');
        }

        return LmsEnrollment::create([
            'lms_course_id' => $courseId,
            'user_id' => $userId,
            'status' => 'active',
            'progress_percentage' => 0,
            'started_at' => now(),
        ]);
    }

    public function getRecommendations(int $userId, int $organizationId, int $limit = 6): array
    {
        $enrolledCourseIds = LmsEnrollment::where('user_id', $userId)
            ->pluck('lms_course_id');

        $availableCourses = LmsCourse::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->whereNotIn('id', $enrolledCourseIds)
            ->select('id', 'title', 'category', 'level', 'description')
            ->limit(20)
            ->get();

        if ($availableCourses->isEmpty()) {
            return [];
        }

        $coursesJson = $availableCourses->toJson();

        $prompt = <<<PROMPT
        Dado el siguiente catálogo de cursos disponibles:
        {$coursesJson}

        Recomienda los {$limit} mejores cursos para el usuario, priorizando diversidad de categorías y nivel progresivo.
        Responde en JSON: [{"id": 1, "reason": "..."}]
        PROMPT;

        $aiResponse = $this->orchestrator->agentThink('Arquitecto de Aprendizaje', $prompt);

        return [
            'recommendations' => $aiResponse,
            'courses' => $availableCourses->take($limit)->values()->toArray(),
        ];
    }

    public function getCategories(int $organizationId): array
    {
        return LmsCourse::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->whereNotNull('category')
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('category')
            ->get()
            ->toArray();
    }

    public function getTags(int $organizationId): array
    {
        $courses = LmsCourse::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->whereNotNull('tags')
            ->pluck('tags');

        $tagCounts = [];
        foreach ($courses as $tags) {
            $tagsArray = is_string($tags) ? json_decode($tags, true) : $tags;
            if (is_array($tagsArray)) {
                foreach ($tagsArray as $tag) {
                    $tagCounts[$tag] = ($tagCounts[$tag] ?? 0) + 1;
                }
            }
        }

        arsort($tagCounts);

        $result = [];
        foreach ($tagCounts as $tag => $count) {
            $result[] = ['tag' => $tag, 'count' => $count];
        }

        return $result;
    }
}
