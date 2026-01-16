<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeopleRoleSkills extends Model
{
    protected $table = 'people_role_skills';

    protected $fillable = [
        'people_id',
        'role_id',
        'skill_id',
        'current_level',
        'required_level',
        'is_active',
        'evaluated_at',
        'expires_at',
        'evaluated_by',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'evaluated_at' => 'datetime',
        'expires_at' => 'datetime',
        'current_level' => 'integer',
        'required_level' => 'integer',
    ];

    /**
     * Compatibilidad: algunos fixtures/tests usan la columna 'level' en lugar de
     * 'current_level' al adjuntar pivotes. Devolver 'level' como fallback.
     */
    public function getCurrentLevelAttribute($value)
    {
        // If a legacy 'level' column was provided (e.g. tests/fixtures), prefer it
        if (array_key_exists('level', $this->attributes) && $this->attributes['level'] !== null) {
            return (int) $this->attributes['level'];
        }

        if (!is_null($value) && $value !== '') {
            return (int) $value;
        }

        return 0;
    }

    // Relaciones
    public function person(): BelongsTo
    {
        return $this->belongsTo(People::class, 'people_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skills::class, 'skill_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    // Scopes útiles
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeNeedsReevaluation($query)
    {
        return $query->where('expires_at', '<', now()->addDays(30));
    }

    public function scopeForPerson($query, $personId)
    {
        return $query->where('people_id', $personId);
    }

    public function scopeForRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    // Helper methods
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function needsReevaluation(): bool
    {
        return $this->expires_at && $this->expires_at->diffInDays(now()) <= 30;
    }

    public function meetsRequirement(): bool
    {
        return $this->current_level >= $this->required_level;
    }

    public function getLevelGap(): int
    {
        return $this->required_level - $this->current_level;
    }
}
