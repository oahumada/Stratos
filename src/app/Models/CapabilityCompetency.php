<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CapabilityCompetency extends Model
{
    use HasFactory;

    protected $table = 'capability_competencies';

    protected $fillable = [
        'scenario_id',
        'capability_id',
        'competency_id',
        'required_level',
        'weight',
        'rationale',
        'is_required',
    ];

    protected $casts = [
        'required_level' => 'integer',
        'weight' => 'integer',
        'is_required' => 'boolean',
    ];

    public function scenario()
    {
        return $this->belongsTo(Scenario::class, 'scenario_id');
    }

    public function capability()
    {
        return $this->belongsTo(Capability::class, 'capability_id');
    }

    public function competency()
    {
        return $this->belongsTo(Competency::class, 'competency_id');
    }
}
