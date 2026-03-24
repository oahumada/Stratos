<?php

namespace App\Services\Validators;

use App\Data\VerificationViolation;

/**
 * BaseBusinessValidator - Helper trait for business rule validators
 *
 * Provides reusable methods for common validation patterns:
 * - Enum validation (Buy/Build/Borrow, L1-L5, etc.)
 * - Numeric range validation (min/max bounds)
 * - Count/constraint validation (max arrays, lists)
 * - Field validation (required, present, not empty)
 *
 * Usage: Include in agent-specific validator classes
 */
trait BaseBusinessValidator
{
    /**
     * Validate enum field (e.g., strategy must be in [Buy, Build, Borrow])
     */
    protected function validateEnum(
        string $fieldName,
        mixed $fieldValue,
        array $validValues
    ): ?VerificationViolation {
        if (! isset($fieldValue)) {
            return null;
        }

        if (! in_array($fieldValue, $validValues, true)) {
            return new VerificationViolation(
                rule: 'invalid_value',
                severity: 'error',
                message: "Invalid {$fieldName}: {$fieldValue}",
                field: $fieldName,
                received: (string) $fieldValue,
                expected: implode(', ', $validValues)
            );
        }

        return null;
    }

    /**
     * Validate numeric field within min/max bounds
     */
    protected function validateNumericRange(
        string $fieldName,
        mixed $fieldValue,
        float $min,
        float $max
    ): ?VerificationViolation {
        if (! isset($fieldValue)) {
            return null;
        }

        if (! is_numeric($fieldValue)) {
            return new VerificationViolation(
                rule: 'invalid_value',
                severity: 'error',
                message: "{$fieldName} must be numeric",
                field: $fieldName,
                received: gettype($fieldValue)
            );
        }

        $numValue = (float) $fieldValue;

        if ($numValue < $min) {
            return new VerificationViolation(
                rule: 'threshold_exceeded',
                severity: 'warning',
                message: "{$fieldName} {$numValue} is below minimum {$min}",
                field: $fieldName,
                received: (string) $numValue,
                expected: ">= {$min}"
            );
        }

        if ($numValue > $max) {
            return new VerificationViolation(
                rule: 'threshold_exceeded',
                severity: 'warning',
                message: "{$fieldName} {$numValue} exceeds maximum {$max}",
                field: $fieldName,
                received: (string) $numValue,
                expected: "<= {$max}"
            );
        }

        return null;
    }

    /**
     * Validate array/list field doesn't exceed max count
     */
    protected function validateMaxCount(
        string $fieldName,
        mixed $fieldValue,
        int $maxCount
    ): ?VerificationViolation {
        if (! isset($fieldValue)) {
            return null;
        }

        if (! is_countable($fieldValue)) {
            return null;
        }

        $count = count($fieldValue);

        if ($count > $maxCount) {
            return new VerificationViolation(
                rule: 'constraint_violated',
                severity: 'warning',
                message: "{$fieldName} has {$count} items, max allowed is {$maxCount}",
                field: $fieldName,
                received: (string) $count,
                expected: "<= {$maxCount}"
            );
        }

        return null;
    }

    /**
     * Validate array/list field has minimum count
     */
    protected function validateMinCount(
        string $fieldName,
        mixed $fieldValue,
        int $minCount
    ): ?VerificationViolation {
        if (! isset($fieldValue)) {
            return null;
        }

        if (! is_countable($fieldValue)) {
            return null;
        }

        $count = count($fieldValue);

        if ($count < $minCount) {
            return new VerificationViolation(
                rule: 'constraint_violated',
                severity: 'warning',
                message: "{$fieldName} has {$count} items, minimum required is {$minCount}",
                field: $fieldName,
                received: (string) $count,
                expected: ">= {$minCount}"
            );
        }

        return null;
    }

    /**
     * Validate required field - must be present and not empty
     */
    protected function validateRequired(
        string $fieldName,
        mixed $fieldValue
    ): ?VerificationViolation {
        if (! isset($fieldValue) || (is_string($fieldValue) && trim($fieldValue) === '')) {
            return new VerificationViolation(
                rule: 'required_field_missing',
                severity: 'error',
                message: "Required field '{$fieldName}' is missing or empty",
                field: $fieldName
            );
        }

        return null;
    }

    /**
     * Validate string field within length bounds
     */
    protected function validateStringLength(
        string $fieldName,
        mixed $fieldValue,
        int $maxLength = 5000,
        int $minLength = 10
    ): ?VerificationViolation {
        if (! isset($fieldValue)) {
            return null;
        }

        if (! is_string($fieldValue)) {
            return new VerificationViolation(
                rule: 'invalid_value',
                severity: 'error',
                message: "{$fieldName} must be a string",
                field: $fieldName,
                received: gettype($fieldValue)
            );
        }

        $length = strlen($fieldValue);

        if ($length < $minLength) {
            return new VerificationViolation(
                rule: 'constraint_violated',
                severity: 'warning',
                message: "{$fieldName} length {$length} is below minimum {$minLength}",
                field: $fieldName,
                received: (string) $length,
                expected: ">= {$minLength}"
            );
        }

        if ($length > $maxLength) {
            return new VerificationViolation(
                rule: 'constraint_violated',
                severity: 'warning',
                message: "{$fieldName} length {$length} exceeds maximum {$maxLength}",
                field: $fieldName,
                received: (string) $length,
                expected: "<= {$maxLength}"
            );
        }

        return null;
    }

    /**
     * Validate field is array type
     */
    protected function validateIsArray(
        string $fieldName,
        mixed $fieldValue
    ): ?VerificationViolation {
        if (! isset($fieldValue)) {
            return null;
        }

        if (! is_array($fieldValue)) {
            return new VerificationViolation(
                rule: 'invalid_value',
                severity: 'error',
                message: "{$fieldName} must be an array",
                field: $fieldName,
                received: gettype($fieldValue)
            );
        }

        return null;
    }

    /**
     * Collect violations, filtering out nulls
     */
    protected function collectViolations(?VerificationViolation ...$violations): array
    {
        return array_filter($violations, fn ($v) => $v !== null);
    }
}
