<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetencyLevelBars extends Model
{
    use HasFactory;

    protected $table = 'competency_levels_bars';

    protected $fillable = [
        'skill_id',
        'level',
        'name',
        'description',
        'key_behaviors',
    ];

    protected $casts = [
        'key_behaviors' => 'array',
    ];

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
}
