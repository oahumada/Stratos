<?php
namespace App\Services;

use App\Models\MetadataValidationIssue;

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

    /**
     * Append validation event to JSONL log for audit.
     * Each line is a JSON object with: timestamp, generation_id (optional), metadata, errors
     */
    public function log(int $generationId = null, array $meta = [], array $errors = []): ?int
    {
        try {
            $path = storage_path('logs/metadata-validation.log');
            $entry = [
                'ts' => now()->toDateTimeString(),
                'generation_id' => $generationId,
                'metadata' => $meta,
                'errors' => $errors,
            ];
            $line = json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL;
            // ensure directory
            $dir = dirname($path);
            if (! is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
            file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
            // also persist a DB record for easier querying and alerting
            try {
                $rec = MetadataValidationIssue::create([
                    'generation_id' => $generationId,
                    'metadata' => $meta ?: null,
                    'errors' => $errors ?: null,
                ]);
                return $rec->id;
            } catch (\Throwable $e) {
                // if DB write fails, still avoid throwing from logger
            }
        } catch (\Throwable $e) {
            // avoid throwing from logger
        }

        return null;
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
