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
        // Only add if column doesn't already exist (safe for both migrate and migrate:fresh)
        if (! Schema::hasColumn('verification_notifications', 'message')) {
            Schema::table('verification_notifications', function (Blueprint $table) {
                $table->string('message')->after('type')->default('');
            });
        }

        // Expand the severity check constraint to include 'error'
        DB::statement('ALTER TABLE verification_notifications DROP CONSTRAINT IF EXISTS verification_notifications_severity_check');
        DB::statement("ALTER TABLE verification_notifications ADD CONSTRAINT verification_notifications_severity_check CHECK (severity IN ('info', 'warning', 'error', 'critical'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_notifications', function (Blueprint $table) {
            $table->dropColumn('message');
        });

        DB::statement('ALTER TABLE verification_notifications DROP CONSTRAINT IF EXISTS verification_notifications_severity_check');
        DB::statement("ALTER TABLE verification_notifications ADD CONSTRAINT verification_notifications_severity_check CHECK (severity IN ('info', 'warning', 'critical'))");
    }
};
