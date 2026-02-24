<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentPolicy extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'target_type',
        'target_id',
        'frequency_months',
        'trigger_event',
        'evaluators_config',
        'owner_id',
        'is_active',
        'last_run_at'
    ];

    protected $casts = [
        'evaluators_config' => 'array',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organizations::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function target()
    {
        $target = null;

        if ($this->target_type === 'department') {
            $target = $this->belongsTo(Departments::class, 'target_id');
        } elseif ($this->target_type === 'role') {
            $target = $this->belongsTo(Roles::class, 'target_id');
        } elseif ($this->target_type === 'person') {
            $target = $this->belongsTo(People::class, 'target_id');
        }

        return $target;
    }
}
