<?php

// app/Models/Evidence.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id', 'type', 'title', 'description',
        'file_path', 'external_url', 'metadata',
        'validated_by', 'validated_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'validated_at' => 'datetime',
    ];

    // Relaciones
    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
