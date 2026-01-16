<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evaluation extends Model
{
    use HasFactory;

    protected $table = 'evaluations';

    protected $fillable = ['evaluatable_type', 'evaluatable_id', 'user_id', 'score', 'notes', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function responses()
    {
        return $this->hasMany(EvaluationResponse::class, 'evaluation_id');
    }
}
