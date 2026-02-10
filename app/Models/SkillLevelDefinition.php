<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillLevelDefinition extends Model
{
    protected $fillable = [
        'level',
        'name',
        'description',
        'points',
    ];

    protected $casts = [
        'level' => 'integer',
        'points' => 'integer',
    ];

    /**
     * Get the display label for this level (number + name)
     */
    public function getDisplayLabelAttribute(): string
    {
        return "{$this->level} - {$this->name}";
    }

    /**
     * Get the full description with level
     */
    public function getFullDescriptionAttribute(): string
    {
        return "Nivel {$this->level} ({$this->name}): {$this->description}";
    }
}
