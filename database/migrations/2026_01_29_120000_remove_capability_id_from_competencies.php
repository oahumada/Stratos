<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove capability_id FK from competencies table.
     * This enforces the N:N model via capability_competencies pivot table.
     * A competency can now be shared across multiple capabilities without duplication.
     *
     * Note: SQLite doesn't support dropping columns with FKs directly,
     * so we handle it differently for SQLite vs other databases.
     */
    public function up(): void
    {
        // Only proceed if the column actually exists
        if (! Schema::hasColumn('competencies', 'capability_id')) {
            return;
        }

        // Disable FK constraints temporarily for SQLite
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        }

        try {
            Schema::table('competencies', function ($table) {
                // For non-SQLite databases
                if (config('database.default') !== 'sqlite') {
                    $table->dropForeign(['capability_id']);
                    // Only drop the index if it exists
                    if (Schema::hasTable('competencies')) {
                        $table->dropIndexIfExists(['capability_id', 'organization_id']);
                    }
                }

                // Drop the column (works for all databases)
                $table->dropColumn('capability_id');
            });
        } finally {
            // Re-enable FK constraints
            if (config('database.default') === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON');
            }
        }
    }

    public function down(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        }

        try {
            Schema::table('competencies', function ($table) {
                // For SQLite: add as nullable first, then can be made not null in a separate migration if needed
                $isQlite = config('database.default') === 'sqlite';
                if ($isQlite) {
                    $table->foreignId('capability_id')->nullable()->constrained()->onDelete('cascade');
                } else {
                    $table->foreignId('capability_id')->constrained()->onDelete('cascade');
                }
                $table->index(['capability_id', 'organization_id']);
            });
        } finally {
            if (config('database.default') === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON');
            }
        }
    }
};
