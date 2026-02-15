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
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE capabilities DROP CONSTRAINT IF EXISTS capabilities_type_check');
            DB::statement('ALTER TABLE capabilities DROP CONSTRAINT IF EXISTS capabilities_category_check');
            DB::statement('ALTER TABLE skills DROP CONSTRAINT IF EXISTS skills_category_check');
            DB::statement('ALTER TABLE skills DROP CONSTRAINT IF EXISTS skills_lifecycle_status_check');
            DB::statement('ALTER TABLE skills DROP CONSTRAINT IF EXISTS skills_complexity_level_check');
            DB::statement('ALTER TABLE skills DROP CONSTRAINT IF EXISTS skills_scope_type_check');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
