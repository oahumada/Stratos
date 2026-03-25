<?php

namespace App\Models;

use App\Traits\HasDigitalSeal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PulseSurvey extends Model
{
    use HasDigitalSeal, HasFactory;

    protected $fillable = [
        'title',
        'type',
        'questions',
        'is_active',
        'department_id',
        'ai_report',
        'digital_signature', 'signed_at', 'signature_version',
    ];

    protected $casts = [
        'questions' => 'array',
        'is_active' => 'boolean',
        'ai_report' => 'array',
        'signed_at' => 'datetime',
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
