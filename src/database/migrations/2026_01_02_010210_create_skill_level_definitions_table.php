<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabla global de definiciones de niveles de skills (1-5).
     * Niveles genéricos que aplican a todas las skills del sistema.
     *
     * Criterios de progresión:
     * - Autonomía funcional (menor a mayor)
     * - Complejidad de tareas (menor a mayor)
     * - Responsabilidad (menor a mayor)
     */
    public function up(): void
    {
        Schema::create('skill_level_definitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('level')->unique()->comment('Nivel numérico (1-5)');
            $table->string('name', 50)->comment('Nombre del nivel (ej: Básico, Intermedio)');
            $table->text('description')->comment('Descripción detallada del nivel');
            $table->unsignedSmallInteger('points')->default(0)->comment('Puntos asociados al nivel (sistema de scoring)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skill_level_definitions');
    }
};
