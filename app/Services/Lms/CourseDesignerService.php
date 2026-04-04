<?php

namespace App\Services\Lms;

use App\Models\LmsCourse;
use App\Models\LmsLesson;
use App\Models\LmsModule;
use App\Services\AiOrchestratorService;
use App\Services\Content\ContentAgentService;
use Illuminate\Support\Facades\DB;

class CourseDesignerService
{
    public function __construct(
        protected AiOrchestratorService $orchestrator,
        protected ContentAgentService $contentAgent,
    ) {}

    /**
     * Step 1: Ask AI to design a course outline from a topic + target audience + skill gaps.
     */
    public function generateOutline(int $organizationId, array $params): array
    {
        $topic = $params['topic'];
        $audience = $params['target_audience'];
        $skillGaps = $params['skill_gaps'] ?? [];
        $duration = $params['duration_target'] ?? 8;
        $level = $params['level'] ?? 'intermediate';

        $skillGapsText = ! empty($skillGaps)
            ? 'Brechas de habilidades a cubrir: ' . implode(', ', $skillGaps) . '.'
            : '';

        $prompt = <<<PROMPT
        Diseña un curso completo sobre: "{$topic}".
        Audiencia objetivo: {$audience}.
        Nivel: {$level}.
        Duración objetivo: {$duration} horas.
        {$skillGapsText}

        Responde en formato JSON con esta estructura exacta:
        {
            "course_outline": "Descripción general del curso",
            "learning_objectives": ["objetivo1", "objetivo2"],
            "modules": [
                {
                    "title": "Título del módulo",
                    "description": "Descripción del módulo",
                    "lessons": [
                        {
                            "title": "Título de la lección",
                            "duration_minutes": 30,
                            "content_type": "article"
                        }
                    ]
                }
            ],
            "assessment_plan": "Plan de evaluación del curso"
        }

        Máximo 20 módulos. Los content_type válidos son: article, video, exercise, quiz.
        PROMPT;

        return $this->orchestrator->agentThink('Arquitecto de Aprendizaje', $prompt);
    }

    /**
     * Step 2: Generate content for a specific lesson using ContentAgentService.
     */
    public function generateLessonContent(int $organizationId, array $params): array
    {
        $lessonTitle = $params['lesson_title'];
        $moduleContext = $params['module_context'] ?? '';
        $courseTopic = $params['course_topic'] ?? '';
        $contentType = $params['content_type'] ?? 'article';

        $topic = "{$lessonTitle} — Módulo: {$moduleContext}, Curso: {$courseTopic}";

        $options = [
            'tone' => 'educational and engaging',
            'length' => 'about 600-1000 words',
            'content_type' => $contentType,
        ];

        $draft = $this->contentAgent->generateDraft($topic, $options);

        return [
            'title' => $lessonTitle,
            'body' => $draft['body'] ?? '',
            'content_type' => $contentType,
            'estimated_duration' => $this->estimateDuration($draft['body'] ?? '', $contentType),
        ];
    }

    /**
     * Step 3: Persist the designed course (outline → LmsCourse + LmsModules + LmsLessons).
     */
    public function persistCourse(int $organizationId, array $courseData): LmsCourse
    {
        return DB::transaction(function () use ($organizationId, $courseData) {
            $course = LmsCourse::create([
                'title' => $courseData['title'],
                'description' => $courseData['description'] ?? null,
                'category' => $courseData['category'] ?? null,
                'level' => $courseData['level'] ?? 'intermediate',
                'estimated_duration_minutes' => $courseData['estimated_duration_minutes'] ?? 0,
                'xp_points' => $courseData['xp_points'] ?? 0,
                'cert_min_resource_completion_ratio' => $courseData['cert_min_resource_completion_ratio'] ?? null,
                'cert_require_assessment_score' => $courseData['cert_require_assessment_score'] ?? false,
                'cert_min_assessment_score' => $courseData['cert_min_assessment_score'] ?? null,
                'cert_template_id' => $courseData['cert_template_id'] ?? null,
                'is_active' => $courseData['is_active'] ?? false,
                'organization_id' => $organizationId,
            ]);

            foreach ($courseData['modules'] ?? [] as $moduleData) {
                $module = LmsModule::create([
                    'lms_course_id' => $course->id,
                    'title' => $moduleData['title'],
                    'order' => $moduleData['order'] ?? 0,
                ]);

                foreach ($moduleData['lessons'] ?? [] as $lessonData) {
                    LmsLesson::create([
                        'lms_module_id' => $module->id,
                        'title' => $lessonData['title'],
                        'description' => $lessonData['description'] ?? null,
                        'content_type' => $lessonData['content_type'] ?? 'article',
                        'content_body' => $lessonData['content_body'] ?? null,
                        'content_url' => $lessonData['content_url'] ?? null,
                        'order' => $lessonData['order'] ?? 0,
                        'duration_minutes' => $lessonData['duration_minutes'] ?? 0,
                    ]);
                }
            }

            return $course->load('modules.lessons');
        });
    }

    /**
     * Step 4: Review — AI feedback on the designed course.
     */
    public function reviewCourse(int $courseId, int $organizationId): array
    {
        $course = LmsCourse::where('organization_id', $organizationId)
            ->with('modules.lessons')
            ->findOrFail($courseId);

        $courseJson = json_encode([
            'title' => $course->title,
            'description' => $course->description,
            'level' => $course->level,
            'estimated_duration_minutes' => $course->estimated_duration_minutes,
            'modules' => $course->modules->map(fn ($m) => [
                'title' => $m->title,
                'lessons' => $m->lessons->map(fn ($l) => [
                    'title' => $l->title,
                    'content_type' => $l->content_type,
                    'duration_minutes' => $l->duration_minutes,
                ])->toArray(),
            ])->toArray(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $prompt = <<<PROMPT
        Revisa el siguiente curso y proporciona feedback pedagógico:

        {$courseJson}

        Responde en formato JSON con esta estructura:
        {
            "score": 85,
            "strengths": ["fortaleza1", "fortaleza2"],
            "improvements": ["mejora1", "mejora2"],
            "suggestions": ["sugerencia1", "sugerencia2"]
        }

        El score debe ser de 0 a 100. Evalúa: estructura, progresión pedagógica, cobertura de contenido, y balance de actividades.
        PROMPT;

        return $this->orchestrator->agentThink('Arquitecto de Aprendizaje', $prompt);
    }

    /**
     * Preview a course with all relations loaded.
     */
    public function previewCourse(int $courseId, int $organizationId): LmsCourse
    {
        return LmsCourse::where('organization_id', $organizationId)
            ->with('modules.lessons')
            ->findOrFail($courseId);
    }

    private function estimateDuration(string $body, string $contentType): int
    {
        $wordCount = str_word_count(strip_tags($body));

        return match ($contentType) {
            'video', 'video_script' => max(5, intval($wordCount / 150)),
            'exercise' => max(10, intval($wordCount / 100)),
            default => max(5, intval($wordCount / 200)),
        };
    }
}
