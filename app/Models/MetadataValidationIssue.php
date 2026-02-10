<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetadataValidationIssue extends Model
{
    protected $table = 'metadata_validation_issues';

    protected $fillable = [
        'generation_id',
        'metadata',
        'errors',
    ];

    protected $casts = [
        'metadata' => 'array',
        'errors' => 'array',
    ];
}
