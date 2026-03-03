<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmartAlert extends Model
{
    use HasFactory;

    protected $table = 'smart_alerts';

    protected $fillable = [
        'organization_id',
        'level',
        'category',
        'title',
        'message',
        'is_read',
        'action_link',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'action_link' => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organizations::class, 'organization_id');
    }
}
