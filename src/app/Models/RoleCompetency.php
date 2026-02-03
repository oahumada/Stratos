<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleCompetency extends Model
{
    protected $fillable = [
        'role_id',
        'competency_id',
        'required_level',
        'is_core',
        'rationale'
    ];

    protected $casts = [
        'is_core' => 'boolean'
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function competency(): BelongsTo
    {
        return $this->belongsTo(Competency::class);
    }
}