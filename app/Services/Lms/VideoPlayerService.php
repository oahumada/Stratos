<?php

namespace App\Services\Lms;

use App\Models\LmsVideoTracking;
use Illuminate\Support\Facades\DB;

class VideoPlayerService
{
    public function getOrCreateTracking(
        int $enrollmentId,
        int $lessonId,
        int $userId,
        int $orgId,
        string $provider,
        string $videoId,
        int $duration,
    ): LmsVideoTracking {
        return LmsVideoTracking::firstOrCreate(
            [
                'enrollment_id' => $enrollmentId,
                'lesson_id' => $lessonId,
            ],
            [
                'organization_id' => $orgId,
                'user_id' => $userId,
                'provider' => $provider,
                'video_id' => $videoId,
                'duration_seconds' => $duration,
                'watched_seconds' => 0,
                'last_position' => 0,
                'completed' => false,
            ]
        );
    }

    public function updateProgress(int $trackingId, int $watchedSeconds, int $lastPosition): LmsVideoTracking
    {
        $tracking = LmsVideoTracking::findOrFail($trackingId);

        $tracking->update([
            'watched_seconds' => max($tracking->watched_seconds, $watchedSeconds),
            'last_position' => $lastPosition,
        ]);

        $tracking->refresh();
        $tracking->markComplete();
        $tracking->refresh();

        return $tracking;
    }

    public function getVideoEmbed(string $provider, string $videoId): array
    {
        return match ($provider) {
            'youtube' => [
                'embed_url' => "https://www.youtube.com/embed/{$videoId}",
                'provider' => 'youtube',
                'video_id' => $videoId,
            ],
            'vimeo' => [
                'embed_url' => "https://player.vimeo.com/video/{$videoId}",
                'provider' => 'vimeo',
                'video_id' => $videoId,
            ],
            'loom' => [
                'embed_url' => "https://www.loom.com/embed/{$videoId}",
                'provider' => 'loom',
                'video_id' => $videoId,
            ],
            default => [
                'embed_url' => null,
                'provider' => $provider,
                'video_id' => $videoId,
            ],
        };
    }

    public function getLessonVideoStats(int $lessonId, int $orgId): array
    {
        $trackings = LmsVideoTracking::where('lesson_id', $lessonId)
            ->where('organization_id', $orgId)
            ->get();

        $total = $trackings->count();

        return [
            'total_viewers' => $total,
            'completed_count' => $trackings->where('completed', true)->count(),
            'avg_watched_seconds' => $total > 0 ? round($trackings->avg('watched_seconds')) : 0,
            'avg_progress' => $total > 0 ? round($trackings->avg(fn ($t) => $t->progress_percentage), 2) : 0,
            'completion_rate' => $total > 0 ? round(($trackings->where('completed', true)->count() / $total) * 100, 2) : 0,
        ];
    }
}
