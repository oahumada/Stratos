<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workforce_planning_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('workforce_planning_scenarios')->onDelete('cascade');
            $table->foreignId('forecast_id')->constrained('workforce_planning_role_forecasts')->onDelete('cascade');
            $table->foreignId('person_id')->constrained('people');
            
            // Evaluación del match
            $table->decimal('match_score', 5, 2); // 0-100
            $table->decimal('skill_match', 5, 2); // Cobertura de skills requeridas
            $table->enum('readiness_level', ['immediate', 'short_term', 'long_term', 'not_ready']);
            $table->json('gaps')->nullable(); // Array de skills con gap
            
            // Tipo de transición
            $table->enum('transition_type', ['promotion', 'lateral', 'reskilling', 'no_match']);
            $table->integer('transition_months')->nullable();
            $table->foreignId('development_path_id')->nullable()->constrained('development_paths');
            
            // Score de riesgo
            $table->decimal('risk_score', 5, 2)->default(0); // 0-100
            $table->json('risk_factors')->nullable(); // ["alto_costo", "baja_cultura_fit"]
            
            $table->text('recommendation')->nullable();
            $table->timestamps();

            $table->unique(['scenario_id', 'forecast_id', 'person_id']);
            $table->index(['scenario_id', 'match_score']);
            $table->index(['forecast_id', 'readiness_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workforce_planning_matches');
    }
};
