<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Embedding extends Model
{
    protected $fillable = [
        'organization_id',
        'resource_type',
        'resource_id',
        'metadata',
        'embedding',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            // Cuando la columna sea vector(1536) en Postgres, este cast se ignora
            // a nivel de PHP y se trata como string/array según el driver.
            // En entornos sin pgvector usamos JSON.
            'embedding' => 'array',
        ];
    }
}
