<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDigitalSeal;

class EmployeePulse extends Model
{
    use HasDigitalSeal;
    protected $table = 'employee_pulses';

    protected $fillable = [
        'people_id',
        'e_nps',
        'stress_level',
        'engagement_level',
        'comments',
        'ai_turnover_risk',
        'ai_turnover_reason',
        'digital_signature', 'signed_at', 'signature_version',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function person()
    {
        return $this->belongsTo(People::class, 'people_id');
    }
}
