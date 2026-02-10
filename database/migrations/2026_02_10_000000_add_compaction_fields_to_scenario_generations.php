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
        Schema::table('scenario_generations', function (Blueprint $table) {
            if (! Schema::hasColumn('scenario_generations', 'chunk_count')) {
                $table->integer('chunk_count')->nullable()->after('metadata');
            }
            if (! Schema::hasColumn('scenario_generations', 'compacted_at')) {
                $table->timestamp('compacted_at')->nullable()->after('chunk_count');
            }
            if (! Schema::hasColumn('scenario_generations', 'compacted_by')) {
                $table->unsignedBigInteger('compacted_by')->nullable()->after('compacted_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scenario_generations', function (Blueprint $table) {
            if (Schema::hasColumn('scenario_generations', 'compacted_by')) {
                $table->dropColumn('compacted_by');
            }
            if (Schema::hasColumn('scenario_generations', 'compacted_at')) {
                $table->dropColumn('compacted_at');
            }
            if (Schema::hasColumn('scenario_generations', 'chunk_count')) {
                $table->dropColumn('chunk_count');
            }
        });
    }
};
