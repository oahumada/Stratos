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
        Schema::create('workforce_planning_succession_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('workforce_planning_scenarios')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles');
            $table->foreignId('department_id')->nullable()->constrained('departments');
            
            // Rol crítico
            $table->enum('criticality_level', ['critical', 'important', 'standard'])->default('standard');
            $table->text('impact_if_vacant')->nullable();
            
            // Sucesores potenciales
            $table->foreignId('primary_successor_id')->nullable()->constrained('people');
            $table->foreignId('secondary_successor_id')->nullable()->constrained('people');
            $table->foreignId('tertiary_successor_id')->nullable()->constrained('people');
            
            // Status del sucesor principal
            $table->enum('primary_readiness_level', ['ready_now', 'ready_12m', 'ready_24m', 'not_ready'])->nullable();
            $table->integer('primary_readiness_percentage')->default(0); // 0-100
            $table->json('primary_gap')->nullable(); // Skills a desarrollar
            
            // Plan de desarrollo para sucesor
            $table->foreignId('development_plan_id')->nullable()->constrained('development_paths');
            
            // Riesgos
            $table->text('succession_risk')->nullable();
            $table->text('mitigation_actions')->nullable();
            
            $table->enum('status', ['draft', 'approved', 'monitoring', 'executed', 'archived'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();

            $table->unique(['scenario_id', 'role_id']);
            $table->index(['scenario_id', 'criticality_level']);
            $table->index(['scenario_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workforce_planning_succession_plans');
    }
};
