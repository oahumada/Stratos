<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\CompetencyVersion;

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

    protected static function booted()
    {
        static::created(function (self $pivot) {
            try {
                $capability = $pivot->capability()->with('competencies')->first();
                if (!$capability) return;

                foreach ($capability->competencies as $comp) {
                    $exists = CompetencyVersion::where('competency_id', $comp->id)->exists();
                    if ($exists) continue;

                    CompetencyVersion::create([
                        'organization_id' => $comp->organization_id ?? $capability->organization_id ?? null,
                        'competency_id' => $comp->id,
                        'version_group_id' => (string) Str::uuid(),
                        'name' => $comp->name,
                        'description' => $comp->description ?? null,
                        'effective_from' => now()->toDateString(),
                        'evolution_state' => 'new_embryo',
                        'metadata' => ['source' => 'scenario_pivot', 'scenario_id' => $pivot->scenario_id],
                        'created_by' => null,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Error creating competency_versions from scenario pivot: '.$e->getMessage(), ['pivot_id' => $pivot->id ?? null]);
            }
        });
    }
}
