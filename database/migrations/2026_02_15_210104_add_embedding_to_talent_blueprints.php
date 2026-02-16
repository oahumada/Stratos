<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Indica si la migración debe ejecutarse dentro de una transacción.
     */
    public $withinTransaction = false;

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
            DB::statement("ALTER TABLE talent_blueprints ADD COLUMN IF NOT EXISTS embedding vector(1536)");
        } else {
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
