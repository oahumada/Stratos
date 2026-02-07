<?php

namespace App\Jobs;

use App\Models\ScenarioGeneration;
use App\Services\LLMClient;
use App\Services\RedactionService;
use App\Services\LLMProviders\Exceptions\LLMRateLimitException;
use App\Services\LLMProviders\Exceptions\LLMServerException;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateScenarioFromLLMJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $generationId;

    public function __construct(int $generationId)
    {
        $this->generationId = $generationId;
    }

    public function handle(LLMClient $llm)
    {
        $generation = ScenarioGeneration::find($this->generationId);
        if (!$generation) {
            return;
        }

        $generation->status = 'processing';
        $generation->save();

        try {
            $result = $llm->generate($generation->prompt ?? '');

            $rawResponse = $result['response'] ?? $result;
            // Redact any PII from LLM response before persisting
            $generation->llm_response = is_array($rawResponse)
                ? RedactionService::redactArray($rawResponse)
                : RedactionService::redactText((string) $rawResponse);
            $generation->confidence_score = $result['confidence'] ?? null;
            $generation->model_version = $result['model_version'] ?? null;
            $generation->generated_at = now();
            $generation->status = 'complete';
            $generation->save();
        } catch (LLMRateLimitException $e) {
            // transient rate limit: requeue with exponential backoff if attempts remain
            $attempts = $this->attempts();
            $maxAttempts = 5;
            $retryAfter = $e->getRetryAfter() ?? (int) pow(2, max(0, $attempts));

            if ($attempts < $maxAttempts) {
                // release back to queue for retry
                $this->release($retryAfter);
                return;
            }

            $generation->status = 'failed';
            $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'rate_limit_exceeded', 'message' => $e->getMessage()]);
            $generation->save();
        } catch (LLMServerException $e) {
            // server-side errors: mark failed and record
            $generation->status = 'failed';
            $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'server_error', 'message' => $e->getMessage()]);
            $generation->save();
        } catch (Exception $e) {
            $generation->status = 'failed';
            $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'exception', 'message' => $e->getMessage()]);
            $generation->save();
        }
    }
}
