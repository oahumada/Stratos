<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifiableCredential extends Model
{
    protected $fillable = [
        'people_id',
        'type',
        'issuer_name',
        'issuer_did',
        'credential_data',
        'cryptographic_signature',
        'issued_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'credential_data' => 'array',
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function person()
    {
        return $this->belongsTo(People::class, 'people_id');
    }
}
