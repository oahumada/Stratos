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
        Schema::create('workforce_planning_skill_gaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('workforce_planning_scenarios')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->foreignId('role_id')->nullable()->constrained('roles');
            
            // Skill
            $table->foreignId('skill_id')->constrained('skills');
            
            // Gap analysis
            $table->decimal('current_proficiency', 3, 1); // Nivel actual (0-10)
            $table->decimal('required_proficiency', 3, 1); // Nivel requerido futuro (0-10)
            $table->decimal('gap', 3, 1); // required - current
            
            // Cobertura
            $table->integer('people_with_skill')->default(0);
            $table->decimal('coverage_percentage', 5, 2)->default(0); // % de cobertura actual
            
            $table->enum('priority', ['critical', 'high', 'medium', 'low'])->default('medium');
            $table->enum('remediation_strategy', ['training', 'hiring', 'reskilling', 'outsourcing'])->default('training');
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->integer('timeline_months')->nullable();
            
            $table->timestamps();

            $table->unique(['scenario_id', 'skill_id', 'department_id', 'role_id']);
            $table->index(['scenario_id', 'priority']);
            $table->index(['skill_id', 'scenario_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workforce_planning_skill_gaps');
    }
};
