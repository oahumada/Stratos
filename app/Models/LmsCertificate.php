<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsCertificate extends Model
{
    use HasFactory;

    protected $table = 'lms_certificates';

    protected $fillable = [
        'organization_id',
        'person_id',
        'lms_enrollment_id',
        'certificate_number',
        'certificate_url',
        'certificate_hash',
        'certificate_template_id',
        'issued_at',
        'expires_at',
        'is_revoked',
        'meta',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_revoked' => 'boolean',
        'meta' => 'array',
    ];

    public function person()
    {
        return $this->belongsTo(People::class, 'person_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function enrollment()
    {
        return $this->belongsTo(LmsEnrollment::class, 'lms_enrollment_id');
    }

    public function template()
    {
        return $this->belongsTo(LmsCertificateTemplate::class, 'certificate_template_id');
    }
}
