<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluation extends Model
{
    use HasFactory;

    protected $table = 'evaluations';

    protected $fillable = ['user_id', 'skill_id', 'scenario_id', 'current_level', 
        'required_level', 'gap', 'confidence_score', 'evaluated_at', 'metadata'];

   protected $casts = [
        'current_level' => 'decimal:2',
        'gap' => 'decimal:2',
        'evaluated_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function scenario()
    {
        return $this->belongsTo(Scenario::class);
    }

    public function responses()
    {
        return $this->hasMany(EvaluationResponse::class);
    }

    public function evidences()
    {
        return $this->hasMany(Evidence::class);
    }

    public function developmentActions()
    {
        return $this->hasMany(DevelopmentPath::class);
    }
}
