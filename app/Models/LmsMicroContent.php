<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsMicroContent extends Model
{
    protected $fillable = [
        'organization_id',
        'lesson_id',
        'cards',
        'estimated_minutes',
    ];

    protected function casts(): array
    {
        return [
            'cards' => 'array',
            'estimated_minutes' => 'integer',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function lesson()
    {
        return $this->belongsTo(LmsLesson::class, 'lesson_id');
    }

    public function getCardCountAttribute(): int
    {
        return count($this->cards ?? []);
    }

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }
}
