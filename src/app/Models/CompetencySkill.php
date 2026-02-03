<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompetencySkill extends Model
{
    use HasFactory;

    protected $table = 'competency_skills';

    protected $fillable = ['competency_id', 'skill_id', 'weight'];

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
}

