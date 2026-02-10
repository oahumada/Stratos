<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeSet extends Model
{
    use HasFactory;

    protected $table = 'change_sets';

    protected $fillable = [
        'organization_id',
        'scenario_id',
        'title',
        'description',
        'change_group_id',
        'status',
        'created_by',
        'approved_by',
        'effective_from',
        'applied_at',
        'diff',
        'metadata',
    ];

    protected $casts = [
        'diff' => 'array',
        'metadata' => 'array',
        'effective_from' => 'date',
        'applied_at' => 'datetime',
    ];

    public function scenario()
    {
        return $this->belongsTo(Scenario::class, 'scenario_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organizations::class, 'organization_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
