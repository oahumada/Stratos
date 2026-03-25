<?php

namespace App\Events;

class OperationCompleted
{
    public int $operationId;
    public array $operation;
    public int $organizationId;

    public function __construct(int $operationId, array $operation, int $organizationId)
    {
        $this->operationId = $operationId;
        $this->operation = $operation;
        $this->organizationId = $organizationId;
    }
}
