<?php

namespace App\Services\Validators;

use InvalidArgumentException;

/**
 * ValidatorFactory - Resolves the correct business validator for each agent
 *
 * Maps agent IDs to their corresponding validator classes, providing
 * a single entry point for getting the right validator.
 */
class ValidatorFactory
{
    /**
     * Map of agent IDs to validator class names
     */
    protected const VALIDATOR_MAP = [
        'Estratega de Talento' => StrategyAgentValidator::class,
        'Orquestador 360' => OrchestracionValidator::class,
        'Matchmaker de Resonancia' => MatchmakerValidator::class,
        'Coach de Crecimiento' => CoachValidator::class,
        'Diseñador de Roles' => RoleDesignerValidator::class,
        'Navegador de Cultura' => CultureNavigatorValidator::class,
        'Curador de Competencias' => CompetencyValidator::class,
        'Arquitecto de Aprendizaje' => LearningArchitectValidator::class,
        'Stratos Sentinel' => SentinelValidator::class,
    ];

    /**
     * Resolve validator for given agent ID
     *
     * @param  string  $agentId  The agent identifier
     * @return object The validator instance (implements validate method)
     *
     * @throws InvalidArgumentException If agent ID is not recognized
     */
    public static function make(string $agentId): object
    {
        if (! array_key_exists($agentId, self::VALIDATOR_MAP)) {
            throw new InvalidArgumentException(sprintf(
                'Unknown agent "%s". Available agents: %s',
                $agentId,
                implode(', ', array_keys(self::VALIDATOR_MAP))
            ));
        }

        $validatorClass = self::VALIDATOR_MAP[$agentId];

        return new $validatorClass;
    }

    /**
     * Get all registered agent IDs
     *
     * @return string[]
     */
    public static function getRegisteredAgents(): array
    {
        return array_keys(self::VALIDATOR_MAP);
    }

    /**
     * Check if agent is registered
     */
    public static function hasAgent(string $agentId): bool
    {
        return array_key_exists($agentId, self::VALIDATOR_MAP);
    }
}
