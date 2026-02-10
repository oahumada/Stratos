<?php
namespace App\Services;

class MetadataValidator
{
    /**
     * Validate metadata shape (lightweight, tolerant).
     * Returns an array of error messages (empty = valid).
     */
    public function validate(array $meta): array
    {
        $errors = [];

        if (! isset($meta['provider']) || ! is_string($meta['provider']) || $meta['provider'] === '') {
            $errors[] = 'provider missing or invalid';
        }

        if (! isset($meta['model']) || ! is_string($meta['model']) || $meta['model'] === '') {
            $errors[] = 'model missing or invalid';
        }

        if (isset($meta['progress']) && ! is_array($meta['progress'])) {
            $errors[] = 'progress must be an object';
        } else {
            $progress = $meta['progress'] ?? [];
            if (isset($progress['received_chunks']) && ! is_int($progress['received_chunks'])) {
                $errors[] = 'progress.received_chunks must be integer';
            }
            if (isset($progress['received_bytes']) && ! is_int($progress['received_bytes'])) {
                $errors[] = 'progress.received_bytes must be integer';
            }
        }

        // timestamps basic check
        foreach (['first_chunk_at', 'last_chunk_at', 'compacted_at'] as $field) {
            if (isset($meta[$field]) && ! $this->isValidDateTimeString($meta[$field])) {
                $errors[] = "{$field} must be date-time string";
            }
        }

        return $errors;
    }

    private function isValidDateTimeString($v): bool
    {
        if (! is_string($v)) return false;
        try {
            new \DateTime($v);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
