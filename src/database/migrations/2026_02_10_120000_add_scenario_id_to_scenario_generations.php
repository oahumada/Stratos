<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('scenario_generations')) {
            return;
        }

        if (! Schema::hasColumn('scenario_generations', 'scenario_id')) {
            Schema::table('scenario_generations', function (Blueprint $table) {
                $table->unsignedBigInteger('scenario_id')->nullable()->after('id');
            });

            // add unique index and foreign key when DB supports it
            Schema::table('scenario_generations', function (Blueprint $table) {
                if (! Schema::hasColumn('scenario_generations', 'scenario_id')) {
                    return;
                }
                $table->unique('scenario_id', 'scenario_generations_scenario_id_unique');
                if (DB::getDriverName() !== 'sqlite') {
                    $table->foreign('scenario_id')->references('id')->on('scenarios')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('scenario_generations')) {
            return;
        }

        if (Schema::hasColumn('scenario_generations', 'scenario_id')) {
            Schema::table('scenario_generations', function (Blueprint $table) {
                if (DB::getDriverName() !== 'sqlite') {
                    $table->dropForeign(['scenario_id']);
                }
                if (Schema::hasColumn('scenario_generations', 'scenario_id')) {
                    $table->dropUnique('scenario_generations_scenario_id_unique');
                    $table->dropColumn('scenario_id');
                }
            });
        }
    }
};
