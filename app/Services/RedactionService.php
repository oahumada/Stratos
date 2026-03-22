<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class RedactionService
{
    /**
     * Redaction types and their patterns
     */
    public const REDACTION_TYPES = [
        'email' => '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i',
        'phone' => '/(\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}\b/',
        'ssn' => '/\b\d{3}[- ]?\d{2}[- ]?\d{4}\b/',
        'credit_card' => '/\b(?:\d{4}[-]?){3}\d{4}\b/',
        'token' => '/\b[A-Za-z0-9_\-]{20,}\b/',
        'api_key' => '/([?&](?:api_key|token|key|access_token|secret)=)([^&\s]+)/i',
        'bearer' => '/Bearer\s+[A-Za-z0-9\-\._~\+\/]+=*/i',
        'passport' => '/\b[A-Z]{1,2}\d{6,9}\b/',
        'date_birth' => '/\b(?:19|20)\d{2}[-\/](?:0?[1-9]|1[0-2])[-\/](?:0?[1-9]|[12]\d|3[01])\b/',
    ];

    protected static array $enabledTypes = ['email', 'phone', 'ssn', 'credit_card', 'token', 'api_key', 'bearer'];

    protected static array $replacementMap = [
        'email' => '[REDACTED_EMAIL]',
        'phone' => '[REDACTED_PHONE]',
        'ssn' => '[REDACTED_SSN]',
        'credit_card' => '[REDACTED_CC]',
        'token' => '[REDACTED_TOKEN]',
        'api_key' => '$1[REDACTED]',
        'bearer' => 'Bearer [REDACTED_TOKEN]',
        'passport' => '[REDACTED_PASSPORT]',
        'date_birth' => '[REDACTED_DOB]',
    ];

    /**
     * Set which redaction types are active
     */
    public static function setEnabledTypes(array $types): void
    {
        $types = array_filter($types, fn ($type) => array_key_exists($type, self::REDACTION_TYPES));
        self::$enabledTypes = ! empty($types) ? $types : self::$enabledTypes;
    }

    /**
     * Get currently enabled redaction types
     */
    public static function getEnabledTypes(): array
    {
        return self::$enabledTypes;
    }

    /**
     * Reset to default redaction types
     */
    public static function resetEnabledTypes(): void
    {
        self::$enabledTypes = ['email', 'phone', 'ssn', 'credit_card', 'token', 'api_key', 'bearer'];
    }

    /**
     * Redact PII and secrets from free text with configurable types
     */
    public static function redactText(string $text, ?array $types = null): string
    {
        $types = $types ?? self::$enabledTypes;
        $originalText = $text;
        $redactedCount = 0;

        foreach ($types as $type) {
            if (! array_key_exists($type, self::REDACTION_TYPES)) {
                continue;
            }

            $pattern = self::REDACTION_TYPES[$type];
            $replacement = self::$replacementMap[$type];

            // Use preg_replace_callback to count replacements
            $text = preg_replace_callback($pattern, function ($matches) use ($replacement, &$redactedCount) {
                $redactedCount++;
                // For patterns with groups (like api_key), handle group replacement
                if (strpos($replacement, '$') !== false) {
                    return str_replace(['$1', '$2'], [$matches[1] ?? '', $matches[2] ?? ''], $replacement);
                }

                return $replacement;
            }, $text);
        }

        // Log redaction if any PII was found
        if ($redactedCount > 0) {
            self::logRedaction($text, $originalText, $redactedCount, $types);
        }

        return $text;
    }

    /**
     * Recursively redact strings inside arrays (LLM JSON responses, metadata, etc.)
     */
    public static function redactArray(array $data, ?array $types = null): array
    {
        $redacted = [];
        $types = $types ?? self::$enabledTypes;

        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $redacted[$k] = self::redactText($v, $types);
            } elseif (is_array($v)) {
                $redacted[$k] = self::redactArray($v, $types);
            } else {
                $redacted[$k] = $v;
            }
        }

        return $redacted;
    }

    /**
     * Log redaction event for audit trail
     */
    protected static function logRedaction(string $redactedText, string $originalText, int $count, array $types): void
    {
        try {
            // Calculate hash of original for comparison
            $originalHash = hash('sha256', $originalText);

            Log::channel('redaction')->info('PII redacted', [
                'types' => $types,
                'count' => $count,
                'original_hash' => $originalHash,
                'redacted_length' => strlen($redactedText),
                'timestamp' => now()->toIso8601String(),
            ]);

            // Also create audit record if needed via RedactionAuditTrail model
            if (class_exists(\App\Models\RedactionAuditTrail::class)) {
                \App\Models\RedactionAuditTrail::create([
                    'redaction_types' => $types,
                    'count' => $count,
                    'original_hash' => $originalHash,
                    'context' => app('request')?->path() ?? 'cli',
                    'user_id' => auth()->id(),
                    'organization_id' => auth()->user()?->organization_id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to log redaction', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get redaction statistics for organization
     */
    public static function getRedactionStats(?int $organizationId = null, ?\DateTime $since = null): array
    {
        if (! class_exists(\App\Models\RedactionAuditTrail::class)) {
            return [];
        }

        $query = \App\Models\RedactionAuditTrail::query();

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        if ($since) {
            $query->where('created_at', '>=', $since);
        }

        return [
            'total_redactions' => $query->sum('count'),
            'redaction_events' => $query->count(),
            'types_frequency' => self::getTypeFrequency($query),
            'top_contexts' => $query->select('context')
                ->groupBy('context')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(10)
                ->pluck('context')
                ->toArray(),
        ];
    }

    /**
     * Get frequency of each redaction type
     */
    protected static function getTypeFrequency($query): array
    {
        $records = $query->select(['redaction_types', 'count'])->get();
        $frequency = [];

        foreach ($records as $record) {
            $types = is_array($record->redaction_types) ? $record->redaction_types : json_decode($record->redaction_types ?? '[]', true);
            foreach ($types as $type) {
                $frequency[$type] = ($frequency[$type] ?? 0) + $record->count;
            }
        }

        arsort($frequency);

        return $frequency;
    }

    /**
     * Validate if text contains PII (without redacting)
     */
    public static function containsPii(string $text, ?array $types = null): bool
    {
        $types = $types ?? self::$enabledTypes;

        foreach ($types as $type) {
            if (array_key_exists($type, self::REDACTION_TYPES)) {
                if (preg_match(self::REDACTION_TYPES[$type], $text)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get detected PII matches (for analysis/reporting)
     */
    public static function detectPii(string $text, ?array $types = null): array
    {
        $types = $types ?? self::$enabledTypes;
        $detected = [];

        foreach ($types as $type) {
            if (array_key_exists($type, self::REDACTION_TYPES)) {
                $pattern = self::REDACTION_TYPES[$type];
                if (preg_match_all($pattern, $text, $matches)) {
                    $detected[$type] = $matches[0];
                }
            }
        }

        return $detected;
    }
}
