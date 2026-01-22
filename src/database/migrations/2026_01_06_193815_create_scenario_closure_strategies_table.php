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
        Schema::create('scenario_closure_strategies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('scenarios')->onDelete('cascade');
            $table->foreignId('skill_id')->nullable()->constrained('skills')->nullOnDelete();
            $table->enum('strategy', ['build', 'buy', 'borrow', 'bot', 'bind', 'bridge']); // 6Bs framework
            $table->string('strategy_name'); // Nombre descriptivo de la estrategia
            $table->text('description')->nullable();
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->integer('estimated_time_weeks')->nullable();
            $table->decimal('success_probability', 3, 2)->default(0.5); // 0.0 a 1.0
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['proposed', 'approved', 'in_progress', 'completed', 'cancelled'])->default('proposed');
            $table->json('action_items')->nullable(); // Lista de acciones específicas
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // Responsable
            $table->date('target_completion_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['scenario_id', 'status']);
            $table->index(['strategy', 'skill_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenario_closure_strategies');
    }
};
