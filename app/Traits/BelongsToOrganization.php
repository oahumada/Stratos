<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToOrganization
{
    /**
     * Boot the trait to add the global organization scope and auto-assign organization_id.
     */
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            $user = Auth::user();
            // If the user is logged in and has an organization context, filter by it.
            // Prefer `current_organization_id` when available (tests and multi-org sessions),
            // fallback to `organization_id` for backwards compatibility.
            $orgId = null;
            if ($user) {
                $orgId = $user->current_organization_id ?? $user->organization_id ?? null;
            }

            if ($orgId) {
                $model = new static;
                $table = $model->getTable();
                $builder->where("{$table}.organization_id", $orgId);
            }
        });

        static::creating(function ($model) {
            if (empty($model->organization_id) && Auth::check()) {
                $user = Auth::user();
                $orgId = $user->current_organization_id ?? $user->organization_id ?? null;
                if ($orgId) {
                    $model->organization_id = $orgId;
                }
            }
        });
    }

    /**
     * Define the relationship to the organization.
     */
    public function organization()
    {
        return $this->belongsTo(\App\Models\Organizations::class, 'organization_id');
    }
}
