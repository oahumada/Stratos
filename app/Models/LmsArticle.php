<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsArticle extends Model
{
    use HasFactory;

    protected $table = 'lms_articles';

    protected $fillable = [
        'organization_id',
        'author_id',
        'title',
        'slug',
        'topic',
        'excerpt',
        'body',
        'status',
        'scheduled_publish_at',
        'published_at',
    ];

    protected $casts = [
        'scheduled_publish_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(People::class, 'author_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}
