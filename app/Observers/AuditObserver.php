<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

/**
 * AuditObserver - Automatically track CRUD operations
 *
 * Observes models that need audit logging and creates AuditLog entries.
 * Attach this observer to models in their booted() method or via service provider.
 */
class AuditObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->logAudit($model, 'created');
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        // Only log if actual changes (not just timestamps)
        $changes = $model->getChanges();

        // Filter out updated_at timestamp
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $this->logAudit($model, 'updated', $this->getChangeDiff($model));
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->logAudit($model, 'deleted');
    }

    /**
     * Create an audit log entry
     */
    protected function logAudit(Model $model, string $action, ?array $changes = null): void
    {
        try {
            // Extract organization_id from model or authenticated user
            $organizationId = $model->organization_id ?? auth()->user()?->organization_id;

            if (! $organizationId) {
                return; // Skip if no organization context
            }

            AuditLog::create([
                'organization_id' => $organizationId,
                'user_id' => auth()->id(),
                'action' => $action,
                'entity_type' => class_basename($model),
                'entity_id' => (string) $model->getKey(),
                'changes' => $changes,
                'metadata' => [
                    'model_class' => get_class($model),
                    'ip_address' => request()?->ip(),
                    'user_agent' => request()?->userAgent(),
                ],
                'triggered_by' => $this->getTriggerSource(),
            ]);
        } catch (\Throwable $e) {
            // Log audit failures silently to avoid breaking model operations
            \Illuminate\Support\Facades\Log::warning('Failed to create audit log', [
                'model' => get_class($model),
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Calculate what changed from original values to new values
     */
    protected function getChangeDiff(Model $model): array
    {
        $changes = [];

        foreach ($model->getChanges() as $field => $newValue) {
            // Skip system fields
            if (in_array($field, ['updated_at', 'created_at'])) {
                continue;
            }

            $oldValue = $model->getOriginal($field);
            $changes[$field] = [$oldValue, $newValue];
        }

        return $changes;
    }

    /**
     * Determine what triggered this audit event
     */
    protected function getTriggerSource(): string
    {
        if (auth()->check()) {
            return 'user';
        }

        if (app()->runningInConsole()) {
            return 'console';
        }

        if (app()->runningInConsole() && str_contains(request()?->path() ?? '', '/api')) {
            return 'api';
        }

        return 'system';
    }
}

