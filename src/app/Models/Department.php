<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function People(): HasMany
    {
        return $this->hasMany(People::class);
    }
}
