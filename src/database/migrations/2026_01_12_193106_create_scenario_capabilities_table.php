<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scenario_capabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained()->onDelete('cascade');
            $table->foreignId('capability_id')->constrained()->onDelete('cascade');
            $table->string('strategic_role')->default('target'); // target, watch, sunset
            $table->unsignedSmallInteger('strategic_weight')->default(10); // 1-100 para IQ ponderado
            $table->unsignedSmallInteger('priority')->default(1); // 1-5
            $table->text('rationale')->nullable();
            $table->integer('required_level')->default(3); // Nivel Dreyfus requerido (1-5)
            $table->boolean('is_critical')->default(false); // Si es crítica para el escenario
            $table->timestamps();

            $table->unique(['scenario_id', 'capability_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('scenario_capabilities');
    }
};
