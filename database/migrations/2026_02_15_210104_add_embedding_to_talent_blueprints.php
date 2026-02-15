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
        // Add embedding column as vector type (1536 dimensions for OpenAI)
        // We use raw SQL because Laravel Schema 10 doesn't natively support vector yet
        try {
            DB::statement("ALTER TABLE talent_blueprints ADD COLUMN IF NOT EXISTS embedding vector(1536)");
        } catch (\Exception $e) {
            // Fallback if pgvector is not available (mostly for local dev/testing)
            Schema::table('talent_blueprints', function (Blueprint $table) {
                if (!Schema::hasColumn('talent_blueprints', 'embedding')) {
                    $table->json('embedding')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_blueprints', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });
    }
};
