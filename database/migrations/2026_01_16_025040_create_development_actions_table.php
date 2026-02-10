<?php

// database/migrations/xxxx_xx_xx_create_development_actions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('development_actions', function (Blueprint $table) {
            $table->id();
            // Relación con el Path (El contenedor)
            $table->foreignId('development_path_id')->constrained()->onDelete('cascade');

            // Definición de la acción
            $table->string('title'); // Ej: "Curso de Arquitectura de Microservicios"
            $table->text('description')->nullable();

            // Tipo de acción (70-20-10)
            $table->enum('type', ['training', 'practice', 'project', 'mentoring'])->default('training');

            // Estrategia Stratos
            $table->enum('strategy', ['build', 'buy', 'borrow', 'bot'])->default('build');

            // Control de ejecución
            $table->integer('order')->default(1); // Orden dentro del Path
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');

            // Metadatos de esfuerzo e impacto
            $table->integer('estimated_hours')->nullable();
            $table->decimal('impact_weight', 3, 2)->default(1.0); // Cuánto aporta esta acción al cierre del gap

            // Fechas
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_actions');
    }
};
