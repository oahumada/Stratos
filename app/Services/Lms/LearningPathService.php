<?php

namespace App\Services\Lms;

use App\Models\LmsEnrollment;
use App\Models\LmsLearningPath;
use App\Models\LmsLearningPathEnrollment;
use App\Models\LmsLearningPathItem;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Facades\DB;

class LearningPathService
{
    public function __construct(
        protected AiOrchestratorService $orchestrator,
    ) {}

    /**
     * Create a learning path with items (courses).
     */
    public function createPath(array $data, int $organizationId, int $userId): LmsLearningPath
    {
        return DB::transaction(function () use ($data, $organizationId, $userId) {
            $path = LmsLearningPath::create([
                'organization_id' => $organizationId,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'level' => $data['level'] ?? null,
                'estimated_duration_minutes' => $data['estimated_duration_minutes'] ?? 0,
                'is_mandatory' => $data['is_mandatory'] ?? false,
                'is_active' => $data['is_active'] ?? true,
                'created_by' => $userId,
            ]);

            foreach ($data['items'] ?? [] as $index => $itemData) {
                $path->items()->create([
                    'lms_course_id' => $itemData['lms_course_id'],
                    'order' => $itemData['order'] ?? $index,
                    'is_required' => $itemData['is_required'] ?? true,
                    'unlock_after_item_id' => $itemData['unlock_after_item_id'] ?? null,
                ]);
            }

            return $path->load('items.course');
        });
    }

    /**
     * Enroll a user in a learning path.
     * Creates LmsLearningPathEnrollment + LmsEnrollment for each course.
     */
    public function enrollUser(int $pathId, int $userId, int $organizationId): LmsLearningPathEnrollment
    {
        $existing = LmsLearningPathEnrollment::where('lms_learning_path_id', $pathId)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($pathId, $userId, $organizationId) {
            $enrollment = LmsLearningPathEnrollment::create([
                'lms_learning_path_id' => $pathId,
                'user_id' => $userId,
                'organization_id' => $organizationId,
                'status' => 'active',
                'progress_percentage' => 0,
                'started_at' => now(),
            ]);

            $items = LmsLearningPathItem::where('lms_learning_path_id', $pathId)->get();

            foreach ($items as $item) {
                LmsEnrollment::firstOrCreate(
                    [
                        'lms_course_id' => $item->lms_course_id,
                        'user_id' => $userId,
                    ],
                    [
                        'status' => 'active',
                        'progress_percentage' => 0,
                        'resources_completed' => 0,
                        'resources_total' => 0,
                        'started_at' => now(),
                    ]
                );
            }

            return $enrollment;
        });
    }

    /**
     * Check if a specific item is unlocked for a user.
     */
    public function isItemUnlocked(int $itemId, int $userId): bool
    {
        $item = LmsLearningPathItem::findOrFail($itemId);

        return $item->isUnlocked($userId);
    }

    /**
     * Recalculate progress for a path enrollment based on course completions.
     */
    public function recalculateProgress(int $pathId, int $userId, int $organizationId): LmsLearningPathEnrollment
    {
        $enrollment = LmsLearningPathEnrollment::where('lms_learning_path_id', $pathId)
            ->where('user_id', $userId)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $items = LmsLearningPathItem::where('lms_learning_path_id', $pathId)->get();
        $requiredItems = $items->where('is_required', true);
        $totalRequired = $requiredItems->count();

        if ($totalRequired === 0) {
            $enrollment->update([
                'progress_percentage' => 100,
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return $enrollment->fresh();
        }

        $completedRequired = 0;
        foreach ($requiredItems as $item) {
            $courseCompleted = LmsEnrollment::where('lms_course_id', $item->lms_course_id)
                ->where('user_id', $userId)
                ->where('status', 'completed')
                ->exists();

            if ($courseCompleted) {
                $completedRequired++;
            }
        }

        $progress = ($completedRequired / $totalRequired) * 100;
        $updateData = ['progress_percentage' => $progress];

        if ($completedRequired === $totalRequired) {
            $updateData['status'] = 'completed';
            $updateData['completed_at'] = now();
        }

        $enrollment->update($updateData);

        return $enrollment->fresh();
    }

    /**
     * Get available (unlocked) courses for user in a path.
     */
    public function getAvailableCourses(int $pathId, int $userId, int $organizationId): array
    {
        $items = LmsLearningPathItem::where('lms_learning_path_id', $pathId)
            ->with('course')
            ->orderBy('order')
            ->get();

        $available = [];
        foreach ($items as $item) {
            $available[] = [
                'item' => $item,
                'course' => $item->course,
                'is_unlocked' => $item->isUnlocked($userId),
                'is_completed' => LmsEnrollment::where('lms_course_id', $item->lms_course_id)
                    ->where('user_id', $userId)
                    ->where('status', 'completed')
                    ->exists(),
            ];
        }

        return $available;
    }

    /**
     * Generate a learning path from AI using AiOrchestratorService.
     */
    public function generatePathWithAi(string $topic, string $targetAudience, int $organizationId, int $userId): LmsLearningPath
    {
        $prompt = <<<PROMPT
        Diseña una ruta de aprendizaje (learning path) sobre: "{$topic}".
        Audiencia objetivo: {$targetAudience}.

        Responde en formato JSON con esta estructura exacta:
        {
            "title": "Título de la ruta de aprendizaje",
            "description": "Descripción general",
            "level": "beginner|intermediate|advanced",
            "estimated_duration_minutes": 480,
            "courses": [
                {
                    "title": "Título del curso",
                    "description": "Descripción del curso",
                    "level": "beginner",
                    "estimated_duration_minutes": 120,
                    "order": 1,
                    "is_required": true
                }
            ]
        }

        Máximo 10 cursos. Ordénalos de menor a mayor dificultad.
        PROMPT;

        $aiResponse = $this->orchestrator->agentThink('Arquitecto de Aprendizaje', $prompt);

        $responseData = json_decode($aiResponse['response'] ?? '{}', true) ?: [];

        return DB::transaction(function () use ($responseData, $organizationId, $userId) {
            $path = LmsLearningPath::create([
                'organization_id' => $organizationId,
                'title' => $responseData['title'] ?? 'Ruta de Aprendizaje',
                'description' => $responseData['description'] ?? null,
                'level' => $responseData['level'] ?? null,
                'estimated_duration_minutes' => $responseData['estimated_duration_minutes'] ?? 0,
                'is_mandatory' => false,
                'is_active' => true,
                'created_by' => $userId,
            ]);

            foreach ($responseData['courses'] ?? [] as $index => $courseData) {
                $course = \App\Models\LmsCourse::create([
                    'title' => $courseData['title'],
                    'description' => $courseData['description'] ?? null,
                    'level' => $courseData['level'] ?? null,
                    'estimated_duration_minutes' => $courseData['estimated_duration_minutes'] ?? 0,
                    'is_active' => true,
                    'organization_id' => $organizationId,
                ]);

                $path->items()->create([
                    'lms_course_id' => $course->id,
                    'order' => $courseData['order'] ?? $index,
                    'is_required' => $courseData['is_required'] ?? true,
                ]);
            }

            return $path->load('items.course');
        });
    }
}
