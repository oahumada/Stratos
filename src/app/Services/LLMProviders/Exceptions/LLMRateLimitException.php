<?php

namespace App\Services\LLMProviders\Exceptions;

use Exception;

class LLMRateLimitException extends Exception
{
    protected ?int $retryAfter = null;

    public function __construct(string $message = 'Rate limit exceeded', ?int $retryAfter = null)
    {
        parent::__construct($message);
        $this->retryAfter = $retryAfter;
    }

    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
