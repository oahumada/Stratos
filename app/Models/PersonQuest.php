<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonQuest extends Model
{
    use HasFactory;

    protected $table = 'person_quests';

    protected $fillable = [
        'people_id',
        'quest_id',
        'status',
        'progress',
        'completed_at',
    ];

    protected $casts = [
        'progress' => 'array',
        'completed_at' => 'datetime',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    public function quest(): BelongsTo
    {
        return $this->belongsTo(Quest::class);
    }
}
