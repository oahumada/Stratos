<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeopleSkill extends Model
{
    protected $table = 'people_skills';

    protected $fillable = [
        'people_id',
        'skill_id',
        'level',
        'last_evaluated_at',
        'evaluated_by',
    ];

    protected $casts = [
        'level' => 'integer',
        'last_evaluated_at' => 'datetime',
    ];

    public function people(): BelongsTo
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skills::class, 'skill_id');
    }

    public function evaluatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
