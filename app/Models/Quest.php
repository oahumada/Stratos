<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'points_reward',
        'badge_id',
        'requirements',
        'status',
    ];

    protected $casts = [
        'requirements' => 'array',
    ];

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }
}
