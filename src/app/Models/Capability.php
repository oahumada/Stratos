<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        // Relación N:N vía tabla pivote capability_competencies
        return $this->belongsToMany(
            Competency::class,
            'capability_competencies',
            'capability_id',
            'competency_id'
        )
            ->withPivot($this->capabilityCompetencyPivotColumns())
            ->withTimestamps();
    }

    private function capabilityCompetencyPivotColumns(): array
    {
        $cols = ['scenario_id', 'required_level', 'weight', 'strategic_weight', 'priority', 'rationale', 'is_required', 'created_at', 'updated_at'];

        return array_values(array_filter($cols, function ($c) {
            return Schema::hasColumn('capability_competencies', $c);
        }));
    }

    public function capabilityCompetencies()
    {
        return $this->hasMany(CapabilityCompetency::class, 'capability_id');
    }

    public function discoveredInScenario()
    {
        return $this->belongsTo(Scenario::class, 'discovered_in_scenario_id');
    }

    public function isIncubating(): bool
    {
        return ! is_null($this->discovered_in_scenario_id);
    }

    protected static function booted()
    {
        static::created(function (self $cap) {
            try {
                // Only attach to scenario pivot when the capability was created as part
                // of a scenario (discovered_in_scenario_id defined). The UI/save action
                // should provide this field when creating from a node.
                $scenarioId = $cap->discovered_in_scenario_id;
                if (empty($scenarioId)) {
                    return;
                }

                $exists = DB::table('scenario_capabilities')
                    ->where('scenario_id', $scenarioId)
                    ->where('capability_id', $cap->id)
                    ->exists();

                if ($exists) {
                    return;
                }

                DB::table('scenario_capabilities')->insert([
                    'scenario_id' => $scenarioId,
                    'capability_id' => $cap->id,
                    'strategic_role' => 'target',
                    'strategic_weight' => 10,
                    'priority' => 1,
                    'rationale' => null,
                    'required_level' => 3,
                    'is_critical' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Throwable $e) {
                \Log::error('Error attaching capability to scenario pivot: '.$e->getMessage(), ['capability_id' => $cap->id, 'scenario_id' => $cap->discovered_in_scenario_id ?? null]);
            }
        });
    }
}
