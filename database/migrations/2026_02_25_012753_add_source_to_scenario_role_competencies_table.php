<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scenario_role_competencies', function (Blueprint $table) {
            // Origen del mapping: quién lo creó
            // 'agent'  → aprobado desde propuesta del agente IA
            // 'manual' → asignado directamente por el operador en la matriz
            // 'auto'   → derivado automáticamente desde el blueprint del Paso 1
            $table->enum('source', ['agent', 'manual', 'auto'])
                  ->default('manual')
                  ->after('rationale')
                  ->comment('Origen del mapping: agent | manual | auto');
        });
    }

    public function down(): void
    {
        Schema::table('scenario_role_competencies', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
