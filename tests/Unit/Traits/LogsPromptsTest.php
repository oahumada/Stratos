<?php

namespace Tests\Unit\Traits;

use App\Traits\LogsPrompts;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class LogsPromptsTest extends TestCase
{
    private $class;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an anonymous class using the trait
        $this->class = new class {
            use LogsPrompts;
        };
    }

    /**
     * Test logPrompt creates proper hash and logs to llm_prompts channel
     */
    public function test_logPrompt_hashes_prompt_safely(): void
    {
        Log::shouldReceive('channel')
            ->with('llm_prompts')
            ->andReturnSelf()
            ->shouldReceive('info')
            ->once()
            ->with('LLM Call', \Mockery::on(function ($arg) {
                return isset($arg['prompt_hash']) && 
                       isset($arg['output_hash']) &&
                       strlen($arg['prompt_hash']) === 64 && // SHA-256 is 64 chars
                       strlen($arg['output_hash']) === 64;
            }));

        Log::shouldReceive('debug')
            ->once()
            ->with('LLM call logged', \Mockery::any());

        $prompt = 'What is the meaning of life?';
        $output = '42';
        $hash = $this->class->logPrompt($prompt, $output, ['agent' => 'test_agent']);

        $this->assertEquals(hash('sha256', $prompt), $hash);
    }

    /**
     * Test logPrompt includes metadata
     */
    public function test_logPrompt_includes_metadata(): void
    {
        Log::shouldReceive('channel')
            ->with('llm_prompts')
            ->andReturnSelf()
            ->shouldReceive('info')
            ->once()
            ->with('LLM Call', \Mockery::on(function ($arg) {
                return $arg['agent'] === 'sentinel' &&
                       $arg['organization_id'] === 42 &&
                       $arg['provider'] === 'openai';
            }));

        Log::shouldReceive('debug')->once();

        $this->class->logPrompt('test', 'output', [
            'agent' => 'sentinel',
            'organization_id' => 42,
            'provider' => 'openai',
            'model' => 'gpt-4',
        ]);
    }

    /**
     * Test logPromptError logs exceptions safely
     */
    public function test_logPromptError_logs_exception_safely(): void
    {
        $exception = new \RuntimeException('API timeout');

        Log::shouldReceive('channel')
            ->with('llm_prompts')
            ->andReturnSelf()
            ->shouldReceive('error')
            ->once()
            ->with('LLM Call Failed', \Mockery::on(function ($arg) {
                return isset($arg['prompt_hash']) &&
                       $arg['error_class'] === \RuntimeException::class &&
                       $arg['error_message'] === 'API timeout';
            }));

        Log::shouldReceive('error')
            ->once()
            ->with('LLM error', \Mockery::any());

        $prompt = 'Generate response';
        $hash = $this->class->logPromptError($prompt, $exception, ['agent' => 'test']);

        $this->assertEquals(hash('sha256', $prompt), $hash);
    }

    /**
     * Test correlatePromptFeedback correlates feedback with prompt
     */
    public function test_correlatePromptFeedback_logs_feedback(): void
    {
        $promptHash = hash('sha256', 'test prompt');

        Log::shouldReceive('channel')
            ->with('llm_prompts')
            ->andReturnSelf()
            ->shouldReceive('info')
            ->once()
            ->with('LLM Feedback Correlated', \Mockery::on(function ($arg) use ($promptHash) {
                return $arg['prompt_hash'] === $promptHash &&
                       $arg['feedback_type'] === 'hallucination';
            }));

        $this->class->correlatePromptFeedback($promptHash, 'hallucination', [
            'feedback_text' => 'Wrong date mentioned',
            'organization_id' => 10,
        ]);
    }

    /**
     * Test that no sensitive data is stored in logs
     */
    public function test_logPrompt_never_stores_plaintext_prompt(): void
    {
        $sensitivePrompt = 'User email: john@example.com, SSN: 123-45-6789';

        Log::shouldReceive('channel')
            ->with('llm_prompts')
            ->andReturnSelf()
            ->shouldReceive('info')
            ->once()
            ->with('LLM Call', \Mockery::on(function ($arg) use ($sensitivePrompt) {
                // Assert that the plaintext prompt is NOT in the logged data
                $loggedString = json_encode($arg);
                
                return ! str_contains($loggedString, 'john@example.com') &&
                       ! str_contains($loggedString, '123-45-6789') &&
                       isset($arg['prompt_hash']);
            }));

        Log::shouldReceive('debug')->once();

        $this->class->logPrompt($sensitivePrompt, 'response', []);
    }
}
