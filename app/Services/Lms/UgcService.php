<?php

namespace App\Services\Lms;

use App\Models\LmsUserContent;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UgcService
{
    public function create(int $orgId, int $authorId, array $data): LmsUserContent
    {
        return LmsUserContent::create([
            'organization_id' => $orgId,
            'author_id' => $authorId,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'content_type' => $data['content_type'] ?? 'article',
            'content_body' => $data['content_body'] ?? null,
            'content_url' => $data['content_url'] ?? null,
            'course_id' => $data['course_id'] ?? null,
            'tags' => $data['tags'] ?? null,
            'status' => 'draft',
        ]);
    }

    public function submitForReview(int $contentId): LmsUserContent
    {
        $content = LmsUserContent::findOrFail($contentId);
        $content->update(['status' => 'pending_review']);

        return $content->fresh();
    }

    public function approve(int $contentId, int $approverId): LmsUserContent
    {
        $content = LmsUserContent::findOrFail($contentId);
        $content->update([
            'status' => 'published',
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);

        return $content->fresh();
    }

    public function reject(int $contentId, int $approverId, ?string $reason = null): LmsUserContent
    {
        $content = LmsUserContent::findOrFail($contentId);
        $content->update([
            'status' => 'rejected',
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);

        return $content->fresh();
    }

    public function listPublished(int $orgId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = LmsUserContent::where('organization_id', $orgId)
            ->published()
            ->with(['author', 'course']);

        if (! empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (! empty($filters['content_type'])) {
            $query->where('content_type', $filters['content_type']);
        }

        if (! empty($filters['course_id'])) {
            $query->where('course_id', (int) $filters['course_id']);
        }

        if (! empty($filters['tags'])) {
            $tags = is_array($filters['tags']) ? $filters['tags'] : [$filters['tags']];
            foreach ($tags as $tag) {
                $query->whereJsonContains('tags', $tag);
            }
        }

        return $query->latest()->paginate($perPage);
    }

    public function listPendingReview(int $orgId): Collection
    {
        return LmsUserContent::where('organization_id', $orgId)
            ->pendingReview()
            ->with(['author'])
            ->latest()
            ->get();
    }

    public function incrementViews(int $contentId): void
    {
        LmsUserContent::where('id', $contentId)->increment('views_count');
    }

    public function toggleLike(int $contentId, int $userId): array
    {
        $content = LmsUserContent::findOrFail($contentId);

        // Simple toggle: use a cache-based or session-based approach
        // For simplicity, just increment/decrement
        $cacheKey = "ugc_like:{$contentId}:{$userId}";
        $liked = cache()->get($cacheKey, false);

        if ($liked) {
            $content->decrement('likes_count');
            cache()->forget($cacheKey);
            $liked = false;
        } else {
            $content->increment('likes_count');
            cache()->put($cacheKey, true, now()->addYear());
            $liked = true;
        }

        return [
            'liked' => $liked,
            'likes_count' => $content->fresh()->likes_count,
        ];
    }
}
