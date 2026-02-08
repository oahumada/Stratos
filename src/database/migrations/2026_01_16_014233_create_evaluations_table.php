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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Evaluado
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->foreignId('scenario_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('current_level', 3, 2)->nullable(); // Nivel calculado (N)
            $table->integer('required_level')->nullable(); // Nivel requerido (R)
            $table->decimal('gap', 3, 2)->nullable(); // R - N
            $table->text('metadata')->nullable(); // JSON con detalles adicionales
            $table->integer('confidence_score')->default(0); // 0-100, basado en consistencia de evidencias
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
