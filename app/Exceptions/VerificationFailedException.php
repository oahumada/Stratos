<?php

namespace App\Exceptions;

/**
 * VerificationFailedException - Thrown when agent output fails verification
 *
 * Raised during 'reject' phase when violations make output invalid
 */
class VerificationFailedException extends \Exception
{
    public function __construct(
        public array $violations = [],
        public string $agentName = '',
        string $message = 'Agent output failed verification',
    ) {
        parent::__construct($message);
    }
}
