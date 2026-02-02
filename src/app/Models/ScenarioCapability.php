<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScenarioCapability extends Model
{
    use HasFactory;

    protected $table = 'scenario_capabilities';

    protected $fillable = ['scenario_id', 'capability_id', 'strategic_role', 'strategic_weight', 'priority', 'rationale', 'required_level', 'is_critical'];

    public function scenario()
    {
        return $this->belongsTo(Scenario::class, 'scenario_id');
    }

    public function capability()
    {
        return $this->belongsTo(Capability::class);
    }
}
