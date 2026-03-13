<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialIndicator extends Model
{
    protected $fillable = [
        'organization_id',
        'people_id',
        'indicator_type',
        'value',
        'currency',
        'reference_date'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'reference_date' => 'date'
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organizations::class, 'organization_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(People::class, 'people_id');
    }
}
