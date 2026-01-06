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
        Schema::create('scenario_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('workforce_planning_scenarios')->onDelete('cascade');
            $table->string('name'); // "Q1 Hiring Complete", "Training Phase 1"
            $table->text('description')->nullable();
            $table->date('target_date');
            $table->date('actual_date')->nullable(); // Fecha real de completación
            $table->enum('status', ['pending', 'in_progress', 'completed', 'delayed', 'cancelled'])->default('pending');
            $table->integer('completion_percentage')->default(0); // 0-100
            $table->json('deliverables')->nullable(); // Lista de entregables esperados
            $table->json('dependencies')->nullable(); // Dependencias con otros milestones
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['scenario_id', 'status']);
            $table->index('target_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenario_milestones');
    }
};
