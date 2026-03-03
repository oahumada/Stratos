<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    use HasFactory;

    protected $table = 'badges';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
    ];

    public function People(): BelongsToMany
    {
        return $this->belongsToMany(People::class, 'people_badges', 'badge_id', 'people_id')
            ->withPivot('awarded_at')
            ->withTimestamps();
    }
}
