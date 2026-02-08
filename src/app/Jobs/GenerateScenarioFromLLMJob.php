<?php

namespace App\Jobs;

use App\Models\ScenarioGeneration;
use App\Services\LLMClient;
use App\Services\LLMProviders\Exceptions\LLMRateLimitException;
use App\Services\LLMProviders\Exceptions\LLMServerException;
use App\Services\RedactionService;
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
        if (! $generation) {
            return;
        }

        $generation->status = 'processing';
        $generation->save();

        try {
            $result = $llm->generate($generation->prompt ?? '');

            $rawResponse = $result['response'] ?? $result;

            // Normalize and validate JSON: accept array/object or JSON string
            $parsed = null;
            if (is_string($rawResponse)) {
                $parsed = json_decode($rawResponse, true);
            } elseif (is_array($rawResponse) || is_object($rawResponse)) {
                $parsed = is_array($rawResponse) ? $rawResponse : json_decode(json_encode($rawResponse), true);
            }

            // Basic schema validation: required top-level keys
            $requiredKeys = ['scenario_metadata', 'capacities', 'competencies', 'skills', 'suggested_roles', 'impact_analysis'];
            $isValid = is_array($parsed);
            if ($isValid) {
                foreach ($requiredKeys as $key) {
                    if (!array_key_exists($key, $parsed)) {
                        $isValid = false;
                        break;
                    }
                }
            }

            if (!$isValid) {
                // Persist metadata about invalid response and mark failed
                $generation->status = 'failed';
                $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'invalid_llm_response', 'message' => 'LLM returned invalid or non-JSON response']);
                $generation->save();
                return;
            }

            // Redact any PII from LLM response before persisting
            $generation->llm_response = RedactionService::redactArray($parsed);
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
