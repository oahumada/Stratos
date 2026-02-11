<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleAiLeverage extends Pivot {
    // Atributos de ingeniería de talento
    // human_share + synthetic_share = 100%
    protected $fillable = [
        'role_id',
        'agent_id',
        'human_share',      // % de carga humana (juicio, empatía)
        'synthetic_share',  // % de carga IA (procesamiento, ejecución)
        'automation_potential_score', // Calculado por el LLM
        'orchestration_key' // El ID del flujo en n8n que activa este agente
    ];
}
