<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!\Schema::hasTable('agents')) {
            \Schema::create('agents', function (Blueprint $table) {
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
        }

        if (\Schema::hasTable('scenario_generations')) {
            \Schema::table('scenario_generations', function (Blueprint $table) {
                // Para trazar qué agente (si hubo uno específico) orquestó la generación
                if (!\Schema::hasColumn('scenario_generations', 'agent_id')) {
                    $table->foreignId('agent_id')->nullable()->constrained('agents');
                }

                // Para guardar el resumen de la composición sugerida sin procesar todo el JSON
                if (!\Schema::hasColumn('scenario_generations', 'hybrid_composition_summary')) {
                    $table->json('hybrid_composition_summary')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Schema::hasTable('scenario_generations')) {
            \Schema::table('scenario_generations', function (Blueprint $table) {
                if (\Schema::hasColumn('scenario_generations', 'hybrid_composition_summary')) {
                    $table->dropColumn('hybrid_composition_summary');
                }

                if (\Schema::hasColumn('scenario_generations', 'agent_id')) {
                    // Attempt to drop foreign key first if exists, then the column
                    try {
                        $table->dropForeign(['agent_id']);
                    }
                    catch (\Throwable $e) {
                    // ignore if foreign key does not exist
                    }

                    $table->dropColumn('agent_id');
                }
            });
        }

        if (\Schema::hasTable('agents')) {
            \Schema::dropIfExists('agents');
        }
    }
	/**
	 */
	function __construct() {
	}
};
