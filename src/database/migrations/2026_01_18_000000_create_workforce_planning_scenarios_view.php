<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Legacy compatibility table `workforce_planning_scenarios` is intentionally deprecated.
        // No-op in this migration to avoid recreating legacy structures in development databases.
        return;
    }

    public function down(): void
    {
        // No-op: nothing to rollback for deprecated compatibility objects.
        return;
    }
};
