<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organizations extends Model
{
    protected $fillable = ['name', 'subdomain', 'industry', 'size'];

    protected $casts = [
        'size' => 'string',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Roles::class);
    }

    public function People(): HasMany
    {
        return $this->hasMany(People::class);
    }

    public function developmentPaths(): HasMany
    {
        return $this->hasMany(DevelopmentPath::class);
    }

    public function jobOpenings(): HasMany
    {
        return $this->hasMany(JobOpening::class);
    }
}
