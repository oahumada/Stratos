<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Añadir columna de embeddings a Escenarios
        DB::statement('ALTER TABLE scenarios ADD COLUMN embedding vector(1536) NULL');

        // Añadir columna de embeddings a Roles de Escenario (contexto específico del escenario)
        DB::statement('ALTER TABLE scenario_roles ADD COLUMN embedding vector(1536) NULL');

        // Añadir columna de embeddings a Generaciones de Escenario (para indexar prompts/respuestas)
        DB::statement('ALTER TABLE scenario_generations ADD COLUMN embedding vector(1536) NULL');

        // Documentación
        DB::statement("COMMENT ON COLUMN scenarios.embedding IS 'Representación vectorial del escenario (nombre, descripción, assumptions) para comparación semántica.'");
        DB::statement("COMMENT ON COLUMN scenario_roles.embedding IS 'Representación vectorial del rol en el contexto del escenario (rationale y ajustes específicos).' ");
        DB::statement("COMMENT ON COLUMN scenario_generations.embedding IS 'Representación vectorial de la interacción con el LLM (prompt/contexto) para trazabilidad y búsqueda de conocimiento.' ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });

        Schema::table('scenario_roles', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });

        Schema::table('scenario_generations', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });
    }
};
