<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGamification extends Model
{
    protected $table = 'user_gamification';

    protected $fillable = [
        'user_id',
        'total_xp',
        'level',
        'current_points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Añadir XP al usuario y manejar subida de nivel.
     */
    public function addExperience(int $amount): void
    {
        $this->total_xp += $amount;
        $this->current_points += $amount;
        
        // Lógica simple de nivel: cada 1000 XP sube un nivel
        $newLevel = (int) floor($this->total_xp / 1000) + 1;
        
        if ($newLevel > $this->level) {
            $this->level = $newLevel;
        }
        
        $this->save();
    }
}
