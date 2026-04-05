<?php

namespace App\Jobs;

use App\Models\Embedding;
use App\Models\EmbeddingAuditLog;
use App\Models\EmbeddingVersion;
use App\Models\ImprovementFeedback;
use App\Services\EmbeddingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ReindexKnowledge implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected int $organizationId,
        protected ?string $versionTag = null,
    ) {}

    public function handle(EmbeddingService $embeddingService): void
    {
        $this->versionTag ??= now()->format('Y-m-d-His');

        // 1. Create version snapshot
        $version = EmbeddingVersion::create([
            'organization_id' => $this->organizationId,
            'version_tag' => $this->versionTag,
            'snapshot_count' => 0,
            'metadata' => ['trigger' => 'feedback_threshold', 'started_at' => now()->toISOString()],
            'created_by' => 'reindex_job',
        ]);

        $embeddingsCreated = 0;
        $embeddingsFlagged = 0;

        // 2. Process positive feedback (rating >= 4, not yet applied)
        $positiveFeedback = ImprovementFeedback::where('organization_id', $this->organizationId)
            ->positive()
            ->where('status', '!=', 'applied')
            ->get();

        foreach ($positiveFeedback as $feedback) {
            $context = $feedback->context ?? [];
            $text = implode(' ', array_filter([
                $context['query'] ?? '',
                $context['response_snippet'] ?? '',
            ]));

            if (empty(trim($text))) {
                continue;
            }

            $embeddingVector = $embeddingService->generate($text);
            if ($embeddingVector) {
                Embedding::create([
                    'organization_id' => $this->organizationId,
                    'resource_type' => 'feedback_positive',
                    'resource_id' => $feedback->id,
                    'metadata' => [
                        'source' => 'feedback_loop',
                        'rating' => $feedback->rating,
                        'version_tag' => $this->versionTag,
                    ],
                    'embedding' => $embeddingVector,
                ]);
                $embeddingsCreated++;

                EmbeddingAuditLog::record(
                    $this->organizationId,
                    $feedback->id,
                    'created',
                    ['source' => 'feedback_positive', 'version_tag' => $this->versionTag],
                    'reindex_job',
                );
            }

            $feedback->update(['status' => 'applied']);
        }

        // 3. Process negative hallucination feedback
        $hallucinationFeedback = ImprovementFeedback::where('organization_id', $this->organizationId)
            ->negative()
            ->where('status', '!=', 'applied')
            ->get()
            ->filter(fn ($fb) => is_array($fb->tags) && in_array('hallucination', $fb->tags));

        foreach ($hallucinationFeedback as $feedback) {
            $context = $feedback->context ?? [];
            $text = $context['response_snippet'] ?? '';

            if (empty(trim($text))) {
                $feedback->update(['status' => 'applied']);

                continue;
            }

            $embeddingVector = $embeddingService->generate($text);
            if ($embeddingVector) {
                $similar = $embeddingService->findSimilar('embeddings', $embeddingVector, 3, $this->organizationId);

                foreach ($similar as $match) {
                    $existing = Embedding::find($match->id);
                    if ($existing) {
                        $metadata = $existing->metadata ?? [];
                        $metadata['flagged_unreliable'] = true;
                        $metadata['flagged_at'] = now()->toISOString();
                        $metadata['flagged_reason'] = 'hallucination_feedback';
                        $existing->update(['metadata' => $metadata]);
                        $embeddingsFlagged++;

                        EmbeddingAuditLog::record(
                            $this->organizationId,
                            $existing->id,
                            'flagged',
                            ['reason' => 'hallucination_feedback', 'feedback_id' => $feedback->id],
                            'reindex_job',
                        );
                    }
                }
            }

            $feedback->update(['status' => 'applied']);
        }

        // 4. Update version snapshot
        $version->update([
            'snapshot_count' => $embeddingsCreated,
            'metadata' => array_merge($version->metadata ?? [], [
                'completed_at' => now()->toISOString(),
                'embeddings_created' => $embeddingsCreated,
                'embeddings_flagged' => $embeddingsFlagged,
                'positive_processed' => $positiveFeedback->count(),
                'hallucination_processed' => $hallucinationFeedback->count(),
            ]),
        ]);

        Log::channel('agents')->info('ReindexKnowledge completed', [
            'organization_id' => $this->organizationId,
            'version_tag' => $this->versionTag,
            'embeddings_created' => $embeddingsCreated,
            'embeddings_flagged' => $embeddingsFlagged,
        ]);
    }
}
