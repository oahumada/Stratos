<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Capability extends Model
{
    use HasFactory;

    protected $table = 'capabilities';

    protected $fillable = ['organization_id', 'name', 'description', 'position_x', 'position_y', 'importance', 'type', 'category', 'status', 'discovered_in_scenario_id'];

    protected $casts = [
        'position_x' => 'decimal:2',
        'position_y' => 'decimal:2',
        'importance' => 'integer',
        'discovered_in_scenario_id' => 'integer',
    ];

    public function competencies()
    {
        return $this->hasMany(Competency::class, 'capability_id');
    }

    public function discoveredInScenario()
    {
        return $this->belongsTo(Scenario::class, 'discovered_in_scenario_id');
    }
}
