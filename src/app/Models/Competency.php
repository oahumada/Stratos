<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Competency extends Model
{
    use HasFactory;

    protected $table = 'competencies';

    protected $fillable = ['organization_id', 'name', 'description'];

    /**
     * Relación N:N con Capabilities vía tabla pivote capability_competencies
     */
    public function capabilities()
    {
        return $this->belongsToMany(
            Capability::class,
            'capability_competencies',
            'competency_id',
            'capability_id'
        )
            ->withPivot('scenario_id', 'required_level', 'weight', 'rationale', 'is_required', 'created_at', 'updated_at')
            ->withTimestamps();
    }

    public function skills()
    {
        return $this->belongsToMany(Skills::class, 'competency_skills', 'competency_id', 'skill_id')
            ->withPivot('weight')
            ->withTimestamps();
    }

    public function competencySkills()
    {
        return $this->hasMany(CompetencySkill::class, 'competency_id');
    }

    public function capabilityCompetencies()
    {
        return $this->hasMany(\App\Models\CapabilityCompetency::class, 'competency_id');
    }
}

