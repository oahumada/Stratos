<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsLesson extends Model
{
protected $fillable = [
        'lms_module_id',
        'title',
        'description',
        'content_type',
        'content_url',
        'content_body',
        'order',
        'duration_minutes',
    ];

    public function module()
    {
        return $this->belongsTo(LmsModule::class, 'lms_module_id');
    }
}
