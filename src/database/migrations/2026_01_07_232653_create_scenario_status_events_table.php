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
        Schema::create('scenario_status_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('scenarios')->onDelete('cascade');
            
            // Transición de estados de decisión
            $table->string('from_decision_status', 50)->nullable();
            $table->string('to_decision_status', 50)->nullable();
            
            // Transición de estados de ejecución
            $table->string('from_execution_status', 50)->nullable();
            $table->string('to_execution_status', 50)->nullable();
            
            // Auditoría
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable()->comment('Datos adicionales del evento');
            
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes
            $table->index(['scenario_id', 'created_at']);
            $table->index('changed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenario_status_events');
    }
};
