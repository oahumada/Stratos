<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PulseSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'questions',
        'is_active',
        'department_id',
        'ai_report',
    ];

    protected $casts = [
        'questions' => 'array',
        'is_active' => 'boolean',
        'ai_report' => 'array',
    ];

    public function responses()
    {
        return $this->hasMany(PulseResponse::class);
    }

    public function department()
    {
        return $this->belongsTo(Departments::class, 'department_id');
    }
}
