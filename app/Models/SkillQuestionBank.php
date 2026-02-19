<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillQuestionBank extends Model
{
    use HasFactory;

    protected $table = 'skill_question_bank';

    protected $fillable = [
        'skill_id',
        'level',
        'archetype',
        'target_relationship',
        'question',
        'question_type',
        'is_global',
    ];

    protected $casts = [
        'level' => 'integer',
        'is_global' => 'boolean',
    ];

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
}
