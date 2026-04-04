<?php

namespace App\Services\Lms;

use App\Models\LmsDiscussion;
use App\Models\LmsDiscussionLike;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DiscussionService
{
    public function getDiscussions(int $organizationId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = LmsDiscussion::query()
            ->forOrganization($organizationId)
            ->with(['user', 'replies.user'])
            ->withCount('likes');

        if (! empty($filters['course_id'])) {
            $query->forCourse((int) $filters['course_id']);
        }

        if (! empty($filters['lesson_id'])) {
            $query->forLesson((int) $filters['lesson_id']);
        }

        if (! empty($filters['root_only'])) {
            $query->rootPosts();
        }

        return $query->latest()->paginate($perPage);
    }

    public function createPost(array $data, int $userId, int $organizationId): LmsDiscussion
    {
        return LmsDiscussion::create([
            'lms_course_id' => $data['course_id'] ?? null,
            'lms_lesson_id' => $data['lesson_id'] ?? null,
            'user_id' => $userId,
            'organization_id' => $organizationId,
            'title' => $data['title'] ?? null,
            'body' => $data['body'],
        ]);
    }

    public function reply(int $parentId, string $body, int $userId, int $organizationId): LmsDiscussion
    {
        $parent = LmsDiscussion::where('id', $parentId)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        return LmsDiscussion::create([
            'lms_course_id' => $parent->lms_course_id,
            'lms_lesson_id' => $parent->lms_lesson_id,
            'user_id' => $userId,
            'organization_id' => $organizationId,
            'body' => $body,
            'parent_id' => $parentId,
        ]);
    }

    public function toggleLike(int $discussionId, int $userId): array
    {
        $discussion = LmsDiscussion::findOrFail($discussionId);

        $existing = LmsDiscussionLike::where('lms_discussion_id', $discussionId)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            $existing->delete();
            $discussion->decrement('likes_count');
            $liked = false;
        } else {
            LmsDiscussionLike::create([
                'lms_discussion_id' => $discussionId,
                'user_id' => $userId,
            ]);
            $discussion->increment('likes_count');
            $liked = true;
        }

        return [
            'liked' => $liked,
            'likes_count' => $discussion->fresh()->likes_count,
        ];
    }

    public function pinPost(int $discussionId, int $organizationId): LmsDiscussion
    {
        $discussion = LmsDiscussion::where('id', $discussionId)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $discussion->update(['is_pinned' => ! $discussion->is_pinned]);

        return $discussion->fresh();
    }

    public function deletePost(int $discussionId, int $userId, int $organizationId): void
    {
        $discussion = LmsDiscussion::where('id', $discussionId)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        if ($discussion->user_id !== $userId) {
            abort(403, 'You can only delete your own posts.');
        }

        $discussion->delete();
    }
}
