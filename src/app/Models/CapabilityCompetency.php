<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class CapabilityCompetency extends Model
{
    use HasFactory;

    protected $table = 'capability_competencies';

    protected $fillable = [
        'scenario_id',
        'capability_id',
        'competency_id',
        'required_level',
        // DB column is `strategic_weight`; keep `weight` accessor for compatibility
        'strategic_weight',
        'weight',
        'priority',
        'rationale',
        'is_required',
    ];

    protected $casts = [
        'required_level' => 'integer',
        'strategic_weight' => 'integer',
        'weight' => 'integer',
        'priority' => 'integer',
        'is_required' => 'boolean',
    ];

    // Backwards-compatible attribute accessors: expose `weight` mapped to DB `strategic_weight`.
    public function getWeightAttribute()
    {
        if (array_key_exists('strategic_weight', $this->attributes)) {
            return $this->attributes['strategic_weight'];
        }
        if (array_key_exists('weight', $this->attributes)) {
            return $this->attributes['weight'];
        }

        return null;
    }

    public function setWeightAttribute($value)
    {
        // Prefer `strategic_weight` column when present, otherwise fall back to `weight`.
        if (Schema::hasColumn($this->getTable(), 'strategic_weight')) {
            $this->attributes['strategic_weight'] = $value;
        } elseif (Schema::hasColumn($this->getTable(), 'weight')) {
            $this->attributes['weight'] = $value;
        } else {
            // Last resort: set strategic_weight
            $this->attributes['strategic_weight'] = $value;
        }
    }

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
