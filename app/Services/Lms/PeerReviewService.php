<?php

namespace App\Services\Lms;

use App\Models\LmsLesson;
use App\Models\LmsPeerReview;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PeerReviewService
{
    public function createAssignment(int $orgId, int $lessonId, array $reviewerIds, array $revieweeIds): Collection
    {
        $reviews = collect();

        DB::transaction(function () use ($orgId, $lessonId, $reviewerIds, $revieweeIds, &$reviews) {
            foreach ($revieweeIds as $index => $revieweeId) {
                $reviewerId = $reviewerIds[$index % count($reviewerIds)];
                $reviews->push(LmsPeerReview::create([
                    'organization_id' => $orgId,
                    'assignment_id' => $lessonId,
                    'reviewer_id' => $reviewerId,
                    'reviewee_id' => $revieweeId,
                    'status' => 'pending_submission',
                ]));
            }
        });

        return $reviews;
    }

    public function submitWork(int $peerReviewId, ?string $submissionUrl, ?string $submissionText): LmsPeerReview
    {
        $review = LmsPeerReview::findOrFail($peerReviewId);
        $review->update([
            'submission_url' => $submissionUrl,
            'submission_text' => $submissionText,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return $review->fresh();
    }

    public function submitReview(int $peerReviewId, float $score, string $feedback, ?array $rubricScores = null): LmsPeerReview
    {
        $review = LmsPeerReview::findOrFail($peerReviewId);
        $review->update([
            'review_score' => $score,
            'review_feedback' => $feedback,
            'rubric_scores' => $rubricScores,
            'status' => 'reviewed',
            'reviewed_at' => now(),
        ]);

        return $review->fresh();
    }

    public function getAssignmentsForUser(int $userId, int $orgId, string $role): Collection
    {
        $query = LmsPeerReview::where('organization_id', $orgId);

        if ($role === 'reviewer') {
            $query->where('reviewer_id', $userId);
        } else {
            $query->where('reviewee_id', $userId);
        }

        return $query->with(['assignment', 'reviewer', 'reviewee'])->get();
    }

    public function autoAssignReviewers(int $lessonId, int $orgId, int $reviewsPerSubmission = 2): Collection
    {
        $existingReviewees = LmsPeerReview::where('assignment_id', $lessonId)
            ->where('organization_id', $orgId)
            ->pluck('reviewee_id')
            ->unique()
            ->values();

        $existingReviewers = LmsPeerReview::where('assignment_id', $lessonId)
            ->where('organization_id', $orgId)
            ->pluck('reviewer_id')
            ->unique()
            ->values();

        $allUsers = $existingReviewees->merge($existingReviewers)->unique()->shuffle();

        $reviews = collect();

        if ($allUsers->count() < 2) {
            return $reviews;
        }

        DB::transaction(function () use ($allUsers, $lessonId, $orgId, $reviewsPerSubmission, &$reviews) {
            foreach ($allUsers as $revieweeId) {
                $potentialReviewers = $allUsers->filter(fn ($id) => $id !== $revieweeId)->shuffle();
                $assignedCount = 0;

                foreach ($potentialReviewers as $reviewerId) {
                    if ($assignedCount >= $reviewsPerSubmission) {
                        break;
                    }

                    $exists = LmsPeerReview::where('assignment_id', $lessonId)
                        ->where('organization_id', $orgId)
                        ->where('reviewer_id', $reviewerId)
                        ->where('reviewee_id', $revieweeId)
                        ->exists();

                    if (! $exists) {
                        $reviews->push(LmsPeerReview::create([
                            'organization_id' => $orgId,
                            'assignment_id' => $lessonId,
                            'reviewer_id' => $reviewerId,
                            'reviewee_id' => $revieweeId,
                            'status' => 'pending_submission',
                        ]));
                        $assignedCount++;
                    }
                }
            }
        });

        return $reviews;
    }

    public function getAggregatedScores(int $lessonId, int $orgId): Collection
    {
        return LmsPeerReview::where('assignment_id', $lessonId)
            ->where('organization_id', $orgId)
            ->where('status', 'reviewed')
            ->select('reviewee_id')
            ->selectRaw('AVG(review_score) as avg_score')
            ->selectRaw('COUNT(*) as review_count')
            ->groupBy('reviewee_id')
            ->with('reviewee')
            ->get();
    }
}
