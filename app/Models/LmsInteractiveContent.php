<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsInteractiveContent extends Model
{
    protected $fillable = [
        'organization_id',
        'lesson_id',
        'widget_type',
        'config',
        'title',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
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

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }
}
