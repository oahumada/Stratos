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
        Schema::create('scenario_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('name'); // "Q1 2026 Planning Comparison"
            $table->text('description')->nullable();
            $table->json('scenario_ids'); // Array de IDs de escenarios a comparar [1, 2, 3]
            $table->json('comparison_criteria')->nullable(); // Criterios: ['cost', 'time', 'risk', 'coverage']
            $table->json('comparison_results')->nullable(); // Resultados de la comparación (calculados)
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenario_comparisons');
    }
};
