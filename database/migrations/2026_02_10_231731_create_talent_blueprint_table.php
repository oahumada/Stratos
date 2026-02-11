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
        // database/migrations/xxxx_create_talent_blueprints_table.php
        Schema::create('talent_blueprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id');
            $table->string('role_name');
            $table->float('total_fte_required');
            $table->integer('human_leverage');     // % Juicio Humano
            $table->integer('synthetic_leverage'); // % Ejecución IA
            $table->string('recommended_strategy'); // Buy, Build, Borrow, Synthetic
            $table->json('agent_specs');           // Requisitos técnicos del agente IA
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_blueprint');
    }
};
