<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // El agente que orquesta o evalúa a este rol
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            
            // La plantilla arquitectónica o blueprint del rol
            $table->foreignId('blueprint_id')->nullable()->constrained('talent_blueprints')->nullOnDelete();
            
            // Modelo del Cubo (3D): Complejidad, Autonomía e Impacto
            $table->json('cube_dimensions')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropForeign(['blueprint_id']);
            
            $table->dropColumn(['agent_id', 'blueprint_id', 'cube_dimensions']);
        });
    }
};
