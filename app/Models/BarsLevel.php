<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarsLevel extends Model
{
    use HasFactory;

    protected $table = 'bars_levels';

    protected $fillable = [
        'skill_id', 
        'level', 
        'level_name', 
        'behavioral_description',
        'learning_content',
        'performance_indicator'
    ];

    // Relaciones
    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function responses()
    {
        return $this->hasMany(EvaluationResponse::class);
    }
}
