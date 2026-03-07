<?php

namespace App\Jobs;

use App\Models\LLMEvaluation;
use App\Services\RAGASEvaluator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EvaluateLLMGeneration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Evaluation record ID to process
     */
    public int $evaluationId;

    /**
     * Number of attempts before failing
     */
    public int $tries = 3;

    /**
     * Backoff strategy for retries (exponential)
     */
    public array $backoff = [10, 60, 300];

    /**
     * Queue name
     */
    public string $queue = 'default';

    /**
     * Create a new job instance.
     */
    public function __construct(int $evaluationId)
    {
        $this->evaluationId = $evaluationId;
        $this->queue = config('ragas.evaluation_queue', 'default');
    }

    /**
     * Execute the job.
     */
    public function handle(RAGASEvaluator $evaluator): void
    {
        $evaluation = LLMEvaluation::find($this->evaluationId);

        if (! $evaluation) {
            Log::warning('LLMEvaluation not found', ['id' => $this->evaluationId]);

            return;
        }

        try {
            Log::info('Starting RAGAS evaluation', [
                'evaluation_id' => $evaluation->id,
                'provider' => $evaluation->llm_provider,
                'organization_id' => $evaluation->organization_id,
            ]);

            // Perform evaluation
            $evaluator->performEvaluation($evaluation);

            Log::info('RAGAS evaluation successful', [
                'evaluation_id' => $evaluation->id,
                'score' => $evaluation->composite_score,
            ]);
        } catch (\Exception $e) {
            Log::error('RAGAS evaluation job failed', [
                'evaluation_id' => $evaluation->id,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
            ]);

            // Retry if attempts remaining
            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff[$this->attempts() - 1] ?? 60);
            } else {
                // Mark as permanently failed after all retries exhausted
                $evaluation->update([
                    'status' => 'failed',
                    'error_message' => "All {$this->tries} retry attempts failed: {$e->getMessage()}",
                ]);
            }
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Exception $exception): void
    {
        Log::error('RAGAS evaluation job permanently failed', [
            'evaluation_id' => $this->evaluationId,
            'error' => $exception->getMessage(),
        ]);

        $evaluation = LLMEvaluation::find($this->evaluationId);
        if ($evaluation) {
            $evaluation->update([
                'status' => 'failed',
                'error_message' => "Job permanently failed after retries: {$exception->getMessage()}",
            ]);
        }
    }
}
