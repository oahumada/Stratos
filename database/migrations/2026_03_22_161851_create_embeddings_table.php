<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('embeddings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable()->index();
            $table->string('resource_type');
            $table->unsignedBigInteger('resource_id');
            $table->json('metadata')->nullable();

            // La columna de vector se añade vía SQL crudo si pgvector está disponible
            // (ver bloque posterior que usa DB::statement).

            $table->timestamps();

            $table->unique(['resource_type', 'resource_id', 'organization_id'], 'embeddings_resource_unique');
        });

        // Añadir columna embedding con tipo vector(1536) solo si pgvector está instalado
        $hasPgVector = false;
        try {
            $hasPgVector = DB::getDriverName() === 'pgsql' && DB::select("SELECT 1 FROM pg_extension WHERE extname = 'vector'");
        } catch (\Exception $e) {
            $hasPgVector = false;
        }

        if ($hasPgVector) {
            DB::statement('ALTER TABLE embeddings ADD COLUMN embedding vector(1536) NULL');
            DB::statement("COMMENT ON COLUMN embeddings.embedding IS 'Vector embedding para búsqueda semántica genérica.'");
        } else {
            Schema::table('embeddings', function (Blueprint $table) {
                $table->json('embedding')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('embeddings');
    }
};
