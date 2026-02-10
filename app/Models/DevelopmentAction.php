<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevelopmentAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'development_path_id',
        'title',
        'description',
        'type',
        'strategy',
        'order',
        'status',
        'estimated_hours',
        'impact_weight',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'impact_weight' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relaciones
    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function path()
    {
        return $this->belongsTo(DevelopmentPath::class, 'development_path_id');
    }
}
