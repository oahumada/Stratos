<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkforcePlanDocument extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'workforce_plan_id',
        'document_type',
        'document_name',
        'document_url',
        'uploaded_by',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function workforcePlan(): BelongsTo
    {
        return $this->belongsTo(WorkforcePlan::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
