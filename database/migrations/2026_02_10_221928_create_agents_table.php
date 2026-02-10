<?php

// database/migrations/2026_02_10_000001_create_agents_table.php
Schema::create('agents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('organization_id')->index();
    $table->string('name'); // Ej: "Stratos Strategic Planner"
    $table->string('provider'); // abacus, anthropic, openai
    $table->string('model'); // opus, sonnet-3.5, gpt-4o
    $table->text('system_prompt')->nullable();
    $table->json('expertise_areas')->nullable(); // ['data', 'hr', 'strategy']
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// database/migrations/2026_02_10_000002_add_hybrid_fields_to_scenario_generations.php
Schema::table('scenario_generations', function (Blueprint $table) {
    // Para trazar qué agente (si hubo uno específico) orquestó la generación
    $table->foreignId('agent_id')->nullable()->constrained('agents');
    // Para guardar el resumen de la composición sugerida sin procesar todo el JSON
    $table->json('hybrid_composition_summary')->nullable(); 
});