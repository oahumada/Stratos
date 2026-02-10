<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleSkill extends Model
{
    protected $table = 'role_skills';

    protected $fillable = [
        'role_id',
        'skill_id',
        'required_level',
        'is_critical',
    ];

    protected $casts = [
        'required_level' => 'integer',
        'is_critical' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Roles::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
}
