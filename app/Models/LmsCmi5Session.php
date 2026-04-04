<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsCmi5Session extends Model
{
    protected $fillable = [
        'organization_id',
        'package_id',
        'registration_id',
        'session_id',
        'user_id',
        'actor_json',
        'launch_mode',
        'launch_url',
        'return_url',
        'move_on',
        'mastery_score',
        'status',
        'satisfied',
        'score_scaled',
        'duration',
    ];

    protected function casts(): array
    {
        return [
            'actor_json' => 'array',
            'mastery_score' => 'float',
            'score_scaled' => 'float',
            'satisfied' => 'boolean',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function package()
    {
        return $this->belongsTo(LmsScormPackage::class, 'package_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForOrganization($query, int $orgId)
    {
        return $query->where('organization_id', $orgId);
    }
}
