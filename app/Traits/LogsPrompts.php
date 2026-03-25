<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

/**
 * LogsPrompts Trait
 *
 * Provides PII-safe logging of LLM prompts and outputs using hash-based tracking.
 * Prevents accidental exposure of sensitive data while maintaining auditability.
 *
 * Usage:
 *   class MyLLMService {
 *       use LogsPrompts;
 *       public function generateResponse($prompt) {
 *           $output = $this->llm->generate($prompt);
 *           $this->logPrompt($prompt, $output, ['agent' => 'guide']);
 *           return $output;
 *       }
 *   }
 */
trait LogsPrompts
{
    /**
     * Log LLM prompt and output with PII protection.
     *
     * Instead of storing the full prompt (which may contain sensitive data),
     * we store a SHA-256 hash that can be used for tracking correlations
     * without exposing actual content.
     *
     * @param  string  $prompt  The original prompt (not stored in plaintext)
     * @param  mixed  $output  The LLM output (can be trimmed/redacted if needed)
     * @param  array  $metadata  Additional context (agent, model, org_id, etc.)
     * @return string Hash of the prompt for correlation tracking
     */
    public static function logPrompt(
        string $prompt,
        mixed $output,
        ?array $metadata = null,
    ): string {
        $promptHash = hash('sha256', $prompt);
        $outputHash = is_string($output) ? hash('sha256', $output) : hash('sha256', json_encode($output));

        $logData = [
            'prompt_hash' => $promptHash,
            'output_hash' => $outputHash,
            'organization_id' => $metadata['organization_id'] ?? null,
            'agent' => $metadata['agent'] ?? null,
            'model' => $metadata['model'] ?? null,
            'provider' => $metadata['provider'] ?? null,
            'timestamp' => now()->toIso8601String(),
            'user_id' => auth()->check() ? auth()->id() : null,
        ];

        // Log to dedicated 'll_prompts' channel for easier filtering/auditing
        Log::channel('llm_prompts')->info('LLM Call', $logData);

        // Also log to standard channel with limited info (for debugging)
        Log::debug('LLM call logged', [
            'prompt_hash' => $promptHash,
            'agent' => $metadata['agent'] ?? 'unknown',
        ]);

        return $promptHash;
    }

    /**
     * Log a prompt error/exception (e.g., LLM timeout, API error).
     *
     * @param  string  $prompt  The original prompt
     * @param  \Throwable  $exception  The exception that occurred
     * @param  array  $metadata  Additional context
     * @return string Hash of the prompt for correlation
     */
    public static function logPromptError(
        string $prompt,
        \Throwable $exception,
        ?array $metadata = null,
    ): string {
        $promptHash = hash('sha256', $prompt);

        $logData = [
            'prompt_hash' => $promptHash,
            'error_class' => $exception::class,
            'error_message' => $exception->getMessage(),
            'organization_id' => $metadata['organization_id'] ?? null,
            'agent' => $metadata['agent'] ?? null,
            'timestamp' => now()->toIso8601String(),
            'user_id' => auth()->check() ? auth()->id() : null,
        ];

        Log::channel('llm_prompts')->error('LLM Call Failed', $logData);
        Log::error('LLM error', ['prompt_hash' => $promptHash, 'error' => $exception->getMessage()]);

        return $promptHash;
    }

    /**
     * Correlate a future action/feedback with a logged prompt.
     * Useful for feedback loops: user rates answer → correlate with original prompt hash.
     *
     * @param  string  $promptHash  The hash returned by logPrompt()
     * @param  string  $feedbackType  Type of feedback (e.g., 'hallucination', 'relevant', 'irrelevant')
     * @param  mixed  $feedbackData  Structured feedback
     */
    public static function correlatePromptFeedback(
        string $promptHash,
        string $feedbackType,
        ?array $feedbackData = null,
    ): void {
        $logData = [
            'prompt_hash' => $promptHash,
            'feedback_type' => $feedbackType,
            'feedback_data' => $feedbackData,
            'organization_id' => $feedbackData['organization_id'] ?? null,
            'timestamp' => now()->toIso8601String(),
            'user_id' => auth()->check() ? auth()->id() : null,
        ];

        Log::channel('llm_prompts')->info('LLM Feedback Correlated', $logData);
    }
}
