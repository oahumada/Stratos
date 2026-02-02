<?php
// app/Models/EvaluationResponse.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id', 'evaluator_id', 'evaluator_role', 
        'bars_level_id', 'evidence_comment'
    ];

    // Relaciones
    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function barsLevel()
    {
        return $this->belongsTo(BarsLevel::class);
    }
}