<?php

namespace App\Services;

class RedactionService
{
    /**
     * Redact PII and secrets from free text.
     */
    public static function redactText(string $text): string
    {
        // Emails
        $text = preg_replace('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', '[REDACTED_EMAIL]', $text);

        // Long tokens / API keys: sequences of alnum/_- of length >=20
        $text = preg_replace('/\b[A-Za-z0-9_\-]{20,}\b/', '[REDACTED_TOKEN]', $text);

        // Bearer tokens in headers or inline
        $text = preg_replace('/Bearer\s+[A-Za-z0-9\-\._~\+\/]+=*/i', 'Bearer [REDACTED_TOKEN]', $text);

        // Query params like ?api_key=..., &token=..., remove values
        $text = preg_replace_callback('/([?&](?:api_key|token|key|access_token)=)([^&\s]+)/i', function ($m) {
            return $m[1].'[REDACTED]';
        }, $text);

        // Simple SSN pattern (US) e.g., 123-45-6789
        $text = preg_replace('/\b\d{3}[- ]?\d{2}[- ]?\d{4}\b/', '[REDACTED_SSN]', $text);

        return $text;
    }

    /**
     * Recursively redact strings inside arrays (LLM JSON responses, metadata, etc.)
     */
    public static function redactArray(array $data): array
    {
        $redacted = [];
        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $redacted[$k] = self::redactText($v);
            } elseif (is_array($v)) {
                $redacted[$k] = self::redactArray($v);
            } else {
                $redacted[$k] = $v;
            }
        }

        return $redacted;
    }
}
