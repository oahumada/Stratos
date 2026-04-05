<?php

namespace App\Contracts;

interface AgentInterface
{
    /**
     * Execute the agent's primary task.
     *
     * @param  array  $input  Agent-specific input payload
     * @param  array  $context  Additional context (organization_id, etc.)
     * @return array Agent output
     */
    public function execute(array $input, array $context = []): array;

    /**
     * Get the agent's registered name.
     */
    public function getName(): string;

    /**
     * Get the agent's capability descriptors.
     *
     * @return array<string>
     */
    public function getCapabilities(): array;
}
