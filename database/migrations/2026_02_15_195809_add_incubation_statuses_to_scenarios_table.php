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
        // Drop the check constraint if it exists (Postgres)
        DB::statement("ALTER TABLE scenarios DROP CONSTRAINT IF EXISTS scenarios_status_check");

        Schema::table('scenarios', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            $table->enum('status', ['draft', 'in_review', 'approved', 'active', 'completed', 'archived'])->default('draft')->change();
        });
    }
};
