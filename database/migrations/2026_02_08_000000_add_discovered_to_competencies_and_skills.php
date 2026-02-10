<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('competencies', 'discovered_in_scenario_id')) {
            Schema::table('competencies', function (Blueprint $table) {
                $table->foreignId('discovered_in_scenario_id')->nullable()->constrained('scenarios')->onDelete('set null');
            });
        }

        if (! Schema::hasColumn('skills', 'discovered_in_scenario_id')) {
            Schema::table('skills', function (Blueprint $table) {
                $table->foreignId('discovered_in_scenario_id')->nullable()->constrained('scenarios')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('competencies', 'discovered_in_scenario_id')) {
            Schema::table('competencies', function (Blueprint $table) {
                $table->dropForeign(['discovered_in_scenario_id']);
                $table->dropColumn('discovered_in_scenario_id');
            });
        }

        if (Schema::hasColumn('skills', 'discovered_in_scenario_id')) {
            Schema::table('skills', function (Blueprint $table) {
                $table->dropForeign(['discovered_in_scenario_id']);
                $table->dropColumn('discovered_in_scenario_id');
            });
        }
    }
};
