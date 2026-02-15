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
        // 1. Roles: Add discovery and incubation fields
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'llm_id')) {
                $table->string('llm_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('roles', 'status')) {
                $table->string('status')->default('active')->after('description');
            }
            if (!Schema::hasColumn('roles', 'discovered_in_scenario_id')) {
                $table->foreignId('discovered_in_scenario_id')->nullable()->after('status')->constrained('scenarios')->onDelete('set null');
            }
        });

        // 2. Competencies: Add status
        Schema::table('competencies', function (Blueprint $table) {
            if (!Schema::hasColumn('competencies', 'status')) {
                $table->string('status')->default('active')->after('description');
            }
        });

        // 3. Skills: Add status (complementing lifecycle_status)
        Schema::table('skills', function (Blueprint $table) {
            if (!Schema::hasColumn('skills', 'status')) {
                $table->string('status')->default('active')->after('description');
            }
        });

        // 4. Capabilities: Change status from enum to string to support 'in_incubation'
        Schema::table('capabilities', function (Blueprint $table) {
            $table->string('status')->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['discovered_in_scenario_id']);
            $table->dropColumn(['llm_id', 'status', 'discovered_in_scenario_id']);
        });

        Schema::table('competencies', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('capabilities', function () {
            // Reverting to enum might be tricky depending on the DB, let's just keep it as string or try to revert
            // $table->enum('status', ['active', 'inactive'])->default('active')->change();
        });
    }
};
