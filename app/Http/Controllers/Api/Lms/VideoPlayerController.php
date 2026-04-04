<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsEnrollment;
use App\Models\LmsLesson;
use App\Services\Lms\VideoPlayerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoPlayerController extends Controller
{
    public function __construct(
        protected VideoPlayerService $service,
    ) {}

    public function getTracking(Request $request, int $lesson): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $userId = $request->user()->id;
        $lessonModel = LmsLesson::findOrFail($lesson);

        $enrollment = LmsEnrollment::where('user_id', $userId)
            ->whereHas('course.modules.lessons', fn ($q) => $q->where('lms_lessons.id', $lessonModel->id))
            ->first();

        if (! $enrollment) {
            return response()->json(['error' => 'Not enrolled'], 403);
        }

        $request->validate([
            'provider' => 'sometimes|string|in:youtube,vimeo,loom',
            'video_id' => 'sometimes|string',
            'duration' => 'sometimes|integer|min:0',
        ]);

        $tracking = $this->service->getOrCreateTracking(
            $enrollment->id,
            $lessonModel->id,
            $userId,
            $orgId,
            $request->input('provider', 'youtube'),
            $request->input('video_id', ''),
            $request->input('duration', 0),
        );

        $embed = $this->service->getVideoEmbed($tracking->provider, $tracking->video_id);

        return response()->json([
            'data' => [
                'tracking' => $tracking,
                'progress_percentage' => $tracking->progress_percentage,
                'embed' => $embed,
            ],
        ]);
    }

    public function updateProgress(Request $request, int $lesson): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $userId = $request->user()->id;

        $request->validate([
            'watched_seconds' => 'required|integer|min:0',
            'last_position' => 'required|integer|min:0',
        ]);

        $tracking = \App\Models\LmsVideoTracking::where('lesson_id', $lesson)
            ->where('user_id', $userId)
            ->where('organization_id', $orgId)
            ->firstOrFail();

        $tracking = $this->service->updateProgress(
            $tracking->id,
            $request->input('watched_seconds'),
            $request->input('last_position'),
        );

        return response()->json([
            'data' => [
                'tracking' => $tracking,
                'progress_percentage' => $tracking->progress_percentage,
            ],
        ]);
    }

    public function stats(Request $request, int $lesson): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $stats = $this->service->getLessonVideoStats($lesson, $orgId);

        return response()->json(['data' => $stats]);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
