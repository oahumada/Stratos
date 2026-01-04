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
        Schema::create('workforce_planning_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('workforce_planning_scenarios')->onDelete('cascade');
            
            // Métricas generales
            $table->integer('total_headcount_current')->default(0);
            $table->integer('total_headcount_projected')->default(0);
            $table->integer('net_growth')->default(0);
            
            // Cobertura interna
            $table->decimal('internal_coverage_percentage', 5, 2)->default(0);
            $table->decimal('external_gap_percentage', 5, 2)->default(0);
            
            // Skills
            $table->integer('total_skills_required')->default(0);
            $table->integer('skills_with_gaps')->default(0);
            $table->integer('critical_skills_at_risk')->default(0);
            
            // Sucesión
            $table->integer('critical_roles')->default(0);
            $table->integer('critical_roles_with_successor')->default(0);
            $table->decimal('succession_risk_percentage', 5, 2)->default(0);
            
            // Estimaciones
            $table->decimal('estimated_recruitment_cost', 12, 2)->default(0);
            $table->decimal('estimated_training_cost', 12, 2)->default(0);
            $table->decimal('estimated_external_hiring_months', 5, 1)->default(0);
            
            // Riesgos
            $table->integer('high_risk_positions')->default(0);
            $table->integer('medium_risk_positions')->default(0);
            
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->unique('scenario_id');
            $table->index('calculated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workforce_planning_analytics');
    }
};
