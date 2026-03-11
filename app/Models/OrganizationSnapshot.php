<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizations_id',
        'snapshot_date',
        'average_gap',
        'total_people',
        'learning_velocity',
        'stratos_iq',
        'metadata'
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'average_gap' => 'float',
        'total_people' => 'integer',
        'learning_velocity' => 'float',
        'stratos_iq' => 'float',
        'metadata' => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organizations::class, 'organizations_id');
    }
}
