<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuideFaq extends Model
{
    /** @use HasFactory<\Database\Factories\GuideFaqFactory> */
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'slug',
        'category',
        'title',
        'question',
        'answer',
        'tags',
        'is_active',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_active' => 'boolean',
    ];
}
