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
        $hasPgVector = false;
        try {
            $hasPgVector = DB::getDriverName() === 'pgsql' && DB::select("SELECT 1 FROM pg_extension WHERE extname = 'vector'");
        } catch (\Exception $e) {
            $hasPgVector = false;
        }

        if ($hasPgVector) {
            // Añadir columna de embeddings a Competencias
            DB::statement('ALTER TABLE competencies ADD COLUMN embedding vector(1536) NULL');

            // Añadir columna de embeddings a Roles
            DB::statement('ALTER TABLE roles ADD COLUMN embedding vector(1536) NULL');

            // Añadir columna de embeddings a Skills (Habilidades)
            DB::statement('ALTER TABLE skills ADD COLUMN embedding vector(1536) NULL');

            // Documentación
            DB::statement("COMMENT ON COLUMN competencies.embedding IS 'Representación vectorial de la competencia para búsqueda semántica.'");
            DB::statement("COMMENT ON COLUMN roles.embedding IS 'Representación vectorial del perfil del rol para búsqueda semántica.'");
            DB::statement("COMMENT ON COLUMN skills.embedding IS 'Representación vectorial de la habilidad para búsqueda semántica.'");
        } else {
            Schema::table('competencies', function (Blueprint $table) {
                $table->json('embedding')->nullable();
            });
            Schema::table('roles', function (Blueprint $table) {
                $table->json('embedding')->nullable();
            });
            Schema::table('skills', function (Blueprint $table) {
                $table->json('embedding')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });
    }
};
