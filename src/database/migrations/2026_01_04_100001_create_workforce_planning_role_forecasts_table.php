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
        Schema::create('workforce_planning_role_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('workforce_planning_scenarios')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles');
            $table->foreignId('department_id')->nullable()->constrained('departments');
            
            // Dotación proyectada
            $table->integer('headcount_current');
            $table->integer('headcount_projected');
            $table->decimal('growth_rate', 5, 2)->nullable(); // % de crecimiento
            $table->text('variance_reason')->nullable();
            
            // Skills requeridas futuro
            $table->json('critical_skills')->nullable(); // Array de skill_ids
            $table->json('emerging_skills')->nullable();
            $table->json('declining_skills')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'approved', 'archived'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();

            $table->unique(['scenario_id', 'role_id', 'department_id']);
            $table->index(['scenario_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workforce_planning_role_forecasts');
    }
};
