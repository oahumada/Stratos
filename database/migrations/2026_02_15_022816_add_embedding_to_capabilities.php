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
        // Add embedding column to capabilities table
        DB::statement('ALTER TABLE capabilities ADD COLUMN embedding vector(1536) NULL');
        
        // Add comment to document the column
        DB::statement("COMMENT ON COLUMN capabilities.embedding IS 'Representación vectorial de la capability (nombre + descripción) para búsqueda semántica y detección de duplicados.'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capabilities', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });
    }
};
