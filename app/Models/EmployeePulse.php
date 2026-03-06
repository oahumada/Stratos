<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePulse extends Model
{
    protected $table = 'employee_pulses';

    protected $fillable = [
        'people_id',
        'e_nps',
        'stress_level',
        'engagement_level',
        'comments',
        'ai_turnover_risk',
        'ai_turnover_reason',
    ];

    public function person()
    {
        return $this->belongsTo(People::class, 'people_id');
    }
}
