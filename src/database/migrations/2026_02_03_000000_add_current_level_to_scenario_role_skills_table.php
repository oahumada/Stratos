<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('scenario_role_skills', 'current_level')) {
            Schema::table('scenario_role_skills', function (Blueprint $table) {
                $table->integer('current_level')->default(1)->after('required_level');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('scenario_role_skills', 'current_level')) {
            Schema::table('scenario_role_skills', function (Blueprint $table) {
                $table->dropColumn('current_level');
            });
        }
    }
};
