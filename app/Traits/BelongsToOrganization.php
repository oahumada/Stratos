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
            // If the user is logged in and has an organization_id, filter by it.
            // We exclude models like Organizations itself if it were to use this trait.
            if ($user && $user->organization_id) {
                $model = new static;
                $table = $model->getTable();
                $builder->where("{$table}.organization_id", $user->organization_id);
            }
        });

        static::creating(function ($model) {
            if (empty($model->organization_id) && Auth::check()) {
                $user = Auth::user();
                if ($user->organization_id) {
                    $model->organization_id = $user->organization_id;
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
