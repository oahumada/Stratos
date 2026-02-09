<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenerationChunk extends Model
{
    use HasFactory;

    protected $table = 'generation_chunks';

    protected $fillable = [
        'scenario_generation_id',
        'sequence',
        'chunk',
    ];

    public function generation()
    {
        return $this->belongsTo(ScenarioGeneration::class, 'scenario_generation_id');
    }
}
