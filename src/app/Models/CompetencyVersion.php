<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompetencyVersion extends Model
{
    use HasFactory;

    protected $table = 'competency_versions';

    protected $fillable = [
        'organization_id',
        'competency_id',
        'version_group_id',
        'name',
        'description',
        'effective_from',
        'evolution_state',
        'obsolescence_reason',
        'metadata',
        'created_by'
    ];

    protected $casts = [
        'metadata' => 'array',
        'effective_from' => 'date',
    ];

    public function competency()
    {
        return $this->belongsTo(Competency::class, 'competency_id');
    }
}
