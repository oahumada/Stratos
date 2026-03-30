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
        if (! Schema::hasTable('scenarios')) {
            return;
        }

        // Drop Postgres check constraints created by enum() definitions if present
        DB::statement('ALTER TABLE scenarios DROP CONSTRAINT IF EXISTS scenarios_decision_status_check');
        DB::statement('ALTER TABLE scenarios DROP CONSTRAINT IF EXISTS scenarios_execution_status_check');

        Schema::table('scenarios', function (Blueprint $table) {
            // Convert to plain string columns with defaults used by services
            if (Schema::hasColumn('scenarios', 'decision_status')) {
                $table->string('decision_status')->default('draft')->change();
            }

            if (Schema::hasColumn('scenarios', 'execution_status')) {
                $table->string('execution_status')->default('planned')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('scenarios')) {
            return;
        }

        Schema::table('scenarios', function (Blueprint $table) {
            // Restore enums to a safe superset used earlier (best-effort)
            if (Schema::hasColumn('scenarios', 'decision_status')) {
                $table->enum('decision_status', ['draft', 'pending_approval', 'simulated', 'proposed', 'approved', 'active', 'archived', 'rejected'])->default('draft')->change();
            }

            if (Schema::hasColumn('scenarios', 'execution_status')) {
                $table->enum('execution_status', ['planned', 'not_started', 'in_progress', 'paused', 'completed'])->default('planned')->change();
            }
        });
    }
};
