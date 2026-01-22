<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scenario_skill_demands', function (Blueprint $table) {
            $table->id();
            // reference canonical `scenarios` table (some legacy code used `workforce_planning_scenarios`)
            $table->foreignId('scenario_id')->constrained('scenarios')->onDelete('cascade');
            $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->string('department')->nullable(); // Departamento objetivo
            $table->integer('required_headcount')->default(0); // Cantidad de personas necesarias
            $table->decimal('required_level', 3, 1)->default(3.0); // Nivel mínimo requerido (1-5)
            $table->integer('current_headcount')->default(0); // Cantidad actual (calculado)
            $table->decimal('current_avg_level', 3, 1)->nullable(); // Nivel promedio actual (calculado)
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('rationale')->nullable(); // Justificación de la demanda
            $table->date('target_date')->nullable(); // Fecha objetivo para cubrir la demanda
            $table->timestamps();
            $table->softDeletes();

            $table->index(['scenario_id', 'priority']);
            $table->index(['skill_id', 'role_id']);
            $table->unique(['scenario_id', 'skill_id', 'role_id', 'department'], 'scenario_skill_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenario_skill_demands');
    }
};
