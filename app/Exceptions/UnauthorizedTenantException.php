<?php

namespace App\Exceptions;

/**
 * UnauthorizedTenantException - Thrown when cross-tenant access detected
 *
 * Verification must be scoped to authenticated user's organization
 */
class UnauthorizedTenantException extends \Exception
{
    public function __construct(
        string $message = 'Cross-tenant access detected in verification',
    ) {
        parent::__construct($message);
    }
}
