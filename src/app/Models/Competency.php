<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Competency extends Model
{
    use HasFactory;

    protected $table = 'competencies';

    protected $fillable = ['organization_id', 'capability_id', 'name', 'description'];

    public function capability()
    {
        return $this->belongsTo(Capability::class);
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
}
